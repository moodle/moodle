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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\form;

use coding_exception;
use MoodleQuickForm_autocomplete;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/autocomplete.php');
// require_once($CFG->libdir . '/formslib.php');

class groupform_autocomplete extends MoodleQuickForm_autocomplete {

    /** @var bool Only visible frameworks? */
    protected $onlyvisible = false;

    /**
     * Constructor.
     *
     * @param string $elementname Element name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display
     *        Valid options are:
     *        - context context The context.
     *        - contextid int The context id.
     *        - multiple bool Whether or not the field accepts more than one values.
     *        - onlyvisible bool Whether or not only visible framework can be listed.
     */
    public function __construct($elementname = null, $elementlabel = null, $options = array()) {

        $contextid = null;
        if (!empty($options['contextid'])) {
            $contextid = $options['contextid'];
        } else if (!empty($options['context'])) {
            $contextid = $options['context']->id;
        } else {
            $context = \context_system::instance();
            $contextid = $context->id;
        }

        $this->onlyvisible = !empty($options['onlyvisible']);

        $validattributes = array(
            'ajax' => 'block_lsuxe/destcourse_source',
            'extended' => true,
            'multiple' => false,
            // 'noselectionstring' => ""
            'data-contextid' => $contextid,
            'data-onlyvisible' => $this->onlyvisible ? '1' : '0',
            'class' => 'xe_dest_course_auto'
            // Example of callback from here: https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#autocomplete
            // 'valuehtmlcallback' => function($value) {
            //     global $DB, $OUTPUT;
            //     $user = $DB->get_record('user', ['id' => (int)$value], '*', IGNORE_MISSING);
            //     if (!$user || !user_can_view_profile($user)) {
            //         return false;
            //     }
            //     $details = user_get_user_details($user);
            //     return $OUTPUT->render_from_template(
            //             'core_search/form-user-selector-suggestion', $details);
            // }
        );
        if (!empty($options['multiple'])) {
            $validattributes['multiple'] = 'multiple';
        }

        parent::__construct($elementname, $elementlabel, array(), $validattributes);
    }
}
