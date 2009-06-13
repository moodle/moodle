<?php
global $CFG;
require_once("$CFG->libdir/form/submit.php");
class MoodleQuickForm_submitlink extends MoodleQuickForm_submit {
    var $_js;
    var $_onclick;
    function MoodleQuickForm_submitlink($elementName=null, $value=null, $attributes=null) {
        parent::MoodleQuickForm_submit($elementName, $value, $attributes);
    }

    function toHtml() {
        $text = $this->_attributes['value'];
        $onmouseover = "window.status=\'" . $text . "\';";
        $onmouseout = "window.status=\'\';"; 

        return "<noscript>" . parent::toHtml() . '</noscript><script type="text/javascript">' . $this->_js . "\n" 
             . 'document.write(\'<a href="#" onclick="' . $this->_onclick . '" onmouseover="' . $onmouseover . '" onmouseout="' . $onmouseout . '">' 
             . $text . "</a>');\n</script>";
    }
}
?>
