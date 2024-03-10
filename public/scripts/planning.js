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
  
    const editedMonth = year + "-" + month;
    console.log(editedMonth);
    const editedDates = editedDays[editedMonth] || [];

  for (let i = 0; i < 6; i++) {
    const row = calendarBody.insertRow();
    for (let j = 1; j <= 7; j++) {
      const cell = row.insertCell();
      if ((i === 0 && j < firstDayIndex) || dateCounter > lastDay) {
        cell.innerText = "";
        cell.classList.add("previous-day");
      } else {
        cell.innerText = dateCounter;
        if (dateCounter === new Date().getDate() && year === new Date().getFullYear() && month === new Date().getMonth() + 1) {
          cell.classList.add("current-day");
        }
        if (new Date(year, month - 1, dateCounter) < new Date() && (year < new Date().getFullYear() || (year === new Date().getFullYear() && month <= new Date().getMonth() + 1)) && !(dateCounter === new Date().getDate() && year === new Date().getFullYear() && month === new Date().getMonth() + 1)) {
          cell.classList.add("previous-day");
        }

        const editedDatesDays = editedDates.map(date => date.getDate());
        if (editedDatesDays.includes(dateCounter)) {
            cell.classList.add("edited-day"); // Ajouter la classe "edited-day" si le jour a été modifié
        }
        dateCounter++;
      }
    }
    if (dateCounter > lastDay) {
      break;
    }
  }

  // Réappliquer les écouteurs d'événements pour les cellules
  if (editMode) {
    const cells = document.querySelectorAll("#calendar td:not(.previous-day)");
    cells.forEach(cell => {
      cell.addEventListener("click", toggleCellEdit);
    });
  }
  }
  
  
  const currentDate = new Date();
  let currentYear = currentDate.getFullYear();
  let currentMonth = currentDate.getMonth() + 1;
  let editMode = false; // Variable pour suivre si le mode édition est activé
  let editedDays = [];
  //init_planning(); // Tableau pour stocker les jours modifiés
  generateCalendar(currentYear, currentMonth);
  
  document.getElementById("prev-month").addEventListener("click", function() {
    valider();
    currentMonth--;
    if (currentMonth === 0) {
      currentMonth = 12;
      currentYear--;
    }
    generateCalendar(currentYear, currentMonth);
  });
  
  document.getElementById("next-month").addEventListener("click", function() {
    valider();
    currentMonth++;
    if (currentMonth === 13) {
      currentMonth = 1;
      currentYear++;
    }
    generateCalendar(currentYear, currentMonth);
  });
  document.getElementById("current-month").addEventListener("click", function() {
    valider();
    const currentDate = new Date();
    currentYear = currentDate.getFullYear();
    currentMonth = currentDate.getMonth() + 1;
    generateCalendar(currentYear, currentMonth);
  });
    
  document.getElementById("edit-mode").addEventListener("click", toggleEditMode);

  function toggleEditMode() {
    editMode = !editMode; // Inversion du mode édition
    const validateButton = document.getElementById("validate-edits");
    
    if (editMode) {
      // Activation du mode édition
      document.getElementById("edit-mode").innerText = "Mode normal";
      validateButton.style.display = "inline"; // Afficher le bouton de validation
      const cells = document.querySelectorAll("#calendar td:not(.previous-day)");
      cells.forEach(cell => {
        cell.addEventListener("click", toggleCellEdit);
      });
    } else {
      // Désactivation du mode édition
      document.getElementById("edit-mode").innerText = "Mode édition";
      validateButton.style.display = "none"; // Cacher le bouton de validation
      const cells = document.querySelectorAll("#calendar td:not(.previous-day)");
      cells.forEach(cell => {
        cell.removeEventListener("click", toggleCellEdit);
      });
    }
  }
  
  function toggleCellEdit() {
    if (this.classList.contains("edited-day")) {
      this.classList.remove("edited-day");
    } else {
      this.classList.add("edited-day");
    }
  }
  
  document.getElementById("validate-edits").addEventListener("click", valider_tout);

  function valider() {
    // Valider les modifications et enregistrer les jours modifiés
    const editedCells = document.querySelectorAll("#calendar td.edited-day");
    const editedMonth = currentYear + "-" + currentMonth; // Clé pour l'objet editedDays
    if (!editedDays.hasOwnProperty(editedMonth)) {
      editedDays[editedMonth] = []; // Initialiser les jours modifiés pour ce mois s'ils n'existent pas déjà
    }
  
    // Récupérer les jours modifiés avant la validation
    const previousEditedDates = editedDays[editedMonth].slice();
  
    editedDays[editedMonth] = []; // Réinitialiser les jours modifiés pour ce mois
    
    editedCells.forEach(cell => {
      const day = parseInt(cell.innerText);
      const date = new Date(currentYear, currentMonth - 1, day); // Créer un objet Date pour la date modifiée
      editedDays[editedMonth].push(date); // Ajouter l'objet Date au tableau editedDays pour ce mois
    });
  
    // Comparer les jours modifiés avant et après la validation
    previousEditedDates.forEach(previousDate => {
      if (!editedDays[editedMonth].some(newDate => newDate.getTime() === previousDate.getTime())) {
        // Si la date modifiée précédente n'est pas présente dans les jours modifiés après la validation, la retirer du tableau
        editedDays[editedMonth] = editedDays[editedMonth].filter(newDate => newDate.getTime() !== previousDate.getTime());
      }
    });
  
    // Afficher les jours modifiés dans la console à titre d'exemple
    console.log("Jours modifiés pour le mois " + editedMonth + ":", editedDays[editedMonth]);
  };

  function valider_tout() {
    valider();
    toggleEditMode();

    var xhr = new XMLHttpRequest();
    var data = new FormData();

    // Convertir les dates en chaînes de caractères au format ISO 8601
    var editedDaysISO = {};
    Object.keys(editedDays).forEach(function(key) {
        var isoDates = editedDays[key].map(function(date) {
            return date.toISOString().split('T')[0]; // format YYYY-MM-DD
        });
        editedDaysISO[key] = isoDates;
    });

    data.append('editedDays', JSON.stringify(editedDaysISO));
    xhr.onreadystatechange = function() {
        if (xhr.responseText != "OK") {
            var errorElement = document.getElementById(xhr.responseText + "Erreur");
            if (errorElement) {
                errorElement.style.display = 'block';
                setTimeout(function() {
                    errorElement.style.display = 'none';
                }, 4000);
            }
        } else {
            console.log(xhr.responseText);
            location.reload();
        }
    };
    xhr.open('POST', '/ajax/planning_validate', true);
    xhr.send(data);
}



  function init_planning(){
    var xhr = new XMLHttpRequest();
    var data = new FormData();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var responseData = JSON.parse(xhr.responseText); // Convertir la réponse JSON en objet JavaScript
            console.log(responseData); // Afficher les données récupérées dans la console pour vérification
            // Utilisez les données récupérées ici
            // Par exemple, vous pouvez les assigner à la variable editedDays
            editedDays = responseData;
        }
    };
    xhr.open('POST', '/ajax/planning_init', true);
    xhr.send(data);
}

  
  
  