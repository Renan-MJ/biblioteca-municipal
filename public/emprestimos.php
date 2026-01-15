<?php
require_once __DIR__ . '/../config/database.php';

// DEVOLVER LIVRO
if (isset($_POST['devolver_id'])) {
    $id = $_POST['devolver_id'];

    $sql = "UPDATE emprestimos SET devolvido = 1 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: emprestimos.php");
    exit;
}


// MOSTRAR ERROS (desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// REGISTRAR EMPRÉSTIMO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livro_id = $_POST['livro_id'] ?? null;
    $leitor_id = $_POST['leitor_id'] ?? null;
    $data_emprestimo = $_POST['data_emprestimo'] ?? null;
    $data_devolucao = $_POST['data_devolucao'] ?? null;

    if ($livro_id && $leitor_id && $data_emprestimo) {
        $sql = "INSERT INTO emprestimos 
                (livro_id, leitor_id, data_emprestimo, data_devolucao, devolvido)
                VALUES (?, ?, ?, ?, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $livro_id,
            $leitor_id,
            $data_emprestimo,
            $data_devolucao
        ]);
    }

    header("Location: emprestimos.php");
    exit;
}

// BUSCAR LIVROS
$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")
              ->fetchAll(PDO::FETCH_ASSOC);

// BUSCAR LEITORES
$leitores = $pdo->query("SELECT id, nome FROM leitores ORDER BY nome")
                ->fetchAll(PDO::FETCH_ASSOC);

// LISTAR EMPRÉSTIMOS
$sql = "
SELECT 
    e.id,
    l.titulo AS livro,
    r.nome AS leitor,
    e.data_emprestimo,
    e.data_devolucao,
    e.devolvido
FROM emprestimos e
JOIN livros l ON l.id = e.livro_id
JOIN leitores r ON r.id = e.leitor_id
ORDER BY e.id DESC
";

$emprestimos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/layout/header.php';
?>

<h2>🔄 Empréstimos</h2>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                Registrar Empréstimo
            </div>
            <div class="card-body">

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Livro</label>
                        <select name="livro_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($livros as $livro): ?>
                                <option value="<?= $livro['id'] ?>">
                                    <?= htmlspecialchars($livro['titulo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Leitor</label>
                        <select name="leitor_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($leitores as $leitor): ?>
                                <option value="<?= $leitor['id'] ?>">
                                    <?= htmlspecialchars($leitor['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data do Empréstimo</label>
                        <input type="date" name="data_emprestimo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data de Devolução</label>
                        <input type="date" name="data_devolucao" class="form-control">
                    </div>

                    <button class="btn btn-dark w-100">
                        Registrar Empréstimo
                    </button>
                </form>

            </div>
        </div>
    </div>

    <!-- LISTAGEM -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white">
                Empréstimos
            </div>
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Leitor</th>
                            <th>Empréstimo</th>
                            <th>Devolução</th>
                            <th>Status</th>
                            <th>Ações</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php if (count($emprestimos) === 0): ?>
                            <tr>
                                <td colspan="5">Nenhum empréstimo registrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($emprestimos as $e): ?>
                                <tr>
                                    <td><?= htmlspecialchars($e['livro']) ?></td>
                                    <td><?= htmlspecialchars($e['leitor']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></td>
                                    <td>
                                        <?= $e['data_devolucao']
                                            ? date('d/m/Y', strtotime($e['data_devolucao']))
                                            : '-' ?>
                                    </td>
                                        <td>
                                            <?php if ($e['devolvido']): ?>
                                                <span class="badge bg-success">Devolvido</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Em aberto</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if (!$e['devolvido']): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="devolver_id" value="<?= $e['id'] ?>">
                                                    <button class="btn btn-sm btn-success">
                                                        Devolver
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
