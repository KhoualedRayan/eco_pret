/*Emprunter une annonce */
function confirmerEmprunt(event, id, type) {
    if (confirm("�tes-vous s�r de vouloir emprunter cette annonce ?")) {
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
                    //Annonce supprim� avec succ�s :D
                    console.log(xhr.responseText);
                    location.reload();
                }
            }
        };
        xhr.open('POST', '/ajax/emprunt', true);
        xhr.send(data);
    }
}