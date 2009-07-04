<?php  // $Id$

require_once('HTML/QuickForm/element.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/lib/filelib.php');

class MoodleQuickForm_filemanager extends HTML_QuickForm_element {
    protected $_helpbutton = '';
    protected $_options    = array('subdirs'=>0, 'maxbytes'=>0, 'maxfiles'=>-1, 'filetypes'=>'*', 'returnvalue'=>'*');

    function MoodleQuickForm_filemanager($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG;
        require_once("$CFG->dirroot/repository/lib.php");

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

    function _get_draftfiles($draftid, $suffix) {
        global $USER, $OUTPUT, $CFG;

        $context = get_context_instance(CONTEXT_USER, $USER->id);
        $fs = get_file_storage();

        $html = '<ul class="file-list" id="draftfiles-'.$suffix.'">';

        if ($files = $fs->get_directory_files($context->id, 'user_draft', $draftid, '/', true)) {
            foreach ($files as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $filename = $file->get_filename();
                $filepath = $file->get_filepath();
                $fullname = ltrim($filepath.$filename, '/');
                $filesize = $file->get_filesize();
                $filesize = $filesize ? display_size($filesize) : '';
                $icon     = mimeinfo_from_type('icon', $file->get_mimetype());
                $viewurl  = file_encode_url("$CFG->wwwroot/draftfile.php", "/$context->id/user_draft/$draftid".$fullname, false, false);
                $html .= '<li>';
                $html .= "<a href=\"$viewurl\"><img src=\"" . $OUTPUT->old_icon_url('f/' . $icon) . "\" class=\"icon\" />&nbsp;".s($fullname)." ($filesize)</a> ";
                // TODO: maybe better use file->id here - but then make 100% it is from my own draftfiles ;-)
                //       anyway this does not work for subdirectories
                $html .= "<a href=\"###\" onclick='rm_file(".$file->get_itemid().", \"".addslashes_js($fullname)."\", this)'><img src=\"" . $OUTPUT->old_icon_url('t/delete') . "\" class=\"iconsmall\" /></a>";;
                $html .= '</li>';
            }
        }
        
        $html .= '</ul>';
        return $html;
    }

    function toHtml() {
        global $CFG, $USER, $COURSE, $OUTPUT;
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
            $this->setValue(file_get_unused_draft_itemid());
            $draftitemid = $this->getValue();
        }

        if ($COURSE->id == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        }

        $client_id = uniqid();
        $repojs = repository_get_client($context, $client_id, $this->_options['filetypes'], $this->_options['returnvalue']);

        $html = $this->_get_draftfiles($draftitemid, $client_id);
        $accessiblefp = get_string('accessiblefilepicker', 'repository');

        $str = $this->_getTabs();
        $str .= $html;
        $str .= $repojs;
        $str .= <<<EOD
<input value="$draftitemid" name="{$this->_attributes['name']}" type="hidden" />
<a href="###" id="btnadd-{$client_id}" style="display:none" class="btnaddfile" onclick="return callpicker('$id', '$client_id', '$draftitemid')">$straddfile</a>
<script type="text/javascript">
//<![CDATA[
document.getElementById('btnadd-{$client_id}').style.display="inline";
//]]>
</script>
EOD;
        if (empty($CFG->filemanagerjsloaded)) {
            $str .= <<<EOD
<script type="text/javascript">
//<![CDATA[
var selected_file = null;
var rm_cb = {
    success: function(o) {
        if(o.responseText){
            repository_client.files[o.responseText]--;
            selected_file.parentNode.removeChild(selected_file);
        }
    }
}
function rm_file(id, name, context) {
    if (confirm('$strdelete')) {
        var trans = YAHOO.util.Connect.asyncRequest('POST',
            '{$CFG->httpswwwroot}/repository/ws.php?action=delete&itemid='+id,
                rm_cb,
                'title='+name+'&client_id=$client_id'
                );
        selected_file = context.parentNode;
    }
}
function fp_callback(obj) {
    var list = document.getElementById('draftfiles-'+obj.client_id);
    var html = '<li><a href="'+obj['url']+'"><img src="'+obj['icon']+'" class="icon" /> '+obj['file']+'</a> ';
    html += '<a href="###" onclick=\'rm_file('+obj['id']+', "'+obj['file']+'", this)\'><img src="{$OUTPUT->old_icon_url('t/delete')}" class="iconsmall" /></a>';;
    html += '</li>';
    list.innerHTML += html;
}
function callpicker(el_id, client_id, itemid) {
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-'+client_id;
    picker.className = 'file-picker';
    document.body.appendChild(picker);
    var el=document.getElementById(el_id);
    var params = {};
    params.env = 'filemanager';
    params.maxbytes = {$this->_options['maxbytes']};
    params.maxfiles = {$this->_options['maxfiles']};
    params.itemid = itemid;
    params.target = el;
    params.callback = fp_callback;
    var fp = open_filepicker(client_id, params);
    return false;
}
//]]>
</script>
<noscript>
<object type="text/html" data="{$CFG->httpswwwroot}/repository/filepicker.php?action=embedded&amp;itemid={$draftitemid}&amp;ctx_id=$context->id" height="300" width="800" style="border:1px solid #000">Error</object>
</noscript>
EOD;
            $CFG->filemanagerjsloaded = true;
        }
        return $str;
    }

}
