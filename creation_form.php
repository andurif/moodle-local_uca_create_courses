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
 * Course creation custom form.
 *
 * Custom form made to simplify the creation of a moodle course.
 *
 * @package    local_uca_create_courses
 * @author     Université Clermont Auvergne, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir. '/coursecatlib.php');

/**
 * Course creation custom form.
 *
 * Custom form made to simplify the creation of a moodle course.
 *
 * @package    local_uca_create_courses
 * @author     Université Clermont Auvergne, Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class creation_form extends moodleform
{
    protected function definition()
    {
        $mform = $this->_form;
        $courseconfig = get_config('moodlecourse');

        $mform->addElement('header','general', get_string('general', 'form'));

        $mform->addElement('text','fullname', get_string('fullnamecourse'),'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);

        $mform->addElement('text', 'shortname', get_string('shortnamecourse'), 'maxlength="100" size="50"');
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);

        $choices = array('0' => get_string('hide'), '1' => get_string('show'));
        $mform->addElement('select', 'visible', get_string('visible'), $choices);
        $mform->addHelpButton('visible', 'visible');
        $mform->setDefault('visible', '1');

        $displaylist = coursecat::make_categories_list('moodle/course:create');
        $mform->addElement('select', 'category', get_string('coursecategory'), $displaylist);
        $mform->addHelpButton('category', 'coursecategory');
        $mform->setDefault('category', $this->_customdata['default_category']->id);
        $mform->addElement('html', '<div id="tree_div" data-select-label="'.get_string('selected_category', 'local_uca_create_courses').'" 
                                        data-default-name="'.$this->_customdata['default_category']->name.'">'.$this->_customdata['tree'].'</div>');

        $mform->addElement('header', 'courseformathdr', get_string('type_format', 'plugin'));

        $courseformats = get_sorted_course_formats(true);
        $formcourseformats = array();
        foreach ($courseformats as $courseformat) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        $mform->addElement('select', 'format', get_string('format'), $formcourseformats);
        $mform->addHelpButton('format', 'format');
        $mform->setDefault('format', $courseconfig->format);

        $activities = get_module_types_names();
        foreach ($activities as $module => $name) {
            if (plugin_supports('mod', $module, FEATURE_NO_VIEW_LINK, false)) {
                unset($activities[$module]);
            }
        }
        $mform->addElement('select', 'activitytype', get_string('activitytype', 'format_singleactivity'), $activities);
        $mform->addHelpButton('activitytype', 'activitytype', 'format_singleactivity');

        $nbsections = range(0, $courseconfig->maxsections);
        $mform->addElement('select', 'numsections', get_string('numberweeks'), $nbsections);
        $mform->setDefault('numsections', $courseconfig->numsections);

        $this->add_action_buttons();
    }

}