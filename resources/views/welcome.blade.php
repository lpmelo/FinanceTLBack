<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FinanceTL</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{asset('favicon.ico')}}">
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="background-image">
    </div>
    <div class="content-container">
        <div class="card">
            <div class="logo-container"><img src="{{ asset('favicon.ico') }}" /></div>
            <div class="title-container">
                <p>FinaceTL API</p>
            </div>
            <p class="routes-title">Rotas da API</p>
            <div class="api-routes-container">
                <div class="default-card" onclick="navigateTo('userview')">
                    <div class="card-title">
                        <p>Usuários</p>
                    </div>
                    <div class="card-content">
                        <p>Para acessar as funcionalidades relacionadas aos usuários utilize a rota:</p>
                        <p>/users</p>
                    </div>
                </div>

                <div class="default-card" onclick="navigateTo('authview')">
                    <div class="card-title">
                        <p>Autenticação</p>
                    </div>
                    <div class="card-content">
                        <p>Para acessar as funcionalidades relacionadas a autenticação utilize a rota:</p>
                        <p>/auth</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>