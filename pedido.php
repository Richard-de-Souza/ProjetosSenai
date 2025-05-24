<?php include 'template_start.php'; ?>

<div class="card p-4">
    <div class="d-flex align-items-center mb-4">
        <h2 class="m-0">Cadastro de Pedidos</h2>
    </div>
    <form id="pedidoForm">
        <input type="hidden" name="acao" value="cadastrar_pedido">

        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente</label>
            <select class="form-select" id="cliente_id" name="cliente_id" required>
                <!-- Preenchido via AJAX -->
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Pizzas</label>
            <div id="lista-pizzas" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                <!-- Preenchido via AJAX -->
            </div>
            <div class="form-text">Marque as pizzas desejadas e defina a quantidade</div>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle"></i> Cadastrar Pedido
        </button>
    </form>
</div>

<script>
$(document).ready(function () {
    // Carregar clientes
    $.post('controller.php', { funcao: 'listarClientes' }, function(res) {
        try {
            const clientes = JSON.parse(res);
            clientes.forEach(c => {
                $('#cliente_id').append(`<option value="${c.id}">${c.nome}</option>`);
            });
        } catch (e) {
            console.error('Erro ao carregar clientes');
        }
    });

    // Carregar pizzas
    $.post('controller.php', { funcao: 'listarPizzas' }, function(res) {
        try {
            const pizzas = JSON.parse(res);
            pizzas.forEach(p => {
                $('#lista-pizzas').append(`
                    <div class="d-flex align-items-center mb-2">
                        <input type="checkbox" class="form-check-input me-2 pizza-checkbox" id="pizza-${p.id}" value="${p.id}" name="pizzas[]">
                        <label for="pizza-${p.id}" class="me-2">${p.nome} - R$ ${parseFloat(p.valor).toFixed(2)}</label>
                        <input type="number" min="1" class="form-control form-control-sm ms-auto pizza-quantidade" name="quantidade_${p.id}" placeholder="Qtd" style="width: 70px;" disabled>
                    </div>
                `);
            });

            $('.pizza-checkbox').on('change', function () {
                const inputQtd = $(this).closest('div').find('.pizza-quantidade');
                if (this.checked) {
                    inputQtd.prop('disabled', false).val(1);
                } else {
                    inputQtd.prop('disabled', true).val('');
                }
            });
        } catch (e) {
            console.error('Erro ao carregar pizzas');
        }
    });

    // Enviar pedido
    $('#pedidoForm').submit(function(e) {
    e.preventDefault();

    let pizzasSelecionadas = [];

    $('#lista-pizzas .d-flex').each(function() {
        const checkbox = $(this).find('.pizza-checkbox');
        if (checkbox.is(':checked')) {
            const id = parseInt(checkbox.val());
            const quantidade = parseInt($(this).find('.pizza-quantidade').val());
            const tamanho = $(this).find('.pizza-tamanho').val();

            if (quantidade > 0) {
                pizzasSelecionadas.push({ id, tamanho, quantidade });
            }
        }
    });

    if (pizzasSelecionadas.length === 0) {
        alert('Selecione ao menos uma pizza com quantidade.');
        return;
    }

    $.ajax({
        url: 'controller.php',
        method: 'POST',
        data: {
            cliente_id: $('#cliente_id').val(),
            pizzas: JSON.stringify(pizzasSelecionadas),
            funcao: 'adicionarPedido'
        },
        success: function(res) {
            // tratar resposta
            Swal.fire({
                icon: 'success',
                title: 'Pedido cadastrado com sucesso',
                showConfirmButton: true
            }).then(() => {
                window.location.href = 'index.php';
            });
        },
        error: function() {
            alert('Erro no envio');
        }
    });
});


});
</script>

<?php include 'template_end.php'; ?>
