<?php

namespace App\Support;

/**
 * Catalogue des modèles 3D de la branche « Lakeust Arts » (pages/arts/*).
 *
 * Modèles réels : les .gltf exportés de Blockbench (autonomes : géométrie et texture
 * pixel art embarquées), servis CHIFFRÉS en .lwm depuis public/models/, plus la scène
 * forestière en .glb. Les textes (descriptions, catégories) sont PROVISOIRES — à
 * réécrire à ta façon ; les triangles et tailles de textures sont mesurés dans les
 * fichiers.
 *
 * Pour ajouter un modèle : exporte-le en glTF (Blockbench : File > Export > Export as
 * glTF ; Blender : File > Export > glTF 2.0), dépose-le dans resources/models/ (dossier
 * hors dépôt, jamais publié), lance `php artisan models:protect` (il produit
 * public/models/<nom>.lwm, à committer) et ajoute une ligne à all(). Un modèle lourd
 * (> ~5 Mo) prend 'load' => 'click' pour ne se charger que sur demande. Tant que 'src'
 * est null, le visualiseur affiche la forme procédurale 'demo'.
 */
final class ModelCatalog
{
    /** Outils connus : libellé + couleur du badge. */
    public const TOOLS = [
        'blender'    => ['label' => 'Blender',       'color' => '#f5792a'],
        'blockbench' => ['label' => 'Blockbench',    'color' => '#29a8ff'],
        'substance'  => ['label' => 'Substance 3D',  'color' => '#b3a9e6'],
        'zbrush'     => ['label' => 'ZBrush',        'color' => '#e8c26a'],
    ];

