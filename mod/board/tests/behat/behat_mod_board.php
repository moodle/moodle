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

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Behat steps for Board.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_board extends behat_base {
    /**
     * Open dialog for adding of new baord.
     *
     * @When /^I open dialog for adding mod_board to "(?P<section_string>(?:[^"]|\\")*)" section$/
     * @param string $section section name
     */
    public function i_open_add_board_dialog(string $section) {
        if (get_config('core', 'version') > 2024100799) {
            // Moodle 5.0 has backwards incompatible changes in course management UI.
            $this->execute('behat_general::i_click_on_in_the', [
                'Add content', 'button',
                $section, 'section',
            ]);
            $this->execute('behat_general::i_click_on_in_the', [
                'Activity or resource', 'button',
                $section, 'section',
            ]);
        } else {
            $this->execute('behat_general::i_click_on_in_the', [
                'Add an activity or resource', 'button',
                $section, 'section',
            ]);
        }
        $this->execute('behat_general::i_click_on_in_the', [
            'Add a new Board', 'link',
            'Add an activity or resource', 'dialogue',
        ]);
    }

    /**
     * Double click column heading and type new name.
     *
     * @When /^I change mod_board "(?P<column_string>(?:[^"]|\\")*)" column name to "(?P<name_string>[^"]*)"$/
     * @param int $column column number
     * @param string $name name of column to type
     */
    public function i_change_column_name(int $column, string $name) {
        $this->execute('behat_general::i_click_on_in_the', [
            'Update column Heading', 'mod_board > button',
            $column, 'mod_board > column',
        ]);
        $newdata = new \Behat\Gherkin\Node\TableNode([['Name', $name]]);
        $this->execute('behat_forms::i_set_the_following_fields_to_these_values', $newdata);
        $this->execute('behat_general::i_click_on_in_the', [
            'Update', 'button',
            'Update column Heading', 'dialogue',
        ]);

        $this->wait_for_pending_js();
    }

    /**
     * Click on Add comment and type text.
     *
     * @When /^I type mod_board comment "(?P<comment_string>(?:[^"]|\\")*)"$/
     * @param string $comment
     */
    public function i_type_note_comment(string $comment) {
        $xpath = "//div[contains(@class,'comment-input ')]";
        $this->get_selected_node('xpath', $xpath)->click();
        $this->wait_for_pending_js();

        $chars = str_split($comment); // No Unicode support here, sorry.
        behat_base::type_keys($this->getSession(), $chars);
        $this->wait_for_pending_js();
    }

    /**
     * Return a list of the exact named selectors for the component.
     *
     * @return behat_component_named_selector[]
     */
    public static function get_exact_named_selectors(): array {
        return [
            new behat_component_named_selector('column', [
                "//div[contains(@class,'board_column ') and position()=%locator%]",
            ]),
            new behat_component_named_selector('button', [
                "//div[@role='button' and (@title=%locator% or @aria-label=%locator%)]",
            ]),
            new behat_component_named_selector('note', [
                "//div[contains(@class,'board_note ') and div/div[contains(@class,'mod_board_note_heading')]=%locator%]",
            ]),
        ];
    }

    /**
     * Return the list of partial named selectors.
     *
     * @return array
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector('comment', [
                "//div[contains(@class,'comment ') and div[contains(@class,'comment-content') and contains(text(), %locator%)]]",
            ]),
        ];
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[page name]" page'.
     *
     * @param string $page name of the page, with the component name removed e.g. 'Admin notification'.
     * @return moodle_url the corresponding URL.
     */
    protected function resolve_page_url(string $page): moodle_url {
        switch (strtolower($page)) {
            case 'templates':
                return new moodle_url('/mod/board/template/index.php');

            default:
                throw new Exception('Unrecognised tool_muprog page "' . $page . '."');
        }
    }
}
