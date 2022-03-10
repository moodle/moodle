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

namespace Moodle\BehatExtension\Context\Step;

use Behat\Gherkin\Node\StepNode;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Chained Step base class.
 *
 * @package    core
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class ChainedStep extends StepNode {
    /**
     * @var string
     */
    private $language;

    // phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
    /**
     * Initializes ChainedStep.
     *
     * @param string $keyword
     * @param string $text
     * @param array  $arguments
     * @param int $line
     * @param string $keywordtype
     */
    public function __construct($keyword, $text, array $arguments, $line = 0, $keywordtype = 'Given') {
        parent::__construct($keyword, $text, $arguments, $line, $keywordtype);
    }
    // phpcs:enable Generic.CodeAnalysis.UselessOverridingMethod.Found

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
