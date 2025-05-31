<?php
include 'template_start.php';
include 'banco.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID do cliente não informado.</div>";
    exit;
}

$id = $_GET['id'];
$banco = new Banco();
$conn = $banco->getConnection();

$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$pizza = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pizza) {
    echo "<div class='alert alert-danger'>Cliente não encontrado.</div>";
    exit;
}
?>

<div class="card p-4">
    <h2 class="mb-4">Editar Cliente</h2>

    <form id="formEditarPizza">
        <input type="hidden" name="id" value="<?= $pizza['id'] ?>">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($pizza['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" rows="3" required><?= htmlspecialchars($pizza['descricao']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label">Valor (R$)</label>
            <input type="number" class="form-control" name="valor" step="0.01" value="<?= number_format($pizza['valor'], 2, '.', '') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="pizza.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
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
                console.log('Resposta recebida:', resposta); // útil para debug
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
