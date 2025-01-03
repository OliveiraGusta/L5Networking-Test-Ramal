<?php

class Ramais{
    private $statusRamais = [];
    private $infoRamais = [];

    public function __construct() {
        $this->ramaisFile = file('ramais');
        $this->filasFile = file('filas');
    }

    private function processFilas() {
        foreach ($this->filasFile as $linha) {
            if (strstr($linha, 'SIP/')) {
                $linhaData = explode(' ', trim($linha));
                list(, $ramal) = explode('/', $linhaData[0]);
                
                $nomeDoMembro = trim(substr($linha, strrpos($linha, ' ') + 1));
                
                if (strstr($linha, '(Ring)')) {
                    $this->statusRamais[$ramal] = [
                        'status' => 'chamando',
                        'membro' => $nomeDoMembro
                    ];
                } elseif (strstr($linha, '(In use)')) {
                    $this->statusRamais[$ramal] = [
                        'status' => 'ocupado',
                        'membro' => $nomeDoMembro
                    ];
                } elseif (strstr($linha, '(Not in use)')) {
                    $this->statusRamais[$ramal] = [
                        'status' => 'disponivel',
                        'membro' => $nomeDoMembro
                    ];
                } else {
                    $this->statusRamais[$ramal] = [
                        'status' => 'offline',
                        'membro' => $nomeDoMembro
                    ];
                }
            }
        }
    }
    
    private function processRamais() {
        array_shift($this->ramaisFile);
        foreach ($this->ramaisFile as $linha) {
            $linhaData = array_filter(explode(' ', trim($linha)));
            $linhaArray = array_values($linhaData);
    
            if (!empty($linhaArray) && isset($linhaArray[0]) && strpos($linhaArray[0], '/') !== false) {
                list($name, $username) = explode('/', $linhaArray[0]);
                $ipHost = $linhaArray[1] ?? null; 
                $porta = $linhaArray[4] ?? null;
                if($porta == "UNKNOWN")
                    $porta = "0";
            
                 $this->infoRamais[] = [
                'nome' => $name,
                'ramal' => $username ?? null,
                'online' => isset($linhaArray[5]) && trim($linhaArray[5]) == "OK",
                'status' => $this->statusRamais[$username]['status'] ?? null,
                'membro' => $this->statusRamais[$username]['membro'] ?? null,
                'ipHost' => $ipHost,
                'porta' => $porta,
            ];
            } else {
                error_log("Linha inválida: " . print_r($linhaArray, true));
            }
        }
    }
    


    public function getRamaisInfo() {
        $this->processFilas();
        $this->processRamais();
        return json_encode($this->infoRamais, JSON_UNESCAPED_UNICODE);
    }
}


$ramaisInfos = new Ramais();
echo $ramaisInfos->getRamaisInfo();
