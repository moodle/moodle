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

namespace mod_data\output;

use moodle_url;
use templatable;
use renderable;

/**
 * Renderable class for the presets table in the database activity.
 *
 * @package    mod_data
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class presets implements templatable, renderable {

    /** @var int $id The database module id. */
    private $id;

    /** @var array $presets The array containing the existing presets. */
    private $presets;

    /** @var moodle_url $formactionurl The the action url for the form. */
    private $formactionurl;

    /** @var bool $manage Whether the manage preset options should be displayed. */
    private $manage;

    /**
     * The class constructor.
     *
     * @param int $id The database module id
     * @param array $presets The array containing the existing presets
     * @param moodle_url $formactionurl The the action url for the form
     * @param bool $manage Whether the manage preset options should be displayed
     */
    public function __construct(int $id, array $presets, moodle_url $formactionurl, bool $manage = false) {
        $this->id = $id;
        $this->presets = $presets;
        $this->formactionurl = $formactionurl;
        $this->manage = $manage;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output The renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {

        return [
            'd' => $this->id,
            'formactionul' => $this->formactionurl->out(),
            'presetstable' => $this->get_presets_table(),
        ];
    }

    /**
     * Generates and returns the HTML for the presets table.
     *
     * @return string
     */
    private function get_presets_table(): string {
        global $OUTPUT, $PAGE, $DB;

        $presetstable = new \html_table();
        $presetstable->align = ['center', 'left', 'left'];
        $presetstable->size = ['1%', '90%', '1%'];

        foreach ($this->presets as $preset) {
            $presetname = $preset->name;
            if (!empty($preset->userid)) {
                $userfieldsapi = \core_user\fields::for_name();
                $namefields = $userfieldsapi->get_sql('', false, '', '', false)->selects;
                $presetuser = $DB->get_record('user', array('id' => $preset->userid), 'id, ' . $namefields, MUST_EXIST);
                $username = fullname($presetuser, true);
                $presetname = "{$presetname} ({$username})";
            }

            $deleteaction = '';
            if ($this->manage) {
                if (data_user_can_delete_preset($PAGE->context, $preset) && $preset->name != 'Image gallery') {
                    $deleteactionurl = new moodle_url('/mod/data/preset.php',
                        ['d' => $this->id, 'fullname' => "{$preset->userid}/{$preset->shortname}",
                        'action' => 'confirmdelete']);
                    $deleteaction = $OUTPUT->action_icon($deleteactionurl,
                        new \pix_icon('t/delete', get_string('delete')));
                }
            }

            $presetstable->data[] = [
                \html_writer::tag('input', '', array('type' => 'radio', 'name' => 'fullname',
                    'value' => "{$preset->userid}/{$preset->shortname}")),
                $presetname,
                $deleteaction,
            ];
        }

        return \html_writer::table($presetstable);
    }
}
