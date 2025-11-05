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
 * Contains the class hierarchy for the Smart File Importer plugin.
 *
 * @package    gradeimport_smart
 * @copyright  2008 onwards Robert Russo, Jason Peak, Philip Cali, Adam Zapletal
 * @copyright  2008 onwards Louisiana State University
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('lib.php');
require_once($CFG->dirroot.'/grade/lib.php');

/**
 * Base class for handling different grade file formats.
 *
 * This abstract class provides the core functionality for parsing a file,
 * validating its contents, mapping student identifiers to Moodle user IDs,
 * and inserting grades into the gradebook.
 *
 * @package    gradeimport_smart
 * @copyright  2008 onwards Robert Russo, Jason Peak, Philip Cali, Adam Zapletal
 * @copyright  2008 onwards Louisiana State University
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class SmartFileBase {
    /** @var int Grade item id we will be mapping these grades to upon insertion. */
    private $giid;

    /** @var array Lines of the uploaded file. */
    protected $filecontents;

    /** @var string Localized string for the name of this file type. */
    protected $name;

    /** @var array Maps either pawsids, lsuids or anon numbers to grades. */
    public $ids_to_grades = array();

    /** @var array Invalid lines in the file. */
    public $bad_lines = array();

    /** @var array Any ids in the uploaded file that did not exist in the course. */
    public $bad_ids = array();

    /** @var int The course id for the import. */
    protected $courseid;

    /** @var array Maps moodle userids to grades. */
    protected $moodleidstogrades = array();

    /**
     * Constructor.
     *
     * @param string $filecontents The raw content of the uploaded file.
     */
    public function __construct($filecontents) {
        $this->filecontents = smart_split_file($filecontents);
    }

    /**
     * Sets the grade item ID for the import.
     *
     * @param int $giid The grade item ID.
     */
    public function set_gi_id($giid) {
        $this->giid = $giid;
    }

    /**
     * Sets the course ID for the import.
     *
     * @param int $courseid The course ID.
     */
    public function set_courseid($courseid) {
        $this->courseid = $courseid;
    }

    /**
     * Gets the name of the file type.
     *
     * @return string The name of the file type.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Gets the user profile field to match against.
     *
     * @return string The user profile field.
     */
    public function get_field() {
        return $this->field;
    }

    /**
     * Returns an array of whatever id field is the key of ids_to_grades.
     *
     * @return array An array of student identifiers.
     */
    public function get_ids() {
        return array_keys($this->ids_to_grades);
    }

    /**
     * Gets users with keypad IDs from the course.
     *
     * @param array $roleids An array of role IDs to include.
     * @param stdClass $context The course context.
     * @return array An array of user objects.
     */
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

    /**
     * Get all user name fields for compatibility.
     *
     * @param string $alias The user table alias.
     * @return string The SQL select statement for user name fields.
     */
    public static function get_all_user_name_fields_compat(string $alias = 'u'): string {
        $fields = \core_user\fields::for_name()->get_sql($alias);
        return $fields->selects;
    }

    /**
     * Takes $ids_to_grades and fills $moodleidstogrades.
     */
    public function convert_ids() {
        global $CFG;

        $roleids = explode(',', $CFG->gradebookroles);
        $context = context_course::instance($this->courseid);
        $moodleidstofield = array();
        $userfields = 'u.id, u.email, ' . self::get_all_user_name_fields_compat('u');
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

    /**
     * This is called after the filetype is discovered. Every line is
     * individually validated and removed if it doesn't pass.
     */
    public function validate() {
        $linecount = 1;

        foreach ($this->filecontents as $line) {
            if (!$this->validate_line($line)) {

                if ($line != '') {
                    $this->bad_lines[$linecount] = $line;
                }

                unset($this->filecontents[$linecount - 1]);
            }

            $linecount++;
        }
    }

    /**
     * Inserts the grades into the gradebook.
     *
     * @return bool True on success, false on failure.
     */
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

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
    }

    /**
     * Extracts data from the file and populates the ids_to_grades array.
     */
    abstract protected function extract_data();
}

/**
 * Handles fixed-width grade files.
 * Format: NNNNNNNN 100.00
 */
class SmartFileFixed extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        if (smart_is_lsuid2(substr($line, 0, 8))) {
            if (strlen(trim($line)) == 16 && count(explode(' ', $line)) == 2) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(' ', $line);
            $this->ids_to_grades[$fields[0]] = $fields[1];
        }
    }
}

/**
 * Handles "insane" fixed-width grade files with extra data.
 * Format: NNNNNNNN anything you want in here 100.00
 */
class SmartFileInsane extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        if (smart_is_lsuid2(substr($line, 0, 8))) {
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

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(' ', $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}


/**
 * Handles grade files from the Measurement and Evaluation Center.
 * Format: XXXNNNNNNNN 100.00
 */
class SmartFileMEC extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        if (smart_is_mec_lsuid(substr($line, 0, 11))) {
            if (count(explode(' ', $line)) >= 2) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(' ', $line);
            $this->ids_to_grades[substr($fields[0], 3, 8)] = trim(end($fields));
        }
    }
}

