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

namespace enrol_lti\local\ltiadvantage\entity;

/**
 * Tests for ags_info.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\ags_info
 */
class ags_info_test extends \advanced_testcase {

    /**
     * Test creation of the object instances.
     * @dataProvider instantiation_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create
     */
    public function test_creation(array $args, array $expectations) {
        if (!$expectations['valid']) {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            ags_info::create(...array_values($args));
        } else {
            $agsinfo = ags_info::create(...array_values($args));
            $this->assertEquals($args['lineitemsurl'], $agsinfo->get_lineitemsurl());
            $this->assertEquals($args['lineitemurl'], $agsinfo->get_lineitemurl());
            if (isset($expectations['scopes'])) {
                $this->assertEquals($expectations['scopes'], $agsinfo->get_scopes());
            } else {
                $this->assertEquals($args['scopes'], $agsinfo->get_scopes());
            }

            $this->assertEquals($expectations['lineitemscope'], $agsinfo->get_lineitemscope());
            $this->assertEquals($expectations['scorescope'], $agsinfo->get_scorescope());
            $this->assertEquals($expectations['resultscope'], $agsinfo->get_resultscope());
        }
    }

    /**
     * Data provider for testing object instantiation.
     * @return array the data for testing.
     */
    public function instantiation_data_provider(): array {
        return [
            'Both lineitems and lineitem URL provided with full list of valid scopes' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly'
                    ],
                    'resultscope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                    'scorescope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                ]
            ],
            'Both lineitems and lineitem URL provided with lineitem scopes only' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                    ],
                    'scorescope' => null,
                    'resultscope' => null
                ]
            ],
            'Both lineitems and lineitem URL provided with score scope only' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => null,
                    'scorescope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/score',
                    'resultscope' => null
                ]
            ],
            'Both lineitems and lineitem URL provided with result scope only' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => null,
                    'scorescope' => null,
                    'resultscope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly'
                ]
            ],
            'Both lineitems and lineitem URL provided with no scopes' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => []
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => null,
                    'scorescope' => null,
                    'resultscope' => null
                ]
            ],
            'Just lineitems URL, no lineitem URL, with full list of valid scopes' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => null,
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly'
                    ],
                    'resultscope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                    'scorescope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                ]
            ],
            'Just lineitems URL, no lineitem URL, with lineitems scopes only' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => null,
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                    ],
                    'scorescope' => null,
                    'resultscope' => null
                ]
            ],
            'Just lineitems URL, no lineitem URL, with score scope only' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => null,
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => null,
                    'scorescope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/score',
                    'resultscope' => null
                ]
            ],
            'Just lineitems URL, no lineitem URL, with result scope only' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => null,
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => null,
                    'scorescope' => null,
                    'resultscope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly'
                ]
            ],
            'Just lineitems URL, no lineitem URL, with no scopes' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => null,
                    'scopes' => []
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => null,
                    'scorescope' => null,
                    'resultscope' => null
                ]
            ],
            'Both lineitems and lineitem URL provided with non-string scope' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score',
                        12345
                    ]
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Scope must be a string value'
                ]
            ],
            'Both lineitems and lineitem URL provided with unsupported scopes' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score',
                        'https://example.com/unsupported/scope'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score',
                    ],
                    'lineitemscope' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly'
                    ],
                    'resultscope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                    'scorescope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                ]
            ],
            'Both lineitems and lineitem URL provided with invalid scope types' => [
                'args' => [
                    'lineitemsurl' => new \moodle_url('https://platform.example.org/10/lineitems'),
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        12
                    ]
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Scope must be a string value"
                ]
            ],
            'Claim contains a single lineitem URL only with valid scopes' => [
                'args' => [
                    'lineitemsurl' => null,
                    'lineitemurl' => new \moodle_url('https://platform.example.org/10/lineitems/4/lineitem'),
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                    'lineitemscope' => null,
                    'scorescope' => 'https://purl.imsglobal.org/spec/lti-ags/scope/score',
                    'resultscope' => null
                ]
            ],
            'Claim contains no lineitems URL or lineitem URL' => [
                'args' => [
                    'lineitemsurl' => null,
                    'lineitemurl' => null,
                    'scopes' => [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                    ]
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Missing lineitem or lineitems URL"
                ]
            ],
        ];
    }
}
