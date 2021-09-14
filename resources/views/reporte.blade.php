<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes de conteo</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="{{ asset('css/vue-croppa.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vue-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dragon-desing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fotocheck.css') }}">
    <link rel="stylesheet" href="{{ asset('font-awesome-4.7.0/css/font-awesome.min.css') }}">
    {{-- @laravelPWA --}}
</head>
<body>
    <div id="app"></div>
    <script src="{{ asset('socket.io.js') }}"></script>
    @include('footer')
</body>
</html>