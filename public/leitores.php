<?php
require_once __DIR__ . '/../config/database.php';

// MOSTRAR ERROS (desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CADASTRAR LEITOR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    if ($nome) {
        $sql = "INSERT INTO leitores (nome, cpf, telefone)
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $cpf, $telefone]);
    }

    header("Location: leitores.php");
    exit;
}

// LISTAR LEITORES
$stmt = $pdo->query("SELECT * FROM leitores ORDER BY id DESC");
$leitores = $stmt->fetchAll();

include __DIR__ . '/layout/header.php';
?>

<h2>👤 Leitores</h2>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                Cadastrar Leitor
            </div>
            <div class="card-body">

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                    </div>

                    <button class="btn btn-success w-100">
                        Salvar Leitor
                    </button>
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
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (count($leitores) === 0): ?>
                            <tr>
                                <td colspan="3">Nenhum leitor cadastrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leitores as $leitor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($leitor['nome']) ?></td>
                                    <td><?= htmlspecialchars($leitor['cpf']) ?></td>
                                    <td><?= htmlspecialchars($leitor['telefone']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>

            </div>
        </div
