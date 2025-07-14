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
 * TinyMCE HTML plugin custom behat steps definitions.
 *
 * @package    tiny_html
 * @category   test
 * @copyright  2023 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Mink\Exception\ExpectationException;
use Behat\Gherkin\Node\{PyStringNode};

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../tests/behat/editor_tiny_helpers.php');
require_once(__DIR__ . '/../../../../../../behat/behat_base.php');

/**
 * TinyMCE HTML plugin custom behat steps definitions.
 *
 * @package    tiny_html
 * @category   test
 * @copyright  2023 Matt Porritt <matt.porritt@moodle.com>
 */
class behat_tiny_html extends behat_base {
    use editor_tiny_helpers;

    /**
     * Get Javascript to navigate to the shadow DOM of the editor,
     * and find specified sourcecode text.
     *
     * @param string $editorid The editor id to search within.
     * @param string $sourcecode The sourcecode to find.
     * @return string The Javascript to execute.
     */
    protected function get_javascript_sourcecode_search(string $editorid, string $sourcecode): string {
        return <<<EOF
            const container = document.getElementById('{$editorid}_codeMirrorContainer');
            const shadowRoot = container.shadowRoot;
            const sourceCode = shadowRoot.querySelector('.modal-codemirror-container [contenteditable="true"]').innerText
            const textToFind = `$sourcecode`;

            if (sourceCode == textToFind) {
              resolve(true);
            } else {
              resolve(false);
            }
        EOF;
    }

    /**
     * Gets the specified formatted single line source code from the editor
     * and compares it to what is expected.
     *
     * @When /^I should see "(?P<sourcecodelocator_string>(?:[^"]|\\")*)" source code for the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     * @throws ExpectationException
     * @param string $sourcecode The type of element to select (for example `p` or `span`)
     * @param string $locator The editor to select within
     */
    public function get_source_code(string $sourcecode, string $locator): void {
        $this->require_tiny_tags();

        $editor = $this->get_textarea_for_locator($locator);
        $editorid = $editor->getAttribute('id');
        $js = $this->get_javascript_sourcecode_search($editorid, $sourcecode);

        if ($this->evaluate_javascript_for_editor($editorid, $js) != 'true') {
            throw new ExpectationException("Source code does not match expected.", $this->getSession());
        }
    }

    /**
     * Gets the specified formatted multiline source code from the editor
     * and compares it to what is expected.
     *
     * @When /^I should see this multiline source code for the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor:$/
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
            throw new ExpectationException("Source code is not indented as expected.", $this->getSession());
        }
    }
}
