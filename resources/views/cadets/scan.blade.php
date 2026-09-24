<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @include('partials.pwa-head')
    <title>Scan QR - Alaminos-Infanta ROTC Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI', Arial, sans-serif; background:#1a2e1a; min-height:100vh; }
        .header { background:#4f8a4f; padding:16px 20px; text-align:center; color:#fff; font-weight:700; font-size:16px; }
        .scan-wrap { padding:20px; max-width:420px; margin:0 auto; }
        #reader { border-radius:12px; overflow:hidden; margin-top:16px; }
        .result-box {
            margin-top:20px; padding:20px; border-radius:10px; text-align:center;
            font-size:15px; font-weight:600; display:none;
        }
        .result-success { background:#e3f1e3; color:#2f5a2f; }
        .result-error { background:#fdeaea; color:#a33; }
        .back-link {
            display:block; text-align:center; margin-top:20px;
            color:#fff; font-size:13px; text-decoration:none;
        }
    </style>
</head>
@include('partials.pwa-register')
<body>

<div class="header">Scan Attendance QR</div>

<div class="scan-wrap">
    <div id="reader"></div>
    <div id="resultBox" class="result-box"></div>
    <a href="{{ route('dashboard') }}" class="back-link">← Back to Dashboard</a>
</div>

<script>
function onScanSuccess(decodedText) {
    html5QrcodeScanner.pause();

    fetch("{{ route('cadet.scan.process') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ token: decodedText })
    })
    .then(res => res.json())
    .then(data => {
        const box = document.getElementById('resultBox');
        box.style.display = 'block';
        box.className = 'result-box ' + (data.success ? 'result-success' : 'result-error');
        box.textContent = data.message;

        setTimeout(() => {
            if (data.success) {
                window.location.href = "{{ route('dashboard') }}";
            } else {
                html5QrcodeScanner.resume();
            }
        }, 2500);
    });
}

const html5QrcodeScanner = new Html5Qrcode("reader");
html5QrcodeScanner.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    onScanSuccess
);
</script>

</body>
</html>