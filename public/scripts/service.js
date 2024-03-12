function setRequired(containerId, required) {
    var container = document.getElementById(containerId);
    container.querySelectorAll('input').forEach(input => {
        input.required = required;
    });
}

function clearFields(containerId) {
    var container = document.getElementById(containerId);
    container.querySelectorAll('input').forEach(input => {
        input.value = '';
    });
}

document.getElementById('recurrence').addEventListener('change', function () {
    var recurrence = this.value;
    if (recurrence === 'quotidienne') {
        document.getElementById('jours').style.display = 'block';
        setRequired('jours', true);
        document.getElementById('mois').style.display = 'none';
        clearFields('mois');
        setRequired('mois', false);
        document.getElementById('semaine').style.display = 'none';
        clearFields('semaine');
        setRequired('semaine', false);
    } else if (recurrence === 'mensuelle') {
        document.getElementById('jours').style.display = 'none';
        clearFields('jours');
        setRequired('jours', false);
        document.getElementById('mois').style.display = 'block';
        setRequired('mois', true);
        document.getElementById('semaine').style.display = 'none';
        clearFields('semaine');
        setRequired('semaine', false);
    } else if (recurrence === 'hebdomadaire') {
        document.getElementById('jours').style.display = 'none';
        clearFields('jours');
        setRequired('jours', false);
        document.getElementById('mois').style.display = 'none';
        clearFields('mois');
        setRequired('mois', false);
        document.getElementById('semaine').style.display = 'block';
        setRequired('semaine', true);
    } else {
        document.getElementById('jours').style.display = 'none';
        clearFields('jours');
        setRequired('jours', false);
        document.getElementById('mois').style.display = 'none';
        clearFields('mois');
        setRequired('mois', false);
        document.getElementById('semaine').style.display = 'none';
        clearFields('semaine');
        setRequired('semaine', false);
    }
});

