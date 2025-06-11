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
 * Test strings lang pack.
 *
 * @package   theme_snap
 * @author    Rafael Monterroza <rafael.monterroza@openlms.net>
 * @copyright Copyright (c) 2020 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;

class urls_and_strings_check_test extends \advanced_testcase {

    /**
     * Setup for each test.
     */
    protected function setUp():void {
        $this->resetAfterTest();
    }

    /**
     * @dataProvider getsubdomains
     *
     * @param string $language
     * @param string $snapstring
     * @param string $expectedsubdomain
     */
    public function test_strings_specific_subdomain_correct($language, $snapstring, $expectedsubdomain) {
        global $PAGE, $SESSION;
        if (!get_string_manager()->translation_exists($language)) {
            $this->markTestSkipped('Lang pack not installed');
        }
        /** @var core_renderer $renderer */
        $renderer = $PAGE->get_renderer('theme_snap', 'core', RENDERER_TARGET_GENERAL);
        $SESSION->forcelang = $language;
        $subdomain = $renderer->get_poweredby_subdomain();
        $message = 'This language pack has a specific redirection URL, please double check and fix it. ';
        $message .= 'String key = ' . $snapstring . '. Language pack = ' . $language;
        $this->assertSame($expectedsubdomain, $subdomain, $message);
    }

    public function getsubdomains() {
        return [
            // Follow the pattern [language, string key, subdomain].
            ['es', 'poweredbyrunby', 'es'],
            ['fr', 'poweredbyrunby', 'fr'],
            ['ja', 'poweredbyrunby', 'jp'],
            ['pt_br', 'poweredbyrunby', 'br'],
        ];
    }

    /**
     * @dataProvider gettranslations
     *
     * @param string $language
     * @param string $snapstring
     */
    public function test_strings_check_lang_pack_correct($language, $snapstring) {

        $stringcontent = get_string_manager()->get_string($snapstring, 'theme_snap', null, $language);
        $containsstring = strpos($stringcontent, 'lackboard');
        $message = 'The word blackboard was found in a language pack. ';
        $message .= 'String key = ' . $snapstring . '. Language pack = ' . $language;
        $this->assertFalse($containsstring, $message);
    }

    public function gettranslations() {
        return [
            // Follow the pattern [language, string key].
            ['ar', 'poweredbyrunby'],
            ['ca', 'poweredbyrunby'],
            ['cs', 'poweredbyrunby'],
            ['da', 'poweredbyrunby'],
            ['de', 'poweredbyrunby'],
            ['en', 'poweredbyrunby'],
            ['es', 'poweredbyrunby'],
            ['fi', 'poweredbyrunby'],
            ['fr', 'poweredbyrunby'],
            ['it', 'poweredbyrunby'],
            ['ja', 'poweredbyrunby'],
            ['nl', 'poweredbyrunby'],
            ['pl', 'poweredbyrunby'],
            ['pt_br', 'poweredbyrunby'],
            ['th', 'poweredbyrunby'],
            ['tr', 'poweredbyrunby'],
            ['zh_tw', 'poweredbyrunby'],
        ];
    }

    public function test_edit_button () {
        global $PAGE;

        $renderer = $PAGE->get_renderer('theme_snap', 'core', RENDERER_TARGET_GENERAL);
        $url = new \moodle_url('course/view.php', ['id' => 1]);
        $editbutton = $renderer->edit_button($url);
        $this->assertEquals('', $editbutton);
    }
}
