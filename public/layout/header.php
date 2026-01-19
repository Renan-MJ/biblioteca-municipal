<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Municipal</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS próprio -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f5f6f8;
        }

        main {
            flex: 1;
        }

        /* Cards clicáveis (home) */
        .card-hover {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .card-hover a {
            text-decoration: none;
            color: inherit;
            display: block;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary py-3 shadow-sm">
    <div class="container">

        <!-- Logo / Nome -->
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
            📚 Biblioteca Municipal
        </a>

        <!-- Botão mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="menuNavbar">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="livros.php">Livros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="leitores.php">Leitores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="emprestimos.php">Empréstimos</a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<main class="container my-4">
