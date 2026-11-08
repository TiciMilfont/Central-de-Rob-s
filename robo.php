
<?php

class Robo {

    public $nome;
    public $modelo;
    public $energia;
    public $potencia;

    // Construtor
    public function __construct($nome, $modelo, $energia, $potencia) {

        $this->nome = $nome;
        $this->modelo = $modelo;
        $this->energia = $energia;
        $this->potencia = $potencia;
    }

    // Exibir informações do robô
    public function exibirStatus() {

        return [
            "nome" => $this->nome,
            "modelo" => $this->modelo,
            "energia" => $this->energia,
            "potencia" => $this->potencia
        ];
    }

    // Recarregar energia
    public function recarregar() {

        $this->energia += 10;

        if ($this->energia > 200) {
            $this->energia = 200;
        }
    }
}

