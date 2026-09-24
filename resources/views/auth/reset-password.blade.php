<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Alaminos-Infanta ROTC Portal</title>
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
            margin-bottom: 20px;
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
    </style>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>

<body>

    <div class="header-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
    </div>

    <div class="content">

        <div class="login-card">
            <h2>Reset Your Password</h2>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="field">
                    <label for="email">Email:</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">New Password:</label>
                    <input id="password" type="password" name="password" required>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                    @error('password_confirmation')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Reset Password</button>
            </form>
        </div>

    </div>

    <div class="footer-bar"></div>

</body>
</html>