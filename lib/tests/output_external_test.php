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
 * Unit tests for lib/classes/output/external.php
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\external;

require_once(__DIR__.'/../../lib/externallib.php');
require_once(__DIR__.'/../../lib/mustache/src/Mustache/Tokenizer.php');
require_once(__DIR__.'/../../lib/mustache/src/Mustache/Parser.php');

/**
 * Class core_output_external_testcase - test \core\output\external class.
 * @package   core
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_output_external_testcase extends base_testcase {

    /**
     * Ensure that stripping comments from templates does not mutilate the template body.
     */
    public function test_strip_template_comments() {

        $templatebody = <<<'TBD'
        <h1>{{# str }} pluginname, mod_lemmings {{/ str }}</h1>
        <div>{{test}}</div>
        <div>{{{unescapedtest}}}</div>
        {{#lemmings}}
            <div>
                <h2>{{name}}</h2>
                {{> mod_lemmings/lemmingprofile }}
                {{# pix }} t/edit, core, Edit Lemming {{/ pix }}
            </div>
        {{/lemmings}}
        {{^lemmings}}Sorry, no lemmings today{{/lemmings}}
        <div id="{{ uniqid }}-tab-container">
            {{# tabheader }}
                <ul role="tablist" class="nav nav-tabs">
                    {{# iconlist }}
                        {{# icons }}
                            {{> core/pix_icon }}
                        {{/ icons }}
                    {{/ iconlist }}
                </ul>
            {{/ tabheader }}
            {{# tabbody }}
                <div class="tab-content">
                    {{# tabcontent }}
                        {{# tabs }}
                            {{> core/notification_info}}
                        {{/ tabs }}
                    {{/ tabcontent }}
                </div>
            {{/ tabbody }}
        </div>
        {{#js}}
            require(['jquery','core/tabs'], function($, tabs) {

                var container = $("#{{ uniqid }}-tab-container");
                tabs.create(container);
            });
        {{/js}}
TBD;
        $templatewithcomment = <<<TBC
        {{!
            This file is part of Moodle - http://moodle.org/

            Moodle is free software: you can redistribute it and/or modify
            it under the terms of the GNU General Public License as published by
            the Free Software Foundation, either version 3 of the License, or
            (at your option) any later version.

            Moodle is distributed in the hope that it will be useful,
            but WITHOUT ANY WARRANTY; without even the implied warranty of
            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
            GNU General Public License for more details.

            You should have received a copy of the GNU General Public License
            along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
        }}
        {{!
            @template mod_lemmings/lemmings

            Lemmings template.

            The purpose of this template is to render a lot of lemmings.

            Classes required for JS:
            * none

            Data attributes required for JS:
            * none

            Context variables required for this template:
            * attributes Array of name / value pairs.

            Example context (json):
            {
                "lemmings": [
                    { "name": "Lemmy Winks", "age" : 1, "size" : "big" },
                    { "name": "Rocky", "age" : 2, "size" : "small" }
                ]
            }

        }}
        $templatebody
        {{!
            Here's some more comment text
            Note, there is no need to test bracketed variables inside comments as gherkin does not support that!
            See this issue: https://github.com/mustache/spec/issues/8
        }}
TBC;

        // Ensure that the template when stripped of comments just includes the body.
        $stripped = phpunit_util::call_internal_method(null, 'strip_template_comments',
                [$templatewithcomment], 'core\output\external');
        $this->assertEquals(trim($templatebody), trim($stripped));

        $tokenizer = new Mustache_Tokenizer();
        $tokens = $tokenizer->scan($templatebody);
        $parser = new Mustache_Parser();
        $tree = $parser->parse($tokens);
        $this->assertNotEmpty($tree);
    }
}
