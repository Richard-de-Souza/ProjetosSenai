<?php

include 'banco.php';

$banco = new Banco();
$conn = $banco->getConnection();

//faça uma verificação de qual a função solicitada

if (isset($_POST['funcao'])) {
    $funcao = $_POST['funcao'];
}

if ($funcao == 'listarPedidos') {
    listarPedidos($conn);
}
if ($funcao == 'detalhesPedido') {
    detalhesPedido($conn);
}
if ($funcao == 'adicionarPedido') {
    adicionarPedido($conn);
}
if ($funcao == 'deletarPedido') {
    deletarPedido($conn);
}


if ($funcao == 'listarClientes') {
    listarClientes($conn);
}
if ($funcao == 'adicionarCliente') {
    adicionarCliente($conn);
}
if ($funcao == 'deletarCliente') {
    deletarCliente($conn);
}



if ($funcao == 'listarPizzas') {
    listarPizzas($conn);
}
if ($funcao == 'adicionarPizza') {
    adicionarPizza($conn);
}
if ($funcao == 'deletarPizza') {
    deletarPizza($conn);
}
if($funcao == 'editarPizza') {
    editarPizza($conn);
}



function listarPedidos($conn) {
    $sql = "SELECT 
                p.id,
                c.nome AS cliente,
                p.data_hora AS data,
                p.quantidade,
                p.valor_total AS total
            FROM pedido p
            JOIN cliente c ON c.id = p.id_cliente
            ORDER BY p.data_hora DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pedidos);
}

function detalhesPedido($conn) {
    $id_pedido = $_POST['id_pedido'] ?? 0;
    $id_pedido = intval($id_pedido);
    if ($id_pedido <= 0) {
        echo json_encode(['status' => 'error', 'mensagem' => 'ID do pedido inválido']);
        return;
    }

    $sql = "SELECT sp.nome, ps.tamanho, sp.valor
            FROM pedido_sabores ps
            JOIN sabores_pizzas sp ON sp.id = ps.id_sabor
            WHERE ps.id_pedido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_pedido]);
    $sabores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Opcional: formatar valor para string moeda brasileira (R$ 00,00)
    foreach ($sabores as &$item) {
        $item['valor_formatado'] = 'R$ ' . number_format($item['valor'], 2, ',', '.');
    }

    echo json_encode(['status' => 'success', 'sabores' => $sabores]);
}


function adicionarPedido($conn) {
    $cliente_id = $_POST['cliente_id'] ?? null;
    $pizzas_raw = $_POST['pizzas'] ?? null;

    if (!$cliente_id || !$pizzas_raw) {
        echo json_encode([
            'status' => 'error',
            'mensagem' => 'Dados incompletos para o pedido.'
        ]);
        return;
    }

    // Decodifica o JSON caso seja string, ou usa array diretamente
    if (is_string($pizzas_raw)) {
        $pizzas = json_decode($pizzas_raw, true);
        if (!is_array($pizzas)) {
            echo json_encode([
                'status' => 'error',
                'mensagem' => 'Formato de pizzas inválido.'
            ]);
            return;
        }
    } else {
        $pizzas = $pizzas_raw;
    }

    $valor_total = 0;
    $quantidade_total = 0;

    foreach ($pizzas as $pizza) {
        $id_sabor = $pizza['id'] ?? null;
        $tamanho = $pizza['tamanho'] ?? 'Pequena'; // padrão pequena
        $quantidade = intval($pizza['quantidade'] ?? 0);

        if (!$id_sabor || $quantidade <= 0) {
            continue; // pula pizza inválida
        }

        // Busca valor base
        $stmt = $conn->prepare("SELECT valor FROM sabores_pizzas WHERE id = ?");
        $stmt->execute([$id_sabor]);
        $valor_base = $stmt->fetchColumn();

        if ($valor_base === false) {
            continue; // sabor inválido, pula
        }

        // Ajuste de valor conforme tamanho
        switch ($tamanho) {
            case 'Média':
                $valor_base *= 1.3;
                break;
            case 'Grande':
                $valor_base *= 1.6;
                break;
            case 'Pequena':
            default:
                // sem ajuste
                break;
        }

        $valor_total += $valor_base * $quantidade;
        $quantidade_total += $quantidade;
    }

    if ($quantidade_total === 0) {
        echo json_encode([
            'status' => 'error',
            'mensagem' => 'Nenhuma pizza válida para o pedido.'
        ]);
        return;
    }

    // Inserir pedido
    $sql = "INSERT INTO pedido (id_cliente, quantidade, valor_total) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $executou = $stmt->execute([$cliente_id, $quantidade_total, $valor_total]);

    if ($executou) {
        $pedido_id = $conn->lastInsertId();

        // Inserir sabores do pedido
        $sql_inserts = [];
        foreach ($pizzas as $pizza) {
            $id_sabor = $pizza['id'] ?? null;
            $tamanho = $pizza['tamanho'] ?? 'Pequena';
            $quantidade = intval($pizza['quantidade'] ?? 0);

            if (!$id_sabor || $quantidade <= 0) {
                continue;
            }

            for ($i = 0; $i < $quantidade; $i++) {
                $stmtPizza = $conn->prepare("INSERT INTO pedido_sabores (id_pedido, id_sabor, tamanho) VALUES (?, ?, ?)");
                $stmtPizza->execute([$pedido_id, $id_sabor, $tamanho]);
                $sql_inserts[] = "INSERT INTO pedido_sabores (id_pedido, id_sabor, tamanho) VALUES ($pedido_id, $id_sabor, '$tamanho')";
            }
        }

        echo json_encode([
            'status' => 'success',
            'mensagem' => 'Pedido cadastrado com sucesso!',
            'sql' => $sql . "\n" . implode(";\n", $sql_inserts) . ";"
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'mensagem' => 'Erro ao cadastrar o pedido.'
        ]);
    }
}
function deletarPedido($conn) {
    $id = $_POST['id_pedido'] ?? 0;
    $id = intval($id);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'mensagem' => 'ID inválido']);
        return;
    }

    try {
        $conn->beginTransaction();

        // Deletar os sabores relacionados antes
        $sqlSabores = "DELETE FROM pedido_sabores WHERE id_pedido = ?";
        $stmtSabores = $conn->prepare($sqlSabores);
        $stmtSabores->execute([$id]);

        // Agora deletar o pedido
        $sqlPedido = "DELETE FROM pedido WHERE id = ?";
        $stmtPedido = $conn->prepare($sqlPedido);
        $stmtPedido->execute([$id]);

        $conn->commit();

        if ($stmtPedido->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'mensagem' => 'Pedido deletado com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'mensagem' => 'Pedido não encontrado ou já deletado.']);
        }

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao deletar pedido: ' . $e->getMessage()]);
    }
}