    /** @return list<array<string,mixed>> */
    public static function all(): array
    {
        $prop = ['fr' => 'Prop', 'en' => 'Prop'];
        $chest = ['fr' => 'Coffre', 'en' => 'Chest'];
        $anim = fn (string $fr, string $en) => ['fr' => $fr, 'en' => $en];
        $no = ['fr' => 'Non', 'en' => 'No'];
        $base = ['tools' => ['blockbench'], 'demo' => 'knot', 'load' => 'auto', 'year' => '2026'];

        // slug, fichier (public/models/), nom, catégorie, triangles, texture, rig/animation, accent, étiquettes, description
        $rows = [
            ['boss-squelette-crystal', 'Boss squelette de cristal', ['fr' => 'Boss', 'en' => 'Boss'], '6 048', '512 × 256', $no, '#7be3ff', ['Boss', 'Voxel', 'Cristal'],
                ['fr' => "Boss squelettique serti de cristaux, construit cube par cube dans Blockbench : le plus détaillé de la collection.",
                 'en' => "Skeleton boss studded with crystals, built cube by cube in Blockbench — the most detailed of the collection."]],
            ['boss-roi-morts-vivants', 'Boss roi des morts-vivants', ['fr' => 'Boss', 'en' => 'Boss'], '7 668', '512 × 512', $no, '#9b7bff', ['Boss', 'Voxel', 'Morts-vivants'],
                ['fr' => "Roi des morts-vivants, boss final d'un donjon : le modèle le plus lourd de la collection, construit cube par cube dans Blockbench.",
                 'en' => "King of the undead, a dungeon's final boss: the heaviest model of the collection, built cube by cube in Blockbench."]],
            ['scythe-weapon-boss-king-of-dead', 'Faux du roi des morts', ['fr' => 'Arme', 'en' => 'Weapon'], '984', '512 × 512', $no, '#9b7bff', ['Arme', 'Faux', 'Boss'],
                ['fr' => "Faux du roi des morts-vivants, l'arme de son boss : lame spectrale et manche en cubes.",
                 'en' => "Scythe of the king of the undead, his boss weapon: spectral blade and cube-built handle."]],
            ['scythe-weapon-boss-squelette-crystal', 'Faux du boss squelette de cristal', ['fr' => 'Arme', 'en' => 'Weapon'], '864', '512 × 256', $no, '#7be3ff', ['Arme', 'Faux', 'Cristal'],
                ['fr' => "Faux du boss squelette de cristal, assortie à ses cristaux : l'arme à faire pivoter à côté de son propriétaire.",
                 'en' => "Scythe of the crystal skeleton boss, matching its crystals: the weapon to spin next to its owner."]],
            ['squelette', 'Squelette',['fr' => 'Personnage', 'en' => 'Character'], '3 168', '—', $no, '#e9e4d0', ['Voxel', 'Personnage', 'Low poly'],
                ['fr' => "Squelette en cubes, ennemi de base des donjons : silhouette lisible, exporté sans texture pour l'instant.",
                 'en' => "Cube-built skeleton, the dungeon grunt: readable silhouette, exported without a texture for now."]],
            ['pierre-tombale', 'Pierre tombale', ['fr' => 'Décor', 'en' => 'Environment'], '2 148', '256 × 64', $no, '#a6b0c0', ['Décor', 'Voxel', 'Pixel art'],
                ['fr' => "Pierre tombale moussue pour cimetières et cryptes, texturée en pixel art.",
                 'en' => "Mossy gravestone for graveyards and crypts, pixel-art textured."]],
            ['coffre-foret-n1', 'Coffre de la forêt · niv. 1', $chest, '852', '128 × 64', $anim('Animé — idle, ouverture, fermeture', 'Animated — idle, open, close'), '#7be3a0', ['Coffre', 'Forêt', 'Niveau 1'],
                ['fr' => "Coffre en bois de la forêt, premier niveau de rareté, avec ses animations d'idle, d'ouverture et de fermeture.",
                 'en' => "Forest wooden chest, first rarity tier, with idle, open and close animations."]],
            ['coffre-foret-n2', 'Coffre de la forêt · niv. 2', $chest, '1 692', '128 × 128', $anim('Animé — idle, ouverture, fermeture', 'Animated — idle, open, close'), '#7be3a0', ['Coffre', 'Forêt', 'Niveau 2'],
                ['fr' => "Deuxième niveau du coffre de la forêt : plus de détails, de ferrures et de volume.",
                 'en' => "Second tier of the forest chest: more detail, more metalwork, more volume."]],
            ['coffre-foret-n3', 'Coffre de la forêt · niv. 3', $chest, '3 384', '256 × 64', $anim('Animé — idle, ouverture, fermeture, ouvert', 'Animated — idle, open, close, opened'), '#7be3a0', ['Coffre', 'Forêt', 'Niveau 3'],
                ['fr' => "Niveau maximal du coffre de la forêt, avec en plus une animation de coffre resté ouvert.",
                 'en' => "Top tier of the forest chest, with an extra loop for the chest left open."]],
            ['coffre-pierre-n1', 'Coffre de pierre · niv. 1', $chest, '720', '128 × 64', $anim('Animé — idle, ouverture, fermeture', 'Animated — idle, open, close'), '#b8c0cc', ['Coffre', 'Pierre', 'Niveau 1'],
                ['fr' => "Coffre de pierre des ruines, premier niveau, avec animations d'idle, d'ouverture et de fermeture.",
                 'en' => "Stone chest from the ruins, first tier, with idle, open and close animations."]],
            ['coffre-pierre-n2', 'Coffre de pierre · niv. 2', $chest, '1 488', '128 × 64', $anim('Animé — idle, ouverture, fermeture', 'Animated — idle, open, close'), '#b8c0cc', ['Coffre', 'Pierre', 'Niveau 2'],
                ['fr' => "Deuxième niveau du coffre de pierre : blocs plus travaillés et gravures.",
                 'en' => "Second tier of the stone chest: more worked blocks and engravings."]],
            ['coffre-pierre-n3', 'Coffre de pierre · niv. 3', $chest, '2 556', '256 × 64', $anim('Animé — idle, ouverture, fermeture, ouvert', 'Animated — idle, open, close, opened'), '#b8c0cc', ['Coffre', 'Pierre', 'Niveau 3'],
                ['fr' => "Niveau maximal du coffre de pierre, avec animation de coffre resté ouvert.",
                 'en' => "Top tier of the stone chest, with an extra loop for the chest left open."]],
            ['clef-foret-n1', 'Clé de la forêt · niv. 1', $prop, '180', '64 × 16', $no, '#7be3a0', ['Clé', 'Forêt', 'Niveau 1'],
                ['fr' => "Clé du coffre de la forêt, niveau 1 : une poignée de cubes pour un objet de quête lisible de loin.",
                 'en' => "Forest chest key, tier 1: a handful of cubes for a quest item readable from afar."]],
            ['clef-foret-n2', 'Clé de la forêt · niv. 2', $prop, '420', '64 × 16', $no, '#7be3a0', ['Clé', 'Forêt', 'Niveau 2'],
                ['fr' => "Clé de la forêt, niveau 2 : panneton et tête plus ouvragés.",
                 'en' => "Forest key, tier 2: more elaborate bit and bow."]],
            ['clef-foret-n3', 'Clé de la forêt · niv. 3', $prop, '804', '64 × 32', $no, '#7be3a0', ['Clé', 'Forêt', 'Niveau 3'],
                ['fr' => "Clé de la forêt, niveau maximal : la plus détaillée de la série.",
                 'en' => "Forest key, top tier: the most detailed of the series."]],
            ['clef-pierre-n1', 'Clé de pierre · niv. 1', $prop, '204', '64 × 16', $no, '#b8c0cc', ['Clé', 'Pierre', 'Niveau 1'],
                ['fr' => "Clé du coffre de pierre, niveau 1.",
                 'en' => "Stone chest key, tier 1."]],
            ['clef-pierre-n2', 'Clé de pierre · niv. 2', $prop, '348', '64 × 16', $no, '#b8c0cc', ['Clé', 'Pierre', 'Niveau 2'],
                ['fr' => "Clé de pierre, niveau 2 : gravures et volumes supplémentaires.",
                 'en' => "Stone key, tier 2: extra engravings and volumes."]],
            ['clef-pierre-n3', 'Clé de pierre · niv. 3', $prop, '624', '64 × 32', $no, '#b8c0cc', ['Clé', 'Pierre', 'Niveau 3'],
                ['fr' => "Clé de pierre, niveau maximal de la série.",
                 'en' => "Stone key, top tier of the series."]],
        ];

        $models = array_map(fn ($r) => $base + [
            'slug' => $r[0], 'src' => 'models/' . $r[0] . '.lwm', 'name' => $r[1], 'category' => $r[2],
            'tris' => $r[3], 'textures' => $r[4], 'rig' => $r[5], 'accent' => $r[6], 'tags' => $r[7], 'desc' => $r[8],
        ], $rows);

        // Scène réelle du dépôt (public/GLB/dirt_road_forest.glb, 16 Mo, compressée Meshopt) :
        // 'load' => 'click' la garde hors du chargement initial.
        $models[] = [
            'slug' => 'foret', 'name' => 'Route forestière', 'demo' => 'knot', 'src' => 'GLB/dirt_road_forest.glb', 'load' => 'click',
            'tools' => ['blender'], 'category' => ['fr' => 'Environnement', 'en' => 'Environment'],
            'tris' => '292 929', 'textures' => '—', 'rig' => $no, 'year' => '2025',   // tris mesuré dans le visualiseur
            'accent' => '#9fd37a', 'tags' => ['Environnement', 'Végétation', 'Glb 16 Mo'],
            'desc' => [
                'fr' => "Scène de route forestière réellement chargée dans le visualiseur (16 Mo) — celle de la cinématique d'accueil du site.",
                'en' => "Forest-road scene actually loaded in the viewer (16 MB) — the one from the site's cinematic.",
            ],
        ];

        return array_map(fn ($m) => $m + ['url' => route('arts.model', $m['slug'])], $models);
    }

