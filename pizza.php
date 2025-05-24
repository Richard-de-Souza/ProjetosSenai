<?php include 'template_start.php'; ?>

<!-- Botão e título -->

<!-- Tabela de pizzas -->
<div class="card p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Sabores de Pizza</h2>
        <a href="pizza_form.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nova Pizza
        </a>
    </div>
    <table id="tabelaPizzas" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Preenchido via AJAX -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    $('#tabelaPizzas').DataTable({
        ajax: {
            url: 'controller.php',
            type: 'POST',
            data: { funcao: 'listarPizzas' },
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nome' },
            { data: 'descricao' },
            { data: 'valor', render: function(data) {
                return 'R$ ' + parseFloat(data).toFixed(2);
            }},
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <a href="pizza_edit.php?id=${row.id}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                        <button class="btn btn-sm btn-danger" onclick="deletarPizza(${row.id})"><i class="bi bi-trash"></i></button>
                    `;
                }
            }
        ],
        language: {
            "decimal": ",",
            "emptyTable": "Nenhum dado disponível na tabela",
            "info": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 até 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros no total)",
            "infoPostFix": "",
            "thousands": ".",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Carregando...",
            "processing": "Processando...",
            "search": "Pesquisar:",
            "zeroRecords": "Nenhum registro correspondente encontrado",
            "paginate": {
                "first": "Primeiro",
                "last": "Último",
                "next": "Próximo",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": ativar para ordenar a coluna em ordem crescente",
                "sortDescending": ": ativar para ordenar a coluna em ordem decrescente"
            }
        }
    });
});

// Função para deletar pizza (futura implementação)
function deletarPizza(id) {
    swal.fire({
        title: "Atenção",
        text: "Deseja realmente excluir este sabor?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, excluir",
        cancelButtonText: "Cancelar"
    })
    .then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'controller.php',
                method: 'POST',
                data: { id: id, funcao: 'deletarPizza' },
                success: function(resposta) {
                    try {
                        const res = JSON.parse(resposta);
                        if (res.status === 'success') {
                            swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: res.mensagem
                                }).then(() => { window.location.reload();
                            });
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: res.mensagem || 'Erro ao deletar pizza.'
                            });
                        }
                    } catch (e) {
                        swal.fire({
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
