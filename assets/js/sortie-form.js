
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
 * Permet définir la limite maximum de la date limite d'inscription
 * date limite < date début
 * @param startingDateElement
 */
function setMaxLimitDate(startingDateElement){
    let maxDate = startingDateElement.value;
    console.log(maxDate);
    if(maxDate)
    {
        let dateLmiteElement = document.getElementById('sortie_registerLimitDate');
        dateLmiteElement.setAttribute('max', (maxDate).replace('T', ' '));
    }
}

/**
 * mise à jour des informations du lieu sélectionné
 * @param lieux
 */
function handleLieuInfos(lieux){

    let lieuId = document.getElementById('sortie_lieu').value;

    let street = document.getElementById('street');
    let codeAndVille = document.getElementById('codeAndVille');
    let coordToString = document.getElementById('coordToString');

    street.value = lieux[lieuId]['street'];
    codeAndVille.value = lieux[lieuId]['codeAndVille'];
    coordToString.value = lieux[lieuId]['coordToString'];
}




