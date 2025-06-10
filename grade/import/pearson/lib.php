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
 * @package    grade_import_pearson
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Robert Russo, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/import/lib.php');

defined('MOODLE_INTERNAL') || die();

/*
 * Grabs and modifies the uploaded file for future use.
 *
 * @return $pearson_file
 *
 */
function pearson_create_file($file_text, $file_type) {
    global $COURSE;

    if (in_array($file_type, array(0, 1))) {
        $pearson_file = new PearsonMyLabFile($file_text, $COURSE->id);
    } else {
        $pearson_file = new PearsonMasteringFile($file_text, $COURSE->id);
    }

    $pearson_file->extract_headers();

    return $pearson_file;
}

abstract class PearsonFile {
    public $lines;
    public $courseid;
    public $id_field;
    public $file_text;
    public $percents;
    public $headers = array();
    public $messages = array();
    public $ids_to_grades = array();
    public $users_not_found = array();
    public $moodle_ids_to_grades = array();

    abstract public function parse($headers_to_items);
    abstract public function preprocess_headers();
    abstract public function discern_id_field();

    function __construct($file_text, $courseid) {
        $this->file_text = $file_text;
        $this->lines = explode("\n", (string)$file_text);

        $this->id_field = $this->discern_id_field();

        $this->courseid = $courseid;
    }

    function convert_ids() {
        global $CFG;

        $_s = function($key, $a) { return get_string($key, 'gradeimport_pearson', $a); };

        $roleids = explode(',', $CFG->gradebookroles);
        $context = context_course::instance($this->courseid);

        $fields = 'u.id, u.' . $this->id_field;

        $moodle_ids_to_field = array();

        $users = array();
        foreach ($roleids as $k => &$roleid) {
            $users[] = get_role_users($roleid, $context, $parent = false);
        }
        $found_users = array();

        foreach ($users as $collection) {
            foreach ($collection as $k => $v) {
                $moodle_ids_to_field[$k] = $v->{$this->id_field};
                $found_users[] = $v->{$this->id_field};
            }
        }

        foreach ($moodle_ids_to_field as $k => $v) {
            foreach ($this->ids_to_grades as $gi_id => $user_and_grade) {
                $ids_only = array_keys($this->ids_to_grades[$gi_id]);

                if (!array_key_exists($gi_id, $this->moodle_ids_to_grades)) {
                    $this->moodle_ids_to_grades[$gi_id] = array();
                }

                $found = array_search($v, $ids_only);

                if ($found !== false) {
                    $vv = $this->ids_to_grades[$gi_id][$ids_only[$found]];
                    $this->moodle_ids_to_grades[$gi_id][$k] = $vv;
                }
            }
        }

        $all_users = array();

        foreach ($this->ids_to_grades as $gi_id => $grades) {
            $all_users += array_keys($grades);
        }

        $all_users = array_unique($all_users);

        $this->users_not_found = array_diff($all_users, $found_users);

        foreach ($this->users_not_found as $user) {
            $this->messages[] = $_s('user_not_found', $user);
        }
    }

    function extract_headers() {
        $headers_raw = $this->preprocess_headers();

        $current_header = '';
        $quote_count = 0;
        $last = ',';

        foreach (str_split($headers_raw) as $c) {
            if ($c == '"') {
                $quote_count++;
            }

            $is_quote = $c == '"';
            $is_comma = $c == ',';
            $count_even = $quote_count % 2 == 0;
            $count_odd = $quote_count % 2 == 1;

            if ($is_quote && $count_even) {
                $this->headers[] = $current_header;
                $current_header = '';
            } else if ($is_quote && $count_odd) {
                // Skip $c
            } else if ($is_comma && $count_even) {
                if ($last != '"') {
                    $this->headers[] = $current_header;
                    $current_header = '';
                }
            } else {
                $current_header .= $c;
            }

            $last = $c;
        }

        if ($current_header) {
            $this->headers[] = $current_header;
        }
    }

