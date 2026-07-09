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

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;

/**
 * Steps definitions to verify a file downloaded from a grade outcomes form submission.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_grade_outcome extends behat_base {

    /**
     * Downloads the file by submitting the named button and verifies its type and content.
     *
     * Supported assertions:
     *   | Has mimetype  | text/plain  |
     *   | Contains text | some string |
     *
     * @Then following :buttontext button should download an outcome file that:
     *
     * @param string $buttontext the text of the button.
     * @param TableNode $table the table of assertions used to check the file contents.
     * @throws ExpectationException if the file cannot be downloaded, or if the download does not pass all the checks.
     */
    public function following_button_should_download_an_outcome_file_that(string $buttontext, TableNode $table): void {
        // Find the submit button by its accessible name.
        $button = $this->find('named_exact', ['button', $buttontext]);
        if (!$button) {
            throw new ExpectationException("Button '{$buttontext}' not found.", $this->getSession());
        }

        // The button is a direct child of its form.
        $form = $button->getParent();

        // The form action is an absolute URL.
        $actionurl = $form->getAttribute('action');

        // Collect GET params from hidden inputs.
        $params = [];
        foreach ($form->findAll('css', 'input[type="hidden"]') as $input) {
            $name = $input->getAttribute('name');
            if ($name !== null && $name !== '') {
                $params[$name] = (string)$input->getAttribute('value');
            }
        }

        $separator = str_contains($actionurl, '?') ? '&' : '?';
        $fullurl = $actionurl . ($params ? $separator . http_build_query($params) : '');

        // Fetch the file content directly, reusing the session cookie.
        $session = $this->getSession()->getCookie('MoodleSession');
        $filecontent = download_file_content($fullurl, ['Cookie' => 'MoodleSession=' . $session]);

        if ($filecontent === false) {
            throw new ExpectationException(
                "Could not download file from form action URL: $fullurl",
                $this->getSession()
            );
        }
        behat_context_helper::get('behat_download')->verify_file_content($filecontent, $table);
    }
}
