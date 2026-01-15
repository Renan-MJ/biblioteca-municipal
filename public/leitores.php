<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS (desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =========================
// EXCLUIR LEITOR (REGRA FINAL)
// =========================
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id > 0) {

        // Verificar empréstimos ATIVOS
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM emprestimos 
            WHERE leitor_id = ? 
              AND devolvido = 0
        ");
        $stmt->execute([$id]);
        $ativos = $stmt->fetchColumn();

        if ($ativos > 0) {
            header("Location: leitores.php?erro=emprestimo_ativo");
            exit;
        }

        // Apagar empréstimos DEVOLVIDOS
        $stmt = $pdo->prepare("
            DELETE FROM emprestimos 
            WHERE leitor_id = ? 
              AND devolvido = 1
        ");
        $stmt->execute([$id]);

        // Excluir leitor
        $stmt = $pdo->prepare("DELETE FROM leitores WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: leitores.php?sucesso=excluido");
    exit;
}

// =========================
// CADASTRAR / EDITAR LEITOR
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nome'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare(
            "UPDATE leitores SET nome=?, cpf=?, telefone=? WHERE id=?"
        );
        $stmt->execute([$nome, $cpf, $telefone, $_POST['id']]);
    } else {
        if ($nome) {
            $stmt = $pdo->prepare(
                "INSERT INTO leitores (nome, cpf, telefone) VALUES (?, ?, ?)"
            );
            $stmt->execute([$nome, $cpf, $telefone]);
        }
    }

    header("Location: leitores.php");
    exit;
}

// =========================
// LISTAR LEITORES
// =========================
$stmt = $pdo->query("SELECT * FROM leitores ORDER BY id DESC");
$leitores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =========================
// EDITAR LEITOR
// =========================
$edit_leitor = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM leitores WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_leitor = $stmt->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/layout/header.php';
?>

<h2 class="mb-4">👤 Gestão de Leitores</h2>

<?php if (isset($_GET['erro']) && $_GET['erro'] === 'emprestimo_ativo'): ?>
    <div class="alert alert-danger shadow-sm">
        ❌ Não é possível excluir este leitor pois ele possui empréstimo ativo.
    </div>
<?php endif; ?>

<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'excluido'): ?>
    <div class="alert alert-success shadow-sm">
        ✅ Leitor excluído com sucesso.
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white fw-semibold">
                <?= $edit_leitor ? "✏️ Editar Leitor" : "➕ Cadastrar Novo Leitor" ?>
            </div>

            <div class="card-body">
                <form method="POST">

                    <?php if ($edit_leitor): ?>
                        <input type="hidden" name="id" value="<?= $edit_leitor['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text"
                               name="nome"
                               class="form-control"
                               placeholder="Ex: Maria da Silva"
                               value="<?= $edit_leitor['nome'] ?? '' ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text"
                               name="cpf"
                               class="form-control"
                               placeholder="000.000.000-00"
                               value="<?= $edit_leitor['cpf'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text"
                               name="telefone"
                               class="form-control"
                               placeholder="(00) 00000-0000"
                               value="<?= $edit_leitor['telefone'] ?? '' ?>">
                    </div>

                    <button class="btn btn-success w-100 shadow-sm">
                        💾 <?= $edit_leitor ? "Salvar Alterações" : "Cadastrar Leitor" ?>
                    </button>

                    <?php if ($edit_leitor): ?>
                        <a href="leitores.php" class="btn btn-outline-secondary w-100 mt-2">
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
            <div class="card-header bg-dark text-white fw-semibold">
                📋 Leitores Cadastrados
            </div>

            <div class="card-body p-0">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th class="text-center" style="width:180px">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($leitores) === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Nenhum leitor cadastrado
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leitores as $leitor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($leitor['nome']) ?></td>
                                    <td><?= htmlspecialchars($leitor['cpf']) ?></td>
                                    <td><?= htmlspecialchars($leitor['telefone']) ?></td>
                                    <td class="text-center">
                                        <a href="?edit=<?= $leitor['id'] ?>" class="btn btn-sm btn-warning">
                                            ✏️ Editar
                                        </a>
                                        <a href="?delete=<?= $leitor['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja excluir este leitor?')">
                                            🗑️ Excluir
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
