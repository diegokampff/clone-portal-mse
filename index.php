<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Portal MSE - 2026</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="container">

    <div class="header-card">
        Portal MSE - 2026
    </div>

    <div class="card" id="formulario">

        <form class="form-grid" id="formCadastro" action="cadastrar.php" method="POST">

            <div class="tabs full-width">
                <div class="tab active">Pessoa Jurídica</div>
                <div class="tab">Fornecedor</div>
            </div>

            <input type="text" name="cnpj" id="cnpj" placeholder="Digite o CNPJ" required>
            <input type="text" name="razao_social" id="razao" placeholder="Razão Social" required>
            <input type="text" name="nome_fantasia" id="fantasia" placeholder="Nome Fantasia" required>
            <input type="text" name="inscricao_estadual" id="inscricao" placeholder="Inscrição Estadual / Isento" required>

            <input type="text" name="regime_icms" placeholder="ICMS" required>
            <input type="text" name="situacao" placeholder="Situação" required>
            <input type="tel" name="telefone" placeholder="Telefone" required>
            <input type="email" name="email" placeholder="E-mail" required>

            <input type="text" name="rua" placeholder="Rua">
            <input type="text" name="numero" placeholder="Número">
            <input type="text" name="bairro" placeholder="Bairro">
            <input type="text" name="complemento" placeholder="Complemento">

            <select name="pais" id="pais">
                <option value="">Selecione o País</option>
                <option value="Brasil">Brasil</option>
            </select>

            <select name="estado" id="estado" disabled>
                <option value="">Selecione o Estado</option>
            </select>

            <input type="text" name="cep" placeholder="CEP">

            <select name="municipio" id="municipio" disabled>
                <option value="">Selecione o Município</option>
            </select>

            <div class="section full-width">
                <p class="section-title">Fornecedor de:</p>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="fornecedor_de[]" value="Serviços"> Serviços</label>
                    <label><input type="checkbox" name="fornecedor_de[]" value="Materiais"> Materiais</label>
                    <label><input type="checkbox" name="fornecedor_de[]" value="Locação"> Locação</label>
                </div>
            </div>

            <div class="section full-width">
                <p class="section-title">Ramo de Atuação:</p>

                <div class="inline-field">
                    <select id="ramoSelect">
                        <option value="">Selecione um ramo</option>
                    </select>
                    <button type="button" class="add-btn" id="addRamo">+</button>
                </div>

                <div id="ramosSelecionados"></div>

                <input type="hidden" name="ramos" id="ramosInput">
            </div>

            <input type="text" name="cnpj_responsavel" placeholder="CNPJ do Responsável" required>
            <input type="text" name="nome_responsavel" placeholder="Nome do Responsável" required>

            <div class="password-field">
                <input type="password" name="senha" placeholder="Senha" required>
                <span class="eye"><i class="fa-solid fa-eye"></i></span>
            </div>

            <div class="password-field">
                <input type="password" name="senha_confirmacao" placeholder="Repetir Senha" required>
                <span class="eye"><i class="fa-solid fa-eye"></i></span>
            </div>

            <div class="full-width" id="msgCadastro" style="display:none;"></div>

            <div class="form-actions full-width">
                <button type="submit" class="submit-btn">Cadastrar</button>
                <p class="login-text">
                    Já tem uma conta? <a href="login.php">Faça login</a>
                </p>
            </div>

        </form>

    </div>

    <div class="info-card">
        <div class="icon">🏭</div>
        <p class="info-title">Para ser um Fornecedor MSE</p>
        <p class="info-subtitle">Cadastre-se aqui</p>
        <a href="#formulario" class="submit-btn">Cadastrar</a>
    </div>

    <div class="info-card">
        <div class="icon">👤</div>
        <p class="info-title">Você é colaborador da MSE?</p>
        <button class="google-login-btn" type="button">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" alt="Google">
            Fazer Login com o Google
        </button>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
const paisSelect = document.getElementById("pais");
const estadoSelect = document.getElementById("estado");
const municipioSelect = document.getElementById("municipio");

paisSelect.addEventListener("change", () => {
    estadoSelect.innerHTML = '<option value="">Selecione o Estado</option>';
    municipioSelect.innerHTML = '<option value="">Selecione o Município</option>';
    municipioSelect.disabled = true;

    if (paisSelect.value === "Brasil") {
        estadoSelect.disabled = false;
        estadoSelect.innerHTML += '<option value="PR">Paraná</option>';
    } else {
        estadoSelect.disabled = true;
    }
});

estadoSelect.addEventListener("change", () => {
    municipioSelect.innerHTML = '<option value="">Selecione o Município</option>';

    if (estadoSelect.value === "PR") {
        municipioSelect.disabled = false;
        municipioSelect.innerHTML += '<option value="Londrina">Londrina</option>';
    } else {
        municipioSelect.disabled = true;
    }
});
</script>

<script src="js/script.js"></script>
</body>
</html>
