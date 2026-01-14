<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/layout/header.php';
?>

<?php include 'layout/header.php'; ?>

<h2>📚 Livros</h2>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Cadastrar Livro
            </div>
            <div class="card-body">

                <form>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autor</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ano</label>
                        <input type="number" class="form-control">
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
                            <th>Categoria</th>
                            <th>Ano</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>Dom Casmurro</td>
                            <td>Machado de Assis</td>
                            <td>Literatura</td>
                            <td>1899</td>
                            <td>
                                <span class="badge bg-success">Disponível</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning">Editar</button>
                                <button class="btn btn-sm btn-danger">Excluir</button>
                            </td>
                        </tr>

                        <tr>
                            <td>O Pequeno Príncipe</td>
                            <td>Antoine de Saint-Exupéry</td>
                            <td>Infantil</td>
                            <td>1943</td>
                            <td>
                                <span class="badge bg-danger">Emprestado</span>
                            </td>
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

