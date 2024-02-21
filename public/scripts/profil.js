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
            if (xhr.responseText == "OK") {
            	document.getElementById('messageSucces').style.display = "block";
            	setTimeout(function () {
				    location.reload();
				}, 500);
            	
            } else {
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