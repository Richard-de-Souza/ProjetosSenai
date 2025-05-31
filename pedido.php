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
            <div id="lista-pizzas" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                <!-- Pizzas serão carregadas aqui -->
            </div>
            <div class="form-text">Escolha as pizzas, tamanhos e quantidades</div>
        </div>

        <div class="fw-bold fs-5 mt-3 text-end" id="valor-total">Total: R$ 0,00</div>

        <button type="submit" class="btn btn-success mt-3">
            <i class="bi bi-check-circle"></i> Cadastrar Pedido
        </button>
    </form>
</div>

<script>
$(document).ready(function () {
    const multiplicadores = {
        "Pequena": 1,
        "Média": 1.5,
        "Grande": 2
    };

    function formatarPreco(valor) {
        return 'R$ ' + valor.toFixed(2).replace('.', ',');
    }

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
                const preco = parseFloat(p.valor);
                $('#lista-pizzas').append(`
                    <div class="row align-items-center mb-2 pizza-item" data-preco-base="${preco}" data-id="${p.id}">
                        <div class="col-auto">
                            <input type="checkbox" class="form-check-input pizza-checkbox" id="pizza-${p.id}" value="${p.id}">
                        </div>
                        <div class="col">
                            <label for="pizza-${p.id}" class="fw-bold">${p.nome}</label>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm pizza-tamanho" disabled>
                                <option value="Pequena">Pequena</option>
                                <option value="Média">Média</option>
                                <option value="Grande">Grande</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" min="1" class="form-control form-control-sm pizza-quantidade" placeholder="Qtd" disabled>
                        </div>
                        <div class="col-md-2 text-end">
                            <span class="pizza-preco text-muted">${formatarPreco(preco)}</span>
                        </div>
                    </div>
                `);
            });

            // Checkbox habilita/desabilita campos
            $('.pizza-checkbox').on('change', function () {
                const linha = $(this).closest('.pizza-item');
                const checked = $(this).is(':checked');
                linha.find('.pizza-tamanho, .pizza-quantidade').prop('disabled', !checked);
                if (!checked) {
                    linha.find('.pizza-quantidade').val('');
                } else {
                    linha.find('.pizza-quantidade').val(1);
                }
                atualizarPrecoLinha(linha);
                atualizarTotal();
            });

            // Atualizar preço linha e total ao mudar tamanho ou quantidade
            $('#lista-pizzas').on('change input', '.pizza-tamanho, .pizza-quantidade', function () {
                const linha = $(this).closest('.pizza-item');
                atualizarPrecoLinha(linha);
                atualizarTotal();
            });
        } catch (e) {
            console.error('Erro ao carregar pizzas');
        }
    });

    function atualizarPrecoLinha(linha) {
        const precoBase = parseFloat(linha.data('preco-base'));
        const tamanho = linha.find('.pizza-tamanho').val();
        const quantidade = parseInt(linha.find('.pizza-quantidade').val()) || 0;
        const mult = multiplicadores[tamanho] || 1;

        const totalLinha = precoBase * mult * quantidade;
        linha.find('.pizza-preco').text(formatarPreco(totalLinha));
    }

    function atualizarTotal() {
        let total = 0;

        $('.pizza-item').each(function () {
            if ($(this).find('.pizza-checkbox').is(':checked')) {
                const precoBase = parseFloat($(this).data('preco-base'));
                const tamanho = $(this).find('.pizza-tamanho').val();
                const quantidade = parseInt($(this).find('.pizza-quantidade').val()) || 0;
                const mult = multiplicadores[tamanho] || 1;
                total += precoBase * mult * quantidade;
            }
        });

        $('#valor-total').text('Total: ' + formatarPreco(total));
    }

    // Enviar pedido
    $('#pedidoForm').submit(function(e) {
        e.preventDefault();

        let pizzasSelecionadas = [];

        $('.pizza-item').each(function () {
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
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Selecione ao menos uma pizza com quantidade válida.'
            });
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
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido cadastrado com sucesso',
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = 'index.php';
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível enviar o pedido.'
                });
            }
        });
    });
});
</script>

<?php include 'template_end.php'; ?>
