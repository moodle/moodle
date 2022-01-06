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
 * Unit tests for lib/classes/output/mustache_template_source_loader.php
 *
 * @package   core
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\mustache_template_source_loader;

/**
 * Unit tests for the Mustache source loader class.
 *
 * @package   core
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_output_mustache_template_source_loader_testcase extends advanced_testcase {
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

        $loader = new mustache_template_source_loader();
        $actual = phpunit_util::call_internal_method(
            $loader,
            'strip_template_comments',
            [$templatewithcomment],
            \core\output\mustache_template_source_loader::class
        );
        $this->assertEquals(trim($templatebody), trim($actual));
    }

    /**
     * Data provider for the test_load function.
     */
    public function test_load_test_cases() {
        $cache = [
            'core' => [
                'test' => '{{! a comment }}The rest of the template'
            ]
        ];
        $loader = $this->build_loader_from_static_cache($cache);

        return [
            'with comments' => [
                'loader' => $loader,
                'component' => 'core',
                'name' => 'test',
                'includecomments' => true,
                'expected' => '{{! a comment }}The rest of the template'
            ],
            'without comments' => [
                'loader' => $loader,
                'component' => 'core',
                'name' => 'test',
                'includecomments' => false,
                'expected' => 'The rest of the template'
            ],
        ];
    }

    /**
     * Test the load function.
     *
     * @dataProvider test_load_test_cases()
     * @param mustache_template_source_loader $loader The loader
     * @param string $component The moodle component
     * @param string $name The template name
     * @param bool $includecomments Whether to strip comments
     * @param string $expected The expected output
     */
    public function test_load($loader, $component, $name, $includecomments, $expected) {
        $this->assertEquals($expected, $loader->load($component, $name, 'boost', $includecomments));
    }

    /**
     * Data provider for the load_with_dependencies function.
     */
    public function test_load_with_dependencies_test_cases() {
        // Create a bunch of templates that include one another in various ways. There is
        // multiple instances of recursive inclusions to test that the code doensn't get
        // stuck in an infinite loop.
        $foo = '{{! a comment }}{{> core/bar }}{{< test/bop }}{{/ test/bop}}{{#str}} help, core {{/str}}';
        $foo2 = '{{! a comment }}hello';
        $bar = '{{! a comment }}{{> core/baz }}';
        $baz = '{{! a comment }}{{#str}} hide, core {{/str}}';
        $bop = '{{! a comment }}{{< test/bim }}{{/ test/bim }}{{> core/foo }}';
        $bim = '{{! a comment }}{{< core/foo }}{{/ core/foo}}{{> test/foo }}';
        $foonocomment = '{{> core/bar }}{{< test/bop }}{{/ test/bop}}{{#str}} help, core {{/str}}';
        $foo2nocomment = 'hello';
        $barnocomment = '{{> core/baz }}';
        $baznocomment = '{{#str}} hide, core {{/str}}';
        $bopnocomment = '{{< test/bim }}{{/ test/bim }}{{> core/foo }}';
        $bimnocomment = '{{< core/foo }}{{/ core/foo}}{{> test/foo }}';
        $cache = [
            'core' => [
                'foo' => $foo,
                'bar' => $bar,
                'baz' => $baz,
            ],
            'test' => [
                'foo' => $foo2,
                'bop' => $bop,
                'bim' => $bim
            ]
        ];
        $loader = $this->build_loader_from_static_cache($cache);

        return [
            'no template includes w comments' => [
                'loader' => $loader,
                'component' => 'test',
                'name' => 'foo',
                'includecomments' => true,
                'expected' => [
                    'templates' => [
                        'test' => [
                            'foo' => $foo2
                        ]
                    ],
                    'strings' => []
                ]
            ],
            'no template includes w/o comments' => [
                'loader' => $loader,
                'component' => 'test',
                'name' => 'foo',
                'includecomments' => false,
                'expected' => [
                    'templates' => [
                        'test' => [
                            'foo' => $foo2nocomment
                        ]
                    ],
                    'strings' => []
                ]
            ],
            'no template includes with string w comments' => [
                'loader' => $loader,
                'component' => 'core',
                'name' => 'baz',
                'includecomments' => true,
                'expected' => [
                    'templates' => [
                        'core' => [
                            'baz' => $baz
                        ]
                    ],
                    'strings' => [
                        'core' => [
                            'hide' => 'Hide'
                        ]
                    ]
                ]
            ],
            'no template includes with string w/o comments' => [
                'loader' => $loader,
                'component' => 'core',
                'name' => 'baz',
                'includecomments' => false,
                'expected' => [
                    'templates' => [
                        'core' => [
                            'baz' => $baznocomment
                        ]
                    ],
                    'strings' => [
                        'core' => [
                            'hide' => 'Hide'
                        ]
                    ]
                ]
            ],
            'full with comments' => [
                'loader' => $loader,
                'component' => 'core',
                'name' => 'foo',
                'includecomments' => true,
                'expected' => [
                    'templates' => [
                        'core' => [
                            'foo' => $foo,
                            'bar' => $bar,
                            'baz' => $baz
                        ],
                        'test' => [
                            'foo' => $foo2,
                            'bop' => $bop,
                            'bim' => $bim
                        ]
                    ],
                    'strings' => [
                        'core' => [
                            'help' => 'Help',
                            'hide' => 'Hide'
                        ]
                    ]
                ]
            ],
            'full without comments' => [
                'loader' => $loader,
                'component' => 'core',
                'name' => 'foo',
                'includecomments' => false,
                'expected' => [
                    'templates' => [
                        'core' => [
                            'foo' => $foonocomment,
                            'bar' => $barnocomment,
                            'baz' => $baznocomment
                        ],
                        'test' => [
                            'foo' => $foo2nocomment,
                            'bop' => $bopnocomment,
                            'bim' => $bimnocomment
                        ]
                    ],
                    'strings' => [
                        'core' => [
                            'help' => 'Help',
                            'hide' => 'Hide'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Test the load_with_dependencies function.
     *
     * @dataProvider test_load_with_dependencies_test_cases()
     * @param mustache_template_source_loader $loader The loader
     * @param string $component The moodle component
     * @param string $name The template name
     * @param bool $includecomments Whether to strip comments
     * @param string $expected The expected output
     */
    public function test_load_with_dependencies($loader, $component, $name, $includecomments, $expected) {
        $actual = $loader->load_with_dependencies($component, $name, 'boost', $includecomments);
        $this->assertEquals($expected, $actual);
    }
    /**
     * Data provider for the test_load function.
     */
    public function test_scan_template_source_for_dependencies_test_cases() {
        $foo = '{{! a comment }}{{> core/bar }}{{< test/bop }}{{/ test/bop}}{{#str}} help, core {{/str}}';
        $bar = '{{! a comment }}{{> core/baz }}';
        $baz = '{{! a comment }}{{#str}} hide, core {{/str}}';
        $bop = '{{! a comment }}hello';
        $multiline1 = <<<TEMPLATE
{{! a comment }}{{#str}} authorreplyingprivatelytoauthor,
mod_forum {{/str}}
TEMPLATE;
        $multiline2 = <<<TEMPLATE
{{! a comment }}{{#str}}
authorreplyingprivatelytoauthor,
mod_forum {{/str}}
TEMPLATE;
        $multiline3 = <<<TEMPLATE
{{! a comment }}{{#str}}
authorreplyingprivatelytoauthor,
mod_forum
{{/str}}
TEMPLATE;
        $multiline4 = <<<TEMPLATE
{{! a comment }}{{#str}}
authorreplyingprivatelytoauthor, mod_forum
{{/str}}
TEMPLATE;
        $multiline5 = <<<TEMPLATE
{{! a comment }}{{#str}}
hide
{{/str}}
TEMPLATE;

        $cache = [
            'core' => [
                'foo' => $foo,
                'bar' => $bar,
                'baz' => $baz,
                'bop' => $bop,
                'multiline1' => $multiline1,
                'multiline2' => $multiline2,
                'multiline3' => $multiline3,
                'multiline4' => $multiline4,
                'multiline5' => $multiline5,
            ]
        ];
        $loader = $this->build_loader_from_static_cache($cache);

        return [
            'single template include' => [
                'loader' => $loader,
                'source' => $bar,
                'expected' => [
                    'templates' => [
                        'core' => ['baz']
                    ],
                    'strings' => []
                ]
            ],
            'single string include' => [
                'loader' => $loader,
                'source' => $baz,
                'expected' => [
                    'templates' => [],
                    'strings' => [
                        'core' => ['hide']
                    ]
                ]
            ],
            'no include' => [
                'loader' => $loader,
                'source' => $bop,
                'expected' => [
                    'templates' => [],
                    'strings' => []
                ]
            ],
            'all include' => [
                'loader' => $loader,
                'source' => $foo,
                'expected' => [
                    'templates' => [
                        'core' => ['bar'],
                        'test' => ['bop']
                    ],
                    'strings' => [
                        'core' => ['help']
                    ]
                ]
            ],
            'string: component on new line' => [
                'loader' => $loader,
                'source' => $multiline1,
                'expected' => [
                    'templates' => [],
                    'strings' => [
                        'mod_forum' => ['authorreplyingprivatelytoauthor']
                    ]
                ]
            ],
            'string: identifier on own line' => [
                'loader' => $loader,
                'source' => $multiline2,
                'expected' => [
                    'templates' => [],
                    'strings' => [
                        'mod_forum' => ['authorreplyingprivatelytoauthor']
                    ]
                ]
            ],
            'string: all parts on new lines' => [
                'loader' => $loader,
                'source' => $multiline3,
                'expected' => [
                    'templates' => [],
                    'strings' => [
                        'mod_forum' => ['authorreplyingprivatelytoauthor']
                    ]
                ]
            ],
            'string: id and component on own line' => [
                'loader' => $loader,
                'source' => $multiline4,
                'expected' => [
                    'templates' => [],
                    'strings' => [
                        'mod_forum' => ['authorreplyingprivatelytoauthor']
                    ]
                ]
            ],
            'string: no component' => [
                'loader' => $loader,
                'source' => $multiline5,
                'expected' => [
                    'templates' => [],
                    'strings' => [
                        'core' => ['hide']
                    ]
                ]
            ],
        ];
    }

    /**
     * Test the scan_template_source_for_dependencies function.
     *
     * @dataProvider test_scan_template_source_for_dependencies_test_cases()
     * @param mustache_template_source_loader $loader The loader
     * @param string $source The template to test
     * @param string $expected The expected output
     */
    public function test_scan_template_source_for_dependencies($loader, $source, $expected) {
        $actual = phpunit_util::call_internal_method(
            $loader,
            'scan_template_source_for_dependencies',
            [$source],
            \core\output\mustache_template_source_loader::class
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Create an instance of mustache_template_source_loader which loads its templates
     * from the given cache rather than disk.
     *
     * @param array $cache A cache of templates
     * @return mustache_template_source_loader
     */
    private function build_loader_from_static_cache(array $cache) : mustache_template_source_loader {
        return new mustache_template_source_loader(function($component, $name, $themename) use ($cache) {
            return $cache[$component][$name];
        });
    }
}
