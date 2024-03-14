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