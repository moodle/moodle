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
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/lib/editor/tinymce/lib.php');
require_once($CFG->dirroot . '/filter/wiris/classes/pluginwrapper.php');

// BEGIN HELPERS FUNCTIONS.
function wrs_assert($condition, $reporttext, $solutionlink) {
    if ($condition) {
        return $reporttext;
    } else {
        $result = html_writer::tag('span', $reporttext);
        // $imageurl = "https://www.wiris.com/system/files/attachments/1689/WIRIS_manual_icon_17_17.png";
        $image = html_writer::empty_tag('img', array('src' => 'img/help.gif', 'class' => 'wrs_plugin wrs_filter'));
        $result .= html_writer::link($solutionlink, $image, array('target' => '_blank'));
        return $result;
    }
}

function wrs_getstatus($condition) {
    $statustext = '';
    if ($condition) {
        $text = get_string('ok', 'filter_wiris');
        $statustext .= html_writer::tag('span', $text, array('class' => 'wrs_ok wrs_plugin wrs_filter'));
        return $statustext;
    } else {
        $text = get_string('error', 'filter_wiris');
        $statustext .= html_writer::tag('span', $text, array('class' => 'wrs_error wrs_plugin wrs_filter'));
        return $statustext;
    }
}

function wrs_createtablerow($testname, $reporttext, $solutionlink, $condition) {
    $output = html_writer::tag('td', $testname, array('class' => 'wrs_plugin wrs_filter'));
    $output .= html_writer::tag('td', wrs_assert($condition, $reporttext, $solutionlink),
                                array('class' => 'wrs_plugin wrs_filter'));
    $output .= html_writer::tag('td',  wrs_getstatus($condition), array('class' => 'wrs_plugin wrs_filter'));
    return $output;
}

function get_current_editor_data() {
    global $CFG;
    $data = array();

    $tinyeditor = new tinymce_texteditor();

    if ($CFG->version < 2012120300) {
        $data['plugin_path'] = '../../lib/editor/tinymce/tiny_mce/' . $tinyeditor->version . '/plugins/tiny_mce_wiris';
        $data['plugin_name'] = get_string('tinymce', 'filter_wiris');
        return $data;
    }

    if ($CFG->version >= 2012120300 && $CFG->version < 2014051200) {
        $data['plugin_path'] = '../../lib/editor/tinymce/plugins/tiny_mce_wiris/tinymce';
        $data['plugin_name'] = get_string('tinymce', 'filter_wiris');
        return $data;
    }

    if ($CFG->version >= 2014051200) {
        $editors = array_flip(explode(',', $CFG->texteditors));
        if (count($editors) <= 0) {
            throw new Exception(get_string('noteditorspluginsinstalled', 'filter_wiris'), 1);
        }
        if (count($editors) == 1) {
            if (array_key_exists('textarea', $editors)) {
                throw new Exception(get_string('onlytextareaeditorinstalled', 'filter_wiris'), 1);
            }
        }

        foreach ($editors as $editor => $value) {
            switch ($editor) {
                case 'atto':
                    $data['plugin_path'] = '../../lib/editor/atto/plugins/wiris';
                    $data['plugin_name'] = get_string('atto', 'filter_wiris');
                return $data;
                case 'tinymce':
                    $data['plugin_path'] = '../../lib/editor/tinymce/plugins/tiny_mce_wiris/tinymce';
                    $data['plugin_name'] = get_string('tinymce', 'filter_wiris');
                return $data;
            }
        }
    }

    return $data;
}

function check_if_wiris_button_are_in_toolbar($editor = null) {
    if ( is_null($editor) ) {
        throw new Exception(get_string('editornameexpected', 'filter_wiris'), 1);
    }

    switch ($editor) {
        case 'Atto':
            return check_if_wiris_button_are_in_atto_toolbar();
        case 'TinyMCE':
            return check_if_wiris_button_are_in_tinymce_toolbar();
        default:
            throw new Exception($editor . get_string('notsupportededitor', 'filter_wiris', 1));
    }
}

