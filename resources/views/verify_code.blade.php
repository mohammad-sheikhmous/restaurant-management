<!DOCTYPE html>
<html lang="">
<head>
    <title>رمز التحقق</title>
</head>
<body>
<h2>مرحبًا {{ $user->name }}</h2>
<p>رمز التحقق الخاص بك هو:</p>
<h1 style="color:#2c3e50;">{{ $code }}</h1>
<p>يرجى إدخاله في التطبيق لتأكيد بريدك الإلكتروني.</p>
</body>
</html>
