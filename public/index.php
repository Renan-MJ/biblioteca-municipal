<?php
require_once __DIR__ . '/../config/database.php';

// =========================
// CONTADORES
// =========================
$totalLivros = $pdo->query("SELECT COUNT(*) FROM livros")->fetchColumn();
$totalLeitores = $pdo->query("SELECT COUNT(*) FROM leitores")->fetchColumn();
$totalEmprestimos = $pdo->query(
    "SELECT COUNT(*) FROM emprestimos WHERE devolvido = 0"
)->fetchColumn();

// =========================
// EMPRÉSTIMOS ATRASADOS (QTD)
// =========================
$qtdAtrasados = $pdo->query("
    SELECT COUNT(*) 
    FROM emprestimos
    WHERE devolvido = 0
      AND data_devolucao < CURDATE()
")->fetchColumn();

// =========================
// LISTA DE ATRASADOS (TOP 5)
// =========================
$atrasados = $pdo->query("
    SELECT 
        r.nome AS leitor,
        l.titulo AS livro,
        e.data_devolucao
    FROM emprestimos e
    JOIN leitores r ON r.id = e.leitor_id
    JOIN livros l ON l.id = e.livro_id
    WHERE e.devolvido = 0
      AND e.data_devolucao < CURDATE()
    ORDER BY e.data_devolucao ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

include 'layout/header.php';
?>

<h2 class="mb-4 border-bottom pb-2">
    📚 Sistema da Biblioteca Municipal
</h2>

<?php if ($qtdAtrasados > 0): ?>
    <div class="alert alert-danger shadow-sm d-flex justify-content-between align-items-center mt-3">
        <div>
            ⚠️ <strong>Atenção!</strong>
            Existem <strong><?= $qtdAtrasados ?></strong> empréstimo(s) atrasado(s).
        </div>

        <a href="emprestimos.php" class="btn btn-sm btn-light text-danger fw-semibold">

            Ver empréstimos
        </a>
    </div>
<?php endif; ?>

<?php if ($qtdAtrasados > 0): ?>
    <div class="card shadow-sm border-0 mt-3">
        <div class="card-header bg-danger text-white fw-semibold">
            ⏰ Empréstimos atrasados
        </div>

        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Devolver até</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($atrasados as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['leitor']) ?></td>
                            <td><?= htmlspecialchars($a['livro']) ?></td>
                            <td class="text-danger fw-bold">
                                <?= date('d/m/Y', strtotime($a['data_devolucao'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<p class="text-muted mt-4">
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
