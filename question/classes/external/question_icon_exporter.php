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
 * Class for exporting a question icon from an stdClass.
 *
 * @package    core_question
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_question\external;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/bank.php');

/**
 * Class for exporting a question from an stdClass.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_icon_exporter extends \core\external\exporter {

    /**
     * Constructor.
     *
     * @param \stdClass $question
     * @param array $related The related data.
     */
    public function __construct(\stdClass $question, $related = []) {
        $qtype = \question_bank::get_qtype($question->qtype, false);

        parent::__construct((object) [
            'key' => 'icon',
            'component' => $qtype->plugin_name(),
            'alttext' => $qtype->local_name()
        ], $related);
    }

    /**
     * Set the moodle context as a required related object.
     *
     * @return array Required related objects.
     */
    protected static function define_related() {
        return ['context' => '\\context'];
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'key' => ['type' => PARAM_TEXT],
            'component' => ['type' => PARAM_COMPONENT],
            'alttext' => ['type' => PARAM_TEXT],
        ];
    }
}
