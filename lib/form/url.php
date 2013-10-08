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
 * url type form element
 *
 * Contains HTML class for a url type element
 *
 * @package   core_form
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("HTML/QuickForm/text.php");

/**
 * url type form element
 *
 * HTML class for a url type element
 * @package   core_form
 * @category  form
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_url extends HTML_QuickForm_text{
    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel=false;

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     * @param array $options data which need to be posted.
     */
    function MoodleQuickForm_url($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG;
        require_once("$CFG->dirroot/repository/lib.php");
        $options = (array)$options;
        foreach ($options as $name=>$value) {
            $this->_options[$name] = $value;
        }
        if (!isset($this->_options['usefilepicker'])) {
            $this->_options['usefilepicker'] = true;
        }
        parent::HTML_QuickForm_text($elementName, $elementLabel, $attributes);
    }

    /**
     * Sets label to be hidden
     *
     * @param bool $hiddenLabel sets if label should be hidden
     */
    function setHiddenLabel($hiddenLabel){
        $this->_hiddenLabel = $hiddenLabel;
    }

    /**
     * Returns HTML for this form element.
     *
     * @return string
     */
    function toHtml(){
        global $PAGE, $OUTPUT;

        $id     = $this->_attributes['id'];
        $elname = $this->_attributes['name'];

        if ($this->_hiddenLabel) {
            $this->_generateId();
            $str = '<label class="accesshide" for="'.$this->getAttribute('id').'" >'.
                        $this->getLabel().'</label>'.parent::toHtml();
        } else {
            $str = parent::toHtml();
        }
        if (empty($this->_options['usefilepicker'])) {
            return $str;
        }

        $client_id = uniqid();

        $args = new stdClass();
        $args->accepted_types = '*';
        $args->return_types = FILE_EXTERNAL;
        $args->context = $PAGE->context;
        $args->client_id = $client_id;
        $args->env = 'url';
        $fp = new file_picker($args);
        $options = $fp->options;

        if (count($options->repositories) > 0) {
            $straddlink = get_string('choosealink', 'repository');
            $str .= <<<EOD
<button id="filepicker-button-{$client_id}" style="display:none">
$straddlink
</button>
EOD;
        }

        // print out file picker
        $str .= $OUTPUT->render($fp);

        $module = array('name'=>'form_url', 'fullpath'=>'/lib/form/url.js', 'requires'=>array('core_filepicker'));
        $PAGE->requires->js_init_call('M.form_url.init', array($options), true, $module);
        $PAGE->requires->js_function_call('show_item', array('filepicker-button-'.$client_id));

        return $str;
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * Slightly different container template when frozen. Don't want to use a label tag
     * with a for attribute in that case for the element label but instead use a div.
     * Templates are defined in renderer constructor.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'static';
        } else {
            return 'default';
        }
    }
}
