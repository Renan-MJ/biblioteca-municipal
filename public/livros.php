<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS (apenas desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mensagem = null;
$tipoMensagem = 'success';

/* ==============================
   EXCLUIR LIVRO
============================== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $check = $pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE livro_id = ?");
    $check->execute([$id]);
    $total = $check->fetchColumn();

    if ($total > 0) {
        $mensagem = "❌ Não é possível excluir este livro pois ele possui empréstimos registrados.";
        $tipoMensagem = "danger";
    } else {
        $stmt = $pdo->prepare("DELETE FROM livros WHERE id = ?");
        $stmt->execute([$id]);
        $mensagem = "✅ Livro excluído com sucesso.";
    }
}

/* ==============================
   CADASTRAR OU EDITAR LIVRO
============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = $_POST['titulo'] ?? '';
    $autor  = $_POST['autor'] ?? '';
    $ano    = !empty($_POST['ano']) ? $_POST['ano'] : null;

    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE livros SET titulo=?, autor=?, ano=? WHERE id=?");
        $stmt->execute([$titulo, $autor, $ano, $id]);
        $mensagem = "✏️ Livro atualizado com sucesso.";
    } else {
        if ($titulo && $autor) {
            $quantidade = 1;
            $stmt = $pdo->prepare(
                "INSERT INTO livros (titulo, autor, ano, quantidade) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$titulo, $autor, $ano, $quantidade]);
            $mensagem = "📚 Livro cadastrado com sucesso.";
        }
    }
}

/* ==============================
   PAGINAÇÃO (ADICIONADO)
============================== */
$limite = 10;
$pagina = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$pagina = max($pagina, 1);
$offset = ($pagina - 1) * $limite;

$totalLivros = $pdo->query("SELECT COUNT(*) FROM livros")->fetchColumn();
$totalPaginas = ceil($totalLivros / $limite);

/* ==============================
   LISTAR LIVROS (COM PAGINAÇÃO)
============================== */
$stmt = $pdo->prepare("SELECT * FROM livros ORDER BY titulo ASC LIMIT :limite OFFSET :offset");
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
   EDITAR LIVRO
============================== */
$edit_livro = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_livro = $stmt->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/layout/header.php';
?>

<h2 class="mb-4">📚 Gestão de Livros</h2>

<?php if ($mensagem): ?>
    <div class="alert alert-<?= $tipoMensagem ?> shadow-sm">
        <?= $mensagem ?>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white fw-semibold">
                <?= $edit_livro ? "✏️ Editar Livro" : "➕ Cadastrar Novo Livro" ?>
            </div>

            <div class="card-body">
                <form method="POST">

                    <?php if ($edit_livro): ?>
                        <input type="hidden" name="id" value="<?= $edit_livro['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text"
                               name="titulo"
                               class="form-control"
                               placeholder="Ex: Dom Casmurro"
                               value="<?= $edit_livro['titulo'] ?? '' ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autor</label>
                        <input type="text"
                               name="autor"
                               class="form-control"
                               placeholder="Ex: Machado de Assis"
                               value="<?= $edit_livro['autor'] ?? '' ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ano</label>
                        <input type="number"
                               name="ano"
                               class="form-control"
                               placeholder="Ex: 1899"
                               value="<?= $edit_livro['ano'] ?? '' ?>">
                    </div>

                    <button class="btn btn-success w-100 shadow-sm">
                             <?= $edit_livro ? "Salvar Alterações" : "Cadastrar Livro" ?>
                    </button>

                    <?php if ($edit_livro): ?>
                        <a href="livros.php" class="btn btn-outline-secondary w-100 mt-2">
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
            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center gap-2">
                <span>📋 Livros Cadastrados</span>

                <!-- 🔍 BUSCA -->
                <input type="text"
                       id="buscaLivro"
                       class="form-control form-control-sm w-50"
                       placeholder="Buscar por título, autor ou ano">
            </div>

            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelaLivros">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Ano</th>
                            <th>Estoque</th>
                            <th class="text-center" style="width:180px">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($livros) === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Nenhum livro cadastrado
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($livros as $livro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($livro['titulo']) ?></td>
                                    <td><?= htmlspecialchars($livro['autor']) ?></td>
                                    <td><?= $livro['ano'] ?: '-' ?></td>
                                    <td>
                                        <?php if ($livro['quantidade'] > 0): ?>
                                            <span class="badge bg-success">
                                                <?= $livro['quantidade'] ?> disponível
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                Sem estoque
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?edit=<?= $livro['id'] ?>" class="btn btn-sm btn-warning">
                                            ✏️ Editar
                                        </a>
                                        <a href="?delete=<?= $livro['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                                            🗑️ Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINAÇÃO (ADICIONADO) -->
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
// 🔍 BUSCA POR TÍTULO, AUTOR E ANO
document.getElementById('buscaLivro').addEventListener('keyup', function () {
    const termo = this.value.toLowerCase();
    document.querySelectorAll('#tabelaLivros tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(termo) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
