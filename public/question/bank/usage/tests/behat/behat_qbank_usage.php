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
require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../tests/behat/behat_question_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Steps definitions to deal with the usage in question.
 *
 * @package    qbank_usage
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qbank_usage extends behat_question_base {

    /**
     * Looks for a table, then looks for a row that contains the given text.
     * Once it finds the right row, it clicks a link in that row.
     *
     * @When I click :arg1 on the usage column
     * @param string $linkname
     */
    public function i_click_on_the_usage_column($linkname) {
        $exception = new ElementNotFoundException($this->getSession(),
            'Cannot find any row on the page containing the text ' . $linkname);
        $row = $this->find('css', sprintf('table tbody tr td.questionusage a:contains("%s")', $linkname), $exception);
        $row->click();
    }

    /**
     * Looks for the appropriate usage count in the column.
     *
     * @Then I should see :arg1 on the usage column
     * @param string $linkdata
     */
    public function i_should_see_on_the_usage_column($linkdata) {
        $exception = new ElementNotFoundException($this->getSession(),
            'Cannot find any row with the usage count of ' . $linkdata . ' on the column named Usage');
        $this->find('css', sprintf('table tbody tr td.questionusage a:contains("%s")', $linkdata), $exception);
    }
}
