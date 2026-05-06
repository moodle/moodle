<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Checkbox for enabling the mobile web service.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enablemobileservice extends \core_admin\setting\setting\configcheckbox {
    /** @var bool True means that the capability 'webservice/rest:use' is set for authenticated user role */
    private $restuse;

    /**
     * Return true if Authenticated user role has the capability 'webservice/rest:use', otherwise false.
     *
     * @return boolean
     */
    private function is_protocol_cap_allowed() {
        global $DB, $CFG;

        // If the $this->restuse variable is not set, it needs to be set.
        if (empty($this->restuse) && $this->restuse !== false) {
            $params = [];
            $params['permission'] = CAP_ALLOW;
            $params['roleid'] = $CFG->defaultuserroleid;
            $params['capability'] = 'webservice/rest:use';
            $this->restuse = $DB->record_exists('role_capabilities', $params);
        }

        return $this->restuse;
    }

    /**
     * Set the 'webservice/rest:use' to the Authenticated user role (allow or not)
     * @param bool $status true to allow, false to not set
     */
    private function set_protocol_cap($status) {
        global $CFG;
        if ($status && !$this->is_protocol_cap_allowed()) {
            // Need to allow the cap.
            $permission = CAP_ALLOW;
            $assign = true;
        } else if (!$status && $this->is_protocol_cap_allowed()) {
            // Need to disallow the cap.
            $permission = CAP_INHERIT;
            $assign = true;
        }
        if (!empty($assign)) {
            $systemcontext = \context_system::instance();
            assign_capability('webservice/rest:use', $permission, $CFG->defaultuserroleid, $systemcontext->id, true);
        }
    }

    /**
     * Builds XHTML to display the control.
     * The main purpose of this overloading is to display a warning when https
     * is not supported by the server
     * @param string $data Unused
     * @param string $query
     * @return string XHTML
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;
        $html = parent::output_html($data, $query);

        if ((string)$data === $this->yes) {
            $notifications = \tool_mobile\api::get_potential_config_issues(); // Safe to call, plugin available if we reach here.
            foreach ($notifications as $notification) {
                $message = get_string($notification[0], $notification[1]);
                $html .= $OUTPUT->notification($message, \core\output\notification::NOTIFY_WARNING);
            }
        }

        return $html;
    }

    /**
     * Retrieves the current setting using the objects name
     *
     * @return string
     */
    public function get_setting() {
        global $CFG;

        // First check if is not set.
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return null;
        }

        // For install cli script, $CFG->defaultuserroleid is not set so return 0.
        // Or if web services aren't enabled this can't be,.
        if (empty($CFG->defaultuserroleid) || empty($CFG->enablewebservices)) {
            return 0;
        }

        require_once($CFG->dirroot . '/webservice/lib.php');
        $webservicemanager = new \webservice();
        $mobileservice = $webservicemanager->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
        if ($mobileservice->enabled && $this->is_protocol_cap_allowed()) {
            return $result;
        } else {
            return 0;
        }
    }

    /**
     * Save the selected setting
     *
     * @param string $data The selected site
     * @return string empty string or error message
     */
    public function write_setting($data) {
        global $DB, $CFG;

        // For install cli script, $CFG->defaultuserroleid is not set so do nothing.
        if (empty($CFG->defaultuserroleid)) {
            return '';
        }

        $servicename = MOODLE_OFFICIAL_MOBILE_SERVICE;

        require_once($CFG->dirroot . '/webservice/lib.php');
        $webservicemanager = new \webservice();

        $updateprotocol = false;
        if ((string)$data === $this->yes) {
             // Code run when enable mobile web service
             // Enable web service systeme if necessary.
             set_config('enablewebservices', true);

             // Enable mobile service.
             $mobileservice = $webservicemanager->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
             $mobileservice->enabled = 1;
             $webservicemanager->update_external_service($mobileservice);

             // Enable REST server.
             $activeprotocols = empty($CFG->webserviceprotocols) ? [] : \explode(',', $CFG->webserviceprotocols);

            if (!in_array('rest', $activeprotocols)) {
                $activeprotocols[] = 'rest';
                $updateprotocol = true;
            }

            if ($updateprotocol) {
                set_config('webserviceprotocols', implode(',', $activeprotocols));
            }

             // Allow rest:use capability for authenticated user.
             $this->set_protocol_cap(true);
        } else {
            // Disable the mobile service.
            $mobileservice = $webservicemanager->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
            $mobileservice->enabled = 0;
            $webservicemanager->update_external_service($mobileservice);
        }

        return (parent::write_setting($data));
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(enablemobileservice::class, \admin_setting_enablemobileservice::class);
