<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Xác nhận đặt bàn – {{ $reservation->code }}</title>
</head>
<body style="margin:0; padding:0; background-color:#0F0F0D; font-family:'Inter', Arial, sans-serif; color:#C9B99A;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0F0F0D; padding:40px 20px;">
<tr>
<td align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#1A1A18; border:1px solid #2E2E2A;">

    {{-- Header --}}
    <tr>
        <td style="padding:40px 40px 32px; text-align:center; border-bottom:1px solid #2E2E2A;">
            <p style="font-family:Georgia, serif; font-size:24px; color:#E5D9C8; letter-spacing:4px; text-transform:uppercase; margin:0 0 6px;">
                SAPIENS HOUSE
            </p>
            <p style="font-size:10px; color:#8C7E6A; letter-spacing:3px; text-transform:uppercase; margin:0;">
                Eatery & Drinks
            </p>
        </td>
    </tr>

    {{-- Greeting --}}
    <tr>
        <td style="padding:40px 40px 24px;">
            <p style="font-size:13px; color:#8C7E6A; margin:0 0 20px;">
                Xin chào <strong style="color:#C9B99A;">{{ $reservation->full_name }}</strong>,
            </p>
            <p style="font-size:13px; color:#8C7E6A; line-height:1.8; margin:0 0 8px;">
                Cảm ơn bạn đã đặt bàn tại Sapiens House. Chúng tôi đã nhận được yêu cầu của bạn và sẽ liên hệ xác nhận trong vòng <strong style="color:#C9B99A;">24 giờ</strong>.
            </p>
        </td>
    </tr>

    {{-- Booking Details --}}
    <tr>
        <td style="padding:0 40px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="border:1px solid #2E2E2A; background-color:#0F0F0D;">
                <tr>
                    <td colspan="2" style="padding:16px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:9px; color:#B8925A; letter-spacing:3px; text-transform:uppercase; margin:0;">
                            Booking Details
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Mã đặt bàn</p>
                        <p style="font-size:14px; color:#B8925A; font-weight:600; letter-spacing:2px; margin:0; font-family:monospace;">{{ $reservation->code }}</p>
                    </td>
                    <td style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Trạng thái</p>
                        <p style="font-size:13px; color:#fbbf24; margin:0;">Đang chờ xác nhận</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Ngày</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->reservation_date->format('d/m/Y') }}</p>
                    </td>
                    <td style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Giờ</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->reservation_time }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Số khách</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->guest_count }} người</p>
                    </td>
                    <td style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Khu vực</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0; text-transform:capitalize;">{{ $reservation->seating_area ?: 'Không yêu cầu' }}</p>
                    </td>
                </tr>
                @if($reservation->is_birthday)
                <tr>
                    <td colspan="2" style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:13px; color:#B8925A; margin:0;">🎂 Dịp sinh nhật / kỷ niệm đặc biệt</p>
                    </td>
                </tr>
                @endif
                @if($reservation->food_allergy)
                <tr>
                    <td colspan="2" style="padding:12px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Dị ứng</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->food_allergy }}</p>
                    </td>
                </tr>
                @endif
                @if($reservation->special_request)
                <tr>
                    <td colspan="2" style="padding:12px 20px;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Yêu cầu đặc biệt</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->special_request }}</p>
                    </td>
                </tr>
                @endif
            </table>
        </td>
    </tr>

    {{-- Location --}}
    <tr>
        <td style="padding:0 40px 40px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background-color:#242420; border:1px solid #2E2E2A; padding:20px;">
                <tr>
                    <td style="padding:20px;">
                        <p style="font-size:9px; color:#B8925A; letter-spacing:3px; text-transform:uppercase; margin:0 0 12px;">
                            How to Find Us
                        </p>
                        <p style="font-size:13px; color:#C9B99A; margin:0 0 4px;">Tầng 4, 44 Nguyễn Huệ</p>
                        <p style="font-size:13px; color:#8C7E6A; margin:0 0 12px;">Quận 1, TP.HCM</p>
                        <a href="https://maps.app.goo.gl/U4srxx72PFPQruoP7"
                           style="font-size:12px; color:#B8925A; text-decoration:none;">
                            ↗ View on Google Maps
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="padding:24px 40px; text-align:center; border-top:1px solid #2E2E2A; background-color:#0F0F0D;">
            <p style="font-size:11px; color:#3A3A35; margin:0;">
                © {{ date('Y') }} Sapiens House — A Modern Cave for Modern Humans
            </p>
        </td>
    </tr>

</table>
</td>
</tr>
</table>

</body>
</html>
