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
 * Defines the editing form for random questions.
 *
 * @package    mod_quiz
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class randomquestion_form
 *
 * @package    mod_quiz
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class randomquestion_form extends \moodleform {

    /**
     * Form definiton.
     */
    public function definition() {
        $mform = $this->_form;

        $contexts = $this->_customdata['contexts'];
        $usablecontexts = $contexts->having_cap('moodle/question:useall');

        // Standard fields at the start of the form.
        $mform->addElement('header', 'generalheader', get_string("general", 'form'));

        $mform->addElement('questioncategory', 'category', get_string('category', 'question'),
                array('contexts' => $usablecontexts, 'top' => true));

        $mform->addElement('advcheckbox', 'includesubcategories', get_string('recurse', 'quiz'), null, null, array(0, 1));

        $tops = question_get_top_categories_for_contexts(array_column($contexts->all(), 'id'));
        $mform->hideIf('includesubcategories', 'category', 'in', $tops);

        $tags = \core_tag_tag::get_tags_by_area_in_contexts('core_question', 'question', $usablecontexts);
        $tagstrings = array();
        foreach ($tags as $tag) {
            $tagstrings["{$tag->id},{$tag->name}"] = $tag->name;
        }
        $options = array(
                'multiple' => true,
                'noselectionstring' => get_string('anytags', 'quiz'),
        );
        $mform->addElement('autocomplete', 'fromtags', get_string('randomquestiontags', 'mod_quiz'), $tagstrings, $options);
        $mform->addHelpButton('fromtags', 'randomquestiontags', 'mod_quiz');

        $mform->addElement('hidden', 'slotid');
        $mform->setType('slotid', PARAM_INT);

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    public function set_data($defaultvalues) {
        $mform = $this->_form;

        if ($defaultvalues->fromtags) {
            $fromtagselement = $mform->getElement('fromtags');
            foreach ($defaultvalues->fromtags as $fromtag) {
                if (!$fromtagselement->optionExists($fromtag)) {
                    $optionname = get_string('randomfromunavailabletag', 'mod_quiz', explode(',', $fromtag)[1]);
                    $fromtagselement->addOption($optionname, $fromtag);
                }
            }
        }

        parent::set_data($defaultvalues);
    }
}
