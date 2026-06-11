<?php

// Subclasse de MaterialReciclavel: representa materiais do tipo papel.
class Papel extends MaterialReciclavel
{
    // Atributo especifico: qualidade do papel recebido.
    private $tipoPapel;

    // Construtor: reutiliza os dados comuns da classe pai e adiciona a qualidade.
    public function __construct($descricao, $peso, $valorQuilo, $tipoPapel)
    {
        parent::__construct($descricao, $peso, $valorQuilo);
        $this->setTipoPapel($tipoPapel);
    }

    // Polimorfismo: o papel calcula o valor aplicando um multiplicador por qualidade.
    public function calcularValorVenda()
    {
        return parent::calcularValorVenda() * $this->getMultiplicadorQualidade();
    }

    // Getter do atributo especifico.
    public function getTipoPapel()
    {
        return $this->tipoPapel;
    }

    // Define o multiplicador usado no calculo do valor de venda.
    public function getMultiplicadorQualidade()
    {
        return match ($this->tipoPapel) {
            'limpo' => 1.10,
            'umido_misturado' => 0.85,
            default => 1.00,
        };
    }

    // Texto amigavel para aparecer na pagina de resultado.
    public function getDescricaoQualidade()
    {
        return match ($this->tipoPapel) {
            'limpo' => 'Limpo',
            'umido_misturado' => 'Umido ou misturado',
            default => 'Comum',
        };
    }

    // Setter com validacao simples para evitar valores desconhecidos.
    public function setTipoPapel($tipoPapel)
    {
        $opcoesValidas = ['limpo', 'comum', 'umido_misturado'];
        $this->tipoPapel = in_array($tipoPapel, $opcoesValidas, true) ? $tipoPapel : 'comum';
    }
}

