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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class ParamsHelper {
    /**
     * Plugin name.
     */
    const PLUGIN = 'local_intellidata';

    /**
     * Migration mode disabled.
     */
    const MIGRATION_MODE_DISABLED = false;
    /**
     * Migration mode enabled.
     */
    const MIGRATION_MODE_ENABLED = true;

    /**
     * Status active.
     */
    const STATE_ACTIVE = 1;
    /**
     * Status in active.
     */
    const STATE_INACTIVE = 0;

    /**
     * Status system.
     */
    const CONTEXT_SYSTEM = 1;
    /**
     * Status course.
     */
    const CONTEXT_COURSE = 2;
    /**
     * Status course cat.
     */
    const CONTEXT_COURSECAT = 3;
    /**
     * Context user.
     */
    const CONTEXT_USER = 5;

    /**
     * Get metadata for export API.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_exportfiles_metadata() {
        return [
            'lastmigrationdate' => (int)SettingsHelper::get_setting('lastmigrationdate'),
            'lastexportdate' => (int)SettingsHelper::get_setting('lastexportdate'),
            'pluginversion' => self::get_plugin_version(),
        ];
    }

    /**
     * Get current plugin version.
     *
     * @return mixed
     * @throws \dml_exception
     */
    public static function get_plugin_version() {
        return get_config(self::PLUGIN)->version;
    }

    /**
     * Get current plugin version.
     *
     * @return mixed
     * @throws \dml_exception
     */
    public static function get_moodle_config() {
        global $CFG;

        return [
            'version' => $CFG->version,
            'release' => $CFG->release,
            'dbtype' => $CFG->dbtype,
            'cronenabled' => $CFG->cron_enabled,
            'moodleworkplace' => (int)class_exists('\tool_tenant\tenancy'),
            'totaraversion' => !empty($CFG->totara_version) ? $CFG->totara_version : '',
        ];
    }

    /**
     * Returns true if the moodle release is greater than the parameter release.
     *
     * @param string $comparerelease
     *
     * @return bool
     */
    public static function compare_release($comparerelease) {
        global $CFG;

        if (empty($CFG->release)) {
            return false;
        }

        $release = explode(" ", $CFG->release)[0];

        $versionparts = explode('.', $release);
        $requiredversionparts = explode('.', $comparerelease);

        $isgreater = false;
        for ($i = 0; $i < count($versionparts); $i++) {
            if (!isset($versionparts[$i]) || !isset($requiredversionparts[$i])) {
                break;
            }

            if ((int)$versionparts[$i] > (int)$requiredversionparts[$i]) {
                $isgreater = true;
                break;
            } else if ((int)$versionparts[$i] < (int)$requiredversionparts[$i]) {
                break;
            }
        }

        return $isgreater;
    }

    /**
     * Get Moodle version.
     */
    public static function get_release() {
        global $CFG;

        return !empty($CFG->release) ? (float)$CFG->release : null;
    }
}
