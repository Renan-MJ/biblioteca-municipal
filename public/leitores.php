<?php
require_once __DIR__ . '/../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =========================
// EXCLUIR LEITOR
// =========================
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id > 0) {

        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM emprestimos 
            WHERE leitor_id = ? 
              AND devolvido = 0
        ");
        $stmt->execute([$id]);

        if ($stmt->fetchColumn() > 0) {
            header("Location: leitores.php?erro=emprestimo_ativo");
            exit;
        }

        $pdo->prepare("
            DELETE FROM emprestimos 
            WHERE leitor_id = ? 
              AND devolvido = 1
        ")->execute([$id]);

        $pdo->prepare("DELETE FROM leitores WHERE id = ?")->execute([$id]);
    }

    header("Location: leitores.php?sucesso=excluido");
    exit;
}

// =========================
// CADASTRAR / EDITAR
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    // 👉 VALIDAÇÃO OBRIGATÓRIA
    if ($nome === '' || $cpf === '' || $telefone === '') {
        header("Location: leitores.php?erro=campos_obrigatorios");
        exit;
    }

    if (!empty($_POST['id'])) {
        $pdo->prepare(
            "UPDATE leitores SET nome=?, cpf=?, telefone=? WHERE id=?"
        )->execute([$nome, $cpf, $telefone, $_POST['id']]);
    } else {
        $pdo->prepare(
            "INSERT INTO leitores (nome, cpf, telefone) VALUES (?, ?, ?)"
        )->execute([$nome, $cpf, $telefone]);
    }

    header("Location: leitores.php");
    exit;
}

// =========================
// LISTAGEM
// =========================
$leitores = $pdo->query("SELECT * FROM leitores ORDER BY id DESC")
                ->fetchAll(PDO::FETCH_ASSOC);

// =========================
// EDITAR
// =========================
$edit_leitor = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM leitores WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_leitor = $stmt->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/layout/header.php';
?>

<h2 class="mb-4">👤 Gestão de Leitores</h2>

<?php if (isset($_GET['erro']) && $_GET['erro'] === 'emprestimo_ativo'): ?>
    <div class="alert alert-danger shadow-sm">
        Não é possível excluir este leitor pois ele possui empréstimo ativo.
    </div>
<?php endif; ?>

<?php if (isset($_GET['erro']) && $_GET['erro'] === 'campos_obrigatorios'): ?>
    <div class="alert alert-warning shadow-sm">
        Preencha todos os campos antes de cadastrar o leitor.
    </div>
<?php endif; ?>

<?php if (isset($_GET['sucesso'])): ?>
    <div class="alert alert-success shadow-sm">
        Leitor excluído com sucesso.
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white fw-semibold">
                <?= $edit_leitor ? "Editar Leitor" : "Cadastrar Leitor" ?>
            </div>

            <div class="card-body">
                <form method="POST">

                    <?php if ($edit_leitor): ?>
                        <input type="hidden" name="id" value="<?= $edit_leitor['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= $edit_leitor['nome'] ?? '' ?>"
                               placeholder="Ex: Maria da Silva" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text"
                               name="cpf"
                               id="cpf"
                               class="form-control"
                               maxlength="14"
                               value="<?= $edit_leitor['cpf'] ?? '' ?>"
                               placeholder="000.000.000-00"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control"
                               value="<?= $edit_leitor['telefone'] ?? '' ?>"
                               placeholder="(00) 00000-0000"
                               required>
                    </div>

                    <button class="btn btn-success w-100">
                        <?= $edit_leitor ? "Salvar alterações" : "Cadastrar leitor" ?>
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
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Leitores cadastrados</span>
                <input type="text" id="buscaLeitor"
                       class="form-control form-control-sm w-50"
                       placeholder="Buscar por nome, CPF ou telefone">
            </div>

            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0" id="tabelaLeitores">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (count($leitores) === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Nenhum leitor cadastrado
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($leitores as $leitor): ?>
                            <tr>
                                <td><?= htmlspecialchars($leitor['nome']) ?></td>
                                <td><?= htmlspecialchars($leitor['cpf']) ?></td>
                                <td><?= htmlspecialchars($leitor['telefone']) ?></td>
                                <td class="text-center">
                                    <a href="?edit=<?= $leitor['id'] ?>" class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                    <a href="?delete=<?= $leitor['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Confirmar exclusão do leitor?')">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
// BUSCA
document.getElementById('buscaLeitor').addEventListener('keyup', function () {
    const termo = this.value.toLowerCase();
    document.querySelectorAll('#tabelaLeitores tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(termo) ? '' : 'none';
    });
});

// 👉 MÁSCARA CPF
document.getElementById('cpf').addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '');
    v = v.slice(0, 11);

    if (v.length >= 9) {
        this.value = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
    } else if (v.length >= 6) {
        this.value = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
    } else if (v.length >= 3) {
        this.value = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
    } else {
        this.value = v;
    }
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
