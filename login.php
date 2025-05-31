<?php
session_start();
include 'banco.php';

$erro = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $banco = new Banco();
    $conn = $banco->getConnection();

    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_logado'] = $usuario['nome'];
                header('Location: index.php');
                exit();
            } else {
                $erro = 'Senha incorreta.';
            }
        } else {
            $erro = 'Usuário não encontrado.';
        }
    } else {
        $erro = 'Erro ao conectar com o banco de dados.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
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
    <h2 class="text-center mb-4">Login</h2>

    <?php if ($erro): ?>
      <div class="alert alert-danger text-center py-2">
        <?= $erro ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <div class="mb-3">
        <input type="text" name="usuario" class="form-control" placeholder="E-mail" required>
      </div>
      <div class="mb-3">
        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>

    <p class="mt-3 text-center">
      <a href="cadastro.php">Não tem conta? Cadastre-se aqui.</a>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
