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

require_login();
$system_ctx = context::instance_by_id(5);
require_capability('moodle/course:create', $system_ctx); //check if we can create a course in the system context

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/uca_create_courses/simplecreate.php'));
$PAGE->set_title(get_string('addnewcourse'));
$PAGE->set_heading(get_string('addnewcourse'));
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('course'));
$PAGE->navbar->add(get_string('categoriesandcoures'), new moodle_url('/course/management.php'));
$PAGE->navbar->add(get_string('addnewcourse'));

$PAGE->requires->jquery();
$PAGE->requires->js('/local/uca_create_courses/js/show_categories.js', true);

$category_def = get_default_category_form(null);
$courserenderer = $PAGE->get_renderer('core', 'course');
$tree = categories_list($courserenderer, null);

$submit_url = new moodle_url('/local/uca_create_courses/simplecreate.php');
$form = new creation_form($submit_url, array('tree' => $tree, 'default_category' => $category_def));
echo $OUTPUT->header();

if ($form->is_cancelled()) {
    //Cancel
    redirect(new moodle_url('/local/uca_create_courses/simplecreate.php'));
    exit;
}
else {
    if ($datas = $form->get_data()) {
        //Form process
        if ($datas->submitbutton) {
//            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $CFG->maxbytes, 'trusttext' => false, 'noclean' => true);
//            $editoroptions['context'] = $system_ctx;
//            $editoroptions['subdirs'] = 0;

            $catcontext = context_coursecat::instance($datas->category);
            require_capability('moodle/course:create', $catcontext);

            $datas->startdate = time();
            if ($datas->format == "social") {
                $datas->numdiscussions = $datas->numsections;
                unset($datas->numsections);
                unset($datas->activityttype);
            }
            if ($datas->format == "singleactivity") {
                unset($datas->numsections);
            } else {
                unset($datas->activityttype);
            }
            $course = create_course($datas); //course creation
            $coursecontext = context_course::instance($course->id);

            //We automatically give the manager role (or the role defined when we create a course) for the current user in order to he can manage the new course.
            //Optionnal
            enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);

            require_capability('enrol/manual:enrol', $coursecontext);
            //Redirect to course page
            redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
        }
    }
    else {
        //Form display
        $form->display();
    }
}

echo $OUTPUT->footer();