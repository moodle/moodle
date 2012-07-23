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
 * course_overview block settings
 *
 * @package    block_course_overview
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_course_overview/defaultmaxcourses', new lang_string('defaultmaxcourses', 'block_course_overview'),
        new lang_string('defaultmaxcoursesdesc', 'block_course_overview'), 10, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('block_course_overview/forcedefaultmaxcourses', new lang_string('forcedefaultmaxcourses', 'block_course_overview'),
        new lang_string('forcedefaultmaxcoursesdesc', 'block_course_overview'), 1, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('block_course_overview/showchildren', new lang_string('showchildren', 'block_course_overview'),
        new lang_string('showchildrendesc', 'block_course_overview'), 1, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('block_course_overview/showwelcomearea', new lang_string('showwelcomearea', 'block_course_overview'),
        new lang_string('showwelcomeareadesc', 'block_course_overview'), 1, PARAM_INT));
}
