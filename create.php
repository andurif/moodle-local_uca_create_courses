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
 * Creation of the course.
 *
 * @package    local_uca_create_courses
 * @author     Université Clermont Auvergne - Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/lib/enrollib.php');
require_once('creation_form.php');
require_once('lib.php');

$type = optional_param('type', null, PARAM_ALPHANUMEXT);

require_login();
$system_ctx = context_system::instance();
require_capability('moodle/course:create', $system_ctx); // Check if we can create a course in the system context.

$PAGE->set_context($system_ctx);
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/uca_create_courses/create.php'));
$PAGE->set_title(get_string('addnewcourse'));
$PAGE->set_heading(get_string('addnewcourse'));
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('course'));
$PAGE->navbar->add(get_string('coursecatmanagement'), new moodle_url('/course/management.php'));
$PAGE->navbar->add(get_string('addnewcourse'));

$PAGE->requires->jquery();

if ($type) {
    if (!in_array($type, array_keys($CFG->static_coursecat_id))) {
        throw new moodle_exception('invalidtype','local_uca_course', new moodle_url('/local/uca_course/create.php'));
    }

    // A type was chosen => show creation form.
    $PAGE->requires->js('/local/uca_create_courses/js/show_categories.js', true);

    $category_def = get_default_category_form($type);
    $courserenderer = $PAGE->get_renderer('core', 'course');
    $tree = categories_list($courserenderer, $category_def->id);

    $submit_url = new moodle_url('/local/uca_create_courses/create.php', array('type' => $type));
    $category_def = ($category_def->id == 0) ? core_course_category::get($CFG->default_category) : $category_def;
    $form = new creation_form($submit_url, array('tree' => $tree, 'default_category' => $category_def));

    if ($form->is_cancelled()) {
        // Cancel.
        redirect(new moodle_url('/local/uca_create_courses/create.php'));
        exit;
    } else {
        if ($datas = $form->get_data()) {
            // Form process.
            if ($datas->submitbutton) {
//                $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $CFG->maxbytes, 'trusttext' => false, 'noclean' => true);
//                $editoroptions['context'] = $system_ctx;
//                $editoroptions['subdirs'] = 0;

                $catcontext = context_coursecat::instance($datas->category);
                require_capability('moodle/course:create', $catcontext);

		        // Aditionnal potential tests in function of course type.
                $formats = get_sorted_course_formats(true);
                if (in_array("social", $formats) && $datas->format == "social") {
                    $datas->numdiscussions = $datas->numsections;
                    unset($datas->numsections);
                    unset($datas->activityttype);
                }
                if (in_array("singleactivity", $formats) && $datas->format == "singleactivity") {
                    unset($datas->numsections);
                } else {
                    unset($datas->activityttype);
                }

                $datas->startdate = time();
                $course = create_course($datas); //course creation
                $coursecontext = context_course::instance($course->id);

                // We automatically give the manager role (or the role defined when we create a course) for the current user in order to he can manage the new course.
		        // And we also check if the user has enrol rights.
                // Optionnal.
                enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);
                require_capability('enrol/manual:enrol', $coursecontext);

                // Redirect to course page.
                redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
            }
        } else {
            // Form display.
            echo $OUTPUT->header();
            $form->display();
        }
    }
} else {
    // Display the page which permits to create the course type.
    $renderer = new core_renderer($PAGE, null);

    echo $OUTPUT->header();
    echo $renderer->render_from_template('local_uca_create_courses/select_type', array(
        'types'     => get_course_types(),
        'url_all'   => new moodle_url('/local/uca_create_courses/create.php', array('type' => 'all'))
    ), null);
}

echo $OUTPUT->footer();