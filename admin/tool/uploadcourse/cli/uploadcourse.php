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
 * CLI Bulk course registration script from a comma separated file
 *
 * @package    tool_uploadcourse
 * @subpackage uploadcourse
 * @copyright  2004 onwards Martin Dougiamas (http://dougiamas.com)
 * @copyright  2012 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir.'/clilib.php');
require_once('../locallib.php');

$courseconfig = get_config('moodlecourse');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
                 array('verbose' => false,
                       'help' => false,
                       'action' => '',
                       'mode' => 'nochange',
                       'file' => '',
                       'delimiter' => 'comma',
                       'encoding' => 'UTF-8',
                       'category' => false,
                       'templateshortname' => false,
                       'template' => false,
                       'format' => $courseconfig->format,
                       'numsections' => $courseconfig->numsections,
                       'reset' => false,
                         ),
                 array('v' => 'verbose',
                       'h' => 'help',
                       'a' => 'action',
                       'm' => 'mode',
                       'f' => 'file',
                       'd' => 'delimiter',
                       'e' => 'encoding',
                       'c' => 'category',
                       's' => 'templateshortname',
                       't' => 'template',
                       'g' => 'format',
                       'n' => 'numsections',
                       'r' => 'reset',
                        ));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help =
"Execute Course Upload.

Options:
-v, --verbose              Print verbose progress information
-h, --help                 Print out this help
-a, --action               Action to perform - addnew, addupdate, update, forceadd
-m, --mode                 Mode of execution - delete, rename, nochange, file, filedefaults, missing
-f, --file                 CSV File
-d, --delimiter            delimiter - colon,semicolon,tab,cfg,comma
-e, --encoding             File encoding - utf8 etc
-c, --category             Course category
-s, --templateshortname    Template course by shortname
-t, --template             Template course by backup file
-g, --format               Course format - weeks,scorm,social,topics
-n, --numsections          Number of sections
-r, --reset                Run the course reset by default after each course import


Example:
\$sudo -u www-data /usr/bin/php admin/tool/uploadcourse/cli/uploadcourse.php --action=addupdate \\
       --mode=delete --file=./courses.csv --delimiter=comma
";

if ($options['help']) {
    echo $help;
    die;
}
echo "Moodle course uploader running ...\n";

$actions = array('addnew' => CC_COURSE_ADDNEW,
                 'addupdate' => CC_COURSE_ADD_UPDATE,
                 'update' => CC_COURSE_UPDATE,
                 'forceadd' => CC_COURSE_ADDINC);
if (!isset($options['action']) ||
    !isset($actions[$options['action']]) ||
    ($options['action'] != 'addnew' && !isset($options['mode']))) {
    echo get_string('invalidinput', 'tool_uploadcourse')."\n";
    echo $help;
    die;
}

if (!empty($options['verbose']) || $CFG->debug) {
    define('CC_DEBUG', true);
}

if (!isset($actions[$options['action']])) {
    echo get_string('invalidaction', 'tool_uploadcourse')."\n";
    echo $help;
    die;
}
$options['cctype'] = $actions[$options['action']];

$updatetype = array('nochange' => CC_UPDATE_NOCHANGES,
                'file' => CC_UPDATE_FILEOVERRIDE,
                'filedefaults' => CC_UPDATE_ALLOVERRIDE,
                'missing' => CC_UPDATE_MISSING);
if ($options['mode'] == 'rename') {
    $options['ccallowrenames'] = 1;
    unset($options['mode']);
} else if ($options['mode'] == 'delete') {
    $options['ccallowdeletes'] = 1;
    unset($options['mode']);
} else if (!isset($updatetype[$options['mode']])) {
    echo get_string('invalidmode', 'tool_uploadcourse')."\n";
    echo $help;
    die;
}
if (isset($options['mode'])) {
    $options['ccupdatetype'] = $updatetype[$options['mode']];
}
$options['ccstandardshortnames'] = 1;
$options['startdate'] = time() + 3600 * 24;
$options['hiddensections'] = $courseconfig->hiddensections;
$options['newsitems'] = $courseconfig->newsitems;
$options['showgrades'] = $courseconfig->showgrades;
$options['showreports'] = $courseconfig->showreports;
$options['maxbytes'] = $courseconfig->maxbytes;
$options['legacyfiles'] = 0;
$options['groupmode'] = $courseconfig->groupmode;
$options['groupmodeforce'] = $courseconfig->groupmodeforce;
$options['visible'] = $courseconfig->visible;
$options['lang'] =  $courseconfig->lang;

if ($options['category']) {
    $split = preg_split('|(?<!\\\)/|', $options['category']);
    $categories = array();
    foreach ($split as $cat) {
        $cat = preg_replace('/\\\/', '', $cat);
        $categories[]= $cat;
    }
    $options['category'] = 0;
    foreach ($categories as $cat) {
        // Does the category exist - does the category hierachy make sense.
        $category = $DB->get_record('course_categories', array('name'=>trim($cat), 'parent' => $options['category']));
        if (empty($category)) {
            echo get_string('invalidcategory', 'tool_uploadcourse')."\n";
            echo $help;
            die;
        }
        $options['category'] = $category->id;
    }
    $options['cccategory'] = $options['category'];
} else {
    $categories = $DB->get_records('course_categories');
    if (empty($categories)) {
        echo get_string('invalidcategory', 'tool_uploadcourse')."\n";
        echo $help;
        die;
    }
    $category = array_shift($categories);
    $options['cccategory'] = $category->id;
}

if (isset($options['templateshortname'])) {
    $options['ccshortname'] = $options['templateshortname'];
}

$options['file'] = realpath($options['file']);
if (!file_exists($options['file'])) {
    echo get_string('invalidcsvfile', 'tool_uploadcourse')."\n";
    echo $help;
    die;
}

$encodings = textlib::get_encodings();
if (!isset($encodings[$options['encoding']])) {
    echo get_string('invalidencoding', 'tool_uploadcourse')."\n";
    echo $help;
    die;
}


if ($options['template']) {
    $options['template'] = realpath($options['template']);
}
if ($options['template'] && !file_exists($options['template'])) {
    echo get_string('invalidtemplatefile', 'tool_uploadcourse')."\n";
    echo $help;
    die;
}
$tmpdir = $CFG->tempdir . '/backup';
if (!check_dir_exists($tmpdir, true, true)) {
    throw new restore_controller_exception('cannot_create_backup_temp_dir');
}
$filename = restore_controller::get_tempdir_name(SITEID, $USER->id);
$restorefile = null;
if ($options['template']) {
    $restorefile = $options['template'];
}

$formdata = (object) $options;


$returnurl = new moodle_url('/admin/tool/uploadcourse/index.php');
$bulknurl  = new moodle_url('/admin/tool/uploadcourse/index.php');
$std_fields = tool_uploadcourse_std_fields();

// Emulate normal session.
cron_setup_user();

$content = file_get_contents($formdata->file);
$iid = csv_import_reader::get_new_iid('uploadcourse');
$cir = new csv_import_reader($iid, 'uploadcourse');
$readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter);
$filecolumns = tool_uploadcourse_validate_course_upload_columns($cir, $std_fields, $returnurl);
unset($content);
if ($readcount === false) {
    print_error('csvfileerror', 'tool_uploadcourse', $returnurl, $cir->get_error());
} else if ($readcount == 0) {
    print_error('csvemptyfile', 'error', $returnurl, $cir->get_error());
}
echo "CSV read count: ".$readcount."\n";

$result = tool_uploadcourse_process_course_upload($formdata, $cir, $filecolumns, $restorefile, true);

exit($result);
