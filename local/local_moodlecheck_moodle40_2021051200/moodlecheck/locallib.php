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
 * Usefull classes for package local_moodlecheck
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot. '/local/moodlecheck/file.php');

/**
 * Handles one rule
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_moodlecheck_rule {
    protected $code;
    protected $callback;
    protected $rulestring;
    protected $errorstring;
    protected $severity = 'error';

    public function __construct($code) {
        $this->code = $code;
    }

    public function set_callback($callback) {
        $this->callback = $callback;
        return $this;
    }

    public function set_rulestring($rulestring) {
        $this->rulestring = $rulestring;
        return $this;
    }

    public function set_errorstring($errorstring) {
        $this->errorstring = $errorstring;
        return $this;
    }

    public function set_severity($severity) {
        $this->severity = $severity;
        return $this;
    }

    public function get_name() {
        if ($this->rulestring !== null && get_string_manager()->string_exists($this->rulestring, 'local_moodlecheck')) {
            return get_string($this->rulestring, 'local_moodlecheck');
        } else if (get_string_manager()->string_exists('rule_'. $this->code, 'local_moodlecheck')) {
            return get_string('rule_'. $this->code, 'local_moodlecheck');
        } else {
            return $this->code;
        }
    }

    public function get_error_message($args) {
        if (strlen($this->errorstring) && get_string_manager()->string_exists($this->errorstring, 'local_moodlecheck')) {
            return get_string($this->errorstring, 'local_moodlecheck', $args);
        } else if (get_string_manager()->string_exists('error_'. $this->code, 'local_moodlecheck')) {
            return get_string('error_'. $this->code, 'local_moodlecheck', $args);
        } else {
            if (isset($args['line'])) {
                // Do not dump line number, it will be included in the final message.
                unset($args['line']);
            }
            if (is_array($args)) {
                $args = ': '. var_export($args, true);
            } else if ($args !== true && $args !== null) {
                $args = ': '. $args;
            } else {
                $args = '';
            }
            return $this->get_name(). '. Error'. $args;
        }
    }

    public function validatefile(local_moodlecheck_file $file) {
        $callback = $this->callback;
        $reterrors = $callback($file);
        $ruleerrors = array();
        foreach ($reterrors as $args) {
            $ruleerrors[] = array(
                'line' => $args['line'],
                'severity' => $this->severity,
                'message' => $this->get_error_message($args),
                'source' => $this->code
            );
        }
        return $ruleerrors;
    }
}

/**
 * Rule registry
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_moodlecheck_registry {
    protected static $rules = array();
    protected static $enabledrules = array();

    public static function add_rule($code) {
        $rule = new local_moodlecheck_rule($code);
        self::$rules[$code] = $rule;
        return $rule;
    }

    public static function get_registered_rules() {
        return self::$rules;
    }

    public static function enable_rule($code, $enable = true) {
        if (!isset(self::$rules[$code])) {
            // Can not enable/disable unexisting rule.
            return;
        }
        if (!$enable) {
            if (isset(self::$enabledrules[$code])) {
                unset(self::$enabledrules[$code]);
            }
        } else {
            self::$enabledrules[$code] = self::$rules[$code];
        }
    }

    public static function &get_enabled_rules() {
        return self::$enabledrules;
    }

    public static function enable_all_rules() {
        foreach (array_keys(self::$rules) as $code) {
            self::enable_rule($code);
        }
    }
}

/**
 * Handles one path being validated (file or directory)
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_moodlecheck_path {
    protected $path = null;
    protected $ignorepaths = null;
    protected $file = null;
    protected $subpaths = null;
    protected $validated = false;
    protected $rootpath = true;

    public function __construct($path, $ignorepaths) {
        $path = clean_param(trim($path), PARAM_PATH);
        // If the path is already one existing full path
        // accept it, else assume it's a relative one.
        if (!file_exists($path) and substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }
        $this->path = $path;
        $this->ignorepaths = $ignorepaths;
    }

    public function get_fullpath() {
        global $CFG;
        // It's already one full path.
        if (file_exists($this->path)) {
            return $this->path;
        }
        return $CFG->dirroot. '/'. $this->path;
    }

    public function validate() {
        if ($this->validated) {
            // Prevent from second validation.
            return;
        }
        if (is_file($this->get_fullpath())) {
            $this->file = new local_moodlecheck_file($this->get_fullpath());
        } else if (is_dir($this->get_fullpath())) {
            $this->subpaths = array();
            if ($dh = opendir($this->get_fullpath())) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' && $file != '..' && $file != '.git'  && $file != '.hg' && !$this->is_ignored($file)) {
                        $subpath = new local_moodlecheck_path($this->path . '/'. $file, $this->ignorepaths);
                        $subpath->set_rootpath(false);
                        $this->subpaths[] = $subpath;
                    }
                }
                closedir($dh);
            }
        }
        $this->validated = true;
    }

    protected function is_ignored($file) {
        $filepath = $this->path. '/'. $file;
        foreach ($this->ignorepaths as $ignorepath) {
            $ignorepath = rtrim($ignorepath, '/');
            if ($filepath == $ignorepath || substr($filepath, 0, strlen($ignorepath) + 1) == $ignorepath . '/') {
                return true;
            }
        }
        return false;
    }

    public function is_file() {
        return $this->file !== null;
    }

    public function is_dir() {
        return $this->subpaths !== null;
    }

    public function get_path() {
        return $this->path;
    }

    public function get_file() {
        return $this->file;
    }

    public function get_subpaths() {
        return $this->subpaths;
    }

    protected function set_rootpath($rootpath) {
        $this->rootpath = (boolean)$rootpath;
    }

    public function is_rootpath() {
        return $this->rootpath;
    }

    public static function get_components($componentsfile = null) {
        static $components = array();
        if (!empty($components)) {
            return $components;
        }
        if (empty($componentsfile)) {
            return array();
        }
        if (file_exists($componentsfile) and is_readable($componentsfile)) {
            $fh = fopen($componentsfile, 'r');
            while (($line = fgets($fh, 4096)) !== false) {
                $split = explode(',', $line);
                if (count($split) != 3) {
                    // Wrong count of elements in the line.
                    continue;
                }
                if (trim($split[0]) != 'plugin' and trim($split[0]) != 'subsystem') {
                    // Wrong type.
                    continue;
                }
                // Let's assume it's a correct line.
                $components[trim($split[0])][trim($split[1])] = trim($split[2]);
            }
            fclose($fh);
        }
        return $components;
    }
}

/**
 * Form for check options
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_moodlecheck_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('textarea', 'path', get_string('path', 'local_moodlecheck'),
            array('rows' => 8, 'cols' => 120));
        $mform->addHelpButton('path', 'path', 'local_moodlecheck');

        $mform->addElement('header', 'selectivecheck', get_string('options'));
        $mform->setExpanded('selectivecheck', false);

        $mform->addElement('textarea', 'ignorepath', get_string('ignorepath', 'local_moodlecheck'),
            array('rows' => 3, 'cols' => 120));

        $mform->addElement('radio', 'checkall', '', get_string('checkallrules', 'local_moodlecheck'), 'all');
        $mform->addElement('radio', 'checkall', '', get_string('checkselectedrules', 'local_moodlecheck'), 'selected');
        $mform->setDefault('checkall', 'all');

        $group = array();
        foreach (local_moodlecheck_registry::get_registered_rules() as $code => $rule) {
            $group[] =& $mform->createElement('checkbox', "rule[$code]", ' ', $rule->get_name());
        }
        $mform->addGroup($group, 'checkboxgroup', '', array('<br>'), false);
        foreach (local_moodlecheck_registry::get_registered_rules() as $code => $rule) {
            $group[] =& $mform->createElement('checkbox', "rule[$code]", ' ', $rule->get_name());
            $mform->setDefault("rule[$code]", 1);
            $mform->disabledIf("rule[$code]", 'checkall', 'eq', 'all');
        }

        $this->add_action_buttons(false, get_string('check', 'local_moodlecheck'));
    }
}
