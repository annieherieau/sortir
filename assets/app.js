import './bootstrap.js';
  /*
  * Welcome to your app's main JavaScript file!/n
  *
  * This file will be included onto the page via the importmap() Twig function,
  * which should already be in your base.html.twig.
  */
  import 'bootstrap';  // Déclaration du JS de Bootstrap
  import 'bootstrap/dist/css/bootstrap.min.css';  // Déclaration du CSS de Bootstrap
  import './styles/app.css';
  console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

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