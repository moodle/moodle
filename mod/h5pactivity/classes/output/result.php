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
 * Contains class mod_h5pactivity\output\result
 *
 * @package   mod_h5pactivity
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class to display an attempt tesult in mod_h5pactivity.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class result implements renderable, templatable {

    /** Correct answer state. */
    const CORRECT = 1;

    /** Incorrect answer state. */
    const INCORRECT = 2;

    /** Checked answer state. */
    const CHECKED = 3;

    /** Unchecked answer state. */
    const UNCHECKED = 4;

    /** Pass answer state. */
    const PASS = 5;

    /** Pass answer state. */
    const FAIL = 6;

    /** Unknown answer state. */
    const UNKNOWN = 7;

    /** Text answer state. */
    const TEXT = 8;

    /** @var stdClass result record */
    protected $result;

    /** @var mixed additional decoded data */
    protected $additionals;

    /** @var mixed response decoded data */
    protected $response;

    /** @var mixed correctpattern decoded data */
    protected $correctpattern = [];

    /**
     * Constructor.
     *
     * @param stdClass $result a h5pactivity_attempts_results record
     */
    protected function __construct(stdClass $result) {
        $this->result = $result;
        if (empty($result->additionals)) {
            $this->additionals = new stdClass();
        } else {
            $this->additionals = json_decode($result->additionals);
        }
        $this->response = $this->decode_response($result->response);
        if (!empty($result->correctpattern)) {
            $correctpattern = json_decode($result->correctpattern);
            foreach ($correctpattern as $pattern) {
                $this->correctpattern[] = $this->decode_response($pattern);
            }
        }
    }

    /**
     * return the correct result output depending on the interactiontype
     *
     * @param stdClass $result h5pactivity_attempts_results record
     * @return result|null the result output class if any
     */
    public static function create_from_record(stdClass $result): ?self {
        // Compound result track is omitted from the report.
        if ($result->interactiontype == 'compound') {
            return null;
        }
        $classname = "mod_h5pactivity\\output\\result\\{$result->interactiontype}";
        $classname = str_replace('-', '', $classname);
        if (class_exists($classname)) {
            return new $classname($result);
        }
        return new self($result);
    }

    /**
     * Return a decoded response structure.
     *
     * @param string $value the current response structure
     * @return array an array of reponses
     */
    private function decode_response(string $value): array {
        // If [,] means a list of elements.
        $list = explode('[,]', $value);
        // Inside a list element [.] means sublist (pair) and [:] a range.
        foreach ($list as $key => $item) {
            if (strpos($item, '[.]') !== false) {
                $list[$key] = explode('[.]', $item);
            } else if (strpos($item, '[:]') !== false) {
                $list[$key] = explode('[:]', $item);
            }
        }
        return $list;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $result = $this->result;

        $data = (object)[
            'id' => $result->id,
            'attemptid' => $result->attemptid,
            'subcontent' => $result->subcontent,
            'timecreated' => $result->timecreated,
            'interactiontype' => $result->interactiontype,
            'description' => strip_tags($result->description),
            'rawscore' => $result->rawscore,
            'maxscore' => $result->maxscore,
            'duration' => $result->duration,
            'completion' => $result->completion,
            'success' => $result->success,
        ];
        $result;

        $options = $this->export_options();

        if (!empty($options)) {
            $data->hasoptions = true;
            $data->optionslabel = $this->get_optionslabel();
            $data->correctlabel = $this->get_correctlabel();
            $data->answerlabel = $this->get_answerlabel();
            $data->options = array_values($options);
            $data->track = true;
        }

        if (!empty($result->maxscore)) {
            $data->score = get_string('score_out_of', 'mod_h5pactivity', $result);
        }
        return $data;
    }

    /**
     * Return the options data structure.
     *
     * Result types have to override this method generate a specific options report.
     *
     * An option is an object with:
     *   - id: the option ID
     *   - description: option description text
     *   - useranswer (optional): what the user answer (see get_answer method)
     *   - correctanswer (optional): the correct answer (see get_answer method)
     *
     * @return array of options
     */
    protected function export_options(): ?array {
        return [];
    }

    /**
     * Return a label for result user options/choices.
     *
     * Specific result types can override this method to customize
     * the result options table header.
     *
     * @return string to use in options table
     */
    protected function get_optionslabel(): string {
        return get_string('choice', 'mod_h5pactivity');
    }

    /**
     * Return a label for result user correct answer.
     *
     * Specific result types can override this method to customize
     * the result options table header.
     *
     * @return string to use in options table
     */
    protected function get_correctlabel(): string {
        return get_string('correct_answer', 'mod_h5pactivity');
    }

    /**
     * Return a label for result user attempt answer.
     *
     * Specific result types can override this method to customize
     * the result options table header.
     *
     * @return string to use in options table
     */
    protected function get_answerlabel(): string {
        return get_string('attempt_answer', 'mod_h5pactivity');
    }

    /**
     * Extract descriptions from array.
     *
     * @param array $data additional attribute to parse
     * @return string[] the resulting strings
     */
    protected function get_descriptions(array $data): array {
        $result = [];
        foreach ($data as $key => $value) {
            $description = $this->get_description($value);
            $index = $value->id ?? $key;
            $index = trim($index);
            if (is_numeric($index)) {
                $index = intval($index);
            }
            $result[$index] = (object)['description' => $description, 'id' => $index];
        }
        ksort($result);
        return $result;
    }

    /**
     * Extract description from data element.
     *
     * @param stdClass $data additional attribute to parse
     * @return string the resulting string
     */
    protected function get_description(stdClass $data): string {
        if (!isset($data->description)) {
            return '';
        }
        $translations = (array) $data->description;
        if (empty($translations)) {
            return '';
        }
        // By default, H5P packages only send "en-US" descriptions.
        $result = $translations['en-US'] ?? array_shift($translations);
        return trim($result);
    }

    /**
     * Return an answer data to show results.
     *
     * @param int $state the answer state
     * @param string $answer the extra text to display (default null)
     * @return stdClass with "answer" text and the state attribute to be displayed
     */
    protected function get_answer(int $state, ?string $answer = null): stdClass {
        $states = [
            self::CORRECT => 'correct',
            self::INCORRECT => 'incorrect',
            self::CHECKED => 'checked',
            self::UNCHECKED => 'unchecked',
            self::PASS => 'pass',
            self::FAIL => 'fail',
            self::UNKNOWN => 'unknown',
            self::TEXT => 'text',
        ];
        $state = $states[$state] ?? self::UNKNOWN;
        if ($answer === null) {
            $answer = get_string('answer_'.$state, 'mod_h5pactivity');
        }
        $result = (object)[
            'answer' => $answer,
            $state => true,
        ];
        return $result;
    }
}