function check_if_wiris_button_are_in_atto_toolbar() {
    $configvalue = get_config('editor_atto', 'toolbar');
    return (strpos($configvalue, 'wiris') !== false);
}

function check_if_wiris_button_are_in_tinymce_toolbar() {
    $configvalue = get_config('editor_tinymce', 'disabledsubplugins');
    return (strpos($configvalue, 'tiny_mce_wiris') === false);
}


// Page prologue.
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('title', 'filter_wiris'));
$PAGE->set_url('/filter/wiris/info.php', array());
echo $OUTPUT->header();

$output = '';
$output .= html_writer::start_tag('h1');
$output .= get_string('title', 'filter_wiris');
$output .= html_writer::end_tag('h1');

$output .= html_writer::start_tag('table', array('id' => 'wrs_filter_info_table', 'class' => 'wrs_plugin wrs_filter'));

$output .= html_writer::start_tag('tr', array('class' => 'wrs_plugin wrs_filter'));
$output .= html_writer::start_tag('th', array('class' => 'wrs_plugin wrs_filter'));
$output .= "Test";
$output .= html_writer::end_tag('th');
$output .= html_writer::start_tag('th', array('class' => 'wrs_plugin wrs_filter'));
$output .= "Report";
$output .= html_writer::end_tag('th');
$output .= html_writer::start_tag('th', array('class' => 'wrs_plugin wrs_filter'));
$output .= "Status";
$output .= html_writer::end_tag('th');
$output .= html_writer::end_tag('tr');
echo $output;

// Get editor data for tests.
$currenteditordata = get_current_editor_data();

// Filter files tests.
$output = '';
$output .= html_writer::start_tag('tr', array('class' => 'wrs_plugin wrs_filter'));
$testname = get_string('lookingforfilterfiles', 'filter_wiris');
$reporttext = get_string('wirispluginfiltermustbeinstalled', 'filter_wiris');
$solutionlink = 'http://www.wiris.com/plugins/moodle/download';
$filterfiles = Array('filter.php', 'version.php');
$exist = true;
foreach ($filterfiles as $value) {
    $condition = file_exists($value);
    if (!$condition) {
        $exist = false;
    }
}
echo wrs_createtablerow($testname, $reporttext, $solutionlink, $exist);
$output .= html_writer::end_tag('tr');
echo $output;

// Filter version existance test.
$output .= html_writer::start_tag('tr', array('class' => 'wrs_plugin wrs_filter'));
$output = '';
$plugin = new stdClass();
require($CFG->dirroot . '/filter/wiris/version.php');
$testname = get_string('lookingforwirisfilterversion', 'filter_wiris');
if (isset($plugin->release)) {
    $reporttext = '<span>' . $plugin->release . '</span>';
    $condition = true;
} else if ($plugin->maturity == MATURITY_BETA) {
     $reporttext = '<span>' . $plugin->version . '</span>';
    $condition = true;
} else {
    $reporttext = get_string('impossibletofindwirisfilterversion', 'filter_wiris');
    $condition = false;
}
$solutionlink = 'http://www.wiris.com/plugins/moodle/download';
echo wrs_createtablerow($testname, $reporttext, $solutionlink, $condition);
$output .= html_writer::end_tag('tr');
echo $output;

// MathType filter enabled.
$output = '';
$output .= html_writer::start_tag('tr', array('class' => 'wrs_plugin wrs_filter'));
$testname = get_string('pluginname', 'filter_wiris');
$solutionlink = 'http://www.wiris.com/plugins/docs/moodle/moodle-2.0';
$filterenabled = filter_is_enabled('filter/wiris');
if ($filterenabled) {
    $reporttext = get_string('enabled', 'filter_wiris');
} else {
    $reporttext = get_string('disabled', 'filter_wiris');
}
echo wrs_createtablerow($testname, $reporttext, $solutionlink, $filterenabled);
$output .= html_writer::end_tag('tr');

$output .= html_writer::start_tag('tr', array('class' => 'wrs_plugin wrs_filter'));
echo $output;

