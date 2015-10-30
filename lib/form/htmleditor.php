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
 * htmleditor type form element
 *
 * Contains HTML class for htmleditor type element
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once("$CFG->libdir/form/textarea.php");

/**
 * htmleditor type form element
 *
 * HTML class for htmleditor type element
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_htmleditor extends MoodleQuickForm_textarea{
    /** @var string defines the type of editor */
    var $_type;

    /** @var array default options for html editor, which can be overridden */
    var $_options=array('rows'=>10, 'cols'=>45, 'width'=>0,'height'=>0);

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the html editor
     * @param string $elementLabel (optional) editor label
     * @param array $options set of options to create html editor
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     */
    function MoodleQuickForm_htmleditor($elementName=null, $elementLabel=null, $options=array(), $attributes=null){
        parent::MoodleQuickForm_textarea($elementName, $elementLabel, $attributes);
        // set the options, do not bother setting bogus ones
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (array_key_exists($name, $this->_options)) {
                    if (is_array($value) && is_array($this->_options[$name])) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
        $this->_type='htmleditor';

        editors_head_setup();
    }

    /**
     * Returns the input field in HTML
     *
     * @return string
     */
    function toHtml(){
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            return $this->_getTabs() .
                    print_textarea(true,
                                    $this->_options['rows'],
                                    $this->_options['cols'],
                                    $this->_options['width'],
                                    $this->_options['height'],
                                    $this->getName(),
                                    preg_replace("/(\r\n|\n|\r)/", '&#010;',$this->getValue()),
                                    0, // unused anymore
                                    true,
                                    $this->getAttribute('id'));
        }
    }

    /**
     * What to display when element is frozen.
     *
     * @return string
     */
    function getFrozenHtml()
    {
        $html = format_text($this->getValue());
        return $html . $this->_getPersistantData();
    }
}
