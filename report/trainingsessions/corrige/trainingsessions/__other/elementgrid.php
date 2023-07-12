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
 * Grid container form element
 *
 * Contains HTML class for a button type element
 *
 * @package   core_form
 * @copyright 2010 Valery Fremaux <valery.fremaux@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!class_exists('MoodleQuickForm_elementgrid')) {
    if (file_exists($CFG->libdir.'/pear/HTML/QuickForm/elementgrid.php')) {
        require_once('HTML/QuickForm/elementgrid.php');
    } else {
        require_once($CFG->dirroot.'/report/trainingsessions/__other/HTML/QuickForm/elementgrid.php');
    }

/**
 * HTML class for a button type element
 *
 * Overloaded {@link HTML_QuickForm_button} to add help button
 *
 * @package   core_form
 * @category  form
 * @copyright 2010 Valery Fremaux <valery.fremaux@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    class MoodleQuickForm_elementgrid extends HTML_QuickForm_elementgrid {
        /** @var string html for help button, if empty then no help */
        var $_helpbutton='';
    
        /**
         * constructor
         *
         * @param string $elementName (optional) name for the button
         * @param string $value (optional) value for the button
         * @param mixed $attributes (optional) Either a typical HTML attribute string
         *              or an associative array
         */
        function __construct($elementName=null, $label=null, $options=null) {
            parent::__construct($elementName, $label, $options);
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
         * Slightly different container template when frozen.
         *
         * @return string
         */
        function getElementTemplateType(){
            if ($this->_flagFrozen){
                return 'nodisplay';
            } else {
                return 'default';
            }
        }

        /**
         * Returns Html for the element
         *
         * @access      public
         * @return      string
         */
        function toHtml(){

            $table = new html_table();
            // $table->updateAttributes($this->getAttributes());

            $col = 0;
            $header = array();
            if ($this->_columnNames) {
                foreach ($this->_columnNames as $key => $value) {
                    ++$col;
                    $header[] = $value;
                }
            }
            $table->head = $header;
            $table->width = $this->_width;
            $table->size = $this->_columnWidths;
            $table->align = $this->_columnAligns;
            $table->classes = implode(' ', $this->_columnClasses);
    
            $data = array();
            foreach (array_keys($this->_rows) as $key) {
                $col = 0;
                $row = array();
                foreach (array_keys($this->_rows[$key]) as $key2) {
                    ++$col;
                    $row[] = $this->_rows[$key][$key2]->toHTML();
                }
                $data[] = $row;
            }
            $table->data = $data;

            return html_writer::table($table);

        }
    }

    if (file_exists($CFG->libdir.'/form/elementgrid.php')){
        MoodleQuickForm::registerElementType('elementgrid', "$CFG->libdir/form/elementgrid.php", 'MoodleQuickForm_elementgrid');
    } else {
        MoodleQuickForm::registerElementType('elementgrid', $CFG->dirroot.'/report/trainingsessions/__other/elementgrid.php', 'MoodleQuickForm_elementgrid');
    }
}