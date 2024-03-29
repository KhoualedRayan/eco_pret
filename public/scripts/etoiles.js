
function clickStar(index) {
    index = Number(index);
    let allStars = document.querySelectorAll('.star');
    // Mettre � jour l'affichage des �toiles en fonction de la s�lection
    allStars.forEach((star, i) => {
        if (i <= index) {
            star.innerHTML = 'star'; // �toile pleine
        } else {
            star.innerHTML = 'star_rate'; // �toile vide
        }
    });
    console.log("Note donnée : ", index + 1);
}