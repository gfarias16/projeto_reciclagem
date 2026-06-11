<?php

// Subclasse de MaterialReciclavel: representa materiais do tipo plastico.
class Plastico extends MaterialReciclavel
{
    // Atributo especifico: tipo do plastico recebido.
    private $tipoPlastico;

    // Construtor: usa os dados comuns da classe pai e adiciona o tipo de plastico.
    public function __construct($descricao, $peso, $valorQuilo, $tipoPlastico)
    {
        parent::__construct($descricao, $peso, $valorQuilo);
        $this->setTipoPlastico($tipoPlastico);
    }

    // Polimorfismo: o plastico calcula o valor aplicando multiplicador por tipo.
    public function calcularValorVenda()
    {
        return parent::calcularValorVenda() * $this->getMultiplicadorTipo();
    }

    // Getter do atributo especifico.
    public function getTipoPlastico()
    {
        return $this->tipoPlastico;
    }

    // Define o multiplicador usado no calculo do valor de venda.
    public function getMultiplicadorTipo()
    {
        return match ($this->tipoPlastico) {
            'pet' => 1.20,
            'pead' => 1.10,
            default => 1.00,
        };
    }

    // Texto amigavel para aparecer na pagina de resultado.
    public function getDescricaoTipo()
    {
        return match ($this->tipoPlastico) {
            'pet' => 'PET',
            'pead' => 'PEAD',
            default => 'Outros',
        };
    }

    // Setter com validacao simples para evitar valores desconhecidos.
    public function setTipoPlastico($tipoPlastico)
    {
        $opcoesValidas = ['pet', 'pead', 'outros'];
        $this->tipoPlastico = in_array($tipoPlastico, $opcoesValidas, true) ? $tipoPlastico : 'outros';
    }
}

