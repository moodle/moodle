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
 * HTML class for a searchable select type element
 *
 * @package   formlib
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('select.php');

/**
 * Display a select input with a search textfield input on the top
 * The search textfield is created by the javascript file searchselector.js
 * (so when javascript is not activated into the browser, the search field is not displayed)
 * If ever the select can be reset/unselect/blank/nooption, you will have to add an option "noselected"
 * and manage this special case when you get/set the form data (i.e. $mform->get_data()/$this->set_data($yourobject)).
 */
class MoodleQuickForm_searchableselector extends MoodleQuickForm_select{


    function MoodleQuickForm_searchableselector($elementName=null, $elementLabel=null, $options=null, $attributes=null) {
        //set size default to 12
        if (empty($attributes) || empty($attributes['size'])) {
            $attributes['size'] = 12;
        }
        parent::MoodleQuickForm_select($elementName, $elementLabel, $options, $attributes);
    }

    function toHtml(){
        global $OUTPUT;
        if ($this->_hiddenLabel){
            return parent::toHtml();
        } else {
            // Javascript for the search/selection fields
            global $PAGE;
            $PAGE->requires->yui2_lib('event');
            $PAGE->requires->js('/lib/form/searchableselector.js');
            $PAGE->requires->js_function_call('selector.filter_init', array(get_string('search'),$this->getAttribute('id')));

            $strHtml = '';
            $strHtml .= parent::toHtml(); //the select input
            return $strHtml;
        }
    }

}
