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
 * Check API manager
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check;

defined('MOODLE_INTERNAL') || die();

/**
 * Check API manager
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * The list of valid check types
     */
    public const TYPES = ['status', 'security', 'performance'];

    /**
     * Return all status checks
     *
     * @param string $type of checks to fetch
     * @return array of check objects
     */
    public static function get_checks(string $type): array {
        if (!in_array($type, self::TYPES)) {
            throw new \moodle_exception("Invalid check type '$type'");
        }
        $method = 'get_' . $type . '_checks';
        $checks = self::$method();
        return $checks;
    }

    /**
     * Return all performance checks
     *
     * @return array of check objects
     */
    public static function get_performance_checks(): array {
        $checks = [
            new performance\designermode(),
            new performance\cachejs(),
            new performance\debugging(),
            new performance\backups(),
            new performance\stats(),
            new performance\dbschema(),
        ];

        // Any plugin can add status checks to this report by implementing a callback
        // <component>_status_checks() which returns a check object.
        $morechecks = get_plugins_with_function('performance_checks', 'lib.php');
        foreach ($morechecks as $plugintype => $plugins) {
            foreach ($plugins as $plugin => $pluginfunction) {
                $result = $pluginfunction();
                foreach ($result as $check) {
                    $check->set_component($plugintype . '_' . $plugin);
                    $checks[] = $check;
                }
            }
        }
        return $checks;
    }

    /**
     * Return all status checks
     *
     * @return array of check objects
     */
    public static function get_status_checks(): array {
        $checks = [
            new environment\environment(),
            new environment\upgradecheck(),
            new environment\antivirus(),
        ];

        // Any plugin can add status checks to this report by implementing a callback
        // <component>_status_checks() which returns a check object.
        $morechecks = get_plugins_with_function('status_checks', 'lib.php');
        foreach ($morechecks as $plugintype => $plugins) {
            foreach ($plugins as $plugin => $pluginfunction) {
                $result = $pluginfunction();
                foreach ($result as $check) {
                    $check->set_component($plugintype . '_' . $plugin);
                    $checks[] = $check;
                }
            }
        }
        return $checks;
    }

    /**
     * Return all security checks
     *
     * @return array of check objects
     */
    public static function get_security_checks(): array {
        $checks = [
            new environment\displayerrors(),
            new environment\unsecuredataroot(),
            new environment\publicpaths(),
            new environment\configrw(),
            new environment\preventexecpath(),
            new security\embed(),
            new security\openprofiles(),
            new security\crawlers(),
            new security\passwordpolicy(),
            new security\emailchangeconfirmation(),
            new security\webcron(),
            new http\cookiesecure(),
            new access\riskadmin(),
            new access\riskxss(),
            new access\riskbackup(),
            new access\defaultuserrole(),
            new access\guestrole(),
            new access\frontpagerole(),
        ];
        // Any plugin can add security checks to this report by implementing a callback
        // <component>_security_checks() which returns a check object.
        $morechecks = get_plugins_with_function('security_checks', 'lib.php');
        foreach ($morechecks as $plugintype => $plugins) {
            foreach ($plugins as $plugin => $pluginfunction) {
                $result = $pluginfunction();
                foreach ($result as $check) {
                    $check->set_component($plugintype . '_' . $plugin);
                    $checks[] = $check;
                }
            }
        }
        return $checks;
    }
}
