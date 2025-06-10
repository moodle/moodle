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

declare(strict_types=1);

namespace mod_adaptivequiz\output;

use renderable;
use renderer_base;
use templatable;

/**
 * Output object to display the number of questions answered out of total question number through an attempt.
 *
 * @package   mod_adaptivequiz
 * @copyright 2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_progress implements renderable, templatable {

    /**
     * @var int $questionsanswered
     */
    private $questionsanswered;

    /**
     * @var int $maximumquestions
     */
    private $maximumquestions;

    /**
     * @var bool $showprogressbar Whether a progress should be depicted as a filling bar. True by default.
     */
    private $showprogressbar;

    /**
     * @var string|null $helpicon Already rendered markup for the help icon if needed.
     */
    private $helpiconcontent;

    /**
     * The constructor. See the related class properties.
     *
     * @param int $questionsanswered
     * @param int $maximumquestions
     * @param bool $showprogressbar
     * @param string|null $helpiconcontent
     */
    public function __construct(int $questionsanswered, int $maximumquestions, bool $showprogressbar, ?string $helpiconcontent) {
        $this->questionsanswered = $questionsanswered;
        $this->maximumquestions = $maximumquestions;
        $this->showprogressbar = $showprogressbar;
        $this->helpiconcontent = $helpiconcontent;
    }

    /**
     * Returns an object of the same class with modified property.
     */
    public function without_progress_bar(): self {
        return new self($this->questionsanswered, $this->maximumquestions, false, $this->helpiconcontent);
    }

    /**
     * Returns an object of the same class with modified property.
     *
     * @param string $helpiconcontent See the related class property.
     */
    public function with_help_icon_content(string $helpiconcontent): self {
        return new self($this->questionsanswered, $this->maximumquestions, $this->showprogressbar, $helpiconcontent);
    }

    /**
     * Exports the renderer data in a format that is suitable for a Mustache template.
     *
     * @param renderer_base $output
     */
    public function export_for_template(renderer_base $output): array {
        $fortemplate = [
            'questionsanswerednumber' => $this->questionsanswered,
            'maximumquestionsnumber' => $this->maximumquestions,
        ];

        if ($this->showprogressbar) {
            $fortemplate['showprogressbar'] = true;
            $fortemplate['percentprogressbarfilled'] = floor($this->questionsanswered / $this->maximumquestions * 100);
        }

        $fortemplate['helpiconcontent'] = $this->helpiconcontent;

        return $fortemplate;
    }

    /**
     * A named constructor to instantiate an object from minimal data.
     *
     * @param int $questionsanswered
     * @param int $maximumquestions
     */
    public static function with_defaults(int $questionsanswered, int $maximumquestions): self {
        return new self($questionsanswered, $maximumquestions, true, null);
    }
}
