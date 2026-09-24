<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Alaminos-Infanta ROTC Portal</title>
    @include('partials.pwa-head')
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header-bar {
            background: #4f8a4f;
            padding: 28px 40px;
            border-bottom: 6px solid #e8c440;
        }
        .header-bar h1 {
            color: #fff;
            font-size: 26px;
            font-weight: 700;
        }
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }
        .login-card {
            background: #fafafa;
            border-radius: 6px;
            padding: 32px 40px;
            width: 360px;
            text-align: center;
        }
        .login-card h2 {
            color: #3d7a3d;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .login-card .description {
            font-size: 13px;
            color: #555;
            text-align: left;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .field {
            text-align: left;
            margin-bottom: 16px;
        }
        .field label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
            color: #222;
        }
        .field input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background: #fff;
        }
        .btn-login {
            width: 100%;
            background: #3d6e3d;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 24px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
        }
        .btn-login:hover { background: #2f5a2f; }
        .footer-bar {
            background: #4f8a4f;
            height: 70px;
            width: 100%;
        }
        .field-error {
            color: #a33;
            font-size: 12px;
            margin-top: 4px;
        }
        .alert {
            width: 100%;
            margin-bottom: 16px;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            text-align: left;
            background: #eaf6ea;
            border: 1px solid #b9dcb9;
            color: #2f5a2f;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>

<body>

    <div class="header-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
    </div>

    <div class="content">

        <div class="login-card">
            <h2>Forgot Password</h2>

            <div class="description">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="field">
                    <label for="email">Email:</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Email Password Reset Link</button>
            </form>
        </div>

    </div>

    <div class="footer-bar"></div>

</body>
</html>