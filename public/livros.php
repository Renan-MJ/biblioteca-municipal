<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS (apenas desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mensagem = null;
$tipoMensagem = 'success';

// ==============================
// EXCLUIR LIVRO
// ==============================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Verificar se existe empréstimo relacionado
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

// ==============================
// CADASTRAR OU EDITAR LIVRO
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $ano = !empty($_POST['ano']) ? $_POST['ano'] : null;

    if (!empty($_POST['id'])) {
        // EDIÇÃO
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE livros SET titulo=?, autor=?, ano=? WHERE id=?");
        $stmt->execute([$titulo, $autor, $ano, $id]);
        $mensagem = "✏️ Livro atualizado com sucesso.";
    } else {
        // CADASTRO
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

// ==============================
// LISTAR LIVROS
// ==============================
$stmt = $pdo->query("SELECT * FROM livros ORDER BY id DESC");
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// EDITAR LIVRO
// ==============================
$edit_livro = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_livro = $stmt->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/layout/header.php';
?>

<h2>📚 Livros</h2>

<?php if ($mensagem): ?>
    <div class="alert alert-<?= $tipoMensagem ?> mt-3">
        <?= $mensagem ?>
    </div>
<?php endif; ?>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <?= $edit_livro ? "Editar Livro" : "Cadastrar Livro" ?>
            </div>
            <div class="card-body">

                <form method="POST">
                    <?php if ($edit_livro): ?>
                        <input type="hidden" name="id" value="<?= $edit_livro['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required
                               value="<?= $edit_livro['titulo'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autor</label>
                        <input type="text" name="autor" class="form-control" required
                               value="<?= $edit_livro['autor'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ano</label>
                        <input type="number" name="ano" class="form-control"
                               value="<?= $edit_livro['ano'] ?? '' ?>">
                    </div>

                    <button class="btn btn-success w-100">
                        <?= $edit_livro ? "Salvar Alterações" : "Cadastrar Livro" ?>
                    </button>

                    <?php if ($edit_livro): ?>
                        <a href="livros.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                    <?php endif; ?>
                </form>

            </div>
        </div>
    </div>

    <!-- LISTAGEM -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white">
                Livros Cadastrados
            </div>
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Ano</th>
                            <th style="width:160px">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($livros) === 0): ?>
                            <tr>
                                <td colspan="4">Nenhum livro cadastrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($livros as $livro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($livro['titulo']) ?></td>
                                    <td><?= htmlspecialchars($livro['autor']) ?></td>
                                    <td><?= $livro['ano'] ?></td>
                                    <td>
                                        <a href="?edit=<?= $livro['id'] ?>" class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                        <a href="?delete=<?= $livro['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                                            Excluir
                                        </a>
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
