<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ROTC Registration Type</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f4f4;
        }

        .header-bar {
            background: #4f8a4f;
            padding: 28px 40px;
            border-bottom: 6px solid #e8c440;
        }

        .header-bar h1 {
            color: white;
            font-size: 26px;
        }

        .page {
            max-width: 700px;
            margin: 60px auto;
            padding: 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
        }

        .description {
            color: #666;
            margin-bottom: 30px;
        }

        .choice-container {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .choice-btn {
            width: 260px;
            padding: 25px;
            border: none;
            border-radius: 8px;
            background: #3d6e3d;
            color: white;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
        }

        .choice-btn:hover {
            background: #2f5a2f;
        }
    </style>
</head>

<body>

    <div class="header-bar">
        <h1>Alaminos-Infanta ROTC Portal</h1>
    </div>

    <div class="page">

        <h2>ROTC REGISTRATION</h2>

        <p class="description">
            Please select your enrollee type.
        </p>

        <form method="POST" action="{{ route('register.type.store') }}">
            @csrf

            <div class="choice-container">

                <button
                    type="submit"
                    name="enrollee_type"
                    value="psu_student"
                    class="choice-btn">
                    PSU Student
                </button>

                <button
                    type="submit"
                    name="enrollee_type"
                    value="cross_enrollee"
                    class="choice-btn">
                    Cross Enrollee
                </button>

            </div>

        </form>

    </div>

</body>
</html>
```
