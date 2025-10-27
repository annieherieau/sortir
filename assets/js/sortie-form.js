
function publish(e){
    e.preventDefault();
    let form = document.forms['sortie']
    let publier = form.elements['sortie_publier'];
    publier.checked = true;
    form.submit();
}

function validateDates(){
    event.preventDefault();
    let form = document.forms['sortie'];
    // TODO validation des dates
    form.submit();

}