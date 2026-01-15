<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS (apenas desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CADASTRAR OU EDITAR LIVRO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $ano = !empty($_POST['ano']) ? $_POST['ano'] : null;

    // Se existe id, é edição
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE livros SET titulo=?, autor=?, ano=? WHERE id=?");
        $stmt->execute([$titulo, $autor, $ano, $id]);
    } else {
        // Cadastro novo
        $quantidade = 1;
        if ($titulo && $autor) {
            $stmt = $pdo->prepare("INSERT INTO livros (titulo, autor, ano, quantidade) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $autor, $ano, $quantidade]);
        }
    }

    header("Location: livros.php");
    exit;
}

// LISTAR LIVROS
$stmt = $pdo->query("SELECT * FROM livros ORDER BY id DESC");
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// EDITAR LIVRO (preencher formulário)
$edit_livro = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM livros WHERE id=?");
    $stmt->execute([$id]);
    $edit_livro = $stmt->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/layout/header.php';
?>

<h2>📚 Livros</h2>

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
                            <th>Ações</th>
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
                                        <a href="?edit=<?= $livro['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
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
