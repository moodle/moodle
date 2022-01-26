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
 * Override step tester to ensure chained steps gets executed.
 *
 * @package    behat
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moodle\BehatExtension\Context\Step;

use Behat\Gherkin\Node\StepNode;
/**
 * Base ChainedStep class.
 */
abstract class ChainedStep extends StepNode {
    /**
     * @var string
     */
    private $language;

    /**
     * Initializes ChainedStep.
     *
     * @param string $type
     * @param string $text
     * @param array  $arguments
     */
    public function __construct($keyword, $text, array $arguments, $line = 0, $keywordType = 'Given') {
        parent::__construct($keyword, $text, $arguments, $line, $keywordType);
    }

    /**
     * Sets language.
     *
     * @param string $language
     */
    public function setLanguage($language) {
        $this->language = $language;
    }

    /**
     * Returns language.
     *
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }
}