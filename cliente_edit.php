<?php
include 'template_start.php';
?>

<div class="card p-4">
    <h2 class="mb-4">Editar Cliente</h2>

    <form id="formEditarCliente">
        <input type="hidden" name="id" id="id">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" id="nome" required>
        </div>

        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" name="cpf" id="cpf" required>
        </div>

        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" name="telefone" id="telefone" required>
        </div>

        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" class="form-control" name="endereco" id="endereco" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="clientes.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (!id) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'ID do cliente não foi fornecido na URL.'
        });
        return;
    }

    // Buscar dados do cliente
    $.ajax({
        url: 'controller.php',
        method: 'GET',
        data: { funcao: 'detalhesCliente', id: id },
        success: function(resposta) {
            try {
                const res = JSON.parse(resposta);
                if (res.status === 'success') {
                    const cliente = res.cliente;
                    $('#id').val(cliente.id);
                    $('#nome').val(cliente.nome);
                    $('#cpf').val(cliente.cpf);
                    $('#telefone').val(cliente.telefone);
                    $('#endereco').val(cliente.endereco);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: res.mensagem || 'Erro ao buscar os dados do cliente.'
                    });
                }
            } catch (e) {
                console.error('Resposta inválida:', resposta);
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

    // Submeter alterações
    $('#formEditarCliente').on('submit', function(e) {
        e.preventDefault();

        const dados = $(this).serialize() + '&funcao=editarCliente';

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
                            title: 'Cliente atualizado com sucesso!',
                            text: res.mensagem
                        }).then(() => {
                            if (res.sql) {
                                Swal.fire({
                                    title: 'SQL Executado',
                                    html: `<pre style="text-align:left; font-size:0.9em; height: 75px; overflow-y: auto; white-space: pre-wrap; word-break: break-word;">${res.sql}</pre>`,
                                    icon: 'info',
                                    width: '600px'
                                }).then(() => {
                                    window.location.href = 'clientes.php';
                                });
                            } else {
                                window.location.href = 'clientes.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: res.mensagem || 'Erro ao atualizar cliente.'
                        });
                    }

                } catch (e) {
                    console.error('Resposta recebida com erro:', resposta);
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
});
</script>

<?php include 'template_end.php'; ?>
