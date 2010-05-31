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
 * File manager
 *
 * @package    moodlecore
 * @subpackage file
 * @copyright  1999 onwards Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once('HTML/QuickForm/element.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');

class MoodleQuickForm_filemanager extends HTML_QuickForm_element {
    public $_helpbutton = '';
    protected $_options    = array('mainfile'=>'', 'subdirs'=>1, 'maxbytes'=>-1, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

    function MoodleQuickForm_filemanager($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG, $PAGE;

        $options = (array)$options;
        foreach ($options as $name=>$value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (!empty($options['maxbytes'])) {
            $this->_options['maxbytes'] = get_max_upload_file_size($CFG->maxbytes, $options['maxbytes']);
        }
        parent::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
    }

    function setName($name) {
        $this->updateAttributes(array('name'=>$name));
    }

    function getName() {
        return $this->getAttribute('name');
    }

    function setValue($value) {
        $this->updateAttributes(array('value'=>$value));
    }

    function getValue() {
        return $this->getAttribute('value');
    }

    function getMaxbytes() {
        return $this->_options['maxbytes'];
    }

    function setMaxbytes($maxbytes) {
        global $CFG;
        $this->_options['maxbytes'] = get_max_upload_file_size($CFG->maxbytes, $maxbytes);
    }

    function getSubdirs() {
        return $this->_options['subdirs'];
    }

    function setSubdirs($allow) {
        $this->_options['subdirs'] = $allow;
    }

    function getMaxfiles() {
        return $this->_options['maxfiles'];
    }

    function setMaxfiles($num) {
        $this->_options['maxfiles'] = $num;
    }

    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }

    function getHelpButton() {
        return $this->_helpbutton;
    }

    function getElementTemplateType() {
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }

    function toHtml() {
        global $CFG, $USER, $COURSE, $PAGE, $OUTPUT;
        require_once("$CFG->dirroot/repository/lib.php");

        // security - never ever allow guest/not logged in user to upload anything or use this element!
        if (isguestuser() or !isloggedin()) {
            print_error('noguest');
        }

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $id          = $this->_attributes['id'];
        $elname      = $this->_attributes['name'];
        $subdirs     = $this->_options['subdirs'];
        $maxbytes    = $this->_options['maxbytes'];
        $draftitemid = $this->getValue();
        $accepted_types = $this->_options['accepted_types'];

        if (empty($draftitemid)) {
            // no existing area info provided - let's use fresh new draft area
            require_once("$CFG->libdir/filelib.php");
            $this->setValue(file_get_unused_draft_itemid());
            $draftitemid = $this->getValue();
        }

        $client_id = uniqid();

        // filemanager options
        $options = new stdclass;
        $options->mainfile  = $this->_options['mainfile'];
        $options->filearea  = 'user_draft';
        $options->maxbytes  = $this->_options['maxbytes'];
        $options->maxfiles  = $this->getMaxfiles();
        $options->client_id = $client_id;
        $options->itemid    = $draftitemid;
        $options->subdirs   = $this->_options['subdirs'];
        $options->target    = $id;
        $options->accepted_types = $accepted_types;
        $options->return_types = FILE_INTERNAL;
        $options->context = $PAGE->context;

        $html = $this->_getTabs();
        $html .= $OUTPUT->file_manager($options);

        $html .= '<input value="'.$draftitemid.'" name="'.$elname.'" type="hidden" />';
        // label element needs 'for' attribute work
        $html .= '<input value="" id="id_'.$elname.'" type="hidden" />';

        return $html;
    }
}
