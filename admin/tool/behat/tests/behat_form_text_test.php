<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tests for behat_form_text class
 *
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_behat;

use behat_form_text;
use Behat\Mink\Session;
use Behat\Mink\Element\NodeElement;
use core_string_manager_standard;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/behat/classes/behat_session_interface.php');
require_once($CFG->libdir . '/behat/classes/behat_session_trait.php');
require_once($CFG->libdir . '/behat/form_field/behat_form_text.php');

/**
 * Tests for the behat_form_text class
 *
 * @package    tool_behat
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \behat_form_text
 * @covers \behat_form_field
 */
class behat_form_text_test extends \basic_testcase {

    /**
     * Data provider for the test_set_get_value() method.
     *
     * @return array of value and expectation pairs to be tested.
     */
    public function provider_test_set_get_value() {
        return [
            'null' => [null, null],
            'int' => [3, 3],
            'float' => [3.14, 3.14],
            'string' => ['hello', 'hello'],
            'utf8' => ['你好', '你好'],
        ];
    }

    /**
     * Test the set_value() and get_value() methods.
     *
     * @param mixed $value value to be set.
     * @param mixed $expectation value to be checked.
     * @dataProvider provider_test_set_get_value()
     */
    public function test_set_get_value($value, $expectation) {
        $session = $this->createMock(Session::class);
        $node = $this->createMock(NodeElement::class);
        $node->method('getValue')->willReturn($value);
        $field = new behat_form_text($session, $node);

        $field->set_value($value);
        $this->assertEquals($expectation, $field->get_value());
    }

    /**
     * Data provider for the test_text_matches() method.
     *
     * @return array of decsep, value, match and result pairs to be tested.
     */
    public function provider_test_matches() {
        return [
            'lazy true' => ['.', 'hello', 'hello', true],
            'lazy false' => ['.', 'hello', 'bye', false],
            'float true' => ['.', '3.14', '3.1400', true],
            'float false' => ['.', '3.14', '3.1401', false],
            'float and float string true' => ['.', 3.14, '3.1400', true],
            'float and unrelated string false' => ['.', 3.14, 'hello', false],
            'float hash decsep true' => ['#', '3#14', '3#1400', true],
            'float hash decsep false' => ['#', '3#14', '3#1401', false],
            'float and float string hash decsep true' => ['#', 3.14, '3.1400', true],
            'float and unrelated string hash decsep false' => ['#', 3.14, 'hello', false],
            'float custom-default decsep mix1 true' => ['#', '3#14', '3.1400', true],
            'float custom-default decsep mix2 true' => ['#', '3.14', '3#1400', true],
            'float 2-custom decsep mix1 false' => ['#', '3#14', '3,1400', false],
            'float 2-custom decsep mix2 false' => [',', '3#14', '3,1400', false],
            'float default-custom decsep mix1 false' => ['.', '3#14', '3.1400', false],
            'float default-custom decsep mix2 false' => ['.', '3.14', '3#1400', false],
        ];
    }

    /**
     * Test the matches() method.
     *
     * @param string $decsep decimal separator to use.
     * @param mixed $value value to be set.
     * @param mixed $match value to be matched.
     * @param bool  $result expected return status of the function.
     * @dataProvider provider_test_matches()
     */
    public function test_matches($decsep, $value, $match, $result) {
        global $CFG;

        // Switch of string manager to avoid having to (slow) customise the lang file.
        $origcustom = $CFG->config_php_settings['customstringmanager'] ?? null;
        $CFG->config_php_settings['customstringmanager'] = '\tool_behat\phpunit_string_manager';
        $manager = get_string_manager(true);
        $manager->set_string('decsep', 'langconfig', $decsep);

        $session = $this->createMock(Session::class);
        $node = $this->createMock(NodeElement::class);
        $node->method('getValue')->willReturn($value);

        $field = new behat_form_text($session, $node);

        $field->set_value($value);
        $this->assertSame($result, $field->matches($match));

        // Switch back to the original string manager.
        if (is_null($origcustom)) {
            unset($CFG->config_php_settings['customstringmanager']);
        } else {
            $CFG->config_php_settings['customstringmanager'] = $origcustom;
        }
        $manager = get_string_manager(true);
    }
}

/**
 * Customised values that will be used instead of standard manager one.
 *
 * If an existing component/identifier is found, return it instead of the real
 * one from language files. Note this doesn't support place holders or another niceties.
 *
 * @package    tool_behat
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_string_manager extends core_string_manager_standard {

    /** @var array language customisations provided by the manager without asking for real contents */
    protected $customstrings = [];

    /**
     * Get String returns a requested string
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @param string|object|array $a An object, string or number that can be used
     *      within translation strings
     * @param string $lang moodle translation language, null means use current
     * @return string The String !
     */
    public function get_string($identifier, $component = '', $a = null, $lang = null) {
        $key = trim($component) . '/' . trim($identifier);
        if (isset($this->customstrings[$key])) {
            return $this->customstrings[$key];
        }
        return parent::get_string($identifier, $component, $a, $lang);
    }

    /**
     * Sets a custom string to be returned by the string manager instead of the language file one.
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @param string $value the contents of the language string to be returned by get_string()
     */
    public function set_string($identifier, $component, $value) {
        $key = trim($component) . '/' . trim($identifier);
        $this->customstrings[$key] = $value;
    }
}
