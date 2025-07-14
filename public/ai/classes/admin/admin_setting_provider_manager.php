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

namespace core_ai\admin;

use admin_setting;
use coding_exception;

/**
 * Admin setting provider manager.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_provider_manager extends admin_setting {
    /**
     * Constructor.
     *
     * @param string $pluginname The name of the plugin these actions related too.
     * @param string $tableclass The class of the management table to use.
     * @param string $name The unique name.
     * @param string $visiblename The localised name.
     * @param string $description The localised long description in Markdown format.
     * @param string $defaultsetting The default setting.
     */
    public function __construct(
        /** @var string The name of the plugin these actions related too */
        protected string $pluginname,
        /** @var string The class of the management table to use */
        protected string $tableclass,
        string $name,
        string $visiblename,
        string $description = '',
        string $defaultsetting = '',
    ) {
        $this->nosave = true;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    #[\Override]
    public function get_setting(): bool {
        return true;
    }

    #[\Override]
    public function write_setting($data): string {
        // Do not write any setting.
        return '';
    }

    #[\Override]
    public function output_html($data, $query = ''): string {
        $table = new $this->tableclass($this->pluginname);
        if (
            !($table instanceof \core_ai\table\aiprovider_management_table)
        ) {
            throw new coding_exception(sprintf(
                "% must be an instance aiprovider_management_table",
                $this->tableclass
            ));
        }
        return highlight($query, $table->get_content());
    }
}
