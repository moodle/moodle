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

require_once(__DIR__ . '/../../../../../lib/behat/behat_deprecated_base.php');

/**
 * Deprecated behat steps for qbank_comment
 *
 * @package   qbank_comment
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qbank_comment_deprecated extends behat_deprecated_base {
    /**
     * Looks for the appropriate hyperlink comment count in the column.
     *
     * @Then I should see :arg1 on the comments column
     * @param string $linkdata
     * @deprecated Since Moodle 5.0 MDL-79122 in favour of the "qbank_comment > Comment count link" named selector.
     * @todo Final removal in Moodle 6.0 MDL-82413.
     */
    public function i_should_see_on_the_column(string $linkdata): void {
        $this->deprecated_message("Use '\"{$linkdata}\" \"qbank_comment > Comment count link\" should exist'");
        $this->execute('behat_general::should_exist', [$linkdata, 'qbank_comment > Comment count link']);
    }

    /**
     * Looks for a table, then looks for a row that contains the given text.
     * Once it finds the right row, it clicks a link in that row.
     *
     * @When I click :arg1 on the row on the comments column
     * @param string $linkname
     * @deprecated Since Moodle 5.0 MDL-79122 in favour of the "qbank_comment > Comment count link" named selector.
     * @todo Final removal in Moodle 6.0 MDL-82413.
     */
    public function i_click_on_the_row_containing(string $linkname): void {
        $this->deprecated_message("Use 'I click on \"{$linkname}\" \"qbank_comment > Comment count link\"'");
        $this->execute('behat_general::i_click_on', [$linkname, 'qbank_comment > Comment count link']);
    }
}
