<?php  // $Id$
/**
 * settingstree.php - Tells the admin menu that there are sub menu pages to
 * include for this activity.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

require_once($CFG->dirroot . '/mod/quiz/lib.php');

// First get a list of quiz reports with there own settings pages. If there none,
// we use a simpler overall menu structure.
$reportsbyname = array();
if ($reports = get_list_of_plugins('mod/quiz/report')) {
    foreach ($reports as $report) {
        if (file_exists($CFG->dirroot . "/mod/quiz/report/$report/settings.php")) {
            $strreportname = get_string($report . 'report', 'quiz_'.$report);
            // Deal with reports which are lacking the language string
            if ($strreportname[0] == '[') {
                $textlib = textlib_get_instance();
                $strreportname = $textlib->strtotitle($report . ' report');
            }
            $reportsbyname[$strreportname] = $report;
        }
    }
    ksort($reportsbyname);
}

// Create the quiz settings page.
if (empty($reportsbyname)) {
    $pagetitle = get_string('modulename', 'quiz');
} else {
    $pagetitle = get_string('generalsettings', 'admin');
}
$quizsettings = new admin_settingpage('modsettingquiz', $pagetitle, 'moodle/site:config');

// Introductory explanation that all the settings are defaults for the add quiz form.
$quizsettings->add(new admin_setting_heading('quizintro', '', get_string('configintro', 'quiz')));

// timelimit
$quizsettings->add(new admin_setting_quiz_text('timelimit',
        get_string('timelimit', 'quiz'), get_string('configtimelimit', 'quiz'),
        array('value' => '0', 'fix' => false), PARAM_INT));

// delay1 and delay2
$timedelayoptions = array();
$timedelayoptions[0] = get_string('none');
$timedelayoptions[1800] = get_string('numminutes', '', 30);
$timedelayoptions[3600] = get_string('numminutes', '', 60);
for($i=2; $i<=23; $i++) {
    $seconds  = $i*3600;
    $timedelayoptions[$seconds] = get_string('numhours', '', $i);
}
$timedelayoptions[86400] = get_string('numhours', '', 24);
for($i=2; $i<=7; $i++) {
     $seconds = $i*86400;
     $timedelayoptions[$seconds] = get_string('numdays', '', $i);
}
$quizsettings->add(new admin_setting_quiz_combo('delay1',
        get_string('delay1', 'quiz'), get_string('configdelay1', 'quiz'),
        array('value' => 0, 'fix' => false), $timedelayoptions));
$quizsettings->add(new admin_setting_quiz_combo('delay2',
        get_string('delay2', 'quiz'), get_string('configdelay2', 'quiz'),
        array('value' => 0, 'fix' => false), $timedelayoptions));

// questionsperpage
$perpage = array();
$perpage[0] = get_string('never');
$perpage[1] = get_string('aftereachquestion', 'quiz');
for ($i = 2; $i <= 50; ++$i) {
    $perpage[$i] = get_string('afternquestions', 'quiz', $i);
}
$quizsettings->add(new admin_setting_quiz_combo('questionsperpage',
        get_string('newpageevery', 'quiz'), get_string('confignewpageevery', 'quiz'),
        array('value' => 1, 'fix' => false), $perpage));

// shufflequestions
$quizsettings->add(new admin_setting_quiz_yesno('shufflequestions',
        get_string('shufflequestions', 'quiz'), get_string('configshufflequestions', 'quiz'),
        array('value' => 0, 'fix' => false)));

// shuffleanswers
$quizsettings->add(new admin_setting_quiz_yesno('shuffleanswers',
        get_string('shufflewithin', 'quiz'), get_string('configshufflewithin', 'quiz'),
        array('value' => 1, 'fix' => false)));

// attempts
$options = array(get_string('unlimited'));
for ($i = 1; $i <= 6; $i++) {
    $options[$i] = $i;
}
$quizsettings->add(new admin_setting_quiz_combo('attempts',
        get_string('attemptsallowed', 'quiz'), get_string('configattemptsallowed', 'quiz'),
        array('value' => 0, 'fix' => false), $options));

// attemptonlast
$quizsettings->add(new admin_setting_quiz_yesno('attemptonlast',
        get_string('eachattemptbuildsonthelast', 'quiz'), get_string('configeachattemptbuildsonthelast', 'quiz'),
        array('value' => 0, 'fix' => false)));

// optionflags
$quizsettings->add(new admin_setting_quiz_yesno('optionflags',
        get_string('adaptive', 'quiz'), get_string('configadaptive', 'quiz'),
        array('value' => 1, 'fix' => false)));

// maximumgrade
$maxgradesetting = new admin_setting_configtext('maximumgrade',
        get_string('maximumgrade'), get_string('configmaximumgrade', 'quiz'), 10, PARAM_INT);
$maxgradesetting->plugin = 'quiz';
$quizsettings->add($maxgradesetting);

// grademethod
$quizsettings->add(new admin_setting_quiz_combo('grademethod',
        get_string('grademethod', 'quiz'), get_string('configgrademethod', 'quiz'),
        array('value' => QUIZ_GRADEHIGHEST, 'fix' => false), quiz_get_grading_options()));

// penaltyscheme
$quizsettings->add(new admin_setting_quiz_yesno('penaltyscheme',
        get_string('penaltyscheme', 'quiz'), get_string('configpenaltyscheme', 'quiz'),
        array('value' => 1, 'fix' => false)));

// decimalpoints
$options = array();
for ($i = 0; $i <= 5; $i++) {
    $options[$i] = $i;
}
$quizsettings->add(new admin_setting_quiz_combo('decimalpoints',
        get_string('decimaldigits', 'quiz'), get_string('configdecimaldigits', 'quiz'),
        array('value' => 2, 'fix' => false), $options));

// review
$quizsettings->add(new admin_setting_quiz_reviewoptions('review',
        get_string('reviewoptions', 'quiz'), get_string('configreviewoptions', 'quiz'),
        array('value' => 0x3fffffff, 'fix' => false)));

// popup
$quizsettings->add(new admin_setting_quiz_yesno('popup',
        get_string('popup', 'quiz'), get_string('configpopup', 'quiz'),
        array('value' => 0, 'fix' => false)));

// quizpassword
$quizsettings->add(new admin_setting_quiz_text('password',
        get_string('requirepassword', 'quiz'), get_string('configrequirepassword', 'quiz'),
        array('value' => '', 'fix' => false), PARAM_TEXT));

// subnet
$quizsettings->add(new admin_setting_quiz_text('subnet',
        get_string('requiresubnet', 'quiz'), get_string('configrequiresubnet', 'quiz'),
        array('value' => '', 'fix' => false), PARAM_TEXT));

/// Now, depending on whether any reports have their own settings page, add
/// the quiz setting page to the appropriate place in the tree.
if (empty($reportsbyname)) {
    $ADMIN->add('modsettings', $quizsettings);
} else {
    $ADMIN->add('modsettings', new admin_category('modsettingsquizcat', get_string('modulename', 'quiz'), !$module->visible));
    $ADMIN->add('modsettingsquizcat', $quizsettings);

/// Add the report pages for the settings.php files in sub directories of mod/quiz/report
    foreach ($reportsbyname as $strreportname => $report) {
        $reportname = $report;
        $settings = new admin_settingpage('modsettingsquizcat'.$reportname, $strreportname, 'moodle/site:config', !$module->visible);
        if ($ADMIN->fulltree) {
            include($CFG->dirroot."/mod/quiz/report/$reportname/settings.php");
        }
        $ADMIN->add('modsettingsquizcat', $settings);
    }
}
?>
