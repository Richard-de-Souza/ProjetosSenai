<?php
include 'template_start.php';
?>

<div class="card p-4">
    <h2 class="mb-4">Editar Pizza</h2>

    <form id="formEditarPizza">
        <input type="hidden" name="id" id="pizzaId" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" id="nome" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" id="descricao" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label">Valor (R$)</label>
            <input type="number" class="form-control" name="valor" id="valor" step="0.01" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="pizza.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
$(document).ready(function () {
    const id = $('#pizzaId').val();

    // Verifica se o ID está presente
    if (!id) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'ID da pizza não fornecido na URL.'
        });
        return;
    }

    // Carrega os dados da pizza via AJAX
    $.ajax({
        url: 'controller.php',
        method: 'GET',
        data: { id: id, funcao: 'detalhesPizza' },
        success: function (resposta) {
            try {
                const res = JSON.parse(resposta);
                if (res.status === 'success') {
                    $('#nome').val(res.pizza.nome);
                    $('#descricao').val(res.pizza.descricao);
                    $('#valor').val(res.pizza.valor);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: res.mensagem
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'A resposta do servidor não pôde ser interpretada.'
                });
                console.error(resposta);
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Erro de requisição',
                text: 'Não foi possível comunicar com o servidor.'
            });
        }
    });

    // Envia o formulário para atualizar a pizza
    $('#formEditarPizza').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'controller.php',
            method: 'POST',
            data: $(this).serialize() + '&funcao=editarPizza',
            success: function(resposta) {
                try {
                    const res = JSON.parse(resposta);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: res.mensagem
                        }).then(() => {
                            if (res.sql) {
                                Swal.fire({
                                    title: 'SQL Executado',
                                    html: `<pre style="text-align:left; font-size:0.9em; height: 75px; overflow-y: auto; white-space: pre-wrap; word-break: break-word;">${res.sql}</pre>`,
                                    icon: 'info',
                                    width: '600px'
                                }).then(() => {
                                    window.location.href = 'pizza.php';
                                });
                            } else {
                                window.location.href = 'pizza.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: res.mensagem
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro inesperado',
                        text: 'A resposta do servidor não pôde ser interpretada.'
                    });
                    console.error('Resposta recebida:', resposta);
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
});
</script>

<?php include 'template_end.php'; ?>
