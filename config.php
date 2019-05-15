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
 * Configuration file.
 *
 * @package    local_uca_create_courses
 * @author     Université Clermont Auvergne - Anthony Durif
 * @copyright  2018 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$CFG->static_coursecat_id = array(
    'all'       => null,
    'collab'    => 2,
    'form'      => 3,
    'carte'     => 4,
    'sandbox'   => 1,
);

$CFG->static_types = array(
    'all'       => array('default_category_id' => null, 'icon' => null, 'in_form' => false),
    'collab'    => array('default_category_id' => 2, 'icon' => 'users', 'in_form' => true),
    'form'      => array('default_category_id' => 3, 'icon' => 'book', 'in_form' => true),
    'carte'     => array('default_category_id' => 4, 'icon' => 'laptop', 'in_form' => true),
    'sandbox'   => array('default_category_id' => 1, 'icon' => 'trash', 'in_form' => true),
);

$CFG->default_category = 1;