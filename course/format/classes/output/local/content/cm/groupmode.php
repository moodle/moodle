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

namespace core_courseformat\output\local\content\cm;

use cm_info;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use core\output\named_templatable;
use core\output\choicelist;
use core\output\local\dropdown\status;
use pix_icon;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render an activity group mode badge.
 *
 * @package   core_courseformat
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groupmode implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    private $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     */
    public function __construct(
        course_format $format,
        section_info $section,
        cm_info $mod,
    ) {
        $this->format = $format;
        $this->section = $section;
        $this->mod = $mod;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass|null data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): ?stdClass {
        if (!$this->format->show_groupmode($this->mod)) {
            return null;
        }
        if ($this->format->show_editor() && $this->format->supports_components()) {
            return $this->build_editor_data($output);
        }
        // If the group mode is not editable, the no groups badge is not displayed.
        if ($this->mod->effectivegroupmode === NOGROUPS) {
            return null;
        }
        return $this->build_static_data($output);
    }

    /**
     * Build the data for the static badge.
     * @param \renderer_base $output
     * @return stdClass
     */
    protected function build_static_data(\renderer_base $output): stdClass {
        switch ($this->mod->effectivegroupmode) {
            case SEPARATEGROUPS:
                $groupalt = get_string('groupsseparate', 'group');
                $groupicon = $this->get_action_icon('cmSeparateGroups', $groupalt);
                break;
            case VISIBLEGROUPS:
                $groupalt = get_string('groupsvisible', 'group');
                $groupicon = $this->get_action_icon('cmVisibleGroups', $groupalt);
                break;
            case NOGROUPS:
            default:
                $groupalt = get_string('groupsnone', 'group');
                $groupicon = $this->get_action_icon('cmNoGroups', $groupalt);
                break;
        }
        $data = (object) [
            'groupicon' => $output->render($groupicon),
            'groupalt' => $groupalt,
            'isInteractive' => false,
        ];
        return $data;
    }

    /**
     * Build the data for the interactive dropdown.
     * @param \renderer_base $output
     * @return stdClass
     */
    protected function build_editor_data(\renderer_base $output): stdClass {
        $choice = $this->get_choice_list();
        $result = $this->get_dropdown_data($output, $choice);
        $result->autohide = ($this->mod->effectivegroupmode === NOGROUPS);
        return $result;
    }

    /**
     * Build the data for the interactive dropdown.
     * @param \renderer_base $output
     * @param choicelist $choice the choice list
     * @return stdClass
     */
    protected function get_dropdown_data(\renderer_base $output, choicelist $choice): stdClass {
        $buttondata = $this->build_static_data($output);
        $dropdown = new status(
            $buttondata->groupicon,
            $choice,
            ['dialogwidth' => status::WIDTH['big']],
        );
        $dropdown->set_dialog_width(status::WIDTH['small']);
        $dropdown->set_position(status::POSITION['end']);
        return (object) [
            'isInteractive' => true,
            'groupicon' => $buttondata->groupicon,
            'groupalt' => $buttondata->groupalt,
            'dropwdown' => $dropdown->export_for_template($output),
        ];
    }

    /**
     * Create a choice list for the dropdown.
     * @return choicelist the choice list
     */
    public function get_choice_list(): choicelist {
        $choice = new choicelist();
        $choice->add_option(
            NOGROUPS,
            get_string('groupsnone', 'group'),
            $this->get_option_data(null, 'cmNoGroups', $this->mod->id)
        );
        $choice->add_option(
            SEPARATEGROUPS,
            get_string('groupsseparate', 'group'),
            $this->get_option_data('groupsseparate', 'cmSeparateGroups', $this->mod->id)
        );
        $choice->add_option(
            VISIBLEGROUPS,
            get_string('groupsvisible', 'group'),
            $this->get_option_data('groupsvisible', 'cmVisibleGroups', $this->mod->id)
        );
        $choice->set_selected_value($this->mod->effectivegroupmode);
        return $choice;
    }

    /**
     * Get the data for the option.
     * @param string|null $name the name of the option
     * @param string $action the state action of the option
     * @param int $id the id of the module
     * @return array
     */
    private function get_option_data(?string $name, string $action, int $id): array {
        return [
            'description' => ($name) ? get_string("groupmode_{$name}_help", 'group') : null,
            // The dropdown icons are decorative, so we don't need to provide alt text.
            'icon' => $this->get_action_icon($action),
            'extras' => [
                'data-id' => $id,
                'data-action' => $action,
            ]
        ];
    }

    /**
     * Get the group mode icon.
     * @param string $groupmode the group mode
     * @param string $groupalt the alt text
     * @return pix_icon
     */
    protected function get_action_icon(string $groupmode, string $groupalt = ''): pix_icon {
        $icons = [
            'cmNoGroups' => 'i/groupn',
            'cmSeparateGroups' => 'i/groups',
            'cmVisibleGroups' => 'i/groupv',
        ];
        return new pix_icon($icons[$groupmode], $groupalt);
    }
}
