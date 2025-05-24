<?php include 'template_start.php'; ?>

<div class="card p-4">
    <div class="d-flex align-items-center mb-4">
        <h2 class="m-0">Cadastro de Clientes</h2>
    </div>
    <form id="clienteForm">
        <input type="hidden" name="acao" value="cadastrar_cliente">

        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" id="cpf" name="cpf" required>
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>

        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" id="telefone" name="telefone" required>
        </div>

        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" class="form-control" id="endereco" name="endereco" required>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle"></i> Cadastrar Cliente
        </button>
    </form>
</div>

<script>
    // Aplica máscara
    $('#cpf').mask('000.000.000-00');
    $('#telefone').mask('(00) 00000-0000');

    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

        let soma = 0, resto;
        for (let i = 1; i <= 9; i++) soma += parseInt(cpf[i - 1]) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf[9])) return false;

        soma = 0;
        for (let i = 1; i <= 10; i++) soma += parseInt(cpf[i - 1]) * (12 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        return resto === parseInt(cpf[10]);
    }

    $('#clienteForm').on('submit', function(e) {
        e.preventDefault();

        const cpfValido = validarCPF($('#cpf').val());
        const cpfSemCaracteresEspeciais = $('#cpf').val().replace(/[^\d]+/g, '');
        const telefoneSemCaracteresEspeciais = $('#telefone').val().replace(/[^\d]+/g, '');

        const enviarDados = () => {
            var dados = $('#clienteForm').serialize() + '&funcao=adicionarCliente';
            dados += `&cpf=${cpfSemCaracteresEspeciais}`;
            dados += `&telefone=${telefoneSemCaracteresEspeciais}`;

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
                                Swal.fire({
                                    title: 'SQL Executado',
                                    html: `<pre style="text-align:left; font-size:0.9em; white-space:pre-wrap; word-wrap:break-word;">${res.sql}</pre>`,
                                    icon: 'info',
                                    width: '600px'
                                });
                            });
                            $('#clienteForm')[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: res.mensagem || 'Erro ao cadastrar cliente.'
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
        };

        if (!cpfValido) {
            Swal.fire({
                icon: 'warning',
                title: 'CPF inválido',
                text: 'Deseja continuar mesmo assim?',
                showCancelButton: true,
                confirmButtonText: 'Sim, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarDados();
                }
            });
        } else {
            enviarDados();
        }
    });

</script>

<?php include 'template_end.php'; ?>
