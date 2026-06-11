# Sistema de Cooperativa de Reciclagem

## 1. Objetivo do projeto

Este projeto foi desenvolvido para a disciplina de Programacao Orientada a Objetos. O tema escolhido foi o **Sistema de Cooperativas de Reciclagem**.

O sistema permite informar o nome da cooperativa, cadastrar um material reciclavel, informar seus dados principais e calcular o valor de venda usando PHP Orientado a Objetos.

O principal conceito demonstrado no projeto e o **polimorfismo**, pois cada tipo de material possui sua propria forma de calcular o valor de venda por meio do metodo:

```php
calcularValorVenda()
```

## 2. Como executar

1. Coloque a pasta do projeto dentro do diretorio do XAMPP:

```text
c:\xampp\htdocs\projeto_reciclagem
```

2. Inicie o Apache pelo painel do XAMPP.

3. Acesse no navegador:

```text
http://localhost/Projeto_reciclagem/frontend/formulario.html
```

Tambem e possivel executar com o servidor interno do PHP:

```bash
php -S 127.0.0.1:8088 -t c:\xampp\htdocs\Projeto_reciclagem
```

Depois acesse:

```text
http://127.0.0.1:8088/frontend/formulario.html
```

## 3. Arquivos principais

| Arquivo | Funcao |
| --- | --- |
| `frontend/formulario.html` | Tela inicial com o formulario de cadastro do material. |
| `backend/processa.php` | Recebe os dados do formulario, valida as informacoes, instancia os objetos e exibe o resultado. |
| `backend/classes/Materia_Reciclavel.php` | Classe base com atributos e calculo comum dos materiais. |
| `backend/classes/Papel.php` | Subclasse para materiais do tipo papel. |
| `backend/classes/Plastico.php` | Subclasse para materiais do tipo plastico. |
| `backend/classes/Metal.php` | Subclasse para materiais do tipo metal. |
| `frontend/style.css` | Estilizacao visual do formulario, resultado e animacoes. |

## 4. Funcionamento geral

O usuario acessa `formulario.html`, informa o nome da cooperativa e escolhe o tipo de material: papel, plastico ou metal.

Depois disso, o JavaScript mostra apenas os campos especificos daquele tipo de material. Ao enviar o formulario, os dados sao enviados para `processa.php` usando o metodo `POST`.

O nome da cooperativa foi mantido como um campo simples do formulario, sem criar nova classe, para nao fugir da proposta principal do trabalho.

No processa.php, o sistema:

1. importa as classes com `require_once`;
2. recebe os dados enviados pelo formulario;
3. valida os campos obrigatorios;
4. cria um objeto da classe correta;
5. chama o metodo `calcularValorVenda()`;
6. exibe o resultado na tela.

## 5. Classe MaterialReciclavel

A classe `MaterialReciclavel` e a classe base do sistema. Ela guarda os dados comuns para todos os materiais:

- descricao;
- peso;
- valor por quilo.

Ela tambem possui o metodo base `calcularValorVenda()`, que multiplica o peso pelo valor por quilo.

```php
<?php

class MaterialReciclavel
{
    private $descricao;
    private $peso;
    private $valorQuilo;

    public function __construct($descricao, $peso, $valorQuilo)
    {
        $this->setDescricao($descricao);
        $this->setPeso($peso);
        $this->setValorQuilo($valorQuilo);
    }

    public function calcularValorVenda()
    {
        return $this->peso * $this->valorQuilo;
    }

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
```

## 6. Classe Papel

A classe `Papel` herda de `MaterialReciclavel` e adiciona o atributo `tipoPapel`, que representa a qualidade do papel.

O calculo do papel usa multiplicadores:

| Qualidade | Multiplicador |
| --- | --- |
| Limpo | 1.10 |
| Comum | 1.00 |
| Umido ou misturado | 0.85 |

```php
<?php

class Papel extends MaterialReciclavel
{
    private $tipoPapel;

    public function __construct($descricao, $peso, $valorQuilo, $tipoPapel)
    {
        parent::__construct($descricao, $peso, $valorQuilo);
        $this->setTipoPapel($tipoPapel);
    }

    public function calcularValorVenda()
    {
        return parent::calcularValorVenda() * $this->getMultiplicadorQualidade();
    }

    public function getTipoPapel()
    {
        return $this->tipoPapel;
    }

    public function getMultiplicadorQualidade()
    {
        return match ($this->tipoPapel) {
            'limpo' => 1.10,
            'umido_misturado' => 0.85,
            default => 1.00,
        };
    }

    public function getDescricaoQualidade()
    {
        return match ($this->tipoPapel) {
            'limpo' => 'Limpo',
            'umido_misturado' => 'Umido ou misturado',
            default => 'Comum',
        };
    }

    public function setTipoPapel($tipoPapel)
    {
        $opcoesValidas = ['limpo', 'comum', 'umido_misturado'];
        $this->tipoPapel = in_array($tipoPapel, $opcoesValidas, true) ? $tipoPapel : 'comum';
    }
}
```

## 7. Classe Plastico

A classe `Plastico` herda de `MaterialReciclavel` e adiciona o atributo `tipoPlastico`.

O calculo do plastico usa multiplicadores:

| Tipo | Multiplicador |
| --- | --- |
| PET | 1.20 |
| PEAD | 1.10 |
| Outros | 1.00 |