    /** Pastilles d'outils (HTML sûr : libellés et couleurs viennent de TOOLS, échappés). */
    public static function badges(array $tools): string
    {
        $out = '';
        foreach ($tools as $key) {
            $t = self::TOOLS[$key] ?? ['label' => ucfirst($key), 'color' => '#e8c26a'];
            $out .= '<span class="ar-badge" style="--t:' . e($t['color']) . '"><i></i>' . e($t['label']) . '</span>';
        }
        return $out;
    }

    /**
     * Menu de la branche Arts : « À propos » (arts.about) et « Modèles 3D » (arts.models) ;
     * les fiches se rattachent à ce dernier.
     * external : toutes les pages Arts chargent leur JS (visualiseur 3D, filtre, vitrine) une
     * seule fois au chargement du document — Barba ne doit donc pas « swapper » entre elles,
     * le script ne se rebrancherait pas sur le nouveau contenu.
     */
    public static function artsNav(): array
    {
        return [
            ['route' => 'arts.about',  'i18n' => 'nav.about',  'keep' => true, 'external' => true],
            ['route' => 'arts.models', 'i18n' => 'nav.models', 'keep' => true, 'external' => true],
        ];
    }

    /**
     * Étapes de production d'un modèle, de l'idée à l'intégration — partagées par la page
     * « À propos » (méthode) et par les fiches de modèle.
     *
     * @return list<array{title: array<string,string>, text: array<string,string>}>
     */
    public static function steps(): array
    {
        return [
            ['title' => ['fr' => 'Blockout',       'en' => 'Blockout'],
             'text'  => ['fr' => "Volumes primaires et proportions, validés avant tout détail.",
                         'en' => "Primary volumes and proportions, validated before any detail."]],
            ['title' => ['fr' => 'Modélisation',   'en' => 'Modelling'],
             'text'  => ['fr' => "Topologie propre, budget de triangles tenu pour le moteur cible.",
                         'en' => "Clean topology, triangle budget kept for the target engine."]],
            ['title' => ['fr' => 'UV & texturing', 'en' => 'UV & texturing'],
             'text'  => ['fr' => "Dépliage, bake des cartes, matériaux PBR ou pixel art selon l'outil.",
                         'en' => "Unwrapping, map baking, PBR or pixel-art materials depending on the tool."]],
            ['title' => ['fr' => 'Export glTF',    'en' => 'glTF export'],
             'text'  => ['fr' => "Un seul .glb : Maillage, matériaux et animations réunis.",
                         'en' => "A single .glb: mesh, materials and animations together."]],
            ['title' => ['fr' => 'Intégration',    'en' => 'Integration'],
             'text'  => ['fr' => "Import dans Unity ou dans la page web, contrôle des échelles et du rendu.",
                         'en' => "Import into Unity or the web page, scale and render checks."]],
        ];
    }

