<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nogaru Car Rental')</title>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
</head>
<body class="authentication-bg position-relative">
<div class="account-pages pt-5 pb-4"><div class="container"><div class="row justify-content-center"><div class="col-lg-5">@yield('content')</div></div></div></div>
</body>
</html>
