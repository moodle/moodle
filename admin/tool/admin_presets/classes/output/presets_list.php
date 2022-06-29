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
 * tool_admin_presets specific renderers
 *
 * @package   tool_admin_presets
 * @copyright  2021 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_admin_presets\output;

use core_adminpresets\manager;
use renderable;
use templatable;
use renderer_base;
use stdClass;
/**
 * Class containing data for admin_presets tool
 *
 * @copyright  2021 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class presets_list implements renderable, templatable {

    /**
     * @var stdClass[]    Array of admin presets.
     */
    private $presets;

    /**
     * @var bool    Wether the action menu is visible.
     */
    private $showactions;

    /**
     * Construct this renderable.
     *
     * @param stdClass[] $presets   Array of existing admin presets.
     * @param bool $showactions Whether actions should be displayed or not.
     */
    public function __construct(array $presets, bool $showactions = false) {
        $this->presets = $presets;
        $this->showactions = $showactions;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $DB;

        $context = new stdClass();
        $context->presets = [];
        foreach ($this->presets as $preset) {
            if ($preset->timeimported) {
                $timeimportedstring = userdate($preset->timeimported);
            } else {
                $timeimportedstring = '';
            }

            $data = [
                'name' => format_text($preset->name, FORMAT_PLAIN),
                'description' => format_text($preset->comments, FORMAT_HTML),
                'release' => format_text($preset->moodlerelease, FORMAT_PLAIN),
                'author' => format_text($preset->author, FORMAT_PLAIN),
                'site' => format_text(clean_text($preset->site, PARAM_URL), FORMAT_PLAIN),
                'timecreated' => userdate($preset->timecreated),
                'timeimported' => $timeimportedstring
            ];

            if ($this->showactions) {
                // Preset actions.
                $actionsmenu = new \action_menu();
                $actionsmenu->set_menu_trigger(get_string('actions'));
                $actionsmenu->set_owner_selector('preset-actions-' . $preset->id);

                $loadlink = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'load', 'id' => $preset->id]);
                $actionsmenu->add(new \action_menu_link_secondary(
                    $loadlink, new \pix_icon('t/play', ''),
                    get_string('applyaction', 'tool_admin_presets')
                ));
                $downloadlink = new \moodle_url('/admin/tool/admin_presets/index.php',
                    ['action' => 'export', 'mode' => 'download_xml', 'sesskey' => sesskey(), 'id' => $preset->id]
                );
                $actionsmenu->add(new \action_menu_link_secondary(
                    $downloadlink,
                    new \pix_icon('t/download', ''),
                    get_string('download')
                ));

                // Delete button won't be displayed for the pre-installed core "Starter" and "Full" presets.
                if ($preset->iscore == manager::NONCORE_PRESET) {
                    $deletelink = new \moodle_url('/admin/tool/admin_presets/index.php',
                    ['action' => 'delete', 'id' => $preset->id]
                    );
                    $actionsmenu->add(new \action_menu_link_secondary(
                        $deletelink,
                        new \pix_icon('i/delete', ''),
                        get_string('delete')
                    ));
                }

                // Look for preset applications.
                if ($DB->get_records('adminpresets_app', ['adminpresetid' => $preset->id])) {
                    $params = ['action' => 'rollback', 'id' => $preset->id];
                    $rollbacklink = new \moodle_url('/admin/tool/admin_presets/index.php', $params);
                    $actionsmenu->add(new \action_menu_link_secondary(
                        $rollbacklink,
                        new \pix_icon('i/reload', ''),
                        get_string('showhistory', 'tool_admin_presets')
                    ));
                }
                $data['actions'] = $actionsmenu->export_for_template($output);
            }
            $context->presets[] = $data;
        }
        $context->nopresets = empty($context->presets);
        $context->showactions = $this->showactions;

        return $context;
    }
}
