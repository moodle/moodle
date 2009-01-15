<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option)  later version.                                      //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Formslib field type for editing tags, both official and peronal.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package formslib
 *//* **/
global $CFG;
require_once($CFG->libdir . '/form/group.php');

/**
 * Formslib field type for editing tags.
 */
class MoodleQuickForm_tags extends MoodleQuickForm_group {
    /** Inidcates that the user should be the usual interface, with the official
     * tags listed seprately, and a text box where they can type anything.
     * @var integer */
    const DEFAULTUI = 0;
    /** Indicates that the user should only be allowed to select official tags.
     * @var integer */
    const ONLYOFFICIAL = 1;
    /** Indicates that the user should just be given a text box to type in (they
     * can still type official tags though.
     * @var integer */
    const NOOFFICIAL = 2;

    /**
     * Control the fieldnames for form elements
     *
     * display => integer, one of the constants above.
     */
    var $_options = array('display' => MoodleQuickForm_tags::DEFAULTUI);

   /**
    * These complement separators, they are appended to the resultant HTML
    * @access   private
    * @var      array
    */
    var $_wrap = array('', '');

    /**
    * Constructor
    *
    * @param string $elementName Element name
    * @param mixed $elementLabel  Label(s) for an element
    * @param array $options Options to control the element's display
    * @param mixed $attributes Either a typical HTML attribute string or an associative array.
    */
    function MoodleQuickForm_tags($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'tags';
        // set the options, do not bother setting bogus ones
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (isset($this->_options[$name])) {
                    if (is_array($value) && is_array($this->_options[$name])) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
    }

    function _createElements() {
        global $CFG, $DB;
        $this->_elements = array();

        // Official tags.
        if ($this->_options['display'] != MoodleQuickForm_tags::NOOFFICIAL) {
            // If the user can manage official tags, give them a link to manage them.
            $label = get_string('otags', 'tag');
            if (has_capability('moodle/tag:manage', get_context_instance(CONTEXT_SYSTEM))) {
                $label .= ' (' . link_to_popup_window($CFG->wwwroot .'/tag/manage.php',
                        'managetags', get_string('manageofficialtags', 'tag'), '', '', get_string('newwindow'), null, true) . ')';
            }

            // Get the list of official tags.
            $noofficial = false;
            $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';
            $officialtags = $DB->get_records_sql_menu("SELECT id, $namefield FROM {tag} WHERE tagtype='official' ORDER by $namefield ASC");
            if (empty($officialtags)) {
                $officialtags = array('' => get_string('none'));
                $noofficial = true;
            } else {
                $officialtags = array_combine($officialtags, $officialtags);
            }

            // Create the element.
            $size = min(5, count($officialtags));
            $officialtagsselect = MoodleQuickForm::createElement('select', 'officialtags', $label, $officialtags, array('size' => $size));
            $officialtagsselect->setMultiple(true);
            if ($noofficial) {
                $officialtagsselect->updateAttributes(array('disabled' => 'disabled'));
            }

            // 
            $this->_elements[] = $officialtagsselect;
        }

        // Other tags.
        if ($this->_options['display'] != MoodleQuickForm_tags::ONLYOFFICIAL) {
            $othertags = MoodleQuickForm::createElement('textarea', 'othertags', get_string('othertags', 'tag'), array('cols'=>'40', 'rows'=>'5'));
            $this->_elements[] = $othertags;
        }

        // Paradoxically, the only way to get labels output is to ask for 'hidden'
        // labels, and then override the .accesshide class in the CSS!
        foreach ($this->_elements as $element){
            if (method_exists($element, 'setHiddenLabel')){
                $element->setHiddenLabel(true);
            }
        }
    }

    function toHtml() {
        require_once('HTML/QuickForm/Renderer/Default.php');
        $renderer =& new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);
        return $this->_wrap[0] . $renderer->toHtml() . $this->_wrap[1];
    }

    function exportValue(&$submitValues, $assoc = false) {
        $valuearray = array();

        // Get the data out of our child elements.
        foreach ($this->_elements as $element){
            $thisexport = $element->exportValue($submitValues[$this->getName()], true);
            if ($thisexport != null){
                $valuearray += $thisexport;
            }
        }

        // Get any manually typed tags.
        $tags = array();
        if ($this->_options['display'] != MoodleQuickForm_tags::ONLYOFFICIAL &&
                !empty($valuearray['othertags'])) {
            $rawtags = explode(',', clean_param($valuearray['othertags'], PARAM_NOTAGS));
            foreach ($rawtags as $tag) {
                $tags[] = trim($tag);
            }
        }

        // Add any official tags that were selected.
        if ($this->_options['display'] != MoodleQuickForm_tags::NOOFFICIAL &&
                !empty($valuearray['officialtags'])) {
            $tags = array_unique(array_merge($tags, $valuearray['officialtags']));
        }

        return array($this->getName() => $tags);
    }
}
?>