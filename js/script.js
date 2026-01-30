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

  function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]+/g, '');
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

    let r = s % 11 < 2 ? 0 : 11 - s % 11;
    if (r != d.charAt(0)) return false;

    t++;
    n = cnpj.substring(0, t);
    s = 0;
    p = t - 7;

    for (let i = t; i >= 1; i--) {
      s += n.charAt(t - i) * p--;
      if (p < 2) p = 9;
    }

    r = s % 11 < 2 ? 0 : 11 - s % 11;
    return r == d.charAt(1);
  }

  const $pais = $("#pais");
  const $estado = $("#estado");
  const $municipio = $("#municipio");

  $estado.prop("disabled", true);
  $municipio.prop("disabled", true);

  $pais.on("change", function () {
    $estado.html('<option value="">Selecione o Estado</option>');
    $municipio.html('<option value="">Selecione o Município</option>').prop("disabled", true);

    if ($(this).val() === "Brasil") {
      $estado.prop("disabled", false).append('<option value="PR">Paraná</option>');
    } else {
      $estado.prop("disabled", true);
    }
  });

  $estado.on("change", function () {
    $municipio.html('<option value="">Selecione o Município</option>');

    if ($(this).val() === "PR") {
      $municipio.prop("disabled", false).append('<option value="Londrina">Londrina</option>');
    } else {
      $municipio.prop("disabled", true);
    }
  });

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
  const $input = $("#ramosInput");

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
    $input.val(selecionados.join(" | "));
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

  $("#formCadastro").on("submit", function (e) {
    e.preventDefault();

    const cnpj = $cnpj.val().trim();
    const senha = ($('[name="senha"]').val() || "").trim();
    const senha2 = ($('[name="senha_confirmacao"]').val() || "").trim();

    if (!cnpj || !$("#razao").val().trim() || !$('[name="email"]').val().trim() || !senha) {
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
      success: function (resp) {
        let data;
        try {
          data = typeof resp === "string" ? JSON.parse(resp) : resp;
        } catch {
          showMsg("Resposta inválida do servidor.", false);
          return;
        }

        showMsg(data.msg, data.ok);

        if (data.ok) {
          setTimeout(() => {
            window.location.href = "login.php";
          }, 2000);
        }
      },
      error: function () {
        showMsg("Erro de comunicação com o servidor.", false);
      }
    });
  });

});
