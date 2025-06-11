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

use core_adminpresets\manager;
use moodle_exception;

/**
 * This class extends base class and handles delete function.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete extends base {

    /**
     * Shows a confirm box
     */
    public function show(): void {
        global $DB, $OUTPUT;

        // Check the preset exists (cannot delete the pre-installed core "Starter" and "Full" presets).
        $presetdata = $DB->get_record('adminpresets', ['id' => $this->id, 'iscore' => manager::NONCORE_PRESET], 'name');

        if ($presetdata) {
            $deletetext = get_string('deletepreset', 'tool_admin_presets', $presetdata->name);

            $params = ['action' => $this->action, 'mode' => 'execute', 'id' => $this->id, 'sesskey' => sesskey()];
            $confirmurl = new \moodle_url('/admin/tool/admin_presets/index.php', $params);

            $cancelurl = new \moodle_url('/admin/tool/admin_presets/index.php');

            // If the preset was applied add a warning text.
            if ($DB->get_records('adminpresets_app', ['adminpresetid' => $this->id])) {
                $deletetext .= '<p><strong>' .
                    get_string("deletepreviouslyapplied", "tool_admin_presets") . '</strong></p>';
            }
            $displayoptions = [
                'confirmtitle' => get_string('deletepresettitle', 'tool_admin_presets', $presetdata->name),
                'continuestr' => get_string('delete')
            ];
            $this->outputs = $OUTPUT->confirm($deletetext, $confirmurl, $cancelurl, $displayoptions);
        } else {
            throw new moodle_exception('errordeleting', 'core_adminpresets');
        }
    }

    /**
     * Delete the DB preset
     */
    public function execute(): void {
        require_sesskey();

        $this->manager->delete_preset($this->id);

        // Trigger the as it is usually triggered after execute finishes.
        $this->log();

        $url = new \moodle_url('/admin/tool/admin_presets/index.php');
        redirect($url);
    }
}
