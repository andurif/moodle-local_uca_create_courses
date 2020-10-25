Moodle UCA - Formulaire de création de cours simplifié
==================================
Projet ayant pour but d'avoir un formulaire de création de cours simplifié permettant aux utilisateurs de ne saisir que les informations principales.

Pré-requis
------------
- Moodle en version 3.2 ou plus récente.<br/>
-> Tests effectués sur des versions 3.2 à 3.9 et avec une installation basique de moodle (certains ajustements seront peut-être nécessaires en cas d'utilisation de plugins additionnels, notamment pour les formats de cours).
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
  
 * +/- l.1580: faire passer la fonction coursecat_tree() en public<br/><br/>
  ```php
  <?php
 protected function coursecat_tree(coursecat_helper $chelper, $coursecat) { ... }
 
 //A remplacer par 
 
 public function coursecat_tree(coursecat_helper $chelper, $coursecat) { ... }
  ```
  
* +/- l. 1767 et 1771: limite passée à null au lieu de $CFG->courseperpage dans la fonction coursecat_ajax().<br/>
    Si besoin on peut aussi déclarer $CFG->courseperpage = null dans le fichier de config mais cela agira de manière globale sur le moodle (les liens "voir plus" et "voir moins" pour les catégories de cours ne seront du coup plus visibles).<br/><br/>
    <i>* les numéros de lignes indiqués pour les changements sont variables en fonction de la version utilisée.</i>
    
```php
  <?php
    //contenu de la fonction coursecat_ajax()...
    $coursedisplayoptions = array(
        'limit' => $CFG->coursesperpage,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1))
    );
    $catdisplayoptions = array(
        'limit' => $CFG->coursesperpage,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1))
    );
 
    //A remplacer par 
 
    //contenu de la fonction coursecat_ajax()...
    $coursedisplayoptions = array(
        'limit' => null,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1))
    );
    $catdisplayoptions = array(
        'limit' => null,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1))
    );
  ```

Présentation / Utilisation
------

Le but de ce plugin est de permettre aux utilisateurs de créer rapidement un cours sans s'occuper des nombreuses options disponibles dans le formulaire de base de Moodle.<br/>

Notre idée étant de choisir en premier lien un type de cours à créer. Par exemple, dans notre cas nous "différencions" des cours "maquette", "formations", "à la carte", etc... Pour chacun de ces types une catégorie de cours où ranger ces cours était spécifiée par défaut.D'autres actions spéficiques à un type peuvent également être mises en place mais ne seront pas présentées dans ce plugin.

En adaptant quelques peu le plugin, cette sélection peut être supprimée pour que le formulaire ne serve qu'à créer des cours classiques dans n'importe quelle catégorie de cours.

#### `Dossier js/`

Contient un fichier javascript permettant l'interaction entre les différents éléments du formulaire.

#### `Dossier lang/`

Dossier spécifique à un plugin Moodle dans lequel on retrouve les traductions.

#### `Dossier templates/`

Contient un template mustache présentant l'écran intermédiaire permettant de choisir le type de cours que l'on souhaite créer.

#### `config.php`

Fichier de configuration dans lequel on va définir les catégories de cours relatives à chaque type:
```php
<?php

$CFG->static_coursecat_id = array(
    'type1' => 1,
    'type2' => $id_category_1,
    'type3' => $id_category_2,
    'etc..' => $x,
);

// ! Nouveauté version 2018101000 !
$CFG->static_types = array(
    'type1'     => array('default_category_id' => 1, 'icon' => null, 'in_form' => false),
    'type2'     => array('default_category_id' => $id_category_1, 'icon' => 'group', 'in_form' => true),
    'type3'      => array('default_category_id' => $id_category_2, 'icon' => 'assignment', 'in_form' => true),
    'etc..'     => array('default_category_id' => $x, 'icon' => 'class', 'in_form' => true),
);

```
Attention, pour chaque type définit il faudra également ajouter une traduction dans les fichiers du dossier lang/.
```php
<?php

$string['choice_type_type1'] = 'Libellé du type de cours type1';
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
            'name'  => get_string('choice_type_type1', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'type1')),
            'icon'  => 'group'
        ],
        //type2
        [
            'name'  => get_string('choice_type_type2', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'type2')),
            'icon'  => 'storage'
        ],
        //etc...
    ];
}
```

<i><b>! Nouveauté version 2018101000 !</b></i>

La nouvelle version du plugin modifie quelques peu cette fonction <i>get_course_types()</i> pour apporter davantage de flexibilité en déportant une partie de la logique dans le fichier de configuration. <br/><br/>
Ainsi vous pourrez définir directement au niveau de la variable $CFG->static_types (cf. doc config.php du dessus) la valeur de la catégorie de cours liée et l'icône à afficher pour chaque type. Un booléen <i>in_form</i> a également été ajouté pour indiquer si le type doit être utilisé dans le formulaire ou non.<br/>
Au niveau de la fonction le traitement sera plus automatisé et vous n'aurez en fait qu'à adapter le fichier de configuration en fonction du besoin (les urls seront cela toujours à modifier au niveau du fichier lib.php en cas de changement dans la structure de votre projet moodle).
```php

<?php
function get_course_types()
{
    //In this example we the array defined in the config.php file
    global $CFG;
    $tabl = [];

    foreach ($CFG->static_types as $key => $type) {
        if($type['in_form']) {
            $tabl[] = [
                'name'  => get_string('choice_type_' . $key, 'local_uca_create_courses'),
                'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => $key)),
                'icon'  => $type['icon']
            ];
        }
    }

    return $tabl;
}
```

Pour adapter le plugin
------

* En utilisant les types de cours
1. Définir les différents types de cours dans le fichier <i>config.php</i>, choisir les catégories de cours et les éventuelles icônes correspondantes à chaque type.
2. Définir l'identifiant de la catégorie de cours où créer vos cours par défault dans le fichier <i>config.php</i>.
3. Ajouter les différentes traductions correspondantes à ces types de cours.
4. Adapter la fonction <i>get_course_types()</i> du fichier <i>lib.php</i> (Optionnel avec la version 2018101000).
5. Modifier le fichier <i>create.php</i> si vous souhaitez ajouter des actions, des tests, etc... en fonction du type choisi (Optionnel).
6. <strong>Accéder au formulaire via l'url <a href="#">monmoodle.fr/local/uca_create_courses/create.php</a> pour visualiser le formulaire.</strong>


* Sans utiliser les types de cours:
1. Définir l'identifiant de la catégorie de cours où créer vos cours par défault dans le fichier <i>config.php</i>.
2. <strong>Accéder au formulaire directement via l'url <a href="#">monmoodle.fr/local/uca_create_courses/simplecreate.php</a> pour visualiser le formulaire.</strong>

A propos
------
<a href="https://www.uca.fr">Université Clermont Auvergne</a> - 2018
