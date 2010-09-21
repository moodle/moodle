<?php
/**
* prints the forms to choose an item-typ to create items and to choose a template to use
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class feedback_edit_add_question_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        //headline
        $mform->addElement('header', 'general', get_string('add_items', 'feedback'));
        // visible elements
        // $feedback_names = feedback_load_feedback_items('mod/feedback/item');
        // $feedback_names_options = array();
        // $feedback_names_options[' '] = get_string('select');
        // foreach($feedback_names as $fn) {
            // $feedback_names_options[$fn] = get_string($fn,'feedback');
        // }
        $feedback_names_options = feedback_load_feedback_items_options();

        $attributes = 'onChange="this.form.submit()"';
        $mform->addElement('select', 'typ', '', $feedback_names_options, $attributes);

        // hidden elements
        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->addElement('hidden', 'position');
        $mform->setType('position', PARAM_INT);
//-------------------------------------------------------------------------------
        // buttons
        $mform->addElement('submit', 'add_item', get_string('add_item', 'feedback'));
    }
}

class feedback_edit_use_template_form extends moodleform {
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
        $mform->addElement('header', '', get_string('using_templates', 'feedback'));
        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // visible elements
        $templates_options = array();
        if($templates = feedback_get_template_list($this->feedbackdata->course)){//get the templates
            $templates_options[' '] = get_string('select');
            foreach($templates as $template) {
                $templates_options[$template->id] = $template->name;
            }
            $attributes = 'onChange="this.form.submit()"';
            $elementgroup[] =& $mform->createElement('select', 'templateid', '', $templates_options, $attributes);
            // buttons
            $elementgroup[] =& $mform->createElement('submit', 'use_template', get_string('use_this_template', 'feedback'));
        }else {
            $mform->addElement('static', 'info', get_string('no_templates_available_yet', 'feedback'));
        }
        $mform->addGroup($elementgroup, 'elementgroup', '', array(' '), false);

    //-------------------------------------------------------------------------------
    }
}

class feedback_edit_create_template_form extends moodleform {
    var $feedbackdata;

    function definition() {
    }

    function data_preprocessing(&$default_values){
        $default_values['templatename'] = '';
    }

    function set_feedbackdata($data) {
        if(is_array($data)) {
            foreach($data as $key => $val) {
                $this->feedbackdata->{$key} = $val;
            }
        }
    }

    function set_form_elements(){
        $mform =& $this->_form;
        // $capabilities = $this->feedbackdata->capabilities;

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'do_show');
        $mform->setType('do_show', PARAM_INT);
        $mform->addElement('hidden', 'savetemplate', 1);
        $mform->setType('savetemplate', PARAM_INT);

        //headline
        $mform->addElement('header', '', get_string('creating_templates', 'feedback'));

        // visible elements
        $elementgroup = array();

        $elementgroup[] =& $mform->createElement('static', 'templatenamelabel', get_string('name', 'feedback'));
        $elementgroup[] =& $mform->createElement('text', 'templatename', get_string('name', 'feedback'), array('size'=>'40', 'maxlength'=>'200'));

        //public templates are currently deactivated
        // if(has_capability('mod/feedback:createpublictemplate', $this->feedbackdata->context)) {
            // $elementgroup[] =& $mform->createElement('checkbox', 'ispublic', get_string('public', 'feedback'), get_string('public', 'feedback'));
        // }

        // buttons
        $elementgroup[] =& $mform->createElement('submit', 'create_template', get_string('save_as_new_template', 'feedback'));
        $mform->addGroup($elementgroup, 'elementgroup', get_string('name', 'feedback'), array(' '), false);

        $mform->setType('templatename', PARAM_TEXT);

//-------------------------------------------------------------------------------
    }
}

