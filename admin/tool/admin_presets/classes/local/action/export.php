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

namespace tool_admin_presets\local\action;

defined('MOODLE_INTERNAL') || die();

use tool_admin_presets\form\export_form;
use moodle_exception;

global $CFG;
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/backup/util/xml/xml_writer.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/memory_xml_output.class.php');

/**
 * This class extends base class and handles export function.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export extends base {

    /**
     * Shows the initial form to export/save admin settings.
     *
     * Loads the database configuration and prints
     * the settings in a hierarchical table
     */
    public function show(): void {
        $url = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'export', 'mode' => 'execute']);
        $this->moodleform = new export_form($url);
    }

    /**
     * Stores a preset into the DB.
     */
    public function execute(): void {
        confirm_sesskey();

        $url = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'export', 'mode' => 'execute']);
        $this->moodleform = new export_form($url);

        if ($data = $this->moodleform->get_data()) {
            list($presetid, $settingsfound, $pluginsfound) = $this->manager->export_preset($data);

            // Store it here for logging and other future id-oriented stuff.
            $this->id = $presetid;

            // If there are no settings nor plugins, an error should be raised.
            if (!$settingsfound && !$pluginsfound) {
                $url = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'export']);
                redirect($url, get_string('novalidsettingsselected', 'tool_admin_presets'));
            }
        }

        // Trigger the as it is usually triggered after execute finishes.
        $this->log();

        $url = new \moodle_url('/admin/tool/admin_presets/index.php');
        redirect($url);
    }

    /**
     * To download system presets.
     *
     * @return void preset file
     * @throws dml_exception
     * @throws moodle_exception
     * @throws xml_output_exception
     * @throws xml_writer_exception
     */
    public function download_xml(): void {
        confirm_sesskey();

        list($xmlstr, $filename) = $this->manager->download_preset($this->id);

        // Trigger the as it is usually triggered after execute finishes.
        $this->log();

        send_file($xmlstr, $filename, 0, 0, true, true);
    }

    protected function get_explanatory_description(): ?string {
        return get_string('exportdescription', 'tool_admin_presets');
    }
}
