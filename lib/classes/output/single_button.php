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

use core\output\actions\confirm_action;
use core\output\actions\component_action;
use moodle_url;
use stdClass;

/**
 * Data structure representing a simple form with only one button.
 *
 * @copyright 2009 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class single_button implements renderable {
    /**
     * Possible button types. From boostrap.
     */
    const BUTTON_TYPES = [
        self::BUTTON_PRIMARY,
        self::BUTTON_SECONDARY,
        self::BUTTON_SUCCESS,
        self::BUTTON_DANGER,
        self::BUTTON_WARNING,
        self::BUTTON_INFO,
    ];

    /**
     * Possible button types - Primary.
     */
    const BUTTON_PRIMARY = 'primary';
    /**
     * Possible button types - Secondary.
     */
    const BUTTON_SECONDARY = 'secondary';
    /**
     * Possible button types - Danger.
     */
    const BUTTON_DANGER = 'danger';
    /**
     * Possible button types - Success.
     */
    const BUTTON_SUCCESS = 'success';
    /**
     * Possible button types - Warning.
     */
    const BUTTON_WARNING = 'warning';
    /**
     * Possible button types - Info.
     */
    const BUTTON_INFO = 'info';

    /**
     * @var moodle_url Target url
     */
    public $url;

    /**
     * @var string Button label
     */
    public $label;

    /**
     * @var string Form submit method post or get
     */
    public $method = 'post';

    /**
     * @var string Wrapping div class
     */
    public $class = 'singlebutton';

    /**
     * @var string Type of button (from defined types). Used for styling.
     */
    protected $type;

    /**
     * @var bool True if button is primary button. Used for styling.
     * @deprecated since Moodle 4.2
     */
    private $primary = false;

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
    public $formid;

    /**
     * @var array List of attached actions
     */
    public $actions = [];

    /**
     * @var array $params URL Params
     */
    public $params;

    /**
     * @var string Action id
     */
    public $actionid;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor
     *
     * @param moodle_url $url
     * @param string $label button text
     * @param string $method get or post submit method
     * @param string $type whether this is a primary button or another type, used for styling
     * @param array $attributes Attributes for the HTML button tag
     */
    public function __construct(
        moodle_url $url,
        $label,
        $method = 'post',
        $type = self::BUTTON_SECONDARY,
        $attributes = []
    ) {
        if (is_bool($type)) {
            debugging('The boolean $primary is deprecated and replaced by $type,
            use single_button::BUTTON_PRIMARY or self::BUTTON_SECONDARY instead');
            $type = $type ? self::BUTTON_PRIMARY : self::BUTTON_SECONDARY;
        }
        $this->url = clone($url);
        $this->label = $label;
        $this->method = $method;
        $this->type = $type;
        $this->attributes = $attributes;
    }

    /**
     * Shortcut for adding a JS confirm dialog when the button is clicked.
     * The message must be a yes/no question.
     *
     * @param string $confirmmessage The yes/no confirmation question. If "Yes" is clicked, the original action will occur.
     */
    public function add_confirm_action($confirmmessage) {
        $this->add_action(new confirm_action($confirmmessage));
    }

    /**
     * Add action to the button.
     * @param component_action $action
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    /**
     * Sets an attribute for the HTML button tag.
     *
     * @param  string $name  The attribute name
     * @param  mixed  $value The value
     * @return null
     */
    public function set_attribute($name, $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * Magic setter method.
     *
     * This method manages access to some properties and will display deprecation message when accessing 'primary' property.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        switch ($name) {
            case 'primary':
                debugging('The primary field is deprecated, use the type field instead');
                // Here just in case we modified the primary field from outside {@see \mod_quiz_renderer::summary_page_controls}.
                $this->type = $value ? self::BUTTON_PRIMARY : self::BUTTON_SECONDARY;
                break;
            case 'type':
                $this->type = in_array($value, self::BUTTON_TYPES) ? $value : self::BUTTON_SECONDARY;
                break;
            default:
                $this->$name = $value;
        }
    }

    /**
     * Magic method getter.
     *
     * This method manages access to some properties and will display deprecation message when accessing 'primary' property.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        switch ($name) {
            case 'primary':
                debugging('The primary field is deprecated, use type field instead');
                return $this->type == self::BUTTON_PRIMARY;
            case 'type':
                return $this->type;
            default:
                return $this->$name;
        }
    }

    /**
     * Export data.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $url = $this->method === 'get' ? $this->url->out_omit_querystring(true) : $this->url->out_omit_querystring();

        $data = new stdClass();
        $data->id = html_writer::random_id('single_button');
        $data->formid = $this->formid;
        $data->method = $this->method;
        $data->url = $url === '' ? '#' : $url;
        $data->label = $this->label;
        $data->classes = $this->class;
        $data->disabled = $this->disabled;
        $data->tooltip = $this->tooltip;
        $data->type = $this->type;
        $data->attributes = [];
        foreach ($this->attributes as $key => $value) {
            $data->attributes[] = ['name' => $key, 'value' => $value];
        }

        // Form parameters.
        $actionurl = new moodle_url($this->url);
        if ($this->method === 'post') {
            $actionurl->param('sesskey', sesskey());
        }
        $data->params = $actionurl->export_params_for_template();

        // Button actions.
        $actions = $this->actions;
        $data->actions = array_map(function ($action) use ($output) {
            return $action->export_for_template($output);
        }, $actions);
        $data->hasactions = !empty($data->actions);

        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(single_button::class, \single_button::class);
