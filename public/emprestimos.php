<?php include 'layout/header.php'; ?>

<h2>🔄 Empréstimos</h2>

<div class="row mt-4">

    <!-- FORMULÁRIO -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                Registrar Empréstimo
            </div>
            <div class="card-body">

                <form>
                    <div class="mb-3">
                        <label class="form-label">Livro</label>
                        <select class="form-select">
                            <option>Dom Casmurro</option>
                            <option>O Pequeno Príncipe</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Leitor</label>
                        <select class="form-select">
                            <option>João da Silva</option>
                            <option>Maria Oliveira</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data do Empréstimo</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data de Devolução</label>
                        <input type="date" class="form-control">
                    </div>

                    <button class="btn btn-dark w-100">
                        Registrar Empréstimo
                    </button>
                </form>

            </div>
        </div>
    </div>

    <!-- LISTAGEM -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white">
                Empréstimos Ativos
            </div>
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Leitor</th>
                            <th>Empréstimo</th>
                            <th>Devolução</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>Dom Casmurro</td>
                            <td>João da Silva</td>
                            <td>01/10/2025</td>
                            <td>08/10/2025</td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    Em aberto
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success">
                                    Devolver
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td>O Pequeno Príncipe</td>
                            <td>Maria Oliveira</td>
                            <td>28/09/2025</td>
                            <td>05/10/2025</td>
                            <td>
                                <span class="badge bg-success">
                                    Devolvido
                                </span>
                            </td>
                            <td>
                                —
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<?php include 'layout/footer.php'; ?>
