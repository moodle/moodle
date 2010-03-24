<?php

global $CFG;

require_once('HTML/QuickForm/element.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');

//TODO:
//  * locking
//  * freezing
//  * ajax format conversion

class MoodleQuickForm_editor extends HTML_QuickForm_element {
    public $_helpbutton = '';
    protected $_options    = array('subdirs'=>0, 'maxbytes'=>0, 'maxfiles'=>0, 'changeformat'=>0,
                                   'context'=>null, 'noclean'=>0, 'trusttext'=>0);
    protected $_values     = array('text'=>null, 'format'=>null, 'itemid'=>null);

    function MoodleQuickForm_editor($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
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
        if (!$this->_options['context']) {
            $this->_options['context'] = get_context_instance(CONTEXT_SYSTEM);
        }
        $this->_options['trusted'] = trusttext_trusted($this->_options['context']);
        parent::HTML_QuickForm_element($elementName, $elementLabel, $attributes);

        editors_head_setup();
    }

    function setName($name) {
        $this->updateAttributes(array('name'=>$name));
    }

    function getName() {
        return $this->getAttribute('name');
    }

    function setValue($values) {
        $values = (array)$values;
        foreach ($values as $name=>$value) {
            if (array_key_exists($name, $this->_values)) {
                $this->_values[$name] = $value;
            }
        }
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

    function getMaxfiles() {
        return $this->_options['maxfiles'];
    }

    function setMaxfiles($num) {
        $this->_options['maxfiles'] = $num;
    }

    function getSubdirs() {
        return $this->_options['subdirs'];
    }

    function setSubdirs($allow) {
        $this->_options['subdirs'] = $allow;
    }

    function setHelpButton($_helpbuttonargs, $function='_helpbutton') {
        if (!is_array($_helpbuttonargs)) {
            $_helpbuttonargs = array($_helpbuttonargs);
        } else {
            $_helpbuttonargs = $_helpbuttonargs;
        }
        //we do this to to return html instead of printing it
        //without having to specify it in every call to make a button.
        if ('_helpbutton' == $function){
            $defaultargs = array('', '', 'moodle', true, false, '', true);
            $_helpbuttonargs = $_helpbuttonargs + $defaultargs ;
        }
        $this->_helpbutton=call_user_func_array($function, $_helpbuttonargs);
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
        global $CFG, $COURSE, $PAGE;
        require_once($CFG->dirroot.'/repository/lib.php');

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $ctx = $this->_options['context'];

        $id           = $this->_attributes['id'];
        $elname       = $this->_attributes['name'];

        $subdirs      = $this->_options['subdirs'];
        $maxbytes     = $this->_options['maxbytes'];
        $maxfiles     = $this->_options['maxfiles'];
        $changeformat = $this->_options['changeformat']; // TO DO: implement as ajax calls

        $text         = $this->_values['text'];
        $format       = $this->_values['format'];
        $draftitemid  = $this->_values['itemid'];

        // security - never ever allow guest/not logged in user to upload anything
        if (isguestuser() or !isloggedin()) {
            $maxfiles = 0;
        }

        $str = $this->_getTabs();
        $str .= '<div>';

        $editor = get_preferred_texteditor($format);
        $strformats = format_text_menu();
        $formats =  $editor->get_supported_formats();
        foreach ($formats as $fid) {
            $formats[$fid] = $strformats[$fid];
        }

        // get filepicker info
        //
        $fpoptions = array();
        if ($maxfiles != 0 ) {
            if (empty($draftitemid)) {
                // no existing area info provided - let's use fresh new draft area
                require_once("$CFG->libdir/filelib.php");
                $this->setValue(array('itemid'=>file_get_unused_draft_itemid()));
                $draftitemid = $this->_values['itemid'];
            }

            $args = new stdclass;
            // need these three to filter repositories list
            $args->accepted_types = array('image');
            $args->return_types = (FILE_INTERNAL | FILE_EXTERNAL);
            $args->context = $ctx;
            $args->env = 'filepicker';

            $image_options = initialise_filepicker($args);

            $args->accepted_types = array('video', 'media');
            $media_options = initialise_filepicker($args); 

            $image_options->client_id = uniqid();
            $media_options->client_id = uniqid();
            $image_options->maxbytes = $this->_options['maxbytes'];
            $media_options->maxbytes  = $this->_options['maxbytes'];
            $image_options->maxfiles = 1;
            $media_options->maxfiles = 1;
            $image_options->env = 'editor';
            $media_options->env = 'editor';
            $image_options->itemid = $draftitemid;
            $media_options->itemid = $draftitemid;
            $fpoptions['image'] = $image_options;
            $fpoptions['media'] = $media_options;
        }

    /// print text area - TODO: add on-the-fly switching, size configuration, etc.
        $editor->use_editor($id, $this->_options, $fpoptions);

        $str .= '<div><textarea id="'.$id.'" name="'.$elname.'[text]" rows="15" cols="80">';
        $str .= s($text);
        $str .= '</textarea></div>';

        $str .= '<div>';
        $str .= '<select name="'.$elname.'[format]">';
        foreach ($formats as $key=>$desc) {
            $selected = ($format == $key) ? 'selected="selected"' : '';
            $str .= '<option value="'.s($key).'" '.$selected.'>'.$desc.'</option>';
        }
        $str .= '</select>';
        $str .= '</div>';

        // during moodle installation, user area doesn't exist
        // so we need to disable filepicker here.
        if (!during_initial_install() && empty($CFG->adminsetuppending)) {
            // 0 means no files, -1 unlimited
            if ($maxfiles != 0 ) {
                $str .= '<div><input type="hidden" name="'.$elname.'[itemid]" value="'.$draftitemid.'" /></div>';
                $str .= '<div id="'.$id.'_filemanager">';
                $editorurl = "$CFG->wwwroot/repository/filepicker.php?action=browse&amp;env=editor&amp;itemid=$draftitemid&amp;subdirs=$subdirs&amp;maxbytes=$maxbytes&amp;ctx_id=".$ctx->id.'&amp;course='.$PAGE->course->id;
                $str .= html_writer::link($editorurl, get_string('manageeditorfiles'), array('target'=>'_blank'));
                //$str .= '<object type="text/html" data="'.$editorurl.'" height="160" width="600" style="border:1px solid #000">Error</object>';
                $str .= '</div>';
            }
        }


        $str .= '</div>';

        return $str;
    }

}
