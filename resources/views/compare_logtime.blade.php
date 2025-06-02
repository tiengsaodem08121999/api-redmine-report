<!DOCTYPE html>
<html>
<head>
    <title>Compare Logtime</title>
</head>
<body>
    <h1>Compare Logtime</h1>
    @foreach ($LogtimeMatchReport as $date => $members)
        <h2>{{ $date }}</h2>
        @foreach ($members as $member => $errors)
            <p><strong>{{ $member }}</strong></p>
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endforeach
    @endforeach
</body>
</html> 
