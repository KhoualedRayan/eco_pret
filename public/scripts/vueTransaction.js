document.querySelectorAll('.star').forEach(function (star, index) {
    star.addEventListener('click', function () {
        let allStars = document.querySelectorAll('.star');
        // Mettre à jour l'affichage des étoiles en fonction de la sélection
        allStars.forEach((star, i) => {
            if (i <= index) {
                star.innerHTML = '&#9733;'; // étoile pleine
            } else {
                star.innerHTML = '&#9734;'; // étoile vide
            }
        });
        // Ici, vous pouvez ajouter du code pour soumettre la note à votre serveur
        console.log("Note donnée : ", index + 1);
    });
});