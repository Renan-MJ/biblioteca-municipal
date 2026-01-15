<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta charset="UTF-8">
    <title>Biblioteca Municipal</title>
    

    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }
        .card-hover {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        cursor: pointer;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
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
    <div class="container-fluid">

        <a class="navbar-brand fw-bold fs-4" href="index.php">
            Biblioteca Municipal
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-4">
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

