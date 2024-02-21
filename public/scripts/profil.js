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
    if (icone.innerHTML == 'cancel') location.reload();
    icone.innerHTML = 'cancel';

    var elems = document.forms[0].elements;

    elems['username'].readOnly = !elems['username'].readOnly;
    elems['nom'].readOnly = !elems['nom'].readOnly;
    elems['prenom'].readOnly = !elems['prenom'].readOnly;
    elems['email'].readOnly = !elems['email'].readOnly;

    elems['valider'].style.display = 'block';
}