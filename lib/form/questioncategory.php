<?php
/**
 * A moodle form field type for question categories.
 *
 * @copyright Jamie Pratt
 * @author Jamie Pratt
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodleforms
 */

global $CFG;
require_once("$CFG->libdir/form/selectgroups.php");
require_once("$CFG->libdir/questionlib.php");

/**
 * HTML class for a drop down element to select a question category.
 * @access public
 */
class MoodleQuickForm_questioncategory extends MoodleQuickForm_selectgroups {
    var $_options = array('top'=>false, 'currentcat'=>0, 'nochildrenof' => -1);

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
    function MoodleQuickForm_questioncategory($elementName = null, $elementLabel = null, $options = null, $attributes = null) {
        MoodleQuickForm_selectgroups::MoodleQuickForm_selectgroups($elementName, $elementLabel, array(), $attributes);
        $this->_type = 'questioncategory';
        if (is_array($options)) {
            $this->_options = $options + $this->_options;
            $this->loadArrayOptGroups(
                        question_category_options($this->_options['contexts'], $this->_options['top'], $this->_options['currentcat'],
                                                false, $this->_options['nochildrenof']));
        }
    }

}
?>