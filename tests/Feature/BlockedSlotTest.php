<?php

namespace Tests\Feature;

use App\Models\BlockedSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlockedSlotTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function futureDate(int $daysAhead = 1): string
    {
        return now()->addDays($daysAhead)->toDateString();
    }

    private function block(string $date, string $time, string $reason = ''): BlockedSlot
    {
        return BlockedSlot::create([
            'blocked_date' => $date,
            'blocked_time' => $time,
            'reason'       => $reason ?: null,
        ]);
    }

    // =========================================================================
    // CLIENT API: GET /reservation/blocked-slots
    // =========================================================================

    #[Test]
    public function api_requires_date_param(): void
    {
        $this->getJson('/reservation/blocked-slots')
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('date');
    }

    #[Test]
    public function api_rejects_invalid_date(): void
    {
        $this->getJson('/reservation/blocked-slots?date=not-a-date')
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('date');
    }

    #[Test]
    public function api_returns_empty_array_when_no_blocks(): void
    {
        $this->getJson('/reservation/blocked-slots?date=' . $this->futureDate())
            ->assertOk()
            ->assertJson(['blocked' => []]);
    }

    #[Test]
    public function api_returns_blocked_times_for_date(): void
    {
        $date = $this->futureDate();
        $this->block($date, '19:00');
        $this->block($date, '21:00');

        $this->getJson('/reservation/blocked-slots?date=' . $date)
            ->assertOk()
            ->assertJsonFragment(['blocked' => ['19:00', '21:00']]);
    }

    #[Test]
    public function api_does_not_return_blocks_from_other_dates(): void
    {
        $target = $this->futureDate(2);
        $other  = $this->futureDate(3);
        $this->block($target, '18:00');
        $this->block($other,  '18:00');

        $data = $this->getJson('/reservation/blocked-slots?date=' . $target)
            ->assertOk()
            ->json('blocked');

        $this->assertCount(1, $data);
        $this->assertContains('18:00', $data);
    }

    #[Test]
    public function api_is_accessible_without_auth(): void
    {
        $this->getJson('/reservation/blocked-slots?date=' . $this->futureDate())
            ->assertOk();
    }

    #[Test]
    public function blocked_date_is_stored_as_date_only_not_datetime(): void
    {
        // Regression for 'date' cast bug — must store Y-m-d not Y-m-d H:i:s
        $date = $this->futureDate();
        $slot = $this->block($date, '20:00');

        $raw = DB::selectOne('SELECT blocked_date FROM blocked_slots WHERE id = ?', [$slot->id]);

        $this->assertEquals($date, $raw->blocked_date,
            'blocked_date must be stored as Y-m-d, not Y-m-d H:i:s. '
            . "Got: {$raw->blocked_date}");
    }

    #[Test]
    public function api_returns_blocks_when_cast_stores_correct_format(): void
    {
        $date = $this->futureDate();
        $this->block($date, '20:00');

        $this->getJson('/reservation/blocked-slots?date=' . $date)
            ->assertOk()
            ->assertJsonFragment(['blocked' => ['20:00']]);
    }

    // =========================================================================
    // ADMIN: POST /admin/blocked-slots
    // =========================================================================

    #[Test]
    public function guest_cannot_create_block(): void
    {
        $this->post('/admin/blocked-slots', [
            'blocked_date' => $this->futureDate(),
            'blocked_time' => '18:00',
        ])->assertRedirect('/login');
    }

    #[Test]
    public function admin_can_create_blocked_slot(): void
    {
        $date = $this->futureDate();

        $this->actingAs($this->admin())
            ->post('/admin/blocked-slots', [
                'blocked_date' => $date,
                'blocked_time' => '18:00',
                'reason'       => 'Private event',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('blocked_slots', [
            'blocked_time' => '18:00',
            'reason'       => 'Private event',
        ]);
    }

    #[Test]
    public function admin_cannot_block_past_date(): void
    {
        $yesterday = now()->subDay()->toDateString();

        $this->actingAs($this->admin())
            ->post('/admin/blocked-slots', [
                'blocked_date' => $yesterday,
                'blocked_time' => '18:00',
            ])
            ->assertSessionHasErrors('blocked_date');

        $this->assertDatabaseCount('blocked_slots', 0);
    }

    #[Test]
    public function admin_cannot_block_invalid_time(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/blocked-slots', [
                'blocked_date' => $this->futureDate(),
                'blocked_time' => '17:00',
            ])
            ->assertSessionHasErrors('blocked_time');
    }

    #[Test]
    public function admin_cannot_block_same_slot_twice_no_duplicate(): void
    {
        $date = $this->futureDate();
        $this->block($date, '20:00');

        $this->actingAs($this->admin())
            ->post('/admin/blocked-slots', [
                'blocked_date' => $date,
                'blocked_time' => '20:00',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('blocked_slots', 1);
    }

    #[Test]
    public function admin_can_block_multiple_slots_on_same_day(): void
    {
        $date = $this->futureDate();

        $this->actingAs($this->admin())
            ->post('/admin/blocked-slots', ['blocked_date' => $date, 'blocked_time' => '19:00']);
        $this->actingAs($this->admin())
            ->post('/admin/blocked-slots', ['blocked_date' => $date, 'blocked_time' => '20:00']);

        $this->assertDatabaseCount('blocked_slots', 2);
    }

    // =========================================================================
    // ADMIN: DELETE /admin/blocked-slots/{id}
    // =========================================================================

    #[Test]
    public function guest_cannot_delete_block(): void
    {
        $slot = $this->block($this->futureDate(), '18:00');

        $this->delete('/admin/blocked-slots/' . $slot->id)
            ->assertRedirect('/login');

        $this->assertDatabaseHas('blocked_slots', ['id' => $slot->id]);
    }

    #[Test]
    public function admin_can_delete_blocked_slot(): void
    {
        $slot = $this->block($this->futureDate(), '18:00');

        $this->actingAs($this->admin())
            ->delete('/admin/blocked-slots/' . $slot->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('blocked_slots', ['id' => $slot->id]);
    }

    #[Test]
    public function deleting_nonexistent_slot_returns_404(): void
    {
        $this->actingAs($this->admin())
            ->delete('/admin/blocked-slots/9999')
            ->assertStatus(404);
    }

    // =========================================================================
    // SERVER-SIDE RESERVATION VALIDATION
    // =========================================================================

    private function validPayload(string $date, string $time): array
    {
        return [
            'full_name'        => 'Test User',
            'phone'            => '0901234567',
            'email'            => 'test@example.com',
            'reservation_date' => $date,
            'reservation_time' => $time,
            'guest_count'      => 2,
            'is_birthday'      => false,
        ];
    }

    #[Test]
    public function submitting_blocked_time_is_rejected_server_side(): void
    {
        $date = $this->futureDate();
        $this->block($date, '19:00');

        $this->postJson('/reservation', $this->validPayload($date, '19:00'))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('reservation_time');
    }

    #[Test]
    public function submitting_unblocked_time_is_accepted(): void
    {
        $date = $this->futureDate();
        $this->block($date, '19:00');

        $this->postJson('/reservation', $this->validPayload($date, '20:00'))
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function submitting_invalid_time_is_rejected(): void
    {
        $this->postJson('/reservation', $this->validPayload($this->futureDate(), '17:00'))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('reservation_time');
    }

    #[Test]
    public function block_on_different_date_does_not_affect_other_dates(): void
    {
        $blocked   = $this->futureDate(1);
        $unblocked = $this->futureDate(2);
        $this->block($blocked, '19:00');

        $this->postJson('/reservation', $this->validPayload($unblocked, '19:00'))
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function unblocking_a_slot_allows_reservation_again(): void
    {
        $date = $this->futureDate();
        $slot = $this->block($date, '19:00');

        // First blocked
        $this->postJson('/reservation', $this->validPayload($date, '19:00'))
            ->assertStatus(422);

        // Unblock
        $slot->delete();

        // Now allowed
        $this->postJson('/reservation', $this->validPayload($date, '19:00'))
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    // =========================================================================
    // ADMIN RESERVATIONS PAGE
    // =========================================================================

    #[Test]
    public function admin_page_shows_only_upcoming_blocks(): void
    {
        $future = $this->futureDate(3);
        $this->block($future, '18:00', 'Upcoming block');

        $past = now()->subDay()->toDateString();
        $this->block($past, '18:00', 'Past block should be hidden');

        $this->actingAs($this->admin())
            ->get('/admin/reservations')
            ->assertOk()
            ->assertSeeText('Upcoming block')
            ->assertDontSeeText('Past block should be hidden');
    }
}
