var list = [-1];

class Nome {
  constructor(valore, lingua, id) {
    this.valore = valore;

    this.lingua = lingua;

    this.id = id;
  }
}

function isIn(lista, id) {
  temp = false;

  for (i = 0; i < lista.length; i++) {
    if (lista[i] == id) {
      temp = true;
    }
  }

  return temp;
}

function nomiSort(a, b) {
  if (a.valore > b.valore) {
    return 1;
  } else {
    return -1;
  }
}

function getMiPiace(id) {
  if (id != "" && !isIn(list, id)) {
    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        var reponse = request.responseText;

        result = JSON.parse(reponse);

        finale = "<p>";

        console.log(result);

        var totale = 0;

        if (result.likes.length == 0) {
          finale += "Non ci sono ancora valutazioni per questo nome! :/";
        } else {
          for (like of result.likes) {
            totale += parseInt(like.value);
          }
          finale +=
            "La media delle valutazioni è di: " +
            totale / result.likes.length +
            "/5";
        }
        finale += "</p>";
        document.getElementById(id).innerHTML = finale;
      }
    };

    document.getElementById(id).innerHTML = "<p>In attesa dei dati...</p>";

    request.open("GET", "/api/getLikes?idNome=" + id, true);

    request.send();
  }
}

function aggiornaNomi() {
  str = document.getElementById("nome").value;

  var lingue_sel_arr = $("select#lingua").val();

  var lingue_sel = "";

  for (i = 0; i < lingue_sel_arr.length; i++) {
    lingue_sel += lingue_sel_arr[i] + ",";
  }

  if (str.length == 0) {
  } else {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        var reponse = xmlhttp.responseText;

        nomi = JSON.parse(reponse);

        var arrayNomi = new Array();

        finale = "";

        nomi = nomi.nomi;

        if (nomi.length == 0) {
          finale +=
            '<ul class="collection"><li class="collection-item">Nessun nome trovato!</li></ul>';
        } else {
          finale +=
            '<ul class="collection"><li class=" collection-item">Sono stati trovati ' +
            nomi.length +
            " risultati.</li></ul>";

          finale += '<ul class="collapsible popout">';
        }

        for (i = 0; i < nomi.length; i++) {
          nome = nomi[i].nome;
          valore = nome.value;
          lingua = nome.language;
          id = nome.id;
          valore = valore.charAt(0).toUpperCase() + valore.slice(1);
          arrayNomi.push(new Nome(valore, lingua, id));
        }

        if (arrayNomi.length < 1500) {
          arrayNomi.sort(nomiSort);
        }

        arrayNomi.forEach((nome) => {
          finale +=
            "<li onClick=(getMiPiace(" +
            nome.id +
            '))><div class="collapsible-header">' +
            nome.valore +
            '<span class="badge">' +
            nome.lingua +
            '</span></div></div><div class="collapsible-body"><span id=' +
            nome.id +
            "></span></div></li>";
        });

        finale += "</ul>";

        document.getElementById("nomi").innerHTML = finale;

        $(document).ready(function () {
          $(".collapsible").collapsible();
        });
      }
    };

    xmlhttp.open(
      "GET",

      "/api/getNames?name=" + str + "&language=" + lingue_sel,

      true
    );

    xmlhttp.send();

    document.getElementById("nomi").innerHTML = "";
  }
}

$(document).ready(function () {
  $("select").formSelect();
});

$(document).on("change", "input.select-dropdown", function () {
  console.log($(this).val());
});

$(".dropdown-trigger").dropdown();

$(document).ready(function () {
  $(".collapsible").collapsible();
});

var $select = $("select");

$select.on("change", function () {
  aggiornaNomi();
});
