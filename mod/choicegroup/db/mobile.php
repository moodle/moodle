<?php
// This file is part of the Choice group module for Moodle - http://moodle.org/
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
 * Choice group module capability definition
 *
 * @package    mod_choicegroup
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$addons = array(
    "mod_choicegroup" => array(
        "handlers" => array( // Different places where the add-on will display content.
            'coursechoicegroup' => array( // Handler unique name (can be anything)
                'displaydata' => array(
                    'title' => 'pluginname',
                    'icon' => $CFG->wwwroot . '/mod/choicegroup/pix/icon.svg',
                    'class' => '',
                ),
                'delegate' => 'CoreCourseModuleDelegate', // Delegate (where to display the link to the add-on)
                'method' => 'mobile_course_view', // Main function in \mod_choicegroup\output\mobile
                'init' => 'mobile_init',
                'offlinefunctions' => array(
                    'mobile_course_view' => array(),
                ), // Function needs caching for offline.
                'styles' => array(
                    'url' => $CFG->wwwroot . '/mod/choicegroup/styles_app.css',
                    'version' => '0.2'
                ),
                'displayrefresh' => false, // Hide default refresh button, a custom one will be used.
            )
        ),
        'lang' => array(
            array('group', 'moodle'),
            array('choice', 'choicegroup'),
            array('choicegroupsaved', 'choicegroup'),
            array('members/', 'choicegroup'),
            array('members/max', 'choicegroup'),
            array('modulename', 'choicegroup'),
            array('pluginname', 'choicegroup'),
            array('removemychoicegroup', 'choicegroup'),
            array('savemychoicegroup', 'choicegroup')
        )
    )
);