/**
 * Handles grade files with anonymous numbers for LAW students.
 * Format: XXXX,100.00
 */
class SmartFileAnonymous extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'anonymous';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = array_map('trim', explode(',', $line));
        return smart_is_anon_num($fields[0]) && smart_is_grade($fields[1]) && count($fields) == 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

/**
 * Handles tab-delimited grade files keyed with LSUID and containing extra information.
 * Format: NNNNNNNN    F,  L   M   shortname   data    time    XX  XX  100.00
 */
class SmartFileTabLongLsuid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $tabs = array_map('trim', explode("\t", $line));
        $n = count($tabs);

        return smart_is_lsuid2($tabs[0]) && smart_is_grade(trim(end($tabs))) && $n > 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}

/**
 * Handles tab-delimited grade files keyed with email.
 * Format: email   100.00
 */
class SmartFileTabShortPawsid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'email';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $tabs = explode("\t", $line);

        if (count($tabs) < 2) {
            return false;
        }

        return smart_is_email($tabs[0]) && smart_is_grade($tabs[1]) && count($tabs) == 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

/**
 * Handles tab-delimited grade files keyed with pawsid and containing extra information.
 * Format: email    F,  L   M   shortname   data    time    XX  XX  100.00
 */
class SmartFileTabLongPawsid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'email';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $tabs = explode("\t", $line);
        $n = count($tabs);

        return smart_is_email($tabs[0]) && smart_is_grade(trim(end($tabs))) && $n > 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}


/**
 * Handles tab-delimited grade files keyed with lsuid.
 * Format: NNNNNNNN    100.00
 */
class SmartFileTabShortLsuid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $tabs = explode("\t", $line);

        return smart_is_lsuid2($tabs[0]) && smart_is_grade($tabs[1]) && count($tabs) == 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode("\t", $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

/**
 * Handles grade files with comma-separated values keyed with email.
 * Format: email,100.00
 */
class SmartFileCSVPawsid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'email';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = array_map('trim', explode(',', $line));

        if (count($fields) < 2) {
            return false;
        }

        return smart_is_email($fields[0]) && smart_is_grade($fields[1]) && count($fields) == 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

/**
 * Handles grade files with comma-separated values keyed with lsuid.
 * Format: NNNNNNNN,100.00
 */
class SmartFileCSVLsuid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = array_map('trim', explode(',', $line));

        return smart_is_lsuid2($fields[0]) && smart_is_grade($fields[1]) && count($fields) == 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

/**
 * Handles comma-separated grade files keyed with lsuid that contain extra information.
 * Format: NNNNNNNN, F, L, M, shortname, data, time, XX, XX, 100.00
 */
class SmartFileCommaLongLsuid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $commas = explode(',', $line);
        $n = count($commas);

        return smart_is_lsuid2($commas[0]) && smart_is_grade(trim(end($commas))) && $n > 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(",", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}

/**
 * Handles comma-separated grade files keyed with pawsid that contain extra information.
 * Format: email, F, L, M, shortname, data, time, XX, XX, 100.00
 */
class SmartFileCommaLongPawsid extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'email';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $commas = explode(',', $line);
        $n = count($commas);

        return smart_is_email($commas[0]) && smart_is_grade($commas[$n - 1]) && $n > 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(",", $line);
            $this->ids_to_grades[$fields[0]] = trim(end($fields));
        }
    }
}



/**
 * Handles grade files from the Maple software package.
 * The first two and last two lines are ignored.
 * Format: Name, NNNNNNNN, Grade %, Grade, Weighted %, Blank Field
 */
class SmartFileMaple extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'idnumber';

    /**
     * Constructor.
     *
     * @param string $filecontents The raw content of the uploaded file.
     */
    public function __construct($filecontents) {
        $lines = smart_split_file($this->filecontents);
        $this->filecontents = array_slice($lines, 2, count($lines) - 4);
    }

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 6 && smart_is_lsuid2($fields[1]) && is_numeric($fields[3]);
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[1]] = $fields[3];
        }
    }
}

/**
 * Handles grade files from the Turning Technologies software package.
 * The first line is ignored.
 * Format: LSU Email, Grade
 */
class SmartFileTurning extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'email';

    /**
     * Constructor.
     *
     * @param string $filecontents The raw content of the uploaded file.
     */
    public function __construct($filecontents) {
        $lines = smart_split_file($filecontents);
        $this->filecontents = array_slice($lines, 1, count($lines) - 1);
    }

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 2 && smart_is_email($fields[0]) && is_numeric($fields[1]);
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

/**
 * Handles grade files with email and grade.
 * Format: LSU Email, Grade
 */
class SmartFileEmail extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'email';

    /**
     * Constructor.
     *
     * @param string $filecontents The raw content of the uploaded file.
     */
    public function __construct($filecontents) {
        $lines = smart_split_file($filecontents);
        $this->filecontents = array_slice($lines, 0, count($lines));
    }

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 2 && smart_is_email($fields[0]) && is_numeric($fields[1]);
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = trim($fields[1]);
        }
    }
}

