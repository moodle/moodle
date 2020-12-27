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
 * Paged Content exporter.
 *
 * @package    core
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\external;

defined('MOODLE_INTERNAL') || die();

use renderer_base;

/**
 * Paged Content exporter.
 *
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class paged_content_exporter extends exporter {
    /** @var int pagesize The number of records to show on each page */
    private $pagesize;

    /** @var int pagenumber The current page number */
    private $pagenumber;

    /** @var int recordcount The total number of records available */
    private $recordcount;

    /** @var callable The callback to use to determine a page URL */
    private $pageurlcallback;

    /**
     * Constructor.
     *
     * @param int $pagesize The number of records to show on each page
     * @param int $pagenumber The current page number
     * @param int $recordcount The total number of records available
     * @param callable $pageurlcallback The callback to use to determine a page URL
     * @param array $related List of related elements
     */
    public function __construct(int $pagesize, int $pagenumber, int $recordcount, callable $pageurlcallback, array $related = []) {
        $this->pagesize = $pagesize;
        $this->pagenumber = $pagenumber;
        $this->recordcount = $recordcount;
        $this->pageurlcallback = $pageurlcallback;

        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'itemsperpage' => ['type' => PARAM_INT],
            'buttons' => [
                'type' => [
                    'first' => ['type' => PARAM_BOOL],
                    'previous' => ['type' => PARAM_BOOL],
                    'next' => ['type' => PARAM_BOOL],
                    'last' => ['type' => PARAM_BOOL],
                ],
            ],
            'pages' => [
                'multiple' => true,
                'type' => [
                    'page' => ['type' => PARAM_INT],
                    'url' => ['type' => PARAM_URL],
                    'active' => ['type' => PARAM_BOOL],
                    'content' => [
                        'optional' => true,
                        'type' => PARAM_RAW,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $pagecount = ceil($this->recordcount / $this->pagesize);

        $pages = [];
        if ($pagecount > 1) {
            for ($pageno = 1; $pageno <= $pagecount; $pageno++) {
                $pages[] = [
                    'page' => $pageno,
                    'url' => call_user_func_array($this->pageurlcallback, [$pageno, $this->pagesize]),
                    'active' => $pageno === $this->pagenumber,
                    'content' => null,
                ];
            }
        }

        return [
            'itemsperpage' => $this->pagesize,
            'buttons' => [
                'first' => false,
                'previous' => false,
                'next' => false,
                'last' => false,
            ],
            'pages' => $pages,
        ];
    }
}
