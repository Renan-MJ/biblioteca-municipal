<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$erro = null;
$sucesso = null;


// ==============================
// DEVOLVER LIVRO
// ==============================
if (isset($_POST['devolver_id'])) {
    $id = $_POST['devolver_id'];

    $stmt = $pdo->prepare("SELECT livro_id FROM emprestimos WHERE id = ?");
    $stmt->execute([$id]);
    $emprestimo = $stmt->fetch();

    if ($emprestimo) {
        $pdo->prepare("UPDATE emprestimos SET devolvido = 1 WHERE id = ?")->execute([$id]);
        $pdo->prepare("UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?")->execute([$emprestimo['livro_id']]);
    }

    header("Location: emprestimos.php");
    exit;
}

// ==============================
// REGISTRAR OU EDITAR EMPRÉSTIMO
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editar_id = $_POST['editar_id'] ?? null;
    $livro_id = $_POST['livro_id'] ?? null;
    $leitor_id = $_POST['leitor_id'] ?? null;
    $data_emprestimo = $_POST['data_emprestimo'] ?? null;
    $data_devolucao = $_POST['data_devolucao'] ?? null;

    if ($livro_id && $leitor_id && $data_emprestimo) {
        if ($editar_id) {
            // ==========================
            // EDIÇÃO
            // ==========================
            // Buscar empréstimo antigo
            $stmt = $pdo->prepare("SELECT livro_id FROM emprestimos WHERE id = ?");
            $stmt->execute([$editar_id]);
            $antigo = $stmt->fetch();

            if ($antigo) {
                $livro_antigo = $antigo['livro_id'];

                // Se o livro mudou, ajustar estoque
                if ($livro_antigo != $livro_id) {
                    // Verificar estoque do novo livro
                    $check = $pdo->prepare("SELECT quantidade FROM livros WHERE id = ?");
                    $check->execute([$livro_id]);
                    $novo_livro = $check->fetch();
                    if (!$novo_livro || $novo_livro['quantidade'] <= 0) {
                        $erro = "❌ O livro selecionado não possui estoque disponível.";
                    }

                    // Aumentar estoque do livro antigo
                    $pdo->prepare("UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?")
                        ->execute([$livro_antigo]);
                    // Diminuir estoque do livro novo
                    $pdo->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE id = ? AND quantidade > 0")
                        ->execute([$livro_id]);
                }

                // Atualizar empréstimo
                $pdo->prepare("
                    UPDATE emprestimos SET
                        livro_id = ?,
                        leitor_id = ?,
                        data_emprestimo = ?,
                        data_devolucao = ?
                    WHERE id = ?
                ")->execute([$livro_id, $leitor_id, $data_emprestimo, $data_devolucao, $editar_id]);

                header("Location: emprestimos.php");
                exit;
            }
        } else {
            // ==========================
            // NOVO EMPRÉSTIMO
            // ==========================
            // Verificar estoque
            $check = $pdo->prepare("SELECT quantidade FROM livros WHERE id = ?");
            $check->execute([$livro_id]);
            $livro = $check->fetch();
            if (!$livro || $livro['quantidade'] <= 0) {
                $erro = "❌ Livro sem estoque disponível para empréstimo.";
            }

            if (!$erro) {
                // Inserir empréstimo
                $pdo->prepare("
                    INSERT INTO emprestimos (livro_id, leitor_id, data_emprestimo, data_devolucao, devolvido)
                    VALUES (?, ?, ?, ?, 0)
                ")->execute([$livro_id, $leitor_id, $data_emprestimo, $data_devolucao]);

                // Diminuir estoque
                $pdo->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE id = ? AND quantidade > 0")
                    ->execute([$livro_id]);

                $sucesso = "📚 Empréstimo registrado com sucesso!";
            }

        }
    }
}

// ==============================
// BUSCAR EMPRÉSTIMO PARA EDIÇÃO
// ==============================
$editar_emprestimo = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM emprestimos WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editar_emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ==============================
// BUSCAR LIVROS E LEITORES
// ==============================
$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);
$leitores = $pdo->query("SELECT id, nome FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// LISTAR EMPRÉSTIMOS
// ==============================
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

<?php if ($erro): ?>
    <div class="alert alert-danger">
        <?= $erro ?>
    </div>
<?php endif; ?>

<?php if ($sucesso): ?>
    <div class="alert alert-success">
        <?= $sucesso ?>
    </div>
<?php endif; ?>


<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                <?= $editar_emprestimo ? "Editar Empréstimo" : "Registrar Empréstimo" ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($editar_emprestimo): ?>
                        <input type="hidden" name="editar_id" value="<?= $editar_emprestimo['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Livro</label>
                        <select name="livro_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($livros as $livro): ?>
                                <option value="<?= $livro['id'] ?>"
                                    <?= ($editar_emprestimo && $editar_emprestimo['livro_id']==$livro['id']) ? 'selected' : '' ?>>
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
                                <option value="<?= $leitor['id'] ?>"
                                    <?= ($editar_emprestimo && $editar_emprestimo['leitor_id']==$leitor['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($leitor['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data do Empréstimo</label>
                        <input type="date" name="data_emprestimo" class="form-control"
                               value="<?= $editar_emprestimo['data_emprestimo'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data de Devolução</label>
                        <input type="date" name="data_devolucao" class="form-control"
                               value="<?= $editar_emprestimo['data_devolucao'] ?? '' ?>">
                    </div>

                    <button class="btn btn-dark w-100">
                        <?= $editar_emprestimo ? "Salvar Alterações" : "Registrar Empréstimo" ?>
                    </button>
                    <?php if ($editar_emprestimo): ?>
                        <a href="emprestimos.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                    <?php endif; ?>
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
                            <tr><td colspan="6">Nenhum empréstimo registrado</td></tr>
                        <?php else: ?>
                            <?php foreach ($emprestimos as $e): ?>
                                <tr>
                                    <td><?= htmlspecialchars($e['livro']) ?></td>
                                    <td><?= htmlspecialchars($e['leitor']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></td>
                                    <td><?= $e['data_devolucao'] ? date('d/m/Y', strtotime($e['data_devolucao'])) : '-' ?></td>
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
                                                <button class="btn btn-sm btn-success">Devolver</button>
                                            </form>
                                            <a href="?edit=<?= $e['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
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
