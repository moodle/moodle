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
 * Classes representing JS event handlers, used by output components.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class used by other components that involve an action on the page (URL or JS).
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class component_action {

    /**
     * The DOM event that will trigger this action when caught
     * @var string $event DOM event
     */
    public $event;

    /**
     * The JS function you create must have two arguments:
     *      1. The event object
     *      2. An object/array of arguments ($jsfunctionargs)
     * @var string $jsfunction A function name to call when the button is clicked
     */
    public $jsfunction = false;

    /**
     * @var array $jsfunctionargs An array of arguments to pass to the JS function
     */
    public $jsfunctionargs = array();

    /**
     * Constructor
     * @param string $event DOM event
     * @param moodle_url $url A moodle_url object, required if no jsfunction is given
     * @param string $method 'post' or 'get'
     * @param string $jsfunction An optional JS function. Required if jsfunctionargs is given
     * @param array  $jsfunctionargs An array of arguments to pass to the jsfunction
     * @return void
     */
    public function __construct($event, $jsfunction, $jsfunctionargs=array()) {
        $this->event = $event;

        $this->jsfunction = $jsfunction;
        $this->jsfunctionargs = $jsfunctionargs;

        if (!empty($this->jsfunctionargs)) {
            if (empty($this->jsfunction)) {
                throw new coding_exception('The component_action object needs a jsfunction value to pass the jsfunctionargs to.');
            }
        }
    }
}


/**
 * Confirm action
 */
class confirm_action extends component_action {
    public function __construct($message, $callback = null, $continuelabel = null, $cancellabel = null) {
        parent::__construct('click', 'M.util.show_confirm_dialog', array(
                'message' => $message, 'callback' => $callback,
                'continuelabel' => $continuelabel, 'cancellabel' => $cancellabel));
    }
}


/**
 * Component action for a popup window.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class popup_action extends component_action {

    public $jsfunction = 'openpopup';

    /**
     * @var array $params An array of parameters that will be passed to the openpopup JS function
     */
    public $params = array(
            'height' =>  400,
            'width' => 500,
            'top' => 0,
            'left' => 0,
            'menubar' => false,
            'location' => false,
            'scrollbars' => true,
            'resizable' => true,
            'toolbar' => true,
            'status' => true,
            'directories' => false,
            'fullscreen' => false,
            'dependent' => true);

    /**
     * Constructor
     * @param string $event DOM event
     * @param moodle_url|string $url A moodle_url object, required if no jsfunction is given
     * @param string $method 'post' or 'get'
     * @param array  $params An array of popup parameters
     * @return void
     */
    public function __construct($event, $url, $name='popup', $params=array()) {
        global $CFG;
        $this->name = $name;

        $url = new moodle_url($url);

        if ($this->name) {
            $_name = $this->name;
            if (($_name = preg_replace("/\s/", '_', $_name)) != $this->name) {
                throw new coding_exception('The $name of a popup window shouldn\'t contain spaces - string modified. '. $this->name .' changed to '. $_name);
                $this->name = $_name;
            }
        } else {
            $this->name = 'popup';
        }

        foreach ($this->params as $var => $val) {
            if (array_key_exists($var, $params)) {
                $this->params[$var] = $params[$var];
            }
        }

        $attributes = array('url' => $url->out(false), 'name' => $name, 'options' => $this->get_js_options($params));
        if (!empty($params['fullscreen'])) {
            $attributes['fullscreen'] = 1;
        }
        parent::__construct($event, $this->jsfunction, $attributes);
    }

    /**
     * Returns a string of concatenated option->value pairs used by JS to call the popup window,
     * based on this object's variables
     *
     * @return string String of option->value pairs for JS popup function.
     */
    public function get_js_options() {
        $jsoptions = '';

        foreach ($this->params as $var => $val) {
            if (is_string($val) || is_int($val)) {
                $jsoptions .= "$var=$val,";
            } elseif (is_bool($val)) {
                $jsoptions .= ($val) ? "$var," : "$var=0,";
            }
        }

        $jsoptions = substr($jsoptions, 0, strlen($jsoptions) - 1);

        return $jsoptions;
    }
}