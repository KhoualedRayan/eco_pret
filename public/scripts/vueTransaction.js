document.querySelectorAll('.star').forEach(function (star, index) {
    star.addEventListener('click', function () {
        let allStars = document.querySelectorAll('.star');
        // Mettre � jour l'affichage des �toiles en fonction de la s�lection
        allStars.forEach((star, i) => {
            if (i <= index) {
                star.innerHTML = '&#9733;'; // �toile pleine
            } else {
                star.innerHTML = '&#9734;'; // �toile vide
            }
        });
        // Ici, vous pouvez ajouter du code pour soumettre la note � votre serveur
        console.log("Note donn�e : ", index + 1);
    });
});

/*Se désister d'une file d'attente */
function validerNotePosteur(username, id) {
    var conf = confirm("Voulez-vous vraiment envoyer la note à " + username + " ?");
    if (conf) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                window.location.reload();
                console.log(id);
            }
        };

        var formData = new FormData();
        formData.append('id', id);

        xhr.open('POST', '/ajax/validerNotePosteur', true);
        xhr.send(formData);
    }
}


/*Se désister d'une file d'attente */
function validerNoteClient(username, id) {
    var conf = confirm("Voulez-vous vraiment envoyer la note à " + username + " ?");
    if (conf) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                window.location.reload();
            }
        };
        xhr.open('POST', '/ajax/validerNoteClient', true);
        xhr.send();
    }
}