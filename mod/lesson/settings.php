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
 * Settings used by the lesson module, were moved from mod_edit
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/lesson/locallib.php');

    /** Slideshow settings */
    $settings->add(new admin_setting_configtext('lesson_slideshowwidth', get_string('slideshowwidth', 'lesson'),
            get_string('configslideshowwidth', 'lesson'), 640, PARAM_INT));

    $settings->add(new admin_setting_configtext('lesson_slideshowheight', get_string('slideshowheight', 'lesson'),
            get_string('configslideshowheight', 'lesson'), 480, PARAM_INT));

    $settings->add(new admin_setting_configtext('lesson_slideshowbgcolor', get_string('slideshowbgcolor', 'lesson'),
            get_string('configslideshowbgcolor', 'lesson'), '#FFFFFF', PARAM_TEXT));

    /** Media file popup settings */
    $settings->add(new admin_setting_configtext('lesson_mediawidth', get_string('mediawidth', 'lesson'),
            get_string('configmediawidth', 'lesson'), 640, PARAM_INT));

    $settings->add(new admin_setting_configtext('lesson_mediaheight', get_string('mediaheight', 'lesson'),
            get_string('configmediaheight', 'lesson'), 480, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('lesson_mediaclose', get_string('mediaclose', 'lesson'),
            get_string('configmediaclose', 'lesson'), false, PARAM_TEXT));

    /** Misc lesson settings */
    $settings->add(new admin_setting_configtext('lesson_maxhighscores', get_string('maxhighscores', 'lesson'),
            get_string('configmaxhighscores','lesson'), 10, PARAM_INT));

    /** Default lesson settings */
    $numbers = array();
    for ($i=20; $i>1; $i--) {
        $numbers[$i] = $i;
    }
    $settings->add(new admin_setting_configselect('lesson_maxanswers', get_string('maximumnumberofanswersbranches','lesson'),
            get_string('configmaxanswers', 'lesson'), 4, $numbers));

    $defaultnextpages = array();
    $defaultnextpages[0] = get_string("normal", "lesson");
    $defaultnextpages[LESSON_UNSEENPAGE] = get_string("showanunseenpage", "lesson");
    $defaultnextpages[LESSON_UNANSWEREDPAGE] = get_string("showanunansweredpage", "lesson");
    $settings->add(new admin_setting_configselect('lesson_defaultnextpage', get_string('actionaftercorrectanswer','lesson'),
            get_string('configactionaftercorrectanswer', 'lesson'), 0, $defaultnextpages));
}