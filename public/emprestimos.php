<?php
require_once __DIR__ . '/../config/database.php';


/**
 * ==============================
 * CONFIGURAÇÃO DE ERROS (DEV)
 * ==============================
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$erro = null;

/**
 * ==============================
 * DEVOLVER LIVRO
 * ==============================
 */
if (isset($_POST['devolver_id'])) {

    $id = (int) $_POST['devolver_id'];

    $stmt = $pdo->prepare("SELECT livro_id FROM emprestimos WHERE id = ?");
    $stmt->execute([$id]);
    $emprestimo = $stmt->fetch();

    if ($emprestimo) {
        $pdo->prepare("UPDATE emprestimos SET devolvido = 1 WHERE id = ?")
            ->execute([$id]);

        $pdo->prepare("UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?")
            ->execute([$emprestimo['livro_id']]);

        header("Location: emprestimos.php?sucesso=devolvido");
        exit;
    }
}

/**
 * ==============================
 * REGISTRAR / EDITAR EMPRÉSTIMO
 * ==============================
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['devolver_id'])) {

    $editar_id       = $_POST['editar_id'] ?? null;
    $livro_id        = $_POST['livro_id'] ?? null;
    $leitor_id       = $_POST['leitor_id'] ?? null;
    $data_emprestimo = $_POST['data_emprestimo'] ?? null;
    $data_devolucao  = $_POST['data_devolucao'] ?? null;

    if ($livro_id && $leitor_id && $data_emprestimo) {

        if ($editar_id) {

            $stmt = $pdo->prepare("SELECT livro_id FROM emprestimos WHERE id = ?");
            $stmt->execute([$editar_id]);
            $antigo = $stmt->fetch();

            if ($antigo && $antigo['livro_id'] != $livro_id) {

                $check = $pdo->prepare("SELECT quantidade FROM livros WHERE id = ?");
                $check->execute([$livro_id]);
                $novo = $check->fetch();

                if (!$novo || $novo['quantidade'] <= 0) {
                    $erro = "❌ O livro selecionado não possui estoque.";
                } else {
                    $pdo->prepare("UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?")
                        ->execute([$antigo['livro_id']]);

                    $pdo->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE id = ?")
                        ->execute([$livro_id]);
                }
            }

            if (!$erro) {
                $pdo->prepare("
                    UPDATE emprestimos SET
                        livro_id = ?,
                        leitor_id = ?,
                        data_emprestimo = ?,
                        data_devolucao = ?
                    WHERE id = ?
                ")->execute([
                    $livro_id,
                    $leitor_id,
                    $data_emprestimo,
                    $data_devolucao,
                    $editar_id
                ]);

                header("Location: emprestimos.php?sucesso=editado");
                exit;
            }

        } else {

            $check = $pdo->prepare("SELECT quantidade FROM livros WHERE id = ?");
            $check->execute([$livro_id]);
            $livro = $check->fetch();

            if (!$livro || $livro['quantidade'] <= 0) {
                $erro = "❌ Livro sem estoque.";
            } else {

                $pdo->prepare("
                    INSERT INTO emprestimos 
                    (livro_id, leitor_id, data_emprestimo, data_devolucao, devolvido)
                    VALUES (?, ?, ?, ?, 0)
                ")->execute([
                    $livro_id,
                    $leitor_id,
                    $data_emprestimo,
                    $data_devolucao
                ]);

                $pdo->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE id = ?")
                    ->execute([$livro_id]);

                header("Location: emprestimos.php?sucesso=registrado");
                exit;
            }
        }
    }
}

/**
 * ==============================
 * BUSCAR PARA EDIÇÃO
 * ==============================
 */
$editar_emprestimo = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM emprestimos WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editar_emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * ==============================
 * LIVROS E LEITORES
 * ==============================
 */
$livros = $pdo->query("SELECT id, titulo, quantidade FROM livros ORDER BY titulo")->fetchAll();
$leitores = $pdo->query("SELECT id, nome FROM leitores ORDER BY nome")->fetchAll();

/**
 * ==============================
 * PAGINAÇÃO (ADICIONADO)
 * ==============================
 */
$limite = 10;
$pagina = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$pagina = max($pagina, 1);
$offset = ($pagina - 1) * $limite;

$totalEmprestimos = $pdo->query("SELECT COUNT(*) FROM emprestimos")->fetchColumn();
$totalPaginas = ceil($totalEmprestimos / $limite);

/**
 * ==============================
 * LISTAR EMPRÉSTIMOS (PAGINADO)
 * ==============================
 */
$stmt = $pdo->prepare("
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
    LIMIT :limite OFFSET :offset
");
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/layout/header.php';
?>

<h2 class="mb-4">🔄 Gestão de Empréstimos</h2>

<?php if ($erro): ?>
    <div class="alert alert-danger shadow-sm"><?= $erro ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning fw-semibold">
                <?= $editar_emprestimo ? "✏️ Editar Empréstimo" : "➕ Registrar Empréstimo" ?>
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
                                    <?= $livro['quantidade'] <= 0 ? 'disabled' : '' ?>
                                    <?= ($editar_emprestimo && $editar_emprestimo['livro_id'] == $livro['id']) ? 'selected' : '' ?>>
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
                                    <?= ($editar_emprestimo && $editar_emprestimo['leitor_id'] == $leitor['id']) ? 'selected' : '' ?>>
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
                        <a href="emprestimos.php" class="btn btn-outline-secondary w-100 mt-2">
                            Cancelar
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- LISTAGEM -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                <span>📋 Empréstimos Registrados</span>
                <input type="text" id="buscaEmprestimo"
                       class="form-control form-control-sm w-50"
                       placeholder="Buscar por livro ou leitor">
            </div>

            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0" id="tabelaEmprestimos">
                    <thead class="table-light">
                        <tr>
                            <th>Livro</th>
                            <th>Leitor</th>
                            <th>Empréstimo</th>
                            <th>Devolução</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emprestimos as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['livro']) ?></td>
                                <td><?= htmlspecialchars($e['leitor']) ?></td>
                                <td><?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></td>
                                <td><?= $e['data_devolucao'] ? date('d/m/Y', strtotime($e['data_devolucao'])) : '-' ?></td>
                                <td>
                                    <?= $e['devolvido']
                                        ? '<span class="badge bg-success">Devolvido</span>'
                                        : '<span class="badge bg-warning text-dark">Em aberto</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!$e['devolvido']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="devolver_id" value="<?= $e['id'] ?>">
                                            <button class="btn btn-sm btn-success">Devolver</button>
                                        </form>
                                        <a href="?edit=<?= $e['id'] ?>" class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINAÇÃO -->
            <?php if ($totalPaginas > 1): ?>
                <div class="card-footer">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $pagina - 1 ?>">Anterior</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $pagina + 1 ?>">Próxima</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<script>
// BUSCA EMPRÉSTIMOS
document.getElementById('buscaEmprestimo').addEventListener('keyup', function () {
    const termo = this.value.toLowerCase();
    document.querySelectorAll('#tabelaEmprestimos tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(termo) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