    /** Slug du modèle présenté seul dans le hero de « À propos ». */
    public const HERO = 'boss-squelette-crystal';

    /**
     * Le modèle du hero de « À propos », ou null s'il a quitté le catalogue (la page affiche
     * alors une scène décorative au lieu de planter). Un modèle lourd (load => click) y reste
     * en attente de clic, sans téléchargement.
     *
     * @return array<string,mixed>|null
     */
    public static function hero(): ?array
    {
        return self::find(self::HERO);
    }

    /**
     * Les $n modèles mis en avant dans la section Modèles de « À propos » : les modèles
     * « légers » d'abord (ils tournent en direct dans leur carte), complétés au besoin par
     * des modèles lourds — dont la carte reste en attente de clic, sans aucun téléchargement.
     * Le modèle du hero, déjà mis en scène en haut de page, ne passe qu'en dernier recours.
     *
     * @return list<array<string,mixed>>
     */
    public static function featured(int $n = 3): array
    {
        $light = $heavy = $hero = [];
        foreach (self::all() as $m) {
            if ($m['slug'] === self::HERO) $hero[] = $m;
            elseif (($m['load'] ?? 'auto') === 'click') $heavy[] = $m;
            else $light[] = $m;
        }
        return array_slice(array_merge($light, $heavy, $hero), 0, $n);
    }

    /** @return array<string,mixed>|null */
    public static function find(string $slug): ?array
    {
        foreach (self::all() as $m) {
            if ($m['slug'] === $slug) return $m;
        }
        return null;
    }
}
