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

use core\exception\coding_exception;
use moodle_url;
use stdClass;

/**
 * Simple URL selection widget description.
 *
 * @copyright 2009 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class url_select implements renderable, templatable {
    /**
     * @var array $urls associative array value=>label ex.: array(1=>'One, 2=>Two)
     *     it is also possible to specify optgroup as complex label array ex.:
     *         array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *         array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     */
    public $urls;

    /**
     * @var string Selected option
     */
    public $selected;

    /**
     * @var array Nothing selected
     */
    public $nothing;

    /**
     * @var array Extra select field attributes
     */
    public $attributes = [];

    /**
     * @var string Button label
     */
    public $label = '';

    /**
     * @var array Button label's attributes
     */
    public $labelattributes = [];

    /**
     * @var string Wrapping div class
     */
    public $class = 'urlselect';

    /**
     * @var bool True if button disabled, false if normal
     */
    public $disabled = false;

    /**
     * @var string Button tooltip
     */
    public $tooltip = null;

    /**
     * @var string Form id
     */
    public $formid = null;

    /**
     * @var help_icon The help icon for this element.
     */
    public $helpicon = null;

    /**
     * @var string If set, makes button visible with given name for button
     */
    public $showbutton = null;

    /**
     * @var array $disabledoptions array of disabled options
     */
    public $disabledoptions = [];

    /**
     * Constructor
     * @param array $urls list of options
     * @param string $selected selected element
     * @param array $nothing
     * @param string $formid
     * @param string $showbutton Set to text of button if it should be visible
     *   or null if it should be hidden (hidden version always has text 'go')
     */
    public function __construct(array $urls, $selected = '', $nothing = ['' => 'choosedots'], $formid = null, $showbutton = null) {
        $this->urls       = $urls;
        $this->selected   = $selected;
        $this->nothing    = $nothing;
        $this->formid     = $formid;
        $this->showbutton = $showbutton;
        $this->disabledoptions = [];
    }

    /**
     * Disable the option(url).
     *
     * @param string $urlkey
     * @param bool $disabled
     */
    public function set_option_disabled(string $urlkey, bool $disabled = true) {
        $this->disabledoptions[$urlkey] = $disabled;
    }

    /**
     * Adds help icon.
     *
     * @param string $identifier The keyword that defines a help page
     * @param string $component
     */
    public function set_help_icon($identifier, $component = 'moodle') {
        $this->helpicon = new help_icon($identifier, $component);
    }

    /**
     * Sets select's label
     *
     * @param string $label
     * @param array $attributes (optional)
     */
    public function set_label($label, $attributes = []) {
        $this->label = $label;
        $this->labelattributes = $attributes;
    }

    /**
     * Clean a URL.
     *
     * @param string $value The URL.
     * @return string The cleaned URL.
     */
    protected function clean_url($value) {
        global $CFG;

        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
        if (empty($value)) {
            // Nothing.
        } else if (strpos($value, $CFG->wwwroot . '/') === 0) {
            $value = str_replace($CFG->wwwroot, '', $value);
        } else if (strpos($value, '/') !== 0) {
            debugging("Invalid url_select urls parameter: url '$value' is not local relative url!", DEBUG_DEVELOPER);
        }

        return $value;
    }

    /**
     * Flatten the options for Mustache.
     *
     * This also cleans the URLs.
     *
     * @param array $options The options.
     * @param array $nothing The nothing option.
     * @return array
     */
    protected function flatten_options($options, $nothing) {
        $flattened = [];

        foreach ($options as $value => $option) {
            if (is_array($option)) {
                foreach ($option as $groupname => $optoptions) {
                    if (!isset($flattened[$groupname])) {
                        $flattened[$groupname] = [
                            'name' => $groupname,
                            'isgroup' => true,
                            'options' => [],
                        ];
                    }
                    foreach ($optoptions as $optvalue => $optoption) {
                        $cleanedvalue = $this->clean_url($optvalue);
                        $flattened[$groupname]['options'][$cleanedvalue] = [
                            'name' => $optoption,
                            'value' => $cleanedvalue,
                            'selected' => $this->selected == $optvalue,
                            'disabled' => $this->disabledoptions[$optvalue] ?? false,
                        ];
                    }
                }
            } else {
                $cleanedvalue = $this->clean_url($value);
                $flattened[$cleanedvalue] = [
                    'name' => $option,
                    'value' => $cleanedvalue,
                    'selected' => $this->selected == $value,
                    'disabled' => $this->disabledoptions[$value] ?? false,
                ];
            }
        }

        if (!empty($nothing)) {
            $value = key($nothing);
            $name = reset($nothing);
            $flattened = [
                    $value => ['name' => $name, 'value' => $value, 'selected' => $this->selected == $value],
                ] + $flattened;
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
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $attributes = $this->attributes;

        $data = new stdClass();
        $data->formid = !empty($this->formid) ? $this->formid : html_writer::random_id('url_select_f');
        $data->classes = $this->class;
        $data->label = $this->label;
        $data->disabled = $this->disabled;
        $data->title = $this->tooltip;
        $data->id = !empty($attributes['id']) ? $attributes['id'] : html_writer::random_id('url_select');
        $data->sesskey = sesskey();
        $data->action = (new moodle_url('/course/jumpto.php'))->out(false);

        // Remove attributes passed as property directly.
        unset($attributes['class']);
        unset($attributes['id']);
        unset($attributes['name']);
        unset($attributes['title']);
        unset($attributes['disabled']);

        $data->showbutton = $this->showbutton;

        // Select options.
        $nothing = false;
        if (is_string($this->nothing) && $this->nothing !== '') {
            $nothing = ['' => $this->nothing];
        } else if (is_array($this->nothing)) {
            $nothingvalue = reset($this->nothing);
            if ($nothingvalue === 'choose' || $nothingvalue === 'choosedots') {
                $nothing = [key($this->nothing) => get_string('choosedots')];
            } else {
                $nothing = $this->nothing;
            }
        }
        $data->options = $this->flatten_options($this->urls, $nothing);

        // Label attributes.
        $data->labelattributes = [];
        // Unset label attributes that are already in the template.
        unset($this->labelattributes['for']);
        // Map the label attributes.
        foreach ($this->labelattributes as $key => $value) {
            $data->labelattributes[] = ['name' => $key, 'value' => $value];
        }

        // Help icon.
        $data->helpicon = !empty($this->helpicon) ? $this->helpicon->export_for_template($output) : false;

        // Finally all the remaining attributes.
        $data->attributes = [];
        foreach ($attributes as $key => $value) {
            $data->attributes[] = ['name' => $key, 'value' => $value];
        }

        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(url_select::class, \url_select::class);
