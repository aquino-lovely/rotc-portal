<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Alaminos-Infanta ROTC Portal</title>
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
        .password-wrap {
            position: relative;
        }
        .password-wrap input {
            padding-right: 40px;
        }
        .toggle-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
            color: #555;
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
        .forgot-link {
            display: block;
            margin-top: 14px;
            font-size: 13px;
            color: #333;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }
        .register-btn {
            display: inline-block;
            margin-top: 28px;
            background: #3d6e3d;
            color: #fff;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 24px;
            font-size: 15px;
            font-weight: 600;
        }
        .register-btn:hover { background: #2f5a2f; }
        .footer-bar {
            background: #4f8a4f;
            height: 70px;
            width: 100%;
        }
        .alert {
            width: 360px;
            margin-bottom: 16px;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            text-align: left;
        }
        .alert-success {
            background: #eaf6ea;
            border: 1px solid #b9dcb9;
            color: #2f5a2f;
        }
        .alert-error {
            background: #fdeaea;
            border: 1px solid #e3b9b9;
            color: #a33;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>

<body>

    <div class="header-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
    </div>

    <div class="content">

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="login-card">
            <h2>Login to your Account</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

        <div class="field">
            <label for="student_number">Username:</label>
            <input id="student_number" type="text" name="student_number" value="{{ old('student_number') }}" placeholder="Enter your student number" autofocus required>
        </div>

                <div class="field">
                    <label for="password">Password:</label>
                    <div class="password-wrap">
                        <input id="password" type="password" name="password" placeholder="Enter your password" required>
                        <button type="button" class="toggle-eye" onclick="togglePassword()" id="eye-icon">&#128065;</button>
                    </div>
                </div>

                <button type="submit" class="btn-login">Log in</button>

                <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
            </form>
        </div>

        <a href="{{ route('register') }}" class="register-btn">Registration in ROTC</a>

    </div>

    <div class="footer-bar"></div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '&#128064;';
            } else {
                input.type = 'password';
                icon.innerHTML = '&#128065;';
            }
        }
    </script>
    @include('partials.pwa-register')
</body>
</html>