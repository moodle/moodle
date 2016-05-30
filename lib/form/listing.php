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
 * Listing form element.
 *
 * Contains HTML class for a listing form element.
 *
 * @package   core_form
 * @copyright 2012 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once("HTML/QuickForm/input.php");

/**
* The listing element is a simple customizable "select" without the input type=select.
* One main div contains the "large" html of an item.
* A show/hide div shows a hidden div containing the list of all items.
* This list is composed by the "small" html of each item.
*
* How to use it:
* The options parameter is an array containing:
*   - items => array of object: the key is the value of the form input
*                               $item->rowhtml => small html
*                               $item->mainhtml => large html
*   - showall/hideall => string for the Show/Hide button
*
* WARNINGS: The form lets you display HTML. So it is subject to CROSS-SCRIPTING if you send it uncleaned HTML.
*           Don't forget to escape your HTML as soon as one string comes from an input/external source.
*
* How to customize it:
*   You can change the css in core.css. For example if you remove float:left; from .formlistingrow,
*   then the item list is not display as tabs but as rows.
*
* @package   core_form
* @copyright 2012 Jerome Mouneyrac
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class MoodleQuickForm_listing extends HTML_QuickForm_input {

    /** @var array items to display. */
    protected $items = array();

    /** @var string language string for Show All. */
    protected $showall;

    /** @var string language string for Hide. */
    protected $hideall;

    /**
     * Constructor.
     *
     * @param string $elementName (optional) name of the listing.
     * @param string $elementLabel (optional) listing label.
     * @param array $attributes (optional) Either a typical HTML attribute string or an associative array.
     * @param array $options set of options to initalize listing.
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null, $options=array()) {

       $this->_type = 'listing';
        if (!empty($options['items'])) {
            $this->items = $options['items'];
        }
        if (!empty($options['showall'])) {
            $this->showall = $options['showall'];
        } else {
            $this->showall = get_string('showall');
        }
        if (!empty($options['hideall'])) {
            $this->hideall = $options['hideall'];
        } else {
            $this->hideall = get_string('hide');
        }
        parent::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_listing($elementName=null, $elementLabel=null, $attributes=null, $options=array()) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes, $options);
    }

    /**
     * Returns HTML for listing form element.
     *
     * @return string the HTML.
     */
    function toHtml() {
        global $CFG, $PAGE;

        $mainhtml = html_writer::tag('div', $this->items[$this->getValue()]->mainhtml,
                array('id' => $this->getName().'_items_main', 'class' => 'formlistingmain'));

        // Add the main div containing the selected item (+ the caption: "More items").
        $html = html_writer::tag('div', $mainhtml .
                    html_writer::tag('div', $this->showall,
                        array('id' => $this->getName().'_items_caption', 'class' => 'formlistingmore')),
                    array('id'=>$this->getName().'_items', 'class' => 'formlisting hide'));

        // Add collapsible region: all the items.
        $itemrows = '';
        $html .= html_writer::tag('div', $itemrows,
                array('id' => $this->getName().'_items_all', 'class' => 'formlistingall'));

        // Add radio buttons for non javascript support.
        $radiobuttons = '';
        foreach ($this->items as $itemid => $item) {
            $radioparams = array('name' => $this->getName(), 'value' => $itemid,
                    'id' => 'id_'.$itemid, 'class' => 'formlistinginputradio', 'type' => 'radio');
            if ($itemid == $this->getValue()) {
                $radioparams['checked'] = 'checked';
            }
            $radiobuttons .= html_writer::tag('div', html_writer::tag('input',
                html_writer::tag('div', $item->rowhtml, array('class' => 'formlistingradiocontent')), $radioparams),
                array('class' => 'formlistingradio'));
        }

        // Container for the hidden hidden input which will contain the selected item.
        $html .= html_writer::tag('div', $radiobuttons,
                array('id' => 'formlistinginputcontainer_' . $this->getName(), 'class' => 'formlistinginputcontainer'));

        $module = array('name'=>'form_listing', 'fullpath'=>'/lib/form/yui/listing/listing.js',
            'requires'=>array('node', 'event', 'transition', 'escape'));

        $PAGE->requires->js_init_call('M.form_listing.init',
                 array(array(
                'elementid' => $this->getName().'_items',
                'hideall' => $this->hideall,
                'showall' => $this->showall,
                'hiddeninputid' => $this->getAttribute('id'),
                'items' => $this->items,
                'inputname' => $this->getName(),
                'currentvalue' => $this->getValue())), true, $module);

        return $html;
    }
}
