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
 * @package    editor_tiny
 * @category   test
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Behat\Hook\Scope\BeforeScenarioScope;

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../behat/behat_base.php');

/**
 * TinyMCE custom behat step definitions.
 *
 * @package    editor_tiny
 * @category   test
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 */
class behat_editor_tiny extends behat_base implements \core_behat\settable_editor {
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

        $js = <<<EOF
        require(['editor_tiny/editor'], (editor) => {
            const instance = editor.getInstanceForElementId('${editorid}');
            if (instance) {
                instance.setContent('${value}');
                instance.undoManager.add();
            }
        });
        EOF;

        $this->execute_script($js);
    }

    /**
     * Set Tiny as default editor before executing Tiny tests.
     *
     * This step is required to ensure that TinyMCE is set as the current default editor as it may
     * not always be the default editor.
     *
     * Any Scenario, or Feature, which has the `editor_tiny` tag, or any `tiny_*` tag will have
     * this step executed before the Scenario.
     *
     * @BeforeScenario
     * @param BeforeScenarioScope $scope The Behat Scope
     */
    public function set_default_editor_flag(BeforeScenarioScope $scope): void {
        // This only applies to a scenario which matches the editor_tiny, or an tiny subplugin.
        $callback = function (string $tag): bool {
            return $tag === 'editor_tiny' || substr($tag, 0, 5) === 'tiny_';
        };

        if (!self::scope_tags_match($scope, $callback)) {
            // This scope does not require TinyMCE. Exit now.
            return;
        }

        // TinyMCE is a JavaScript editor so require JS here.
        $this->require_javascript();

        $this->execute('behat_general::the_default_editor_is_set_to', ['tiny']);
    }
}
