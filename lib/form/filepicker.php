<?php

global $CFG;

require_once("HTML/QuickForm/button.php");
require_once($CFG->dirroot.'/repository/lib.php');

/**
 * HTML class for a single filepicker element (based on button)
 *
 * @author       Moodle.com
 * @version      1.0
 * @since        Moodle 2.0
 * @access       public
 */
class MoodleQuickForm_filepicker extends HTML_QuickForm_input {
    public $_helpbutton = '';
    protected $_options    = array('maxbytes'=>0, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

    function MoodleQuickForm_filepicker($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG;

        $options = (array)$options;
        foreach ($options as $name=>$value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (!empty($options['maxbytes'])) {
            $this->_options['maxbytes'] = get_max_upload_file_size($CFG->maxbytes, $options['maxbytes']);
        }
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
    }

    function setHelpButton($helpbuttonargs, $function='helpbutton') {
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
        global $CFG, $COURSE, $USER, $PAGE;


        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $strsaved = get_string('filesaved', 'repository');
        $straddfile = get_string('openpicker', 'repository');
        $currentfile = '';
        if ($draftitemid = (int)$this->getValue()) {
            $fs = get_file_storage();
            $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user_draft', $draftitemid, 'id DESC', false)) {
                $file = reset($files);
                $currentfile = $file->get_filename();
            }
        } else {
            // no existing area info provided - let's use fresh new draft area
            $draftitemid = file_get_unused_draft_itemid();
            $this->setValue($draftitemid);
        }
        if ($COURSE->id == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        }

        $client_id = uniqid();

        $args = new stdclass;
        // need these three to filter repositories list
        $args->accepted_types = $this->_options['accepted_types']?$this->_options['accepted_types']:'*';
        $args->return_types = FILE_INTERNAL;
        $args->context = $PAGE->context;

        $options = initialise_filepicker($args);

        $options->client_id = $client_id;
        $options->maxbytes = $this->_options['maxbytes'];
        $options->maxfiles = 1;
        $options->env = 'filepicker';
        $options->itemid = $draftitemid;

        $id     = $this->_attributes['id'];
        $elname = $this->_attributes['name'];

        $str = $this->_getTabs();
        $str .= '<input type="hidden" name="'.$elname.'" id="'.$id.'" value="'.$draftitemid.'" />';

        $str .= <<<EOD
<div id="filepicker-wrapper-{$client_id}" style="display:none">
    <div class="filemanager-toolbar">
        <a href="###" id="filepicker-button-{$client_id}">$straddfile</a>
    </div>
    <div id="file_info_{$client_id}" class="mdl-left">$currentfile</div>
</div>
<!-- non javascript file picker -->
<noscript>
<object type="text/html" id="nonjs-filepicker-{$client_id}" data="{$CFG->httpswwwroot}/repository/filepicker.php?env=filepicker&amp;action=embedded&amp;itemid={$draftitemid}&amp;ctx_id=$context->id" height="300" width="800" style="border:1px solid #000">
Moodle File Picker
</object>
</noscript>
EOD;
        $module = array('name'=>'form_filepicker', 'fullpath'=>'/lib/form/filepicker.js', 'requires'=>array('core_filepicker'));
        $PAGE->requires->js_init_call('M.form_filepicker.init', array($options), true, $module);
        return $str;
    }

    function exportValue(&$submitValues, $assoc = false) {
        global $USER;

        // make sure max one file is present and it is not too big
        if ($draftitemid = $submitValues[$this->_attributes['name']]) {
            $fs = get_file_storage();
            $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user_draft', $draftitemid, 'id DESC', false)) {
                $file = array_shift($files);
                if ($this->_options['maxbytes'] and $file->get_filesize() > $this->_options['maxbytes']) {
                    // bad luck, somebody tries to sneak in oversized file
                    $file->delete();
                }
                foreach ($files as $file) {
                    // only one file expected
                    $file->delete();
                }
            }
        }

        return array($this->_attributes['name'] => $submitValues[$this->_attributes['name']]);
    }
}
