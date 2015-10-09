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
 * Provides validation class to check the plugin ZIP contents
 *
 * Uses fragments of the local_plugins_archive_validator class copyrighted by
 * Marina Glancy that is part of the local_plugins plugin.
 *
 * @package     core_plugin
 * @subpackage  validation
 * @copyright   2013, 2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\update;

use core_component;
use core_plugin_manager;
use help_icon;
use coding_exception;

defined('MOODLE_INTERNAL') || die();

if (!defined('T_ML_COMMENT')) {
    define('T_ML_COMMENT', T_COMMENT);
} else {
    define('T_DOC_COMMENT', T_ML_COMMENT);
}

/**
 * Validates the contents of extracted plugin ZIP file
 *
 * @copyright 2013, 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class validator {

    /** Critical error message level, causes the validation fail. */
    const ERROR     = 'error';

    /** Warning message level, validation does not fail but the admin should be always informed. */
    const WARNING   = 'warning';

    /** Information message level that the admin should be aware of. */
    const INFO      = 'info';

    /** Debugging message level, should be displayed in debugging mode only. */
    const DEBUG     = 'debug';

    /** @var string full path to the extracted ZIP contents */
    protected $extractdir = null;

    /** @var array as returned by {@link zip_packer::extract_to_pathname()} */
    protected $extractfiles = null;

    /** @var bool overall result of validation */
    protected $result = null;

    /** @var string the name of the plugin root directory */
    protected $rootdir = null;

    /** @var array explicit list of expected/required characteristics of the ZIP */
    protected $assertions = null;

    /** @var array of validation log messages */
    protected $messages = array();

    /** @var array|null array of relevant data obtained from version.php */
    protected $versionphp = null;

    /** @var string|null the name of found English language file without the .php extension */
    protected $langfilename = null;

    /**
     * Factory method returning instance of the validator
     *
     * @param string $zipcontentpath full path to the extracted ZIP contents
     * @param array $zipcontentfiles (string)filerelpath => (bool|string)true or error
     * @return \core\update\validator
     */
    public static function instance($zipcontentpath, array $zipcontentfiles) {
        return new static($zipcontentpath, $zipcontentfiles);
    }

    /**
     * Set the expected plugin type, fail the validation otherwise
     *
     * @param string $required plugin type
     */
    public function assert_plugin_type($required) {
        $this->assertions['plugintype'] = $required;
    }

    /**
     * Set the expectation that the plugin can be installed into the given Moodle version
     *
     * @param string $required Moodle version we are about to install to
     */
    public function assert_moodle_version($required) {
        $this->assertions['moodleversion'] = $required;
    }

    /**
     * Execute the validation process against all explicit and implicit requirements
     *
     * Returns true if the validation passes (all explicit and implicit requirements
     * pass) and the plugin can be installed. Returns false if the validation fails
     * (some explicit or implicit requirement fails) and the plugin must not be
     * installed.
     *
     * @return bool
     */
    public function execute() {

        $this->result = (
                $this->validate_files_layout()
            and $this->validate_version_php()
            and $this->validate_language_pack()
            and $this->validate_target_location()
        );

        return $this->result;
    }

    /**
     * Returns overall result of the validation.
     *
     * Null is returned if the validation has not been executed yet. Otherwise
     * this method returns true (the installation can continue) or false (it is not
     * safe to continue with the installation).
     *
     * @return bool|null
     */
    public function get_result() {
        return $this->result;
    }

    /**
     * Return the list of validation log messages
     *
     * Each validation message is a plain object with properties level, msgcode
     * and addinfo.
     *
     * @return array of (int)index => (stdClass) validation message
     */
    public function get_messages() {
        return $this->messages;
    }

    /**
     * Returns human readable localised name of the given log level.
     *
     * @param string $level e.g. self::INFO
     * @return string
     */
    public function message_level_name($level) {
        return get_string('validationmsglevel_'.$level, 'core_plugin');
    }

    /**
     * If defined, returns human readable validation code.
     *
     * Otherwise, it simply returns the code itself as a fallback.
     *
     * @param string $msgcode
     * @return string
     */
    public function message_code_name($msgcode) {

        $stringman = get_string_manager();

        if ($stringman->string_exists('validationmsg_'.$msgcode, 'core_plugin')) {
            return get_string('validationmsg_'.$msgcode, 'core_plugin');
        }

        return $msgcode;
    }

    /**
     * Returns help icon for the message code if defined.
     *
     * @param string $msgcode
     * @return \help_icon|false
     */
    public function message_help_icon($msgcode) {

        $stringman = get_string_manager();

        if ($stringman->string_exists('validationmsg_'.$msgcode.'_help', 'core_plugin')) {
            return new help_icon('validationmsg_'.$msgcode, 'core_plugin');
        }

        return false;
    }

    /**
     * Localizes the message additional info if it exists.
     *
     * @param string $msgcode
     * @param array|string|null $addinfo value for the $a placeholder in the string
     * @return string
     */
    public function message_code_info($msgcode, $addinfo) {

        $stringman = get_string_manager();

        if ($addinfo !== null and $stringman->string_exists('validationmsg_'.$msgcode.'_info', 'core_plugin')) {
            return get_string('validationmsg_'.$msgcode.'_info', 'core_plugin', $addinfo);
        }

        return '';
    }

    /**
     * Return the information provided by the the plugin's version.php
     *
     * If version.php was not found in the plugin, null is returned. Otherwise
     * the array is returned. It may be empty if no information was parsed
     * (which should not happen).
     *
     * @return null|array
     */
    public function get_versionphp_info() {
        return $this->versionphp;
    }

    /**
     * Returns the name of the English language file without the .php extension
     *
     * This can be used as a suggestion for fixing the plugin root directory in the
     * ZIP file during the upload. If no file was found, or multiple PHP files are
     * located in lang/en/ folder, then null is returned.
     *
     * @return null|string
     */
    public function get_language_file_name() {
        return $this->langfilename;
    }

    /**
     * Returns the rootdir of the extracted package (after eventual renaming)
     *
     * @return string|null
     */
    public function get_rootdir() {
        return $this->rootdir;
    }

    // End of external API.

    /**
     * No public constructor, use {@link self::instance()} instead.
     *
     * @param string $zipcontentpath full path to the extracted ZIP contents
     * @param array $zipcontentfiles (string)filerelpath => (bool|string)true or error
     */
    protected function __construct($zipcontentpath, array $zipcontentfiles) {
        $this->extractdir = $zipcontentpath;
        $this->extractfiles = $zipcontentfiles;
    }

    // Validation methods.

    /**
     * Returns false if files in the ZIP do not have required layout.
     *
     * @return bool
     */
    protected function validate_files_layout() {

        if (!is_array($this->extractfiles) or count($this->extractfiles) < 4) {
            // We need the English language pack with the name of the plugin at least.
            $this->add_message(self::ERROR, 'filesnumber');
            return false;
        }

        foreach ($this->extractfiles as $filerelname => $filestatus) {
            if ($filestatus !== true) {
                $this->add_message(self::ERROR, 'filestatus', array('file' => $filerelname, 'status' => $filestatus));
                return false;
            }
        }

        foreach (array_keys($this->extractfiles) as $filerelname) {
            if (!file_exists($this->extractdir.'/'.$filerelname)) {
                $this->add_message(self::ERROR, 'filenotexists', array('file' => $filerelname));
                return false;
            }
        }

        foreach (array_keys($this->extractfiles) as $filerelname) {
            $matches = array();
            if (!preg_match("#^([^/]+)/#", $filerelname, $matches)
                    or (!is_null($this->rootdir) and $this->rootdir !== $matches[1])) {
                $this->add_message(self::ERROR, 'onedir');
                return false;
            }
            $this->rootdir = $matches[1];
        }

        if ($this->rootdir !== clean_param($this->rootdir, PARAM_PLUGIN)) {
            $this->add_message(self::ERROR, 'rootdirinvalid', $this->rootdir);
            return false;
        } else {
            $this->add_message(self::INFO, 'rootdir', $this->rootdir);
        }

        return is_dir($this->extractdir.'/'.$this->rootdir);
    }

    /**
     * Returns false if the version.php file does not declare required information.
     *
     * @return bool
     */
    protected function validate_version_php() {

        if (!isset($this->assertions['plugintype'])) {
            throw new coding_exception('Required plugin type must be set before calling this');
        }

        if (!isset($this->assertions['moodleversion'])) {
            throw new coding_exception('Required Moodle version must be set before calling this');
        }

        $fullpath = $this->extractdir.'/'.$this->rootdir.'/version.php';

        if (!file_exists($fullpath)) {
            // This is tolerated for themes only.
            if ($this->assertions['plugintype'] === 'theme') {
                $this->add_message(self::DEBUG, 'missingversionphp');
                return true;
            } else {
                $this->add_message(self::ERROR, 'missingversionphp');
                return false;
            }
        }

        $this->versionphp = array();
        $info = $this->parse_version_php($fullpath);

        if (isset($info['module->version'])) {
            $this->add_message(self::ERROR, 'versionphpsyntax', '$module');
            return false;
        }

        if (isset($info['plugin->version'])) {
            $this->versionphp['version'] = $info['plugin->version'];
            $this->add_message(self::INFO, 'pluginversion', $this->versionphp['version']);
        } else {
            $this->add_message(self::ERROR, 'missingversion');
            return false;
        }

        if (isset($info['plugin->requires'])) {
            $this->versionphp['requires'] = $info['plugin->requires'];
            if ($this->versionphp['requires'] > $this->assertions['moodleversion']) {
                $this->add_message(self::ERROR, 'requiresmoodle', $this->versionphp['requires']);
                return false;
            }
            $this->add_message(self::INFO, 'requiresmoodle', $this->versionphp['requires']);
        }

        if (!isset($info['plugin->component'])) {
            $this->add_message(self::ERROR, 'missingcomponent');
            return false;
        }

        $this->versionphp['component'] = $info['plugin->component'];
        list($reqtype, $reqname) = core_component::normalize_component($this->versionphp['component']);
        if ($reqtype !== $this->assertions['plugintype']) {
            $this->add_message(self::ERROR, 'componentmismatchtype', array(
                'expected' => $this->assertions['plugintype'],
                'found' => $reqtype));
            return false;
        }
        if ($reqname !== $this->rootdir) {
            $this->add_message(self::ERROR, 'componentmismatchname', $reqname);
            return false;
        }
        $this->add_message(self::INFO, 'componentmatch', $this->versionphp['component']);

        if (isset($info['plugin->maturity'])) {
            $this->versionphp['maturity'] = $info['plugin->maturity'];
            if ($this->versionphp['maturity'] === 'MATURITY_STABLE') {
                $this->add_message(self::INFO, 'maturity', $this->versionphp['maturity']);
            } else {
                $this->add_message(self::WARNING, 'maturity', $this->versionphp['maturity']);
            }
        }

        if (isset($info['plugin->release'])) {
            $this->versionphp['release'] = $info['plugin->release'];
            $this->add_message(self::INFO, 'release', $this->versionphp['release']);
        }

        return true;
    }

    /**
     * Returns false if the English language pack is not provided correctly.
     *
     * @return bool
     */
    protected function validate_language_pack() {

        if (!isset($this->assertions['plugintype'])) {
            throw new coding_exception('Required plugin type must be set before calling this');
        }

        if (!isset($this->extractfiles[$this->rootdir.'/lang/en/'])
                or $this->extractfiles[$this->rootdir.'/lang/en/'] !== true
                or !is_dir($this->extractdir.'/'.$this->rootdir.'/lang/en')) {
            $this->add_message(self::ERROR, 'missinglangenfolder');
            return false;
        }

        $langfiles = array();
        foreach (array_keys($this->extractfiles) as $extractfile) {
            $matches = array();
            if (preg_match('#^'.preg_quote($this->rootdir).'/lang/en/([^/]+).php?$#i', $extractfile, $matches)) {
                $langfiles[] = $matches[1];
            }
        }

        if (empty($langfiles)) {
            $this->add_message(self::ERROR, 'missinglangenfile');
            return false;
        } else if (count($langfiles) > 1) {
            $this->add_message(self::WARNING, 'multiplelangenfiles');
        } else {
            $this->langfilename = $langfiles[0];
            $this->add_message(self::DEBUG, 'foundlangfile', $this->langfilename);
        }

        if ($this->assertions['plugintype'] === 'mod') {
            $expected = $this->rootdir.'.php';
        } else {
            $expected = $this->assertions['plugintype'].'_'.$this->rootdir.'.php';
        }

        if (!isset($this->extractfiles[$this->rootdir.'/lang/en/'.$expected])
                or $this->extractfiles[$this->rootdir.'/lang/en/'.$expected] !== true
                or !is_file($this->extractdir.'/'.$this->rootdir.'/lang/en/'.$expected)) {
            $this->add_message(self::ERROR, 'missingexpectedlangenfile', $expected);
            return false;
        }

        return true;
    }

    /**
     * Returns false of the given add-on can't be installed into its location.
     *
     * @return bool
     */
    public function validate_target_location() {

        if (!isset($this->assertions['plugintype'])) {
            throw new coding_exception('Required plugin type must be set before calling this');
        }

        $plugintypepath = $this->get_plugintype_location($this->assertions['plugintype']);

        if (is_null($plugintypepath)) {
            $this->add_message(self::ERROR, 'unknowntype', $this->assertions['plugintype']);
            return false;
        }

        if (!is_dir($plugintypepath)) {
            throw new coding_exception('Plugin type location does not exist!');
        }

        // Always check that the plugintype root is writable.
        if (!is_writable($plugintypepath)) {
            $this->add_message(self::ERROR, 'pathwritable', $plugintypepath);
            return false;
        } else {
            $this->add_message(self::INFO, 'pathwritable', $plugintypepath);
        }

        // The target location itself may or may not exist. Even if installing an
        // available update, the code could have been removed by accident (and
        // be reported as missing) etc. So we just make sure that the code
        // can be replaced if it already exists.
        $target = $plugintypepath.'/'.$this->rootdir;
        if (file_exists($target)) {
            if (!is_dir($target)) {
                $this->add_message(self::ERROR, 'targetnotdir', $target);
                return false;
            }
            $this->add_message(self::WARNING, 'targetexists', $target);
            if ($this->get_plugin_manager()->is_directory_removable($target)) {
                $this->add_message(self::INFO, 'pathwritable', $target);
            } else {
                $this->add_message(self::ERROR, 'pathwritable', $target);
                return false;
            }
        }

        return true;
    }

    // Helper methods.

    /**
     * Get as much information from existing version.php as possible
     *
     * @param string $fullpath full path to the version.php file
     * @return array of found meta-info declarations
     */
    protected function parse_version_php($fullpath) {

        $content = $this->get_stripped_file_contents($fullpath);

        preg_match_all('#\$((plugin|module)\->(version|maturity|release|requires))=()(\d+(\.\d+)?);#m', $content, $matches1);
        preg_match_all('#\$((plugin|module)\->(maturity))=()(MATURITY_\w+);#m', $content, $matches2);
        preg_match_all('#\$((plugin|module)\->(release))=([\'"])(.*?)\4;#m', $content, $matches3);
        preg_match_all('#\$((plugin|module)\->(component))=([\'"])(.+?_.+?)\4;#m', $content, $matches4);

        if (count($matches1[1]) + count($matches2[1]) + count($matches3[1]) + count($matches4[1])) {
            $info = array_combine(
                array_merge($matches1[1], $matches2[1], $matches3[1], $matches4[1]),
                array_merge($matches1[5], $matches2[5], $matches3[5], $matches4[5])
            );

        } else {
            $info = array();
        }

        return $info;
    }

    /**
     * Append the given message to the messages log
     *
     * @param string $level e.g. self::ERROR
     * @param string $msgcode may form a string
     * @param string|array|object $a optional additional info suitable for {@link get_string()}
     */
    protected function add_message($level, $msgcode, $a = null) {
        $msg = (object)array(
            'level'     => $level,
            'msgcode'   => $msgcode,
            'addinfo'   => $a,
        );
        $this->messages[] = $msg;
    }

    /**
     * Returns bare PHP code from the given file
     *
     * Returns contents without PHP opening and closing tags, text outside php code,
     * comments and extra whitespaces.
     *
     * @param string $fullpath full path to the file
     * @return string
     */
    protected function get_stripped_file_contents($fullpath) {

        $source = file_get_contents($fullpath);
        $tokens = token_get_all($source);
        $output = '';
        $doprocess = false;
        foreach ($tokens as $token) {
            if (is_string($token)) {
                // Simple one character token.
                $id = -1;
                $text = $token;
            } else {
                // Token array.
                list($id, $text) = $token;
            }
            switch ($id) {
                case T_WHITESPACE:
                case T_COMMENT:
                case T_ML_COMMENT:
                case T_DOC_COMMENT:
                    // Ignore whitespaces, inline comments, multiline comments and docblocks.
                    break;
                case T_OPEN_TAG:
                    // Start processing.
                    $doprocess = true;
                    break;
                case T_CLOSE_TAG:
                    // Stop processing.
                    $doprocess = false;
                    break;
                default:
                    // Anything else is within PHP tags, return it as is.
                    if ($doprocess) {
                        $output .= $text;
                        if ($text === 'function') {
                            // Explicitly keep the whitespace that would be ignored.
                            $output .= ' ';
                        }
                    }
                    break;
            }
        }

        return $output;
    }

    /**
     * Returns the full path to the root directory of the given plugin type.
     *
     * @param string $plugintype
     * @return string|null
     */
    public function get_plugintype_location($plugintype) {
        return $this->get_plugin_manager()->get_plugintype_root($plugintype);
    }

    /**
     * Returns plugin manager to use.
     *
     * @return core_plugin_manager
     */
    protected function get_plugin_manager() {
        return core_plugin_manager::instance();
    }
}
