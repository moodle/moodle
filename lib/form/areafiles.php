<?php  // $Id$

require_once('HTML/QuickForm/element.php');

class MoodleQuickForm_areafiles extends HTML_QuickForm_element {
    protected $_helpbutton = '';
    protected $_options    = array('subdirs'=>0, 'maxbytes'=>0);

    function MoodleQuickForm_areafiles($elementName=null, $elementLabel=null, $options=null) {
        if (!empty($options['subdirs'])) {
            $this->_options['subdirs'] = 1;
        }
        if (!empty($options['maxbytes'])) {
            $this->_options['maxbytes'] = $options['maxbytes'];
        }
        parent::HTML_QuickForm_element($elementName, $elementLabel);
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
        $this->_options['maxbytes'] = $maxbytes;
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
        global $CFG, $USER;

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
        $draftitemid = $this->getValue();

        if (empty($draftitemid)) {
            // no existing area info provided - let's use fresh new draft area
            require_once("$CFG->libdir/filelib.php");
            $this->setValue(file_get_new_draftitemid());
            $draftitemid = $this->getValue();
        }

        $editorurl = "$CFG->wwwroot/files/draftfiles.php?itemid=$draftitemid&amp;subdirs=$subdirs";

        $str = $this->_getTabs();
        $str .= '<input type="hidden" name="'.$elname.'" value="'.$draftitemid.'" />';
        $str .= '<object type="text/html" id="'.$id.'" data="'.$editorurl.'" height="160" width="600" style="border:1px solid #000">Error</object>'; // TODO: localise, fix styles, etc.

        return $str;
    }

}
