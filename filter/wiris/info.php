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
 * MathType filter test page.
 *
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/filter/wiris/classes/pluginwrapper.php');
require_once($CFG->dirroot . '/lib/editorlib.php');

// BEGIN HELPERS FUNCTIONS.

/**
 * Checks if the WIRIS buttons are present in the toolbar of the specified editor.
 *
 * @param  string|null $editor The name of the editor. Defaults to null.
 * @return bool Returns true if the WIRIS buttons are present in the toolbar, false otherwise.
 * @throws Exception Throws an exception if the editor name is null or not supported.
 */
function check_if_wiris_button_are_in_toolbar($editor = null) {
    if (is_null($editor)) {
        throw new Exception(get_string('editornameexpected', 'filter_wiris'), 1);
    }

    switch ($editor) {
        case 'Atto':
        case 'atto':
            return check_if_wiris_button_are_in_atto_toolbar();
        case 'TinyMCE':
        case 'tinymce':
            return check_if_wiris_button_are_in_tinymce_toolbar();
        case 'tiny':
            return check_if_wiris_button_are_in_tiny_toolbar();
        default:
            throw new Exception($editor . '&nbsp;' . get_string('notsupportededitor', 'filter_wiris', 1));
    }
}

/**
 * Checks if the WIRIS buttons are present in the Atto toolbar.
 *
 * @return bool Returns true if the WIRIS buttons are present in the Atto toolbar, false otherwise.
 */
function check_if_wiris_button_are_in_atto_toolbar() {
    $configvalue = get_config('editor_atto', 'toolbar');
    return (strpos($configvalue, 'wiris') !== false);
}

/**
 * Checks if the WIRIS buttons are present in the TinyMCE toolbar.
 *
 * @return bool Returns true if the WIRIS buttons are enabled in the TinyMCE toolbar, false otherwise.
 */
function check_if_wiris_button_are_in_tinymce_toolbar() {
    $configvalue = get_config('editor_tinymce', 'disabledsubplugins');
    return (strpos($configvalue, 'tiny_mce_wiris') === false);
}

/**
 * Checks if the WIRIS buttons are enabled in the TinyMCE toolbar.
 *
 * @return bool Returns true if the WIRIS buttons are enabled, false otherwise.
 */
function check_if_wiris_button_are_in_tiny_toolbar() {
    $configvalue = get_config("tiny_wiris", 'disabled');
    return (empty($configvalue) === true);
}

/**
 * Checks for a tiny incompatibility and displays a warning if found.
 *
 * This function checks if the current version of the system is greater than or equal to 2022112807.
 * If it is, it checks if the directory '/lib/editor/tinymce/plugins/tiny_mce_wiris' exists.
 * If the directory exists, it displays a warning message using the 'tinymceincompatibility' string from the 'filter_wiris' language pack.
 *
 * @return void
 */
function warning_tiny_incompatibility() {
    global $CFG;

    if ($CFG->version < 2022112807) {
        return;
    }

    if (is_dir($CFG->dirroot . '/lib/editor/tinymce/plugins/tiny_mce_wiris')) {
        \core\notification::warning(get_string('tinymceincompatibility', 'filter_wiris'));
    }
}

/**
 * Retrieves the test text based on the given title and editor.
 *
 * @param  string      $title  The title of the test text.
 * @param  string|null $editor The editor for the test text. Default is null.
 * @return string The test text.
 */
function get_test_text($title, $editor = null) {
    if ($editor == null) {
        return get_string($title, 'filter_wiris');
    } else {
        return get_string($title, 'filter_wiris', get_string($editor, 'filter_wiris'));
    }
}

/**
 * Function to start a table for displaying filter information.
 *
 * @return void
 */
function start_table() {
    $output = html_writer::tag('h1', get_string('title', 'filter_wiris'), ['class' => 'wrs_plugin wrs_filter']);

    $output .= html_writer::start_tag('table', ['id' => 'wrs_filter_info_table', 'class' => 'wrs_plugin wrs_filter']);

    $output .= html_writer::start_tag('tr', ['class' => 'wrs_plugin wrs_filter']);
    $output .= html_writer::tag('th', 'Test', ['class' => 'wrs_plugin wrs_filter']);
    $output .= html_writer::tag('th', 'Test Outcome', ['class' => 'wrs_plugin wrs_filter']);
    $output .= html_writer::end_tag('tr');

    echo $output;
}

