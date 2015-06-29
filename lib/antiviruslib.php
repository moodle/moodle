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
 * Utility classes and functions for antivirus integration.
 *
 * @package    core_antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns list of enabled antiviruses.
 *
 * @return array Array ('antivirusname'=>stdClass antivirus object).
 */
function antiviruses_get_enabled() {
    global $CFG;

    $active = array();
    if (empty($CFG->antiviruses)) {
        return $active;
    }

    foreach(explode(',', $CFG->antiviruses) as $e) {
        if ($antivirus = antiviruses_get_antivirus($e)) {
            if ($antivirus->is_configured()) {
                $active[$e] = $antivirus;
            }
        }
    }
    return $active;
}

/**
 * Scan file using all enabled antiviruses, throws exception in case of infected file.
 *
 * @param string $file Full path to the file.
 * @param string $filename Name of the file (could be different from physical file if temp file is used).
 * @param bool $deleteinfected whether infected file needs to be deleted.
 * @throws antivirus_exception If file is infected.
 * @return void
 */
function antiviruses_scan_file($file, $filename, $deleteinfected) {
    $antiviruses = antiviruses_get_enabled();
    foreach ($antiviruses as $antivirus) {
        $antivirus->scan_file($file, $filename, $deleteinfected);
    }
}

/**
 * Returns instance of antivirus.
 *
 * @param string $antivirusname name of antivirus.
 * @return object|bool antivirus instance or false if does not exist.
 */
function antiviruses_get_antivirus($antivirusname) {
    global $CFG;

    $libfile = "$CFG->libdir/antivirus/$antivirusname/lib.php";
    if (!file_exists($libfile)) {
        return false;
    }
    require_once($libfile);
    $classname = 'antivirus_' . $antivirusname;
    if (!class_exists($classname)) {
        return false;
    }
    return new $classname();
}

/**
 * Get the list of available antiviruses.
 *
 * @return array Array ('antivirusname'=>'localised antivirus name').
 */
function antiviruses_get_available() {
    $antiviruses = array();
    foreach (core_component::get_plugin_list('antivirus') as $antivirusname => $dir) {
        $antiviruses[$antivirusname] = get_string('pluginname', 'antivirus_'.$antivirusname);
    }
    return $antiviruses;
}

/**
 * Base abstract antivirus class.
 *
 * @package    core
 * @subpackage antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class antivirus {
    /** @var stdClass the config for antivirus */
    protected $config;

    /**
     * Class constructor.
     *
     * @return void.
     */
    public function __construct() {
        // Populate config variable, child class name is matching full plugin name,
        // so we can use it directly to retrieve plugin configuration.
        $this->config = get_config(get_class($this));
    }

    /**
     * Are the antivirus settings configured?
     *
     * @return bool True if plugin has been configured.
     */
    public abstract function is_configured();

    /**
     * Scan file, throws exception in case of infected file.
     *
     * @param string $file Full path to the file.
     * @param string $filename Name of the file (could be different from physical file if temp file is used).
     * @param bool $deleteinfected whether infected file needs to be deleted.
     * @throws antivirus_exception If file is infected.
     * @return void
     */
    public abstract function scan_file($file, $filename, $deleteinfected);

    /**
     * Email admins about antivirus scan outcomes.
     *
     * @param string $notice The body of the email to be sent.
     * @return void
     */
    public function message_admins($notice) {

        $site = get_site();

        $subject = get_string('emailsubject', 'antivirus', format_string($site->fullname));
        $admins = get_admins();
        foreach ($admins as $admin) {
            $eventdata = new stdClass();
            $eventdata->component         = 'moodle';
            $eventdata->name              = 'errors';
            $eventdata->userfrom          = get_admin();
            $eventdata->userto            = $admin;
            $eventdata->subject           = $subject;
            $eventdata->fullmessage       = $notice;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }
    }
}

/**
 * An antivirus exception class.
 *
 * @package    core
 * @subpackage antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class antivirus_exception extends moodle_exception {
    /**
     * Constructs a new exception
     *
     * @param string $errorcode
     * @param string $link
     * @param mixed $a
     * @param mixed $debuginfo
     */
    public function __construct($errorcode, $link = '', $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, 'antivirus', $link, $a, $debuginfo);
    }
}
