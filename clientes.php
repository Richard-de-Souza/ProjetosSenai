<?php include 'template_start.php'; ?>

<div class="card p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Clientes</h2>
        <a href="cliente_form.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Novo Cliente
        </a>
    </div>
    <table id="tabelaClientes" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th>Endereço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Conteúdo via AJAX -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    $('#tabelaClientes').DataTable({
        ajax: {
            url: 'controller.php',
            type: 'POST',
            data: { funcao: 'listarClientes' },
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nome' },
            { data: 'cpf' },
            { data: 'telefone' },
            { data: 'endereco' },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <a href="cliente_edit.php?id=${row.id}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                        <button class="btn btn-sm btn-danger" onclick="deletarCliente(${row.id})"><i class="bi bi-trash"></i></button>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });
});

function deletarCliente(id) {
    Swal.fire({
        title: "Tem certeza?",
        text: "Deseja realmente excluir este cliente?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, excluir",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'controller.php',
                method: 'POST',
                data: { id: id, funcao: 'deletarCliente' },
                success: function(resposta) {
                    try {
                        const res = JSON.parse(resposta);
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: res.mensagem
                            });
                            $('#tabelaClientes').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: res.mensagem || 'Erro ao deletar cliente.'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro inesperado',
                            text: 'A resposta do servidor não pôde ser interpretada.'
                        });
                    }
                }
            });
        }
    });
}
</script>

<?php include 'template_end.php'; ?>
