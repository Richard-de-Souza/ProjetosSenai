<?php include 'template_start.php'; ?>

<style>
  #tabelaPedidos tbody tr {
    cursor: pointer;
  }
  #tabelaPedidos tbody tr:hover {
    background-color: #f1f1f1;
  }
  /* Botão de excluir não deixa a linha inteira clicável */
  .btn-excluir {
    cursor: pointer;
  }
</style>

<div class="card p-4">
    <h2 class="mb-4">Pedidos Recentes</h2>

    <table id="tabelaPedidos" class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Quantidade</th>
                <th>Total</th>
                <th>Ações</th> <!-- nova coluna -->
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal detalhes do pedido -->
<div class="modal fade" id="modalDetalhesPedido" tabindex="-1" aria-labelledby="modalDetalhesPedidoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalhesPedidoLabel">Detalhes do Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Sabor</th>
              <th>Tamanho</th>
              <th>Valor</th>
            </tr>
          </thead>
          <tbody id="detalhesPedidoCorpo">
            <!-- Dados via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
    // Função para formatar valor em moeda BR
    function formatarMoedaBR(valor) {
        return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    var tabela = $('#tabelaPedidos').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'controller.php',
            type: 'POST',
            data: { funcao: 'listarPedidos' },
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'cliente' },
            { 
              data: 'data',
              render: function(data) {
                var dt = new Date(data);
                return dt.toLocaleDateString('pt-BR') + ' ' + dt.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
              }
            },
            { data: 'quantidade' },
            { 
              data: 'total',
              render: function(data) {
                // Supondo que data seja numérico
                return formatarMoedaBR(parseFloat(data));
              }
            },
            {
              data: null,
              orderable: false,
              searchable: false,
              className: 'text-center',
              render: function(data, type, row) { 
                return `
                  <button class="btn btn-primary btn-sm btn-editar me-2" title="Editar pedido">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-danger btn-sm btn-excluir" title="Excluir pedido">
                    <i class="bi bi-trash"></i>
                  </button>`;
              }
            }
        ],
        language: {
            "decimal": ",",
            "emptyTable": "Nenhum dado disponível na tabela",
            "info": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 até 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros no total)",
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

    // Tooltip no hover para ajudar o usuário
    $('#tabelaPedidos tbody').on('mouseenter', 'tr', function () {
        $(this).attr('title', 'Clique para ver detalhes');
    });

    // Evento clique na linha da tabela para abrir modal (exceto quando clicar no botão excluir)
    $('#tabelaPedidos tbody').on('click', 'tr', function (e) {
        if ($(e.target).closest('.btn-excluir').length > 0) {
            return; // evita abrir modal ao clicar no botão excluir
        }

        var data = tabela.row(this).data();
        if (!data) return;

        var pedidoId = data.id;

        // Limpa conteúdo do modal
        $('#detalhesPedidoCorpo').html('');

        // Requisição AJAX para buscar detalhes do pedido
        $.ajax({
            url: 'controller.php',
            type: 'POST',
            data: { funcao: 'detalhesPedido', id_pedido: pedidoId },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    var html = '';
                    let total = 0;
                    response.sabores.forEach(function(item){
                        html += '<tr><td>' + item.nome + '</td><td>' + item.tamanho + '</td><td>' + item.valor_formatado + '</td></tr>';
                        total += parseFloat(item.valor);
                    });
                    html += `<tr><td colspan="2"><strong>Total</strong></td><td><strong>${formatarMoedaBR(total)}</strong></td></tr>`;
                    $('#detalhesPedidoCorpo').html(html);
                    var modal = new bootstrap.Modal(document.getElementById('modalDetalhesPedido'));
                    modal.show();
                } else {
                    Swal.fire('Erro', 'Erro ao carregar detalhes: ' + response.mensagem, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro', 'Erro na requisição', 'error');
            }
        });
    });

    // Evento clique no botão excluir
    $('#tabelaPedidos tbody').on('click', '.btn-excluir', function () {
        var data = tabela.row($(this).closest('tr')).data();
        if (!data) return;

        var pedidoId = data.id;

        Swal.fire({
            title: 'Confirma exclusão?',
            text: "Esta ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Chamada AJAX para excluir o pedido
                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: { funcao: 'deletarPedido', id_pedido: pedidoId },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            Swal.fire('Excluído!', 'Pedido excluído com sucesso.', 'success');
                            tabela.ajax.reload(null, false); // recarrega a tabela sem resetar página
                        } else {
                            Swal.fire('Erro', response.mensagem || 'Erro ao excluir pedido.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro', 'Erro na requisição.', 'error');
                    }
                });
            }
        });
    });

    // Editar pedido
    $('#tabelaPedidos tbody').on('click', '.btn-editar', function () {
        var data = tabela.row($(this).closest('tr')).data();
        if (!data) return;
        window.location.href = 'pedido_edit.php?id=' + data.id;
    });
});
</script>

<?php include 'template_end.php'; ?>