```php
<?php

class Plastico extends MaterialReciclavel
{
    private $tipoPlastico;

    public function __construct($descricao, $peso, $valorQuilo, $tipoPlastico)
    {
        parent::__construct($descricao, $peso, $valorQuilo);
        $this->setTipoPlastico($tipoPlastico);
    }

    public function calcularValorVenda()
    {
        return parent::calcularValorVenda() * $this->getMultiplicadorTipo();
    }

    public function getTipoPlastico()
    {
        return $this->tipoPlastico;
    }

    public function getMultiplicadorTipo()
    {
        return match ($this->tipoPlastico) {
            'pet' => 1.20,
            'pead' => 1.10,
            default => 1.00,
        };
    }

    public function getDescricaoTipo()
    {
        return match ($this->tipoPlastico) {
            'pet' => 'PET',
            'pead' => 'PEAD',
            default => 'Outros',
        };
    }

    public function setTipoPlastico($tipoPlastico)
    {
        $opcoesValidas = ['pet', 'pead', 'outros'];
        $this->tipoPlastico = in_array($tipoPlastico, $opcoesValidas, true) ? $tipoPlastico : 'outros';
    }
}
```

## 8. Classe Metal

A classe `Metal` herda de `MaterialReciclavel` e adiciona:

- tipo do metal;
- percentual de pureza.

O valor do metal e calculado de acordo com a pureza:

```text
peso * valorPorQuilo * (purezaPercentual / 100)
```

```php
<?php

class Metal extends MaterialReciclavel
{
    private $tipoMetal;
    private $purezaPercentual;

    public function __construct($descricao, $peso, $valorQuilo, $tipoMetal, $purezaPercentual)
    {
        parent::__construct($descricao, $peso, $valorQuilo);
        $this->setTipoMetal($tipoMetal);
        $this->setPurezaPercentual($purezaPercentual);
    }

    public function calcularValorVenda()
    {
        return parent::calcularValorVenda() * ($this->purezaPercentual / 100);
    }

    public function getTipoMetal()
    {
        return $this->tipoMetal;
    }

    public function getPurezaPercentual()
    {
        return $this->purezaPercentual;
    }

    public function setTipoMetal($tipoMetal)
    {
        $this->tipoMetal = $tipoMetal;
    }

    public function setPurezaPercentual($purezaPercentual)
    {
        $this->purezaPercentual = max(0, min(100, $purezaPercentual));
    }
}
```

## 9. Como os objetos sao instanciados

Os objetos sao criados no arquivo `processa.php`, depois que o sistema recebe e valida os dados enviados pelo formulario.

Quando o usuario escolhe papel, o sistema cria um objeto da classe `Papel`:

```php
$material = new Papel($descricao, $peso, $valorQuilo, $tipoPapel);
```

Quando o usuario escolhe plastico, o sistema cria um objeto da classe `Plastico`:

```php
$material = new Plastico($descricao, $peso, $valorQuilo, $tipoPlastico);
```

Quando o usuario escolhe metal, o sistema cria um objeto da classe `Metal`:

```php
$material = new Metal($descricao, $peso, $valorQuilo, $tipoMetal, $purezaPercentual);
```

Depois disso, o calculo e feito com a mesma chamada para todos os tipos:

```php
$valorVenda = $material ? $material->calcularValorVenda() : 0.0;
```

Isso demonstra o polimorfismo, pois a variavel `$material` pode guardar um objeto de `Papel`, `Plastico` ou `Metal`, e cada classe executa sua propria versao do metodo `calcularValorVenda()`.

## 10. Exemplo de calculo

Exemplo com papel limpo:

```text
Peso: 10 kg
Valor por quilo: R$ 2,00
Multiplicador: 1.10

Valor final = 10 * 2 * 1.10 = R$ 22,00
```

Exemplo com plastico PET:

```text
Peso: 10 kg
Valor por quilo: R$ 2,00
Multiplicador: 1.20

Valor final = 10 * 2 * 1.20 = R$ 24,00
```

Exemplo com metal com 50% de pureza:

```text
Peso: 10 kg
Valor por quilo: R$ 2,00
Pureza: 50%

Valor final = 10 * 2 * 0.50 = R$ 10,00
```

## 11. Conceitos de POO usados

| Conceito | Onde aparece |
| --- | --- |
| Classe | `MaterialReciclavel`, `Papel`, `Plastico`, `Metal` |
| Objeto | Criado com `new Papel`, `new Plastico` ou `new Metal` |
| Encapsulamento | Atributos `private` com getters e setters |
| Heranca | As subclasses herdam de `MaterialReciclavel` |
| Polimorfismo | Cada subclasse sobrescreve `calcularValorVenda()` |
| Construtor | Metodo `__construct()` em cada classe |

## 12. Recurso de apresentacao: Bastidores do PHP

A tela de resultado tambem possui uma telinha flutuante chamada **Bastidores do PHP**. Ele mostra, em linguagem simples, o caminho dos dados dentro do sistema:

1. o formulario envia os dados por POST;
2. o arquivo `backend/processa.php` recebe e valida os campos;
3. o PHP instancia a classe correta, como `Papel`, `Plastico` ou `Metal`;
4. a classe usada herda de `MaterialReciclavel`;
5. o metodo `calcularValorVenda()` executa a regra propria da classe;
6. o resultado final e exibido para o usuario.

Esse recurso ajuda na apresentacao porque mostra claramente os conceitos de classe, objeto, heranca e polimorfismo enquanto o sistema esta funcionando.

## 13. Impacto ambiental

Apos o calculo, o sistema exibe uma mensagem curta de impacto ambiental conforme o tipo de material reciclado. Isso conecta o calculo financeiro com o problema real da cooperativa: reduzir descarte, organizar materiais e gerar renda.
## 14. Conclusao

O sistema cumpre a proposta do tema de cooperativa de reciclagem, pois permite cadastrar materiais reciclaveis, calcular seu valor de venda e demonstrar os conceitos principais de Programacao Orientada a Objetos em PHP.





