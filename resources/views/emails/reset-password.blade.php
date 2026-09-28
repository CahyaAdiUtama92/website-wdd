<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f9fafb; color: #374151; line-height: 1.5; margin: 0; padding: 0; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; box-sizing: border-box; }
        .header { text-align: center; padding: 20px 0; }
        .header img { max-width: 100px; height: auto; }
        .header h1 { font-size: 20px; color: #2E8B57; margin-top: 10px; font-weight: bold; }
        .content { background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #E67E22; }
        .title { font-size: 22px; font-weight: bold; color: #111827; margin-top: 0; margin-bottom: 20px; text-align: center; }
        .btn-container { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; background-color: #2E8B57; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; font-size: 16px; }
        .fallback { font-size: 12px; color: #6b7280; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px; word-break: break-all; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('images/Logo-WDD.png') }}" alt="Logo WDD">
            <h1>Paiketan Warga Dura Desa</h1>
        </div>
        <div class="content">
            <h2 class="title">Atur Ulang Password</h2>
            <p>Halo,</p>
            <p>Kami menerima permintaan untuk mengatur ulang password akun Anda.</p>
            
            <div class="btn-container">
                <a href="{{ $url }}" class="btn">ATUR ULANG PASSWORD</a>
            </div>
            
            <p>Tautan ini akan kedaluwarsa dalam 60 menit.</p>
            <p>Jika Anda tidak meminta perubahan password, abaikan email ini. Tidak ada perubahan yang akan dilakukan pada akun Anda.</p>
            
            <p>Salam,<br>Paiketan Warga Dura Desa</p>
            
            <div class="fallback">
                Jika tombol di atas tidak dapat digunakan, salin dan buka tautan berikut di browser Anda:<br>
                <a href="{{ $url }}" style="color: #2E8B57;">{{ $url }}</a>
            </div>
        </div>
        <div class="footer">
            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
