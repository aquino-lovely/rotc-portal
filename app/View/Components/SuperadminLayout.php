<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Super Admin' }} - Alaminos-Infanta ROTC Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f4; color: #222; }

        .top-bar {
            background: #2f6b2f;
            color: #fff;
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid #e6c34a;
        }
        .top-bar h1 { font-size: 22px; }
        .top-bar-right { display: flex; align-items: center; gap: 16px; }
        .user-chip { display: flex; align-items: center; gap: 10px; font-size: 14px; }
        .avatar {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .logout-link {
            color: #fff; background: none; border: none; cursor: pointer;
            font-size: 14px;
        }

        .main-content { max-width: 900px; margin: 32px auto; padding: 0 24px; }
        .page-title { font-size: 24px; font-weight: bold; margin-bottom: 20px; color: #2f6b2f; }

        .alert {
            padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;
        }
        .alert-success { background: #e3f1e3; color: #2f5a2f; border: 1px solid #b9dcb9; }
        .alert-error { background: #fbe4e4; color: #a33; border: 1px solid #f0b6b6; }

        .btn {
            display: inline-block;
            background: #2f6b2f;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 24px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .btn:hover { background: #255525; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        th, td {
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        th { background: #eef5ee; color: #2f6b2f; }
        tr:last-child td { border-bottom: none; }

        form label {
            display: block; margin-top: 14px; margin-bottom: 6px;
            font-size: 14px; font-weight: bold; color: #333;
        }
        form input, form select {
            width: 100%; padding: 10px 12px;
            border: 1px solid #ccc; border-radius: 6px;
            font-size: 14px;
        }
        form .error { color: #c0392b; font-size: 13px; margin-top: 4px; display: block; }
        form button[type="submit"] {
            margin-top: 20px;
        }
        .card {
            background: #fff; padding: 24px; border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08); max-width: 480px;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
        <div class="top-bar-right">
            <div class="user-chip">
                <div class="avatar">&#128100;</div>
                <div>{{ auth()->user()->name ?? 'Super Admin' }}<br><small style="opacity:0.85;">Super Admin</small></div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-link">&#8650; Log Out</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{ $slot }}
    </div>

</body>
</html>