/**
 * Handles grade files with comma-separated values keyed with keypadid.
 * Format: 170E98,30
 */
class SmartFileKeypadidCSV extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'user_keypadid';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = explode(',', $line);

        return count($fields) == 2 && smart_is_keypadid($fields[0]) && is_numeric($fields[1]);
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[$fields[0]] = $fields[1];
        }
    }
}

/**
 * Handles grade files with comma-separated values keyed with 89 numbers.
 * Format: 891234567,89.02
 */
class SmartFile89NumberCSV extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'school_id';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = array_map('trim', explode(',', $line));

        if (count($fields) < 2) {
            return false;
        }

        return smart_is_89_number($fields[0]) && smart_is_grade($fields[1]) && count($fields) == 2;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = explode(',', $line);
            $this->ids_to_grades[trim($fields[0])] = trim($fields[1]);
        }
    }

    /**
     * Gets users with 89 numbers from the course.
     *
     * @param array $roleids An array of role IDs to include.
     * @param stdClass $context The course context.
     * @return array An array of user objects.
     */
    public function get_89_users($roleids, $context) {
        global $DB;

        $strings = function($id) {
            return "'$id'";
        };
        $roleusers = array();

        foreach ($roleids as $roleid) {
            $roleusers = $roleusers + get_role_users($roleid, $context, false);
        }

        $roleuserids = implode(',', array_keys($roleusers));

        $schoolids = array_keys($this->ids_to_grades);
        $keys = implode(',', array_map($strings, $schoolids));

        $sql = 'SELECT u.*, s.school_id
            FROM {user} u
            JOIN {enrol_wds_students} s ON u.id = s.userid
            WHERE s.school_id IN (' . $keys . ')
              AND u.id IN (' . $roleuserids . ')';

        return $DB->get_records_sql($sql);
    }

    /**
     * Takes $ids_to_grades and fills $moodleidstogrades.
     */
    public function convert_ids() {
        global $CFG;

        $roleids = explode(',', $CFG->gradebookroles);
        $context = context_course::instance($this->courseid);
        $users = $this->get_89_users($roleids, $context);

        $moodleidstofield = array();
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
}

/**
 * Handles Scantron fixed-width grade files.
 * Format: NNN 89NNNNNNN ... 071.43
 */
class SmartFileScantron extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'school_id';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = preg_split('/\s+/', trim($line));

        if (count($fields) < 2) {
            return false;
        }

        // Check for Scantron format indicators.
        if (!preg_match('/^\d{3}$/', $fields[0])) {
            return false;
        }

        if (!smart_is_89_number($fields[1])) {
            return false;
        }

        if (!smart_is_grade(end($fields))) {
            return false;
        }

        return true;
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = preg_split('/\s+/', trim($line));
            $this->ids_to_grades[$fields[1]] = end($fields);
        }
    }

    /**
     * Gets users with 89 numbers from the course.
     *
     * @param array $roleids An array of role IDs to include.
     * @param stdClass $context The course context.
     * @return array An array of user objects.
     */
    public function get_89_users($roleids, $context) {
        global $DB;

        $strings = function($id) {
            return "'$id'";
        };
        $roleusers = array();

        foreach ($roleids as $roleid) {
            $roleusers = $roleusers + get_role_users($roleid, $context, false);
        }

        if (empty($roleusers)) {
            return [];
        }
        $roleuserids = implode(',', array_keys($roleusers));

        $schoolids = array_keys($this->ids_to_grades);
        if (empty($schoolids)) {
            return [];
        }
        $keys = implode(',', array_map($strings, $schoolids));

        $sql = 'SELECT u.*, s.school_id
            FROM {user} u
            JOIN {enrol_wds_students} s ON u.id = s.userid
            WHERE s.school_id IN (' . $keys . ')
              AND u.id IN (' . $roleuserids . ')';

        return $DB->get_records_sql($sql);
    }

    /**
     * Takes $ids_to_grades and fills $moodleidstogrades.
     */
    public function convert_ids() {
        global $CFG;

        $roleids = explode(',', $CFG->gradebookroles);
        $context = context_course::instance($this->courseid);
        $users = $this->get_89_users($roleids, $context);

        $moodleidstofield = array();
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
}


/**
 * Handles grade files with tabbed or spaced values keyed with keypadid.
 * Format: 170E98  30
 */
class SmartFileKeypadidTabbed extends SmartFileBase {
    /** @var string The user profile field to match against. */
    protected $field = 'user_keypadid';

    /**
     * Validates a single line of the file.
     *
     * @param string $line The line to validate.
     * @return bool True if the line is valid, false otherwise.
     */
    public static function validate_line($line) {
        $fields = preg_split('/\s+/', $line);

        return count($fields) == 2 && smart_is_keypadid($fields[0]) && is_numeric($fields[1]);
    }

    /**
     * Extracts data from the file.
     */
    public function extract_data() {
        foreach ($this->filecontents as $line) {
            $fields = preg_split('/\s+/', $line);
            $this->ids_to_grades[$fields[0]] = $fields[1];
        }
    }

}
