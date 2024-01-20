function addFavorite(member, concept, dest) {
    window.location.href = "../concept/add.php?state=favorite&member=" +
        member + "&concept=" + concept + "&dest=" + dest;
}

function removeFavorite(member, concept, dest) {
    window.location.href = "../concept/rem.php?state=favorite&member=" +
        member + "&concept=" + concept + "&dest=" + dest;
}

function viewMore(id) {
    window.location.href = "../concept/view.php?id=" + id;
}

function editConcept(id) {
    window.location.href = "../concept/modify_concept.php?id=" + id;
}

function addTranslate(id) {
    window.location.href = "../concept/add_translate.php?id=" + id;
}
