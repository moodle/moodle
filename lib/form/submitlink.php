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
 * submit link type form element
 *
 * Contains HTML class for a submitting to link
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once("$CFG->libdir/form/submit.php");
/**
 * submit link type form element
 *
 * HTML class for a submitting to link
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_submitlink extends MoodleQuickForm_submit {
    /** @var string javascript for submitting element's data */
    var $_js;

    /** @var string callback function which will be called onclick event */
    var $_onclick;

    /**
     * constructor
     *
     * @param string $elementName (optional) name of the field
     * @param string $value (optional) field label
     * @param string $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementName=null, $value=null, $attributes=null) {
        parent::__construct($elementName, $value, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_submitlink($elementName=null, $value=null, $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $value, $attributes);
    }

    /**
     * Returns HTML for submitlink form element.
     *
     * @return string
     */
    function toHtml() {
        $text = $this->_attributes['value'];

        return "<noscript><div>" . parent::toHtml() . '</div></noscript><script type="text/javascript">' . $this->_js . "\n"
             . 'document.write(\'<a href="#" onclick="' . $this->_onclick . '">'
             . $text . "</a>');\n</script>";
    }
}
