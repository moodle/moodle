<?php  // $Id$

require_once('HTML/QuickForm/element.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/lib/filelib.php');

class MoodleQuickForm_filemanager extends HTML_QuickForm_element {
    protected $_helpbutton = '';
    protected $_options    = array('subdirs'=>0, 'maxbytes'=>0, 'maxfiles'=>-1);

    function MoodleQuickForm_filemanager($elementName=null, $elementLabel=null, $options=null, $attributes=null) {
        global $CFG;

        $options = (array)$options;
        foreach ($options as $name=>$value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (!empty($options['filetypes'])) {
            $this->filetypes = $options['filetypes'];
        } else {
            $this->filetypes = '*';
        }
        if (!empty($options['returnvalue'])) {
            $this->returnvalue = $options['returnvalue'];
        } else {
            $this->returnvalue = '*';
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

    function _get_draftfiles($draftid, $suffix) {
        global $USER, $CFG;
        $html = '';
        if (!$context = get_context_instance(CONTEXT_USER, $USER->id)) {
        }
        $contextid = $context->id;
        $filearea  = 'user_draft';

        $browser = get_file_browser();
        $fs      = get_file_storage();
        $filepath = '/';
        if (!$directory = $fs->get_file($context->id, 'user_draft', $draftid, $filepath, '.')) {
            $directory = new virtual_root_file($context->id, 'user_draft', $draftid);
            $filepath = $directory->get_filepath();
        }
        $files = $fs->get_directory_files($context->id, 'user_draft', $draftid, $directory->get_filepath());
        $parent = $directory->get_parent_directory();
        $html .= '<ul id="draftfiles-'.$suffix.'">';
        foreach ($files as $file) {
            $filename    = $file->get_filename();
            $filenameurl = rawurlencode($filename);
            $filepath    = $file->get_filepath();
            $filesize    = $file->get_filesize();
            $filesize    = $filesize ? display_size($filesize) : '';
            $mimetype = $file->get_mimetype();
            $icon    = mimeinfo_from_type('icon', $mimetype);
            $viewurl = $browser->encodepath("$CFG->wwwroot/draftfile.php", "/$contextid/user_draft/$draftid".$filepath.$filename, false, false);
            $html .= '<li>';
            $html .= "<a href=\"$viewurl\"><img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" />&nbsp;".s($filename)." ($filesize)</a> ";
            $html .= "<a href=\"###\" onclick='rm_$suffix(".$file->get_itemid().", \"".$filename."\", this)'><img src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" /></a>";;
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    function toHtml() {
        global $CFG, $USER, $COURSE;
        require_once("$CFG->dirroot/repository/lib.php");

        $strdelete  = get_string('confirmdeletefile', 'repository');
        $straddfile = get_string('add', 'repository');

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

        if (empty($draftitemid)) {
            // no existing area info provided - let's use fresh new draft area
            require_once("$CFG->libdir/filelib.php");
            $this->setValue(file_get_new_draftitemid());
            $draftitemid = $this->getValue();
        }

        if ($COURSE->id == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        }

        $repo_info = repository_get_client($context, $this->filetypes, $this->returnvalue);
        $suffix = $repo_info['suffix'];

        $html = $this->_get_draftfiles($draftitemid, $suffix);

        $str = $this->_getTabs();
        $str .= $html;
        $str .= $repo_info['css'];
        $str .= $repo_info['js'];
        $str .= <<<EOD
<script type="text/javascript">
var elitem = null;
var rm_cb_$suffix = {
    success: function(o) {
        if(o.responseText && o.responseText == 200){
            elitem.parentNode.removeChild(elitem);
        }
    }
}
function rm_$suffix(id, name, context) {
    if (confirm('$strdelete')) {
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            '{$CFG->httpswwwroot}/repository/ws.php?action=delete&itemid='+id,
                rm_cb_$suffix,
                'title='+name
                );
        elitem = context.parentNode;
    }
}
function uf_$suffix(obj) {
    var list = document.getElementById('draftfiles-$suffix');
    var html = '<li><a href="'+obj['url']+'"><img src="'+obj['icon']+'" class="icon" /> '+obj['file']+'</a> ';
    html += '<a href="###" onclick=\'rm_$suffix('+obj['id']+', "'+obj['file']+'", this)\'><img src="{$CFG->pixpath}/t/delete.gif" class="iconsmall" /></a>';;
    html += '</li>';
    list.innerHTML += html;
}
function callpicker_$suffix() {
    document.body.className += ' yui-skin-sam';
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-$suffix';
    picker.className = 'file-picker';
    document.body.appendChild(picker);
    var el=document.getElementById('$id');
    openpicker_$suffix({'env':'filemanager', 'target':el, 'itemid': $draftitemid, 'callback':uf_$suffix})
}
</script>
<input value="$draftitemid" name="{$this->_attributes['name']}" type="hidden" />
<div>
    <input value="$straddfile" onclick="callpicker_$suffix()" type="button" />
</div>
EOD;
        return $str;
    }

}
