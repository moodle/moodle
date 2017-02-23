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
 * Class containing data for courses view in the myoverview block.
 *
 * @package    block_myoverview
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_myoverview\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
/**
 * Class containing data for courses view in the myoverview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_view implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        return [
            'past' => [
                'pagingbar' => [
                    'pagecount' => 3,
                    'first' => [
                        'page' => '&laquo;',
                        'url' => '#',
                    ],
                    'last' => [
                        'page' => '&raquo;',
                        'url' => '#',
                    ],
                    'pages' => [
                        [
                            'number' => 1,
                            'page' => 1,
                            'url' => '#',
                            'active' => true,
                        ],
                        [
                            'number' => 2,
                            'page' => 2,
                            'url' => '#',
                        ],
                        [
                            'number' => 3,
                            'page' => 3,
                            'url' => '#',
                        ],
                    ]
                ],
            ],
            'inprogress' => [
                'pagingbar' => [
                    'pagecount' => 3,
                    'first' => [
                        'page' => '&laquo;',
                        'url' => '#',
                    ],
                    'last' => [
                        'page' => '&raquo;',
                        'url' => '#',
                    ],
                    'pages' => [
                        [
                            'number' => 1,
                            'page' => 1,
                            'url' => '#',
                            'active' => true,
                        ],
                        [
                            'number' => 2,
                            'page' => 2,
                            'url' => '#',
                        ],
                        [
                            'number' => 3,
                            'page' => 3,
                            'url' => '#',
                        ],
                    ]
                ],
            ],
            'future' => [
                'pagingbar' => [
                    'pagecount' => 3,
                    'first' => [
                        'page' => '&laquo;',
                        'url' => '#',
                    ],
                    'last' => [
                        'page' => '&raquo;',
                        'url' => '#',
                    ],
                    'pages' => [
                        [
                            'number' => 1,
                            'page' => 1,
                            'url' => '#',
                            'active' => true,
                        ],
                        [
                            'number' => 2,
                            'page' => 2,
                            'url' => '#',
                        ],
                        [
                            'number' => 3,
                            'page' => 3,
                            'url' => '#',
                        ],
                    ]
                ],
            ],
        ];
    }
}
