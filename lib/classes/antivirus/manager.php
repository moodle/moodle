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
            $result = $antivirus->scan_file($file, $filename);
            if ($result === $antivirus::SCAN_RESULT_FOUND) {
                // Infection found, send notification.
                $notice = $antivirus->get_scanning_notice();
                $incidencedetails = $antivirus->get_incidence_details($file, $filename, $notice);
                $antivirus->message_admins($notice, FORMAT_MOODLE, 'infected');

                // Move to quarantine folder.
                $zipfile = \core\antivirus\quarantine::quarantine_file($file, $filename, $incidencedetails, $notice);

                // Log file infected event.
                $params = array(
                    'context' => \context_system::instance(),
                    'relateduserid' => $USER->id,
                    'other' => ['filename' => $filename, 'zipfile' => $zipfile, 'incidencedetails' => $incidencedetails],
                );
                $event = \core\event\antivirus_file_infected::create($params);
                $event->trigger();

                if ($deleteinfected) {
                    unlink($file);
                }
                throw new \core\antivirus\scanner_exception('virusfound', '', array('item' => $filename));
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
            $result = $antivirus->scan_data($data);
            if ($result === $antivirus::SCAN_RESULT_FOUND) {
                // Infection found, send notification.
                $filename = get_string('datastream', 'antivirus');
                $notice = $antivirus->get_scanning_notice();
                $incidencedetails = $antivirus->get_incidence_details('', $filename, $notice);
                $antivirus->message_admins($notice, FORMAT_MOODLE, 'infected');

                // Copy data to quarantine folder.
                $zipfile = \core\antivirus\quarantine::quarantine_data($data, $filename, $incidencedetails, $notice);

                // Log file infected event.
                $params = array(
                    'context' => \context_system::instance(),
                    'relateduserid' => $USER->id,
                    'other' => ['filename' => $filename, 'zipfile' => $zipfile, 'incidencedetails' => $incidencedetails],
                );
                $event = \core\event\antivirus_data_infected::create($params);
                $event->trigger();

                throw new \core\antivirus\scanner_exception('virusfound', '', array('item' => get_string('datastream', 'antivirus')));
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
}
