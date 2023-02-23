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
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/lesson/locallib.php');
    $yesno = array(0 => get_string('no'), 1 => get_string('yes'));

    // Introductory explanation that all the settings are defaults for the add lesson form.
    $settings->add(new admin_setting_heading('mod_lesson/lessonintro', '', get_string('configintro', 'lesson')));

    // Appearance settings.
    $settings->add(new admin_setting_heading('mod_lesson/appearance', get_string('appearance'), ''));

    // Media file popup settings.
    $setting = new admin_setting_configempty('mod_lesson/mediafile', get_string('mediafile', 'lesson'),
            get_string('mediafile_help', 'lesson'));

    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, true);
    $settings->add($setting);

    $settings->add(new admin_setting_configtext('mod_lesson/mediawidth', get_string('mediawidth', 'lesson'),
            get_string('configmediawidth', 'lesson'), 640, PARAM_INT));

    $settings->add(new admin_setting_configtext('mod_lesson/mediaheight', get_string('mediaheight', 'lesson'),
            get_string('configmediaheight', 'lesson'), 480, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('mod_lesson/mediaclose', get_string('mediaclose', 'lesson'),
            get_string('configmediaclose', 'lesson'), false, PARAM_TEXT));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/progressbar',
        get_string('progressbar', 'lesson'), get_string('progressbar_help', 'lesson'),
        array('value' => 0, 'adv' => false), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/ongoing',
        get_string('ongoing', 'lesson'), get_string('ongoing_help', 'lesson'),
        array('value' => 0, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/displayleftmenu',
        get_string('displayleftmenu', 'lesson'), get_string('displayleftmenu_help', 'lesson'),
        array('value' => 0, 'adv' => false), $yesno));

    $percentage = array();
    for ($i = 100; $i >= 0; $i--) {
        $percentage[$i] = $i.'%';
    }
    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/displayleftif',
        get_string('displayleftif', 'lesson'), get_string('displayleftif_help', 'lesson'),
        array('value' => 0, 'adv' => true), $percentage));

    // Slideshow settings.
    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/slideshow',
        get_string('slideshow', 'lesson'), get_string('slideshow_help', 'lesson'),
        array('value' => 0, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configtext('mod_lesson/slideshowwidth', get_string('slideshowwidth', 'lesson'),
            get_string('configslideshowwidth', 'lesson'), 640, PARAM_INT));

    $settings->add(new admin_setting_configtext('mod_lesson/slideshowheight', get_string('slideshowheight', 'lesson'),
            get_string('configslideshowheight', 'lesson'), 480, PARAM_INT));

    $settings->add(new admin_setting_configtext('mod_lesson/slideshowbgcolor', get_string('slideshowbgcolor', 'lesson'),
            get_string('configslideshowbgcolor', 'lesson'), '#FFFFFF', PARAM_TEXT));

    $numbers = array();
    for ($i = 20; $i > 1; $i--) {
        $numbers[$i] = $i;
    }

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/maxanswers',
        get_string('maximumnumberofanswersbranches', 'lesson'), get_string('maximumnumberofanswersbranches_help', 'lesson'),
        array('value' => '5', 'adv' => true), $numbers));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/defaultfeedback',
        get_string('displaydefaultfeedback', 'lesson'), get_string('displaydefaultfeedback_help', 'lesson'),
        array('value' => 0, 'adv' => true), $yesno));

    $setting = new admin_setting_configempty('mod_lesson/activitylink', get_string('activitylink', 'lesson'),
        '');

    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, true);
    $settings->add($setting);

    // Availability settings.
    $settings->add(new admin_setting_heading('mod_lesson/availibility', get_string('availability'), ''));

    $settings->add(new admin_setting_configduration_with_advanced('mod_lesson/timelimit',
        get_string('timelimit', 'lesson'), get_string('configtimelimit_desc', 'lesson'),
            array('value' => '0', 'adv' => false), 60));

    $settings->add(new admin_setting_configcheckbox_with_advanced('mod_lesson/password',
        get_string('password', 'lesson'), get_string('configpassword_desc', 'lesson'),
        array('value' => 0, 'adv' => true)));

    // Flow Control.
    $settings->add(new admin_setting_heading('lesson/flowcontrol', get_string('flowcontrol', 'lesson'), ''));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/modattempts',
        get_string('modattempts', 'lesson'), get_string('modattempts_help', 'lesson'),
        array('value' => 0, 'adv' => false), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/displayreview',
        get_string('displayreview', 'lesson'), get_string('displayreview_help', 'lesson'),
        array('value' => 0, 'adv' => false), $yesno));

    $attempts = ['0' => get_string('unlimited')];
    for ($i = 10; $i > 0; $i--) {
        $attempts[$i] = $i;
    }

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/maximumnumberofattempts',
        get_string('maximumnumberofattempts', 'lesson'), get_string('maximumnumberofattempts_help', 'lesson'),
        array('value' => '1', 'adv' => false), $attempts));

    $defaultnextpages = array();
    $defaultnextpages[0] = get_string("normal", "lesson");
    $defaultnextpages[LESSON_UNSEENPAGE] = get_string("showanunseenpage", "lesson");
    $defaultnextpages[LESSON_UNANSWEREDPAGE] = get_string("showanunansweredpage", "lesson");

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/defaultnextpage',
            get_string('actionaftercorrectanswer', 'lesson'), '',
            array('value' => 0, 'adv' => true), $defaultnextpages));

    $pages = array();
    for ($i = 100; $i >= 0; $i--) {
        $pages[$i] = $i;
    }
    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/numberofpagestoshow',
        get_string('numberofpagestoshow', 'lesson'), get_string('numberofpagestoshow_help', 'lesson'),
        array('value' => '1', 'adv' => true), $pages));

    // Grade.
    $settings->add(new admin_setting_heading('lesson/grade', get_string('gradenoun'), ''));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/practice',
        get_string('practice', 'lesson'), get_string('practice_help', 'lesson'),
        array('value' => 0, 'adv' => false), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/customscoring',
        get_string('customscoring', 'lesson'), get_string('customscoring_help', 'lesson'),
        array('value' => 1, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/retakesallowed',
        get_string('retakesallowed', 'lesson'), get_string('retakesallowed_help', 'lesson'),
        array('value' => 0, 'adv' => false), $yesno));

    $options = array();
    $options[0] = get_string('usemean', 'lesson');
    $options[1] = get_string('usemaximum', 'lesson');

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/handlingofretakes',
        get_string('handlingofretakes', 'lesson'), get_string('handlingofretakes_help', 'lesson'),
        array('value' => 0, 'adv' => true), $options));

    $settings->add(new admin_setting_configselect_with_advanced('mod_lesson/minimumnumberofquestions',
        get_string('minimumnumberofquestions', 'lesson'), get_string('minimumnumberofquestions_help', 'lesson'),
        array('value' => 0, 'adv' => true), $pages));

}
