<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ورود</title>
    @vite(['resources/js/login.js', 'node_modules/primeicons/primeicons.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script>
        window.captcha = '{{ captcha_src() }}'
    </script>
    <style>
        @font-face {
            font-family: IRANSanss;!important;
            src: url({{ asset('assets/fonts/IRANSansWeb.ttf') }});!important;
        }
        * {
            font-family: IRANSanss, Helvetica Neue, Helvetica, Arial, sans-serif!important;
        }
        #app{
            background-image: url("{{ asset('login.jpg') }}");
            background-repeat: no-repeat;
            background-size: cover;
            /*background-position: center;*/
        }
    </style>
</head>
<body id="app">
</body>
</html>