function listarClientes($conn) {
    $sql = "SELECT * FROM cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clientes);
}

function adicionarCliente($conn) {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    $sql = "INSERT INTO cliente (nome, cpf, telefone, endereco) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $executou = $stmt->execute([$nome, $cpf, $telefone, $endereco]);

    if ($executou && $stmt->rowCount() > 0) {
        // Monta string SQL com valores "inseridos" para mostrar no popup
        $sqlComValores = "INSERT INTO cliente (nome, cpf, telefone, endereco) VALUES ('" 
            . addslashes($nome) . "', '" 
            . addslashes($cpf) . "', '" 
            . addslashes($telefone) . "', '" 
            . addslashes($endereco) . "')";
        echo json_encode(['status' => 'success', 'mensagem' => 'Cliente cadastrado com sucesso.', 'sql' => $sqlComValores]);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao cadastrar cliente.']);
    }
}

function deletarCliente($conn) {
    $id = $_POST['id'];
    $sql = "DELETE FROM cliente WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'mensagem' => 'Cliente deletado com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao deletar cliente.']);
    }
}



function listarPizzas($conn) {
    $sql = "SELECT * FROM sabores_pizzas";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $pizzas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($pizzas);
}

function adicionarPizza($conn) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];

    $sql = "INSERT INTO sabores_pizzas (nome, descricao, valor) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $executou = $stmt->execute([$nome, $descricao, $valor]);

    if ($executou && $stmt->rowCount() > 0) {
        // Monta string SQL com valores "inseridos" para mostrar no popup
        $sqlComValores = "INSERT INTO sabores_pizzas (nome, descricao, valor) VALUES ('" 
            . addslashes($nome) . "', '" 
            . addslashes($descricao) . "', " 
            . number_format($valor, 2, '.', '') . ")";

        echo json_encode([
            'status' => 'success',
            'mensagem' => 'Pizza cadastrada com sucesso!',
            'sql' => $sqlComValores
        ]);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao cadastrar pizza.']);
    }
}


function deletarPizza($conn) {
    $id = $_POST['id'];
    $sql = "DELETE FROM sabores_pizzas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'mensagem' => 'Pizza deletada com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao deletar pizza.']);
    }
}

function editarPizza() {
    if (!isset($_POST['id'], $_POST['nome'], $_POST['descricao'], $_POST['valor'])) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Dados incompletos.']);
        return;
    }

    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $valor = floatval(str_replace(',', '.', $_POST['valor'])); // trata "10,50" como 10.50

    try {
        $banco = new Banco();
        $conn = $banco->getConnection();

        $sql = "UPDATE sabores_pizzas SET nome = ?, descricao = ?, valor = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $sucesso = $stmt->execute([$nome, $descricao, $valor, $id]);

        if ($sucesso) {
            echo json_encode(['status' => 'success', 'mensagem' => 'Pizza atualizada com sucesso!',
            'sql' => "UPDATE sabores_pizzas SET nome = '$nome', descricao = '$descricao', valor = '$valor' WHERE id = $id" ]);
        } else {
            echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao atualizar a pizza.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
    }
}


?>


