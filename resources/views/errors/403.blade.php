<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            text-align: center;
        }
        .error-code {
            font-size: 120px;
            font-weight: 900;
            color: #dc3545;
            line-height: 1;
        }
        .error-title {
            font-size: 28px;
            font-weight: 700;
            margin-top: 20px;
            color: #333;
        }
        .error-message {
            color: #666;
            margin: 20px 0;
            font-size: 16px;
        }
        .error-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .error-details h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .error-details p {
            margin: 5px 0;
            color: #6c757d;
        }
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <p class="error-message">
            {{ $message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
        </p>
        
        @if(isset($your_arenas) && isset($match_arena))
        <div class="error-details">
            <h6>📍 Detail Arena:</h6>
            <p><strong>Arena Anda:</strong> {{ $your_arenas }}</p>
            <p><strong>Arena Pertandingan:</strong> {{ $match_arena }}</p>
            <p style="margin-top: 15px; font-size: 14px; color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px;">
                ⚠️ Anda hanya bisa mengakses pertandingan di arena yang telah di-assign kepada Anda.
            </p>
        </div>
        @endif
        
        <a href="{{ url()->previous() }}" class="btn-back">← Kembali</a>
    </div>
</body>
</html>