    function import_grades() {
        global $DB, $USER;

        $_s = function($key, $a=null) { return get_string($key, 'gradeimport_pearson', $a); };
        $_g = function($key) { return get_string($key, 'grades'); };

        $importcode = get_new_importcode();

        foreach ($this->moodle_ids_to_grades as $gi_id => $grades) {
            $gi_params = array('id' => $gi_id, 'courseid' => $this->courseid);

            if (!$grade_item = grade_item::fetch($gi_params)) {
                continue;
            }

            $grademax = $grade_item->grademax;

            foreach ($grades as $userid => $grade) {
                // Make sure grade_grade isn't locked
                $grade_params = array('itemid'=>$gi_id, 'userid'=>$userid);

                if ($grade_grade = new grade_grade($grade_params)) {
                    $grade_grade->grade_item =& $grade_item;

                    if ($grade_grade->is_locked()) {
                        continue;
                    }
                }

                $newgrade = (object) new stdClass();
                $newgrade->itemid = $grade_item->id;
                $newgrade->userid = $userid;
                $newgrade->importcode = $importcode;
                $newgrade->importer = $USER->id;

                // We have a normalized percentage grade.
                if ($this->percents) {
                    // Normalized percentages converted to a raw moodle grade by multiplying by grademax.
                    $newgrade->finalgrade = $grade * $grademax;

                // We have a raw percentage grade.
                } else if (!$this->percents) {
                    // Normalize the raw percentage grade and multiply it by the grademax.
                    $newgrade->finalgrade = (($grade / 100) * $grademax);
                }

                if (!$DB->insert_record('grade_import_values', $newgrade)) {
                    $this->messages[] = $_g('importfailed');
                }
            }
        }

        return grade_import_commit($this->courseid, $importcode, false, false);
    }

    function process($headers_to_items) {
        $this->parse($headers_to_items);
        $this->convert_ids();

        return $this->import_grades();
    }
}

class PearsonMyLabFile extends PearsonFile {
    function preprocess_headers() {
        return trim(trim($this->lines[0]), ',');
    }

    function discern_id_field() {
        return 'username';
    }

    function parse($headers_to_items) {
        $exploded = explode('Course:', $this->file_text);
        $lines = explode("\n", reset($exploded));

        $keepers = array_slice($lines, 5);

        $headers_to_grades = array();

        $this->percents = true;

        // Build a percent array to see what's what regarding grade values.
        $pa = array();

        foreach ($keepers as $line) {
            if (trim($line) == '') {
                continue;
            }

            $fields = explode(',', $line);

            array_pop($fields);
            $pawsid = strtolower($fields[2]);

            $grades = array_slice($fields, 5);

            while (count($grades) < count($this->headers)) {
                $grades[] = 0.000;
            }

            foreach ($grades as $n => $grade) {
                if (!isset($headers_to_grades[$n])) {
                    $headers_to_grades[$n] = array();
                }

                $pa[] = $grade;

                if (!$grade) {
                    $grade = 0.000;
                }

                $headers_to_grades[$n][$pawsid] = $grade;
            }
        }

        $arrayval = array_sum($pa) / count($pa);

        $this->percents = $arrayval >= 1 ? false : true;

        foreach ($headers_to_items as $i => $gi_id) {
            $this->ids_to_grades[$gi_id] = $headers_to_grades[$i];
        }
    }
}

class PearsonMasteringFile extends PearsonFile {
    function preprocess_headers() {

        if ($this->lines[0]) {
            $exploded = explode('Group(s),', $this->lines[3]);
            return end($exploded);
        }
    }

    function discern_id_field() {
        return 'username';
    }

