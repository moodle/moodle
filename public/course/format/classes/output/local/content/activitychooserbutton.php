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

namespace core_courseformat\output\local\content;

use action_link;
use cm_info;
use renderable;
use renderer_base;
use section_info;
use stdClass;
use core\output\named_templatable;
use core\di;
use core\hook;

/**
 * Class to render a activity chooser button.
 *
 * @package    core_course
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activitychooserbutton implements named_templatable, renderable {
    /**
     * Constructor.
     *
     * @todo remove $sectionreturn int support in 6.0 (MDL-86310)
     * @param section_info $section the section info
     * @param cm_info|null $mod the course module ionfo
     * @param int|section_info|null $sectionreturn the section to return to
     * @param array|null $actionlinks the action links
     */
    public function __construct(
        /** @var section_info the section object */
        protected section_info $section,
        /** @var cm_info|null the course module instance */
        protected ?cm_info $mod = null,
        /** @var int|null the section to return to */
        protected section_info|int|null $sectionreturn = null,
        /** @var array|null action_link[] the action links */
        protected ?array $actionlinks = [],
    ) {
        if (is_int($this->sectionreturn)) {
            debugging(
                'Using int as sectionreturn in activitychooserbutton is deprecated, use a section_info instance instead',
                DEBUG_DEVELOPER
            );
            $this->sectionreturn = get_fast_modinfo($this->section->course)->get_section_info($this->sectionreturn);
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        // Look for plugins that want to add extra action links to the activity chooser button.
        di::get(hook\manager::class)->dispatch(
            new \core_course\hook\before_activitychooserbutton_exported(
                $this,
                $this->section,
                $this->mod,
            ),
        );
        /** @var section_info|null $sectionreturn */
        $sectionreturn = $this->sectionreturn;

        return (object)[
            // We keep the old sectionnum properties for backwards compatibility.
            'sectionnum' => $this->section->sectionnum,
            'sectionid' => $this->section->id,
            'sectionname' => get_section_name($this->section->course, $this->section),
            'sectionreturn' => $sectionreturn?->sectionnum ?? false,
            'sectionreturnid' => $sectionreturn?->id ?? false,
            'modid' => $this->mod ? $this->mod->id : false,
            'activityname' => $this->mod ? $this->mod->get_formatted_name() : false,
            'hasactionlinks' => !empty($this->actionlinks),
            'actionlinks' => array_map(fn(action_link $action) => $action->export_for_template($output), $this->actionlinks),
        ];
    }

    /**
     * Add an action link.
     *
     * @param action_link $action the action link to add
     */
    public function add_action_link(action_link $action): void {
        $this->actionlinks[] = $action;
    }

    #[\Override]
    public function get_template_name(renderer_base $renderer): string {
        return 'core_courseformat/local/content/activitychooserbutton';
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(activitychooserbutton::class, \core_course\output\activitychooserbutton::class);
