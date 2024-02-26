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
    if (icone.innerHTML == 'cancel')
        location.reload();
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

function closeDialog() {
    document.getElementById('mdpDialog').close();
}
function openDialog() {
	aCacher = document.querySelectorAll('.cache');
	for (var i = 0; i < aCacher.length; i++) {
		aCacher[i].style.display = 'none';
	}
	document.getElementById('motDePasseActuel').value = "";
    document.getElementById('nouveauMotDePasse').value = "";
    document.getElementById('confirmNouveauMDP').value = "";
    document.getElementById('mdpDialog').showModal();
}

function submitMotDePasseForm(event) {
	event.preventDefault();
	var xhr = new XMLHttpRequest();

	var data = new FormData();
    data.append('motDePasseActuel', document.getElementById('motDePasseActuel').value);
    data.append('nouveauMotDePasse', document.getElementById('nouveauMotDePasse').value);
    data.append('confirmNouveauMDP', document.getElementById('confirmNouveauMDP').value);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText != "OK") {
                document.getElementById(xhr.responseText + "Erreur").style.display = 'block';
				setTimeout(function () {
				    document.getElementById(xhr.responseText + "Erreur").style.display = 'none';
				}, 4000);
            }
        }
    };

    xhr.open('POST', '/ajax/mdpForm', true);
    xhr.send(data);
}

function toggleVisiblite(id, elem) {
	if (elem.innerHTML == 'visibility') {
		document.getElementById(id).type = "text";
		elem.innerHTML = 'visibility_off';
	} else {
		document.getElementById(id).type = "password";
		elem.innerHTML = 'visibility';
	}
}
/*Modifcation d'une annonce avec une boite de dialogue */
function openAnnonceDialog(prixActuel, titreActuel, descriptionActuelle,id,type) {
    document.getElementById('editPrix').value = prixActuel;
    document.getElementById('editTitre').value = titreActuel;
    document.getElementById('editDescription').value = descriptionActuelle;
    document.getElementById('editAnnonceDialog').setAttribute('data-id', id);
    document.getElementById('editAnnonceDialog').setAttribute('data-type', type);
    document.getElementById('editAnnonceDialog').showModal();
}

function closeAnnonceDialog() {
    document.getElementById('editAnnonceDialog').close();
}

function submitAnnonceForm(event) {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    var annonceId = document.getElementById('editAnnonceDialog').getAttribute('data-id');
    var annonceType = document.getElementById('editAnnonceDialog').getAttribute('data-type');
    var data = new FormData();

    data.append('nouveauPrix', document.getElementById('editPrix').value);
    data.append('nouveauTitre', document.getElementById('editTitre').value);
    data.append('nouvelleDescription', document.getElementById('editDescription').value);
    data.append('annonceId', annonceId);
    data.append('annonceType', annonceType);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText != "OK") {
                //Erreur
                console.log(xhr.responseText)
            }
            else {
                //Annonce modifiée avec succès :D
                console.log(xhr.responseText);
                location.reload();
            }
        }
    };
    xhr.open('POST', '/ajax/modif_annonce', true);
    xhr.send(data);
}

/*Suppresion d'une annonce */
function confirmerSuppression(event,id,type) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette annonce ?")) {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('annonceId', id);
        data.append('annonceType', type);
        console.log("Type : " + type + ", Id : " + id);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText != "OK") {
                    //Erreur
                    console.log(xhr.responseText)
                }
                else {
                    //Annonce supprimé avec succès :D
                    console.log(xhr.responseText);
                    location.reload();
                }
            }
        };
        xhr.open('POST', '/ajax/suppr_annonce', true);
        xhr.send(data);
    }
}