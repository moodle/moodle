<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Drop down for question categories.
 *
 * Contains HTML class for a drop down element to select a question category.
 *
 * @package   core_form
 * @copyright 2007 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once("$CFG->libdir/form/selectgroups.php");
require_once("$CFG->libdir/questionlib.php");

/**
 * Drop down for question categories.
 *
 * HTML class for a drop down element to select a question category.
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_questioncategory extends MoodleQuickForm_selectgroups {
    /** @var array default options for question categories */
    var $_options = array('top'=>false, 'currentcat'=>0, 'nochildrenof' => -1);

    /**
     * Constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param array $options additional options. Recognised options are courseid, published and
     *              only_editable, corresponding to the arguments of question_category_options
     *              from moodlelib.php.
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
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