/**
 * Adds a table row to display test information.
 *
 * @param  mixed $test     The test name.
 * @param  mixed $outcome  The test outcome.
 * @param  int   $index    The index of the test.
 * @param  int   $subindex The subindex of the test.
 * @return void
 */
function add_table_row($test, $outcome, $index, $subindex) {
    if (is_null($outcome)) {
        // Dont show the empty infomation
        return;
    }

    $testname = "";

    if ($subindex != 0) {
        $testname = "\t" . $index . '.' . $subindex . " - " . $test;
    } else {
        $testname = $index . " - " . $test;
    }

    $outcometext = $outcome;

    // If `$outcome` is a boolean, convert into "Yes" or "No", instead of 1 or 0
    if ((is_bool($outcome))) {
        $outcometext = ($outcome) ? get_test_text('yes') : get_test_text('no');
    }

    $testnamestyle = ['class' => 'wrs_plugin wrs_filter'];
    if ($subindex == 0) {
        $testnamestyle = ['class' => 'wrs_plugin wrs_filter title'];
    }

    $output = html_writer::start_tag('tr', ['class' => 'wrs_plugin wrs_filter']);
    $output .= html_writer::tag('td', $testname, $testnamestyle);
    $output .= html_writer::tag('td', $outcometext, ['class' => 'wrs_plugin wrs_filter']);
    $output .= html_writer::end_tag('tr');

    echo $output;
}
/**
 * Function to display the installation result in a table.
 *
 * @param  bool $instalationresult The installation result (true for success, false for failure).
 * @return void
 */
function end_table($instalationresult) {
    // Prepare result text (success or failure)
    $statustext = '';
    if ($instalationresult) {
        $statustext .= html_writer::tag('span', get_test_text('success'), ['class' => 'wrs_ok wrs_plugin wrs_filter']);
    } else {
        $statustext .= html_writer::tag('span', get_test_text('failure'), ['class' => 'wrs_error wrs_plugin wrs_filter']);
    }

    // Show Instalation result
    $output = html_writer::start_tag('tr', ['class' => 'wrs_plugin wrs_filter']);
    $output .= html_writer::tag('td', get_test_text('integrationinstallation'), ['class' => 'wrs_plugin wrs_filter title']);
    $output .= html_writer::start_tag('td', ['class' => 'wrs_plugin wrs_filter']);
    $output .= $statustext;
    $output .= html_writer::end_tag('td');
    $output .= html_writer::end_tag('tr');

    // Close table
    $output .= html_writer::end_tag('table');

    $output .= html_writer::start_tag('p');
    $output .= html_writer::start_tag('br');

    echo $output;
}

// Function to increment index and subindex based on outcome
// &$current_subindex pass as reference in order to increment
/**
 * Process a table row.
 *
 * This function adds a table row to the specified test, outcome, current index, and current subindex.
 * If the outcome is not null, it increments the current subindex.
 *
 * @param  mixed $test             The test to add the row to.
 * @param  mixed $outcome          The outcome of the row.
 * @param  int   $currentindex     The current index.
 * @param  int   &$currentsubindex The current subindex.
 * @return void
 */
function process_table_row($test, $outcome, $currentindex, &$currentsubindex) {
    add_table_row($test, $outcome, $currentindex, $currentsubindex);

    if (!is_null($outcome)) {
        $currentsubindex++;
    }
}

// Reset subindex and increment index if starting a new group
// &$current_index, &$current_subindex pass as reference in order to increment or reset
/**
 * Starts a new group.
 *
 * This function is used to start a new group. It increments the current index by 1 and resets the current subindex to 0.
 *
 * @param  int $currentindex    The current index.
 * @param  int $currentsubindex The current subindex.
 * @return void
 */
