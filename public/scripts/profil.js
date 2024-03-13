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

function closeDialogMDP() {
    document.getElementById('mdpDialog').close();
}

function closeDialogDesabo() {
    document.getElementById('desaboDialog').close();
}

function openDialogMDP() {
	aCacher = document.querySelectorAll('.cache');
	for (var i = 0; i < aCacher.length; i++) {
		aCacher[i].style.display = 'none';
	}
	document.getElementById('motDePasseActuel').value = "";
    document.getElementById('nouveauMotDePasse').value = "";
    document.getElementById('confirmNouveauMDP').value = "";
    document.getElementById('mdpDialog').showModal();
}

function openDialogDesabo() {
	aCacher = document.querySelectorAll('.cache');
	for (var i = 0; i < aCacher.length; i++) {
		aCacher[i].style.display = 'none';
	}
    document.getElementById('desaboDialog').showModal();
}

function activeModeSommeil(){
    console.log("sleepmode activé");
    var xhr = new XMLHttpRequest();
    var data = new FormData();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText != "OK") {
                document.getElementById(xhr.responseText + "Erreur").style.display = 'block';
				setTimeout(function () {
				    document.getElementById(xhr.responseText + "Erreur").style.display = 'none';
				}, 4000);
            }else{
                console.log(xhr.responseText);
                location.reload();
            }
        }
    };
    xhr.open('POST', '/ajax/activeSleepMode', true);
    xhr.send(data);
}

function submitMotDePasseForm(event) {
    console.log("Édition en cours...");

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

function desabonner() {
    var conf = confirm("Voulez vous vraiment vous désabonner ? (Vous ne perdrez pas votre abonnement actuel)");
    if (conf) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                location.reload();
            }
        };
        xhr.open('POST', '/ajax/desabo_form', true);
        xhr.send();
    }
    
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
function openAnnonceDialog(prixActuel, titreActuel, descriptionActuelle, id, type, categorie, duree, datePoncService, dateReccu_Debut, dateReccu_Fin, dateReccu_Type) {
    document.getElementById('editPrix').value = prixActuel;
    document.getElementById('editTitre').value = titreActuel;
    document.getElementById('editDescription').value = descriptionActuelle;    
    document.getElementById('editAnnonceDialog').setAttribute('data-id', id);
    document.getElementById('editAnnonceDialog').setAttribute('data-type', type);
    

    if (type == "Materiel")
    {
        document.getElementById('editCategorieMat').value = categorie;
        var result = duree.match(/^(\d+)\s*(.*)$/);
        document.getElementById('editDureeNombre').value = result[1];
        document.getElementById('editDureePeriode').value = result[2];
        
        //Affiche le bloc Matériel et cache le bloc service
        document.getElementById("blocMateriel").style.display = "block";
        document.getElementById("blocService").style.display = "none";
        document.getElementById("editCategorieService").required = false;
        document.getElementById("date_pret").required = false;
        
    } else if (type == "Service") {
        var datePoncts = JSON.parse(datePoncService);
        var dateR_deb = JSON.parse(dateReccu_Debut);
        var dateR_fin = JSON.parse(dateReccu_Fin);
        var dateR_type = JSON.parse(dateReccu_Type);

        var inputs = document.getElementsByName('additional_date[]');
        var inputs_reccu = document.getElementsByName('additional_recurrence[]');
        var inputs_ends = document.getElementsByName('additional_ends[]');

        // Ajout des dates optionnelss pour les date ponctuelles
        if (datePoncService != null) {
            for (var i = 1; i < datePoncts.length; i++) {
                document.getElementById('addDateButton').click();
                inputs[i - 1].value = datePoncts[i]; // Assigner chaque date à l'entrée correspondante
                console.log(datePoncts[i]);
            }
        }
        // Ajout des dates optionnels pour les date reccurente
        if (dateReccu_Debut != null) {
            for (var i = 1; i < dateR_deb.length; i++) {
                //Rajoute une date
                document.getElementById('addDateButton').click();
                //Date début
                inputs[i - 1].value = dateR_deb[i]; 
                //Permet de mettre le bon type
                inputs_reccu[i - 1].value = dateR_type[i];
                var event = new Event('change', { bubbles: true, cancelable: true });
                inputs_reccu[i - 1].dispatchEvent(event);
                //Date fin
                inputs_ends[i +2].value = dateR_fin[i]; 
                console.log('Date début : ' + dateR_deb[i] + ', Date fin : ' + dateR_fin[i] + ',type : ' + dateR_type[i]);

            }
        }
        
        document.getElementById('editCategorieService').value = categorie;
        if (datePoncService != null) {
            document.getElementById('date_pret').value = datePoncts[0];
        } else {
            document.getElementById('date_pret').value = dateR_deb[0];
            document.getElementById('recurrence').value = dateR_type[0];
            var event = new Event('change', { bubbles: true, cancelable: true });
            document.getElementById('recurrence').dispatchEvent(event);
            inputs_ends[2].value = dateR_fin[0]; 
        }
        // l'inverse
        document.getElementById("blocMateriel").style.display = "none";
        document.getElementById("blocService").style.display = "block";
        document.getElementById("editDureeNombre").required = false;
        document.getElementById("editCategorieMat").required = false;
    }
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
    if (annonceType == "Materiel") {
        data.append('nouvelleCategorie', document.getElementById('editCategorieMat').value);
        data.append('nouvelleDureeValeur', document.getElementById('editDureeNombre').value);
        data.append('nouvelleDureePeriode', document.getElementById('editDureePeriode').value);
        console.log(document.getElementById('editDureeNombre').value);
        console.log(document.getElementById('editDureePeriode').value);
        console.log(document.getElementById('editCategorieMat').value);
    } else if (annonceType == "Service") {
        data.append('nouvelleCategorie', document.getElementById('editCategorieService').value);
        let additionalDatesValues = [];

        let additionalDates = document.getElementsByName('additional_date[]');
        for (let i = 0; i < additionalDates.length; i++) {
            data.append(`additional_date[${i}]`, additionalDates[i].value);
        }

        let additionalRecurrences = document.getElementsByName('additional_recurrence[]');
        for (let i = 0; i < additionalRecurrences.length; i++) {
            data.append(`additional_recurrence[${i}]`, additionalRecurrences[i].value);
        }
        let additionalEnds = document.getElementsByName('additional_ends[]');
        for (let i = 0; i < additionalEnds.length; i++) {
            data.append(`additional_ends[${i}]`, additionalEnds[i].value);
        }

        data.append('date_pret', document.getElementById('date_pret').value);
        data.append('recurrence', document.getElementById('recurrence').value);
    }

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
function confirmerSuppression(event,id) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette annonce ?")) {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('annonceId', id);
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

/*Suppresion d'une transaction */
function supprimerTransaction(event, id) {
    if (confirm("Êtes-vous sûr de vouloir annuler cette transaction ?")) {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('transactionId', id);
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
        xhr.open('POST', '/ajax/suppr_transaction', true);
        xhr.send(data);
    }
}
/*Se désister d'une file d'attente */
function seDesister(event, id) {
    if (confirm("Êtes-vous sûr de vouloir vous désister ?")) {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('annonceId', id);
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
        xhr.open('POST', '/ajax/se_desister', true);
        xhr.send(data);
    }
}
/*Posteur annule une transaction avec un client  */
function annulerTransactionAvecClient(event, id) {
    if (confirm("Êtes-vous sûr de vouloir annuler cette transaction ?")) {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('transactionId', id);
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
        xhr.open('POST', '/ajax/annul_transaction', true);
        xhr.send(data);
    }
}