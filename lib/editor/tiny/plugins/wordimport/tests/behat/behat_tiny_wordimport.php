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
 * TinyMCE custom steps definitions.
 *
 * @package    tiny_wordimport
 * @category   test
 * @copyright  2024 University of Graz
 * @author     André Menrath <andre.menrath@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Gherkin\Node\{PyStringNode};
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../../../../lib/behat/core_behat_file_helper.php');

/**
 * TinyMCE custom behat step definitions.
 *
 * @package    tiny_wordimport
 * @category   test
 * @copyright  2024 University of Graz
 *             2024 André Menrath <andre.menrath@uni-graz.at>
 *             2023 Matt Porritt <matt.porritt@moodle.com>
 *             2022 Andrew Nicols
 */
class behat_tiny_wordimport extends behat_base implements \core_behat\settable_editor {
    use editor_tiny_helpers;

    /**
     * Upload a file in the file picker using the repository_upload plugin.
     *
     * Note: This step assumes we are already in the file picker.
     *       See MDL-76001 for details.
     *
     * @Given /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" to the file picker$/
     *
     * @param string $filepath The relative file path of the file from Moodles dirroot.
     */
    public function i_upload_a_file_in_the_filepicker(string $filepath): void {
        // Ensure that we are on the "Upload a file" repository plugin.
        $filepicker = $this->select_upload_repository();

        // Grab the repository region of the file picker.
        $reporegion = $filepicker->find('css', '.fp-repo-items');

        // Upload the file.
        $this->upload_the_file($reporegion, $filepath);

        // Attach it.
        $this->execute('behat_general::i_click_on_in_the', [
            get_string('upload', 'repository'), 'button',
            $reporegion, 'NodeElement',
        ]);
    }

    /**
     * Select the "Upload a file" repository plugin from the filepicker.
     *
     * @return NodeElement The filepicker region.
     */
    protected function select_upload_repository(): NodeElement {
        if (!$this->has_tag('javascript')) {
            throw new DriverException('The file picker is only available with javascript enabled');
        }

        if (!$this->has_tag('_file_upload')) {
            throw new DriverException('File upload tests must have the @_file_upload tag on either the scenario or feature.');
        }

        $filepicker = $this->find('dialogue', get_string('filepicker', 'core_repository'));

        $this->execute('behat_general::i_click_on_in_the', [
            get_string('pluginname', 'repository_upload'), 'link',
            $filepicker, 'NodeElement',
        ]);

        return $filepicker;
    }

    /**
     * Upload the specified file into the repository_upload repository.
     *
     * Note: This action is synchronous and WebDriver will wait for it to return before proceeding.
     *
     * @param NodeElement $reporegion The region that the file input is contained in
     * @param string $filepath The filepath within the Moodle repository
     */
    protected function upload_the_file(NodeElement $reporegion, string $filepath): void {
        $fileinput = $this->find('field', get_string('attachment', 'core_repository'), false, $reporegion);
        $filepath = $this->normalise_fixture_filepath($filepath);
        $fileinput->attachFile($filepath);
    }

    /**
     * Get Javascript to navigate to the shadow DOM of the editor,
     * and find specified sourcecode text.
     *
     * @copyright                2023 Matt Porritt <matt.porritt@moodle.com>
     * @param string $editorid   The editor id to search within.
     * @param string $sourcecode The sourcecode to find.
     * @return string            The Javascript to execute.
     */
    protected function get_javascript_sourcecode_search(string $editorid, string $sourcecode): string {
        return <<<EOF
            const container = document.getElementById('{$editorid}_codeMirrorContainer');
            const shadowRoot = container.shadowRoot;
            const sourceCode = shadowRoot.querySelector('.modal-codemirror-container [contenteditable="true"]').innerText
            const textToFind = `$sourcecode`;

            if (sourceCode.includes(textToFind)) {
              resolve(true);
            } else {
              resolve(false);
            }
        EOF;
    }

    /**
     * Gets the specified formatted multiline source code from the editor
     * and compares it to what is expected.
     *
     * @copyright 2023 Matt Porritt <matt.porritt@moodle.com>
     * @When /^I should find this multiline source code within the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor:$/
     * @throws ExpectationException
     * @param string $locator
     * @param PyStringNode $sourcecode
     * @return void
     */
    public function get_multiline_source_code(string $locator, PyStringNode $sourcecode): void {
        $this->require_tiny_tags();

        $editor = $this->get_textarea_for_locator($locator);
        $editorid = $editor->getAttribute('id');
        $js = $this->get_javascript_sourcecode_search($editorid, $sourcecode);

        if ($this->evaluate_javascript_for_editor($editorid, $js) != 'true') {
            throw new ExpectationException("Specified string was not found in source code.", $this->getSession());
        }
    }
}

