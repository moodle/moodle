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
    /** Scanning result indicating no virus found. */
    const SCAN_RESULT_OK = 0;
    /** Scanning result indicating that virus is found. */
    const SCAN_RESULT_FOUND = 1;
    /** Scanning result indicating the error. */
    const SCAN_RESULT_ERROR = 2;

    /** @var stdClass the config for antivirus */
    protected $config;
    /** @var string scanning notice */
    protected $scanningnotice = '';

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
     * Get scanning notice.
     *
     * @return string
     */
    public function get_scanning_notice() {
        return $this->scanningnotice;
    }

    /**
     * Set scanning notice.
     *
     * @param string $notice notice to set.
     * @return void
     */
    protected function set_scanning_notice($notice) {
        $this->scanningnotice = $notice;
    }

    /**
     * Are the antivirus settings configured?
     *
     * @return bool True if plugin has been configured.
     */
    public abstract function is_configured();

    /**
     * Scan file.
     *
     * @param string $file Full path to the file.
     * @param string $filename Name of the file (could be different from physical file if temp file is used).
     * @return int Scanning result constants.
     */
    public abstract function scan_file($file, $filename);

    /**
     * Scan data.
     *
     * By default it saves data variable content to file and then scans it using
     * scan_file method, however if antivirus plugin permits scanning data directly,
     * the method can be overridden.
     *
     * @param string $data The variable containing the data to scan.
     * @return int Scanning result constants.
     */
    public function scan_data($data) {
        // Prepare temp file.
        $tempdir = make_request_directory();
        $tempfile = $tempdir . DIRECTORY_SEPARATOR . rand();
        file_put_contents($tempfile, $data);

        // Perform a virus scan now.
        return $this->scan_file($tempfile, get_string('datastream', 'antivirus'));
    }

    /**
     * Email admins about antivirus scan outcomes.
     *
     * @param string $notice The body of the email to be sent.
     * @param string $format The body format.
     * @param string $eventname event name
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function message_admins($notice, $format = FORMAT_PLAIN, $eventname = 'errors') {
        $noticehtml = $format !== FORMAT_PLAIN ? format_text($notice, $format) : '';
        $site = get_site();

        $subject = get_string('emailsubject', 'antivirus', format_string($site->fullname));
        $notifyemail = get_config('antivirus', 'notifyemail');
        if (!empty($notifyemail)) {
            $user = new \stdClass();
            $user->id = -1;
            $user->email = $notifyemail;
            email_to_user($user, get_admin(), $subject, $noticehtml);
            return;
        }

        $admins = get_admins();
        foreach ($admins as $admin) {
            $eventdata = new \core\message\message();
            $eventdata->courseid          = SITEID;
            $eventdata->component         = 'moodle';
            $eventdata->name              = $eventname;
            $eventdata->userfrom          = get_admin();
            $eventdata->userto            = $admin;
            $eventdata->subject           = $subject;
            $eventdata->fullmessage       = $notice;
            $eventdata->fullmessageformat = $format;
            $eventdata->fullmessagehtml   = $noticehtml;
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }
    }

    /**
     * Return incidence details
     *
     * @param string $file full path to the file
     * @param string $filename original name of the file
     * @param string $notice notice from antivirus
     * @return string the incidence details
     * @throws \coding_exception
     */
    public function get_incidence_details($file = '', $filename = '', $notice = '') {
        global $USER;
        if (empty($notice)) {
            $notice = $this->get_scanning_notice();
        }
        $content = new \stdClass();
        $unknown = get_string('unknown', 'antivirus');;
        $content->filename = !empty($filename) ? $filename : $unknown;
        if (!empty($file)) {
            $content->filesize = filesize($file);
            $content->contenthash = \file_storage::hash_from_string(file_get_contents($file));
            $content->contenttype = mime_content_type($file);
        } else {
            $content->filesize = $unknown;
            $content->contenthash = $unknown;
            $content->contenttype = $unknown;
        }

        $content->author = \core_user::is_real_user($USER->id) ? fullname($USER) . " ($USER->username)" : $unknown;
        $content->ipaddress = getremoteaddr();
        $content->date = userdate(time(), get_string('strftimedatetimeshort'));
        $content->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $unknown;
        $content->notice = $notice;
        $report = new \moodle_url('/report/infectedfiles/index.php');
        $content->report = $report->out();
        return get_string('incidencedetails', 'antivirus', $content);
    }
}
