<?php

// Classe base: representa qualquer material reciclavel recebido pela cooperativa.
class MaterialReciclavel
{
    // Atributos comuns para todos os tipos de material.
    private $descricao;
    private $peso;
    private $valorQuilo;

    // Construtor: recebe os dados principais e chama os setters.
    public function __construct($descricao, $peso, $valorQuilo)
    {
        $this->setDescricao($descricao);
        $this->setPeso($peso);
        $this->setValorQuilo($valorQuilo);
    }

    // Metodo base de calculo: pode ser reaproveitado ou sobrescrito pelas subclasses.
    public function calcularValorVenda()
    {
        return $this->peso * $this->valorQuilo;
    }

    // Getters: permitem acessar os atributos privados com seguranca.
    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function getValorQuilo()
    {
        return $this->valorQuilo;
    }

    // Setters: centralizam a atribuicao dos valores nos atributos.
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function setPeso($peso)
    {
        $this->peso = $peso;
    }

    public function setValorQuilo($valorQuilo)
    {
        $this->valorQuilo = $valorQuilo;
    }
}

