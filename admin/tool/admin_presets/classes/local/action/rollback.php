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

use stdClass;
use tool_admin_presets\form\continue_form;

/**
 * This class extends base class and handles rollback function.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rollback extends base {

    /**
     * Displays the different previous applications of the preset
     */
    public function show(): void {
        global $DB, $OUTPUT;

        // Preset data.
        $preset = $DB->get_record('adminpresets', ['id' => $this->id]);

        // Applications data.
        $context = new stdClass();
        $applications = $DB->get_records('adminpresets_app', ['adminpresetid' => $this->id], 'time DESC');
        $context->noapplications = !empty($applications);
        $context->applications = [];
        foreach ($applications as $application) {
            $format = get_string('strftimedatetime', 'langconfig');
            $user = $DB->get_record('user', ['id' => $application->userid]);
            $rollbacklink = new \moodle_url(
                '/admin/tool/admin_presets/index.php',
                ['action' => 'rollback', 'mode' => 'execute', 'id' => $application->id, 'sesskey' => sesskey()]
            );

            $context->applications[] = [
                'timeapplied' => \core_date::strftime($format, (int)$application->time),
                'user' => fullname($user),
                'action' => $rollbacklink->out(false),
            ];
        }

        $this->outputs .= '<br/>' . $OUTPUT->heading(get_string('presetname', 'tool_admin_presets') . ': ' . $preset->name, 3);
        $this->outputs = $OUTPUT->render_from_template('tool_admin_presets/preset_applications_list', $context);

        $url = new \moodle_url('/admin/tool/admin_presets/index.php');
        $this->moodleform = new continue_form($url);
    }

    /**
     * Executes the application rollback
     *
     * Each setting value is checked against the config_log->value
     */
    public function execute(): void {
        global $OUTPUT;

        require_sesskey();

        list($presetapp, $rollback, $failures) = $this->manager->revert_preset($this->id);

        if (!is_null($presetapp)) {
            // Change $this->id to point to the preset.
            $this->id = $presetapp->adminpresetid;
        }

        $appliedchanges = new stdClass();
        $appliedchanges->show = !empty($rollback);
        $appliedchanges->caption = get_string('rollbackresults', 'tool_admin_presets');
        $appliedchanges->settings = $rollback;

        $skippedchanges = new stdClass();
        $skippedchanges->show = !empty($failures);
        $skippedchanges->caption = get_string('rollbackfailures', 'tool_admin_presets');
        $skippedchanges->settings = $failures;

        $data = new stdClass();
        $data->appliedchanges = $appliedchanges;
        $data->skippedchanges = $skippedchanges;
        $data->beforeapplying = true;
        $this->outputs = $OUTPUT->render_from_template('tool_admin_presets/settings_rollback', $data);

        $url = new \moodle_url('/admin/tool/admin_presets/index.php');
        $this->moodleform = new continue_form($url);
    }

    protected function get_title(): string {
        global $DB;

        $title = '';
        if ($preset = $DB->get_record('adminpresets', ['id' => $this->id])) {
            $title = get_string($this->action . $this->mode, 'tool_admin_presets', $preset->name);
        }

        return $title;
    }

    protected function get_explanatory_description(): ?string {
        $text = null;
        if ($this->mode == 'show') {
            $text = get_string('rollbackdescription', 'tool_admin_presets');
        }

        return $text;
    }
}
