<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <title>Nota Pesanan - {{ $nota['pesanan_id'] }}</title>
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
                <h1>Nota Pesanan - {{ $nota['pesanan_id'] }}</h1>
                <div class="email-text">
                    <p><strong>Tanggal Pesanan:</strong> {{ \Carbon\Carbon::parse($nota['tanggal'])->format('d F Y') }}</p> <!-- Tanggal Pesanan -->
                    <p>Berikut adalah informasi pesanan Anda:</p>
                    <table style="width: 100%; margin-top: 20px; border-collapse: collapse; text-align: left;">
                        <tr>
                            <th>Pesanan ID</th>
                            <th>Diskon (%)</th>
                            <th>Diskon (Rp)</th>
                            <th>Pajak (%)</th>
                            <th>Pajak (Rp)</th>
                            <th>Total Akhir (Rp)</th>
                        </tr>
                        <tr>
                            <td>{{ $nota['pesanan_id'] }}</td>
                            <td>{{ $nota['persentase_diskon'] }}%</td>
                            <td>{{ number_format($nota['diskon'], 0, ',', '.') }}</td>
                            <td>{{ $nota['persentase_pajak'] }}%</td>
                            <td>{{ number_format($nota['pajak'], 0, ',', '.') }}</td>
                            <td>{{ number_format($nota['total_akhir'], 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <h3>Item Pesanan</h3>
                    <table style="width: 100%; margin-top: 10px; border-collapse: collapse; text-align: left;">
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga (Rp)</th>
                            <th>Total (Rp)</th>
                        </tr>
                        @foreach ($item_nota as $item)
                            <tr>
                                <td>{{ $item['produk'] }}</td>
                                <td>{{ $item['jumlah'] }}</td>
                                <td>{{ number_format($item['harga'], 0, ',', '.') }}</td>
                                <td>{{ number_format($item['total'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="email-footer">
                <p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi kami melalui email di
                    <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}">{{ env('MAIL_FROM_ADDRESS') }}</a>
                </p>
                <p>Sleman, Yogyakarta, Indonesia</p>
            </div>
        </div>
    </div>
</body>

</html>
