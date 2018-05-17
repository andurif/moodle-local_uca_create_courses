Moodle UCA - Formulaire de création de cours simplifié
==================================
Projet ayant pour but d'avoir un formulaire de création de cours simplifié permettant aux utilisateurs de ne saisir que les informations principales.

Pré-requis
------------
- Moodle en version 3.2 ou plus récente.<br/>
-> Tests effectués sur des versions 3.2, 3.3 et 3.4 et avec une installation basique de moodle (certains ajustements seront peut-être nécessaires en cas d'utilisation de plugins additionnels, notamment pour les formats de cours).
- Thème qui supporte bootstrap.

Installation basique
------------
1. Installation du plugin

- Avec git:
> git clone https://github.com/andurif/moodle-local_uca_create_courses.git local/uca_create_courses

- En téléchargement:
> Télécharger le zip depuis <a href="https://github.com/andurif/moodle-local_uca_create_courses/archive/master.zip">https://github.com/andurif/moodle-local_uca_create_courses/archive/master.zip</a>, dézipper l'archive dans le dossier local/ et renommer le si besoin le dossier en "uca_create_courses".

Vous pouvez bien sur changer le dossier dans lequel déposer le projet ou le nom du projet lui-même mais ces changements seront à reporter dans le code du plugin (notamment les urls).

2. Aller sur la page de Notifications pour terminer l'installation du plugin.

3. Pour l'affichage de l'arbre des catégories présent dans le formulaire nous avons été obligé de modifier quelques éléments du code du core de Moodle dans le fichier course/renderer.php. (Point à améliorer car les modifications peuvent être à refaire en cas de mise à jour de Moodle).<br/>
(Risque d'avoir ce type de message d'erreur sinon "Erreur de programmation détectée. Ceci doit être corrigé par un programmeur : Unknown method called against theme_boost\output\core\course_renderer :: coursecat_tree").
  > environ l.1580: faire passer la fonction coursecat_tree() en public<br/><br/>
    environ l. 1767 et 1771: limite passée à null au lieu de $CFG->courseperpage dans la fonction coursecat_ajax().<br/>
    Si besoin on peut aussi déclarer $CFG->courseperpage = null dans le fichier de config mais cela agira de manière globale sur le moodle (les liens "voir plus" et "voir moins" pour les catégories de cours ne seront du coup plus visibles).<br/><br/>
    <i>* les numéros de lignes indiqués pour les changements sont variables en fonction de la version utilisée.</i>

Présentation / Utilisation
------

Le but de ce plugin est de permettre aux utilisateurs de créer rapidement un cours sans s'occuper des nombreuses options disponibles dans le formulaire de base de Moodle.<br/>

Notre idée étant de choisir en premier lieu le type de cours à créer. Par exemple, dans notre cas nous "différencions" des cours "maquette", "formations", "à la carte", etc... Pour chacun de ces types une catégorie de cours où ranger ces cours était spécifiée par défaut. D'autres actions spéficiques à un type peuvent également être mises en place mais ne seront pas présentées dans ce plugin.

En adaptant quelques peu le plugin, cette sélection peut être supprimée pour que le formulaire ne serve qu'à créer des cours classiques dans n'importe quelle catégorie de cours.

#### `Dossier js/`

Contient un fichier javascript permettant l'interaction entre les différents éléments du formulaire.

#### `Dossier lang/`

Dossier spécifique à un plugin Moodle dans lequel on retrouve les traductions.

#### `Dossier templates/`

Contient un template mustache présentant l'écran intermédiaire permettant de choisir le type de cours que l'on souhaite créer.

#### `config.php`

Fichier de configuration dans lequel on va définir les catégories de cours relatives aux différents types:
```php
<?php
//Types definitions 
$CFG->static_coursecat_id = array(
    'type1' => 1,
    'type2' => $id_category_1,
    'type3' => $id_category_1,
    'etc..' => $x,
);
```
Attention, pour chaque type définit il faudra également ajouter une traduction dans les fichiers du dossier lang/.
```php
<?php
//Traductions
$string['choice_type:type1'] = 'Libellé du type de cours type1';
```

#### `create.php et simplecreate.php` 

Fichiers php où seront traités le formulaire et les informations saisies en prenant en compte le type de cours (create.php) ou non (simplecreate.php).

#### `creation_form.php`

Classe php correspondant au formulaire moodle crée.
C'est dans ce fichier qu'il faudra si besoin ajouter les champs supplémentaires que vous voudrez faire afficher.

#### `lib.php`

Fichier regroupant les différentes fonctions utiles au fonctionnement du plugin.
Il existe dans ce fichier une fonction <i>get_course_types()</i> qui servira à construire l'écran listant les types de cours. 
Il vous faudra modifier cette fonction pour faire afficher vos types de cours spécifiques. Pour chaque type vous devrez renseigner:
 * name : libellé de votre type (généralement la traduction),
 * url : lien de la page où se trouve le formulaire (cela revient à donner votre type en paramètres de l'url. => Ex: array('type' => 'type1') ) 
 * icon : icône qui sera affichée sur le bouton (utilisation des icones de <a href="https://material.io/tools/icons/?style=baseline" target="_blank" >material design</a>).
```php

<?php
function get_course_types()
{
    return [
        //type1
        [
            'name'  => get_string('choice_type:type1', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'type1')),
            'icon'  => 'group'
        ],
        //type2
        [
            'name'  => get_string('choice_type:type2', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'type2')),
            'icon'  => 'storage'
        ],
        //etc...
    ];
}
```

Pour adapter le plugin
------

* En utilisant les types de cours:
1. Définir les différents types de cours dans le fichier <i>config.php</i> et choisir les catégories de cours correspondantes à chaque type.
2. Définir l'identifiant de la catégorie de cours où créer vos cours par défault dans le fichier <i>config.php</i>.
3. Ajouter les différentes traductions correspondantes à ces types de cours.
4. Adapter la fonction <i>get_course_types()</i> du fichier <i>lib.php</i>.
5. Modifier le fichier <i>create.php</i> si vous souhaitez ajouter des actions, des tests, etc... en fonction du type choisi (Optionnel).
6. <strong>Accéder au formulaire via l'url <a href="#">monmoodle.fr/local/uca_create_courses/create.php</a>.</strong>


* Sans utiliser les types de cours:
1. Définir l'identifiant de la catégorie de cours où créer vos cours par défault dans le fichier <i>config.php</i>.
2. <strong>Accéder au formulaire directement via l'url <a href="#">monmoodle.fr/local/uca_create_courses/simplecreate.php</a>.</strong>

A propos
------
<a href="www.uca.fr">Université Clermont Auvergne</a> - 2018
