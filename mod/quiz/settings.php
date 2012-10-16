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
 * Administration settings definitions for the quiz module.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2010 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/settingslib.php');

// First get a list of quiz reports with there own settings pages. If there none,
// we use a simpler overall menu structure.
$reports = get_plugin_list_with_file('quiz', 'settings.php', false);
$reportsbyname = array();
foreach ($reports as $report => $reportdir) {
    $strreportname = get_string($report . 'report', 'quiz_'.$report);
    $reportsbyname[$strreportname] = $report;
}
ksort($reportsbyname);

// Create the quiz settings page.
if (empty($reportsbyname)) {
    $pagetitle = get_string('modulename', 'quiz');
} else {
    $pagetitle = get_string('generalsettings', 'admin');
}
$quizsettings = new admin_settingpage('modsettingquiz', $pagetitle, 'moodle/site:config');

// Introductory explanation that all the settings are defaults for the add quiz form.
$quizsettings->add(new admin_setting_heading('quizintro', '', get_string('configintro', 'quiz')));

// Time limit.
$quizsettings->add(new admin_setting_configtext_with_advanced('quiz/timelimit',
        get_string('timelimitsec', 'quiz'), get_string('configtimelimitsec', 'quiz'),
        array('value' => '0', 'fix' => false), PARAM_INT));

// What to do with overdue attempts.
$quizsettings->add(new mod_quiz_admin_setting_overduehandling('quiz/overduehandling',
        get_string('overduehandling', 'quiz'), get_string('overduehandling_desc', 'quiz'),
        array('value' => 'autoabandon', 'fix' => false), null));

// Grace period time.
$quizsettings->add(new admin_setting_configtext_with_advanced('quiz/graceperiod',
        get_string('graceperiod', 'quiz'), get_string('graceperiod_desc', 'quiz'),
        array('value' => '86400', 'fix' => false), PARAM_INT));

// Minimum grace period used behind the scenes.
$quizsettings->add(new admin_setting_configtext('quiz/graceperiodmin',
        get_string('graceperiodmin', 'quiz'), get_string('graceperiodmin_desc', 'quiz'),
        60, PARAM_INT));

// Number of attempts.
$options = array(get_string('unlimited'));
for ($i = 1; $i <= QUIZ_MAX_ATTEMPT_OPTION; $i++) {
    $options[$i] = $i;
}
$quizsettings->add(new admin_setting_configselect_with_advanced('quiz/attempts',
        get_string('attemptsallowed', 'quiz'), get_string('configattemptsallowed', 'quiz'),
        array('value' => 0, 'fix' => false), $options));

// Grading method.
$quizsettings->add(new mod_quiz_admin_setting_grademethod('quiz/grademethod',
        get_string('grademethod', 'quiz'), get_string('configgrademethod', 'quiz'),
        array('value' => QUIZ_GRADEHIGHEST, 'fix' => false), null));

// Maximum grade.
$quizsettings->add(new admin_setting_configtext('quiz/maximumgrade',
        get_string('maximumgrade'), get_string('configmaximumgrade', 'quiz'), 10, PARAM_INT));

// Shuffle questions.
$quizsettings->add(new admin_setting_configcheckbox_with_advanced('quiz/shufflequestions',
        get_string('shufflequestions', 'quiz'), get_string('configshufflequestions', 'quiz'),
        array('value' => 0, 'adv' => false)));

// Questions per page.
$perpage = array();
$perpage[0] = get_string('never');
$perpage[1] = get_string('aftereachquestion', 'quiz');
for ($i = 2; $i <= QUIZ_MAX_QPP_OPTION; ++$i) {
    $perpage[$i] = get_string('afternquestions', 'quiz', $i);
}
$quizsettings->add(new admin_setting_configselect_with_advanced('quiz/questionsperpage',
        get_string('newpageevery', 'quiz'), get_string('confignewpageevery', 'quiz'),
        array('value' => 1, 'fix' => false), $perpage));

// Navigation method.
$quizsettings->add(new admin_setting_configselect_with_advanced('quiz/navmethod',
        get_string('navmethod', 'quiz'), get_string('confignavmethod', 'quiz'),
        array('value' => QUIZ_NAVMETHOD_FREE, 'adv' => true), quiz_get_navigation_options()));

// Shuffle within questions.
$quizsettings->add(new admin_setting_configcheckbox_with_advanced('quiz/shuffleanswers',
        get_string('shufflewithin', 'quiz'), get_string('configshufflewithin', 'quiz'),
        array('value' => 1, 'adv' => false)));

// Preferred behaviour.
$quizsettings->add(new admin_setting_question_behaviour('quiz/preferredbehaviour',
        get_string('howquestionsbehave', 'question'), get_string('howquestionsbehave_desc', 'quiz'),
        'deferredfeedback'));

// Each attempt builds on last.
$quizsettings->add(new admin_setting_configcheckbox_with_advanced('quiz/attemptonlast',
        get_string('eachattemptbuildsonthelast', 'quiz'),
        get_string('configeachattemptbuildsonthelast', 'quiz'),
        array('value' => 0, 'adv' => true)));

