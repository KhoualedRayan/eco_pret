function openTab(evt, tabName) {
    var i, tabcontent, tablinks;

    // Masquer tous les onglets
    tabcontent = document.getElementsByClassName("tab");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Désactiver tous les boutons d'onglet
    tablinks = document.getElementsByClassName("tab-button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Afficher l'onglet actuel et activer le bouton correspondant
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

function edit(icone) {
    icone.innerHTML = icone.innerHTML == 'cancel' ? 'edit_square' : 'cancel';

    var elems = document.forms[0].elements;

    elems['username'].readOnly = !elems['username'].readOnly;
    elems['nom'].readOnly = !elems['nom'].readOnly;
    elems['prenom'].readOnly = !elems['prenom'].readOnly;
    elems['email'].readOnly = !elems['email'].readOnly;

    var abo = elems['options'];
    for (var i = 0; i < abo.length; i++) {
        abo[i].disabled = !abo[i].disabled;
    }

    elems['valider'].style.display = icone.innerHTML == 'cancel' ? 'block' : 'none';
}

function clickAbo(clique, actuel) {
    document.getElementById('changeAbo').disabled = clique == actuel;
}

function changeAbo() {

}