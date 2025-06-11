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
 * Steps definitions for behat theme.
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2019 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Check for color setup in categories
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2019 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_custom_elements extends behat_base {

    /**
     * Checks if classes are loaded to body on pages.
     *
     * @Given /^I wait until "(?P<custom_element_string>(?:[^"]|\\")*)" custom element is registered$/
     * @param string $classes classes separated by comma
     * @throws Exception
     */
    public function i_wait_for_custom_element_registry($customelement) {
        // No need to wait if not running JS.
        if (!$this->running_javascript()) {
            return;
        }
        // Wait for custom element to be registered.
        $this->wait_for_js_condition("customElements !== undefined && customElements.get(\"{$customelement}\") !== undefined");
    }

    /**
     * Wait
     *
     * @param integer $time
     * @param string  $condition
     *
     * @throws ExpectationException If timeout is reached
     */
    public function wait_for_js_condition($condition = null, $time = 10000) {
        if (!$this->getSession()->getDriver() instanceof Selenium2Driver) {
            return;
        }
        $start = microtime(true);
        $end = $start + $time / 1000.0;
        if ($condition === null) {
            $defaultcondition = true;
            $conditions = [
                "document.readyState == 'complete'",           // Page is ready.
                "typeof $ != 'undefined'",                     // Here jQuery is loaded.
                "!$.active",                                   // No ajax request is active.
                "$('#page').css('display') == 'block'",        // Page is displayed (no progress bar).
                "$('.loading-mask').css('display') == 'none'", // Page is not loading (no black mask loading page).
                "$('.jstree-loading').length == 0",            // Jstree has finished loading.
            ];
            $condition = implode(' && ', $conditions);
        } else {
            $defaultcondition = false;
        }
        // Make sure the AJAX calls are fired up before checking the condition.
        $this->getSession()->wait(100, false);
        $this->getSession()->wait($time, $condition);
        // Check if we reached the timeout unless the condition is false to explicitly wait the specified time.
        if ($condition !== false && microtime(true) > $end) {
            if ($defaultcondition) {
                foreach ($conditions as $condition) {
                    $result = $this->getSession()->evaluateScript($condition);
                    if (!$result) {
                        throw new ExpectationException(
                            sprintf(
                                'Timeout of %d reached when checking on "%s"',
                                $time,
                                $condition
                            )
                        );
                    }
                }
            } else {
                throw new ExpectationException(sprintf('Timeout of %d reached when checking on %s', $time, $condition));
            }
        }
    }

}
