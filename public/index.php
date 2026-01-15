<?php
require_once __DIR__ . '/../config/database.php';

// CONTADORES
$totalLivros = $pdo->query("SELECT COUNT(*) FROM livros")->fetchColumn();
$totalLeitores = $pdo->query("SELECT COUNT(*) FROM leitores")->fetchColumn();
$totalEmprestimos = $pdo->query(
    "SELECT COUNT(*) FROM emprestimos WHERE devolvido = 0"
)->fetchColumn();

include 'layout/header.php';
?>



<h2 class="mb-4 border-bottom pb-2">
    📚 Sistema da Biblioteca Municipal
</h2>

<p class="text-muted">
    Sistema interno para gerenciamento de livros, leitores e empréstimos da Biblioteca Municípal de Pontal do Paraná.
</p>

<div class="row mt-4">

    <div class="col-md-4">
        <a href="livros.php" class="text-decoration-none">
            <div class="card card-hover shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">📚 Livros</h5>
                    <p class="display-6 mb-0"><?= $totalLivros ?></p>
                    <small class="text-muted">Cadastrados</small>
                </div>
            </div>
        </a>
    </div>


    <div class="col-md-4">
        <a href="leitores.php" class="text-decoration-none">
            <div class="card card-hover shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">👤 Leitores</h5>
                    <p class="display-6 mb-0"><?= $totalLeitores ?></p>
                    <small class="text-muted">Cadastrados</small>
                </div>
            </div>
        </a>
    </div>


    <div class="col-md-4">
        <a href="emprestimos.php" class="text-decoration-none">
            <div class="card card-hover shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">🔄 Empréstimos</h5>
                    <p class="display-6 mb-0"><?= $totalEmprestimos ?></p>
                    <small class="text-muted">Ativos</small>
                </div>
            </div>
        </a>
    </div>


</div>

<?php include 'layout/footer.php'; ?>
