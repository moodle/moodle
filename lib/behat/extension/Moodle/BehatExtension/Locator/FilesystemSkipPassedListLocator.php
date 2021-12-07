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
 * Skips gherkin features using a file with the list of scenarios.
 *
 * @copyright  2016 onwards Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moodle\BehatExtension\Locator;

use Behat\Behat\Gherkin\Specification\LazyFeatureIterator;
use Behat\Gherkin\Gherkin;
use Behat\Testwork\Specification\Locator\SpecificationLocator;
use Behat\Testwork\Specification\NoSpecificationsIterator;
use Behat\Testwork\Suite\Suite;

/**
 * Skips gherkin features using a file with the list of scenarios.
 *
 * @copyright  2016 onwards Rajesh Taneja
 */
final class FilesystemSkipPassedListLocator implements SpecificationLocator {
    /**
     * @var Gherkin
     */
    private $gherkin;

    /**
     * Initializes locator.
     *
     * @param Gherkin $gherkin
     */
    public function __construct(Gherkin $gherkin) {
        $this->gherkin = $gherkin;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocatorExamples() {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function locateSpecifications(Suite $suite, $locator) {
        if (!is_file($locator) || 'passed' !== pathinfo($locator, PATHINFO_EXTENSION)) {
            return new NoSpecificationsIterator($suite);
        }

        $scenarios = json_decode(trim(file_get_contents($locator)), true);
        if (empty($scenarios) || empty($scenarios[$suite->getName()])) {
            return new NoSpecificationsIterator($suite);
        }

        $suitepaths = $this->getSuitePaths($suite);

        $scenarios = array_diff($suitepaths, array_values($scenarios[$suite->getName()]));

        return new LazyFeatureIterator($suite, $this->gherkin, $scenarios);
    }

    /**
     * Returns array of feature paths configured for the provided suite.
     *
     * @param Suite $suite
     *
     * @return string[]
     *
     * @throws SuiteConfigurationException If `paths` setting is not an array
     */
    private function getSuitePaths(Suite $suite) {
        if (!is_array($suite->getSetting('paths'))) {
            throw new SuiteConfigurationException(
                sprintf('`paths` setting of the "%s" suite is expected to be an array, %s given.',
                    $suite->getName(),
                    gettype($suite->getSetting('paths'))
                ),
                $suite->getName()
            );
        }

        return $suite->getSetting('paths');
    }
}
