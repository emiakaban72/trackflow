<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Global Supply Chain Risk Intelligence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --corporate-dark: #1E293B;
            --matcha-500: #f97316;
            --matcha-700: #ea580c;
        }
        body {
            background-color: #F8FAFC;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex justify-content-center align-items-center mb-3 shadow-sm" style="width: 60px; height: 60px; background-color: var(--matcha-500); border-radius: 12px;">
                            <i class="fa-solid fa-anchor text-white fs-3"></i>
                        </div>
                        <h4 class="fw-bold" style="color: var(--corporate-dark);">Track</h4>
                        <p class="text-muted small">Global Supply Chain Risk Intelligence</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger small rounded-3 border-0">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.process') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">ALAMAT EMAIL</label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-2" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-bold">PASSWORD</label>
                            <input type="password" name="password" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <button type="submit" class="btn w-100 text-white fw-bold py-2 mb-3 shadow-sm" style="background-color: var(--matcha-700); border-radius: 8px;">
                            Masuk ke Sistem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>