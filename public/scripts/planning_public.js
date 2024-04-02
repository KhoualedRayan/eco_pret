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
    calendarBody.innerHTML = ''; 
  
    let dateCounter = 1;
  
    const headerRow = calendarBody.insertRow();
    for (let i = 0; i < 7; i++) {
      const headerCell = headerRow.insertCell();
      headerCell.classList.add("days");
      headerCell.innerText = dayNames[i];
    }
  
    const editedMonth = year + "-" + month;
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
            cell.classList.remove("edited-day");
          }
  
          const editedDatesDays = editedDates.map(date => date.getDate());
          if (editedDatesDays.includes(dateCounter)) {
            cell.classList.add("edited-day"); // Ajouter la classe "edited-day" si le jour a été modifié (indispo)
          }
          dateCounter++;
        }
      }
      if (dateCounter > lastDay) {
        break;
      }
    }
  
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
  let editMode = false; // mode édition
  let editedDays = [];// Tableau pour stocker les jours modifiés
  init_planning();
  
  
  
  document.getElementById("prev-month").addEventListener("click", function () {
    valider();
    currentMonth--;
    if (currentMonth === 0) {
      currentMonth = 12;
      currentYear--;
    }
    generateCalendar(currentYear, currentMonth);
  });
  
  document.getElementById("next-month").addEventListener("click", function () {
    valider();
    currentMonth++;
    if (currentMonth === 13) {
      currentMonth = 1;
      currentYear++;
    }
    generateCalendar(currentYear, currentMonth);
  });
  document.getElementById("current-month").addEventListener("click", function () {
    valider();
    const currentDate = new Date();
    currentYear = currentDate.getFullYear();
    currentMonth = currentDate.getMonth() + 1;
    generateCalendar(currentYear, currentMonth);
  });
    
  
  function toggleCellEdit() {
    if (this.classList.contains("edited-day")) {
      this.classList.remove("edited-day");
    } else {
      this.classList.add("edited-day");
    }
  }
  
  
  function valider() {
    const editedCells = document.querySelectorAll("#calendar td.edited-day");
    const editedMonth = currentYear + "-" + currentMonth; // Clé pour l'objet editedDays
    if (!editedDays.hasOwnProperty(editedMonth)) {
      editedDays[editedMonth] = []; // Initialiser les jours modifiés pour ce mois s'ils n'existent pas déjà
    }
  
    // Récupérer les jours modifiés avant la validation
    const previousEditedDates = editedDays[editedMonth].slice();
  
    editedDays[editedMonth] = []; // Réinitialise
  
    editedCells.forEach(cell => {
      const day = parseInt(cell.innerText);
      const date = new Date(currentYear, currentMonth-1, day);
      editedDays[editedMonth].push(date); 
    });
  
    // Comparer les jours modifiés avant et après la validation
    previousEditedDates.forEach(previousDate => {
      if (!editedDays[editedMonth].some(newDate => newDate.getTime() === previousDate.getTime())) {
        editedDays[editedMonth] = editedDays[editedMonth].filter(newDate => newDate.getTime() !== previousDate.getTime());
      }
    });
  
  };
  
  function valider_tout() {
  
    valider();
    toggleEditMode();
    var xhr = new XMLHttpRequest();
    var data = new FormData();
    var editedDaysISOStrings = {};
  
  
    Object.keys(editedDays).forEach(function (monthYear) {
      // Initialiser un tableau pour stocker les dates modifiées pour ce mois et cette année
      var editedDates = editedDays[monthYear];
      var isoDates = editedDates.map(function (date) {
        // Obtenir la date au format (YYYY-MM-DD) (en String)
        return formatDate(date);
      });
      editedDaysISOStrings[monthYear] = isoDates;
    });
    data.append('editedDays', JSON.stringify(editedDaysISOStrings));
    xhr.onreadystatechange = function () {
      if (xhr.responseText != "OK") {
        var errorElement = document.getElementById(xhr.responseText + "Erreur");
        if (errorElement) {
          errorElement.style.display = 'block';
          setTimeout(function () {
            errorElement.style.display = 'none';
          }, 4000);
        }
      } else {
        window.location.reload();
      }
    };
    xhr.open('POST', '/ajax/planning_validate', true);
    xhr.send(data);
  }
  
  
  function init_planning() {
    var xhr = new XMLHttpRequest();
    var data = new FormData();
    var userId = document.getElementById('user-id').getAttribute('data-user-id');
    data.append('id', userId);
    return new Promise(function (resolve, reject) {
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            var responseData = JSON.parse(xhr.responseText);
            editedDays = responseData;
            var editedDaysAsDate = {};
  
            Object.keys(editedDays).forEach(function (monthYear) {
              var editedDates = editedDays[monthYear];
  
              // Convertir les dates en objets Date
              var datesAsDate = editedDates.map(function (dateObject) {
                if ('date' in dateObject) {
                  var dateValue = dateObject.date;
                  var year = parseInt(dateValue.substring(0, 4));
                  var month = parseInt(dateValue.substring(5, 7)) - 1; // Soustraire 1 pour correspondre à l'index des mois dans les objets Date
                  var day = parseInt(dateValue.substring(8, 10));
                  return new Date(year, month, day);
                } else {
                  console.log("Erreur : 'date' n'est pas présente dans l'objet");
                  return null;
                }
              });
  
              editedDaysAsDate[monthYear] = datesAsDate.filter(function (date) {
                return date !== null; 
              });
            });
            
            editedDays = editedDaysAsDate;
            //generateCalendar(currentYear, currentMonth);
            resolve(); // Indique que l'initialisation est terminée
          } else {
            reject(xhr.statusText); // Rejette la promesse en cas d'erreur
          }
        }
      };
      xhr.open('POST', '/ajax/planning_public_init', true);
      xhr.send(data);
    });
  }
  
  init_planning().then(function () {
    generateCalendar(currentYear, currentMonth);
  
  }).catch(function (error) {
    console.error("Une erreur s'est produite lors du chargement des données :", error);
  });
  
  
  function formatDate(date) {
    var year = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var day = ('0' + date.getDate()).slice(-2);
    return year + '-' + month + '-' + day;
  }
  
  
  
  
  