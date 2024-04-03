function showRec(id) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('recWindow').innerHTML = xhr.responseText;
            document.querySelectorAll('selected').forEach(e => {e.classList.remove('selected'); });
            document.getElementById('rec-'+id).classList.add('selected');
        }
    };
    xhr.open('POST', '/ajax/reclamation/'+id, true);
    xhr.send();
}