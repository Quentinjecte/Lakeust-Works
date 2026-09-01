/* dispose.js — nettoyage Three.js partagé : libère géométries/matériaux
   d'une scène entière, et force la perte du contexte WebGL du renderer.
   Portée volontairement étroite : les scènes qui construisent leur rendu
   « à la main » plutôt que via une scene graph classique (blackhole.js —
   render targets et matériaux nommés, pas un scene.traverse) gardent leur
   propre dispose ; celui-ci sert les scènes en scene.traverse (home-stage.js
   l'a déjà, forest-stage.js ne l'avait pas — fuite mémoire à chaque
   montage/démontage, corrigée en branchant ce helper). */

/* Parcourt `scene` et dispose chaque géométrie + matériau rencontrés (les
   textures d'un matériau, elles, restent partagées par défaut — les
   disposer ici les invaliderait si un autre objet les référence encore ;
   à disposer explicitement côté appelant si elles sont vraiment privées à
   la scène qu'on détruit). */
export function disposeScene(scene) {
  if (!scene) return;
  scene.traverse(node => {
    if (node.geometry) node.geometry.dispose();
    const mat = node.material;
    if (mat) (Array.isArray(mat) ? mat : [mat]).forEach(m => m.dispose());
  });
  scene.background = null;
}

/* .dispose() seul libère les objets GPU (programmes, buffers) mais garde le
   contexte WebGL vivant — sur une page qui recrée souvent des scènes
   (labs, cinématiques), forceContextLoss() le relâche vraiment, évitant
   d'épuiser la limite de contextes WebGL simultanés du navigateur. */
export function disposeRenderer(renderer) {
  if (!renderer) return;
  renderer.dispose();
  renderer.forceContextLoss();
}
