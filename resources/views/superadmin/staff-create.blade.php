<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Staff Account - Alaminos-Infanta ROTC Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f4; color: #222; }

        .top-bar {
            background: #4f8a4f;
            color:#f4f4f4;
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

        .staff-wrap { max-width: 500px; margin: 32px auto; padding: 0 24px; }
        .staff-title { font-size: 24px; font-weight: bold; color: #2f6b2f; margin-bottom: 20px; }
        .staff-card { background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .staff-card label { display: block; margin-top: 14px; margin-bottom: 6px; font-size: 14px; font-weight: bold; color: #333; }
        .staff-card input, .staff-card select { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .staff-error { color: #c0392b; font-size: 13px; margin-top: 4px; display: block; }
        .staff-btn { display: inline-block; background: #2f6b2f; color: #fff; padding: 10px 20px; border: none; border-radius: 24px; cursor: pointer; font-size: 14px; margin-top: 20px; }
        .staff-btn:hover { background: #255525; }

        .layout { display: flex; min-height: calc(100vh - 78px); }
.sidebar {
    width: 230px;
    flex-shrink: 0;
    background: #2f5a31;
    padding: 20px 10px;
}
.sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    margin-bottom: 6px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
}
.sidebar a:hover { background: rgba(255, 255, 255, 0.15); }
.sidebar a.active { background: #6b9a6d; color: #fff; }
.staff-wrap { flex: 1; }
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

    <div class="layout">
   

      @include('partials.sidebar')


    <div class="staff-wrap">
        <div class="staff-title">Create Staff Account</div>

        <div class="staff-card">
            <form method="POST" action="{{ route('staff.store') }}">
                @csrf

                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}">
                @error('name') <span class="staff-error">{{ $message }}</span> @enderror

                <label>Username</label>
                <input type="text" name="username" value="{{ old('username') }}">
                @error('username') <span class="staff-error">{{ $message }}</span> @enderror

                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}">
                @error('email') <span class="staff-error">{{ $message }}</span> @enderror

             <label>Role</label>
            <select name="role">
                <option value="deputy_s1" {{ old('role') == 'deputy_s1' ? 'selected' : '' }}>Deputy S1</option>
                <option value="S1" {{ old('role') == 'S1' ? 'selected' : '' }}>S1</option>
                <option value="GMA" {{ old('role') == 'GMA' ? 'selected' : '' }}>GMA</option>
            </select>
                @error('role') <span class="staff-error">{{ $message }}</span> @enderror

                <button type="submit" class="staff-btn">Create Account</button>
            </form>
        </div>
    </div>
 </div>
</body>
</html>