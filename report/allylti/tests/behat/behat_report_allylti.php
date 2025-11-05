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
 * Ally lti report context
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use \Behat\Mink\Exception\ExpectationException;
use \Behat\Mink\Element\NodeElement;
use \Moodle\BehatExtension\Exception\SkippedException;
use \tool_ally\local_content;
use \tool_ally\models\component_content;

/**
 * Ally filter context
 *
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category  test
 * @package   filter_ally
 */
class behat_report_allylti extends behat_base {

    private function findnofail(string $selector, string $locator) {
        try {
            $node = $this->find($selector, $locator);
        } catch (ExpectationException $e) {
            return false;
        }
        return $node;
    }

    /**
     * @Given I navigate to the course accessibility report
     */
    public function navigate_ax_report() {
        if ($this->findnofail('xpath', '//div[@id="settingsnav"]')) {
            $node = $this->find('xpath', '//div[@id="settingsnav"]//ul//li//p//a[text()="Reports"]');
            $node->click();
        }

        $linkselector = '//a[contains(@href, "report/allylti/launch.php?reporttype=course")]';
        $node = $this->find('xpath', $linkselector);
        $node->click();
    }

    /**
     * Switches to the newly opened tab/window. Useful when you do not know name of window/tab.
     *
     * @Given /^I switch to the new window$/
     */
    public function switch_to_the_new_window() {
        $windownames = $this->getSession()->getWindowNames();
        if (count($windownames) > 1) {
            $this->getSession()->switchToWindow(end($windownames));
        } else {
            throw new Exception('Only one tab/window available.');
        }
    }
}
