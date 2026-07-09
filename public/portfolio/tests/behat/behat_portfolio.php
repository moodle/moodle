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

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Steps definitions related to core_portfolio.
 *
 * @package    core_portfolio
 * @copyright  2026 Moodle Pty Ltd <support@moodle.com>
 * @author     2026 Yerai Rodríguez <yerai.rodriguez@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_portfolio extends behat_base {
    /**
     * Configure portfolio plugin, set value for portfolio instance
     *
     * @When /^I set the portfolio instance "(?P<portfolioinstance_string>(?:[^"]|\\")*)" to "(?P<value_string>(?:[^"]|\\")*)"$/
     * @param string $portfolioinstance
     * @param string $value
     */
    public function i_set_the_portfolio_instance_to($portfolioinstance, $value) {
        $rowxpath = "//table[contains(@class, 'generaltable')]//tr//td[contains(text(), '"
            . $portfolioinstance . "')]/following-sibling::td";

        $selectxpath = $rowxpath . '//select';
        $select = $this->find('xpath', $selectxpath);
        $select->selectOption($value);

        if (!$this->running_javascript()) {
            $this->execute(
                'behat_general::i_click_on_in_the',
                [get_string('go'), 'button', $rowxpath, 'xpath_element']
            );
        }
    }
}
