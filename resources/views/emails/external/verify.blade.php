<!DOCTYPE html>
<html lang="id" style="margin:0; padding:0;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:20px 0;">
        <tr>
            <td text-align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td text-align="center" style="background-color:#ff9800; padding:20px;">
                            <img src="https://i.imgur.com/Z8r4y0U.png" alt="FleetSAR Logo" width="100" style="margin-bottom:10px;">
                            <h2 style="color:#ffffff; margin:0; font-size:24px;">Verifikasi Email Anda</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#333333;">
                            <p style="font-size:16px; margin-bottom:20px;">
                                Hai <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="font-size:15px; line-height:1.6; margin-bottom:20px;">
                                Terima kasih telah membuat akun External pada sistem <strong>FleetSAR Palu</strong>.
                                Untuk mengaktifkan akun Anda, silakan klik tombol berikut:
                            </p>

                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ $verificationUrl }}"
                                   style="background-color:#ff9800; color:#ffffff; padding:15px 30px; border-radius:6px; font-size:16px; text-decoration:none; display:inline-block;">
                                    Verifikasi Email Sekarang
                                </a>
                            </div>

                            <p style="font-size:14px; color:#555;">
                                Jika Anda tidak membuat akun ini, abaikan email ini.
                            </p>

                            <p style="font-size:15px; margin-top:30px;">
                                Salam hormat,
                                <br><strong>FleetSAR Palu</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td text-align="center" style="background:#eeeeee; padding:15px; font-size:12px; color:#555;">
                            Email ini dikirim secara otomatis, mohon untuk tidak membalas.
                            <br>© {{ date('Y') }} FleetSAR Palu. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
