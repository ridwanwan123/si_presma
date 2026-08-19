{{-- resources/views/errors/404.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        }

        .error-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 100vh;
            padding: 20px;
        }

        .error-wrapper img {
            max-width: 550px;
            width: 100%;
            margin-bottom: 32px;
        }

        .error-wrapper p {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0F5132;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <img src="{{ asset('assets/images/404.png') }}" alt="404 - Halaman Tidak Ditemukan">
        <p>Halaman yang kamu cari tidak ditemukan.</p>
    </div>
</body>
</html>