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

namespace mod_questionnaire\responsetype\answer;

/**
 * This defines a structured class to hold question answers.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 * @copyright 2019, onwards Poet
 */
class answer {

    // Class properties.

    /** @var int $id The id of the question response data record this applies to. */
    public $id;

    /** @var int $responseid This id of the response record this applies to. */
    public $responseid;

    /** @var int $questionid The id of the question this response applies to. */
    public $questionid;

    /** @var string $content The choiceid of this response (if applicable). */
    public $choiceid;

    /** @var string $value The value of this response (if applicable). */
    public $value;

    /**
     * Answer constructor.
     * @param null $id
     * @param null $responseid
     * @param null $questionid
     * @param null $choiceid
     * @param null $value
     */
    public function __construct($id = null, $responseid = null, $questionid = null, $choiceid = null, $value = null) {
        $this->id = $id;
        $this->responseid = $responseid;
        $this->questionid = $questionid;
        $this->choiceid = $choiceid;
        $this->value = $value;
    }

    /**
     * Create and return an answer object from data.
     *
     * @param \stdClass|array $answerdata The data to load.
     * @return answer
     */
    public static function create_from_data($answerdata) {
        if (!is_array($answerdata)) {
            $answerdata = (array)$answerdata;
        }

        $properties = array_keys(get_class_vars(__CLASS__));
        foreach ($properties as $property) {
            if (!isset($answerdata[$property])) {
                $answerdata[$property] = null;
            }
        }

        return new answer($answerdata['id'], $answerdata['responseid'], $answerdata['questionid'], $answerdata['choiceid'],
            $answerdata['value']);
    }
}
