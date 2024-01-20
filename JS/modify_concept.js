function changeTerminology(id) {
    const comps = document.querySelectorAll("div.comp");
    var changing = "";

    for (var i = 0; i < comps.length; i++) { 
        var composant_id = comps[i].getAttribute("id");
        var terminology = (document.querySelector("div#" + composant_id + " > h3 > span")).innerHTML;
        changing += composant_id + "_" + terminology + "|";
    }

    window.location.href = "modify_concept.php?id=" + id + "&update=" + changing;
}

function addTranslateInBDD(concept, lang) {
    const comps = document.querySelectorAll("span.edit");
    var changing = "";

    for (var i = 0; i < comps.length; i++) { 
        var composant_id = comps[i].getAttribute("id");
        var terminology = comps[i].innerHTML;
        changing += composant_id + "_" + terminology + "|";
    }

    window.location.href = "../concept/add.php?state=translate&concept=" + concept +
        "&lang=" + lang + "&update=" + changing;
}

function resetConcept(id) {
    window.location.href = "../concept/view.php?id=" + id;
}