function start_new_group(&$currentindex, &$currentsubindex) {
    // $current_index is not equal to 0 mean the previous test is not skipped
    if ($currentsubindex != 0) {
        $currentindex++;
    }
    $currentsubindex = 0;
}


// Functions to perform tests
// @return null|string|bool If it returns a string, it will return the requested text data (e.g., version number).
// If it returns a boolean, it will be true (test result is YES) or false (test result is NO).
// If it returns null, it means the entire test will be skipped and not displayed.
/**
 * Retrieves the Moodle version.
 *
 * @return string The Moodle version.
 */
function get_moodle_version() {
    global $CFG;
    return $CFG->release;
}

/**
 * Checks if the 'filter' plugin exists in the given array of plugins.
 *
 * @param  array $plugins The array of plugins.
 * @return bool Returns true if the 'filter' plugin exists, false otherwise.
 */
function get_exists_mt_filter($plugins) {
    return isset($plugins['filter']);
}

/**
 * Retrieves the version of the MT filter.
 *
 * @param  bool  $existsfilter Indicates whether the MT filter exists.
 * @param  array $plugins      The array containing the plugins.
 * @return string|null The version of the MT filter, or null if the filter does not exist.
 */
function get_mt_filter_version($existsfilter, $plugins) {
    if (!$existsfilter) {
        return null;
    }

    return $plugins['filter']['release'];
}


/**
 * Retrieves the status of the "filter/wiris" filter.
 *
 * This function checks if the "filter/wiris" filter is enabled or not.
 *
 * @param  bool $existsfilter Indicates whether the filter exists or not.
 * @return bool|null The status of the "filter/wiris" filter. Returns null if the filter does not exist.
 */
function get_mt_filter_enabled($existsfilter) {
    if (!$existsfilter) {
        return null;
    }

    $filterenabled = filter_is_enabled('filter/wiris');

    return $filterenabled;
}

/**
 * Checks if the specified editor exists and is enabled.
 *
 * @param  string $editorname The name of the editor to check.
 * @return bool|null Returns true if the editor exists and is enabled, false if it does not exist, and null if the check is skipped based on the Moodle version.
 */
function get_editor_exists_and_enabled($editorname) {
    global $CFG;

    if ($editorname === 'tinymce' && $CFG->branch > 402) {
        // if Moodle version is 4.1 or later, do not check if tiny (legacy) exists
        return null;
    }

    if ($editorname === 'tiny' && $CFG->branch < 401) {
        // if Moodle version is 4.1 or prior, do not check if tiny (current) exists
        return null;
    }

    // get_texteditor returns (boolean)false if not exists or an object if the editor exists
    $editors = array_keys(editors_get_enabled());
    return in_array($editorname, $editors);
}

/**
 * Checks if a specific editor exists in the given plugins array.
 *
 * @param  bool   $existeditor Whether the editor exists or not.
 * @param  array  $plugins     The array of plugins.
 * @param  string $editorname  The name of the editor to check.
 * @return bool|null Returns true if the editor exists, false if it doesn't, or null if $existeditor is false.
 */
function get_exists_mt_editor($existeditor, $plugins, $editorname) {
    if (!$existeditor) {
        return null;
    }

    return isset($plugins[$editorname]);
}

/**
 * Retrieves the version of the specified editor.
 *
 * @param  bool   $exismtteditor Indicates whether the editor exists.
 * @param  array  $plugins       An array containing information about the plugins.
 * @param  string $editorname    The name of the editor.
 * @return mixed|null The version of the editor, or null if the editor does not exist.
 */
function get_mt_editor_version($exismtteditor, $plugins, $editorname) {
    if (!$exismtteditor) {
        return null;
    }

    return $plugins[$editorname]['release'];
}

/**
 * Retrieves the status of the MathType editor for a given editor.
 *
 * @param  bool   $existsmteditor Indicates if the MathType editor exists.
 * @param  string $editorname     The name of the editor.
 * @return bool|null The status of the MathType editor for the given editor, or null if an exception occurs.
 */
