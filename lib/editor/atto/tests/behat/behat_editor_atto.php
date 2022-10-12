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
 * Atto custom steps definitions.
 *
 * @package    editor_atto
 * @category   test
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Behat\Hook\Scope\BeforeScenarioScope;

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../behat/behat_base.php');
require_once(__DIR__ . '/../../../../behat/classes/settable_editor.php');

/**
 * Steps definitions to deal with the atto text editor
 *
 * @package    editor_atto
 * @category   test
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_editor_atto extends behat_base implements \core_behat\settable_editor {

    /**
     * Set the value for the editor.
     *
     * @param string $editorid
     * @param string $value
     */
    public function set_editor_value(string $editorid, string $value): void {
        $js = <<<EOF
            (function() {
                const editableEditor = document.getElementById("${editorid}editable");
                if (editableEditor && editableEditor.classList.contains('editor_atto_content')) {
                    editableEditor.innerHTML = "${value}";
                }
                const editor = document.getElementById("${editorid}");
                if (editor) {
                    editor.value = "${value}";
                }
            })();
        EOF;

        $this->execute_script($js);
    }

    /**
     * Select the text in an Atto field.
     *
     * @Given /^I select the text in the "([^"]*)" Atto editor$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $field
     * @return void
     */
    public function select_the_text_in_the_atto_editor($fieldlocator) {
        if (!$this->running_javascript()) {
            throw new coding_exception('Selecting text requires javascript.');
        }
        // We delegate to behat_form_field class, it will
        // guess the type properly.
        $field = behat_field_manager::get_form_field_from_label($fieldlocator, $this);

        if (!method_exists($field, 'select_text')) {
            throw new coding_exception('Field does not support the select_text function.');
        }
        $field->select_text();
    }

    /**
     * Ensure that the Atto editor is used for all tests using the editor_atto, or atto_* tags.
     *
     * This ensures, whatever the default editor, that the Atto editor is used for these tests.
     *
     * @BeforeScenario
     * @param BeforeScenarioScope $scope The Behat Scope
     */
    public function set_default_editor_flag(BeforeScenarioScope $scope): void {
        // This only applies to a scenario which matches the editor_atto, or an atto subplugin.
        $callback = function (string $tag): bool {
            return $tag === 'editor_atto' || substr($tag, 0, 5) === 'atto_';
        };

        if (!self::scope_tags_match($scope, $callback)) {
            return;
        }

        $this->execute('behat_general::the_default_editor_is_set_to', ['atto']);
    }
}
