<?php  // $Id$
/**
 * Form to define a new instance of lesson or edit an instance.
 * It is used from /course/modedit.php.
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once('locallib.php');

class mod_lesson_mod_form extends moodleform_mod {

    function definition() {
        global $LESSON_NEXTPAGE_ACTION, $COURSE;

        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('selectyesno', 'timed', get_string('timed', 'lesson'));
        $mform->setDefault('timed', 0);
        $mform->setHelpButton('timed', array('timed', get_string('timed', 'lesson'), 'lesson'));

        $mform->addElement('text', 'maxtime', get_string('maxtime', 'lesson'));
        $mform->setDefault('maxtime', 20);
        $mform->addRule('maxtime', null, 'required', null, 'client');
        $mform->addRule('maxtime', null, 'numeric', null, 'client');
        $mform->setType('maxtime', PARAM_INT);

        $numbers = array();
        for ($i=20; $i>1; $i--) {
            $numbers[$i] = $i;
        }
        $mform->addElement('select', 'maxanswers', get_string('maximumnumberofanswersbranches', 'lesson'), $numbers);
        $mform->setDefault('maxanswers', 4);
        $mform->setHelpButton('maxanswers', array('maxanswers', get_string('displayformat', 'lesson'), 'lesson'));

//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('gradeoptions', 'lesson'));

        $mform->addElement('selectyesno', 'practice', get_string('practice', 'lesson'));
        $mform->setHelpButton('practice', array('practice', get_string('practice', 'lesson'), 'lesson'));
        $mform->setDefault('practice', 0);

        $mform->addElement('selectyesno', 'custom', get_string('customscoring', 'lesson'));
        $mform->setHelpButton('custom', array('custom', get_string('customscoring', 'lesson'), 'lesson'));
        $mform->setDefault('custom', 1);

        $grades = array();
        for ($i=100; $i>=0; $i--) {
            $grades[$i] = $i;
        }
        $mform->addElement('select', 'grade', get_string('maximumgrade'), $grades);
        $mform->setDefault('grade', 0);
        $mform->setHelpButton('grade', array('grade', get_string('maximumgrade', 'lesson'), 'lesson'));

        $mform->addElement('selectyesno', 'retake', get_string('canretake', 'lesson', $COURSE->student));
        $mform->setHelpButton('retake', array('retake', get_string('canretake', 'lesson', $COURSE->student), 'lesson'));
        $mform->setDefault('retake', 0);

        $options = array();
        $options[0] = get_string('usemean', 'lesson');
        $options[1] = get_string('usemaximum', 'lesson');
        $mform->addElement('select', 'usemaxgrade', get_string('handlingofretakes', 'lesson'), $options);
        $mform->setHelpButton('usemaxgrade', array('handlingofretakes', get_string('handlingofretakes', 'lesson'), 'lesson'));
        $mform->setDefault('usemaxgrade', 0);

        $mform->addElement('selectyesno', 'ongoing', get_string('ongoing', 'lesson'));
        $mform->setHelpButton('ongoing', array('ongoing', get_string('ongoing', 'lesson'), 'lesson'));
        $mform->setDefault('ongoing', 0);

//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('flowcontrol', 'lesson'));

        $mform->addElement('selectyesno', 'modattempts', get_string('modattempts', 'lesson'));
        $mform->setHelpButton('modattempts', array('modattempts', get_string('modattempts', 'lesson'), 'lesson'));
        $mform->setDefault('modattempts', 0);

        $mform->addElement('selectyesno', 'review', get_string('displayreview', 'lesson'));
        $mform->setHelpButton('review', array('review', get_string('displayreview', 'lesson'), 'lesson'));
        $mform->setDefault('review', 0);

        $numbers = array();
        for ($i=10; $i>0; $i--) {
            $numbers[$i] = $i;
        }
        $mform->addElement('select', 'maxattempts', get_string('maximumnumberofattempts', 'lesson'), $numbers);
        $mform->setHelpButton('maxattempts', array('maxattempts', get_string('maximumnumberofattempts', 'lesson'), 'lesson'));
        $mform->setDefault('maxattempts', 1);

        $mform->addElement('select', 'nextpagedefault', get_string('actionaftercorrectanswer', 'lesson'), $LESSON_NEXTPAGE_ACTION);
        $mform->setHelpButton('nextpagedefault', array('nextpageaction', get_string('actionaftercorrectanswer', 'lesson'), 'lesson'));
        $mform->setDefault('nextpagedefault', 0);

        $mform->addElement('selectyesno', 'feedback', get_string('displaydefaultfeedback', 'lesson'));
        $mform->setHelpButton('feedback', array('feedback', get_string('displaydefaultfeedback', 'lesson'), 'lesson'));
        $mform->setDefault('feedback', 0);

        $numbers = array();
        for ($i = 100; $i >= 0; $i--) {
            $numbers[$i] = $i;
        }
        $mform->addElement('select', 'minquestions', get_string('minimumnumberofquestions', 'lesson'), $numbers);
        $mform->setHelpButton('minquestions', array('minquestions', get_string('minimumnumberofquestions', 'lesson'), 'lesson'));
        $mform->setDefault('minquestions', 0);

        $numbers = array();
        for ($i = 100; $i >= 0; $i--) {
            $numbers[$i] = $i;
        }
        $mform->addElement('select', 'maxpages', get_string('numberofpagestoshow', 'lesson'), $numbers);
        $mform->setHelpButton('maxpages', array('maxpages', get_string('numberofpagestoshow', 'lesson'), 'lesson'));
        $mform->setDefault('maxpages', 0);

//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('lessonformating', 'lesson'));

        $mform->addElement('selectyesno', 'slideshow', get_string('slideshow', 'lesson'));
        $mform->setHelpButton('slideshow', array('slideshow', get_string('slideshow', 'lesson'), 'lesson'));
        $mform->setDefault('slideshow', 0);

        $mform->addElement('text', 'width', get_string('slideshowwidth', 'lesson'));
        $mform->setDefault('width', 640);
        $mform->addRule('width', null, 'required', null, 'client');
        $mform->addRule('width', null, 'numeric', null, 'client');
        $mform->setHelpButton('width', array('width', get_string('slideshowwidth', 'lesson'), 'lesson'));
        $mform->setType('width', PARAM_INT);

        $mform->addElement('text', 'height', get_string('slideshowheight', 'lesson'));
        $mform->setDefault('height', 480);
        $mform->addRule('height', null, 'required', null, 'client');
        $mform->addRule('height', null, 'numeric', null, 'client');
        $mform->setHelpButton('height', array('height', get_string('slideshowheight', 'lesson'), 'lesson'));
        $mform->setType('height', PARAM_INT);

        $mform->addElement('text', 'bgcolor', get_string('slideshowbgcolor', 'lesson'));
        $mform->setDefault('bgcolor', '#FFFFFF');
        $mform->addRule('bgcolor', null, 'required', null, 'client');
        $mform->setHelpButton('bgcolor', array('bgcolor', get_string('slideshowbgcolor', 'lesson'), 'lesson'));
        $mform->setType('bgcolor', PARAM_TEXT);

        $mform->addElement('selectyesno', 'displayleft', get_string('displayleftmenu', 'lesson'));
        $mform->setHelpButton('displayleft', array('displayleft', get_string('displayleftmenu', 'lesson'), 'lesson'));
        $mform->setDefault('displayleft', 0);

        $options = array();
        for($i = 100; $i >= 0; $i--) {
            $options[$i] = $i.'%';
        }
        $mform->addElement('select', 'displayleftif', get_string('displayleftif', 'lesson'), $options);
        $mform->setDefault('displayleftif', 0);

        $mform->addElement('selectyesno', 'progressbar', get_string('progressbar', 'lesson'));
        $mform->setHelpButton('progressbar', array('progressbar', get_string('progressbar', 'lesson'), 'lesson'));
        $mform->setDefault('progressbar', 0);


//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('accesscontrol', 'lesson'));

        $mform->addElement('selectyesno', 'usepassword', get_string('usepassword', 'lesson'));
        $mform->setHelpButton('usepassword', array('usepassword', get_string('usepassword', 'lesson'), 'lesson'));
        $mform->setDefault('usepassword', 0);

        $mform->addElement('text', 'password', get_string('password', 'lesson'));
        $mform->setHelpButton('password', array('password', get_string('password', 'lesson'), 'lesson'));
        $mform->setDefault('password', '');
        //never displayed converted to md5
        $mform->setType('password', PARAM_RAW);

        $mform->addElement('date_time_selector', 'available', get_string('available', 'lesson'));
        $mform->setDefault('available', 0);

        $mform->addElement('date_time_selector', 'deadline', get_string('deadline', 'lesson'));
        $mform->setDefault('deadline', 0);

//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('dependencyon', 'lesson'));

        $options = array(0=>get_string('none'));
        if ($lessons = get_all_instances_in_course('lesson', $COURSE)) {
            foreach($lessons as $lesson) {
                if ($lesson->id != $this->_instance){
                    $options[$lesson->id] = format_string($lesson->name, true);
                }

            }
        }
        $mform->addElement('select', 'dependency', get_string('dependencyon', 'lesson'), $options);
        $mform->setHelpButton('dependency', array('dependency', get_string('dependency', 'lesson'), 'lesson'));
        $mform->setDefault('dependency', 0);

        $mform->addElement('text', 'timespent', get_string('timespentminutes', 'lesson'));
        $mform->setDefault('timespent', 0);
        $mform->setType('timespent', PARAM_INT);

        $mform->addElement('checkbox', 'completed', get_string('completed', 'lesson'));
        $mform->setDefault('completed', 0);

        $mform->addElement('text', 'gradebetterthan', get_string('gradebetterthan', 'lesson'));
        $mform->setDefault('gradebetterthan', 0);
        $mform->setType('gradebetterthan', PARAM_INT);

//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('mediafile', 'lesson'));

        $mform->addElement('choosecoursefile', 'mediafile', get_string('mediafile', 'lesson'), array('courseid'=>$COURSE->id));
        $mform->setHelpButton('mediafile', array('mediafile', get_string('mediafile', 'lesson'), 'lesson'));
        $mform->setDefault('mediafile', '');
        $mform->setType('mediafile', PARAM_RAW);

        $mform->addElement('selectyesno', 'mediaclose', get_string('mediaclose', 'lesson'));
        $mform->setDefault('mediaclose', 0);

        $mform->addElement('text', 'mediaheight', get_string('mediaheight', 'lesson'));
        $mform->setHelpButton('mediaheight', array('mediaheight', get_string('mediaheight', 'lesson'), 'lesson'));
        $mform->setDefault('mediaheight', 100);
        $mform->addRule('mediaheight', null, 'required', null, 'client');
        $mform->addRule('mediaheight', null, 'numeric', null, 'client');
        $mform->setType('mediaheight', PARAM_INT);

        $mform->addElement('text', 'mediawidth', get_string('mediawidth', 'lesson'));
        $mform->setHelpButton('mediawidth', array('mediawidth', get_string('mediawidth', 'lesson'), 'lesson'));
        $mform->setDefault('mediawidth', 650);
        $mform->addRule('mediawidth', null, 'required', null, 'client');
        $mform->addRule('mediawidth', null, 'numeric', null, 'client');
        $mform->setType('mediawidth', PARAM_INT);

//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('other', 'lesson'));

        // get the modules
        if ($mods = get_course_mods($COURSE->id)) {
            $modinstances = array();
            foreach ($mods as $mod) {

                // get the module name and then store it in a new array
                if ($module = get_coursemodule_from_instance($mod->modname, $mod->instance, $COURSE->id)) {
                    if (isset($this->_cm->id) and $this->_cm->id != $mod->id){
                        $modinstances[$mod->id] = $mod->modname.' - '.$module->name;
                    }
                }
            }
            asort($modinstances); // sort by module name
            $modinstances=array(0=>get_string('none'))+$modinstances;

            $mform->addElement('select', 'activitylink', get_string('activitylink', 'lesson'), $modinstances);
            $mform->setHelpButton('activitylink', array('activitylink', get_string('activitylink', 'lesson'), 'lesson'));
            $mform->setDefault('activitylink', 0);

        }

        $mform->addElement('text', 'maxhighscores', get_string('maxhighscores', 'lesson'));
        $mform->setHelpButton('maxhighscores', array('maxhighscores', get_string('maxhighscores', 'lesson'), 'lesson'));
        $mform->setDefault('maxhighscores', 10);
        $mform->addRule('maxhighscores', null, 'required', null, 'client');
        $mform->addRule('maxhighscores', null, 'numeric', null, 'client');
        $mform->setType('maxhighscores', PARAM_INT);

        $mform->addElement('selectyesno', 'lessondefault', get_string('lessondefault', 'lesson'));
        $mform->setHelpButton('lessondefault', array('lessondefault', get_string('lessondefault', 'lesson'), 'lesson'));
        $mform->setDefault('lessondefault', 0);

//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements(false);
//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    /**
     * Enforce defaults here
     *
     * @param array $default_values Form defaults
     * @return void
     **/
    function data_preprocessing(&$default_values) {
        if (isset($default_values['conditions'])) {
            $conditions = unserialize($default_values['conditions']);
            $default_values['timespent'] = $conditions->timespent;
            $default_values['completed'] = $conditions->completed;
            $default_values['gradebetterthan'] = $conditions->gradebetterthan;
        }
        if (isset($default_values['password'])) {
            unset($default_values['password']);
        }
        if (isset($default_values['add']) and $defaults = get_record('lesson_default', 'course', $default_values['course'])) {
            foreach ($defaults as $fieldname => $default) {
                switch ($fieldname) {
                    case 'conditions':
                        $conditions = unserialize($default);
                        $default_values['timespent'] = $conditions->timespent;
                        $default_values['completed'] = $conditions->completed;
                        $default_values['gradebetterthan'] = $conditions->gradebetterthan;
                        break;
                    default:
                        $default_values[$fieldname] = $default;
                        break;
                }
            }
        }
    }
}
?>
