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
 * List of not migrated HVP activities.
 *
 * @package     tool_migratehvp2h5p
 * @category    output
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_migratehvp2h5p\output;

use tool_migratehvp2h5p\api;
use hvpactivities_table;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * List of not migrated HVP activities.
 *
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class listnotmigrated implements renderable, templatable {

    /** @var data_requests_table $table The data requests table. */
    protected $table;

    /**
     * Contructor.
     *
     * @param hvpactivities_table $table The data requests table.
     */
    public function __construct(\tool_migratehvp2h5p\output\hvpactivities_table $table) {
        $this->table = $table;
    }

    /**
     * Export the page data for the mustache template.
     *
     * @param renderer_base $output renderer to be used to render the page elements.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = (object)[
            'formaction' => 'index.php',
            'settings' => [
                [
                    'name' => 'keeporiginal',
                    'label' => get_string('keeporiginal', 'tool_migratehvp2h5p'),
                    'options' => [
                            ['value' => api::HIDEORIGINAL, 'text' => get_string('keeporiginal_hide', 'tool_migratehvp2h5p')],
                            ['value' => api::DELETEORIGINAL, 'text' => get_string('keeporiginal_delete', 'tool_migratehvp2h5p')],
                            ['value' => api::KEEPORIGINAL, 'text' => get_string('keeporiginal_nothing', 'tool_migratehvp2h5p')],
                    ]
                ],
                [
                    'name' => 'copy2cb',
                    'label' => get_string('copy2cb', 'tool_migratehvp2h5p'),
                    'options' => [
                        [
                            'value' => api::COPY2CBYESWITHLINK,
                            'text' => get_string('copy2cb_yeswithlink', 'tool_migratehvp2h5p')
                        ],
                        [
                            'value' => api::COPY2CBYESWITHOUTLINK,
                            'text' => get_string('copy2cb_yeswithoutlink', 'tool_migratehvp2h5p')
                        ],
                        [
                            'value' => api::COPY2CBNO,
                            'text' => get_string('copy2cb_no', 'tool_migratehvp2h5p')
                        ],
                    ],
                ],
            ],
        ];

        ob_start();
        $this->table->out($this->table->get_page_size(), true);
        $hvpactivities = ob_get_contents();
        ob_end_clean();
        $data->hvpactivities = $hvpactivities;

        return $data;
    }
}
