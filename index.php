

<?php

// INICIAR SESSÃO
session_start();

if (!isset($_SESSION["recargas"])) {
    $_SESSION["recargas"] = 0;
}

// CARREGAR ARQUIVOS
require_once __DIR__ . "/funcoes.php";
require_once __DIR__ . "/robo.php";

$robos = lerRobos();
$mensagem = "";

// PROCESSAR FORMULÁRIOS
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $acao = $_POST["acao"] ?? "cadastrar";

    // CADASTRAR NOVO ROBÔ
    if ($acao == "cadastrar") {

        $nome = trim($_POST["nome"] ?? "");
        $modelo = $_POST["modelo"] ?? "";
        $operador = trim($_POST["operador"] ?? "");

        if ($nome == "" || $modelo == "" || $operador == "") {

            $mensagem = "Erro: preencha todos os campos!";

        } else {

            if ($modelo == "Explorador") {
                $energia = 100;
                $potencia = 20;

            } elseif ($modelo == "Engenheiro") {
                $energia = 120;
                $potencia = 15;

            } elseif ($modelo == "Defensor") {
                $energia = 150;
                $potencia = 30;

            } else {
                $mensagem = "Erro: modelo inválido!";
            }

            if (isset($energia) && isset($potencia)) {

                $robo = new Robo(
                    $nome,
                    $modelo,
                    $energia,
                    $potencia
                );

                $novoRobo = $robo->exibirStatus();
                $novoRobo["operador"] = $operador;

                $robos[] = $novoRobo;

                salvarRobos($robos);

                header("Location: index.php?sucesso=1");
                exit;
            }
        }
    }

    // RECARREGAR ENERGIA
    elseif ($acao == "recarregar") {

        $indice = filter_input(
            INPUT_POST,
            "indice",
            FILTER_VALIDATE_INT
        );

        if (
            $indice !== false &&
            $indice !== null &&
            isset($robos[$indice])
        ) {

            $dados = $robos[$indice];

            $robo = new Robo(
                $dados["nome"],
                $dados["modelo"],
                $dados["energia"],
                $dados["potencia"]
            );

            // GUARDAR ENERGIA ANTERIOR
            $energiaAnterior = $robo->energia;

            // EXECUTAR RECARGA
            $robo->recarregar();

            // ATUALIZAR ENERGIA
            $robos[$indice]["energia"] = $robo->energia;

            // CONTAR SOMENTE RECARGAS EFETIVAS
            if ($robo->energia > $energiaAnterior) {
                $_SESSION["recargas"]++;
            }

            // SALVAR NO JSON
            salvarRobos($robos);

            header("Location: index.php?recarregado=1");
            exit;

        } else {
            $mensagem = "Erro: robô não encontrado!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Robôs</title>
    <link rel="stylesheet" href="teste.css">
</head>

<body>

    <h1>Central de Robôs</h1>

    <!-- CONTADOR DA SESSÃO -->
    <h3 class="contador">
        Recargas realizadas nesta sessão:
        <?= $_SESSION["recargas"] ?>
    </h3>

    <?php if ($mensagem != ""): ?>
        <p><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <h2>Cadastrar novo robô</h2>

    <form method="POST">

        <input type="hidden" name="acao" value="cadastrar">

        <label>Nome do robô:</label>
        <input type="text" name="nome" required>

        <br><br>

        <label>Modelo:</label>
        <select name="modelo" required>
            <option value="">Selecione</option>
            <option value="Explorador">Explorador</option>
            <option value="Engenheiro">Engenheiro</option>
            <option value="Defensor">Defensor</option>
        </select>

        <br><br>

        <label>Nome do operador:</label>
        <input type="text" name="operador" required>

        <br><br>

        <button type="submit">Cadastrar robô</button>

    </form>

    <hr>

    <h2>Robôs cadastrados</h2>

    <?php if (empty($robos)): ?>

        <p>Nenhum robô cadastrado.</p>

    <?php else: ?>

        <?php foreach ($robos as $indice => $robo): ?>

            <!-- CARTÃO DO ROBÔ -->
            <div class="robo-card">

                <h3>
                    <?= htmlspecialchars($robo["nome"] ?? "") ?>
                </h3>

                <p>
                    <strong>Modelo:</strong>
                    <?= htmlspecialchars($robo["modelo"] ?? "") ?>
                    <br>

                    <strong>Operador:</strong>
                    <?= htmlspecialchars($robo["operador"] ?? "Não informado") ?>
                    <br>

                    <strong>Energia:</strong>
                    <?= (int) ($robo["energia"] ?? 0) ?> / 200
                    <br>

                    <strong>Potência:</strong>
                    <?= (int) ($robo["potencia"] ?? 0) ?>
                </p>

                <!-- BOTÃO DE RECARGA -->
                <form method="POST">

                    <input
                        type="hidden"
                        name="acao"
                        value="recarregar"
                    >

                    <input
                        type="hidden"
                        name="indice"
                        value="<?= $indice ?>"
                    >

                    <button
                        type="submit"
                        <?= ($robo["energia"] ?? 0) >= 200 ? "disabled" : "" ?>
                    >
                        Recarregar +10
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <!-- POP-UP DE CADASTRO -->
    <?php if (isset($_GET["sucesso"])): ?>

        <script>
            window.history.replaceState({}, "", "index.php");
            alert("Robô cadastrado com sucesso!");
        </script>

    <?php endif; ?>

    <!-- POP-UP DE RECARGA -->
    <?php if (isset($_GET["recarregado"])): ?>

        <script>
            window.history.replaceState({}, "", "index.php");
            alert("Energia recarregada com sucesso!");
        </script>

    <?php endif; ?>

</body>
</html>
