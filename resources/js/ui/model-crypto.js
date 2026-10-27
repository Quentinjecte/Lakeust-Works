/* model-crypto.js — déchiffre les modèles protégés (.lwm) pour le visualiseur.

   Format d'un .lwm, produit par `php artisan models:protect` :
       "LWM1" (4 octets) | IV (12 octets) | AES-256-GCM : chiffré + tag (16 octets)
   Une fois déchiffré, c'est le glTF / GLB d'origine.

   Ce que ça protège : le fichier servi par le site (et toute URL /models/…) est illisible
   — un clic droit, un « enregistrer sous » ou un aspirateur de site ne rapporte rien
   d'ouvrable dans Blender. Ce que ça ne protège PAS : la clé voyage avec le script (un site
   statique n'a pas de serveur pour la garder) et le modèle finit en clair dans la mémoire
   du navigateur, comme tout ce qui s'affiche à l'écran ; un visiteur déterminé peut donc
   l'extraire. C'est un frein sérieux, pas un coffre-fort. */

import { MODEL_KEY } from './model-key.js';

const MAGIC = 'LWM1';

let keyPromise = null;
const getKey = () => keyPromise ??= crypto.subtle.importKey(
    'raw', Uint8Array.from(atob(MODEL_KEY), c => c.charCodeAt(0)), 'AES-GCM', false, ['decrypt']);

/** @param {ArrayBuffer} buf contenu d'un .lwm  @returns {Promise<ArrayBuffer>} le glTF / GLB en clair */
export async function decryptModel(buf) {
    const b = new Uint8Array(buf);
    if (String.fromCharCode(...b.subarray(0, 4)) !== MAGIC) throw new Error('modèle protégé illisible (en-tête)');
    return crypto.subtle.decrypt({ name: 'AES-GCM', iv: b.subarray(4, 16) }, await getKey(), b.subarray(16));
}
