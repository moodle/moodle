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
 * Manager class for antivirus integration.
 *
 * @package    core_antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\antivirus;

defined('MOODLE_INTERNAL') || die();

/**
 * Class used for various antivirus related stuff.
 *
 * @package    core_antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /**
     * Returns list of enabled antiviruses.
     *
     * @return array Array ('antivirusname'=>stdClass antivirus object).
     */
    private static function get_enabled() {
        global $CFG;

        $active = array();
        if (empty($CFG->antiviruses)) {
            return $active;
        }

        foreach (explode(',', $CFG->antiviruses) as $e) {
            if ($antivirus = self::get_antivirus($e)) {
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
     * @throws \core\antivirus\scanner_exception If file is infected.
     * @return void
     */
    public static function scan_file($file, $filename, $deleteinfected) {
        global $USER;
        $antiviruses = self::get_enabled();
        foreach ($antiviruses as $antivirus) {
            // Attempt to scan, catching internal exceptions.
            try {
                $result = $antivirus->scan_file($file, $filename);
            } catch (\core\antivirus\scanner_exception $e) {
                // If there was a scanner exception (such as ClamAV denying upload), send messages and rethrow.
                $notice = $antivirus->get_scanning_notice();
                $incidentdetails = $antivirus->get_incident_details($file, $filename, $notice, false);
                self::send_antivirus_messages($antivirus, $incidentdetails);
                throw $e;
            }

            $notice = $antivirus->get_scanning_notice();
            if ($result === $antivirus::SCAN_RESULT_FOUND) {
                // Infection found, send notification.
                $incidentdetails = $antivirus->get_incident_details($file, $filename, $notice);
                self::send_antivirus_messages($antivirus, $incidentdetails);

                // Move to quarantine folder.
                $zipfile = \core\antivirus\quarantine::quarantine_file($file, $filename, $incidentdetails, $notice);
                // If file not stored due to disabled quarantine, store a message.
                if (empty($zipfile)) {
                    $zipfile = get_string('quarantinedisabled', 'antivirus');
                }

                // Log file infected event.
                $params = [
                    'context' => \context_system::instance(),
                    'relateduserid' => $USER->id,
                    'other' => ['filename' => $filename, 'zipfile' => $zipfile, 'incidentdetails' => $incidentdetails],
                ];
                $event = \core\event\virus_infected_file_detected::create($params);
                $event->trigger();

                if ($deleteinfected) {
                    unlink($file);
                }

                // Get custom message to display to user from antivirus engine.
                $displaymessage = $antivirus->get_virus_found_message();
                $placeholders = array_merge(['item' => $filename], $displaymessage['placeholders']);

                throw new \core\antivirus\scanner_exception(
                    $displaymessage['string'],
                    '',
                    $placeholders,
                    null,
                    $displaymessage['component']
                );
            } else if ($result === $antivirus::SCAN_RESULT_ERROR) {
                // Here we need to generate a different incident based on an error.
                $incidentdetails = $antivirus->get_incident_details($file, $filename, $notice, false);
                self::send_antivirus_messages($antivirus, $incidentdetails);
            }
        }
    }

    /**
     * Scan data steam using all enabled antiviruses, throws exception in case of infected data.
     *
     * @param string $data The variable containing the data to scan.
     * @throws \core\antivirus\scanner_exception If data is infected.
     * @return void
     */
    public static function scan_data($data) {
        global $USER;
        $antiviruses = self::get_enabled();
        foreach ($antiviruses as $antivirus) {
            // Attempt to scan, catching internal exceptions.
            try {
                $result = $antivirus->scan_data($data);
            } catch (\core\antivirus\scanner_exception $e) {
                // If there was a scanner exception (such as ClamAV denying upload), send messages and rethrow.
                $notice = $antivirus->get_scanning_notice();
                $filename = get_string('datastream', 'antivirus');
                $incidentdetails = $antivirus->get_incident_details('', $filename, $notice, false);
                self::send_antivirus_messages($antivirus, $incidentdetails);

                throw $e;
            }

            $filename = get_string('datastream', 'antivirus');
            $notice = $antivirus->get_scanning_notice();

            if ($result === $antivirus::SCAN_RESULT_FOUND) {
                // Infection found, send notification.
                $incidentdetails = $antivirus->get_incident_details('', $filename, $notice);
                self::send_antivirus_messages($antivirus, $incidentdetails);

                // Copy data to quarantine folder.
                $zipfile = \core\antivirus\quarantine::quarantine_data($data, $filename, $incidentdetails, $notice);
                // If file not stored due to disabled quarantine, store a message.
                if (empty($zipfile)) {
                    $zipfile = get_string('quarantinedisabled', 'antivirus');
                }

                // Log file infected event.
                $params = [
                    'context' => \context_system::instance(),
                    'relateduserid' => $USER->id,
                    'other' => ['filename' => $filename, 'zipfile' => $zipfile, 'incidentdetails' => $incidentdetails],
                ];
                $event = \core\event\virus_infected_data_detected::create($params);
                $event->trigger();

                // Get custom message to display to user from antivirus engine.
                $displaymessage = $antivirus->get_virus_found_message();
                $placeholders = array_merge(['item' => get_string('datastream', 'antivirus')], $displaymessage['placeholders']);

                throw new \core\antivirus\scanner_exception(
                    $displaymessage['string'],
                    '',
                    $placeholders,
                    null,
                    $displaymessage['component']
                );
            } else if ($result === $antivirus::SCAN_RESULT_ERROR) {
                // Here we need to generate a different incident based on an error.
                $incidentdetails = $antivirus->get_incident_details('', $filename, $notice, false);
                self::send_antivirus_messages($antivirus, $incidentdetails);
            }
        }
    }

    /**
     * Returns instance of antivirus.
     *
     * @param string $antivirusname name of antivirus.
     * @return object|bool antivirus instance or false if does not exist.
     */
    public static function get_antivirus($antivirusname) {
        global $CFG;

        $classname = '\\antivirus_' . $antivirusname . '\\scanner';
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
    public static function get_available() {
        $antiviruses = array();
        foreach (\core_component::get_plugin_list('antivirus') as $antivirusname => $dir) {
            $antiviruses[$antivirusname] = get_string('pluginname', 'antivirus_'.$antivirusname);
        }
        return $antiviruses;
    }

    /**
     * This function puts all relevant information into the messages required, and sends them.
     *
     * @param \core\antivirus\scanner $antivirus the scanner engine.
     * @param string $incidentdetails details of the incident.
     * @return void
     */
    public static function send_antivirus_messages(\core\antivirus\scanner $antivirus, string $incidentdetails) {
        $messages = $antivirus->get_messages();

        // If there is no messages, and a virus is found, we should generate one, then send it.
        if (empty($messages)) {
            $antivirus->message_admins($antivirus->get_scanning_notice(), FORMAT_MOODLE, 'infected');
            $messages = $antivirus->get_messages();
        }

        foreach ($messages as $message) {

            // Check if the information is already in the current scanning notice.
            if (!empty($antivirus->get_scanning_notice()) &&
                strpos($antivirus->get_scanning_notice(), $message->fullmessage) === false) {
                // This is some extra information. We should append this to the end of the incident details.
                $incidentdetails .= \html_writer::tag('pre', $message->fullmessage);
            }

            // Now update the message to the detailed version, and format.
            $message->name = 'infected';
            $message->fullmessagehtml = $incidentdetails;
            $message->fullmessageformat = FORMAT_MOODLE;
            $message->fullmessage = format_text_email($incidentdetails, $message->fullmessageformat);

            // Now we must check if message is going to a real account.
            // It may be an email that needs to be sent to non-user address.
            if ($message->userto->id === -1) {
                // If this doesnt exist, send a regular email.
                email_to_user(
                    $message->userto,
                    get_admin(),
                    $message->subject,
                    $message->fullmessage,
                    $message->fullmessagehtml
                );
            } else {
                // And now we can send.
                message_send($message);
            }
        }
    }
}
