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

defined('MOODLE_INTERNAL') || die();

require_once('lib.php');
require_once($CFG->dirroot.'/grade/lib.php');

abstract class SmartFileBase {
    // Grade item id we will be mapping these grades to upon insertion.
    private $giid;

    // Lines of the uplaoded file.
    protected $filecontents;

    // Localized string for the name of this file type.
    protected $name;

    // Maps either pawsids, lsuids or anon numbers to grades.
    public $ids_to_grades = array();

    // Invalid lines in the file.
    public $bad_lines = array();

    // Any ids in the uploaded file that did not exist in the course.
    public $bad_ids = array();

    // File objects need to keep track of the course id to convert_ids and insert_grades.
    protected $courseid;

    // Maps moodle userids to grades.
    private $moodleidstogrades = array();

    // Set file name and get file contents in constructor. Also set localized file type name.
    // Note: Maple uses a different constructor.
    public function __construct($filecontents) {
        $this->filecontents = smart_split_file($filecontents);
    }

    public function set_gi_id($giid) {
        $this->giid = $giid;
    }

    public function set_courseid($courseid) {
        $this->courseid = $courseid;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_field() {
        return $this->field;
    }

    // Returns an array of whatever id field is the key of ids_to_grades.
    public function get_ids() {
        return array_keys($this->ids_to_grades);
    }

    public function get_keypad_users($roleids, $context) {
        global $DB;

        $strings = function($id) {
            return "'$id'";
        };
        $roleusers = array();

        foreach ($roleids as $roleid) {
            $roleusers = $roleusers + get_role_users($roleid, $context, false);
        }

        $roleuserids = implode(',', array_keys($roleusers));

        $keyids = array_keys($this->ids_to_grades);
        $keys = implode(',', array_map($strings, $keyids));

        $sql = 'SELECT u.*, d.data AS user_keypadid
            FROM {user} u, {user_info_data} d
            WHERE d.userid = u.id
              AND d.fieldid = :fieldid
              AND d.data IN (' . $keys . ')
              AND u.id IN (' . $roleuserids . ')';

        $profileid = get_config('smart_import', 'keypadprofile');
        $params = array('fieldid' => $profileid);

        return $DB->get_records_sql($sql, $params);
    }

    // Takes $ids_to_grades and fills $moodleidstogrades.
    public function convert_ids() {
        global $CFG;

        $roleids = explode(',', $CFG->gradebookroles);
        $context = context_course::instance($this->courseid);
        $moodleidstofield = array();
        $userfields = 'u.id, u.username, ' . get_all_user_name_fields(true, 'u');
        $users = array();

        // Keypadid temp fix.
        if ($this->get_field() == 'user_keypadid') {
            $users = $this->get_keypad_users($roleids, $context);
        } else {
            foreach ($roleids as $roleid) {
                $users = $users + get_role_users($roleid, $context, false);
            }
        }
        foreach ($users as $k => $v) {
            $field = $this->get_field();
            $moodleidstofield[$k] = $v->$field;
        }

        $idsonly = array_keys($this->ids_to_grades);

        foreach ($moodleidstofield as $k => $v) {
            $found = array_search($v, $idsonly);

            if ($found !== false) {
                $this->moodleidstogrades[$k] = $this->ids_to_grades[$idsonly[$found]];
            }
        }

        foreach ($this->ids_to_grades as $id => $grade) {
            if (!in_array($id, $moodleidstofield)) {
                $this->bad_ids[] = $id;
            }
        }
    }

    // This is called after the filetype is discovered. Every line is
    // individually validated and removed if it doesn't pass.
    public function validate() {
        $linecount = 1;

        foreach ($this->filecontents as $line) {
            if (!$this->validate_line($line)) {
                $this->bad_lines[$linecount] = $line;

                unset($this->filecontents[$linecount - 1]);
            }

            $linecount++;
        }
    }

    public function insert_grades() {
        global $CFG;
        global $USER;

        if (!$this->moodleidstogrades) {
            return false;
        }

        $giparams = array('id' => $this->giid, 'courseid' => $this->courseid);

        if (!$grade_item = grade_item::fetch($giparams)) {
            return false;
        }

        foreach ($this->moodleidstogrades as $userid => $grade) {
            $params = array('itemid' => $this->giid, 'userid' => $userid);

            if ($grade_grade = new grade_grade($params)) {
                $grade_grade->grade_item =& $grade_item;

                if ($grade_grade->is_locked()) {
                    continue;
                }
            }

            $result = $grade_item->update_final_grade($userid, $grade, 'import');
        }

        return true;
    }

    public static function validate_line($line) {
    }

    abstract protected function extract_data();
}

// Fixed width grade file.
// 89XXXXXXX 100.00.
// 89XXXXXXX 090.00.
class SmartFileFixed extends SmartFileBase {
    protected $field = 'idnumber';

