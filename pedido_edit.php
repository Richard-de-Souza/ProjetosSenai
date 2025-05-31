<?php include 'template_start.php'; ?>

<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID do pedido não informado ou inválido.</div>";
    include 'template_end.php';
    exit;
}
$pedidoId = intval($_GET['id']);
?>

<div class="card p-4">
    <div class="d-flex align-items-center mb-4">
        <h2 class="m-0">Editar Pedido #<?= $pedidoId ?></h2>
    </div>
    <form id="pedidoForm">
        <input type="hidden" name="acao" value="editar_pedido">
        <input type="hidden" name="id_pedido" value="<?= $pedidoId ?>">

        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente</label>
            <select class="form-select" id="cliente_id" name="cliente_id" required>
                <!-- Carregado via AJAX -->
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Pizzas</label>
            <div id="lista-pizzas" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                <!-- Carregado via AJAX -->
            </div>
            <div class="form-text">Marque as pizzas desejadas, selecione tamanho e defina a quantidade</div>
        </div>

        <div class="fw-bold fs-5 mt-3 text-end" id="valor-total">Total: R$ 0,00</div>

        <button type="submit" class="btn btn-primary mt-3">
            <i class="bi bi-save"></i> Salvar Alterações
        </button>
    </form>
</div>

<script>
$(document).ready(function () {
    const pedidoId = <?= $pedidoId ?>;
    const multiplicadores = {
        "Pequena": 1,
        "Média": 1.5,
        "Grande": 2
    };

    function formatarPreco(valor) {
        return 'R$ ' + valor.toFixed(2).replace('.', ',');
    }

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

    // Carregar pedido
    $.post('controller.php', { funcao: 'buscarPedido', id_pedido: pedidoId }, function(res) {
        try {
            const dados = JSON.parse(res);
            if (dados.status !== 'success') {
                alert(dados.mensagem || 'Erro ao carregar pedido.');
                return;
            }

            const pizzasSelecionadas = dados.pizzas;

            // Carregar clientes
            $.post('controller.php', { funcao: 'listarClientes' }, function(resClientes) {
                const clientes = JSON.parse(resClientes);
                clientes.forEach(c => {
                    const selected = (c.id == dados.pedido.cliente_id) ? 'selected' : '';
                    $('#cliente_id').append(`<option value="${c.id}" ${selected}>${c.nome}</option>`);
                });
            });

            // Carregar pizzas
            $.post('controller.php', { funcao: 'listarPizzas' }, function(resPizzas) {
                const pizzas = JSON.parse(resPizzas);
                pizzas.forEach(p => {
                    const selecionada = pizzasSelecionadas.find(pp => pp.id == p.id);
                    const checked = selecionada ? 'checked' : '';
                    const qtd = selecionada ? selecionada.quantidade : '';
                    const tamanho = selecionada ? selecionada.tamanho : '';
                    const preco = parseFloat(p.valor);
                    const precoLinha = selecionada ? preco * multiplicadores[tamanho] * qtd : preco;

                    $('#lista-pizzas').append(`
                        <div class="row align-items-center mb-2 pizza-item" data-id="${p.id}" data-preco-base="${preco}">
                            <div class="col-auto">
                                <input type="checkbox" class="form-check-input pizza-checkbox" id="pizza-${p.id}" value="${p.id}" ${checked}>
                            </div>
                            <div class="col">
                                <label for="pizza-${p.id}" class="fw-bold">${p.nome}</label>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm pizza-tamanho" ${checked ? '' : 'disabled'}>
                                    <option value="Pequena" ${tamanho === 'Pequena' ? 'selected' : ''}>Pequena</option>
                                    <option value="Média" ${tamanho === 'Média' ? 'selected' : ''}>Média</option>
                                    <option value="Grande" ${tamanho === 'Grande' ? 'selected' : ''}>Grande</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" min="1" class="form-control form-control-sm pizza-quantidade" value="${qtd}" ${checked ? '' : 'disabled'}>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="pizza-preco text-muted">${formatarPreco(precoLinha)}</span>
                            </div>
                        </div>
                    `);
                });

                // Eventos
                $('.pizza-checkbox').on('change', function () {
                    const linha = $(this).closest('.pizza-item');
                    const ativo = $(this).is(':checked');
                    linha.find('.pizza-tamanho, .pizza-quantidade').prop('disabled', !ativo);
                    if (!ativo) {
                        linha.find('.pizza-quantidade').val('');
                        linha.find('.pizza-tamanho').val('Pequena');
                    } else {
                        linha.find('.pizza-quantidade').val(1);
                    }
                    atualizarPrecoLinha(linha);
                    atualizarTotal();
                });

                $('#lista-pizzas').on('change input', '.pizza-tamanho, .pizza-quantidade', function () {
                    const linha = $(this).closest('.pizza-item');
                    atualizarPrecoLinha(linha);
                    atualizarTotal();
                });

                atualizarTotal();
            });
        } catch (e) {
            console.error(e);
            alert('Erro ao carregar pedido.');
        }
    });

    // Submissão
    $('#pedidoForm').submit(function(e) {
        e.preventDefault();

        let pizzasSelecionadas = [];

        $('.pizza-item').each(function () {
            if ($(this).find('.pizza-checkbox').is(':checked')) {
                const id = parseInt($(this).data('id'));
                const tamanho = $(this).find('.pizza-tamanho').val();
                const quantidade = parseInt($(this).find('.pizza-quantidade').val());

                if (tamanho && quantidade > 0) {
                    pizzasSelecionadas.push({ id, tamanho, quantidade });
                }
            }
        });

        if (pizzasSelecionadas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Selecione ao menos uma pizza com tamanho e quantidade válidos.'
            });
            return;
        }

        $.ajax({
            url: 'controller.php',
            method: 'POST',
            data: {
                funcao: 'editarPedido',
                id_pedido: pedidoId,
                cliente_id: $('#cliente_id').val(),
                pizzas: JSON.stringify(pizzasSelecionadas)
            },
            success: function(res) {
                try {
                    const data = JSON.parse(res);
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pedido atualizado com sucesso!',
                            text: data.mensagem || ''
                        }).then(() => {
                            if (data.sql_executados && data.sql_executados.length > 0) {
                                // Montar texto com todas as queries
                                let sqlTexto = '';
                                data.sql_executados.forEach((item, i) => {
                                    sqlTexto += `Query ${i + 1}:\n${item.query}\nParâmetros: ${JSON.stringify(item.params)}\n\n`;
                                });

                                Swal.fire({
                                    title: 'SQL Executado',
                                    html: `<pre style="text-align:left; font-size:0.9em; max-height: 200px; overflow-y: auto; white-space: pre-wrap; word-break: break-word;">${sqlTexto}</pre>`,
                                    icon: 'info',
                                    width: '600px'
                                }).then(() => {
                                    window.location.href = 'index.php';
                                });
                            } else {
                                window.location.href = 'index.php';
                            }
                        });
                    } else {
                        Swal.fire('Erro', data.mensagem || 'Erro ao atualizar.', 'error');
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('Erro', 'Erro inesperado na resposta.', 'error');
                }
            },

            error: function() {
                Swal.fire('Erro', 'Erro na requisição.', 'error');
            }
        });
    });
});
</script>

<?php include 'template_end.php'; ?>
