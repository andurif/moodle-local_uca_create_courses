<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Module functions definitions.
 *
 * @package    local_uca_create_courses
 * @author     Université Clermont Auvergne - Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to return the default course category to select in the creation form.
 * @param $type the type of course we want to create (types eventually defined in others files and config).
 * @return coursecat|null the category
 */
function get_default_category_form($type = null)
{
    global $DB, $CFG;

    //We search the category which corresponds to the given type
    if(in_array($type, array_keys($CFG->static_coursecat_id))) {
        return coursecat::get($CFG->static_coursecat_id[$type]);
    }

    //By default we return the category with the id added in config (most of the time 1, the automatically created category)
    return coursecat::get($CFG->default_category);
}

/**
 * Returns HTML to print tree of course categories.
 * @param $renderer the renderer.
 * @param int|stdClass|coursecat $category the course category.
 * @return string the html which corresponds to the tree.
 */
function categories_list($renderer, $category) {
    global $CFG;
    require_once($CFG->libdir. '/coursecatlib.php');

    $chelper = new coursecat_helper();

    // Prepare parameters for courses and categories lists in the tree
    $chelper->set_show_courses($renderer::COURSECAT_SHOW_COURSES_COUNT)
        ->set_attributes(array('class' => 'category-browse category-browse-'.$category))
        ->set_categories_display_options(array('visible' => true));

    return $renderer->coursecat_tree($chelper, coursecat::get($category));
}

/**
 * Function to add types of courses you want to use in the form.
 * You need at least to defined the name of the type and an url (in fact the parameter because the base url is the same for all types).
 * You can also add an icon (material icon) to add some design.
 * @return array an array with all types
 */
function get_course_types()
{
    //In this example we use 4 types of courses
    return [
        [
            'name'  => get_string('choice_type:collab', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'collab')),
            'icon'  => 'group'
        ],
        [
            'name'  => get_string('choice_type:carte', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'carte')),
            'icon'  => 'storage'
        ],
        [
            'name'  => get_string('choice_type:form', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'form')),
            'icon'  => 'assignment'
        ],
        [
            'name'  => get_string('choice_type:sandbox', 'local_uca_create_courses'),
            'url'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'sandbox')),
            'icon'  => 'delete'
        ]
    ];
}