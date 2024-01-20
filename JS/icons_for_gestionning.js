function removeUser(id) {
    window.location.href = "../auth/remove.php?member_id=" + id;
}

function removeConcept(id) {
    window.location.href = "../concept/rem.php?state=concept&concept=" + id;
}

function removeComponent(comp, concept) {
    window.location.href = "../concept/rem.php?state=component&comp=" + comp +
        "&concept=" + concept;
}

function removeTranslate(concept, lang) {
    window.location.href = "../concept/rem.php?state=translate&concept=" + concept +
        "&lang=" + lang;
}

function removeField(field) {
    window.location.href = "../concept/rem.php?state=field&field=" + field;
}

function modifyPassword(member) {
    window.location.href = "user.php?modify=" + member;
}

function removeLang(id) {
    window.location.href = "../concept/rem.php?state=lang&lang=" + id;
}

function dissociateConceptOfField(concept, field) {
    window.location.href = "../concept/rem.php?state=dissociate&concept=" + concept + "&field=" + field;
}
