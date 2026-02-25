<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — TDR HPZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0b0b0f;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(230,57,70,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(212,168,67,0.04) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-card {
            background: rgba(26,26,46,0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            border-top: 3px solid #e63946;
            box-shadow: 0 8px 40px rgba(0,0,0,0.4);
        }
        .brand-icon {
            width: 48px;
            height: 48px;
            background: #e63946;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            margin: 0 auto 16px;
        }
        .form-control {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            color: #e8e8e8;
            border-radius: 8px;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.08);
            border-color: #e63946;
            color: #e8e8e8;
            box-shadow: 0 0 0 3px rgba(230,57,70,0.15);
        }
        .form-control::placeholder { color: #6b7084; }
        .form-label { color: #e8e8e8; font-weight: 600; font-size: 0.85rem; }
        .btn-primary {
            background: #e63946;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px;
        }
        .btn-primary:hover {
            background: #ff4d5a;
            transform: translateY(-1px);
        }
        .alert-danger {
            background: rgba(230,57,70,0.1);
            border: 1px solid rgba(230,57,70,0.2);
            color: #ff6b7a;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container position-relative" style="z-index:1">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="login-card p-5">
                <div class="text-center mb-4">
                    <div class="brand-icon"><i class="bi bi-gear-fill"></i></div>
                    <h4 class="fw-bold mb-1" style="color:#e8e8e8">TDR HPZ</h4>
                    <p style="color:#6b7084;font-size:0.85rem" class="mb-0">Login Admin Panel</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
