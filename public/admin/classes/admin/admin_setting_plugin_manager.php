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

namespace core_admin\admin;

use admin_setting;
use core_plugin_manager;
use core_text;
use core_admin\admin_search;

/**
 * Admin setting plugin manager.
 *
 * @package    core_admin
 * @subpackage admin
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_plugin_manager extends admin_setting {
    /** @var core_plugin_manager The plugin manager instance */
    protected core_plugin_manager $pluginmanager;

    /** @var string The plugintype that this manager covers */
    protected string $plugintype;

    /** @var string The class of the management table to use */
    protected string $tableclass;

    public function __construct(
        string $plugintype,
        string $tableclass,
        string $name,
        string $visiblename,
        string $description = '',
        string $defaultsetting = '',
    ) {
        $this->nosave = true;
        $this->pluginmanager = core_plugin_manager::instance();
        $this->plugintype = $plugintype;
        $this->tableclass = $tableclass;

        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting(): bool {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting(): bool {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    // phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
    public function write_setting($data): string {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available editors
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $plugins = $this->pluginmanager->get_installed_plugins($this->plugintype);
        foreach (array_keys($plugins) as $plugin) {
            $plugin = "{$this->plugintype}_{$plugin}";
            if (str_contains($plugin, $query)) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }

            $pluginname = get_string('pluginname', $plugin);
            if (strpos(core_text::strtolower($pluginname), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }

        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = ''): string {
        $table = new $this->tableclass();
        if (!($table instanceof \core_admin\table\plugin_management_table)) {
            throw new \coding_exception("{$this->tableclass} must be an instance of \\core_admin\\table\\plugin_management_table");
        }
        return highlight($query, $table->get_content());
    }
}
