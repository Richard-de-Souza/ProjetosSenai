<?php
class Banco {
    private $host = 'localhost';       // servidor do banco
    private $dbname = 'pizzaria';      // nome do banco de dados
    private $username = 'root';        // usuário do banco
    private $password = '';            // senha do banco
    private $conn;

    public function __construct() {
        $this->conectar();
    }

    private function conectar() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );
            // Configura o modo de erro do PDO para exceções
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Se der erro na conexão, mostra mensagem e termina execução
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    // Método para retornar a conexão
    public function getConnection() {
        return $this->conn;
    }
}
