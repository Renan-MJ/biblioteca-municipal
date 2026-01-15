<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS (apenas desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CADASTRAR LIVRO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $editora = $_POST['editora'] ?? '';
    $ano = !empty($_POST['ano']) ? $_POST['ano'] : null;
    $quantidade = 1;

    if ($titulo && $autor) {
        $sql = "INSERT INTO livros (titulo, autor, editora, ano, quantidade)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $autor, $editora, $ano, $quantidade]);
    }

    header("Location: livros.php");
    exit;
}

// LISTAR LIVROS
$stmt = $pdo->query("SELECT * FROM livros ORDER BY id DESC");
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/layout/header.php';
?>


<h2>📚 Livros</h2>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Cadastrar Livro
            </div>
            <div class="card-body">

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autor</label>
                        <input type="text" name="autor" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Editora</label>
                        <input type="text" name="editora" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ano</label>
                        <input type="number" name="ano" class="form-control">
                    </div>

                    <button class="btn btn-success w-100">
                        Salvar Livro
                    </button>
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
                            <th>Editora</th>
                            <th>Ano</th>
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
                                    <td><?= htmlspecialchars($livro['editora']) ?></td>
                                    <td><?= $livro['ano'] ?></td>
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
