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

namespace core_question\output;

use renderer_base;
use templatable;
use renderable;
use question_bank;

require_once($CFG->dirroot . '/question/engine/bank.php');

/**
 * A UI widget to select other versions of a particular question.
 *
 * It will help plugins to enable version selection in locations like modal, page etc.
 *
 * @package    core_question
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_version_selection implements templatable, renderable {

    /** @var string */
    private $uniqueidentifier;

    /** @var int */
    private $currentselectedquestionid = null;

    /**
     * Constructor.
     *
     * @param string $uniqueidentifier unique identifier for the api usage.
     * @param int $currentlyselectedquestionid selected question id in dropdown.
     */
    protected function __construct(string $uniqueidentifier, int $currentlyselectedquestionid) {
        $this->uniqueidentifier = $uniqueidentifier;
        $this->currentselectedquestionid = $currentlyselectedquestionid;
    }

    /**
     * Set the selected question id for the currently selected question.
     *
     * @param string $uniqueidentifier unique identifier for the api usage.
     * @param int $currentlyselectedquestionid selected question id in dropdown.
     * @return self an instance of this UI widget for the given question.
     */
    public static function make_for_question(string $uniqueidentifier, int $currentlyselectedquestionid): self {
        return new self($uniqueidentifier, $currentlyselectedquestionid);
    }

    /**
     * Export the data for version selection mustache.
     *
     * @param renderer_base $output renderer of the output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $displaydata = [];
        $versionsoptions = question_bank::get_all_versions_of_question($this->currentselectedquestionid);
        foreach ($versionsoptions as $versionsoption) {
            $versionsoption->selected = false;
            $a = new \stdClass();
            $a->version = $versionsoption->version;
            $versionsoption->name = get_string('version_selection', 'core_question', $a);
            if ($versionsoption->questionid == $this->currentselectedquestionid) {
                $versionsoption->selected = true;
            }
            $displaydata[] = $versionsoption;
        }

        return [
            'options' => $displaydata,
            'uniqueidentifier' => $this->uniqueidentifier,
        ];
    }
}
