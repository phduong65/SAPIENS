<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Xác nhận đặt bàn – {{ $reservation->code }}</title>
<style type="text/css">
body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
img { -ms-interpolation-mode: bicubic; border: 0; display: block; }
@media only screen and (max-width: 600px) {
    .ew { padding: 12px 8px !important; }
    .eh { padding: 24px 20px 20px !important; }
    .eb { padding: 24px 20px !important; }
    .ebs { padding: 0 20px 20px !important; }
    .ef { padding: 16px 20px !important; }
    .ec { display: block !important; width: 100% !important; box-sizing: border-box !important; }
    .ec-last { border-bottom: 1px solid #2E2E2A !important; }
}
</style>
</head>
<body style="margin:0; padding:0; background-color:#0F0F0D; font-family:'Inter', Arial, sans-serif; color:#C9B99A;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="ew" style="background-color:#0F0F0D; padding:40px 20px;">
<tr>
<td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#1A1A18; border:1px solid #2E2E2A;">

    {{-- Header --}}
    <tr>
        <td class="eh" style="padding:36px 40px 28px; text-align:center; border-bottom:1px solid #2E2E2A;">
            <img src="{{ asset('images/sapiens/SAPIENS%20HOUSE_LOGO_HORIZONTAL.png') }}"
                 alt="Sapiens House"
                 width="180"
                 style="display:block; margin:0 auto; max-width:180px; height:auto;">
        </td>
    </tr>

    {{-- Greeting --}}
    <tr>
        <td class="eb" style="padding:36px 40px 24px;">
            <p style="font-size:13px; color:#8C7E6A; margin:0 0 16px;">
                Kính gửi Quý khách <strong style="color:#C9B99A;">{{ $reservation->full_name }}</strong>,
            </p>
            <p style="font-size:13px; color:#8C7E6A; line-height:1.8; margin:0;">
                Sapiens House xin xác nhận Quý khách đã đặt bàn thành công như sau:
            </p>
        </td>
    </tr>

    {{-- Booking Details --}}
    <tr>
        <td class="ebs" style="padding:0 40px 32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="border:1px solid #2E2E2A; background-color:#0F0F0D;">
                <tr>
                    <td colspan="2" style="padding:14px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:9px; color:#B8925A; letter-spacing:3px; text-transform:uppercase; margin:0;">
                            Booking Details
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="ec ec-last" style="padding:12px 20px; border-bottom:1px solid #2E2E2A; width:50%;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Mã đặt bàn</p>
                        <p style="font-size:14px; color:#B8925A; font-weight:600; letter-spacing:2px; margin:0; font-family:monospace;">{{ $reservation->code }}</p>
                    </td>
                    <td class="ec" style="padding:12px 20px; border-bottom:1px solid #2E2E2A; width:50%;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Trạng thái</p>
                        <p style="font-size:13px; color:#fbbf24; margin:0;">Đang chờ xác nhận</p>
                    </td>
                </tr>
                <tr>
                    <td class="ec ec-last" style="padding:12px 20px; border-bottom:1px solid #2E2E2A; width:50%;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Ngày</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->reservation_date->format('d/m/Y') }}</p>
                    </td>
                    <td class="ec" style="padding:12px 20px; border-bottom:1px solid #2E2E2A; width:50%;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Giờ</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->reservation_time }}</p>
                    </td>
                </tr>
                <tr>
                    <td class="ec ec-last" style="padding:12px 20px; border-bottom:1px solid #2E2E2A; width:50%;">
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Số khách</p>
                        <p style="font-size:13px; color:#C9B99A; margin:0;">{{ $reservation->guest_count }} người</p>
                    </td>
                    <td class="ec" style="padding:12px 20px; border-bottom:1px solid #2E2E2A; width:50%;">
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
                        <p style="font-size:10px; color:#8C7E6A; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">Dị ứng thực phẩm</p>
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
        <td class="ebs" style="padding:0 40px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="background-color:#242420; border:1px solid #2E2E2A;">
                <tr>
                    <td style="padding:20px;">
                        <p style="font-size:9px; color:#B8925A; letter-spacing:3px; text-transform:uppercase; margin:0 0 12px;">
                            Địa điểm
                        </p>
                        <p style="font-size:13px; color:#C9B99A; margin:0 0 4px; font-weight:600;">Sapiens House</p>
                        <p style="font-size:13px; color:#8C7E6A; margin:0 0 10px;">Tầng 4, 44 Nguyễn Huệ, Sài Gòn, TP. Hồ Chí Minh</p>
                        <a href="https://maps.app.goo.gl/U4srxx72PFPQruoP7"
                           style="font-size:12px; color:#B8925A; text-decoration:none; display:inline-block;">
                            ↗ Xem trên Google Maps
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Policy --}}
    <tr>
        <td class="ebs" style="padding:0 40px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #2E2E2A;">
                <tr>
                    <td style="padding:16px 20px; border-bottom:1px solid #2E2E2A;">
                        <p style="font-size:9px; color:#B8925A; letter-spacing:3px; text-transform:uppercase; margin:0 0 8px;">Chính sách giữ bàn</p>
                        <p style="font-size:12px; color:#8C7E6A; line-height:1.8; margin:0;">
                            Bàn sẽ được giữ trong vòng <strong style="color:#C9B99A;">15 phút</strong> kể từ giờ đặt. Sau thời gian này, nhà hàng có thể sắp xếp lại bàn nếu không nhận được thông báo từ Quý khách.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 20px;">
                        <p style="font-size:9px; color:#B8925A; letter-spacing:3px; text-transform:uppercase; margin:0 0 8px;">Thay đổi / Hủy đặt bàn</p>
                        <p style="font-size:12px; color:#8C7E6A; line-height:1.8; margin:0;">
                            Nếu Quý khách cần thay đổi hoặc hủy booking, vui lòng liên hệ sớm để Sapiens House hỗ trợ điều chỉnh kịp thời.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Closing --}}
    <tr>
        <td class="ebs" style="padding:0 40px 36px;">
            <p style="font-size:13px; color:#8C7E6A; line-height:1.8; margin:0;">
                Chúng tôi rất mong được đón tiếp Quý khách tại Sapiens House.
            </p>
            <p style="font-size:13px; color:#8C7E6A; margin:12px 0 0;">
                Best regards,<br>
                <strong style="color:#C9B99A;">Sapiens House Team</strong>
            </p>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td class="ef" style="padding:20px 40px; text-align:center; border-top:1px solid #2E2E2A; background-color:#0F0F0D;">
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
