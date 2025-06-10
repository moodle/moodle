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
 * Version information class
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;
// Prepare for code checker update. Will be removed on INT-17966.
// @codingStandardsIgnoreLine
defined('MOODLE_INTERNAL') || die();

use core_component,
    core_plugin_manager,
    moodle_exception;

/**
 * Version information class
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class version_information {

    /**
     * @var bool|stdClass
     */
    public $core;

    /**
     * @var bool|stdClass
     */
    public $toolally;

    /**
     * @var bool|stdClass
     */
    public $filterally;

    /**
     * @var bool|stdClass
     */
    public $reportally;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->warn_on_site_policy_not_accepted();
        $this->core = $this->get_component_version('core');
        $this->toolally = $this->get_component_version('tool_ally');
        $this->filterally = $this->get_component_version('filter_ally');
        $this->filterally->active = $this->check_filter_active();
        $this->reportally = $this->get_component_version('report_allylti');
        $this->system = $this->get_system_info();
    }

    /**
     * Throw an error if site policy not accepted.
     * @throws moodle_exception
     */
    private function warn_on_site_policy_not_accepted() {
        global $CFG, $USER;
        $manager = new \core_privacy\local\sitepolicy\manager();
        // Check that the user has agreed to a site policy if there is one - do not test in case of admins.
        if (empty($USER->policyagreed) and !is_siteadmin()) {
            if ($manager->is_defined() and !isguestuser()) {
                $url = $manager->get_embed_url();
                throw new moodle_exception('sitepolicynotagreed', 'error', '', $url->get_path());
            } else if ($manager->is_defined(true) and isguestuser()) {
                $guesturl = $manager->get_embed_url(true);
                throw new moodle_exception('sitepolicynotagreed', 'error', '', $guesturl->get_path());
            }
        }
    }

    /**
     * Returns the version information of an installed component.
     *
     * @param string $component component name
     * @return \stdClass|bool version data or false if the component is not found
     */
    private function get_component_version($component) {
        global $CFG;

        list($type, $name) = core_component::normalize_component($component);

        // Get Moodle core version.
        if ($type === 'core') {
            return (object) [
                'version' => $CFG->version,
                'release' => $CFG->release,
                'branch' => $CFG->branch
            ];
        }

        // Check installed.
        $installed = core_component::get_component_directory($component) !== null;

        // Get plugin version.
        if ($installed) {
            $pluginman = core_plugin_manager::instance();
            try {
                $plug = $pluginman->get_plugin_info($component);
            } catch (\Exception $e) {
                $plug = false;
            }
            if (!$plug) {
                $installed = false;
            }
        }

        $plugin = new \stdClass();
        $plugin->installed = $installed;

        if ($plugin && $installed) {
            $plugin->version = $plug->versiondb;
            $plugin->requires = $plug->versionrequires;
            $plugin->release = $plug->release;
        }

        return $plugin;
    }

    protected function check_filter_active() {
        return !empty(filter_get_global_states()['ally']);
    }

    private function get_db_version() {
        global $CFG, $DB;

        if (stripos($CFG->dbtype, 'mysql') !== false ||
            stripos($CFG->dbtype, 'pgsql') !== false
        ) {
            $row = (array) $DB->get_record_sql('SELECT version();');
            if (isset($row['version'])) {
                return $row['version'];
            } else if (isset($row['version()'])) {
                return $row['version()'];
            }
        }

        return 'unknown';
    }

    private function get_system_info() {
        global $CFG;

        // It would be so nice if Moodle used PDO for DB connections :-(
        // https://stackoverflow.com/a/32197593/6756121.
        return (object) [
            'os' => php_uname(),
            'phposbuild' => PHP_OS,
            'phpversion' => phpversion(),
            'dbtype' => $CFG->dbtype,
            'dbversion' => $this->get_db_version()
        ];
    }
}
