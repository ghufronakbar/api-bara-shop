<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }

        .container {
            width: 100%;
            background-color: #f4f4f7;
            padding: 40px 0;
            text-align: center;
        }

        .email-content {
            background-color: #ffffff;
            margin: 0 auto;
            padding: 20px;
            max-width: 700px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .email-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .email-header img {
            margin-top: 10px;
            width: 200px;
            vertical-align: middle;
        }

        .email-body h1 {
            font-size: 26px;
            color: #000000;
        }

        .email-body p {
            font-size: 16px;
            color: #000000;
            line-height: 1.5;
            margin: 10px;
        }

        .email-body .confirm-btn {
            display: inline-block;
            background-color: #23ACE3;
            color: #ffffff;
            margin-top: 10px;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .email-footer {
            margin-top: 30px;
            text-align: center;
            color: #888888;
            font-size: 12px;
        }

        .email-footer a {
            color: #23ACE3;
            text-decoration: underline;
        }

        @media only screen and (max-width: 800px) {
            .email-content {
                width: 85%;
                padding: 15px;
                box-shadow: none;
                border-radius: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="email-content">
            <div class="email-header">
                <img src="https://i.ibb.co/fkRRZrP/LOGO-WIKA.png" alt="{{ env('APP_NAME') }}">
            </div>
            <div class="email-body">
                <h1>Halo {{ $nama }}!</h1>
                <div class="email-text">
                    <p>Peran akun Anda di <strong>{{ strtoupper(env('APP_NAME')) }}</strong> telah berhasil diperbarui.</p>
                    <p>Peran baru Anda adalah: <strong>{{ $peranBaru }}</strong></p>
                    <p>Harap periksa kembali akses dan fungsionalitas baru yang tersedia untuk Anda berdasarkan perubahan ini.</p>
                </div>
                <div class="confirm-btn">
                    <a href="{{ env('CLIENT_URL') }}/login">Login Sekarang</a>
                </div>
            </div>

            <div class="email-footer">
                <p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan lebih lanjut, silakan hubungi kami melalui email di
                    <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}">{{ env('MAIL_FROM_ADDRESS') }}</a>
                </p>
                <p>Sleman, Yogyakarta, Indonesia</p>
            </div>
        </div>
    </div>
</body>

</html>
