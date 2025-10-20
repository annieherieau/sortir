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
