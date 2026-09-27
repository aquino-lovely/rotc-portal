<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Accounts - Alaminos-Infanta ROTC Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f4; color: #222; }

        .top-bar {
            background: #4f8a4f;
            color: #f4f4f4;
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 6px solid #e8c440;
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
        .logout-btn {
            display: flex; align-items: center; gap: 6px;
            color: #fff; background: none; border: none; cursor: pointer;
            font-size: 14px;
        }

        .staff-wrap { max-width: 900px; margin: 32px auto; padding: 0 24px; }
        .staff-title { font-size: 24px; font-weight: bold; color: #2f6b2f; margin-bottom: 20px; }
        .staff-alert { background: #e3f1e3; color: #2f5a2f; border: 1px solid #b9dcb9; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .staff-btn { display: inline-block; background:#4f8a4f; color: #fff; padding: 10px 20px; border-radius: 24px; text-decoration: none; font-size: 14px; margin-bottom: 20px; }
        .staff-btn:hover { background: #255525; }
        .staff-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .staff-table th, .staff-table td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 14px; }
        .staff-table th { background: #eef5ee; color: #4f8a4f; }
        .staff-table tr:last-child td { border-bottom: none; }

        .action-cell { display: flex; gap: 10px; }
        .delete-btn {
            background: #c0392b; color: #fff; border: none; padding: 6px 14px;
            border-radius: 16px; font-size: 13px; cursor: pointer;
        }
        .delete-btn:hover { background: #a5301f; }
        .edit-link {
            background: #4f8a4f; color: #fff; padding: 6px 14px;
            border-radius: 16px; font-size: 13px; text-decoration: none;
        }
        .edit-link:hover { background: #255525; }
    </style>
</head>
<body>

    <div class="top-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
        <div class="top-bar-right">
            <div class="user-chip">
                <div class="avatar">&#128100;</div>
                <div>
                    {{ auth()->user()->name ?? 'Super Admin' }}<br>
                    <small style="opacity:0.85;">Super admin</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">&#8650; Log Out</button>
            </form>
        </div>
    </div>

    <div class="staff-wrap">
        <div class="staff-title">Staff Accounts</div>

        @if(session('success'))
            <div class="staff-alert">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="staff-alert" style="background:#fbeaea; color:#a5301f; border-color:#f0c4c4;">{{ session('error') }}</div>
        @endif

        <a href="{{ route('staff.create') }}" class="staff-btn">+ Create Account</a>

        <table class="staff-table">
            <tr><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr>
            @foreach($staffAccounts as $account)
                <tr>
                    <td>{{ $account->username }}</td>
                    <td>{{ $account->email }}</td>
                    <td>{{ $account->role }}</td>
                    <td class="action-cell">
                        <form method="POST" action="{{ route('staff.destroy', $account->id) }}"
                              onsubmit="return confirm('Delete {{ $account->username }}? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

</body>
</html>