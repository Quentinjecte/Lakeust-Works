/* Entrée Vite de /orbital-cinematic.
   Ordre imposé : le custom element doit être défini avant que la timeline le cherche.

   L'éditeur Theatre.js n'est plus initialisé ici : il l'était sans condition,
   donc ouvert à tous les visiteurs et livré dans le bundle. Il passe par
   ?studio comme les autres cinématiques, et uniquement en dev
   (voir createCinematic dans home-cinematic.js). */
import './home-stage.js';
import './home-cinematic.js';
