<?php


class Database {
    private $host = 'localhost'; 
    private $db_name = 'db_callcenter'; 
    private $username = 'root'; 
    private $password = 'root'; 
    private $connection;

    public function connect() {
        $this->connection = null;

        try {
           
        $this->connection =
                new mysqli(
                    $this->host,
                    $this->username,
                    $this->password,
                    $this->db_name
                );

            if ($this->connection->connect_error) {
                throw new Exception("Erro na conexão: " . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            die("Erro: " . $e->getMessage()); 
        }

        return $this->connection;
    }
}
?>