function get_mt_editor_enabled($existsmteditor, $editorname) {
    if (!$existsmteditor) {
        return null;
    }

    try {
        $condition = check_if_wiris_button_are_in_toolbar($editorname);
        return ($condition);
    } catch (Exception $e) {
        return null;
    }
}

// Validates that MathType is usable at least in one of the text editors supported
/**
 * Checks the installation status of the WIRIS filter in Moodle.
 *
 * @param  bool   $filterenabled         Whether the WIRIS filter is enabled.
 * @param  string $filterversion         The version of the WIRIS filter.
 * @param  array  $enabledpluginsversion The versions of the enabled editor plugins.
 * @return bool Returns true if the installation is successful, false otherwise.
 */
function get_instalation_check($filterenabled, $filterversion, $enabledpluginsversion) {
    // Precondition - all plugins passed as the argument $enabledpluginsversion have been checked and are enabled.

    // Condition 1 - at least 1 editor installed and enabled
    $editorinstalledenabled = (count($enabledpluginsversion) > 0) ? true : false;

    // Condition 2 - filter enabled - passed as argument

    // Condition 3 - filter version same as one of enabled editor
    $sameversion = false;

    foreach ($enabledpluginsversion as $pluginversion) {
        if ($pluginversion == $filterversion) {
            $sameversion = true;
        }
    }

    // Special case: if user has the last version of MT for Tiny (legacy), always return true
    if ($enabledpluginsversion['tinymce'] == '8.6.2' && $filterenabled) {
        return true;
    }

    $instalationsuccess = ($editorinstalledenabled && $filterenabled && $sameversion);

    return $instalationsuccess;
}

// Page prologue.
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('title', 'filter_wiris'));
$PAGE->set_url('/filter/wiris/info.php', []);

echo $OUTPUT->header();
warning_tiny_incompatibility();

global $CFG;
$plugin = new stdClass();
require($CFG->dirroot . '/filter/wiris/version.php');
// Array of arrays witch contains all necesaty information for testing
$plugins = filter_wiris_pluginwrapper::get_wiris_plugins_information();

// Save version of all enabled plugins for test
$enabledplugins = [];

// Start to check all tests
$moodleversion = get_moodle_version();
$existsfilter = get_exists_mt_filter($plugins);
$filterversion = get_mt_filter_version($existsfilter, $plugins);
$filterenabled = get_mt_filter_enabled($existsfilter);

$exisatto = get_editor_exists_and_enabled('atto');
$existsmtatto = get_exists_mt_editor($exisatto, $plugins, 'atto');
$mtattoversion = get_mt_editor_version($existsmtatto, $plugins, 'atto');
$mtattoenabled = get_mt_editor_enabled($existsmtatto, 'atto');
$enabledplugins['atto'] = ($mtattoenabled) ? $mtattoversion : null;

$existstinylegacy = get_editor_exists_and_enabled('tinymce');
$existsmttinylegacy = get_exists_mt_editor($existstinylegacy,  $plugins, 'tinymce');
$mttinylegacyversion = get_mt_editor_version($existsmttinylegacy,  $plugins, 'tinymce');
$mttinylegacyenabled = get_mt_editor_enabled($existsmttinylegacy, 'tinymce');
$enabledplugins['tinymce'] = ($mttinylegacyenabled) ? $mttinylegacyversion : null;

$existstinycurrent = get_editor_exists_and_enabled('tiny');
$existsmttinycurrent = get_exists_mt_editor($existstinycurrent, $plugins, 'tiny');
$mttinycurrentversion = get_mt_editor_version($existsmttinycurrent, $plugins, 'tiny');
$mttinycurrentenabled = get_mt_editor_enabled($existsmttinycurrent, 'tiny');
$enabledplugins['tiny'] = ($mttinycurrentenabled) ? $mttinycurrentversion : null;

$instalationresult = get_instalation_check($filterenabled, $filterversion, $enabledplugins);

// Construct new html table
start_table();

$currentindex = 1;
$currentsubindex = 0;

