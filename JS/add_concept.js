// Variables globales

var count_comp = 0;
var coords = [];
var div = document.getElementById("image");
const img = document.getElementById("imported");

// Fonction de clic, permettant d'ajouter la zone accompagné de leur informations telles que
// Le numéro
// La coordonnée en abscisse
// La coordonnée en ordonnée
function clickOnImage(e, title) {
    count_comp += 1;
    var x = e.x - 25 + window.scrollX;
    var y = e.y - 25 + window.scrollY;
    coords.push([count_comp, x, y]);
    
    updateImage(title);
    // console.log(coords);
}

// Fonction permettant de mettre à jour l'affichage
// Ajout de la zone sur l'image
// Ajout du champ associé à la zone
function updateImage(title) {
    var i = coords[coords.length - 1][0];
    var x = coords[coords.length - 1][1];
    var y = coords[coords.length - 1][2];
    addArea(x, y, i);
    addInput(i, title);
}

function removeArea(i) {
    if (i > 0) {
        var area = document.getElementById("A" + i)
        document.getElementById('areas').removeChild(area);
    }
}

function addArea(x, y, i) {
    var area = document.createElement('div');
    area.setAttribute("id", "A" + i);
    area.setAttribute("style", "top: " + y + "px; left: " + x + "px;");
    area.classList.add('area');
    area.innerHTML = i;
    document.getElementById("areas").appendChild(area);
    // div.parentElement.appendChild(area);
}

function removeInput(i) {
    if (i > 0) {
        removeArea(i);
        var input = document.getElementById("I" + i);
        document.getElementById('input_components').removeChild(input);
        removeCoords(i);
    }
}

function addInput(i, title) {
    var input = document.createElement('div');
    input.setAttribute("id", "I" + i);
    input.classList.add('input');

    const id = "Composant" + i;
    var sub_input = document.createElement('div');
    // Label
    var label = document.createElement('label');
    label.innerHTML = "N °" + i;
    label.setAttribute("for", id);

    // Ajout du champ texte
    var input_comp = document.createElement('input');
    input_comp.setAttribute("type", "text");
    input_comp.setAttribute("value", title + " " + i);
    input_comp.setAttribute("required", "true");
    input_comp.setAttribute("id", id);
    input_comp.setAttribute("name", id);
    sub_input.appendChild(label);
    sub_input.appendChild(input_comp);

    // Ajout du div comprenant le label et le champ texte dans le conteneur input
    input.appendChild(sub_input);

    // Ajout le bouton de suppression
    var btn_remove = document.createElement('div');

    var icon_remove = document.createElement('img');
    btn_remove.setAttribute("onclick", "removeInput(" + i + ")");
    icon_remove.src = "../../assets/icons/remove.png";
    icon_remove.alt = "Icone de suppression";
    icon_remove.style = "height: 50px; width: 50px";
    btn_remove.classList.add("remove");
    btn_remove.appendChild(icon_remove);

    // Ajout du bouton de suppression dans le conteneur input
    input.appendChild(btn_remove);
    
    // Ajout du conteneur input sur la page
    document.getElementById("input_components").appendChild(input);
}

function removeCoords(i) {
    for (index = 0; index < coords.length; index++) {
        if (coords[index][0] == i) {
            coords.splice(index, 1);
            return;
        }
    }
}

function validate(member, lang, name) {
    const title = document.getElementById("title").value;
    if (coords.length == 0) {
        alert("Aucune zone n'a été sélectionné");
        return;
    }

    if (title.length == 0) {
        alert("Le titre n'a pas été saisi");
        return;
    }

    // console.log(coords);
    
    var components = "";
    // console.log(coords);
    for (index = 0; index < coords.length; index++) {
        // console.log(coords[index]);
        var id = coords[index][0];
        var term = document.querySelector("#I" + id + " div input").value;
        var info_component = coords[index][1] + "_" +
            coords[index][2] + "_" + term;
        components += info_component + "|";
        // console.log(info_component);

    }

    window.location.href = "add.php?state=concept&member=" + member +
        "&lang=" + lang + "&name=" + name + "&title=" +
        title + "&comps=" + components + "&count=" + coords.length;
}
