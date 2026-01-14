<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/layout/header.php';
?>

<?php include 'layout/header.php'; ?>

<h2>👤 Leitores</h2>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                Cadastrar Leitor
            </div>
            <div class="card-body">

                <form>
                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text" class="form-control" placeholder="000.000.000-00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control" placeholder="(00) 00000-0000">
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
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>João da Silva</td>
                            <td>123.456.789-00</td>
                            <td>(11) 99999-9999</td>
                            <td>
                                <button class="btn btn-sm btn-warning">Editar</button>
                                <button class="btn btn-sm btn-danger">Excluir</button>
                            </td>
                        </tr>

                        <tr>
                            <td>Maria Oliveira</td>
                            <td>987.654.321-00</td>
                            <td>(21) 98888-7777</td>
                            <td>
                                <button class="btn btn-sm btn-warning">Editar</button>
                                <button class="btn btn-sm btn-danger">Excluir</button>
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>

