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
namespace core_courseformat\output\local\content\section;

use context_course;
use core\output\choicelist;
use core\output\local\dropdown\status;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use core_courseformat\sectiondelegatemodule;
use pix_icon;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render a section visibility inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2024 Laurent David <laurent.david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class visibility implements named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    protected $section;

    /**
     * Constructor.
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass|null data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): ?stdClass {
        global $USER;

        if ($this->section->visible) {
            return null;
        }

        $context = context_course::instance($this->section->course);
        $data = new stdClass();

        if (!has_capability('moodle/course:sectionvisibility', $context, $USER)) {
            $data->notavailable = true;
            return $data;
        }

        $data->editing = $this->format->show_editor();

        if ($this->section->is_orphan()) {
            $data->editing = false;
        }

        $data->notavailable = false;
        $data->hiddenfromstudents = true;
        if ($data->editing && $this->is_section_visibility_editable()) {
            $data->dropwdown = $this->get_visibility_dropdown($output);
        } else {
            // The user is editing but cannot edit the visibility on this specific section,
            $data->editing = false;
        }
        return $data;
    }

    /**
     * Check if the section visibility is editable.
     *
     * @return bool
     */
    protected function is_section_visibility_editable(): bool {
        // Delegated section inside a hidden sections are not editable.
        $parentsection = $this->section->get_component_instance()?->get_parent_section();
        if ($parentsection && !$parentsection->visible) {
            return false;
        }
        return true;
    }

    /**
     * Get the section visibility dropdown.
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array
     */
    protected function get_visibility_dropdown(\renderer_base $output): array {
        $badgetext = $output->visually_hidden_text(get_string('availability'));
        $badgetext .= get_string('hiddenfromstudents');
        $icon = $this->get_icon('hide');

        $choice = new choicelist();
        $choice->add_option(
            'show',
            get_string('availability_show', 'core_courseformat'),
            $this->get_option_data('show', 'sectionShow', 'section_show')
        );
        $choice->add_option(
            'hide',
            get_string('availability_hide', 'core_courseformat'),
            $this->get_option_data('hide', 'sectionHide', 'section_hide')
        );
        $choice->set_selected_value('hide');

        $dropdown = new status(
            $output->render($icon) . ' ' . $badgetext,
            $choice,
            ['dialogwidth' => status::WIDTH['big']],
        );
        return $dropdown->export_for_template($output);
    }

    /**
     * Get the data for the option.
     *
     * @param string $name the name of the option
     * @param string $mutation the mutation name
     * @param string $stateaction the state action name
     * @return array
     */
    private function get_option_data(string $name, string $mutation, string $stateaction): array {
        $format = $this->format;
        $nonajaxurl = $format->get_update_url(
            action: $stateaction,
            ids: [$this->section->id],
            returnurl: $format->get_view_url($format->get_sectionnum(), ['navigation' => true]),
        );

        return [
            'description' => get_string("availability_{$name}_help", 'core_courseformat'),
            'icon' => $this->get_icon($name),
            'url' => $nonajaxurl,
            'extras' => [
                'data-id' => $this->section->id,
                'data-action' => $mutation,
            ],
        ];
    }

    /**
     * Get the icon for the section visibility.
     * @param string $selected the visibility selected value
     * @return pix_icon
     */
    protected function get_icon(string $selected): pix_icon {
        if ($selected === 'hide') {
            return new pix_icon('t/show', '');
        } else {
            return new pix_icon('t/hide', '');
        }
    }
}