    function parse($headers_to_items) {
        $lines = explode("\n", $this->file_text);

        $keepers = array_slice($lines, 4);

        $headers_to_grades = array();

        $this->percents = true;

        // Build a percent array to see what's what regarding grade values.
        $pa = array();

        foreach ($keepers as $n => $line) {
            if (!$line) {
                continue;
            }

            if (strpos($line, '"","","","","",Average:,') !== False) {
                continue;
            }

            $fields = explode(',', $line);

            if (!isset($fields[2])) {
                continue;
            }
            $username = strtolower($fields[2]);
            $grades = array_slice($fields, 5);

            foreach ($grades as $n => $grade) {
                if (!isset($headers_to_grades[$n])) {
                    $headers_to_grades[$n] = array();
                }

                $pa[] = $grade;

                if (!$grade || $grade == '--') {
                    $grade = 0.000;
                }

                $headers_to_grades[$n][$username] = $grade;
            }
        }

        $arrayval = array_sum($pa) / count($pa);

        $this->percents = $arrayval >= 1 ? false : true;

        foreach ($headers_to_items as $i => $gi_id) {
            $this->ids_to_grades[$gi_id] = $headers_to_grades[$i];
        }
    }
}

class helpers {

    /**
     * Config Converter - config settings that have multiple lines with
     * a key value settings will be broken down and converted into an
     * associative array, for example:
     * Monthly 720,
     * Weekly 168
     * .....etc
     * Becomes (Monthly => 720, Weekly => 168)
     * @param  string $configstring setting
     * @param  string $arraytype by default multi, use mirror to miror key/value
     *
     * @return array
     */
    public static function config_to_array($configstring, $arraytype = "multi") {

        $configname = get_config('moodle', $configstring);

        // Strip the line breaks.
        $breakstripped = preg_replace("/\r|\n/", " ", $configname);
        // Make sure there are not double spaces.
        $breakstripped = str_replace("  ", " ", $breakstripped);
        // Remove any spaces or line breaks from start or end.
        $breakstripped = trim($breakstripped);

        $exploded = explode(" ", $breakstripped);
        $explodedcount = count($exploded);
        $final = array();

        if ($arraytype == "multi") {
            // Now convert to array and transform to an assoc. array.
            for ($i = 0; $i < $explodedcount; $i += 2) {
                $final[$exploded[$i + 1]] = $exploded[$i];
            }
        } else if ($arraytype == "mirror") {
            // It's possible there may be an extra line break from user input.
            for ($i = 0; $i < $explodedcount; $i++) {
                $tempval = $exploded[$i];
                $final[$tempval] = $tempval;
            }
        }
        return $final;
    }

