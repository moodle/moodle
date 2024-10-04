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

namespace core;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/graphlib.php");

/**
 * Tests for Graphlib.
 *
 * @coversDefaultClass \graph
 * @package    core
 * @copyright  2023 Meirza (meirza.arson@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class graphlib_test extends \basic_testcase {

    /**
     * Data provider for test_graphlib.
     *
     * @return array
     */
    public static function create_data(): array {
        return [
            'data' =>
            [
                'mock' => [
                    'survey_name' => 'Survey 101',
                    'names' => [
                        'Relevance',
                        'Reflective thinking',
                        'Interactivity',
                        'Tutor support',
                        'Peer support',
                        'Interpretation',
                    ],
                    'buckets1' => [
                        0.75,
                        2.5,
                        1.5,
                        1.5,
                        2.5,
                        2.5,
                    ],
                    'stractual' => 'Actual',
                    'buckets2' => [
                        -1,
                        -1,
                        -1,
                        -1,
                        -1,
                        -1,
                    ],
                    'strpreferred' => 'Preferred',
                    'strimagine' => 'Imagine',
                    'buckets3' => [
                        1,
                        2.75,
                        0.5,
                        3.5,
                        1.25,
                        3,
                    ],
                    'stdev1' => [
                        0.82915619758885,
                        1.1180339887499,
                        1.1180339887499,
                        1.1180339887499,
                        1.1180339887499,
                        1.1180339887499,
                    ],
                    'stdev2' => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                    'stdev3' => [
                        0.92915619758885,
                        2.1180339887499,
                        2.1180339887499,
                        2.1180339887499,
                        2.1180339887499,
                        2.1180339887499,
                    ],
                    'options' => [
                        'Almost never',
                        'Seldom',
                        'Sometimes',
                        'Often',
                        'Almost always',
                    ],
                    'maxbuckets1' => 2.5,
                    'maxbuckets2' => -1,
                    'maxbuckets3' => 3.5,
                ]
            ]
        ];
    }

    /**
     * Test graphlib.
     *
     * @dataProvider create_data
     * @covers ::output
     * @param array $mock
     * @return void
     */
    public function test_graphlib($mock): void {
        $graph = new \graph(300, 200);
        ob_start();
        $graph->parameter['title'] = strip_tags(format_string($mock['survey_name'], true));
        $graph->parameter['zero_axis'] = 'black';
        $graph->x_data = $mock['names'];
        $graph->y_data['answers1'] = $mock['buckets1'];
        $graph->y_format['answers1'] = [
            'colour' => 'ltblue',
            'line' => 'line',
            'point' => 'square',
            'shadow_offset' => 4,
            'legend' => $mock['stractual'],
        ];
        $graph->y_data['answers2'] = $mock['buckets2'];
        $graph->y_format['answers2'] = [
            'colour' => 'ltorange',
            'line' => 'line',
            'point' => 'square',
            'shadow_offset' => 4,
            'legend' => $mock['strpreferred'],
        ];
        $graph->y_data['answers3'] = $mock['buckets3'];
        $graph->y_format['answers3'] = [
            'colour' => 'ltred',
            'line' => 'brush',
            'point' => 'square',
            'shadow_offset' => 4,
            'legend' => $mock['stractual'],
        ];
        $graph->y_data['stdev1'] = $mock['stdev1'];
        $graph->y_format['stdev1'] = [
            'colour' => 'ltltblue',
            'bar' => 'fill',
            'shadow_offset' => '4',
            'legend' => 'none',
            'bar_size' => 0.3,
        ];
        $graph->y_data['stdev2'] = $mock['stdev2'];
        $graph->y_format['stdev2'] = [
            'colour' => 'ltltorange',
            'bar' => 'fill',
            'shadow_offset' => '4',
            'legend' => 'none',
            'bar_size' => 0.2,
        ];
        $graph->y_data['stdev3'] = $mock['stdev3'];
        $graph->y_format['stdev3'] = [
            'colour' => 'ltred',
            'bar' => 'fill',
            'shadow_offset' => '4',
            'legend' => 'none',
            'bar_size' => 0.2,
        ];
        $graph->offset_relation['stdev1'] = 'answers1';
        $graph->offset_relation['stdev2'] = 'answers2';
        $graph->offset_relation['stdev3'] = 'answers3';
        $graph->parameter['legend'] = 'outside-top';
        $graph->parameter['legend_border'] = 'black';
        $graph->parameter['legend_offset'] = 4;
        $graph->y_tick_labels = $mock['options'];
        if (($mock['maxbuckets1'] > 0.0) && ($mock['maxbuckets2'] > 0.0) && ($mock['maxbuckets3'] > 0.0)) {
            $graph->y_order = [
                'stdev1',
                'answers1',
                'stdev2',
                'answers2',
                'stdev3',
                'answers3',
            ];
        } else if ($mock['maxbuckets1'] > 0.0) {
            $graph->y_order = [
                'stdev1',
                'answers1',
            ];
        } else if ($mock['maxbuckets2'] > 0.0) {
            $graph->y_order = [
                'stdev2',
                'answers2',
            ];
        } else {
            $graph->y_order = [
                'stdev3',
                'answers3',
            ];
        }
        $graph->parameter['y_max_left'] = 4;
        $graph->parameter['y_axis_gridlines'] = 5;
        $graph->parameter['y_resolution_left'] = 1;
        $graph->parameter['y_decimal_left'] = 1;
        $graph->parameter['x_axis_angle'] = 0;
        $graph->parameter['x_inner_padding'] = 6;
        @$graph->draw();
        $res = ob_end_clean();

        $this->assertTrue($res);
    }
}
