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
 * @package     tool_installaddon
 * @subpackage  classes
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!defined('T_ML_COMMENT')) {
   define('T_ML_COMMENT', T_COMMENT);
} else {
   define('T_DOC_COMMENT', T_ML_COMMENT);
}

/**
 * Validates the contents of extracted plugin ZIP file
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_validator {

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

    /** @var moodle_url|null URL to continue with the installation of validated add-on */
    protected $continueurl = null;

    /**
     * Factory method returning instance of the validator
     *
     * @param string $zipcontentpath full path to the extracted ZIP contents
     * @param array $zipcontentfiles (string)filerelpath => (bool|string)true or error
     * @return tool_installaddon_validator
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
     * Return the information provided by the the plugin's version.php
     *
     * If version.php was not found in the plugin (which is tolerated for
     * themes only at the moment), null is returned. Otherwise the array
     * is returned. It may be empty if no information was parsed (which
     * should not happen).
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

    /**
     * Sets the URL to continue to after successful validation
     *
     * @param moodle_url $url
     */
    public function set_continue_url(moodle_url $url) {
        $this->continueurl = $url;
    }

    /**
     * Get the URL to continue to after successful validation
     *
     * Null is returned if the URL has not been explicitly set by the caller.
     *
     * @return moodle_url|null
     */
    public function get_continue_url() {
        return $this->continueurl;
    }

    // End of external API /////////////////////////////////////////////////////

    /**
     * @param string $zipcontentpath full path to the extracted ZIP contents
     * @param array $zipcontentfiles (string)filerelpath => (bool|string)true or error
     */
    protected function __construct($zipcontentpath, array $zipcontentfiles) {
        $this->extractdir = $zipcontentpath;
        $this->extractfiles = $zipcontentfiles;
    }

    // Validation methods //////////////////////////////////////////////////////

    /**
     * @return bool false if files in the ZIP do not have required layout
     */
    protected function validate_files_layout() {

        if (!is_array($this->extractfiles) or count($this->extractfiles) < 4) {
            // We need the English language pack with the name of the plugin at least
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
            if (!preg_match("#^([^/]+)/#", $filerelname, $matches) or (!is_null($this->rootdir) and $this->rootdir !== $matches[1])) {
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
     * @return bool false if the version.php file does not declare required information
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

        if ($this->assertions['plugintype'] === 'mod') {
            $type = 'module';
        } else {
            $type = 'plugin';
        }

        if (!isset($info[$type.'->version'])) {
            if ($type === 'module' and isset($info['plugin->version'])) {
                // Expect the activity module using $plugin in version.php instead of $module.
                $type = 'plugin';
                $this->versionphp['version'] = $info[$type.'->version'];
                $this->add_message(self::INFO, 'pluginversion', $this->versionphp['version']);
            } else {
                $this->add_message(self::ERROR, 'missingversion');
                return false;
            }
        } else {
            $this->versionphp['version'] = $info[$type.'->version'];
            $this->add_message(self::INFO, 'pluginversion', $this->versionphp['version']);
        }

        if (isset($info[$type.'->requires'])) {
            $this->versionphp['requires'] = $info[$type.'->requires'];
            if ($this->versionphp['requires'] > $this->assertions['moodleversion']) {
                $this->add_message(self::ERROR, 'requiresmoodle', $this->versionphp['requires']);
                return false;
            }
            $this->add_message(self::INFO, 'requiresmoodle', $this->versionphp['requires']);
        }

        if (isset($info[$type.'->component'])) {
            $this->versionphp['component'] = $info[$type.'->component'];
            list($reqtype, $reqname) = normalize_component($this->versionphp['component']);
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
        }

        if (isset($info[$type.'->maturity'])) {
            $this->versionphp['maturity'] = $info[$type.'->maturity'];
            if ($this->versionphp['maturity'] === 'MATURITY_STABLE') {
                $this->add_message(self::INFO, 'maturity', $this->versionphp['maturity']);
            } else {
                $this->add_message(self::WARNING, 'maturity', $this->versionphp['maturity']);
            }
        }

        if (isset($info[$type.'->release'])) {
            $this->versionphp['release'] = $info[$type.'->release'];
            $this->add_message(self::INFO, 'release', $this->versionphp['release']);
        }

        return true;
    }

    /**
     * @return bool false if the English language pack is not provided correctly
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
     * @return bool false of the given add-on can't be installed into its location
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

        $target = $plugintypepath.'/'.$this->rootdir;

        if (file_exists($target)) {
            $this->add_message(self::ERROR, 'targetexists', $target);
            return false;
        }

        if (is_writable($plugintypepath)) {
            $this->add_message(self::INFO, 'pathwritable', $plugintypepath);
        } else {
            $this->add_message(self::ERROR, 'pathwritable', $plugintypepath);
            return false;
        }

        return true;
    }

    // Helper methods //////////////////////////////////////////////////////////

    /**
     * Get as much information from existing version.php as possible
     *
     * @param string full path to the version.php file
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
     * Returns the full path to the root directory of the given plugin type
     *
     * @param string $plugintype
     * @return string|null
     */
    public function get_plugintype_location($plugintype) {

        $plugintypepath = null;

        foreach (get_plugin_types() as $type => $fullpath) {
            if ($type === $plugintype) {
                $plugintypepath = $fullpath;
                break;
            }
        }

        return $plugintypepath;
    }
}
