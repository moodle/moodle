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
        $this->_type = 'filemanager';
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
        $options = new stdClass();
        $options->mainfile  = $this->_options['mainfile'];
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
        $html .= form_filemanager_render($options);

        $html .= '<input value="'.$draftitemid.'" name="'.$elname.'" type="hidden" />';
        // label element needs 'for' attribute work
        $html .= '<input value="" id="id_'.$elname.'" type="hidden" />';

        return $html;
    }
}



/**
 * Data structure representing a file manager.
 *
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class form_filemanaer_x {
    //TODO: do not use this abstraction (skodak)

    public $options;
    public function __construct(stdClass $options) {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot. '/repository/lib.php');
        $defaults = array(
            'maxbytes'=>-1,
            'maxfiles'=>-1,
            'itemid'=>0,
            'subdirs'=>0,
            'client_id'=>uniqid(),
            'accepted_types'=>'*',
            'return_types'=>FILE_INTERNAL,
            'context'=>$PAGE->context
            );
        foreach ($defaults as $key=>$value) {
            if (empty($options->$key)) {
                $options->$key = $value;
            }
        }

        $fs = get_file_storage();

        // initilise options, getting files in root path
        $this->options = file_get_drafarea_files($options->itemid, '/');

        // calculate file count
        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $options->itemid, 'id', false);
        $filecount = count($files);
        $this->options->filecount = $filecount;

        // copying other options
        foreach ($options as $name=>$value) {
            $this->options->$name = $value;
        }

        // building file picker options
        $params = new stdClass();
        $params->accepted_types = $options->accepted_types;
        $params->return_types = $options->return_types;
        $params->context = $options->context;
        $params->env = 'filemanager';
        $params->disable_types = !empty($options->disable_types)?$options->disable_types:array();
        $filepicker_options = initialise_filepicker($params);
        $this->options->filepicker = $filepicker_options;
    }
}

/**
 * Print the file manager
 *
 * <pre>
 * $OUTPUT->file_manager($options);
 * </pre>
 *
 * @param array $options associative array with file manager options
 *   options are:
 *       maxbytes=>-1,
 *       maxfiles=>-1,
 *       itemid=>0,
 *       subdirs=>false,
 *       client_id=>uniqid(),
 *       acepted_types=>'*',
 *       return_types=>FILE_INTERNAL,
 *       context=>$PAGE->context
 * @return string HTML fragment
 */
function form_filemanager_render($options) {
    global $CFG, $OUTPUT, $PAGE;

    $fm = new form_filemanaer_x($options); //TODO: this is unnecessary here, the nested options are getting too complex

    static $filemanagertemplateloaded;

    $html = '';
    $options = $fm->options;
    $straddfile  = get_string('addfile', 'repository');
    $strmakedir  = get_string('makeafolder', 'moodle');
    $strdownload = get_string('downloadfolder', 'repository');
    $strloading  = get_string('loading', 'repository');

    $icon_progress = $OUTPUT->pix_icon('i/loading_small', $strloading).'';

    $client_id = $options->client_id;
    $itemid    = $options->itemid;
    list($context, $course, $cm) = get_context_info_array($options->context->id);
    if (is_object($course)) {
        $course_maxbytes = $course->maxbytes;
    } else {
        $course_maxbytes = $CFG->maxbytes;
    }

    if ($options->maxbytes == -1 || empty($options->maxbytes)) {
        $options->maxbytes = $CFG->maxbytes;
    }

    if (empty($options->filecount)) {
        $extra = ' style="display:none"';
    } else {
        $extra = '';
    }

    $maxsize = get_string('maxfilesize', 'moodle', display_size(get_max_upload_file_size($CFG->maxbytes, $course_maxbytes, $options->maxbytes)));
    $loading = get_string('loading', 'repository');
    $html .= <<<FMHTML
<div class="filemanager-loading mdl-align" id='filemanager-loading-{$client_id}'>
$icon_progress
</div>
<div id="filemanager-wrapper-{$client_id}" style="display:none">
    <div class="fm-breadcrumb" id="fm-path-{$client_id}"></div>
    <div class="filemanager-toolbar">
        <input type="button" class="fm-btn-add" id="btnadd-{$client_id}" onclick="return false" value="{$straddfile}" />
        <input type="button" class="fm-btn-mkdir" id="btncrt-{$client_id}" onclick="return false" value="{$strmakedir}" />
        <input type="button" class="fm-btn-download" id="btndwn-{$client_id}" onclick="return false" {$extra} value="{$strdownload}" />
        <span> $maxsize </span>
    </div>
    <div class="filemanager-container" id="filemanager-{$client_id}">
        <ul id="draftfiles-{$client_id}" class="fm-filelist">
            <li>{$loading}</li>
        </ul>
    </div>
</div>
<div class='clearer'></div>
FMHTML;
    if (empty($filemanagertemplateloaded)) {
        $filemanagertemplateloaded = true;
        $html .= <<<FMHTML
<div id="fm-template" style="display:none">___fullname___ ___action___</div>
FMHTML;
    }

    $module = array(
        'name'=>'form_filemanager',
        'fullpath'=>'/lib/form/filemanager.js',
        'requires' => array('core_filepicker', 'base', 'io', 'node', 'json', 'yui2-button', 'yui2-container', 'yui2-layout', 'yui2-menu', 'yui2-treeview'),
        'strings' => array(array('loading', 'repository'), array('nomorefiles', 'repository'), array('confirmdeletefile', 'repository'),
             array('add', 'repository'), array('accessiblefilepicker', 'repository'), array('move', 'moodle'),
             array('cancel', 'moodle'), array('download', 'moodle'), array('ok', 'moodle'),
             array('emptylist', 'repository'), array('nofilesattached', 'repository'), array('entername', 'repository'), array('enternewname', 'repository'),
             array('zip', 'editor'), array('unzip', 'moodle'), array('rename', 'moodle'), array('delete', 'moodle'),
             array('cannotdeletefile', 'error'), array('confirmdeletefile', 'repository'),
             array('nopathselected', 'repository'), array('popupblockeddownload', 'repository'),
             array('draftareanofiles', 'repository'), array('path', 'moodle'), array('setmainfile', 'repository'),
             array('moving', 'repository'), array('files', 'moodle')
        )
    );
    $PAGE->requires->js_module($module);
    $PAGE->requires->js_init_call('M.form_filemanager.init', array($options), true, $module);

    // non javascript file manager
    $filemanagerurl = new moodle_url('/repository/draftfiles_manager.php', array(
        'env'=>'filemanager',
        'action'=>'browse',
        'itemid'=>$itemid,
        'subdirs'=>$options->subdirs,
        'maxbytes'=>$options->maxbytes,
        'maxfiles'=>$options->maxfiles,
        'ctx_id'=>$PAGE->context->id,
        'course'=>$PAGE->course->id,
        'sesskey'=>sesskey(),
        ));

    $html .= '<noscript>';
    $html .= "<div><object type='text/html' data='$filemanagerurl' height='160' width='600' style='border:1px solid #000'></object></div>";
    $html .= '</noscript>';


    return $html;
}
