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
 * Contains the default activity availability information.
 *
 * @package   core_courseformat
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\cm;

use action_menu_link_secondary;
use core\output\local\action_menu\subpanel as action_menu_subpanel;
use cm_info;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use core\output\choicelist;
use core\output\local\dropdown\status;
use core\output\named_templatable;
use pix_icon;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render a course module availability inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class visibility implements named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    protected $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /**
     * Constructor.
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     */
    public function __construct(course_format $format, section_info $section, cm_info $mod) {
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
        if (!$this->show_visibility()) {
            return null;
        }
        $format = $this->format;
        // In rare legacy cases, the section could be stealth (orphaned) but they are not editable.
        if (!$format->show_editor()
            || !has_capability('moodle/course:activityvisibility', $this->mod->context)) {
            return $this->build_static_data($output);
        } else {
            return $this->build_editor_data($output);
        }
    }

    /**
     * Check if the visibility is displayed.
     * @return bool
     */
    protected function show_visibility(): bool {
        return !$this->mod->visible || $this->mod->is_stealth();
    }

    /**
     * Get the icon for the section visibility.
     * @param string $selected the visibility selected value
     * @return pix_icon
     */
    protected function get_icon(string $selected): pix_icon {
        if ($selected === 'hide') {
            return new pix_icon('t/show', '');
        } else if ($selected === 'stealth') {
            return new pix_icon('t/stealth', '');
        } else {
            return new pix_icon('t/hide', '');
        }
    }

    /**
     * Build the data for the editor.
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass|null data context for a mustache template
     */
    public function build_editor_data(\renderer_base $output): ?stdClass {
        $choice = $this->get_choice_list();
        return $this->get_dropdown_data($output, $choice);
    }

    /**
     * Build the data for the interactive dropdown.
     * @param \renderer_base $output
     * @param choicelist $choice the choice list
     * @return stdClass
     */
    protected function get_dropdown_data(
        \renderer_base $output,
        choicelist $choice,
    ): stdClass {
        $badgetext = $output->sr_text(get_string('availability'));

        if (!$this->mod->visible) {
            $badgetext .= get_string('hiddenfromstudents');
            $icon = $this->get_icon('hide');
        } else if ($this->mod->is_stealth()) {
            $badgetext .= get_string('hiddenoncoursepage');
            $icon = $this->get_icon('stealth');
        } else {
            $badgetext .= get_string("availability_show", 'core_courseformat');
            $icon = $this->get_icon('show');
        }
        $dropdown = new status(
            $output->render($icon) . ' ' . $badgetext,
            $choice,
            ['dialogwidth' => status::WIDTH['big']],
        );
        return (object) [
            'isInteractive' => true,
            'dropwdown' => $dropdown->export_for_template($output),
        ];
    }

    /**
     * Get the availability choice list.
     * @return choicelist
     */
    public function get_choice_list(): choicelist {
        $choice = $this->create_choice_list();
        $choice->set_selected_value($this->get_selected_choice_value());
        return $choice;
    }

    /**
     * Return the cm availability menu item.
     *
     * By default, the cm availability is displayed as a menu item subpanel.
     * However, it can be simplified when there is only one option and
     * it is not stealth (stealth require a subpanel to inform the user).
     *
     * @return action_menu_link_secondary|action_menu_subpanel|null
     */
    public function get_menu_item(): action_menu_link_secondary|action_menu_subpanel|null {
        $choice = $this->get_choice_list();
        $selectableoptions = $choice->get_selectable_options();

        if (count($selectableoptions) === 0) {
            return null;
        }

        // Visible activities in hidden sections are always considered stealth.
        if ($this->section->visible && count($selectableoptions) === 1) {
            $option = reset($selectableoptions);
            $actionlabel = $option->value === 'show' ? 'modshow' : 'modhide';
            return new action_menu_link_secondary(
                $option->url,
                $option->icon,
                get_string($actionlabel, 'moodle'),
                $choice->get_option_extras($option->value)
            );
        }

        return new action_menu_subpanel(
            get_string('availability', 'moodle'),
            $choice,
            ['class' => 'editing_availability'],
            new pix_icon('t/hide', '', 'moodle', ['class' => 'iconsmall'])
        );
    }

    /**
     * Get the selected choice value depending on the course, section and stealth settings.
     * @return string
     */
    protected function get_selected_choice_value(): string {
        if (!$this->mod->visible) {
            return 'hide';
        }
        if (!$this->mod->is_stealth()) {
            return 'show';
        }
        if (!$this->section->visible) {
            // All visible activities in a hidden sections are considered stealth
            // but they don't use the stealth attribute for it. It is just implicit.
            return 'show';
        }
        return 'stealth';
    }

    /**
     * Create a choice list for the dropdown.
     * @return choicelist the choice list
     */
    protected function create_choice_list(): choicelist {
        global $CFG;

        $choice = new choicelist();
        if ($this->section->visible || $this->mod->has_view()) {
            $label = $this->section->visible ? 'show' : 'stealth';
            $choice->add_option(
                'show',
                get_string("availability_{$label}", 'core_courseformat'),
                $this->get_option_data($label, 'cmShow')
            );
        }
        $choice->add_option(
            'hide',
            get_string('availability_hide', 'core_courseformat'),
            $this->get_option_data('hide', 'cmHide')
        );

        if ($CFG->allowstealth && $this->format->allow_stealth_module_visibility($this->mod, $this->section)) {
            $choice->add_option(
                'stealth',
                get_string('availability_stealth', 'core_courseformat'),
                $this->get_option_data('stealth', 'cmStealth')
            );
        }
        return $choice;
    }

    /**
     * Get the data for the option.
     * @param string $name the name of the option
     * @param string $action the state action of the option
     * @return array
     */
    private function get_option_data(string $name, string $action): array {
        return [
            'description' => get_string("availability_{$name}_help", 'core_courseformat'),
            'icon' => $this->get_icon($name),
            // Non-ajax behat is not smart enough to discrimante hidden links
            // so we need to keep providing the non-ajax links.
            'url' => $this->format->get_non_ajax_cm_action_url($action, $this->mod),
            'extras' => [
                'data-id' => $this->mod->id,
                'data-action' => $action,
            ]
        ];
    }

    /**
     * Build the static badges data.
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass|null data context for a mustache template
     */
    public function build_static_data(\renderer_base $output): ?stdClass {
        $data = (object) [
            'isInteractive' => false,
        ];

        if (!$this->mod->visible) {
            $data->modhiddenfromstudents = true;
        } else if ($this->mod->is_stealth()) {
            $data->modstealth = true;
        }
        return $data;
    }
}
