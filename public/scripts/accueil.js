/*Emprunter une annonce */
function confirmerEmprunt(event, id, type) {
    if (confirm("Etes-vous sûr de vouloir emprunter cette annonce ?")) {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('annonceId', id);
        data.append('annonceType', type);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText != "OK") {
                    //Erreur
                    console.log(xhr.responseText)
                }
                else {
                    //Transaction réussi
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

function filtrer() {
    var d = document.getElementById("filtres").style.display;
    document.getElementById("filtres").style.display = d == "block" ? "none" : "block";
}

function noFilterType() {
    document.getElementById("categorieFiltre").style.display = "none";
    document.getElementById("dureeFiltre").style.display = "none";
    document.getElementById("periodeServiceFiltre").style.display = "none";
}

function filtrerType(type) {
    document.getElementById("categorieFiltre").style.display = "block";
    // mettre les bonnes catégories à partir de la bdd
    var divCateg = document.getElementById("categs");
    if (type == "materiel") {
        document.getElementById("dureeFiltre").style.display = "block";
        document.getElementById("periodeServiceFiltre").style.display = "none";
    } else {
        document.getElementById("dureeFiltre").style.display = "none";
        document.getElementById("periodeServiceFiltre").style.display = "block";
    }
    if (divCateg.typeAnnonce == type) {
        return;
    } else {
        while (divCateg.firstChild) {
            divCateg.removeChild(divCateg.firstChild);
        }
    }
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            divCateg.innerHTML = xhr.responseText;
            divCateg.typeAnnonce = type;
        }
    };
    xhr.open('POST', '/ajax/getCategories/'+type, true);
    xhr.send();
}

function filtrerMateriel() {
    filtrerType("materiel");
}

function filtrerService() {
    filtrerType("service");
}

function resetNoteFiltre() {
    let allStars = document.querySelectorAll('.star');
    allStars.forEach((star, i) => {
        star.innerHTML = 'star_rate';
    });
}

function resetFilters() {
    document.getElementById("toutType").checked = true;
    noFilterType();
    document.getElementById("categs").typeAnnonce = "";
    document.getElementById("prix_min").value = "";
    document.getElementById("prix_max").value = "";
    resetNoteFiltre();
    document.getElementById("toutAvecClient").checked = true;
}
