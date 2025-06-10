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
 * Unit tests for mod_customcert lib.
 *
 * @package    mod_customcert
 * @category   test
 * @copyright  2023 Diego Felipe Monroy <dfelipe.monroyc@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/customcert/lib.php');
require_once($CFG->libdir.'/componentlib.class.php');

/**
 * Unit tests for mod_customcert lib.
 *
 * @package    mod_customcert
 * @category   test
 * @copyright  2023 Diego Felipe Monroy <dfelipe.monroyc@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Tests force custom language for current session.
     *
     * @covers ::mod_customcert_force_current_language
     */
    public function test_mod_customcert_force_current_language() {
        global $USER;

        $user1 = $this->getDataGenerator()->create_user();
        $USER = $user1;
        $testlangs = ['es_mx', 'pt_br', 'cs', 'da', 'nb', 'sv'];

        $activelangs = get_string_manager()->get_list_of_translations();
        // Testing not installed languages.
        foreach ($testlangs as $lang) {
            $forced = mod_customcert_force_current_language($lang);
            $this->assertFalse($forced);
            $this->assertArrayNotHasKey($lang, $activelangs);
        }

        // Testing english language.
        $forced = mod_customcert_force_current_language('en');
        $this->assertFalse($forced);
        $this->assertArrayHasKey('en', $activelangs);

        // Install Language packs.
        $this->install_languagues();

        $activelangs = get_string_manager()->get_list_of_translations();
        foreach ($testlangs as $lang) {
            $forced = mod_customcert_force_current_language($lang);
            $this->assertTrue($forced);
            $this->assertArrayHasKey($lang, $activelangs);
        }
    }

    /**
     * Install lang packs by lang codes.
     *
     * @return bool
     */
    private function install_languagues(): bool {
        \core_php_time_limit::raise();

        $langcodes = [
            'es' => 'es_mx',
            'br' => 'pt_br',
            'cz' => 'cs',
            'dk' => 'da',
            'no' => 'nb',
            'se' => 'sv',
        ];
        get_string_manager()->reset_caches();

        $controller = new \tool_langimport\controller();
        try {
            $updated = $controller->install_languagepacks($langcodes);
            return true;
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }

        return false;
    }
}
