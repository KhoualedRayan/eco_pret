
/*Se désister d'une file d'attente */
function validerNotePosteur(username, id) {
    let nbEtoiles = 0;
    let allStars = document.querySelectorAll('.star');
    allStars.forEach((star, i) => {
        if (star.textContent == '★') {
            nbEtoiles++;
        } 
    });
    console.log("Note donnée : ", nbEtoiles);
    if (nbEtoiles == 0) {
        alert("Veuillez mettre un nombre d'étoiles !");
    } else {
        var conf = confirm("Voulez-vous vraiment envoyer la note à " + username + " ?");
        if (conf) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.reload();
                    console.log(id);
                }
            };

            var formData = new FormData();
            formData.append('username', username);
            formData.append('id', id);
            formData.append('note', nbEtoiles);
            formData.append('commentaire', document.getElementById("commentaire").value);

            xhr.open('POST', '/ajax/validerNotePosteur', true);
            xhr.send(formData);
        }
    }
}


/*Se désister d'une file d'attente */
function validerNoteClient(username, id) {
    let nbEtoiles = 0;
    let allStars = document.querySelectorAll('.star');
    allStars.forEach((star, i) => {
        if (star.textContent == '★') {
            nbEtoiles++;
        } 
    });
    console.log("Note donnée : ", nbEtoiles);

    if (nbEtoiles == 0) {
        alert("Veuillez mettre un nombre d'étoiles !");
    } else {
        var conf = confirm("Voulez-vous vraiment envoyer la note à " + username + " ?");
        if (conf) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.reload();
                }
            };
            var formData = new FormData();
            formData.append('username', username);
            formData.append('id', id);
            formData.append('note', nbEtoiles);
            formData.append('commentaire', document.getElementById("commentaire").value);
            xhr.open('POST', '/ajax/validerNoteClient', true);
            xhr.send(formData);
        }
    }
}