// Review options.
$quizsettings->add(new admin_setting_heading('reviewheading',
        get_string('reviewoptionsheading', 'quiz'), ''));
foreach (mod_quiz_admin_review_setting::fields() as $field => $name) {
    $default = mod_quiz_admin_review_setting::all_on();
    $forceduring = null;
    if ($field == 'attempt') {
        $forceduring = true;
    } else if ($field == 'overallfeedback') {
        $default = $default ^ mod_quiz_admin_review_setting::DURING;
        $forceduring = false;
    }
    $quizsettings->add(new mod_quiz_admin_review_setting('quiz/review' . $field,
            $name, '', $default, $forceduring));
}

// Show the user's picture.
$quizsettings->add(new admin_setting_configcheckbox_with_advanced('quiz/showuserpicture',
        get_string('showuserpicture', 'quiz'), get_string('configshowuserpicture', 'quiz'),
        array('value' => 0, 'adv' => false)));

// Decimal places for overall grades.
$options = array();
for ($i = 0; $i <= QUIZ_MAX_DECIMAL_OPTION; $i++) {
    $options[$i] = $i;
}
$quizsettings->add(new admin_setting_configselect_with_advanced('quiz/decimalpoints',
        get_string('decimalplaces', 'quiz'), get_string('configdecimalplaces', 'quiz'),
        array('value' => 2, 'fix' => false), $options));

// Decimal places for question grades.
$options = array(-1 => get_string('sameasoverall', 'quiz'));
for ($i = 0; $i <= QUIZ_MAX_Q_DECIMAL_OPTION; $i++) {
    $options[$i] = $i;
}
$quizsettings->add(new admin_setting_configselect_with_advanced('quiz/questiondecimalpoints',
        get_string('decimalplacesquestion', 'quiz'),
        get_string('configdecimalplacesquestion', 'quiz'),
        array('value' => -1, 'fix' => true), $options));

// Show blocks during quiz attempts.
$quizsettings->add(new admin_setting_configcheckbox_with_advanced('quiz/showblocks',
        get_string('showblocks', 'quiz'), get_string('configshowblocks', 'quiz'),
        array('value' => 0, 'adv' => true)));

// Password.
$quizsettings->add(new admin_setting_configtext_with_advanced('quiz/password',
        get_string('requirepassword', 'quiz'), get_string('configrequirepassword', 'quiz'),
        array('value' => '', 'fix' => true), PARAM_TEXT));

// IP restrictions.
$quizsettings->add(new admin_setting_configtext_with_advanced('quiz/subnet',
        get_string('requiresubnet', 'quiz'), get_string('configrequiresubnet', 'quiz'),
        array('value' => '', 'fix' => true), PARAM_TEXT));

// Enforced delay between attempts.
$quizsettings->add(new admin_setting_configtext_with_advanced('quiz/delay1',
        get_string('delay1st2nd', 'quiz'), get_string('configdelay1st2nd', 'quiz'),
        array('value' => 0, 'fix' => true), PARAM_INT));
$quizsettings->add(new admin_setting_configtext_with_advanced('quiz/delay2',
        get_string('delaylater', 'quiz'), get_string('configdelaylater', 'quiz'),
        array('value' => 0, 'fix' => true), PARAM_INT));

// Browser security.
$quizsettings->add(new mod_quiz_admin_setting_browsersecurity('quiz/browsersecurity',
        get_string('showinsecurepopup', 'quiz'), get_string('configpopup', 'quiz'),
        array('value' => '-', 'adv' => true), null));

// Allow user to specify if setting outcomes is an advanced setting
if (!empty($CFG->enableoutcomes)) {
    $quizsettings->add(new admin_setting_configcheckbox('quiz/outcomes_adv',
        get_string('outcomesadvanced', 'quiz'), get_string('configoutcomesadvanced', 'quiz'),
        '0'));
}

// Now, depending on whether any reports have their own settings page, add
// the quiz setting page to the appropriate place in the tree.
if (empty($reportsbyname)) {
    $ADMIN->add('modsettings', $quizsettings);
} else {
    $ADMIN->add('modsettings', new admin_category('modsettingsquizcat',
            get_string('modulename', 'quiz'), $module->is_enabled() === false));
    $ADMIN->add('modsettingsquizcat', $quizsettings);

    // Add the report pages for the settings.php files in sub directories of mod/quiz/report.
    foreach ($reportsbyname as $strreportname => $report) {
        $reportname = $report;

        $settings = new admin_settingpage('modsettingsquizcat'.$reportname,
                $strreportname, 'moodle/site:config', $module->is_enabled() === false);
        if ($ADMIN->fulltree) {
            include($CFG->dirroot . "/mod/quiz/report/$reportname/settings.php");
        }
        $ADMIN->add('modsettingsquizcat', $settings);
    }
}

$settings = null; // We do not want standard settings link.
