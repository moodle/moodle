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
 * Search and replace strings throughout all texts in the whole database.
 *
 * Unfortunately it was a bad idea to use spans for multilang because it
 * can not support nesting. Hopefully this will get thrown away soon....
 *
 * @package    tool
 * @subpackage multilangupgrade
 * @copyright  2006 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('toolmultilangupgrade');

$go = optional_param('go', 0, PARAM_BOOL);

###################################################################
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pluginname', 'tool_multilangupgrade'));

$strmultilangupgrade = get_String('multilangupgradeinfo', 'tool_multilangupgrade');

if (!$go or !data_submitted() or !confirm_sesskey()) {   /// Print a form
    $optionsyes = array('go'=>1, 'sesskey'=>sesskey());
    echo $OUTPUT->confirm($strmultilangupgrade, new moodle_url('/admin/tool/multilangupgrade/index.php', $optionsyes), new moodle_url('/admin/'));
    echo $OUTPUT->footer();
    die;
}


if (!$tables = $DB->get_tables() ) {    // No tables yet at all.
    print_error('notables', 'debug');
}

echo $OUTPUT->box_start();

/// Turn off time limits, sometimes upgrades can be slow.

core_php_time_limit::raise();

echo '<strong>Progress:</strong>';
$i = 0;
$skiptables = array('config', 'block_instances', 'sessions'); // we can not process tables with serialised data here

foreach ($tables as $table) {
    if (strpos($table,'pma') === 0) { // Not our tables
        continue;
    }
    if (in_array($table, $skiptables)) { // Don't process these
        continue;
    }
    $fulltable = $DB->get_prefix().$table;
    if ($columns = $DB->get_columns($table)) {
        if (!array_key_exists('id', $columns)) {
            continue; // moodle tables have id
        }
        foreach ($columns as $column => $data) {
            if (in_array($data->type, array('text','mediumtext','longtext','varchar'))) {  // Text stuff only
                // first find candidate records
                $sql = "SELECT id, $column FROM $fulltable WHERE $column LIKE '%</lang>%' OR $column LIKE '%<span lang=%'";
                $rs = $DB->get_recordset_sql($sql);
                foreach ($rs as $data) {
                    $text = $data->$column;
                    $id   = $data->id;
                    if ($i % 600 == 0) {
                        echo '<br />';
                    }
                    if ($i % 10 == 0) {
                        echo '.';
                    }
                    $i++;

                    if (empty($text) or is_numeric($text)) {
                        continue; // nothing to do
                    }

                    $search = '/(<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.+?<\/(?:lang|span)>)(\s*<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.+?<\/(?:lang|span)>)+/is';
                    $newtext = preg_replace_callback($search, 'multilangupgrade_impl', $text);

                    if (is_null($newtext)) {
                        continue; // regex error
                    }

                    if ($newtext != $text) {
                        $DB->execute("UPDATE $fulltable SET $column=? WHERE id=?", array($newtext, $id));
                    }
                }
                $rs->close();
            }
        }
    }
}

// set conversion flag - switches to new plugin automatically
set_config('filter_multilang_converted', 1);

echo $OUTPUT->box_end();

/// Rebuild course cache which might be incorrect now
echo $OUTPUT->notification('Rebuilding course cache...', 'notifysuccess');
rebuild_course_cache(0, true);
echo $OUTPUT->notification('...finished', 'notifysuccess');

echo $OUTPUT->continue_button(new moodle_url('/admin/'));

echo $OUTPUT->footer();
die;


function multilangupgrade_impl($langblock) {
    $searchtosplit = '/<(?:lang|span) lang="([a-zA-Z0-9_-]*)".*?>(.+?)<\/(?:lang|span)>/is';
    preg_match_all($searchtosplit, $langblock[0], $rawlanglist);
    $return = '';
    foreach ($rawlanglist[1] as $index=>$lang) {
        $return .= '<span lang="'.$lang.'" class="multilang">'.$rawlanglist[2][$index].'</span>';
    }
    return $return;
}

