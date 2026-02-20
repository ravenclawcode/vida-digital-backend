<!DOCTYPE html>
<html>

<head>
    <style>
        .container {
            font-family: 'Arial', sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 20px;
        }

        .content {
            padding: 30px 0;
            text-align: center;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #ED72A6;
            letter-spacing: 5px;
            padding: 15px;
            background: #FFE5F0;
            display: inline-block;
            border-radius: 8px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #f8f9fa;
            padding-top: 20px;
        }

        .btn {
            background-color: #ED72A6;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #333;">VIDA Digital</h2>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $userName }}</strong></p>
            <p>Kami menerima permintaan untuk mengatur ulang kata sandi Anda. Gunakan kode OTP di bawah ini untuk melanjutkan:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>Kode ini berlaku selama <strong>10 menit</strong>. Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} VIDA Digital. All rights reserved.</p>
            <p>Gorontalo, Indonesia</p>
        </div>
    </div>
</body>

</html>