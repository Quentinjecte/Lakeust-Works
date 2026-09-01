/* Entrée Vite de /orbital-cinematic.
   Ordre imposé : le custom element doit être défini avant que la timeline le cherche. */
import './home-stage.js';
import './home-cinematic.js';

/* Deux pièges avec l'initialisation manuelle du studio Theatre.js :

   1. `@theatre/studio` n'expose pas toujours son objet directement sur le
      default export selon la façon dont le bundler l'interop-wrap (CJS vs ESM)
      — c'est pourquoi createCinematic() plus haut fait la même vérification
      en trois temps avant d'appeler .initialize().

   2. studio.initialize() lève une erreur ("...without importing @theatre/core")
      si `@theatre/core` n'a pas déjà été importé — studio a besoin que core
      se soit enregistré auprès de lui AVANT l'appel. L'import ci-dessous doit
      donc rester présent même si getProject/types ne sont pas utilisés ici :
      son effet de bord (l'enregistrement) est ce qui compte, pas ses bindings. */
import studioMod from '@theatre/studio';
import { getProject, types } from '@theatre/core';

const studio = studioMod && studioMod.initialize ? studioMod
  : (studioMod && studioMod.default && studioMod.default.initialize) ? studioMod.default
  : studioMod;

studio.initialize();
