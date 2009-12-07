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

class MoodleQuickForm_filemanager extends HTML_QuickForm_element {
    public $_helpbutton = '';
    protected $_options    = array('mainfile'=>'', 'subdirs'=>0, 'maxbytes'=>0, 'maxfiles'=>-1, 'filetypes'=>'*', 'returntypes'=>FILE_INTERNAL);

    function MoodleQuickForm_filemanager($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG, $PAGE;
        require_once("$CFG->dirroot/repository/lib.php");

        // has to require these js files before head
        $PAGE->requires->yui_lib('menu');
        $PAGE->requires->yui_lib('connection');
        $PAGE->requires->yui_lib('json');

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

        repository_head_setup();
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
        if (!is_array($helpbuttonargs)){
            $helpbuttonargs=array($helpbuttonargs);
        }else{
            $helpbuttonargs=$helpbuttonargs;
        }
        //we do this to to return html instead of printing it
        //without having to specify it in every call to make a button.
        if ('helpbutton' == $function){
            $defaultargs=array('', '', 'moodle', true, false, '', true);
            $helpbuttonargs=$helpbuttonargs + $defaultargs ;
        }
        $this->_helpbutton=call_user_func_array($function, $helpbuttonargs);
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

        if ($COURSE->id == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        }

        $client_id = uniqid();

        // Generate file picker
        $repojs = repository_get_client($context, $client_id, $this->_options['filetypes'], $this->_options['returntypes']);
        $result = new stdclass;

        $options = file_get_draft_area_files($draftitemid);
        $options->mainfile  = $this->_options['mainfile'];
        $options->maxbytes  = $this->getMaxbytes();
        $options->maxfiles  = $this->getMaxfiles();
        $options->client_id = $client_id;
        $options->filecount = $filecount;
        $options->itemid    = $draftitemid;
        $options->subdirs   = $this->_options['subdirs'];
        $options->target    = $id;

        $html = $this->_getTabs();
        $html .= $repojs;

        if (empty($CFG->filemanagerjsloaded)) {
            $PAGE->requires->js('lib/form/filemanager.js');
            $CFG->filemanagerjsloaded = true;
            // print html template
            $html .= <<<FMHTML
<div id="fm-template" style="display:none"><div class="fm-file-menu">___action___</div> <div class="fm-file-name">___fullname___</div></div>
FMHTML;
        }

        $html .= <<<FMHTML
<input value="$draftitemid" name="{$elname}" type="hidden" />
<div id="filemanager-wrapper-{$client_id}" style="display:none">
    <div class="fm-breadcrumb" id="fm-path-{$client_id}"></div>
    <div class="filemanager-toolbar">
        <a href="###" id="btnadd-{$client_id}">{$straddfile}</a>
        <a href="###" id="btncrt-{$client_id}">{$strmakedir}</a>
        <a href="###" id="btndwn-{$client_id}">{$strdownload}</a>
    </div>

    <div class="filemanager-container" id="filemanager-{$client_id}">
        <ul id="draftfiles-{$client_id}">
            <li>Loading...</li>
        </ul>
    </div>
</div>
FMHTML;
        // non-javascript file manager, will be destroied automatically if javascript is enabled.
        $html .= '<div id="nonjs-filemanager-'.$client_id.'">';
        $editorurl = "$CFG->wwwroot/repository/filepicker.php?env=filemanager&amp;action=embedded&amp;itemid=$draftitemid&amp;subdirs=/&amp;maxbytes=$options->maxbytes&amp;ctx_id=".$context->id;
        $html .= '<object type="text/html" data="'.$editorurl.'" height="160" width="600" style="border:1px solid #000">Error</object>';
        $html .= '</div>';

        $html .= $PAGE->requires->js_function_call('destroy_item', array("nonjs-filemanager-{$client_id}"))->asap();
        $html .= $PAGE->requires->js_function_call('show_item', array("filemanager-wrapper-{$client_id}"))->asap();
        $PAGE->requires->js_function_call('launch_filemanager', array($client_id, $options))->on_dom_ready();

        return $html;
    }

}
