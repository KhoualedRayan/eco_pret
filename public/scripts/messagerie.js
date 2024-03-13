var idDeLaTransaction = null;
// au lieu de faire un addEventListener, on peut tout simplement créer une fonction
// qui prend en paramètre le nom de l'interlocuteur au lieu de la fouiller dans le html
function updateDiscussion(dest, interlocuteur) {
    // Trouver l'�l�ment actuellement s�lectionn� et le d�s�lectionner
    const selectionne = document.querySelector('.colonne-gauche .destinataire.selectionne');
    const titreH2 = document.querySelector('.section-haut h3');
    if (selectionne) {
        selectionne.classList.remove('selectionne');
    }
    // Ajouter la classe 'selectionne' � l'�l�ment cliqu�
    dest.classList.add('selectionne');
    const transactionId = dest.id;
    idDeLaTransaction = transactionId;
    //console.log("ID de la transaction s�lectionn�e :", transactionId);
    
    const sectionHautH2 = document.querySelector('.colonne-droite .section-haut h3');
    if (sectionHautH2) {
        sectionHautH2.textContent = interlocuteur;
    }
    titreH2.classList.remove('cache');
    document.getElementById('actions').classList.remove('cache');

    // change le bouton de validation selon le statut

    // Mettre à jour le contenu de la section bas
    // + la barre de scroll, à ne pas assimiler avec refreshMessages car cela reseterait la barre de scroll toutes les 2 secondes
    fetch(`/charger-messages/${idDeLaTransaction}`)
        .then(response => response.json())
        .then(data => {
            var zoneScroll = document.querySelector(".section-bas");
            zoneScroll.innerHTML = data.html;
            zoneScroll.scrollTop = zoneScroll.scrollHeight; // Défilement vers le bas dès le début
            if (data.statut.includes('-')) {
                var statut = data.statut.split("-")[1];
                if (statut == data.userRole) {
                    document.getElementById('validerBouton').innerHTML = "<span class='material-icons'>done</span> (1/2)";
                }
            } else if (data.statut == "Valide") {
                document.getElementById('validerBouton').innerHTML = "done_all";
                document.getElementById('validerBouton').classList.add("material-icons");
            }
            
        })
        .catch(error => console.error('Erreur:', error));
}

function refreshMessages() {
    // Mettre � jour le contenu de la section bas
    fetch(`/charger-messages/${idDeLaTransaction}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('.section-bas').innerHTML = data.html;
        })
        .catch(error => console.error('Erreur:', error));
}

function nouveauMessage(expediteur, text) {

    if (idDeLaTransaction == null) {
        return;
    }

    var xhr = new XMLHttpRequest();

    var data = new FormData();
    data.append('expediteur', expediteur);
    data.append('message', text);
    data.append('transactionId', idDeLaTransaction);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText != "OK") {
                console.log(xhr.responseText)
            }
            else {
                refreshMessages();
            }
        }
    };

    xhr.open('POST', '/ajax/nouveau_message', true);
    xhr.send(data);
}

// refresh automatique de la page tt les 5secs
function refresh() {
    //console.log("refresh");
    fetch('/messagerie_refresh')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.colonne-gauche').innerHTML = data.html;
            if (idDeLaTransaction) {
                const destinataire = document.getElementById(idDeLaTransaction);
                destinataire.classList.add('selectionne');
                refreshMessages();
            }
            
        })
        .catch(error => console.error('Erreur:', error));
}
const intervalId = setInterval(refresh, 2000);

function ajusterTaille() {
    var textarea = document.getElementById('input');
    textarea.style.height = 'auto'; // Réinitialiser la hauteur
    textarea.style.height = textarea.scrollHeight + 'px'; // Ajuster la hauteur en fonction de la hauteur du contenu
}


function send(expediteur) {
    var text = document.getElementById('input').value.trim();
    if (text.length > 0 ) {
        document.getElementById('input').value = '';
        ajusterTaille();
        nouveauMessage(expediteur, text);
    }
}

function entree(event, expediteur) {
    if (event.keyCode === 13 && !event.shiftKey) {
        event.preventDefault();
        send(expediteur);        
    }
}

function valider() {
    if (idDeLaTransaction == null) {
        alert("Something went wrong.");
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText != "ERROR") {
                document.getElementById("valider").innerHTML = xhr.responseText;
                document.getElementById("valider").showModal();
            }
                
        }
    };
    xhr.open('POST', '/ajax/validation/'+idDeLaTransaction, true);
    xhr.send();
}

function closeValidDialog() {
    document.getElementById("valider").close();
}
