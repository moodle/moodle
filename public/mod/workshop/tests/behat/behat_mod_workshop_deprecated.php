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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_deprecated_base.php');

/**
 * Deprecated behat steps in Workshop activity plugin.
 *
 * @package    mod_workshop
 * @category   test
 * @copyright  2026 Moodle Pty Ltd <support@moodle.com>
 * @author     2026 Yerai Rodríguez <yerai.rodriguez@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_workshop_deprecated extends behat_deprecated_base {
    /**
     * Configure portfolio plugin, set value for portfolio instance
     *
     * @deprecated since Moodle 5.3, use {@see behat_portfolio::i_set_the_portfolio_instance_to} instead.
     *
     * @When /^I set portfolio instance "(?P<portfolioinstance_string>(?:[^"]|\\")*)" to "(?P<value_string>(?:[^"]|\\")*)"$/
     * @param string $portfolioinstance
     * @param string $value
     * @todo Final deprecation in Moodle 7.0, MDL-79721.
     */
    #[\core\attribute\deprecated(
        replacement: 'behat_portfolio::i_set_the_portfolio_instance_to',
        since: '5.3',
        mdl: 'MDL-89069',
    )]
    public function i_set_portfolio_instance_to($portfolioinstance, $value) {
        $this->deprecated_message('behat_portfolio::i_set_the_portfolio_instance_to');

        $rowxpath = "//table[contains(@class, 'generaltable')]//tr//td[contains(text(), '"
            . $portfolioinstance . "')]/following-sibling::td";

        $selectxpath = $rowxpath . '//select';
        $select = $this->find('xpath', $selectxpath);
        $select->selectOption($value);

        if (!$this->running_javascript()) {
            $this->execute(
                'behat_general::i_click_on_in_the',
                [get_string('go'), "button", $rowxpath, "xpath_element"],
            );
        }
    }
}
