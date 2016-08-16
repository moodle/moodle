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
 * Base class for antivirus integration.
 *
 * @package    core_antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\antivirus;

defined('MOODLE_INTERNAL') || die();

/**
 * Base abstract antivirus scanner class.
 *
 * @package    core
 * @subpackage antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class scanner {
    /** @var stdClass the config for antivirus */
    protected $config;

    /**
     * Class constructor.
     *
     * @return void.
     */
    public function __construct() {
        // Populate config variable, inheriting class namespace is matching
        // full plugin name, so we can use it directly to retrieve plugin
        // configuration.
        $ref = new \ReflectionClass(get_class($this));
        $this->config = get_config($ref->getNamespaceName());
    }

    /**
     * Config get method.
     *
     * @param string $property config property to get.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function get_config($property) {
        if (property_exists($this->config, $property)) {
            return $this->config->$property;
        }
        throw new \coding_exception('Config property "' . $property . '" doesn\'t exist');
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
     * @throws \core\antivirus\scanner_exception If file is infected.
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
            $eventdata = new \stdClass();
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