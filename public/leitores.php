<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS (desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CADASTRAR OU EDITAR LEITOR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    // Se existe id, é edição
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE leitores SET nome=?, cpf=?, telefone=? WHERE id=?");
        $stmt->execute([$nome, $cpf, $telefone, $id]);
    } else {
        // Cadastro novo
        if ($nome) {
            $stmt = $pdo->prepare("INSERT INTO leitores (nome, cpf, telefone) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $cpf, $telefone]);
        }
    }

    header("Location: leitores.php");
    exit;
}

// LISTAR LEITORES
$stmt = $pdo->query("SELECT * FROM leitores ORDER BY id DESC");
$leitores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// EDITAR LEITOR (preencher formulário)
$edit_leitor = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM leitores WHERE id=?");
    $stmt->execute([$id]);
    $edit_leitor = $stmt->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/layout/header.php';
?>

<h2>👤 Leitores</h2>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <?= $edit_leitor ? "Editar Leitor" : "Cadastrar Leitor" ?>
            </div>
            <div class="card-body">

                <form method="POST">
                    <?php if ($edit_leitor): ?>
                        <input type="hidden" name="id" value="<?= $edit_leitor['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="nome" class="form-control" required
                               value="<?= $edit_leitor['nome'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text" name="cpf" class="form-control"
                               placeholder="000.000.000-00"
                               value="<?= $edit_leitor['cpf'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control"
                               placeholder="(00) 00000-0000"
                               value="<?= $edit_leitor['telefone'] ?? '' ?>">
                    </div>

                    <button class="btn btn-success w-100">
                        <?= $edit_leitor ? "Salvar Alterações" : "Cadastrar Leitor" ?>
                    </button>

                    <?php if ($edit_leitor): ?>
                        <a href="leitores.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                    <?php endif; ?>
                </form>

            </div>
        </div>
    </div>

    <!-- LISTAGEM -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white">
                Leitores Cadastrados
            </div>
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($leitores) === 0): ?>
                            <tr>
                                <td colspan="4">Nenhum leitor cadastrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leitores as $leitor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($leitor['nome']) ?></td>
                                    <td><?= htmlspecialchars($leitor['cpf']) ?></td>
                                    <td><?= htmlspecialchars($leitor['telefone']) ?></td>
                                    <td>
                                        <a href="?edit=<?= $leitor['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
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
