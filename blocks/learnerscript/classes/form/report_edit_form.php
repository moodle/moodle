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

/** LearnerScript Reports
 * A Moodle block for creating LearnerScript Reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\form;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}
require_once($CFG->libdir . '/formslib.php');
use moodleform;
use block_learnerscript\local\ls;
class report_edit_form extends moodleform {

    public function definition() {
        global $DB, $USER, $CFG;

        $adminmode = optional_param('adminmode', null, PARAM_INT);

        $mform = &$this->_form;
        $mform->addElement('header', 'general', get_string('report'));
        $mform->addElement('text', 'name', get_string('name'), array('maxlength' => 60, 'size' => 58));
        $mform->addRule('name', get_string('spacevalidation', 'block_learnerscript'), 'regex', "/\S{1}/", 'client');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_NOTAGS);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $typeoptions = (new ls)->cr_get_report_plugins($this->_customdata['courseid']);

        $eloptions = array();
        if (isset($this->_customdata['report']->id) && $this->_customdata['report']->id) {
            $eloptions = array('disabled' => 'disabled');
        }

        $select = $mform->addElement('select', 'type', get_string("typeofreport", 'block_learnerscript'), $typeoptions, $eloptions);
        $mform->addHelpButton('type', 'typeofreport', 'block_learnerscript');
        $select->setSelected('sql');

        $mform->addElement('textarea', 'querysql', get_string('querysql', 'block_learnerscript'), 'rows="15" cols="80"');
         //$mform->addRule('querysql', get_string('required'), 'required', null, 'server');

        $selectedoptions = array('sql', 'statistics');
        $querysqloptions = array_diff(array_keys($typeoptions), $selectedoptions);
        $querysqloptions1 = implode('|', $querysqloptions);

        $mform->disabledIf('querysql', 'type', 'in', $querysqloptions1);
        //$mform->hideIf('querysql', 'type', 'in', $querysqloptions1);

        $mform->addElement('header', 'advancedoptions', get_string('advanced'));
        $mform->addElement('editor', 'description', get_string('summary'));
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('checkbox', 'global', get_string('global', 'block_learnerscript'),
         get_string('enableglobal', 'block_learnerscript'));
        $mform->addHelpButton('global', 'global', 'block_learnerscript');
        $mform->setDefault('global', 1);

        $mform->addElement('checkbox', 'disabletable',
            get_string('disabletable', 'block_learnerscript'),
            get_string('enabletable', 'block_learnerscript'));
        $mform->setDefault('disabletable', 0);

        // $mform->addElement('checkbox', 'enabletabs', get_string('enabletabs', 'block_learnerscript'), get_string('enabletabs', 'block_learnerscript'));
        // $mform->addHelpButton('enabletabs', 'enabletabs', 'block_learnerscript');
        // $mform->setDefault('enabletabs', 0);

        // $mform->addElement('header', 'exportoptions', get_string('exportoptions', 'block_learnerscript'));
        // $options = (new ls)->cr_get_export_plugins();

        // foreach ($options as $key => $val) {
        //  $mform->addElement('checkbox', "exportformats[$key]", null, $val);
        // }

        if (isset($this->_customdata['report']->id) && $this->_customdata['report']->id) {
            $mform->addElement('hidden', 'id', $this->_customdata['report']->id);
        }

        $mform->setType('id', PARAM_INT);
        if (!empty($adminmode)) {
            $mform->addElement('text', 'courseid', get_string("setcourseid", 'block_learnerscript'),
                $this->_customdata['courseid']);
            $mform->setType('courseid', PARAM_INT);
        } else {
            $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
            $mform->setType('courseid', PARAM_INT);
        }
        $mform->setExpanded('advancedoptions', false);

        $this->add_action_buttons(true, get_string('next'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['type'] === 'sql' || $data['type'] === 'statistics') {
            $errors['querysql'] = get_string('required');
        }
        if (get_config('block_learnerscript', 'sqlsecurity')) {
            return $this->validation_high_security($data, $files);
        } else {
            return $this->validation_low_security($data, $files);
        }
        return $errors;
    }
    public function validation_high_security($data, $files) {
        global $DB, $CFG, $db, $USER;

        $errors = parent::validation($data, $files);
        if ($data['type'] === 'sql' || $data['type'] === 'statistics') {
            $sql = $data['querysql'];
        } else {
            $sql = "";
        }
        $sql = trim($sql);
        if (empty($data['querysql']) && ($data['type'] == 'sql' || $data['type'] == 'statistics')) {
            $errors['querysql'] = get_string('required');
        }
        // Simple test to avoid evil stuff in the SQL.
        if (preg_match('/\b(ALTER|CREATE|DELETE|DROP|GRANT|INSERT|INTO|TRUNCATE|UPDATE|SET|VACUUM|REINDEX|DISCARD|LOCK)\b/i', $sql)) {
            $errors['querysql'] = get_string('notallowedwords', 'block_learnerscript');

            // Do not allow any semicolons.
        } else if (strpos($sql, ';') !== false) {
            $errors['querysql'] = get_string('nosemicolon', 'report_customsql');

            // Make sure prefix is prefix_, not explicit.
        } else if ($CFG->prefix != '' && preg_match('/\b' . $CFG->prefix . '\w+/i', $sql)) {
            $errors['querysql'] = get_string('noexplicitprefix', 'block_learnerscript');

            // Now try running the SQL, and ensure it runs without errors.
        }
        // else {

        //     $sql = $this->_customdata['reportclass']->prepare_sql($sql);
        //     $rs = $this->_customdata['reportclass']->execute_query($sql, 2);
        //     if (!$rs) {
        //         $errors['querysql'] = get_string('queryfailed', 'block_learnerscript', $db->ErrorMsg());
        //     } else if (!empty($data['singlerow'])) {
        //         if (rs_EOF($rs)) {
        //             $errors['querysql'] = get_string('norowsreturned', 'block_learnerscript');
        //         }
        //     }

        //     if ($rs) {
        //      //   $rs->close();
        //     }
        // }

        return $errors;
    }

    public function validation_low_security($data, $files) {
        global $DB, $CFG, $db, $USER;

        $errors = parent::validation($data, $files);

        $sql = $data['querysql'];
        $sql = trim($sql);


        if (empty($this->_customdata['report']->runstatistics) OR $this->_customdata['report']->runstatistics == 0) {
            // Simple test to avoid evil stuff in the SQL.
            //if (preg_match('/\b(ALTER|CREATE|DELETE|DROP|GRANT|INSERT|INTO|TRUNCATE|UPDATE|SET|VACUUM|REINDEX|DISCARD|LOCK)\b/i', $sql)) {
            // Allow cron SQL queries to run CREATE|INSERT|INTO queries.
            if (preg_match('/\b(ALTER|DELETE|DROP|GRANT|TRUNCATE|UPDATE|SET|VACUUM|REINDEX|DISCARD|LOCK)\b/i', $sql)) {
                $errors['querysql'] = get_string('notallowedwords', 'block_learnerscript');
            }

            // Now try running the SQL, and ensure it runs without errors.
        }
        // else {

        //     $sql = $this->_customdata['reportclass']->prepare_sql($sql);
        //     $rs = $this->_customdata['reportclass']->execute_query($sql, 2);
        //     if (!$rs) {
        //         $errors['querysql'] = get_string('queryfailed', 'block_learnerscript', $db->ErrorMsg());
        //     } else if (!empty($data['singlerow'])) {
        //         if (rs_EOF($rs)) {
        //             $errors['querysql'] = get_string('norowsreturned', 'block_learnerscript');
        //         }
        //     }

        //     if ($rs) {
        //         $rs->close();
        //     }
        // }

        return $errors;
    }
}
