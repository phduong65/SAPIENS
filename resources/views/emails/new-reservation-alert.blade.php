<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Reservation – {{ $reservation->code }}</title>
</head>
<body style="margin:0; padding:0; background:#0A0A08; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#0A0A08; padding:40px 20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                {{-- Header --}}
                <tr>
                    <td style="background:#0F0F0D; border:1px solid #2E2E2A; border-bottom:2px solid #B8925A;
                                padding:32px 40px; text-align:center;">
                        <p style="margin:0 0 4px; font-size:10px; letter-spacing:4px; text-transform:uppercase;
                                   color:#8C7E6A;">Sapiens House · Admin Alert</p>
                        <h1 style="margin:12px 0 0; font-family:Georgia,serif; font-size:22px;
                                   font-weight:normal; color:#E5D9C8; letter-spacing:1px;">
                            New Reservation Received
                        </h1>
                    </td>
                </tr>

                {{-- Code Banner --}}
                <tr>
                    <td style="background:#B8925A; padding:14px 40px; text-align:center;">
                        <p style="margin:0; font-size:13px; letter-spacing:3px; font-weight:600;
                                   color:#0A0A08; text-transform:uppercase;">
                            {{ $reservation->code }}
                        </p>
                    </td>
                </tr>

                {{-- Guest Info --}}
                <tr>
                    <td style="background:#0F0F0D; border:1px solid #2E2E2A; border-top:none; padding:32px 40px;">

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-bottom:24px;">
                                    <p style="margin:0 0 8px; font-size:10px; letter-spacing:3px;
                                               text-transform:uppercase; color:#8C7E6A;">Guest</p>
                                    <p style="margin:0; font-size:18px; color:#E5D9C8;
                                               font-family:Georgia,serif;">{{ $reservation->full_name }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="border-top:1px solid #2E2E2A; padding-top:24px;">
                                        <tr>
                                            {{-- Contact --}}
                                            <td style="width:50%; padding-bottom:20px; vertical-align:top;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Phone</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">{{ $reservation->phone }}</p>
                                            </td>
                                            <td style="width:50%; padding-bottom:20px; vertical-align:top;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Email</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">{{ $reservation->email }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            {{-- Date / Time --}}
                                            <td style="padding-bottom:20px; vertical-align:top;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Date</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">
                                                    {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                                                </p>
                                            </td>
                                            <td style="padding-bottom:20px; vertical-align:top;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Time</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">{{ $reservation->reservation_time }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            {{-- Guests / Area --}}
                                            <td style="padding-bottom:20px; vertical-align:top;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Guests</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">{{ $reservation->guest_count }} người</p>
                                            </td>
                                            <td style="padding-bottom:20px; vertical-align:top;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Area</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">
                                                    {{ $reservation->seating_area ? ucfirst($reservation->seating_area) : 'No preference' }}
                                                </p>
                                            </td>
                                        </tr>

                                        @if($reservation->is_birthday)
                                        <tr>
                                            <td colspan="2" style="padding-bottom:20px;">
                                                <p style="margin:0; font-size:13px; color:#B8925A;
                                                           border:1px solid rgba(184,146,90,0.3);
                                                           padding:8px 12px; display:inline-block;">
                                                    🎂 Birthday celebration
                                                </p>
                                            </td>
                                        </tr>
                                        @endif

                                        @if($reservation->food_allergy)
                                        <tr>
                                            <td colspan="2" style="padding-bottom:20px;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Food Allergy</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">{{ $reservation->food_allergy }}</p>
                                            </td>
                                        </tr>
                                        @endif

                                        @if($reservation->special_request)
                                        <tr>
                                            <td colspan="2" style="padding-bottom:20px;">
                                                <p style="margin:0 0 5px; font-size:9px; letter-spacing:2px;
                                                           text-transform:uppercase; color:#8C7E6A;">Special Request</p>
                                                <p style="margin:0; font-size:14px; color:#C9B99A;">{{ $reservation->special_request }}</p>
                                            </td>
                                        </tr>
                                        @endif

                                    </table>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- CTA --}}
                <tr>
                    <td style="background:#0F0F0D; border:1px solid #2E2E2A; border-top:none; padding:0 40px 32px; text-align:center;">
                        <a href="{{ url('/admin/reservations') }}"
                           style="display:inline-block; background:#B8925A; color:#0A0A08;
                                  font-size:11px; letter-spacing:2px; text-transform:uppercase;
                                  font-weight:600; padding:14px 32px; text-decoration:none;">
                            View in Admin Panel →
                        </a>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:24px 40px; text-align:center;">
                        <p style="margin:0; font-size:11px; color:#3A3A35; letter-spacing:1px;">
                            Sapiens House · Tầng 4, 44 Nguyễn Huệ, Quận 1, TP.HCM
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
