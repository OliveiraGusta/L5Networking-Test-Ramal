<?php
include '../db_connection.php';

$database = new Database();
$connection = $database->connect();

$data = json_decode(file_get_contents('php://input'), true);

foreach ($data as $ramal) {
    $nome = $connection->real_escape_string($ramal['nome']);
    $ramalNumero = $connection->real_escape_string($ramal['ramal']);
    $status = $connection->real_escape_string($ramal['status']);
    $membro = $connection->real_escape_string($ramal['membro']);
    $ipHost = $connection->real_escape_string($ramal['ipHost']);
    $porta = $connection->real_escape_string($ramal['porta']);
    
    $sql = "INSERT INTO ramais (nome, ramal, status, membro, ipHost, porta) 
            VALUES ('$nome', '$ramalNumero', '$status', '$membro', '$ipHost', '$porta')
            ON DUPLICATE KEY UPDATE
            status = '$status', membro = '$membro', ipHost = '$ipHost', porta = '$porta'";

    if ($connection->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $connection->error;
    }
}

$connection->close();
?>
