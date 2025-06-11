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

namespace core\output\actions;

use core\exception\coding_exception;
use moodle_url;

/**
 * Component action for a popup window.
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class popup_action extends component_action {
    /**
     * @var string The JS function to call for the popup
     */
    public $jsfunction = 'openpopup';

    /**
     * @var array An array of parameters that will be passed to the openpopup JS function
     */
    public $params = [
        'height' => 400,
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
        'dependent' => true,
    ];

    /**
     * Constructor
     *
     * @param string $event DOM event
     * @param moodle_url|string $url A moodle_url object, required if no jsfunction is given
     * @param string $name The JS function to call for the popup (default 'popup')
     * @param array  $params An array of popup parameters
     */
    public function __construct($event, $url, $name = 'popup', $params = []) {
        global $CFG;

        $url = new moodle_url($url);

        if ($name) {
            $checkname = $name;
            if (($checkname = preg_replace("/\s/", '_', $checkname)) != $name) {
                throw new coding_exception(
                    "The {$name} of a popup window shouldn't contain spaces - string modified. {$name} changed to {$checkname}",
                );
                $name = $checkname;
            }
        } else {
            $name = 'popup';
        }

        foreach ($this->params as $var => $val) {
            if (array_key_exists($var, $params)) {
                $this->params[$var] = $params[$var];
            }
        }

        $attributes = ['url' => $url->out(false), 'name' => $name, 'options' => $this->get_js_options($params)];
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
            } else if (is_bool($val)) {
                $jsoptions .= ($val) ? "$var," : "$var=0,";
            }
        }

        $jsoptions = substr($jsoptions, 0, strlen($jsoptions) - 1);

        return $jsoptions;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(popup_action::class, \popup_action::class);
