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
use core\output\actions\component_action;
use moodle_url;
use stdClass;

/**
 * Simple form with just one select field that gets submitted automatically.
 *
 * If JS not enabled small go button is printed too.
 *
 * @copyright 2009 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class single_select implements renderable, templatable {
    /**
     * @var moodle_url Target url - includes hidden fields
     */
    public $url;

    /**
     * @var string Name of the select element.
     */
    public $name;

    /**
     * @var array $options associative array value=>label ex.: array(1=>'One, 2=>Two)
     *     it is also possible to specify optgroup as complex label array ex.:
     *         array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *         array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     */
    public $options;

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
     * @var string Form submit method post or get
     */
    public $method = 'get';

    /**
     * @var string Wrapping div class
     */
    public $class = 'singleselect';

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

    /** @var component_action[] component action. */
    public $actions = [];

    /**
     * Constructor
     * @param moodle_url $url form action target, includes hidden fields
     * @param string $name name of selection field - the changing parameter in url
     * @param array $options list of options
     * @param string $selected selected element
     * @param ?array $nothing
     * @param string $formid
     */
    public function __construct(
        moodle_url $url,
        $name,
        array $options,
        $selected = '',
        $nothing = ['' => 'choosedots'],
        $formid = null,
    ) {
        $this->url      = $url;
        $this->name     = $name;
        $this->options  = $options;
        $this->selected = $selected;
        $this->nothing  = $nothing;
        $this->formid   = $formid;
    }

    /**
     * Shortcut for adding a JS confirm dialog when the button is clicked.
     * The message must be a yes/no question.
     *
     * @param string $confirmmessage The yes/no confirmation question. If "Yes" is clicked, the original action will occur.
     */
    public function add_confirm_action($confirmmessage) {
        $this->add_action(new component_action('submit', 'M.util.show_confirm_dialog', ['message' => $confirmmessage]));
    }

    /**
     * Add action to the button.
     *
     * @param component_action $action
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    /**
     * Adds help icon.
     *
     * @deprecated since Moodle 2.0
     */
    public function set_old_help_icon($helppage, $title, $component = 'moodle') {
        throw new coding_exception('set_old_help_icon() can not be used any more, please see set_help_icon().');
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
     * Export data.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $attributes = $this->attributes;

        $data = new stdClass();
        $data->name = $this->name;
        $data->method = $this->method;
        $data->action = $this->method === 'get' ? $this->url->out_omit_querystring(true) : $this->url->out_omit_querystring();
        $data->classes = $this->class;
        $data->label = $this->label;
        $data->disabled = $this->disabled;
        $data->title = $this->tooltip;
        $data->formid = !empty($this->formid) ? $this->formid : html_writer::random_id('single_select_f');
        $data->id = !empty($attributes['id']) ? $attributes['id'] : html_writer::random_id('single_select');

        // Select element attributes.
        // Unset attributes that are already predefined in the template.
        unset($attributes['id']);
        unset($attributes['class']);
        unset($attributes['name']);
        unset($attributes['title']);
        unset($attributes['disabled']);

        // Map the attributes.
        $data->attributes = array_map(function ($key) use ($attributes) {
            return ['name' => $key, 'value' => $attributes[$key]];
        }, array_keys($attributes));

        // Form parameters.
        $actionurl = new moodle_url($this->url);
        if ($this->method === 'post') {
            $actionurl->param('sesskey', sesskey());
        }
        $data->params = $actionurl->export_params_for_template();

        // Select options.
        $hasnothing = false;
        if (is_string($this->nothing) && $this->nothing !== '') {
            $nothing = ['' => $this->nothing];
            $hasnothing = true;
            $nothingkey = '';
        } else if (is_array($this->nothing)) {
            $nothingvalue = reset($this->nothing);
            if ($nothingvalue === 'choose' || $nothingvalue === 'choosedots') {
                $nothing = [key($this->nothing) => get_string('choosedots')];
            } else {
                $nothing = $this->nothing;
            }
            $hasnothing = true;
            $nothingkey = key($this->nothing);
        }
        if ($hasnothing) {
            $options = $nothing + $this->options;
        } else {
            $options = $this->options;
        }

        foreach ($options as $value => $name) {
            if (is_array($options[$value])) {
                foreach ($options[$value] as $optgroupname => $optgroupvalues) {
                    $sublist = [];
                    foreach ($optgroupvalues as $optvalue => $optname) {
                        $option = [
                            'value' => $optvalue,
                            'name' => $optname,
                            'selected' => strval($this->selected) === strval($optvalue),
                        ];

                        if ($hasnothing && $nothingkey === $optvalue) {
                            $option['ignore'] = 'data-ignore';
                        }

                        $sublist[] = $option;
                    }
                    $data->options[] = [
                        'name' => $optgroupname,
                        'optgroup' => true,
                        'options' => $sublist,
                    ];
                }
            } else {
                $option = [
                    'value' => $value,
                    'name' => $options[$value],
                    'selected' => strval($this->selected) === strval($value),
                    'optgroup' => false,
                ];

                if ($hasnothing && $nothingkey === $value) {
                    $option['ignore'] = 'data-ignore';
                }

                $data->options[] = $option;
            }
        }

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

        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(single_select::class, \single_select::class);
