/*Emprunter une annonce */
function confirmerEmprunt(event, id, type) {
    if (confirm("Etes-vous sûr de vouloir emprunter cette annonce ?")) {
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
                    //Transaction réussi
                    console.log(xhr.responseText);
                    window.location.href = '/profile/transactions';
                }
            }
        };
        xhr.open('POST', '/ajax/emprunt', true);
        xhr.send(data);
    }
}

function search() {
    var texte = document.getElementById("searchInput").value;
    if (texte.trim().length != 0) {
        params = window.location.href.split("?")
        window.location.href = params[0]+"?search="+texte;
    }
}