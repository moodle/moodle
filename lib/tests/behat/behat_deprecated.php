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
 * Steps definitions that will be deprecated in the next releases.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Deprecated behat step definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_deprecated extends behat_base {

    /**
     * Click on the specified element inside a table row containing the specified text.
     *
     * @deprecated since Moodle 2.7 MDL-42627
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     * @see behat_general::i_click_on_in_the()
     *
     * @Given /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<row_text_string>(?:[^"]|\\")*)" table row$/
     * @throws ElementNotFoundException
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     * @param string $tablerowtext The table row text
     */
    public function i_click_on_in_the_table_row($element, $selectortype, $tablerowtext) {

        // Throw an exception if deprecated methods are not allowed otherwise allow it's execution.
        $alternative = 'I click on "' . $this->escape($element) . '" "' . $this->escape($selectortype) .
            '" in the "' . $this->escape($tablerowtext) . '" "table_row"';
        $this->deprecated_message($alternative);

        // The table row container.
        $nocontainerexception = new ElementNotFoundException($this->getSession(), '"' . $tablerowtext . '" row text ');
        $tablerowtext = $this->getSession()->getSelectorsHandler()->xpathLiteral($tablerowtext);
        $rownode = $this->find('xpath', "//tr[contains(., $tablerowtext)]", $nocontainerexception);

        // Looking for the element DOM node inside the specified row.
        list($selector, $locator) = $this->transform_selector($selectortype, $element);
        $elementnode = $this->find($selector, $locator, false, $rownode);
        $elementnode->click();
    }

    /**
     * Goes to notification page ensuring site admin navigation is loaded.
     *
     * Step [I expand "Site administration" node] will ensure that administration menu
     * is opened in both javascript and non-javascript modes.
     *
     * @deprecated since 2.7
     * @todo MDL-42862 This will be deleted in Moodle 2.9
     *
     * @Given /^I go to notifications page$/
     * @return Given[]
     */
    public function i_go_to_notifications_page() {
        $alternative = array(
            'I expand "' . get_string('administrationsite') . '" node',
            'I click on "' . get_string('notifications') . '" "link" in the "'.get_string('administration').'" "block"'
        );
        $this->deprecated_message($alternative);
        return array(
            new Given($alternative[0]),
            new Given($alternative[1]),
        );
    }

    /**
     * Throws an exception if $CFG->behat_usedeprecated is not allowed.
     *
     * @throws Exception
     * @param string|array $alternatives Alternative/s to the requested step
     * @return void
     */
    protected function deprecated_message($alternatives) {
        global $CFG;

        // We do nothing if it is enabled.
        if (!empty($CFG->behat_usedeprecated)) {
            return;
        }

        if (is_scalar($alternatives)) {
            $alternatives = array($alternatives);
        }

        $message = 'Deprecated step, rather than using this step you can:';
        foreach ($alternatives as $alternative) {
            $message .= PHP_EOL . '- ' . $alternative;
        }
        $message .= PHP_EOL . '- Set $CFG->behat_usedeprecated in config.php to allow the use of deprecated steps if you don\'t have any other option';
        throw new Exception($message);
    }

}
