Moodle UCA - Course creation simplfied form
==================================
Plugin project to create a course with a simplified form where users only enter main informations about the course.

Requirements
------------
- Moodle 3.2 or later.<br/>
-> Tests on Moodle 3.3 to 3.11.0 versions and with a basic moodle installation (some adjustements may be needed if you use some additionnal plugins espacially course formats plugins).
- Bootstrap support in your moodle theme.

Installation
------------
1. Local plugin installation

- Git way:
> git clone https://github.com/andurif/moodle-local_uca_create_courses.git local/uca_create_courses

- Download way:
> Download the zip from <a href="https://github.com/andurif/moodle-local_uca_create_courses/archive/refs/heads/master.zip">https://github.com/andurif/moodle-local_uca_create_courses/archive/refs/heads/master.zip</a>, unzip it in local/ folder and rename it "uca_create_courses" if necessary.

You can change the plugin folder or project name but these changes must be report in the plugin code (especially urls calls).

2. Then visit your Admin Notifications page to complete the installation.

3. To have a good display of the course categories tree we had to update some core Moodle functions in the course/renderer.php file (This is an improvement to do because this action may be needed after every Moodle update).<br/>
(Risk to have an error "Coding error detected, it must be fixed by a programmer : Unknown method called against theme_boost\output\core\course_renderer :: coursecat_tree").
  
 * +/- l.1670: change function coursecat_tree() visibility to public<br/><br/>
  ```php
  <?php
 protected function coursecat_tree(coursecat_helper $chelper, $coursecat) { ... }
 
 // To be replaced by 
 
 public function coursecat_tree(coursecat_helper $chelper, $coursecat) { ... }
  ```
  
* +/- l. 1862 et 1866: change limit to null rather than $CFG->courseperpage in the coursecat_ajax() function.<br/>
    You may declare $CFG->courseperpage = null in the config.php file but this change will be used globally in moodle (links "view more" and "view less" for the course categories will not be visible).<br/><br/>
    <i>* line numbers shown here may be different in function of your moodle version.</i>
    
```php
  <?php
    // content of the coursecat_ajax() function...
    $coursedisplayoptions = array(
        'limit' => $CFG->coursesperpage,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1))
    );
    $catdisplayoptions = array(
        'limit' => $CFG->coursesperpage,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1))
    );
 
    // To be replaced by  
 
    // content of the coursecat_ajax() function...
    $coursedisplayoptions = array(
        'limit' => null,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1))
    );
    $catdisplayoptions = array(
        'limit' => null,
        'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1))
    );
  ```

Presentation / Usages
------

This plugin aim is to let users create easily a course without use all given options in the Moodle form.<br/>

Our idea was to first choose a type for the course to create. For example, in our case we separate "program", "formation", "optionnal" courses... For each type a course category is defined to put by default these new courses. Other specific actions to a type can be implemented but not seen in this plugin.

This selection can be removed by adjusting the plugin code. The form will only create classic courses in any course category.

#### `js/ folder`

It contents a javascript file where form interactions are defined.

#### `lang/ folder`

Specific folder with translation files.

#### `templates/ folder`

It contents a mustache template which display the form part where the user choose the type of the course to create.

#### `config.php`

Configuration file where course categories for each type are defined:
```php
<?php

$CFG->static_coursecat_id = array(
    'type1' => 1,
    'type2' => $id_category_1,
    'type3' => $id_category_2,
    'etc..' => $x,
);

// ! New version 2018101000 !
$CFG->static_types = array(
    'type1'     => array('default_category_id' => 1, 'icon' => null, 'in_form' => false),
    'type2'     => array('default_category_id' => $id_category_1, 'icon' => 'group', 'in_form' => true),
    'type3'      => array('default_category_id' => $id_category_2, 'icon' => 'assignment', 'in_form' => true),
    'etc..'     => array('default_category_id' => $x, 'icon' => 'class', 'in_form' => true),
);

```
Be careful each type defined in the configuration file also need a translation in the files of the lang/ folder.
```php
<?php

$string['choice_type_type1'] = 'Label type1 course';
```

#### `create.php and simplecreate.php` 

Php files where form datas are processed in function of the selected type (create.php) or not (simplecreate.php). 

#### `creation_form.php`

Php class corresponding to the moodle form.
It is in this file you need if necessary to display additionnal fields.

#### `lib.php`

Php file with plugin needed functions.  
A function named <i>get_course_types()</i> will construct the page which display course types. 
You need to update this function to display your specific course types. For each type you need to indicate:
 * name : name of the course type (or translation),
 * url : page link to find the form (give the url params. => Ex: array('type' => 'type1') ) 
 * icon : displayed icon on the button (using <a href="https://material.io/tools/icons/?style=baseline" target="_blank" >material design</a> icons).
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

The last plugin version upgrades the <i>get_course_types()</i> function to add more flexibility by using a the configuration file. <br/><br/>
You can directy define with the $CFG->static_types var (cf. doc config.php above) the value of the linked course category and the displayed icon for each type. A boolean <i>in_form</i> is also use to determine if this type has to be displayed in the form.<br/>
In this function the process will be more automatic and you will only have to adapt this configuration file (however urls will have to be updated in the lib.php file if there are changes in the structure of your moodle project).
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

Adapt the plugin
------

* With course types:
1. Define all the course types in the <i>config.php</i> file, choose course categories and icons corresponding to each type.
2. Define in the <i>config.php</i> file course category ids where course will be created by default. 
3. Add these course types' translations. 
4. Adapt the <i>get_course_types()</i> function of the <i>lib.php</i> file (Optionnal with the 2018101000 version).
5. Update the <i>create.php</i> file if you want to add some actions, tests, etc... in function of the selected type (Optionnal).
6. <strong>Access to the form: <a href="#">mymoodle.com/local/uca_create_courses/create.php</a>.</strong>


* Without course types:
1. Define in the <i>config.php</i> file course category ids where course will be created by default.
2. <strong>Access to the form: <a href="#">monmoodle.fr/local/uca_create_courses/simplecreate.php</a>.</strong>

About us
------
<a href="https://www.uca.fr">Université Clermont Auvergne</a> - 2021
