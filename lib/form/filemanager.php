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
require_once("$CFG->dirroot/repository/lib.php");

class MoodleQuickForm_filemanager extends HTML_QuickForm_element {
    public $_helpbutton = '';
    protected $_options    = array('mainfile'=>'', 'subdirs'=>1, 'maxbytes'=>0, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

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

        // language strings
        $straddfile  = get_string('add', 'repository') . '...';
        $strmakedir  = get_string('makeafolder', 'moodle');
        $strdownload  = get_string('downloadfolder', 'repository');


        $PAGE->requires->string_for_js('loading', 'repository');
        $PAGE->requires->string_for_js('nomorefiles', 'repository');
        $PAGE->requires->string_for_js('confirmdeletefile', 'repository');
        $PAGE->requires->string_for_js('add', 'repository');
        $PAGE->requires->string_for_js('accessiblefilepicker', 'repository');
        $PAGE->requires->string_for_js('move', 'moodle');
        $PAGE->requires->string_for_js('cancel', 'moodle');
        $PAGE->requires->string_for_js('download', 'moodle');
        $PAGE->requires->string_for_js('ok', 'moodle');
        $PAGE->requires->string_for_js('emptylist', 'repository');
        $PAGE->requires->string_for_js('entername', 'repository');
        $PAGE->requires->string_for_js('enternewname', 'repository');
        $PAGE->requires->string_for_js('zip', 'editor');
        $PAGE->requires->string_for_js('unzip', 'moodle');
        $PAGE->requires->string_for_js('rename', 'moodle');
        $PAGE->requires->string_for_js('delete', 'moodle');
        $PAGE->requires->string_for_js('setmainfile', 'resource');
        $PAGE->requires->string_for_js('cannotdeletefile', 'error');
        $PAGE->requires->string_for_js('confirmdeletefile', 'repository');
        $PAGE->requires->string_for_js('nopathselected', 'repository');
        $PAGE->requires->string_for_js('popupblockeddownload', 'repository');
        $PAGE->requires->string_for_js('path', 'moodle');

        if (empty($draftitemid)) {
            // no existing area info provided - let's use fresh new draft area
            require_once("$CFG->libdir/filelib.php");
            $this->setValue(file_get_unused_draft_itemid());
            $draftitemid = $this->getValue();
        }

        $draftareainfo = file_get_draft_area_info($draftitemid);
        $filecount = $draftareainfo['filecount'];
        $client_id = uniqid();

        $args = new stdclass;
        // need these three to filter repositories list
        $args->accepted_types = $accepted_types;
        $args->return_types = FILE_INTERNAL;
        $args->context = $PAGE->context;
        $args->env = 'filemanager';

        $filepicker_options = initialise_filepicker($args);

        $filepicker_options->client_id = $client_id;
        $filepicker_options->maxbytes = $this->_options['maxbytes'];
        $filepicker_options->maxfiles = $this->_options['maxfiles'];
        $filepicker_options->env      = 'filemanager';
        $filepicker_options->itemid   = $draftitemid;

        // Generate file picker
        $result = new stdclass;

        $options = file_get_draft_area_files($draftitemid);
        $options->mainfile  = $this->_options['mainfile'];
        $options->maxbytes  = $this->getMaxbytes();
        $options->maxfiles  = $this->getMaxfiles();
        $options->client_id = $client_id;
        $options->filecount = $filecount;
        $options->itemid    = $draftitemid;
        $options->subdirs   = $this->_options['subdirs'];
        // store filepicker options
        $options->filepicker = $filepicker_options;
        $options->target    = $id;

        $html = $this->_getTabs();

        
        $module = array('name'=>'form_filemanager', 'fullpath'=>'/lib/form/filemanager.js', 'requires' => array('core_filepicker', 'base', 'io', 'node', 'json', 'yui2-button', 'yui2-container', 'yui2-layout', 'yui2-menu', 'yui2-treeview'));
        $PAGE->requires->js_module($module);
        $PAGE->requires->js_init_call('M.form_filemanager.init', array($options), true, $module);

        // print out this only once
        if (empty($CFG->filemanagertemplateloaded)) {
            $CFG->filemanagertemplateloaded = true;
            $html .= <<<FMHTML
<div id="fm-template" style="display:none"><div class="fm-file-menu">___action___</div> <div class="fm-file-name">___fullname___</div></div>
FMHTML;
        }

        $html .= <<<FMHTML
<input value="$draftitemid" name="{$elname}" type="hidden" />
<div id="filemanager-wrapper-{$client_id}" style="display:none">
    <div class="fm-breadcrumb" id="fm-path-{$client_id}"></div>
    <div class="filemanager-toolbar">
        <button id="btnadd-{$client_id}" onclick="return false">{$straddfile}</button>
        <button id="btncrt-{$client_id}" onclick="return false">{$strmakedir}</button>
        <button id="btndwn-{$client_id}" onclick="return false">{$strdownload}</button>
    </div>
    <div class="filemanager-container" id="filemanager-{$client_id}">
        <ul id="draftfiles-{$client_id}">
            <li>Loading...</li>
        </ul>
    </div>
</div>
FMHTML;
        // non-javascript file manager, will be destroied automatically if javascript is enabled.
        // will be removed if javascript is enabled
        $editorurl = "$CFG->wwwroot/repository/filepicker.php?env=filemanager&amp;action=embedded&amp;itemid=$draftitemid&amp;subdirs=/&amp;maxbytes=$options->maxbytes&amp;ctx_id=".$PAGE->context->id.'&amp;course='.$PAGE->course->id;
        $html .= <<<NONJS
<div id="nonjs-filemanager-$client_id">
<object type="text/html" data="$editorurl" height="160" width="600" style="border:1px solid #000">Error</object>
</div>
NONJS;
        return $html;
    }
}
