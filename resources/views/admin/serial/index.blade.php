<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Serial Numbers - Alaminos-Infanta ROTC Portal</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
                    <small style="opacity:0.85;">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</small>
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

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
    <div class="alert alert-danger">Some serial numbers are invalid. Please check the highlighted rows.</div>
@endif

            <div class="page-title">Serial Numbers</div>
            <div class="page-subtitle">Assign or update serial numbers for approved cadets</div>

            <div class="card">
                <div class="search-row">
                   <form method="GET" action="{{ route('admin.serial.index') }}"
      style="display:flex; gap:10px; flex:1;">

    <select name="school_year" class="search-input" style="max-width:180px;" onchange="this.form.submit()">
        <option value="all" @selected($schoolYear === 'all')>All batches</option>
        @foreach($schoolYears as $sy)
            <option value="{{ $sy }}" @selected($schoolYear === $sy)>S.Y. {{ $sy }}</option>
        @endforeach
    </select>

    <input type="text" name="search" class="search-input"
           placeholder="Search by name, student number, serial number"
           value="{{ request('search') }}">
    <button type="submit" class="btn-green">Search</button>
</form>
                </div>

                <form method="POST" action="{{ route('admin.serial.update') }}">
                    @csrf
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>STUDENT NO.</th>
                                <th>NAME</th>
                                <th>RANK</th>
                                <th>COMPANY</th>
                                <th>CADET TYPE</th>
                                <th>SERIAL NUMBER</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cadets as $cadet)
                            <tr>
                                <td>{{ $cadet->cadet->student_number ?? '—' }}</td>
                                <td>{{ $cadet->cadet->last_name ?? '' }}, {{ $cadet->cadet->first_name ?? '' }}</td>
                                <td>{{ $cadet->cadet->rank ?? '—' }}</td>
                                <td>{{ $cadet->cadet->company ?? '—' }}</td>
                                <td>{{ ucfirst($cadet->cadet->cadet_type ?? '—') }}</td>
                                <td>
                                <input type="text"
                                    name="serial[{{ $cadet->id }}]"
                                    value="{{ old('serial.'.$cadet->id, $cadet->cadet->serial_number ?? '') }}"
                                    placeholder="PG-R24-000412 PA(RES)"
                                    pattern="PG-R\d{2}-\d{6} PA\(RES\)"
                                    maxlength="21"
                                    title="Format: PG-R24-000412 PA(RES)"
                                    class="serial-input"
                                    style="padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px; width:200px;">
                                @error('serial.'.$cadet->id)
                                    <small style="color:#c0392b; display:block; margin-top:4px;">{{ $message }}</small>
                                @enderror
                            </td>
                                                        </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center; color:#666; padding:30px;">
                                    No approved cadets found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">
                        <div>{{ $cadets->withQueryString()->links() }}</div>
                        <button type="submit" class="btn-green" style="padding:10px 28px;">
                            Save All
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.serial-input').forEach(input => {
    input.addEventListener('blur', () => {
        const raw = input.value.trim();
        if (!raw) return;

        const digits = raw.replace(/\D/g, '');
        if (digits.length === 8) {
            // 2-digit year + 6-digit number
            input.value = `PG-R${digits.slice(0, 2)}-${digits.slice(2)} PA(RES)`;
        } else {
            input.value = raw.toUpperCase();
        }
    });
});
</script>

</body>
</html>