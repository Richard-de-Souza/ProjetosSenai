<?php include 'template_start.php'; ?>


    <div class="card p-4">
        <div class="d-flex align-items-center mb-4">
            <h2 class="m-0">Cadastro de Pizzas</h2>
        </div>
        <form id="pizzaForm">
            <input type="hidden" name="acao" value="cadastrar_pizza">

            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Sabor</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label for="tamanho" class="form-label">Tamanho</label>
                <select class="form-select" id="tamanho" name="tamanho" required>
                    <option value="">Selecione o tamanho</option>
                    <option value="1">Pequena</option>
                    <option value="2">Média</option>
                    <option value="3">Grande</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="valor" class="form-label">Valor (R$)</label>
                <input type="number" step="0.01" class="form-control" id="valor" name="valor" required>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Cadastrar Pizza
            </button>
        </form>
    </div>


<script>
    $('#pizzaForm').on('submit', function(e) {
        e.preventDefault();

        const dados = $('#pizzaForm').serialize() + '&funcao=adicionarPizza';

        $.ajax({
            url: 'controller.php',
            method: 'POST',
            data: dados,
            success: function(resposta) {
                try {
                    const res = JSON.parse(resposta);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: res.mensagem
                        }).then(() => {
                            // Exibe outro pop-up com o SQL, se existir
                            if(res.sql) {
                                Swal.fire({
                                    title: 'SQL Executado',
                                    html: `<pre style="text-align:left; font-size:0.9em; height: 75px; overflow-y: auto; white-space: pre-wrap; word-break: break-word;">${res.sql}</pre>`,
                                    icon: 'info',
                                    width: '600px'
                                });
                            }
                        });
                        $('#pizzaForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: res.mensagem || 'Erro ao cadastrar pizza.'
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro inesperado',
                        text: 'A resposta do servidor não pôde ser interpretada.'
                    });
                }
            },

            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de requisição',
                    text: 'Não foi possível comunicar com o servidor.'
                });
            }
        });

    });
</script>

<?php include 'template_end.php'; ?>
