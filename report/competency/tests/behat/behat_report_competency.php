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
 * Behat competency report definitions.
 *
 * @package    report_competency
 * @category   test
 * @copyright  2022 Noel De Martin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Competency report definitions.
 *
 * @package    report_competency
 * @category   test
 * @copyright  2022 Noel De Martin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_report_competency extends behat_base {

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype  | name meaning | description                            |
     * | Breakdown | Course name  | The course competencies breakdown page |
     *
     * @param string $page identifies which type of page this is, e.g. 'Breakdown'.
     * @param string $identifier identifies the particular page, e.g. 'C1'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $page, string $identifier): moodle_url {
        switch (strtolower($page)) {
            case 'breakdown':
                $courseid = $this->get_course_id($identifier);
                return new moodle_url('/report/competency/index.php', [
                    'id' => $courseid,
                ]);
            default:
                throw new Exception("Unrecognised page type '{$page}'");
        }
    }

    /**
     * Return a list of the exact named selectors for the component.
     *
     * @return behat_component_named_selector[]
     */
    public static function get_exact_named_selectors(): array {
        return [
            new behat_component_named_selector('breakdown', [
                "//*[@data-region='competency-breakdown-report']//table".
                    "//tr[contains(., //a[@data-action='competency-dialogue'][contains(., %locator%)])]",
            ]),
            new behat_component_named_selector('breakdown rating', [
                "//td[position()=2][contains(., //a[@title='User competency summary'][contains(., %locator%)])]",
            ]),
        ];
    }

    /**
     * Set the value of a competency filter.
     *
     * @When /^I set the competency filter "([^"]*)" to "([^"]*)"$/
     * @param string $fieldlocator The field locator.
     * @param string $value The value to set.
     * @throws Exception
     */
    public function set_competency_filter(
        string $fieldlocator,
        string $value,
    ): void {
        $field = behat_field_manager::get_form_field_from_label($fieldlocator, $this)->get_node();
        $session = $this->getSession();

        $value = trim($value);

        // Click into the field.
        $field->click();

        // Remove any existing text.
        do {
            behat_base::type_keys($session, [behat_keys::BACKSPACE, behat_keys::DELETE]);
        } while (strlen($field->getValue()) > 0);
        $this->wait_for_pending_js();

        // Type in the new value.
        behat_base::type_keys($session, str_split($value));
        $this->wait_for_pending_js();

        // If the autocomplete found suggestions, then it will have:
        // 1) marked itself as expanded; and
        // 2) have an aria-selected suggestion in the list.
        $expanded = $field->getAttribute('aria-expanded');
        $suggestion = $field->getParent()->getParent()->find('css', '.form-autocomplete-suggestions > [aria-selected="true"]');

        if ($expanded && null !== $suggestion) {
            // A suggestion was found.
            // Click on the first item in the list.
            $suggestion->click();
        } else {
            throw new \InvalidArgumentException(
                "Unable to find '{$value}' in the list of options."
            );
        }

        $this->wait_for_pending_js();
    }
}
