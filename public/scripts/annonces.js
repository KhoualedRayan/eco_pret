// affiche la boite de dialogue d'une annonce

function showAnnonce(id) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText != 'KO') {
                var dialog = document.createElement('dialog');
                document.body.appendChild(dialog);
                dialog.innerHTML = xhr.responseText;
                document.getElementById('close').addEventListener("click", function() {
                    dialog.close();
                    document.body.removeChild(dialog);
                });
                dialog.showModal();
            }
        }
    };
    xhr.open('POST', '/ajax/vue/annonce/'+id, true);
    xhr.send();
}