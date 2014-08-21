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
 * User button. Adapted from core_select_user_button.
 *
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_history\output;

defined('MOODLE_INTERNAL') || die;

/**
 * A button that is used to select users for a form.
 *
 * @since      Moodle 2.8
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_button extends \single_button implements \renderable {
    /**
     * Initialises the new select_user_button.
     *
     * @param \moodle_url $url
     * @param string $label The text to display in the button
     * @param string $method Either post or get
     */
    public function __construct(\moodle_url $url, $label, $method = 'post') {
        parent::__construct($url, $label, $method);
        $this->class = 'singlebutton selectusersbutton gradereport_history_plugin';
        $this->formid = \html_writer::random_id('selectusersbutton');
    }
}