document.getElementById('addDateButton').addEventListener('click', function () {
    var additionalDates = document.getElementsByName('additional_date[]');

    // Parcourir tous les champs de dates
    for (var i = 0; i < additionalDates.length; i++) {
        // Afficher la valeur de chaque champ dans la console
        console.log("Date supplémentaire " + (i + 1) + ": " + additionalDates[i].value);
    }

    // Créer un nouvel élément <br>
    var lineBreak = document.createElement('br');


    var newInput = document.createElement('input');
    newInput.type = 'datetime-local';
    newInput.name = 'additional_date[]';
    newInput.min = '{{ "now"|date("Y-m-d") }}T00:00';
    newInput.title = 'Date et heure du prêt optionnel, doit être supérieur à maintenant';
    newInput.required = true;
    var additionalRecurrencesDiv = document.getElementById('additionalDates'); //pour suppr avec le bouton, appartient au recurrence
    var deleteButton = document.createElement('button');
    var div_gen = document.createElement('div');
    deleteButton.textContent = "Supprimer";
    deleteButton.addEventListener('click', function () {
        additionalRecurrencesDiv.removeChild(div_gen);
        additionalRecurrencesDiv.removeChild(lineBreak);
    });
    var newDiv = document.createElement('div');

    // Créer un élément span pour le texte en gras
    var boldText = document.createElement('span');
    boldText.style.fontWeight = 'bold'; // Appliquer le style de police en gras
    boldText.textContent = 'Date supplémentaire *'; // Ajouter le texte

    // Ajouter le span contenant le texte en gras à newDiv2
    newDiv.appendChild(boldText);
    newDiv.appendChild(document.createElement('br'));
    newDiv.appendChild(newInput);
    newDiv.appendChild(document.createElement('br'));
    newDiv.appendChild(document.createElement('br'));
    div_gen.appendChild(newDiv);



    //RECURRENCE

    var newSelect = document.createElement('select');
    var newPeriode = document.createElement('div');
    var newDiv2 = document.createElement('div');

    // Créer un élément span pour le texte en gras
    var boldText2 = document.createElement('span');
    boldText2.style.fontWeight = 'bold'; // Appliquer le style de police en gras
    boldText2.textContent = 'Récurrence '; // Ajouter le texte

    // Ajouter le span contenant le texte en gras à newDiv2
    newDiv2.appendChild(boldText2);

    newDiv2.appendChild(document.createElement('br'));

    newSelect.name = 'additional_recurrence[]';
    newPeriode.name = 'additional_periode[]';
    newSelect.innerHTML = document.getElementById('recurrence').innerHTML;
    newPeriode.innerHTML = document.getElementById('periode').innerHTML;
    newPeriode.innerHTML = '';

    newSelect.addEventListener('change', function () {
        newPeriode.innerHTML = ""; // Efface le contenu précédent de newPeriode

        if (newSelect.value === 'quotidienne') {
            // Créer un nouvel élément div pour contenir les champs de formulaire et les libellés pour l'option "quotidienne"
            var newJoursDiv = document.createElement('div');
            newJoursDiv.id = 'jours_cloned';
            var joursInputs = document.getElementById('jours').querySelectorAll('input');

            joursInputs.forEach(input => {
                // Créer un nouveau champ de formulaire cloné
                var newInput = document.createElement('input');
                newInput.type = input.type;
                newInput.name = input.name;
                newInput.required = true; // Rendre le champ requis

                // Créer un libellé correspondant au champ de formulaire
                var newLabel = document.createElement('label');
                newLabel.textContent = input.previousElementSibling.textContent; // Utiliser le libellé précédent

                // Ajouter le libellé et le champ de formulaire au div
                newJoursDiv.appendChild(newLabel);
                newJoursDiv.appendChild(newInput);
                newJoursDiv.appendChild(document.createElement('br'));
            });

            // Ajouter le div contenant les champs de formulaire clonés à newPeriode
            newPeriode.appendChild(newJoursDiv);
        } else if (newSelect.value === 'hebdomadaire') {
            // Créer un nouvel élément div pour contenir les champs de formulaire et les libellés pour l'option "hebdomadaire"
            var newSemaineDiv = document.createElement('div');
            newSemaineDiv.id = 'semaine_cloned';
            var semaineInputs = document.getElementById('semaine').querySelectorAll('input');

            semaineInputs.forEach(input => {
                // Créer un nouveau champ de formulaire cloné
                var newInput = document.createElement('input');
                newInput.type = input.type;
                newInput.name = input.name;
                newInput.required = true; // Rendre le champ requis

                // Créer un libellé correspondant au champ de formulaire
                var newLabel = document.createElement('label');
                newLabel.textContent = input.previousElementSibling.textContent; // Utiliser le libellé précédent

                // Ajouter le libellé et le champ de formulaire au div
                newSemaineDiv.appendChild(newLabel);
                newSemaineDiv.appendChild(newInput);
                newSemaineDiv.appendChild(document.createElement('br'));
            });

            // Ajouter le div contenant les champs de formulaire clonés à newPeriode
            newPeriode.appendChild(newSemaineDiv);
        } else if (newSelect.value === 'mensuelle') {
            // Créer un nouvel élément div pour contenir les champs de formulaire et les libellés pour l'option "mensuelle"
            var newMoisDiv = document.createElement('div');
            newMoisDiv.id = 'mois_cloned';
            var moisInputs = document.getElementById('mois').querySelectorAll('input');

            moisInputs.forEach(input => {
                // Créer un nouveau champ de formulaire cloné
                var newInput = document.createElement('input');
                newInput.type = input.type;
                newInput.name = input.name;
                newInput.required = true; // Rendre le champ requis

                // Créer un libellé correspondant au champ de formulaire
                var newLabel = document.createElement('label');
                newLabel.textContent = input.previousElementSibling.textContent; // Utiliser le libellé précédent

                // Ajouter le libellé et le champ de formulaire au div
                newMoisDiv.appendChild(newLabel);
                newMoisDiv.appendChild(newInput);
                newMoisDiv.appendChild(document.createElement('br'));
            });

            // Ajouter le div contenant les champs de formulaire clonés à newPeriode
            newPeriode.appendChild(newMoisDiv);
        }
        // Si aucune option n'est sélectionnée, ne rien faire
    });


    newDiv2.appendChild(newSelect);
    newDiv2.append(newSelect.value);
    newDiv2.appendChild(newPeriode);
    newDiv2.appendChild(deleteButton);
    newDiv2.appendChild(document.createElement('br'));

    div_gen.classList.add("special-div");
    div_gen.appendChild(newDiv2);
    additionalRecurrencesDiv.appendChild(div_gen);
    additionalRecurrencesDiv.appendChild(lineBreak);

});
