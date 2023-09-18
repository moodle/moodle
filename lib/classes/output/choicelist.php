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

namespace core\output;

use renderable;
use renderer_base;
use core\output\named_templatable;

/**
 * A generic user choice output class.
 *
 * This class can be used as a generic user choice data structure for any dropdown,  modal, or any
 * other component that offers choices to the user.
 *
 * @package    core
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class choicelist implements renderable, named_templatable {

    /** @var object[] The user choices. */
    protected $options = [];

    /** @var string the selected option. */
    protected $selected = null;

    /** @var string the choice description. */
    protected $description = null;

    /**
     * Constructor.
     *
     * @param string $description the choice description.
     */
    public function __construct(?string $description = null) {
        $this->description = $description;
    }

    /**
     * Add option to the user choice.
     *
     * The definition object could contain the following keys:
     * - string description: the description of the option.
     * - \moodle_url url: the URL to link to.
     * - \pix_icon icon: the icon to display.
     * - bool disabled: whether the option is disabled.
     * - bool selected: whether the option is selected.
     * - array extras: an array of HTML attributes to add to the option (attribute => value).
     *
     * @param string $value
     * @param string $name
     * @param array $definition an optional array of definition for the option.
     */
    public function add_option(string $value, string $name, array $definition = []) {
        $option = [
            'value' => $value,
            'name' => $name,
            'description' => $definition['description'] ?? null,
            'url' => $definition['url'] ?? null,
            'icon' => $definition['icon'] ?? null,
            'disabled' => (!empty($definition['disabled'])) ? true : false,
        ];
        if (!empty($definition['selected'])) {
            $this->selected = $value;
        }
        $this->options[$value] = $option;
        if (isset($definition['extras'])) {
            $this->set_option_extras($value, $definition['extras']);
        }
    }

    /**
     * Get the number of options added to the choice list.
     * @return int
     */
    public function count_options(): int {
        return count($this->options);
    }

    /**
     * Set the selected option.
     *
     * @param string $value The value of the selected option.
     */
    public function set_selected_value(string $value) {
        $this->selected = $value;
    }

    /**
     * Get the selected option.
     *
     * @return string|null The value of the selected option.
     */
    public function get_selected_value(): ?string {
        return $this->selected;
    }

    /**
     * Set the general choice description option.
     *
     * @param string $value the new description.
     */
    public function set_description(string $value) {
        $this->description = $value;
    }

    /**
     * Get the choice description option.
     *
     * @return string|null the current description.
     */
    public function get_description(): ?string {
        return $this->description;
    }

    /**
     * Set the option disabled.
     *
     * @param string $value The value of the option.
     * @param bool $disabled Whether the option is disabled.
     */
    public function set_option_disabled(string $value, bool $disabled) {
        if (isset($this->options[$value])) {
            $this->options[$value]['disabled'] = $disabled;
        }
    }

    /**
     * Set the option disabled.
     *
     * @param string $value The value of the option.
     * @param array $extras an array to add HTML attributes to the option (attribute => value).
     */
    public function set_option_extras(string $value, array $extras) {
        if (!isset($this->options[$value])) {
            return;
        }
        $extrasattributes = [];
        foreach ($extras as $attribute => $attributevalue) {
            $extrasattributes[] = [
                'attribute' => $attribute,
                'value' => $attributevalue,
            ];
        }
        $this->options[$value]['extras'] = $extrasattributes;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $options = [];
        foreach ($this->options as $option) {
            if (!empty($option['icon'])) {
                $option['icon'] = $option['icon']->export_for_pix($output);
            }
            $option['hasicon'] = !empty($option['icon']);

            if (!empty($option['url'])) {
                $option['url'] = $option['url']->out(true);
            }
            $option['hasurl'] = !empty($option['url']);

            if ($option['value'] == $this->selected) {
                $option['selected'] = true;
            }

            $option['optionnumber'] = count($options) + 1;
            $option['first'] = count($options) === 0;
            $option['optionuniqid'] = \html_writer::random_id('choice_option_');

            $options[] = $option;
        }
        return [
            'description' => $this->description,
            'options' => $options,
            'hasoptions' => !empty($options),
        ];
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param renderer_base $renderer The renderer requesting the template name
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'core/local/choicelist';
    }
}
