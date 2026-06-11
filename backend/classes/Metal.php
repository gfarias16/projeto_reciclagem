<?php

// Subclasse de MaterialReciclavel: representa materiais do tipo metal.
class Metal extends MaterialReciclavel
{
    // Atributos especificos do metal.
    private $tipoMetal;
    private $purezaPercentual;

    // Construtor: usa os dados comuns da classe pai e adiciona dados do metal.
    public function __construct($descricao, $peso, $valorQuilo, $tipoMetal, $purezaPercentual)
    {
        parent::__construct($descricao, $peso, $valorQuilo);
        $this->setTipoMetal($tipoMetal);
        $this->setPurezaPercentual($purezaPercentual);
    }

    // Polimorfismo: o metal calcula o valor de acordo com sua pureza.
    public function calcularValorVenda()
    {
        return parent::calcularValorVenda() * ($this->purezaPercentual / 100);
    }

    // Getters dos atributos especificos.
    public function getTipoMetal()
    {
        return $this->tipoMetal;
    }

    public function getPurezaPercentual()
    {
        return $this->purezaPercentual;
    }

    // Setters: guardam e limitam os valores antes de salvar.
    public function setTipoMetal($tipoMetal)
    {
        $this->tipoMetal = $tipoMetal;
    }

    public function setPurezaPercentual($purezaPercentual)
    {
        $this->purezaPercentual = max(0, min(100, $purezaPercentual));
    }
}