    public static function validate_line($line) {
        if (smart_is_lsuid2(substr($line, 0, 9))) {
            if (strlen(trim($line)) == 16 && count(explode(' ', $line)) == 2) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(' ', $line);
            $this->ids_to_grades[$fields[0]] = $fields[1];
        }
    }
}

// Insane Fixed width grade file.
// 89XXXXXXX anything you want in here 100.00.
// 89XXXXXXX i mean anything 090.00.
class SmartFileInsane extends SmartFileBase {
    protected $field = 'idnumber';

    public static function validate_line($line) {
        if (smart_is_lsuid2(substr($line, 0, 9))) {
            if (count(explode(' ', $line)) > 2) {
                if (count(explode(',', $line)) > 2) {
                    return false;
                }
                return true;
            }

            return false;
        }

        return false;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(' ', $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}


// Grade file from the Measurement and Evaluation Center.
// XXX89XXXXXXX 100.00.
// XXX89XXXXXXX  90.00.
class SmartFileMEC extends SmartFileBase {
    protected $field = 'idnumber';

    public static function validate_line($line) {
        if (smart_is_mec_lsuid(substr($line, 0, 12))) {
            if (count(explode(' ', $line)) >= 2) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(' ', $line);
            $this->ids_to_grades[substr($fields[0], 3, 9)] = trim(end($fields));
        }
    }
}

// Grade file for LAW students being graded with an anonymous number.
// XXXX,100.00.
// XXXX, 90.00.
class SmartFileAnonymous extends SmartFileBase {
    protected $field = 'anonymous';

    public static function validate_line($line) {
        $fields = array_map('trim', explode(',', $line));
        return smart_is_anon_num($fields[0]) && smart_is_grade($fields[1]) && count($fields) == 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

// Tab-delimited grade file keyed with lsuid that contains extra information.
// 89XXXXXXX    F,  L   M   shortname   data    time    XX  XX  100.00.
// 89XXXXXXX    F,  L   M   shortname   data    time    XX  XX  90.00.
class SmartFileTabLongLsuid extends SmartFileBase {
    protected $field = 'idnumber';

    public static function validate_line($line) {
        $tabs = explode("\t", $line);
        $n = count($tabs);

        return smart_is_lsuid2($tabs[0]) && smart_is_grade($tabs[$n - 1]) && $n > 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}

// Tab-delimited grade file keyed with pawsid.
// pawsid   100.00.
// pawsid   90.00.
class SmartFileTabShortPawsid extends SmartFileBase {
    protected $field = 'username';

    public static function validate_line($line) {
        $tabs = explode("\t", $line);

        if (count($tabs) < 2) {
            return false;
        }

        return smart_is_pawsid($tabs[0]) && smart_is_grade($tabs[1]) && count($tabs) == 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

// Tab-delimited grade file keyed with pawsid that contains extra information.
// pawsid    F,  L   M   shortname   data    time    XX  XX  100.00.
// pawsid    F,  L   M   shortname   data    time    XX  XX  90.00.
class SmartFileTabLongPawsid extends SmartFileBase {
    protected $field = 'username';

    public static function validate_line($line) {
        $tabs = explode("\t", $line);
        $n = count($tabs);

        return smart_is_pawsid($tabs[0]) && smart_is_grade($tabs[$n - 1]) && $n > 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}


// Tab-delimited grade file keyed with lsuid.
// 89XXXXXXX    100.00.
// 89XXXXXXX    90.00.
class SmartFileTabShortLsuid extends SmartFileBase {
    protected $field = 'idnumber';

    public static function validate_line($line) {
        $tabs = explode("\t", $line);

        return smart_is_lsuid2($tabs[0]) && smart_is_grade($tabs[1]) && count($tabs) == 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

// Grade file with comma-separated values keyed with pawsid.
// pawsid,100.00.
// pawsid,90.00.
class SmartFileCSVPawsid extends SmartFileBase {
    protected $field = 'username';

    public static function validate_line($line) {
        $fields = array_map('trim', explode(',', $line));

        if (count($fields) < 2) {
            return false;
        }

        return smart_is_pawsid($fields[0]) && smart_is_grade($fields[1]) && count($fields) == 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

// Grade file with comma-separated values keyed with lsuid.
// 89XXXXXXX,100.00.
// 89XXXXXXX,90.00.
class SmartFileCSVLsuid extends SmartFileBase {
    protected $field = 'idnumber';

    public static function validate_line($line) {
        $fields = array_map('trim', explode(',', $line));

        return smart_is_lsuid2($fields[0]) && smart_is_grade($fields[1]) && count($fields) == 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

// Comma seperated grade file keyed with lsuid that contains extra information.
// 89XXXXXXX,    F,  L,   M,   shortname,   data,    time,    XX,  XX,  100.00.
// 89XXXXXXX,    F,  L,   M,   shortname,   data,    time,    XX,  XX,  90.00.
class SmartFileCommaLongLsuid extends SmartFileBase {
    protected $field = 'idnumber';

    public static function validate_line($line) {
        $commas = explode(',', $line);
        $n = count($commas);

        return smart_is_lsuid2($commas[0]) && smart_is_grade($commas[$n - 1]) && $n > 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(",", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}

// Comma seperated grade file keyed with pawsid that contains extra information.
// pawsid,    F,  L,   M,   shortname,   data,    time,    XX,  XX,  100.00.
// pawsid,    F,  L,   M,   shortname,   data,    time,    XX,  XX,  90.00.
class SmartFileCommaLongPawsid extends SmartFileBase {
    protected $field = 'username';

    public static function validate_line($line) {
        $commas = explode(',', $line);
        $n = count($commas);

        return smart_is_pawsid($commas[0]) && smart_is_grade($commas[$n - 1]) && $n > 2;
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(",", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}



// Grade file from the Maple software package.
// Irrelevant line.
// Irrelevant line.
// Name, 89XXXXXXX, Grade %, Grade, Weighted %, Blank Field.
// Name, 89XXXXXXX, Grade %, Grade, Weighted %, Blank Field.
// Irrelevant line.
// Irrelevant line.
class SmartFileMaple extends SmartFileBase {
    protected $field = 'idnumber';

    public function __construct($filecontents) {
        $lines = smart_split_file($this->filecontents);
        $this->filecontents = array_slice($lines, 2, count($lines) - 4);
    }

    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 6 && smart_is_lsuid2($fields[1]) && is_numeric($fields[3]);
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[1]] = $fields[3];
        }
    }
}

// Grade file from the turning software package.
// Irrelevant line.
// LSU Email, Grade.
// LSU Email, Grade.
class SmartFileTurning extends SmartFileBase {
    protected $field = 'email';

    public function __construct($filecontents) {
        $lines = smart_split_file($filecontents);
        $this->filecontents = array_slice($lines, 1, count($lines) - 1);
    }

    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 2 && smart_is_email($fields[0]) && is_numeric($fields[1]);
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

// Grade file with email and grade.
// LSU Email, Grade.
// LSU Email, Grade.
class SmartFileEmail extends SmartFileBase {
    protected $field = 'email';

    public function __construct($filecontents) {
        $lines = smart_split_file($filecontents);
        $this->filecontents = array_slice($lines, 0, count($lines));
    }

    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 2 && smart_is_email($fields[0]) && is_numeric($fields[1]);
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

// Grade file with comma-separated values keyed with keypadid.
// 170E98,30.
// 1718C0,80.
class SmartFileKeypadidCSV extends SmartFileBase {
    protected $field = 'user_keypadid';

    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 2 && smart_is_keypadid($fields[0]) && is_numeric($fields[1]);
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = $fields[1];
        }
    }

}


// Grade file with tabbed or spaced values keyed with keypadid.
// 170E98  30.
// 1718C0  80.
class SmartFileKeypadidTabbed extends SmartFileBase {
    protected $field = 'user_keypadid';

    public static function validate_line($line) {
        $fields = preg_split('/\s+/', $line);

        return count($fields) == 2 && smart_is_keypadid($fields[0]) && is_numeric($fields[1]);
    }

    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = preg_split('/\s+/', $line);
            $this->ids_to_grades[$fields[0]] = $fields[1];
        }
    }

}