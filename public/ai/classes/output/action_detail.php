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

namespace core_ai\output;

/**
 * Renderable for the full detail of a single logged AI action.
 *
 * @package    core_ai
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_detail implements \renderable, \templatable {
    /** @var string[] Action basenames that store a text prompt and a generated text response. */
    private const TEXT_ACTIONS = ['generate_text', 'summarise_text', 'explain_text'];

    /**
     * Create a new action_detail renderable.
     *
     * @param \stdClass $record The merged ai_action_register record, as returned by
     *                          {@see \core_ai\manager::get_action_detail()}.
     */
    public function __construct(
        /** @var \stdClass $record */
        protected readonly \stdClass $record,
    ) {
    }

    #[\Override]
    public function export_for_template(\renderer_base $output): array {
        global $USER;

        $record = $this->record;
        $typedata = $record->typedata;

        // The stored prompt and generated content are only shown to the user who performed the
        // action: exposing that text to course staff has no privacy sign-off, so other viewers
        // see the action metadata only.
        $ownaction = (int) $record->userid === (int) $USER->id;

        $actioncontext = \context::instance_by_id($record->contextid, IGNORE_MISSING);
        $user = \core_user::get_user($record->userid);

        $data = [
            'id' => $record->id,
            'actionname' => get_string("action_{$record->actionname}", 'core_ai'),
            'provider' => $this->format_provider($record->provider),
            'model' => $record->model,
            'success' => (bool) $record->success,
            'statustext' => $record->success ? get_string('yes') : get_string('no'),
            'errormessage' => $record->errormessage,
            'timecreated' => userdate($record->timecreated),
            'timecompleted' => $record->timecompleted ? userdate($record->timecompleted) : null,
            'contextname' => $actioncontext ? $actioncontext->get_context_name() : null,
            'contexturl' => $actioncontext ? $actioncontext->get_url()->out(false) : null,
            'userfullname' => $user ? fullname($user) : null,
            'userprofileurl' => $user ? (new \moodle_url('/user/profile.php', ['id' => $user->id]))->out(false) : null,
            'istext' => $ownaction && in_array($record->actionname, self::TEXT_ACTIONS, true) && $typedata !== null,
            'isimage' => $ownaction && $record->actionname === 'generate_image' && $typedata !== null,
        ];

        if ($data['istext']) {
            $data['prompt'] = $typedata->prompt;
            $data['generatedcontent'] = $typedata->generatedcontent;
            $data['prompttokens'] = $record->prompttokens;
            $data['completiontokens'] = $record->completiontokens;
        }

        if ($data['isimage']) {
            $data['prompt'] = $typedata->prompt;
            $data['revisedprompt'] = $typedata->revisedprompt;
            $data['sourceurl'] = $typedata->sourceurl;
            $data['quality'] = $typedata->quality;
            $data['aspectratio'] = $typedata->aspectratio;
            $data['style'] = $typedata->style;
            $data['numberimages'] = $typedata->numberimages;
        }

        return $data;
    }

    /**
     * Format a provider identifier for display, falling back to the raw identifier when no plugin name
     * string exists for it (for example a provider that has since been uninstalled).
     *
     * @param string $provider The provider component name.
     * @return string
     */
    private function format_provider(string $provider): string {
        if (get_string_manager()->string_exists('pluginname', $provider)) {
            return get_string('pluginname', $provider);
        }
        return $provider;
    }
}
