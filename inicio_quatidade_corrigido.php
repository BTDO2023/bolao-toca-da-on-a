<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Verificar se o token na URL corresponde ao token associado ao usuário
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    // Verifique o token no banco de dados ou em algum lugar seguro
    // Se o token não for válido, redirecione para a página de login
    // Caso contrário, o usuário está autorizado a acessar a página
} else {
    header("Location: login.php");
    exit;
}

// Armazenar o nome de usuário na sessão
$email = $_SESSION['username']; // Supondo que o e-mail do usuário esteja armazenado na sessão
$username = explode('@', $email)[0]; // Obtendo o nome de usuário a partir do e-mail
$_SESSION['username'] = $username;

// Verificar se o usuário é "toca@hotmail.com"
$is_toca = ($email === 'toca');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLÃO TOCA DA ONÇA</title>
    <style>
        /* Estilos CSS */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            position: relative; /* Adicionado para o posicionamento do menu */
        }
        input[type="text"] {
            font-size: 18px;
            width: 300px;
            border: 1px solid black;
            text-transform: uppercase; /* Converter todas as letras em maiúsculas */
            border-radius: 10px; /* Cantos mais arredondados */
            padding: 8px; /* Espaçamento interno */
            margin-bottom: 10px; /* Espaçamento inferior */
        }
        input[type="text"].error {
            border: 1px solid red;
        }
        #mensagem {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            z-index: 9999;
            border-radius: 0 0 10px 10px; /* Cantos superiores mais arredondados */
        }
        #confirmacao {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            text-align: center;
            border-radius: 10px; /* Cantos mais arredondados */
        }
        #confirmacao button {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        #confirmacao .sim {
            background-color: blue;
            color: white;
        }
        #confirmacao .nao {
            background-color: red;
            color: white;
        }
        #conteudo {
            margin: 20px;
            margin-top: 50px;
        }
        #formulario {
            max-width: 400px;
            margin: 0 auto; /* Centralizar o formulário */
        }
        #formulario label {
            display: block;
            margin-bottom: 8px;
        }
        #formulario input, #formulario select, #formulario textarea {
            padding: 10px;
            margin-bottom: 5px; /* Aproximando os campos */
            box-sizing: border-box;
            text-align: center;
            display: inline-block;
            border-radius: 10px; /* Cantos mais arredondados */
            border: 1px solid black; /* Bordas */
        }
        #formulario .campo-container {
            position: relative;
            display: flex;
            align-items: center;
            flex-direction: column-reverse;
        }
        .campo-container .contador-dezenas {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        .contador-campos {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }
        #formulario button {
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-right: 10px;
            border-radius: 5px;
            color: white; /* Cor do texto */
        }
        #formulario button.submit {
            background-color: #4CAF50; /* Cor de fundo verde */
        }
        #formulario button.cancelar {
            background-color: #FF0000; /* Cor de fundo vermelha */
        }
        #formulario button.repetir {
            background-color: #0080FF; /* Cor de fundo azul */
        }
        #formulario button.aleatorio {
            background-color: purple; /* Cor de fundo roxa */
        }
        #formulario button.colar {
            background-color: black; /* Cor de fundo preta */
        }
        #formulario button:hover,
        #formulario button.cancelar:hover,
        #formulario button.repetir:hover,
        #formulario button.aleatorio:hover,
        #formulario button.colar:hover {
            background-color: #45a049; /* Cor de fundo ao passar o mouse */
        }
        .jogo-adicional {
            margin-top: 5px; /* Aproximando os campos */
            margin-bottom: 10px; /* Espaçamento inferior */
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: none;
            vertical-align: middle;
            margin-right: 5px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Estilos para o menu lateral */
        #menuLateral {
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            height: 100%;
            background-color: #333;
            color: white;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            padding: 20px;
            box-shadow: -4px 0 8px rgba(0, 0, 0, 0.2); /* Adiciona sombra para destaque */
            display: flex;
            flex-direction: column; /* Para garantir que o botão de fechar fique no topo */
        }

        /* Adiciona um contêiner para o botão de fechar e o conteúdo do menu */
        #menuLateral .menu-top {
            display: flex;
            justify-content: flex-end; /* Alinha o botão de fechar à direita */
            margin-bottom: 20px; /* Espaçamento entre o botão e o conteúdo do menu */
        }

        #menuLateral .menu-content {
            flex: 1; /* Faz com que o conteúdo do menu ocupe o espaço restante */
        }

        #menuLateral.open {
            transform: translateX(0);
        }

        #menuLateral div {
            margin-bottom: 20px; /* Espaçamento entre o nome do usuário e os links */
            font-size: 22px; /* Tamanho da fonte do nome do usuário */
            font-weight: bold; /* Destaca o nome do usuário */
        }

        #menuLateral a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px; /* Espaçamento interno */
            border-radius: 5px; /* Cantos arredondados */
            background-color: #444; /* Cor de fundo dos itens */
            transition: background-color 0.3s ease; /* Transição suave para a cor de fundo */
            margin-bottom: 10px; /* Espaçamento entre os itens */
        }

        #menuLateral a:hover {
            background-color: #555; /* Cor de fundo ao passar o mouse */
        }

        #menuBotao {
            position: fixed;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            z-index: 1002; /* Garante que o ícone do menu fique acima do menu lateral */
        }

        #menuFechar {
            width: 30px;
            height: 30px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2); /* Adiciona sombra para destaque */
            z-index: 1003; /* Garante que o botão de fechar fique acima do ícone de menu */
        }

        /* Posição do botão de fechar dentro do menu lateral */
        #menuLateral .menu-top #menuFechar {
            position: absolute;
            top: 10px;
            left: 10px; /* Coloca o botão de fechar no canto superior esquerdo do menu */
        }

        /* Esconder o campo de cambista se não for o usuário toca */
        <?php if (!$is_toca): ?>
        .cambista-container {
            display: none;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <div id="mensagem">Cadastrado com sucesso!</div>
    <div id="confirmacao">
        <p>Confirmar aposta?</p>
        <button class="sim">Sim</button>
        <button class="nao">Não</button>
    </div>
    <div id="conteudo">
        <!-- Botão para abrir o menu lateral -->
        <button id="menuBotao">&#9776;</button>

        <!-- Menu lateral -->
        <div id="menuLateral">
            <!-- Botão para fechar o menu lateral -->
            <div class="menu-top">
                <button id="menuFechar">&times;</button>
            </div>
            <!-- Exibindo o nome do usuário -->
            <div>Olá, <?php echo strtoupper(htmlspecialchars($username)); ?></div>

            <!-- Links no menu lateral -->
            <?php if ($is_toca): ?>
                <div class="menu-content">
                    <a href="https://script.google.com/macros/s/AKfycbyMB1J_d-Pz1NfF_6bFmPQb4FQQoLXclmYjzeq03KC4/dev">Repetir Apostas</a>
                    <a href="https://script.google.com/macros/s/AKfycbxzwXC8RP-FZXS1aSnME5eRVC6_ESZ_xhxs4m1h7PD4aWhZbh-joTP-vG197xYIuMkk2w/exec">Cancelar Apostas</a>
                    <a href="consultar_apostas.php">Consultar Apostas</a>
                    <a href="pdf.html">Gerar PDF</a>
                    <a href="consultar_usuario.php">Gerenciar Cambistas</a>
                </div>
            <?php else: ?>
                <div class="menu-content">
                    <a href="repetir_apostas.php">Repetir Apostas</a>
                    <a href="consultar_apostas.php">Consultar Apostas</a>
                </div>
            <?php endif; ?>
            <a href="logout.php">Sair ➡️</a>
        </div>

        <h1>BOLÃO TOCA DA ONÇA</h1>
        <div id="formulario">
            <form id="form" action="https://script.google.com/macros/s/AKfycbw0qYKv1eirln_RiwBEAJuzl2PlbcfjThvQZXL8lZ-CeMsgoi4l2WKbE_NiNGgCKNYgTA/exec" method="post">
                <div class="cambista-container"> <!-- Container para o campo de cambista -->
                    <label for="nomeApostador">CAMBISTA</label>
                    <input type="text" id="nomeApostador" name="nomeApostador" value="<?php echo strtoupper($username); ?>" <?php if (!$is_toca) echo "readonly"; ?> required <?php if ($is_toca) echo "oninput='this.value = this.value.toUpperCase();'"; ?>>
                </div>
                <label for="participante">PARTICIPANTE</label>
                <input type="text" id="participante" name="participante" required>
                <label for="jogo">JOGO 1:</label> <!-- Alterado de "DEZENAS" para "JOGO 1" -->
                <div class="campo-container">
                    <div class="contador-dezenas">0/10</div>
                    <input type="text" id="jogo" name="jogo" oninput="formatarJogos(this); verificarDezenas(this);" required maxlength="29">
                </div>
                <div id="jogosAdicionais" class="jogo-adicional"></div>
                <div id="contadorCampos" class="contador-campos">QTD DE APOSTAS: 1</div>
                <button type="submit" class="submit">
                    <span class="loader"></span>
                    ✔Cadastrar
                </button>
                <button type="button" class="cancelar" onclick="limparFormulario()">Cancelar</button>
                <button type="button" class="repetir" onclick="adicionarCampo()">+</button>
                <button type="button" class="aleatorio" onclick="gerarDezenasAleatorias()">Aleatório</button>
                <button type="button" class="colar" onclick="abrirFormularioColar()">Colar</button>
                <label for="quantidadeCampos">Escolher Quantidade:</label>
                <select id="quantidadeCampos" onchange="atualizarQuantidadeCampos(this.value)">
                    <?php
                    for ($i = 1; $i <= 100; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </form>
        </div>
    </div>
    <script>
        // Função para abrir e fechar o menu lateral
        document.getElementById('menuBotao').addEventListener('click', function() {
            var menu = document.getElementById('menuLateral');
            if (menu.classList.contains('open')) {
                menu.classList.remove('open');
            } else {
                menu.classList.add('open');
            }
        });

        document.getElementById('menuFechar').addEventListener('click', function() {
            document.getElementById('menuLateral').classList.remove('open');
        });

        let campoIndex = 1;
        let contadorCampos = 1;

        function adicionarCampo() {
            campoIndex++;
            contadorCampos++;
            const novoCampo = document.createElement("div");
            novoCampo.innerHTML = `
                <label for="jogo${campoIndex}">JOGO ${campoIndex}:</label>
                <div class="campo-container">
                    <div class="contador-dezenas">0/10</div>
                    <input type="text" id="jogo${campoIndex}" name="jogo${campoIndex}" oninput="formatarJogos(this); verificarDezenas(this);" required maxlength="29">
                </div>
            `;
            document.getElementById("jogosAdicionais").appendChild(novoCampo);
            document.getElementById("contadorCampos").innerText = `QTD DE APOSTAS: ${contadorCampos}`;
        }

        function abrirFormularioColar() {
            const numerosColados = prompt("Cole os números aqui:");
            if (numerosColados !== null) {
                distribuirNumeros(numerosColados);
            }
        }

        function distribuirNumeros(numerosColados) {
            // Remover o conteúdo entre colchetes usando expressão regular
            numerosColados = numerosColados.replace(/\[.*?\]/g, '');
            const numeros = numerosColados.match(/\d{2}/g);
            if (numeros) {
                const campos = document.querySelectorAll('input[id^="jogo"]');
                let currentIndex = 0;
                campos.forEach(function(campo) {
                    const dezenas = [];
                    for (let i = 0; i < 10; i++) {
                        if (numeros[currentIndex]) {
                            dezenas.push(numeros[currentIndex]);
                            currentIndex++;
                        }
                    }
                    campo.value = dezenas.join("|");
                    contarDezenas(campo);
                });
            } else {
                alert("Formato de números inválido. Use dois dígitos para cada número.");
            }
        }

        function removerCampos(quantidade) {
            for (let i = 0; i < quantidade; i++) {
                const campoRemovido = document.querySelector("#jogosAdicionais > div:last-child");
                if (campoRemovido) {
                    campoRemovido.remove();
                    campoIndex--;
                    contadorCampos--;
                }
            }
            document.getElementById("contadorCampos").innerText = `QTD DE APOSTAS: ${contadorCampos}`;
        }

        function atualizarQuantidadeCampos(novaQuantidade) {
    novaQuantidade = parseInt(novaQuantidade);
    if (!isNaN(novaQuantidade) && novaQuantidade >= 1 && novaQuantidade <= 100) {
        const diferenca = novaQuantidade - contadorCampos;
        if (diferenca > 0) {
            for (let i = 0; i < diferenca; i++) {
                adicionarCampo();
            }
        } else if (diferenca < 0) {
            removerCampos(Math.abs(diferenca));
        }
    }
}

function contarDezenas(input) {
    const contadorElemento = input.parentNode.querySelector(".contador-dezenas");
    const numeroDezenas = (input.value.match(/\d{2}/g) || []).length;
    contadorElemento.innerText = `${numeroDezenas}/10`;
}

function limparFormulario() {
    document.getElementById("participante").value = "";
    document.getElementById("jogo").value = "";
    document.getElementById("jogosAdicionais").innerHTML = "";
    document.querySelector("#formulario .contador-dezenas").innerText = "0/10";
    contadorCampos = 1;
    campoIndex = 1; // Reset do índice dos campos
    document.getElementById("contadorCampos").innerText = `QTD DE APOSTAS: ${contadorCampos}`;
    document.getElementById("quantidadeCampos").value = "1"; // Reset para 1
    document.getElementById("mensagem").style.display = "block";
    setTimeout(function() {
        document.getElementById("mensagem").style.display = "none";
    }, 10000); // Oculta a mensagem após 10 segundos

    // Parar a animação do botão e reativar o botão "Cadastrar"
    var loader = document.querySelector("#form .loader");
    var submitButton = document.querySelector("#form .submit");
    loader.style.display = "none";
    submitButton.disabled = false;
}

// Adiciona a função para exibir a confirmação
function exibirConfirmacao() {
    document.getElementById("confirmacao").style.display = "block";
}

// Função para ocultar a confirmação
function ocultarConfirmacao() {
    document.getElementById("confirmacao").style.display = "none";
}

// Adiciona evento de clique para o botão "Não"
document.querySelector("#confirmacao .nao").addEventListener("click", function() {
    ocultarConfirmacao();
});

function formatarJogos(input) {
    var numericValue = input.value.replace(/\D/g, '');
    var cursorPos = input.selectionStart; // Armazena a posição do cursor antes da formatação
    var formattedValue = numericValue.replace(/(\d{2})(?=\d)/g, '$1|'); // Insere "|" após cada par de dígitos
    input.value = formattedValue;
    // Restaura a posição do cursor após a formatação
    input.setSelectionRange(cursorPos + Math.floor(cursorPos / 2), cursorPos + Math.floor(cursorPos / 2));
    contarDezenas(input);
}

function gerarDezenasAleatorias() {
    function formatarNumero(numero) {
        return numero.toString().padStart(2, '0');
    }

    let dezenas = [];
    while (dezenas.length < 10) {
        let numero = Math.floor(Math.random() * 100);
        let dezenaFormatada = formatarNumero(numero);
        if (!dezenas.includes(dezenaFormatada)) {
            dezenas.push(dezenaFormatada);
        }
    }

    document.getElementById("jogo").value = dezenas.join('|');

    contarDezenas(document.getElementById("jogo"));

    let camposAdicionais = document.querySelectorAll('[id^="jogo"]');
    camposAdicionais.forEach(function(campo, index) {
        if (index > 0) {
            let dezenasCampo = [];
            while (dezenasCampo.length < 10) {
                let numero = Math.floor(Math.random() * 100);
                let dezenaFormatada = formatarNumero(numero);
                if (!dezenasCampo.includes(dezenaFormatada) && !dezenas.includes(dezenaFormatada)) {
                    dezenasCampo.push(dezenaFormatada);
                }
            }
            campo.value = dezenasCampo.join('|');
            contarDezenas(campo);
        }
    });
}

function verificarNumeroDeDezenas(input) {
    const jogo = input.value;
    const dezenas = jogo.split('|').filter(Boolean);
    const isValid = dezenas.length === 10;
    if (!isValid) {
        input.classList.add("error");
    } else {
        input.classList.remove("error");
    }
    return isValid;
}

function verificarDezenasRepetidas(input) {
    const jogo = input.value;
    const dezenas = jogo.split('|').filter(Boolean);
    const uniqueDezenas = new Set(dezenas);
    if (dezenas.length !== uniqueDezenas.size) {
        input.classList.add("error");
        input.parentNode.querySelector(".contador-dezenas").innerHTML = `<span style="color: red;">${dezenas.filter((dezena, index) => dezenas.indexOf(dezena) !== index).join(', ')}</span>`;
        return false;
    } else {
        input.classList.remove("error");
        input.parentNode.querySelector(".contador-dezenas").innerText = `${dezenas.length}/10`;
        return true;
    }
}

function verificarDezenas(input) {
    const jogo = input.value;
    const isValid = verificarDezenasRepetidas(input);
    if (!isValid) {
        return false;
    }
    return verificarNumeroDeDezenas(input);
}

document.getElementById("form").addEventListener("submit", function(event) {
    event.preventDefault();
    exibirConfirmacao();
});

// Adiciona evento de clique para o botão "Sim"
document.querySelector("#confirmacao .sim").addEventListener("click", async function() {
    ocultarConfirmacao();

    var campos = document.querySelectorAll('input[id^="jogo"]');
    var isValid = true;
    campos.forEach(function(campo) {
        if (!verificarNumeroDeDezenas(campo) || !verificarDezenasRepetidas(campo)) {
            isValid = false;
        }
    });

    if (!isValid) {
        alert("Por favor, preencha todos os campos com exatamente 10 dezenas e sem repetições.");
        return;
    }

    var formData = new FormData(document.getElementById("form"));
    var submitButton = document.querySelector("#form .submit");
    var loader = document.querySelector("#form .loader");
    
    loader.style.display = "inline-block";
    submitButton.disabled = true;

    var numerosOrdenados = document.getElementById("jogo").value.split('|').sort().join('|');
    formData.set("jogo", numerosOrdenados);
    
    var cambista = formData.get("nomeApostador");
    var participante = formData.get("participante");
    formData.delete("nomeApostador");
    formData.set("cambista", cambista);
    formData.set("participante", participante);
    
    campos.forEach(function(campo) {
        var numerosOrdenadosCampo = campo.value.split('|').sort().join('|');
        formData.set(campo.name, numerosOrdenadosCampo);
    });

    limparFormulario(); // Limpar o formulário imediatamente e parar a animação do botão

    // Enviar o formulário em segundo plano
    fetch(document.getElementById("form").action, {
        method: "POST",
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error("Erro ao enviar os dados. Por favor, tente novamente.");
        }
    }).catch(error => {
        console.error("Erro ao enviar os dados:", error);
        alert("Erro ao enviar os dados. Por favor, verifique sua conexão com a internet e tente novamente.");
    }).finally(() => {
        loader.style.display = "none";
        submitButton.disabled = false;
    });
});
</script>
</body>
</html>