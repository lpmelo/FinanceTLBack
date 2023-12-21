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

            <div class="routes-title">
                <div class="icon-button" onclick="navigateTo('/')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <title>Voltar</title>
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z" />
                    </svg>
                </div>
                Rotas de usuários
            </div>

            <div class="api-routes-container">
                <div class="default-card no-click">
                    <div class="card-title">
                        <span class="tag-request post">POST</span>
                        <a>Cadastrar usuário</a>
                    </div>
                    <div class="card-content">
                        <p>Para cadastrar um usuário, realize um POST para:</p>
                        <p>/users</p>
                        <p>Se atentando para enviar no request body o JSON:</p>
                        <p class="code-console">
                            {
                            <br />
                            "username": "username typed",
                            <br />
                            "password":"password typed",
                            <br />
                            "name": "name typed",
                            <br />
                            "nickname":"nickname typed"
                            <br />
                            }
                        </p>
                        <br />
                        <p>Detalhamento das propriedades:</p>
                        <p class="code-console">
                            username = username digitado pelo usuário,
                            <br />
                            password = password digitado pelo usuário,
                            <br />
                            name = name digitado pelo usuário,
                            <br />
                            nickname =nickname digitado pelo usuário
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>