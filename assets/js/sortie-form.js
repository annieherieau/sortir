
/**
 * Formulaire Création/modification de sortie
 * *********************************************

 * permet de cocher le checkbox 'sortie_publier'
 * avant de soumettre le formulaire
 * @param e // event
 */
function publish(e){
    e.preventDefault();
    let form = document.forms['sortie']
    let publier = form.elements['sortie_publier'];
    publier.checked = true;
    form.submit();
}

/**
 * Permet de vérifier la cohérence des dates
 */
function validateDates(e){
    e.preventDefault();
    let form = document.forms['sortie'];
    // TODO validation des dates
}

function handleLieuInfos(lieux){

    let lieu_s = document.getElementById('sortie_lieu');
    let lieu_id = lieu_s.value;
    console.log(lieux);

}




