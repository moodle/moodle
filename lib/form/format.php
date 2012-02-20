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
 * editor format form element
 *
 * Contains HTML class for a editor format drop down element
 *
 * @package    core_form
 * @copyright  2007 Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once "$CFG->libdir/form/select.php";

/**
 * editor format form element
 *
 * HTML class for a editor format drop down element
 *
 * @package    core_form
 * @copyright  2007 Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 2.0 Please do not use this form element.
 * @todo       MDL-31294, remove this element
 * @see        MoodleQuickForm_editor
 */
class MoodleQuickForm_format extends MoodleQuickForm_select{

    /**
     * Class constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     * @param mixed $useHtmlEditor Either a string returned from can_use_html_editor() or false for no html editor
     *              default 'detect' tells element to use html editor if it is available.
     */
    function MoodleQuickForm_format($elementName=null, $elementLabel=null, $attributes=null, $useHtmlEditor=null)
    {
        throw new coding_exception('MFORMS: Coding error, text formats are handled only by new editor element.');
    } //end constructor

}
