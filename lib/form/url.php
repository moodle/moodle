<?php
require_once("HTML/QuickForm/text.php");

/**
 * HTML class for a url type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_url extends HTML_QuickForm_text{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    var $_hiddenLabel=false;

    function MoodleQuickForm_url($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG;
        require_once("$CFG->dirroot/repository/lib.php");
        $options = (array)$options;
        foreach ($options as $name=>$value) {
            $this->_options[$name] = $value;
        }
        if (!isset($this->_options['usefilepicker'])) {
            $this->_options['usefilepicker'] = true;
        }
        parent::HTML_QuickForm_text($elementName, $elementLabel, $attributes);
    }

    function setHiddenLabel($hiddenLabel){
        $this->_hiddenLabel = $hiddenLabel;
    }
    function toHtml(){
        global $CFG, $COURSE, $USER, $PAGE, $OUTPUT;

        $id     = $this->_attributes['id'];
        $elname = $this->_attributes['name'];

        if ($this->_hiddenLabel) {
            $this->_generateId();
            $str = '<label class="accesshide" for="'.$this->getAttribute('id').'" >'.
                        $this->getLabel().'</label>'.parent::toHtml();
        } else {
            $str = parent::toHtml();
        }
        if (empty($this->_options['usefilepicker'])) {
            return $str;
        }
        $strsaved = get_string('filesaved', 'repository');
        $straddlink = get_string('choosealink', 'repository');
        if ($COURSE->id == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        }
        $client_id = uniqid();

        $str .= <<<EOD
<button id="filepicker-button-{$client_id}" style="display:none">
$straddlink
</button>
EOD;
        $args = new stdClass();
        $args->accepted_types = '*';
        $args->return_types = FILE_EXTERNAL;
        $args->context = $PAGE->context;
        $args->client_id = $client_id;
        $args->env = 'url';
        $fp = new file_picker($args);
        $options = $fp->options;

        // print out file picker
        $str .= $OUTPUT->render($fp);

        $module = array('name'=>'form_url', 'fullpath'=>'/lib/form/url.js', 'requires'=>array('core_filepicker'));
        $PAGE->requires->js_init_call('M.form_url.init', array($options), true, $module);
        $PAGE->requires->js_function_call('show_item', array('filepicker-button-'.$client_id));

        return $str;
    }

    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }
    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }
    /**
     * Slightly different container template when frozen. Don't want to use a label tag
     * with a for attribute in that case for the element label but instead use a div.
     * Templates are defined in renderer constructor.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'static';
        } else {
            return 'default';
        }
    }
}
