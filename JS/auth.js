// Comportement patterns

// Les vérifications

function isValidName(name) {
    var count_separator = name.split("-").length - 1;
    
    if (count_separator > 1) {
        return false;
    }
    // console.log(name.indexOf("-"));
    // console.log(name.length - (name.indexOf("-") + 1));
    if (count_separator == 1 && ((name.indexOf("-") < 3) || (name.length - (name.indexOf("-") + 1) < 3))) {
        return false;
    }

    if (count_separator == 0 && name.length < 3) {
        return false;
    }

    var separator = count_separator != 0 ? name.indexOf("-") : name.length;

    if (!(name.charCodeAt(0) >= 65 && name.charCodeAt(0) < 91)) {
        return false;
    }
    for (var i = 1; i < separator; i++) {
        var codeLetter = name.charCodeAt(i);
        if (!(codeLetter >= 97 && codeLetter < 123)) {
            return false;
        }
    }

    if (separator != name.length) {
        if (!(name.charCodeAt(separator + 1) >= 65 && name.charCodeAt(separator + 1) < 91)) {
            return false;
        }
        for (var i = separator + 2; i < name.length; i++) {
            var codeLetter = name.charCodeAt(i);
            if (!(codeLetter >= 97 && codeLetter < 123)) {
                return false;
            }
        }
    }

    return true;
}

function isValidEmail(mail) {
    var regex = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))/i);
    return regex.test(mail);
}

function isValidPseudo(pseudo) {
    var respectLen = (pseudo.length >= 4);
    var hereArobase = (pseudo.charAt(0) == "@");
    var count = 0;
    for (i = 1; i < pseudo.length; i++) {
        var codeLetter = pseudo.charCodeAt(i);
        var char = pseudo.charAt(i);
        var letter = (codeLetter >= 65 && codeLetter < 91) || (codeLetter >= 97 && codeLetter < 123);
        var number = (char >= '0' && char <= '9');
        var specials = ["-", "_", "\."].includes(char);
        if (letter || number || specials) {
            count = count + 1;
        }
    }
    // console.log("Test");
    // console.log(respectLen);
    // console.log(hereArobase);
    // console.log(pseudo.length - 1 == count);
    return hereArobase && respectLen && (pseudo.length - 1 == count);
}

function isValidPass(pass) {
    if (pass.length < 8) {
        return false;
    }
    var count = 0;
    for (i = 0; i < pass.length; i++) {
        var codeLetter = pass.charCodeAt(i);
        var char = pass.charAt(i);
        var letter = (codeLetter >= 65 && codeLetter < 91) || (codeLetter >= 97 && codeLetter < 123);
        var number = (char >= '0' && char <= '9');
        var specials = ["@", "-", "_", "\."].includes(char);
        if (letter || number || specials) {
            count = count + 1;
        }
    }
    return count == pass.length;
}

function isValidPassConfirm(pass) {
    if (!isValidPass(pass)) {
        return false;
    }

    return inputPass.value.trim() === pass;
}

// Les évènements

function checkName() {
    var error = document.getElementById("errorFirstName");
    inputName.addEventListener("input", function() {
        if (!isValidName(inputName.value.trim())) {
            updateError(inputName, error, "Le prénom est invalide");
        } else {
            updateSuccess(inputName, error);
        }
    }, false);
}

function checkLastName() {
    var error = document.getElementById("errorLastName");
    inputLastname.addEventListener("input", function() {
        if (!isValidName(inputLastname.value.trim())) {
            updateError(inputLastname, error, "Le nom de famille est invalide");
        } else {
            updateSuccess(inputLastname, error);
        }
    }, false);
}

function checkMail() {
    var error = document.getElementById("errorMail");
    inputMail.addEventListener("input", function() {
        if (!isValidEmail(inputMail.value.trim())) {
            updateError(inputMail, error, "L'adresse mail est invalide");
        } else {
            updateSuccess(inputMail, error);
        }
    }, false);
}

function checkPseudo() {
    var error = document.getElementById("errorPseudo");
    inputPseudo.addEventListener("input", function() {
        if (!isValidPseudo(inputPseudo.value.trim())) {
            updateError(inputPseudo, error, "Le pseudo est invalide");
        } else {
            updateSuccess(inputPseudo, error);
        }
    }, false);
}

function checkPassword() {
    var error = document.getElementById("errorPassword");
    inputPass.addEventListener("input", function() {
        if (inputPseudo.value.length != 0 && inputPseudo.value == "@root") {
            updateSuccess(inputPass, error);
        } else {
            if (!isValidPass(inputPass.value.trim())) {
                updateError(inputPass, error, "Le mot de passe est invalide");
            } else {
                updateSuccess(inputPass, error);
            }
        }
    }, false);
}

function checkPasswordConfirm() {
    var error = document.getElementById("errorPasswordConfirm");
    inputPassConfirm.addEventListener("input", function() {
        if (!isValidPassConfirm(inputPassConfirm.value.trim())) {
            updateError(inputPassConfirm, error, "La confirmation du mot de passe est invalide");
        } else {
            updateSuccess(inputPassConfirm, error);
        }
    }, false);
}

// La gestion d'erreur et de succès

function updateError(input, error, message) {
    input.classList.add("errorInput");
    input.classList.remove("successInput");
    error.innerHTML = message;
}

function updateSuccess(input, error) {
    input.classList.add("successInput");
    input.classList.remove("errorInput");
    error.innerHTML = "";
}
