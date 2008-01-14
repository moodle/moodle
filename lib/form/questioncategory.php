<?php
/**
 * A moodle form field type for question categories.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodleforms
 *//** */

global $CFG;
require_once("$CFG->libdir/form/select.php");

/**
 * HTML class for a drop down element to select a question category.
 * @access public
 */
class MoodleQuickForm_questioncategory extends MoodleQuickForm_select {

    /**
     * Constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     * @param array $options additional options. Recognised options are courseid, published and
     * only_editable, corresponding to the arguments of question_category_options from moodlelib.php.
     * @access public
     * @return void
     */
    function MoodleQuickForm_questioncategory($elementName = null,
            $elementLabel = null, $attributes = null, $options = null) {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes, null);

        global $COURSE;
        $this->_type = 'questioncategory';
        if (!empty($options['courseid'])) {
            $this->_courseid = $options['courseid'];
        } else {
            $this->_courseid = $COURSE->id;
        }
        if (!empty($options['published'])) {
            $this->_published = $options['published'];
        } else {
            $this->_published = false;
        }
        if (!empty($options['only_editable'])) {
            $this->_only_editable = $options['only_editable'];
        } else {
            $this->_only_editable = false;
        }
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @access public
     * @return mixed
     */
    function onQuickFormEvent($event, $arg, &$caller) {
        switch ($event) {
            case 'createElement':
                $this->load(question_category_options($this->_courseid, $this->_published, $this->_only_editable));
            break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }
}
?>