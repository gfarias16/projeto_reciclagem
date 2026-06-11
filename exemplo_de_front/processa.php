<?php

require_once 'Medico.php';
require_once 'Pediatra.php';
require_once 'Anestesista.php';

$tipo = $_POST['tipo'] ?? '';
$nome = $_POST['nome'] ?? '';
$crm = $_POST['crm'] ?? '';
$especialidade = $_POST['especialidade'] ?? '';

function moeda($valor) {
    return number_format($valor, 2, ',', '.');
}

function limparTexto($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

$recebimento = 0;
$empregado = null;
$titulo = '';
$detalhes = [];
$mensagem = 'Faça direito seu 71!';

if ($tipo === 'pediatra') {
    $mensagem = 'Muito bem!!';
    $salarioBase = floatval($_POST['salarioBase'] ?? 0);
    $honorarioHora = floatval($_POST['honorarioHora'] ?? 0);
    $horasTrabalhadas = floatval($_POST['horasTrabalhadas'] ?? 0);
    $atendeEmDomicilio = isset($_POST['atendeEmDomicilio']);
    $planoSaude = isset($_POST['planoSaude']);

    $empregado = new Pediatra(
        $nome,
        $crm,
        $especialidade,
        $salarioBase,
        $honorarioHora,
        $horasTrabalhadas,
        $atendeEmDomicilio,
        $planoSaude
    );

    $recebimento = $empregado->calcularRecebimentos();
    $titulo = 'Pediatra';
    $detalhes[] = 'Salário base: R$ ' . moeda($salarioBase);
    $detalhes[] = 'Honorário por hora: R$ ' . moeda($honorarioHora);
    $detalhes[] = 'Horas trabalhadas: ' . moeda($horasTrabalhadas);
    $detalhes[] = 'Atende em domicílio: ' . ($atendeEmDomicilio ? 'Sim' : 'Não');
    $detalhes[] = 'Plano de saúde: ' . ($planoSaude ? 'Sim' : 'Não');

} elseif ($tipo === 'anestesista') {
    $mensagem = 'Muito bem!!';
    $salarioBase = floatval($_POST['salarioBase'] ?? 0);
    $honorarioHora = floatval($_POST['honorarioHora'] ?? 0);
    $valorAnestesiaEspecial = floatval($_POST['valorAnestesiaEspecial'] ?? 0);
    $horasAnestesiaEspecial = floatval($_POST['horasAnestesiaEspecial'] ?? 0);
    $qtdehorasAnestesia = floatval($_POST['qtdehorasAnestesia'] ?? 0);

    $empregado = new Anestesista(
        $nome,
        $crm,
        $especialidade,
        $salarioBase,
        $honorarioHora,
        $valorAnestesiaEspecial,
        $horasAnestesiaEspecial,
        $qtdehorasAnestesia
    );

    $recebimento = $empregado->calcularRecebimentosComAcrescimos();
    $titulo = 'Anestesista';
    $detalhes[] = 'Salário base: R$ ' . moeda($salarioBase);
    $detalhes[] = 'Honorário por hora: R$ ' . moeda($honorarioHora);
    $detalhes[] = 'Quantidade de horas de anestesia: ' . moeda($qtdehorasAnestesia);
    $detalhes[] = 'Horas de anestesia especial: ' . moeda($horasAnestesiaEspecial);
    $detalhes[] = 'Valor da anestesia especial: R$ ' . moeda($valorAnestesiaEspecial);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Cálculo</title>
    <link rel="stylesheet" href="cadastro.css">
</head>
<body>
    <main class="page-shell">
        <section class="card">
            <header class="card-header">
                <div>
                    <h1>Resultado do Cálculo</h1>
                </div>
            </header>

            <div class="result-grid">
                <?php if ($empregado !== null): ?>
                    <div class="result-block">
                        <p><strong>Nome:</strong> <?php echo limparTexto($empregado->getNome()); ?></p>
                        <p><strong>CRM:</strong> <?php echo limparTexto($empregado->getCrm()); ?></p>
                        <p><strong>Especialidade:</strong> <?php echo limparTexto($empregado->getEspecialidade()); ?></p>
                        <?php foreach ($detalhes as $detalhe): ?>
                            <p><?php echo limparTexto($detalhe); ?></p>
                        <?php endforeach; ?>
                        <p class="result-value"><strong>Recebimento:</strong> R$ <?php echo moeda($recebimento); ?></p>
                    </div>
                <?php else: ?>
                    <div class="result-block">
                        <p>Tipo de empregado inválido. Volte ao formulário e selecione uma opção válida.</p>
                    </div>
                <?php endif; ?>

                <div class="actions">
                    <a href="cadastro.html" class="btn-submit">Voltar</a>
                </div>
            </div>
        </section>
    </main>

    <div class="farmou-aura-container">
        <div class="farmou-aura-text">FARMOU AURA!!!</div>
    </div>

    <div class="luciene-wrapper" aria-hidden="true">
        <div class="luciene-speech"><?php echo $mensagem; ?></div>
        <div class="luciene-sprite">
            <div class="luciene-hair"></div>
            <div class="luciene-face">
                <div class="eye left-eye"></div>
                <div class="eye right-eye"></div>
                <div class="glasses">
                    <span class="lens left"></span>
                    <span class="lens right"></span>
                </div>
                <div class="mouth"></div>
            </div>
            <div class="luciene-body"></div>
        </div>
    </div>

    <script>
        // Desaparecer FARMOU AURA após 5 segundos
        const farmouAuraContainer = document.querySelector('.farmou-aura-container');
        if (farmouAuraContainer) {
            setTimeout(() => {
                farmouAuraContainer.classList.add('fade-out');
            }, 5000);
        }

        const luciene = document.querySelector('.luciene-wrapper');
        const moveLuciene = () => {
            const margin = 32;
            const vw = window.innerWidth - luciene.offsetWidth - margin;
            const vh = window.innerHeight - luciene.offsetHeight - margin;
            if (vw <= 0 || vh <= 0) return;
            const left = Math.random() * vw + margin / 2;
            const top = Math.random() * vh + margin / 2;
            luciene.style.transform = `translate(${left}px, ${top}px)`;
        };
        window.addEventListener('load', () => {
            moveLuciene();
            setInterval(moveLuciene, 5200);
            window.addEventListener('resize', moveLuciene);
        });
    </script>
</body>
</html>
