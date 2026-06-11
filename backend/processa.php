<?php

// Importa as classes usadas para criar os objetos do sistema.
require_once __DIR__ . '/classes/Materia_Reciclavel.php';
require_once __DIR__ . '/classes/Papel.php';
require_once __DIR__ . '/classes/Plastico.php';
require_once __DIR__ . '/classes/Metal.php';

// Protege textos exibidos na tela contra caracteres maliciosos.
function limparTexto($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Formata valores em reais para a exibicao.
function moeda($valor)
{
    return number_format($valor, 2, ',', '.');
}

// Formata numeros decimais comuns, como peso e percentual.
function decimal($valor)
{
    return number_format($valor, 2, ',', '.');
}

// Retorna uma mensagem simples de impacto ambiental conforme o tipo de material.
function obterImpactoAmbiental($tipo, $peso)
{
    $pesoFormatado = decimal($peso);

    return match ($tipo) {
        'papel' => "Voce reciclou {$pesoFormatado} kg de papel. Isso ajuda a reduzir o descarte comum e incentiva o reaproveitamento de fibras.",
        'plastico' => "Voce reciclou {$pesoFormatado} kg de plastico. Isso ajuda a evitar que residuos plasticos cheguem ao solo, rios e bueiros.",
        'metal' => "Voce reciclou {$pesoFormatado} kg de metal. O reaproveitamento de metal reduz desperdicio e costuma gerar bom retorno financeiro.",
        default => 'Material recebido pela cooperativa para avaliacao.',
    };
}

// Recebe os dados comuns enviados pelo formulario.
$cooperativa = $_POST['cooperativa'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$peso = filter_input(INPUT_POST, 'peso', FILTER_VALIDATE_FLOAT);
$valorQuilo = filter_input(INPUT_POST, 'valor_quilo', FILTER_VALIDATE_FLOAT);

// Variaveis usadas para montar a resposta da pagina.
$material = null;
$titulo = 'Material invalido';
$detalhes = [];
$erros = [];
$mensagem = 'Revise os dados enviados.';
$impactoAmbiental = '';
$classeExecutada = '';
$regraExecutada = '';
$bastidores = [];
$codigoExecutado = [];

// Validacao dos campos comuns a todos os materiais.
if ($cooperativa === '') {
    $erros[] = 'Informe o nome da cooperativa.';
}

if (!in_array($tipo, ['papel', 'plastico', 'metal'], true)) {
    $erros[] = 'Selecione um tipo de material valido.';
}

if ($descricao === '') {
    $erros[] = 'Informe a descricao do material.';
}

if ($peso === false || $peso <= 0) {
    $erros[] = 'Informe um peso maior que zero.';
}

if ($valorQuilo === false || $valorQuilo <= 0) {
    $erros[] = 'Informe um valor por quilo maior que zero.';
}

// Se os dados comuns estiverem corretos, o PHP cria o objeto do tipo escolhido.
if (!$erros) {
    // Criacao do objeto Papel e detalhes especificos do papel.
    if ($tipo === 'papel') {
        $tipoPapel = $_POST['tipo_papel'] ?? '';

        if (!in_array($tipoPapel, ['limpo', 'comum', 'umido_misturado'], true)) {
            $erros[] = 'Selecione a qualidade do papel.';
        } else {
            $material = new Papel($descricao, $peso, $valorQuilo, $tipoPapel);
            $titulo = 'Papel';
            $mensagem = 'Papel separado com sucesso!';
            $detalhes[] = 'Qualidade: ' . $material->getDescricaoQualidade();
            $detalhes[] = 'Multiplicador aplicado: ' . decimal($material->getMultiplicadorQualidade()) . 'x';
        }

    // Criacao do objeto Plastico e detalhes especificos do plastico.
    } elseif ($tipo === 'plastico') {
        $tipoPlastico = $_POST['tipo_plastico'] ?? '';

        if (!in_array($tipoPlastico, ['pet', 'pead', 'outros'], true)) {
            $erros[] = 'Selecione o tipo de plastico.';
        } else {
            $material = new Plastico($descricao, $peso, $valorQuilo, $tipoPlastico);
            $titulo = 'Plastico';
            $mensagem = 'Plastico classificado com sucesso!';
            $detalhes[] = 'Tipo de plastico: ' . $material->getDescricaoTipo();
            $detalhes[] = 'Multiplicador aplicado: ' . decimal($material->getMultiplicadorTipo()) . 'x';
        }

    // Criacao do objeto Metal e detalhes especificos do metal.
    } elseif ($tipo === 'metal') {
        $tipoMetal = $_POST['tipo_metal'] ?? '';
        $purezaPercentual = filter_input(INPUT_POST, 'pureza_percentual', FILTER_VALIDATE_FLOAT);

        if ($tipoMetal === '') {
            $erros[] = 'Informe o tipo de metal.';
        }

        if ($purezaPercentual === false || $purezaPercentual < 0 || $purezaPercentual > 100) {
            $erros[] = 'Informe uma pureza entre 0 e 100.';
        }

        if (!$erros) {
            $material = new Metal($descricao, $peso, $valorQuilo, $tipoMetal, $purezaPercentual);
            $titulo = 'Metal';
            $mensagem = 'Metal pesado e conferido!';
            $detalhes[] = 'Tipo de metal: ' . $material->getTipoMetal();
            $detalhes[] = 'Pureza: ' . decimal($material->getPurezaPercentual()) . '%';
        }
    }
}

// Chamada polimorfica: cada objeto executa sua propria versao de calcularValorVenda().
$valorVenda = $material ? $material->calcularValorVenda() : 0.0;

// Monta os textos educativos exibidos na tela de resultado para facilitar a apresentacao.
if ($material) {
    $classeExecutada = get_class($material);
    $impactoAmbiental = obterImpactoAmbiental($tipo, $material->getPeso());

    $regraExecutada = match ($tipo) {
        'papel' => 'Valor base multiplicado pela qualidade do papel.',
        'plastico' => 'Valor base multiplicado pelo tipo do plastico.',
        'metal' => 'Valor base multiplicado pelo percentual de pureza do metal.',
        default => 'Regra geral de material reciclavel.',
    };

    $bastidores = [
        '1. O formulario enviou os dados pelo metodo POST.',
        "   Cooperativa informada: {$cooperativa}.",
        '2. O arquivo backend/processa.php recebeu e validou os campos.',
        "3. O sistema criou um objeto da classe {$classeExecutada}.",
        "4. A classe {$classeExecutada} herdou atributos e metodos de MaterialReciclavel.",
        '5. A chamada calcularValorVenda() executou a regra propria da classe.',
        '6. O PHP formatou o resultado e montou esta tela para o usuario.',
    ];

    $codigoExecutado = [
        "POST['cooperativa'] = '{$cooperativa}'",
        "POST['tipo'] = '{$tipo}'",
        "POST['descricao'] = '" . $material->getDescricao() . "'",
        "new {$classeExecutada}(...) // objeto criado com os dados validados",
        '$material->calcularValorVenda() // polimorfismo em acao',
        'Valor final: R$ ' . moeda($valorVenda),
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Configuracoes basicas da pagina de resultado. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Cooperativa de Reciclagem</title>
    <link rel="stylesheet" href="../frontend/style.css">
</head>
<body>
    <!-- Estrutura principal da pagina de resultado. -->
    <main class="pagina resultado-pagina">
        <!-- Bloco lateral com titulo do resultado. -->
        <section class="apresentacao">
            <span class="selo">Resultado</span>
            <h1><?php echo limparTexto($titulo); ?></h1>
            <p>
                <?php echo $material ? 'Valor calculado usando polimorfismo no metodo calcularValorVenda().' : 'Nao foi possivel calcular o material enviado.'; ?>
            </p>
        </section>

        <!-- Bloco com os dados calculados ou mensagens de erro. -->
        <section class="formulario-area resultado-area" aria-labelledby="titulo-resultado">
            <h2 id="titulo-resultado">Resumo do calculo</h2>

            <!-- Exibe o resumo quando o objeto foi criado com sucesso. -->
            <?php if ($material): ?>
                <div class="resultado-card">
                    <p><strong>Cooperativa:</strong> <?php echo limparTexto($cooperativa); ?></p>
                    <p><strong>Descricao:</strong> <?php echo limparTexto($material->getDescricao()); ?></p>
                    <p><strong>Peso:</strong> <?php echo decimal($material->getPeso()); ?> kg</p>
                    <p><strong>Valor por quilo:</strong> R$ <?php echo moeda($material->getValorQuilo()); ?></p>

                    <?php foreach ($detalhes as $detalhe): ?>
                        <p><?php echo limparTexto($detalhe); ?></p>
                    <?php endforeach; ?>

                    <p class="valor-final"><strong>Valor de venda:</strong> R$ <?php echo moeda($valorVenda); ?></p>
                </div>

                <!-- Mostra o impacto ambiental de forma simples para conectar o calculo com o problema real. -->
                <div class="impacto-card">
                    <span class="card-etiqueta">Impacto da reciclagem</span>
                    <p><?php echo limparTexto($impactoAmbiental); ?></p>
                </div>

            <!-- Exibe os erros quando algum dado esta incorreto. -->
            <?php else: ?>
                <div class="resultado-card erro-card">
                    <?php foreach ($erros as $erro): ?>
                        <p><?php echo limparTexto($erro); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Link para voltar ao formulario inicial. -->
            <div class="acoes">
                <a href="../frontend/formulario.html" class="botao-principal link-botao">Voltar ao cadastro</a>
            </div>
        </section>
    </main>

    <!-- Painel lateral: mostra os bastidores do PHP como se fosse uma pequena tela de execucao. -->
    <?php if ($material): ?>
        <aside class="bastidores-flutuante" aria-label="Bastidores do processamento em PHP">
            <div class="terminal-topo">
                <span></span>
                <span></span>
                <span></span>
                <strong>execucao.php</strong>
            </div>

            <div class="terminal-conteudo">
                <span class="card-etiqueta etiqueta-terminal">Bastidores do PHP</span>
                <h3>Codigo em execucao</h3>

                <ol class="fluxo-lista terminal-fluxo">
                    <?php foreach ($bastidores as $passo): ?>
                        <li><?php echo limparTexto($passo); ?></li>
                    <?php endforeach; ?>
                </ol>

                <div class="poo-resumo terminal-resumo">
                    <p><strong>Classe:</strong> <?php echo limparTexto($classeExecutada); ?></p>
                    <p><strong>Heranca:</strong> <?php echo limparTexto($classeExecutada); ?> extends MaterialReciclavel</p>
                    <p><strong>Polimorfismo:</strong> <?php echo limparTexto($regraExecutada); ?></p>
                </div>

                <pre class="codigo-execucao codigo-rolando"><code><?php echo limparTexto(implode("\n", array_merge($codigoExecutado, $bastidores, $codigoExecutado))); ?></code></pre>
            </div>
        </aside>
    <?php endif; ?>

    <!-- Texto animado exibido apenas quando o calculo da certo. -->
    <?php if ($material): ?>
        <div class="reciclagem-aura-container">
            <div class="reciclagem-aura-text">RECICLOU!</div>
        </div>
    <?php endif; ?>
    <!-- Imagem real da professora com balao de fala na tela de resultado. -->
    <div class="professora-wrapper" aria-hidden="true">
        <div class="professora-speech<?php echo $material ? '' : ' speech-alert'; ?>">
            <?php echo limparTexto($material ? $mensagem : 'Confira os campos!'); ?>
        </div>
        <div class="professora-sprite">
            <img src="../img/img_luciene.png" alt="" class="professora-imagem">
        </div>
    </div>

    <script>
        // Faz o texto animado desaparecer depois de alguns segundos.
        const aura = document.querySelector('.reciclagem-aura-container');
        if (aura) {
            setTimeout(() => {
                aura.classList.add('fade-out');
            }, 4200);
        }

        // Seleciona a personagem que se movimenta pela tela.
        const professora = document.querySelector('.professora-wrapper');

        // Seleciona a telinha de bastidores para permitir arrastar com o mouse.
        const painelBastidores = document.querySelector('.bastidores-flutuante');
        const barraBastidores = document.querySelector('.terminal-topo');

        // Move a personagem para uma posicao aleatoria dentro da janela.
        function moverProfessora() {
            const margem = 28;
            const limiteX = window.innerWidth - professora.offsetWidth - margem;
            const limiteY = window.innerHeight - professora.offsetHeight - margem;

            if (limiteX <= 0 || limiteY <= 0) {
                return;
            }

            const left = Math.random() * limiteX + margem / 2;
            const top = Math.random() * limiteY + margem / 2;
            professora.style.transform = `translate(${left}px, ${top}px)`;
        }

        // Permite clicar na barra superior do painel e arrastar para outro lugar da tela.
        function ativarPainelArrastavel() {
            if (!painelBastidores || !barraBastidores) {
                return;
            }

            let arrastando = false;
            let distanciaX = 0;
            let distanciaY = 0;

            barraBastidores.addEventListener('mousedown', event => {
                if (window.innerWidth < 1180) {
                    return;
                }

                const posicaoAtual = painelBastidores.getBoundingClientRect();
                arrastando = true;
                distanciaX = event.clientX - posicaoAtual.left;
                distanciaY = event.clientY - posicaoAtual.top;

                painelBastidores.classList.add('arrastando');
                painelBastidores.style.left = `${posicaoAtual.left}px`;
                painelBastidores.style.top = `${posicaoAtual.top}px`;
                painelBastidores.style.right = 'auto';
                painelBastidores.style.transform = 'none';
            });

            document.addEventListener('mousemove', event => {
                if (!arrastando) {
                    return;
                }

                const margem = 12;
                const larguraMaxima = window.innerWidth - painelBastidores.offsetWidth - margem;
                const alturaMaxima = window.innerHeight - painelBastidores.offsetHeight - margem;
                const novoLeft = Math.max(margem, Math.min(event.clientX - distanciaX, larguraMaxima));
                const novoTop = Math.max(margem, Math.min(event.clientY - distanciaY, alturaMaxima));

                painelBastidores.style.left = `${novoLeft}px`;
                painelBastidores.style.top = `${novoTop}px`;
            });

            document.addEventListener('mouseup', () => {
                arrastando = false;
                painelBastidores.classList.remove('arrastando');
            });
        }

        // Inicia o movimento quando a pagina termina de carregar.
        window.addEventListener('load', () => {
            moverProfessora();
            ativarPainelArrastavel();
            setInterval(moverProfessora, 5200);
            window.addEventListener('resize', moverProfessora);
        });
    </script>
</body>
</html>








