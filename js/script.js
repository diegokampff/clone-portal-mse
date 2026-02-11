$(function () {
  function showMsg(text, ok) {
    $("#msgCadastro").text(text).css({
      display: "block",
      padding: "10px",
      borderRadius: "6px",
      border: ok ? "1px solid #b7eb8f" : "1px solid #ffccc7",
      background: ok ? "#f6ffed" : "#fff2f0",
      color: ok ? "#135200" : "#a8071a",
      fontSize: "13px",
      textAlign: "center",
      marginBottom: "10px"
    });
  }

  function onlyDigits(v) {
    return (v || "").toString().replace(/\D+/g, "");
  }

  function validarCNPJ(cnpj) {
    cnpj = onlyDigits(cnpj);
    if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;

    let t = cnpj.length - 2;
    let n = cnpj.substring(0, t);
    let d = cnpj.substring(t);
    let s = 0;
    let p = t - 7;

    for (let i = t; i >= 1; i--) {
      s += n.charAt(t - i) * p--;
      if (p < 2) p = 9;
    }

    let r = s % 11 < 2 ? 0 : 11 - (s % 11);
    if (r != d.charAt(0)) return false;

    t++;
    n = cnpj.substring(0, t);
    s = 0;
    p = t - 7;

    for (let i = t; i >= 1; i--) {
      s += n.charAt(t - i) * p--;
      if (p < 2) p = 9;
    }

    r = s % 11 < 2 ? 0 : 11 - (s % 11);
    return r == d.charAt(1);
  }

  const UFs = [
    "AC","AL","AP","AM","BA","CE","DF","ES","GO","MA","MT","MS","MG",
    "PA","PB","PR","PE","PI","RJ","RN","RS","RO","RR","SC","SP","SE","TO"
  ];

  const $pais = $("#pais");
  const $estado = $("#estado");
  const $municipio = $("#municipio");

  function carregarUFs() {
    $estado.html('<option value="">Selecione o Estado</option>');
    UFs.forEach(uf => $estado.append(`<option value="${uf}">${uf}</option>`));
  }

  $estado.prop("disabled", true);
  $municipio.prop("disabled", true);

  $pais.on("change", function () {
    $municipio.html('<option value="">Selecione o Município</option>').prop("disabled", true);
    if ($(this).val() === "Brasil") {
      $estado.prop("disabled", false);
      carregarUFs();
    } else {
      $estado.prop("disabled", true).html('<option value="">Selecione o Estado</option>');
    }
  });

  $estado.on("change", function () {
    $municipio.html('<option value="">Selecione o Município</option>');
    if ($(this).val()) {
      $municipio.prop("disabled", false);
    } else {
      $municipio.prop("disabled", true);
    }
  });

  function setEstadoMunicipio(uf, cidade) {
    if (!uf) return;

    if ($pais.val() !== "Brasil") $pais.val("Brasil").trigger("change");
    if ($estado.prop("disabled")) $estado.prop("disabled", false);

    if ($estado.find(`option[value="${uf}"]`).length === 0) {
      $estado.append(`<option value="${uf}">${uf}</option>`);
    }
    $estado.val(uf);

    if (cidade) {
      $municipio.prop("disabled", false);
      $municipio.html(`<option value="${cidade}">${cidade}</option>`);
      $municipio.val(cidade);
    }
  }

  const ramos = [
    "MAQUINAS LINHA AMARELA, TERRAPLANAGEM E ESCAVAÇÃO, LOCAÇÃO DE",
    "CAMINHÕES PARA TRANSPORTE, LOCAÇÃO DE",
    "FORMAS E LAJES, MONTAGEM DE",
    "VEÍCULOS, LOCAÇÃO DE",
    "GERADORES, LOCAÇÃO DE",
    "COMPRESSOR DE AR, LOCAÇÃO DE",
    "MAQUINAS DE FUNDAÇÃO, LOCAÇÃO DE",
    "ANDAIMES, LOCAÇÃO DE",
    "EQUIPAMENTOS DE ELEVAÇÃO, LOCAÇÃO DE"
  ];

  const selecionados = [];
  const $select = $("#ramoSelect");
  const $lista = $("#ramosSelecionados");
  const $inputRamos = $("#ramosInput");

  if ($select.length) {
    ramos.forEach(r => $select.append(`<option value="${r}">${r}</option>`));
  }

  $("#addRamo").on("click", function () {
    const v = $select.val();
    if (!v || selecionados.includes(v)) return;
    selecionados.push(v);
    renderRamos();
  });

  function renderRamos() {
    $lista.html("");
    selecionados.forEach((r, i) => {
      $lista.append(`<div class="ramo-item"><span>${r}</span><a href="#" data-index="${i}">×</a></div>`);
    });
    $inputRamos.val(selecionados.join(" | "));
  }

  $lista.on("click", "a", function (e) {
    e.preventDefault();
    selecionados.splice($(this).data("index"), 1);
    renderRamos();
  });

  $(".eye").on("click", function () {
    const $i = $(this).siblings("input");
    const $c = $(this).find("i");
    const isPwd = $i.attr("type") === "password";
    $i.attr("type", isPwd ? "text" : "password");
    $c.toggleClass("fa-eye fa-eye-slash");
  });

  const $cnpj = $("#cnpj");
  const $camposEmpresa = $("#razao, #fantasia, #inscricao");

  $camposEmpresa.prop("disabled", true);

  $cnpj.on("input", function () {
    const on = $(this).val().trim().length > 0;
    $camposEmpresa.prop("disabled", !on);
    if (!on) $camposEmpresa.val("");
  });

  $("#cep").on("blur", function () {
    const cep = onlyDigits($(this).val());
    if (!cep) return;

    $.ajax({
      url: "api/buscar_cep.php",
      method: "GET",
      dataType: "json",
      data: { cep },
      success: function (resp) {
        if (!resp.ok) return;

        if (resp.logradouro) $("#rua").val(resp.logradouro);
        if (resp.bairro) $("#bairro").val(resp.bairro);
        if (resp.complemento) $("#complemento").val(resp.complemento);

        setEstadoMunicipio(resp.uf, resp.localidade);
      }
    });
  });

  $("#cnpj").on("blur", function () {
    const cnpj = onlyDigits($(this).val());
    if (!cnpj) return;

    if (!validarCNPJ(cnpj)) {
      showMsg("CNPJ inválido.", false);
      return;
    }

    $.ajax({
      url: "api/buscar_cnpj.php",
      method: "GET",
      dataType: "json",
      data: { cnpj },
      success: function (resp) {
        if (!resp.ok) return;

        if (resp.razao_social) $("#razao").val(resp.razao_social);
        if (resp.nome_fantasia) $("#fantasia").val(resp.nome_fantasia);
        if (resp.descricao_situacao_cadastral) $("#situacao").val(resp.descricao_situacao_cadastral);

        if (resp.cep) $("#cep").val(resp.cep);

        if (resp.logradouro) $("#rua").val(resp.logradouro);
        if (resp.numero) $("#numero").val(resp.numero);
        if (resp.bairro) $("#bairro").val(resp.bairro);
        if (resp.complemento) $("#complemento").val(resp.complemento);

        setEstadoMunicipio(resp.uf, resp.municipio);
      }
    });
  });

  $("#formCadastro").on("submit", function (e) {
    e.preventDefault();

    const cnpj = $cnpj.val().trim();
    const senha = ($('[name="senha"]').val() || "").trim();
    const senha2 = ($('[name="senha_confirmacao"]').val() || "").trim();

    if (!cnpj || !$("#razao").val().trim() || !$("#email").val().trim() || !senha) {
      showMsg("Preencha todos os campos obrigatórios.", false);
      return;
    }

    if (!validarCNPJ(cnpj)) {
      showMsg("CNPJ inválido.", false);
      return;
    }

    if (senha !== senha2) {
      showMsg("As senhas não conferem.", false);
      return;
    }

    $.ajax({
      url: $(this).attr("action"),
      method: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (data) {
        showMsg(data.msg, data.ok);
        if (data.ok) {
          setTimeout(() => window.location.href = "login.php", 1200);
        }
      },
      error: function () {
        showMsg("Erro de comunicação com o servidor.", false);
      }
    });
  });
});
