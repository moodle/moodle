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

declare(strict_types=1);

namespace core\output;

/**
 * A single-select combobox widget that is functionally similar to an HTML select element.
 *
 * @package   core
 * @category  output
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select_menu implements renderable, templatable {
    /** @var array List of options. */
    protected $options;

    /** @var string|null The value of the preselected option. */
    protected $selected;

    /** @var string The combobox label */
    protected $label;

    /** @var array Button label's attributes */
    protected $labelattributes = [];

    /** @var bool Whether the label is inline or not */
    protected $inlinelabel = false;

    /** @var string Name of the combobox element */
    protected $name;

    /**
     * select_menu constructor.
     *
     * @param string $name Name of the combobox element
     * @param array $options List of options in an associative array format like ['val' => 'Option'].
     *                       Supports grouped options as well. Empty string or null values will be rendered as dividers.
     * @param string|null $selected The value of the preselected option.
     */
    public function __construct(string $name, array $options, ?string $selected = null) {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
    }

    /**
     * Sets the select menu's label.
     *
     * @param string $label The label.
     * @param array $attributes List of attributes to apply on the label element.
     * @param bool $inlinelabel Whether the label is inline or not.
     */
    public function set_label(string $label, array $attributes = [], bool $inlinelabel = false) {
        $this->label = $label;
        $this->labelattributes = $attributes;
        $this->inlinelabel = $inlinelabel;
    }

    /**
     * Flatten the options for Mustache.
     *
     * @return array
     */
    protected function flatten_options(): array {
        $flattened = [];

        foreach ($this->options as $value => $option) {
            if (is_array($option)) {
                foreach ($option as $groupname => $optoptions) {
                    if (!isset($flattened[$groupname])) {
                        $flattened[$groupname] = [
                            'name' => $groupname,
                            'isgroup' => true,
                            'id' => \html_writer::random_id('select-menu-group'),
                            'options' => [],
                        ];
                    }
                    foreach ($optoptions as $optvalue => $optoption) {
                        if (empty($optoption)) {
                            $flattened[$groupname]['options'][$optvalue] = ['isdivider' => true];
                        } else {
                            $flattened[$groupname]['options'][$optvalue] = [
                                'name' => $optoption,
                                'value' => $optvalue,
                                'selected' => $this->selected == $optvalue,
                                'id' => \html_writer::random_id('select-menu-option'),
                            ];
                        }
                    }
                }
            } else {
                if (empty($option)) {
                    $flattened[$value] = ['isdivider' => true];
                } else {
                    $flattened[$value] = [
                        'name' => $option,
                        'value' => $value,
                        'selected' => $this->selected == $value,
                        'id' => \html_writer::random_id('select-menu-option'),
                    ];
                }
            }
        }

        // Make non-associative array.
        foreach ($flattened as $key => $value) {
            if (!empty($value['options'])) {
                $flattened[$key]['options'] = array_values($value['options']);
            }
        }
        $flattened = array_values($flattened);

        return $flattened;
    }

    /**
     * Return the name of the selected option.
     *
     * @return string|null The name of the selected option or null.
     */
    private function get_selected_option(): ?string {
        foreach ($this->options as $value => $option) {
            if (is_array($option)) {  // This is a group.
                foreach ($option as $groupname => $optoptions) {
                    // Loop through the options within the group to check whether any of them matches the 'selected' value.
                    foreach ($optoptions as $optvalue => $optoption) {
                        // If the value of the option matches the 'selected' value, return the name of the option.
                        if ($this->selected == $optvalue) {
                            return $optoption;
                        }
                    }
                }
            } else { // This is a standard option item.
                // If the value of the option matches the 'selected' value, return the name of the option.
                if ($this->selected == $value) {
                    return $option;
                }
            }
        }
        return null;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output): \stdClass {
        $data = new \stdClass();
        $data->baseid = \html_writer::random_id('select-menu');
        $data->label = $this->label;
        $data->inlinelabel = $this->inlinelabel;
        $data->options = $this->flatten_options();
        $data->selectedoption = $this->get_selected_option();
        $data->name = $this->name;
        $data->value = $this->selected;

        // Label attributes.
        $data->labelattributes = [];
        // Map the label attributes.
        foreach ($this->labelattributes as $key => $value) {
            $data->labelattributes[] = ['name' => $key, 'value' => $value];
        }

        return $data;
    }
}