$output = '';
$testname = get_string('lookingforwirisplugin', 'filter_wiris') . $currenteditordata['plugin_name'];
$reporttext = get_string('wirispluginfor', 'filter_wiris') . $currenteditordata['plugin_name'] .
                get_string('mustbeinstalled', 'filter_wiris');
$solutionlink = 'http://www.wiris.com/plugins/moodle/download';
$wirisplugin = $currenteditordata['plugin_path'];
$condition = file_exists($wirisplugin);
if (!$condition) {
    $wirisplugin = '../../lib/editor/tinymce/plugins/tiny_mce_wiris';
    $condition = file_exists($wirisplugin);
}
echo wrs_createtablerow($testname, $reporttext, $solutionlink, $condition);
$output .= html_writer::end_tag('tr');
echo $output;

// Version compatibility test.
$output = '';
$output .= html_writer::start_tag('tr', array('class' => 'wrs_plugin wrs_filter'));
$wirisplugin = filter_wiris_pluginwrapper::get_wiris_plugin();
$testname = get_string('wirispluginfilterfor', 'filter_wiris') . $currenteditordata['plugin_name'] . ' versions';

if (isset($plugin->version)) {
    $filterversion = $plugin->version;
}

// Using version.php to check release number.
if (strtolower($currenteditordata['plugin_name']) == 'tinymce') {
    require($currenteditordata['plugin_path'] . '/../version.php');
} else {
    require($currenteditordata['plugin_path'] . '/version.php');
}

if (isset($plugin->version)) {
    $pluginversion = $plugin->version;
} else {
    $pluginversion = "";
}

if ($filterversion == $pluginversion) {
    $reporttext = get_string('wirispluginfilterfor', 'filter_wiris'). $currenteditordata['plugin_name'] .
                    get_string('havesameversion', 'filter_wiris');
    $condition = true;
} else {
    $reporttext = get_string('wirispluginfilterfor', 'filter_wiris'). $currenteditordata['plugin_name'] .
                    get_string('versionsdontmatch', 'filter_wiris');
    $reporttext .= "<br>" . get_string('wirisfilterversion', 'filter_wiris') . $filterversion;
    $reporttext .= "<br>" . get_string('wirispluginfor', 'filter_wiris') .  $currenteditordata['plugin_name'] . ' ' .
                    get_string('version', 'filter_wiris'). ' = ' . $pluginversion;
    $condition = false;
}

$solutionlink = 'http://www.wiris.com/plugins/moodle/download';
echo wrs_createtablerow($testname, $reporttext, $solutionlink, $condition);
$output .= html_writer::end_tag('tr');
echo $output;

// MathType enabled test.
$output = '';
$output .= html_writer::start_tag('tr', array('class' => 'wrs_plugin wrs_filter'));

$testname = get_string('lookingforwirispluginenabled', 'filter_wiris') . $currenteditordata['plugin_name'];
try {
    $condition = check_if_wiris_button_are_in_toolbar($currenteditordata['plugin_name']);
    $reporttext = ($condition) ? get_string('enabled', 'filter_wiris') : get_string('disabled', 'filter_wiris');
} catch (Exception $e) {
    $condition = false;
    $reporttext = $e->getMessage();
}

echo wrs_createtablerow($testname, $reporttext, $solutionlink, $condition);
$output .= html_writer::end_tag('tr');
// END TEST 7.

// START PAGE EPILOGUE.
$output .= html_writer::end_tag('table');

$output .= html_writer::start_tag('p');
$output .= html_writer::start_tag('br');
echo $output;
$output = '';
echo get_string('clickwirisplugincorrectlyinstalled', 'filter_wiris') . "<br/>";
$link = 'integration/test.php';
$input = '<input type="button" value="' . get_string('button1', 'filter_wiris');
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
$output .= html_writer::start_tag('span', array('style' => 'font-size:14px; font-weight:normal;'));
$output .= get_string('contact', 'filter_wiris');
$output .= " (<a href=\"mailto:support@wiris.com\">support@wiris.com</a>)";
$output .= html_writer::end_tag('span');
$output .= html_writer::end_tag('br');
$output .= html_writer::end_tag('p');

echo $output;
