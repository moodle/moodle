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
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
// Based on Custom SQL Reports Plugin
// See http://moodle.org/mod/data/view.php?d=13&rid=2884

if (!defined('MOODLE_INTERNAL')) {
    die(get_string('nodirectaccess','block_learnerscript'));    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class customsql_form extends moodleform {

    function definition() {
        global $DB, $CFG, $COURSE;

        $mform = & $this->_form;

        $mform->addElement('textarea', 'querysql', get_string('querysql', 'block_learnerscript'), 'rows="35" cols="80"');
        $mform->addRule('querysql', get_string('required'), 'required', null, 'client');
        $mform->setType('querysql', PARAM_RAW);

        $mform->addElement('hidden', 'courseid', $COURSE->id);
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();

        $mform->addElement('static', 'note', '', get_string('listofsqlreports', 'block_learnerscript'));

        if ($userandrepo = get_config('block_learnerscript', 'sharedsqlrepository')) {

            $c = new curl();
            $res = $c->get("https://api.github.com/repos/$userandrepo/contents/");
            $res = json_decode($res);

            if (is_array($res)) {
                $reportcategories = array(get_string('choose'));
                foreach ($res as $item) {
                    if ($item->type == 'dir')
                        $reportcategories[$item->path] = $item->path;
                }

                $mform->addElement('select', 'reportcategories', get_string('reportcategories', 'block_learnerscript'), $reportcategories, array('onchange' => 'M.block_learnerscript.onchange_reportcategories(this,"' . sesskey() . '")'));

                $mform->addElement('select', 'reportsincategory', get_string('reportsincategory', 'block_learnerscript'), $reportcategories, array('onchange' => 'M.block_learnerscript.onchange_reportsincategory(this,"' . sesskey() . '")'));

                $mform->addElement('textarea', 'remotequerysql', get_string('remotequerysql', 'block_learnerscript'), 'rows="15" cols="90"');
            }
        }

        //$this->add_action_buttons();
    }

    function validation($data, $files) {
        if (get_config('block_learnerscript', 'sqlsecurity')) {
            return $this->validation_high_security($data, $files);
        } else {
            return $this->validation_low_security($data, $files);
        }
    }

    function validation_high_security($data, $files) {
        global $DB, $CFG, $db, $USER;

        $errors = parent::validation($data, $files);

        $sql = $data['querysql'];
        $sql = trim($sql);

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
        } else {

            $sql = $this->_customdata['reportclass']->prepare_sql($sql);
            $rs = $this->_customdata['reportclass']->execute_query($sql, 2);
            if (!$rs) {
                $errors['querysql'] = get_string('queryfailed', 'block_learnerscript', $db->ErrorMsg());
            } else if (!empty($data['singlerow'])) {
                if (rs_EOF($rs)) {
                    $errors['querysql'] = get_string('norowsreturned', 'block_learnerscript');
                }
            }

            if ($rs) {
             //   $rs->close();
            }
        }

        return $errors;
    }

    function validation_low_security($data, $files) {
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
        } else {

            $sql = $this->_customdata['reportclass']->prepare_sql($sql);
            $rs = $this->_customdata['reportclass']->execute_query($sql, 2);
            if (!$rs) {
                $errors['querysql'] = get_string('queryfailed', 'block_learnerscript', $db->ErrorMsg());
            } else if (!empty($data['singlerow'])) {
                if (rs_EOF($rs)) {
                    $errors['querysql'] = get_string('norowsreturned', 'block_learnerscript');
                }
            }

            if ($rs) {
                $rs->close();
            }
        }

        return $errors;
    }

}
