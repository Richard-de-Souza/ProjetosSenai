<?php
include 'banco.php';

$banco = new Banco();
$conn = $banco->getConnection();

if (isset($_REQUEST['funcao'])) {
    $funcao = $_REQUEST['funcao'];

    if ($funcao == 'cadastrarUsuario') {
        cadastrarUsuario($conn);
    } elseif ($funcao == 'loginUsuario') {
        loginUsuario($conn);
    }
}

function cadastrarUsuario($conn) {
    $nome  = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$nome || !$email || !$senha) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Preencha todos os campos.']);
        return;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->execute();

        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Usuário cadastrado com sucesso.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao cadastrar: ' . $e->getMessage()]);
    }
}

// (Opcional) Função para login por AJAX
function loginUsuario($conn) {
    session_start();
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Preencha todos os campos.']);
        return;
    }

    try {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_logado'] = $usuario['nome'];
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Login realizado com sucesso.']);
        } else {
            echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail ou senha incorretos.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao acessar o banco: ' . $e->getMessage()]);
    }
}
