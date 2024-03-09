function generateCalendar(year, month) {
    const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
    const dayNames = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"]; // Noms des jours de la semaine de lundi à dimanche
    const date = new Date(year, month - 1, 1);
    const lastDay = new Date(year, month, 0).getDate();
    let firstDayIndex = date.getDay(); // Obtenir le bon premier jour du mois
    if (firstDayIndex === 0) {
      firstDayIndex = 7; // Si c'est un dimanche, on ajuste à 7 pour considérer le lundi comme premier jour de la semaine
    }
    const monthYear = monthNames[month - 1] + " " + year;
    document.getElementById("month-year").innerText = monthYear;
  
    const calendarBody = document.getElementById("calendar");
    calendarBody.innerHTML = ''; // Effacer le contenu du calendrier
  
    let dateCounter = 1;
  
    // Ajout des noms des jours dans la première ligne du calendrier
    const headerRow = calendarBody.insertRow();
    for (let i = 0; i < 7; i++) {
      const headerCell = headerRow.insertCell();
      headerCell.classList.add("days");
      headerCell.innerText = dayNames[i];
    }
  
    for (let i = 0; i < 6; i++) {
      const row = calendarBody.insertRow();
      for (let j = 1; j <= 7; j++) { // Commencer à partir de 1 pour considérer le lundi comme premier jour de la semaine
        const cell = row.insertCell();
        if ((i === 0 && j < firstDayIndex) || dateCounter > lastDay) { // Condition modifiée ici
          cell.innerText = "";
          cell.classList.add("previous-day");
        } else {
          cell.innerText = dateCounter;
          if (dateCounter === new Date().getDate() && year === new Date().getFullYear() && month === new Date().getMonth() + 1) {
            cell.classList.add("current-day"); // Ajouter la classe "current-day" si c'est le jour actuel
          }
          if (new Date(year, month - 1, dateCounter) < new Date() && (year < new Date().getFullYear() || (year === new Date().getFullYear() && month <= new Date().getMonth() + 1)) && !(dateCounter === new Date().getDate() && year === new Date().getFullYear() && month === new Date().getMonth() + 1)) {
            cell.classList.add("previous-day");
          }
          
          dateCounter++;
        }
      }
      // Arrêter la génération si on a déjà affiché tous les jours du mois
      if (dateCounter > lastDay) {
        break;
      }
    }
  }
  
  
  const currentDate = new Date();
  let currentYear = currentDate.getFullYear();
  let currentMonth = currentDate.getMonth() + 1;
  generateCalendar(currentYear, currentMonth);
  
  document.getElementById("prev-month").addEventListener("click", function() {
    currentMonth--;
    if (currentMonth === 0) {
      currentMonth = 12;
      currentYear--;
    }
    generateCalendar(currentYear, currentMonth);
  });
  
  document.getElementById("next-month").addEventListener("click", function() {
    currentMonth++;
    if (currentMonth === 13) {
      currentMonth = 1;
      currentYear++;
    }
    generateCalendar(currentYear, currentMonth);
  });
  document.getElementById("current-month").addEventListener("click", function() {
    const currentDate = new Date();
    currentYear = currentDate.getFullYear();
    currentMonth = currentDate.getMonth() + 1;
    generateCalendar(currentYear, currentMonth);
  });
    