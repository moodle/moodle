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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;

/**
 * Behat helpers for TinyMCE Plugins.
 *
 * @package    editor_tiny
 * @category   test
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 */
trait editor_tiny_helpers {
    /**
     * Execute some JavaScript for a particular Editor instance.
     *
     * The editor instance is available on the 'instnace' variable.
     *
     * @param string $editorid The ID of the editor
     * @param string $code The code to execute
     */
    protected function execute_javascript_for_editor(string $editorid, string $code): void {
        $js = <<<EOF
        require(['editor_tiny/editor'], (editor) => {
            const instance = editor.getInstanceForElementId('{$editorid}');
            {$code}
        });
        EOF;

        $this->execute_script($js);
    }

    /**
     * Resolve some JavaScript for a particular Editor instance.
     *
     * The editor instance is available on the 'instnace' variable.
     * The code should return a value by passing it to the `resolve` function.
     *
     * @param string $editorid The ID of the editor
     * @param string $code The code to evaluate
     * @return string|null|array
     */
    protected function evaluate_javascript_for_editor(string $editorid, string $code) {
        $js = <<<EOF
        return new Promise((resolve, reject) => {
            require(['editor_tiny/editor'], (editor) => {
                const instance = editor.getInstanceForElementId('{$editorid}');
                if (!instance) {
                    reject("Instance '{$editorid}' not found");
                }

                {$code}
            });
        });
        EOF;

        return $this->evaluate_script($js);
    }

    /**
     * Set the value for the editor.
     *
     * Note: This function is called by the behat_form_editor class.
     * It is called regardless of the current default editor as editor selection is a user preference.
     * Therefore it must fail gracefully and only set a value if the editor instance was found on the page.
     *
     * @param string $editorid
     * @param string $value
     */
    public function set_editor_value(string $editorid, string $value): void {
        if (!$this->running_javascript()) {
            return;
        }

        $this->execute_javascript_for_editor($editorid, <<<EOF
            instance.setContent('{$value}');
            instance.undoManager.add();
            EOF);
    }

    /**
     * Store the current value of the editor, if it is a Tiny editor, to the textarea.
     *
     * @param string $editorid The ID of the editor.
     */
    public function store_current_value(string $editorid): void {
        $this->execute_javascript_for_editor($editorid, "instance?.save();");
    }

    /**
     * Ensure that the editor_tiny tag is in use.
     *
     * This function should be used for any step defined in this file.
     *
     * @throws DriverException Thrown if the editor_tiny tag is not specified for this file
     */
    protected function require_tiny_tags(): void {
        // Ensure that this step only runs in TinyMCE tags.
        if (!$this->has_tag('editor_tiny')) {
            throw new DriverException(
                'TinyMCE tests using this step must have the @editor_tiny tag on either the scenario or feature.'
            );
        }
    }

    /**
     * Get the Mink NodeElement of the <textarea> for the specified locator.
     *
     * Moodle mostly referes to the textarea, rather than the editor itself and interactions are translated to the
     * Editor using the TinyMCE API.
     *
     * @param string $locator A Moodle field locator
     * @return NodeElement The element found by the find_field function
     */
    protected function get_textarea_for_locator(string $locator): NodeElement {
        return $this->find_field($locator);
    }

    /**
     * Get the Mink NodeElement of the container for the specified locator.
     *
     * This is the top-most HTML element for the editor found by TinyMCE.getContainer().
     *
     * @param string $locator A Moodle field locator
     * @return NodeElement The Mink NodeElement representing the container.
     */
    protected function get_editor_container_for_locator(string $locator): NodeElement {
        $textarea = $this->get_textarea_for_locator($locator);
        $editorid = $textarea->getAttribute('id');

        $targetid = uniqid();
        $js = <<<EOF
            const container = instance.getContainer();
            if (!container.id) {
                container.id = '{$targetid}';
            }
            resolve(container.id);
        EOF;
        $containerid = $this->evaluate_javascript_for_editor($editorid, $js);

        return $this->find('css', "#{$containerid}");
    }

    /**
     * Get the name of the iframe relating to the editor.
     *
     * If no name is found, then add one.
     *
     * If the editor it not found, then throw an exception.
     *
     * @param string $locator The name of the editor
     * @return string The name of the iframe
     */
    protected function get_editor_iframe_name(string $locator): string {
        return $this->get_editor_iframe_name_for_element($this->get_textarea_for_locator($locator));
    }

    /**
     * Get the name of the iframe relating to the editor.
     *
     * If no name is found, then add one.
     *
     * If the editor it not found, then throw an exception.

     * @param NodeElement $editor The editor element
     * @return string The name of the iframe
     */
    protected function get_editor_iframe_name_for_element(NodeElement $editor): string {
        $editorid = $editor->getAttribute('id');

        // Ensure that a name is set on the iframe relating to the editorid.
        $js = <<<EOF
            if (!instance.iframeElement.name) {
                instance.iframeElement.name = '{$editorid}';
            }
            resolve(instance.iframeElement.name);
        EOF;

        return $this->evaluate_javascript_for_editor($editorid, $js);
    }

    /**
     * Normalise the fixture file path relative to the dirroot.
     *
     * @param string $filepath
     * @return string
     */
    protected function normalise_fixture_filepath(string $filepath): string {
        global $CFG;

        $filepath = str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        if (!is_readable($filepath)) {
            $filepath = $CFG->dirroot . DIRECTORY_SEPARATOR . $filepath;
            if (!is_readable($filepath)) {
                throw new ExpectationException('The file to be uploaded does not exist.', $this->getSession());
            }
        }

        return $filepath;
    }
}
