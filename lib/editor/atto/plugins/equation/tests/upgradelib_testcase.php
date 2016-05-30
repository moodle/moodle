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
 * PHPUnit testcase class for atto equation upgrade lib.
 *
 * @package    atto_equation
 * @copyright  2015 Sam Chaffee <sam@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit testcase class for atto equation upgrade lib.
 *
 * @package    atto_equation
 * @copyright  2015 Sam Chaffee <sam@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class atto_equation_upgradelib_testcase extends advanced_testcase {
    /**
     * The name of the plugin in config_plugins.
     */
    const SETTING_PLUGIN = 'atto_equation';

    /**
     * The name of the setting in config_plugins.
     */
    const SETTING_NAME = 'librarygroup4';

    /**
     * Does testsuite set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Tests the upgradelib atto_equation_update_librarygroup4_setting function.
     */
    public function test_update_librarygroup4_update() {
        global $CFG;
        require_once($CFG->libdir . '/editor/atto/plugins/equation/db/upgradelib.php');

        $originaldefaults = [
            '\sum{a,b}',
            '\int_{a}^{b}{c}',
            '\iint_{a}^{b}{c}',
            '\iiint_{a}^{b}{c}',
            '\oint{a}',
            '(a)',
            '[a]',
            '\lbrace{a}\rbrace',
            '\left| \begin{matrix} a_1 & a_2 \\ a_3 & a_4 \end{matrix} \right|',
        ];

        $newconfig = '
\sum{a,b}
\sqrt[a]{b+c}
\int_{a}^{b}{c}
\iint_{a}^{b}{c}
\iiint_{a}^{b}{c}
\oint{a}
(a)
[a]
\lbrace{a}\rbrace
\left| \begin{matrix} a_1 & a_2 \\ a_3 & a_4 \end{matrix} \right|
\frac{a}{b+c}
\vec{a}
\binom {a} {b}
{a \brack b}
{a \brace b}
';

        // Test successful update using windows line endings.
        $originaldefaultswindows = "\r\n" . implode("\r\n", $originaldefaults) . "\r\n";
        set_config(self::SETTING_NAME, $originaldefaultswindows, self::SETTING_PLUGIN);
        atto_equation_update_librarygroup4_setting();

        $this->assertEquals($newconfig, get_config(self::SETTING_PLUGIN, self::SETTING_NAME));

        // Test successful update using linux line .
        $originaldefaultslinux = "\n" . implode("\n", $originaldefaults) . "\n";
        set_config(self::SETTING_NAME, $originaldefaultslinux, self::SETTING_PLUGIN);
        atto_equation_update_librarygroup4_setting();

        $this->assertEquals($newconfig, get_config(self::SETTING_PLUGIN, self::SETTING_NAME));

        // Alter the original configuration by removing one of the equations.
        $alteredconfig = array_slice($originaldefaults, 0, -1);

        // Test no update using windows line endings.
        $alteredconfigwindows = "\r\n" . implode("\r\n", $alteredconfig) . "\r\n";
        set_config(self::SETTING_NAME, $alteredconfigwindows, self::SETTING_PLUGIN);
        atto_equation_update_librarygroup4_setting();

        $this->assertEquals($alteredconfigwindows, get_config(self::SETTING_PLUGIN, self::SETTING_NAME));

        // Test no update using linux line endings.
        $alteredconfiglinux = "\n" . implode("\n", $alteredconfig) . "\n";
        set_config(self::SETTING_NAME, $alteredconfiglinux, self::SETTING_PLUGIN);
        atto_equation_update_librarygroup4_setting();

        $this->assertEquals($alteredconfiglinux, get_config(self::SETTING_PLUGIN, self::SETTING_NAME));

        // Test no configuration.
        unset_config(self::SETTING_NAME, self::SETTING_PLUGIN);
        atto_equation_update_librarygroup4_setting();

        $this->assertFalse(get_config(self::SETTING_PLUGIN, self::SETTING_NAME));
    }
}