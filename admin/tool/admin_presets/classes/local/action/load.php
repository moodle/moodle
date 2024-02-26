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

use moodle_exception;
use stdClass;
use tool_admin_presets\form\continue_form;
use tool_admin_presets\form\load_form;
use tool_admin_presets\output\presets_list;

/**
 * This class extends base class and handles load function.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class load extends base {

    /**
     * Executes the settings load into the system
     */
    public function execute(): void {
        global $OUTPUT;

        $url = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'load', 'mode' => 'execute']);
        $this->moodleform = new load_form($url);

        if ($this->moodleform->is_cancelled()) {
            redirect(new \moodle_url('/admin/tool/admin_presets/index.php?action=base'));
        }

        if ($this->moodleform->is_submitted() && $this->moodleform->is_validated() && ($this->moodleform->get_data())) {
            // Apply preset settings and set plugins visibility.
            [$applied, $skipped] = $this->manager->apply_preset($this->id);

            if (empty($applied)) {
                $message = [
                    'message' => get_string('nothingloaded', 'tool_admin_presets'),
                    'closebutton' => true,
                    'announce' => true,
                ];
            } else {
                $message = [
                    'message' => get_string('settingsappliednotification', 'tool_admin_presets'),
                    'closebutton' => true,
                    'announce' => true,
                ];
            }
            $application = new stdClass();
            $applieddata = new stdClass();
            $applieddata->show = !empty($applied);
            $applieddata->message = $message;
            $applieddata->heading = get_string('settingsapplied', 'tool_admin_presets');
            $applieddata->caption = get_string('settingsapplied', 'tool_admin_presets');
            $applieddata->settings = $applied;
            $application->appliedchanges = $applieddata;

            $skippeddata = new stdClass();
            $skippeddata->show = !empty($skipped);
            $skippeddata->heading = get_string('settingsnotapplied', 'tool_admin_presets');
            $skippeddata->caption = get_string('settingsnotapplicable', 'tool_admin_presets');
            $skippeddata->settings = $skipped;
            $application->skippedchanges = $skippeddata;

            $this->outputs = $OUTPUT->render_from_template('tool_admin_presets/settings_application', $application);
            $url = new \moodle_url('/admin/tool/admin_presets/index.php');
            $this->moodleform = new continue_form($url);
        }
    }

    /**
     * Displays the select preset settings to select what to import.
     * Loads the preset data and displays a settings tree.
     *
     * It checks the Moodle version and it only allows users to import
     * the preset available settings.
     */
    public function show(): void {
        $this->display_preset(true);
    }

    /**
     * Displays a preset information (name, description, settings different from the current configuration...).
     */
    public function preview(): void {
        $this->display_preset(false, false);
    }

    /**
     * Method to prepare the information to preview/load the preset.
     *
     * @param bool $displayform Whether the form should be displayed in the page or not.
     * @param bool $raiseexception Whether the exception should be raised or not when the preset doesn't exist. When it's set
     * to false, a message is displayed, instead of raising the exception.
     */
    protected function display_preset(bool $displayform = true, bool $raiseexception = true) {
        global $DB, $OUTPUT;

        $data = new stdClass();
        $data->id = $this->id;

        // Preset data.
        if (!$preset = $DB->get_record('adminpresets', ['id' => $data->id])) {
            if ($raiseexception) {
                throw new moodle_exception('errornopreset', 'core_adminpresets');
            } else {
                $this->outputs = get_string('errornopreset', 'core_adminpresets');
                return;
            }
        }

        // Print preset basic data.
        $list = new presets_list([$preset]);
        $this->outputs = $OUTPUT->render($list);

        // Simulate preset application to display settings and plugins that will change.
        [$applied] = $this->manager->apply_preset($this->id, true);

        // Order the applied array by the visiblename column.
        if (!empty($applied)) {
            $visiblenamecolumn = array_column($applied, 'visiblename');
            array_multisort($visiblenamecolumn, SORT_ASC, $applied);
        }

        $application = new stdClass();
        $applieddata = new stdClass();
        $applieddata->show = !empty($applied);
        $applieddata->heading = get_string('settingstobeapplied', 'tool_admin_presets');
        $applieddata->caption = get_string('settingsapplied', 'tool_admin_presets');
        $applieddata->settings = $applied;
        $applieddata->beforeapplying = true;
        $application->appliedchanges = $applieddata;
        if ($displayform) {
            if (empty($applied)) {
                // Display a warning when no settings will be applied.
                $applieddata->message = get_string('nosettingswillbeapplied', 'tool_admin_presets');

                // Only display the Continue button.
                $url = new \moodle_url('/admin/tool/admin_presets/index.php');
                $this->moodleform = new continue_form($url);
            } else {
                // Display the form to apply the preset.
                $url = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'load', 'mode' => 'execute']);
                $this->moodleform = new load_form($url);
                $this->moodleform->set_data($data);
            }
        }

        $this->outputs .= $OUTPUT->render_from_template('tool_admin_presets/settings_application', $application);
    }

    protected function get_explanatory_description(): ?string {
        $text = null;
        if ($this->mode == 'show') {
            $text = get_string('loaddescription', 'tool_admin_presets');
        }

        return $text;
    }
}
