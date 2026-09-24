<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    @include('partials.pwa-head')
    <style>
        .pw-card {
            background: #fff;
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            max-width: 480px;
        }
        .pw-card-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .pw-field { margin-bottom: 18px; }
        .pw-field label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .pw-input-wrap { position: relative; }
        .pw-input-wrap input {
            width: 100%;
            padding: 12px 44px 12px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .pw-input-wrap input:focus {
            outline: none;
            border-color: #3d6e3d;
        }
        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
        }
        .pw-submit {
            background: #3d6e3d;
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .pw-submit:hover { background: #2f5a2f; }
    </style>
</head>
<body>

<div class="app-shell" style="flex-direction: column;">

    <div class="top-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
        <div class="top-bar-right">
            <div class="user-chip">
                <div class="avatar">&#128100;</div>
                <div>
                    {{ auth()->user()->name }}<br>
                    <small style="opacity:0.85;">
                        {{ auth()->user()->role === 'cadet' ? 'Cadet ' . (auth()->user()->cadet->rank ?? '') : ucfirst(str_replace('_',' ', auth()->user()->role)) }}
                    </small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-link" style="background:none;border:none;cursor:pointer;">
                    &#8650; Log Out
                </button>
            </form>
        </div>
    </div>

    <div class="body-row">
       @include('partials.sidebar')

        <div class="main-content">

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="page-title">Change Password</div>

            <div class="pw-card">
                <div class="pw-card-title">Update Your Password</div>

                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @php $forced = auth()->user()->must_change_password; @endphp

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    @unless($forced)
                    <div class="pw-field">
                        <label>Current Password</label>
                        <div class="pw-input-wrap">
                            <input type="password" name="current_password" id="current_password" autocomplete="current-password">
                            <button type="button" class="pw-toggle" onclick="toggle('current_password')">&#128065;</button>
                        </div>
                    </div>
                    @endunless

                    <div class="pw-field">
                        <label>New Password</label>
                        <div class="pw-input-wrap">
                            <input type="password" name="password" id="password" required autocomplete="new-password">
                            <button type="button" class="pw-toggle" onclick="toggle('password')">&#128065;</button>
                        </div>
                    </div>

                    <div class="pw-field">
                        <label>Confirm New Password</label>
                        <div class="pw-input-wrap">
                            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password">
                            <button type="button" class="pw-toggle" onclick="toggle('password_confirmation')">&#128065;</button>
                        </div>
                    </div>

                    <button type="submit" class="pw-submit">Change Password</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
function toggle(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

@include('partials.pwa-register')
</body>
</html>