// Show all tests
process_table_row(get_test_text('moodleversion'), $moodleversion, $currentindex, $currentsubindex);

start_new_group($currentindex, $currentsubindex);
process_table_row(get_test_text('existsinmoodle', 'themathtypefilter'), $existsfilter, $currentindex, $currentsubindex);
process_table_row(get_test_text('pluginversion', 'mathtypefilter'), $filterversion, $currentindex, $currentsubindex);
process_table_row(get_test_text('isenabled', 'themathtypefilter'), $filterenabled, $currentindex, $currentsubindex);

start_new_group($currentindex, $currentsubindex);
process_table_row(get_test_text('existsandenabledinmoodle', 'atto'), $exisatto, $currentindex, $currentsubindex);
process_table_row(get_test_text('existsinmoodle', 'mtatto'), $existsmtatto, $currentindex, $currentsubindex);
process_table_row(get_test_text('pluginversion', 'mtatto'), $mtattoversion, $currentindex, $currentsubindex);
process_table_row(get_test_text('isenabled', 'mtatto'), $mtattoenabled, $currentindex, $currentsubindex);

start_new_group($currentindex, $currentsubindex);
process_table_row(get_test_text('existsandenabledinmoodle', 'tinymcelegacy'), $existstinylegacy, $currentindex, $currentsubindex);
process_table_row(get_test_text('existsinmoodle', 'mttinymcelegacy'), $existsmttinylegacy, $currentindex, $currentsubindex);
process_table_row(get_test_text('pluginversion', 'mttinymcelegacy'), $mttinylegacyversion, $currentindex, $currentsubindex);
process_table_row(get_test_text('isenabled', 'mttinymcelegacy'), $mttinylegacyenabled, $currentindex, $currentsubindex);

start_new_group($currentindex, $currentsubindex);
process_table_row(get_test_text('existsandenabledinmoodle', 'tinymcecurrent'), $existstinycurrent, $currentindex, $currentsubindex);
process_table_row(get_test_text('existsinmoodle', 'mttinymcecurrent'), $existsmttinycurrent, $currentindex, $currentsubindex);
process_table_row(get_test_text('pluginversion', 'mttinymcecurrent'), $mttinycurrentversion, $currentindex, $currentsubindex);
process_table_row(get_test_text('isenabled', 'mttinymcecurrent'), $mttinycurrentenabled, $currentindex, $currentsubindex);

// Close html table
end_table($instalationresult);



// Footer information
$solutionlink = 'https://docs.wiris.com/mathtype/en/mathtype-for-lms/mathtype-for-moodle.html#install-mathtype-for-moodle?utm_source=moodle&utm_medium=referral';

// Warning information
echo html_writer::end_tag('p') . get_string('versionmustbethesame', 'filter_wiris') . html_writer::end_tag('p');

$output = '';
echo get_test_text('clickwirisplugincorrectlyinstalled') . "<br/>";
$link = 'integration/test.php';
$input = '<input type="button" value="' . get_test_text('button1');
$input .= '" onClick="javascript:window.open(\'' . $link . '\');" /><br/>';
echo $input;

$wqversion = get_config('qtype_wq', 'version');
if (!empty($wqversion)) {
    echo get_string('clickwirisquizzescorrectlyinstalled', 'filter_wiris') . "<br/>";
    $link = '../../question/type/wq/info.php';
    $input = '<input type="button" value="' . get_string('button2', 'filter_wiris');
    $input .= '" onClick="javascript:window.open(\'' . $link . '\');" /><br/>';
    echo $input;
}
$output .= html_writer::end_tag('br');
$output .= html_writer::end_tag('p');

$output .= html_writer::start_tag('p');
$output .= html_writer::start_tag('br');
$output .= html_writer::start_tag('span', ['style' => 'font-size:14px; font-weight:normal;']);
$output .= get_string('contact', 'filter_wiris');
$output .= " (<a href=\"mailto:support@wiris.com\">support@wiris.com</a>)";
$output .= html_writer::end_tag('span');
$output .= html_writer::end_tag('br');
$output .= html_writer::end_tag('p');

echo $output;
