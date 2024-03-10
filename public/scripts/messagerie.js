var nomDuDestinataire = '';
var idDeLaTransaction = '';
document.addEventListener('DOMContentLoaded', function () {
    const destinataires = document.querySelectorAll('.colonne-gauche .destinataire');
    const bouton = document.querySelector('.section-haut button');
    const titreH2 = document.querySelector('.section-haut h2');
    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', nouveauMessage);
    }
    destinataires.forEach(function (destinataire) {
        destinataire.addEventListener('click', function () {
            // Trouver l'élément actuellement sélectionné et le désélectionner
            const selectionne = document.querySelector('.colonne-gauche .destinataire.selectionne');
            if (selectionne) {
                selectionne.classList.remove('selectionne');
            }

            // Ajouter la classe 'selectionne' à l'élément cliqué
            destinataire.classList.add('selectionne');
            const transactionId = destinataire.id;
            idDeLaTransaction = transactionId;
            console.log("ID de la transaction sélectionnée :", transactionId);

            // Mettre à jour le contenu de la section haut
            const nomDestinataire = destinataire.querySelector('.nom').textContent;
            nomDuDestinataire = nomDestinataire;
            const sectionHautH2 = document.querySelector('.colonne-droite .section-haut h2');
            if (sectionHautH2) {
                sectionHautH2.textContent = nomDestinataire;
            }
            bouton.classList.remove('cache');
            titreH2.classList.remove('cache');

            // Mettre à jour le contenu de la section bas
            fetch(`/charger-messages/${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.section-bas').innerHTML = data.html;
                })
                .catch(error => console.error('Erreur:', error));
        });
    });
});
function openNouveauMessageDialog() {
    document.getElementById('destinataire').value = nomDuDestinataire;

    document.getElementById('nouveauMessageDialog').showModal();
}

function closeNouveauMessageDialog() {
    document.getElementById('nouveauMessageDialog').close();
}
function nouveauMessage(event) {
    event.preventDefault();
    console.log("Envoie de nouveau message en cours...");

    var xhr = new XMLHttpRequest();

    var data = new FormData();
    data.append('destinataire', document.getElementById('destinataire').value);
    data.append('message', document.getElementById('nouveauMessage').value);
    data.append('transactionId', idDeLaTransaction);
    console.log("Destinataire : " + document.getElementById('destinataire').value);
    console.log("Message : " + document.getElementById('nouveauMessage').value);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText != "OK") {
                console.log(xhr.responseText)
            }
            else {
                console.log('message envoyé');
                location.reload();
            }
        }
    };

    xhr.open('POST', '/ajax/nouveau_message', true);
    xhr.send(data);
}