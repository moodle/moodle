<?php
require_once('HTML/QuickForm/password.php');

/**
 * HTML class for a password type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_password extends HTML_QuickForm_password{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    function MoodleQuickForm_password($elementName=null, $elementLabel=null, $attributes=null) {
        global $CFG;
        if (empty($CFG->xmlstrictheaders)) {
            // no standard mform in moodle should allow autocomplete of passwords
            // this is valid attribute in html5, sorry, we have to ignore validation errors in legacy xhtml 1.0
            $attributes = (array)$attributes;
            if (!isset($attributes['autocomplete'])) {
                $attributes['autocomplete'] = 'off';
            }
        }

        parent::HTML_QuickForm_password($elementName, $elementLabel, $attributes);
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
}
