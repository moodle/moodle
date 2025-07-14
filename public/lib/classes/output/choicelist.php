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
class choicelist implements named_templatable, renderable {
    /** @var object[] The user choices. */
    protected $options = [];

    /** @var string the selected option. */
    protected $selected = null;

    /** @var string the choice description. */
    protected $description = null;

    /** @var bool if the selected value can be empty. */
    protected $allowempty = null;

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
     * Get the selectable options.
     *
     * This method returns an array of options that are selectable, excluding the selected option and any disabled options.
     *
     * @return \stdClass[]
     */
    public function get_selectable_options(): array {
        $selectableoptions = [];
        foreach ($this->options as $option) {
            if ($option['value'] !== $this->selected && !$option['disabled']) {
                $selectableoptions[] = (object) $option;
            }
        }
        return $selectableoptions;
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
        if (empty($this->selected) && !$this->allowempty && !empty($this->options)) {
            return array_key_first($this->options);
        }
        return $this->selected;
    }

    /**
     * Set the allow empty option.
     * @param bool $allowempty Whether the selected value can be empty.
     */
    public function set_allow_empty(bool $allowempty) {
        $this->allowempty = $allowempty;
    }

    /**
     * Get the allow empty option.
     * @return bool Whether the selected value can be empty.
     */
    public function get_allow_empty(): bool {
        return $this->allowempty;
    }

    /**
     * Check if the value is in the options.
     * @param string $value The value to check.
     * @return bool
     */
    public function has_value(string $value): bool {
        return isset($this->options[$value]);
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
     * Sets the HTML attributes to the option.
     *
     * This method will remove any previous extra attributes.
     *
     * @param string $value The value of the option.
     * @param array $extras an array to add HTML attributes to the option (attribute => value).
     */
    public function set_option_extras(string $value, array $extras) {
        if (!isset($this->options[$value])) {
            return;
        }
        $this->options[$value]['extras'] = [];
        $this->add_option_extras($value, $extras);
    }

    /**
     * Add HTML attributes to the option.
     * @param string $value The value of the option.
     * @param array $extras an array to add HTML attributes to the option (attribute => value).
     */
    public function add_option_extras(string $value, array $extras) {
        if (!isset($this->options[$value])) {
            return;
        }
        if (!isset($this->options[$value]['extras'])) {
            $this->options[$value]['extras'] = [];
        }
        foreach ($extras as $attribute => $attributevalue) {
            $this->options[$value]['extras'][] = [
                'attribute' => $attribute,
                'value' => $attributevalue,
            ];
        }
    }

    /**
     * Retrieves the HTML attributes for a given value from the options array.

     * @param string $value The value for which to retrieve the extras.
     * @return array an array of HTML attributes of the option (attribute => value).
     */
    public function get_option_extras(string $value): array {
        if (!isset($this->options[$value]) || !isset($this->options[$value]['extras'])) {
            return [];
        }
        $result = [];
        foreach ($this->options[$value]['extras'] as $extra) {
            $result[$extra['attribute']] = $extra['value'];
        }
        return $result;
    }

    /**
     * Get the selected option HTML.
     *
     * This method is used to display the selected option and the option icon.
     *
     * @param renderer_base $output The renderer.
     * @return string
     */
    public function get_selected_content(renderer_base $output): string {
        if (empty($this->selected)) {
            return '';
        }
        $option = $this->options[$this->selected];
        $icon = '';
        if (!empty($option['icon'])) {
            $icon = $output->render($option['icon']);
        }
        return $icon . $option['name'];
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

            if ($option['value'] == $this->get_selected_value()) {
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
