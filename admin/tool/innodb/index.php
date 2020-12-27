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
 * InnoDB conversion tool.
 *
 * @package    tool
 * @subpackage innodb
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('toolinnodb');

$confirm = optional_param('confirm', 0, PARAM_BOOL);


echo $OUTPUT->header();
echo $OUTPUT->heading('Convert all MySQL tables from MYISAM to InnoDB');

if ($DB->get_dbfamily() != 'mysql') {
    notice('This function is for MySQL databases only!', new moodle_url('/admin/'));
}

$prefix = str_replace('_', '\\_', $DB->get_prefix()).'%';
$sql = "SHOW TABLE STATUS WHERE Name LIKE ? AND Engine <> 'InnoDB'";
$rs = $DB->get_recordset_sql($sql, array($prefix));
if (!$rs->valid()) {
    $rs->close();
    echo $OUTPUT->box('<p>All tables are already using InnoDB database engine.</p>');
    echo $OUTPUT->continue_button('/admin/');
    echo $OUTPUT->footer();
    die;
}

if (data_submitted() and $confirm and confirm_sesskey()) {

    echo $OUTPUT->notification('Please be patient and wait for this to complete...', 'notifysuccess');

    core_php_time_limit::raise();

    foreach ($rs as $table) {
        $DB->set_debug(true);
        $fulltable = $table->name;
        try {
            $DB->change_database_structure("ALTER TABLE $fulltable ENGINE=INNODB");
        } catch (moodle_exception $e) {
            echo $OUTPUT->notification(s($e->getMessage()).'<br />'.s($e->debuginfo));
        }
        $DB->set_debug(false);
    }
    $rs->close();
    echo $OUTPUT->notification('... done.', 'notifysuccess');
    echo $OUTPUT->continue_button(new moodle_url('/admin/'));
    echo $OUTPUT->footer();

} else {
    $rs->close();
    $optionsyes = array('confirm'=>'1', 'sesskey'=>sesskey());
    $formcontinue = new single_button(new moodle_url('/admin/tool/innodb/index.php', $optionsyes), get_string('yes'));
    $formcancel = new single_button(new moodle_url('/admin/'), get_string('no'), 'get');
    echo $OUTPUT->confirm('Are you sure you want convert all your tables to the InnoDB format?', $formcontinue, $formcancel);
    echo $OUTPUT->footer();
}


