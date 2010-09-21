<?php
/**
* prints the form to choose the group you want to analyse
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class feedback_choose_group_form extends moodleform {
    var $feedbackdata;

    function definition() {
        $this->feedbackdata = new stdClass();
        //this function can not be called, because not all data are available at this time
        //I use set_form_elements instead
    }

    //this function set the data used in set_form_elements()
    //in this form the only value have to set is course
    //eg: array('course' => $course)
    function set_feedbackdata($data) {
        if(is_array($data)) {
            foreach($data as $key => $val) {
                $this->feedbackdata->{$key} = $val;
            }
        }
    }

    //here the elements will be set
    //this function have to be called manually
    //the advantage is that the data are already set
    function set_form_elements(){
        $mform =& $this->_form;

        $elementgroup = array();
        //headline
        // $mform->addElement('header', 'general', get_string('choose_group', 'feedback'));
        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'do_show');
        $mform->setType('do-show', PARAM_INT);

        // visible elements
        $groups_options = array();
        if(isset($this->feedbackdata->groups)){
            $groups_options['-1'] = get_string('allgroups');
            foreach($this->feedbackdata->groups as $group) {
                $groups_options[$group->id] = $group->name;
            }
        }
        $attributes = 'onChange="this.form.submit()"';
        $elementgroup[] =& $mform->createElement('select', 'lstgroupid', '', $groups_options, $attributes);
        // buttons
        $elementgroup[] =& $mform->createElement('submit', 'switch_group', get_string('switch_group', 'feedback'));
        $mform->addGroup($elementgroup, 'elementgroup', '', array(' '), false);

//-------------------------------------------------------------------------------
    }
}