    /**
     * Replace ASCII chars with UTF-8. Note there are ASCII characters that don't
     * correctly map and will be replaced by spaces. Return the updated string.
     * @param  string $string the string go compare the map against.
     *
     * @return string
     */
    public static function fixMSWord($string) {
        $map = Array(
            '33' => '!', '34' => '"', '35' => '#', '36' => '$', '37' => '%', '38' => '&', '39' => "'", '40' => '(', '41' => ')', '42' => '*',
            '43' => '+', '44' => ',', '45' => '-', '46' => '.', '47' => '/', '48' => '0', '49' => '1', '50' => '2', '51' => '3', '52' => '4',
            '53' => '5', '54' => '6', '55' => '7', '56' => '8', '57' => '9', '58' => ':', '59' => ';', '60' => '<', '61' => '=', '62' => '>',
            '63' => '?', '64' => '@', '65' => 'A', '66' => 'B', '67' => 'C', '68' => 'D', '69' => 'E', '70' => 'F', '71' => 'G', '72' => 'H',
            '73' => 'I', '74' => 'J', '75' => 'K', '76' => 'L', '77' => 'M', '78' => 'N', '79' => 'O', '80' => 'P', '81' => 'Q', '82' => 'R',
            '83' => 'S', '84' => 'T', '85' => 'U', '86' => 'V', '87' => 'W', '88' => 'X', '89' => 'Y', '90' => 'Z', '91' => '[', '92' => '\\',
            '93' => ']', '94' => '^', '95' => '_', '96' => '`', '97' => 'a', '98' => 'b', '99' => 'c', '100'=> 'd', '101'=> 'e', '102'=> 'f',
            '103'=> 'g', '104'=> 'h', '105'=> 'i', '106'=> 'j', '107'=> 'k', '108'=> 'l', '109'=> 'm', '110'=> 'n', '111'=> 'o', '112'=> 'p',
            '113'=> 'q', '114'=> 'r', '115'=> 's', '116'=> 't', '117'=> 'u', '118'=> 'v', '119'=> 'w', '120'=> 'x', '121'=> 'y', '122'=> 'z',
            '123'=> '{', '124'=> '|', '125'=> '}', '126'=> '~', '127'=> ' ', '128'=> '&#8364;', '129'=> ' ', '130'=> ',', '131'=> ' ', '132'=> '"',
            '133'=> '.', '134'=> ' ', '135'=> ' ', '136'=> '^', '137'=> ' ', '138'=> ' ', '139'=> '<', '140'=> ' ', '141'=> ' ', '142'=> ' ',
            '143'=> ' ', '144'=> ' ', '145'=> "'", '146'=> "'", '147'=> '"', '148'=> '"', '149'=> '.', '150'=> '-', '151'=> '-', '152'=> '~',
            '153'=> ' ', '154'=> ' ', '155'=> '>', '156'=> ' ', '157'=> ' ', '158'=> ' ', '159'=> ' ', '160'=> ' ', '161'=> '¡', '162'=> '¢',
            '163'=> '£', '164'=> '¤', '165'=> '¥', '166'=> '¦', '167'=> '§', '168'=> '¨', '169'=> '©', '170'=> 'ª', '171'=> '«', '172'=> '¬',
            '173'=> '­', '174'=> '®', '175'=> '¯', '176'=> '°', '177'=> '±', '178'=> '²', '179'=> '³', '180'=> '´', '181'=> 'µ', '182'=> '¶',
            '183'=> '·', '184'=> '¸', '185'=> '¹', '186'=> 'º', '187'=> '»', '188'=> '¼', '189'=> '½', '190'=> '¾', '191'=> '¿', '192'=> 'À',
            '193'=> 'Á', '194'=> 'Â', '195'=> 'Ã', '196'=> 'Ä', '197'=> 'Å', '198'=> 'Æ', '199'=> 'Ç', '200'=> 'È', '201'=> 'É', '202'=> 'Ê',
            '203'=> 'Ë', '204'=> 'Ì', '205'=> 'Í', '206'=> 'Î', '207'=> 'Ï', '208'=> 'Ð', '209'=> 'Ñ', '210'=> 'Ò', '211'=> 'Ó', '212'=> 'Ô',
            '213'=> 'Õ', '214'=> 'Ö', '215'=> '×', '216'=> 'Ø', '217'=> 'Ù', '218'=> 'Ú', '219'=> 'Û', '220'=> 'Ü', '221'=> 'Ý', '222'=> 'Þ',
            '223'=> 'ß', '224'=> 'à', '225'=> 'á', '226'=> 'â', '227'=> 'ã', '228'=> 'ä', '229'=> 'å', '230'=> 'æ', '231'=> 'ç', '232'=> 'è',
            '233'=> 'é', '234'=> 'ê', '235'=> 'ë', '236'=> 'ì', '237'=> 'í', '238'=> 'î', '239'=> 'ï', '240'=> 'ð', '241'=> 'ñ', '242'=> 'ò',
            '243'=> 'ó', '244'=> 'ô', '245'=> 'õ', '246'=> 'ö', '247'=> '÷', '248'=> 'ø', '249'=> 'ù', '250'=> 'ú', '251'=> 'û', '252'=> 'ü',
           '253'=> 'ý', '254'=> 'þ', '255'=> 'ÿ' ,'c389'=>'é'
        );

        $search = Array();
        $replace = Array();

        foreach ($map as $s => $r) {
            $search[] = chr((int)$s);
            $replace[] = $r;
        }

        return str_replace($search, $replace, $string);
    }
}
