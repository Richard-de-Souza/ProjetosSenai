<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(120deg, #3498db, #6dd5fa);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-card {
      background-color: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
    }
  </style>
</head>

<body>
  <div class="login-card">
    <h3 class="text-center mb-4">Cadastro de Usuário</h3>

    <form id="formCadastro">
      <div class="mb-3">
        <input type="text" name="nome" class="form-control" placeholder="Nome" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
    </form>

    <p class="mt-3 text-center" id="mensagem"></p>

    <p class="mt-3 text-center">
      <a href="login.php">Já tem conta? Faça login aqui.</a>
    </p>
  </div>

  <script>
    document.getElementById('formCadastro').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('controllerlogin.php?funcao=cadastrarUsuario', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        const msgEl = document.getElementById('mensagem');
        msgEl.textContent = data.mensagem;
        msgEl.className = data.status === 'sucesso' ? 'text-success' : 'text-danger';

        if (data.status === 'sucesso') {
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 1500);
        }
      })
      .catch(() => {
        document.getElementById('mensagem').textContent = 'Erro ao cadastrar.';
        document.getElementById('mensagem').className = 'text-danger';
      });
    });
  </script>
</body>
</html>
