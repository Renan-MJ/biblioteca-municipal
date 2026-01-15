<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Municipal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">📚 Biblioteca</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="livros.php">Livros</a></li>
                <li class="nav-item"><a class="nav-link" href="leitores.php">Leitores</a></li>
                <li class="nav-item"><a class="nav-link" href="emprestimos.php">Empréstimos</a></li>
            </ul>
        </div>
    </div>
</nav>

<main>
    <div class="container mt-4">
