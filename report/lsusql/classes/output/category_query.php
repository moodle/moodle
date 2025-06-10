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

namespace report_lsusql\output;

use context;
use moodle_url;
use renderable;
use report_lsusql\local\query;
use report_lsusql\local\category;
use templatable;


/**
 * Renderable class to show the query item in category page.
 *
 * @package    report_lsusql
 * @copyright  2021 The Open University
 * @copyright  2022 Louisiana State University
 * @copyright  2022 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_query implements renderable, templatable {
    /** @var query Query object. */
    private $query;

    /** @var query Category object. */
    private $category;

    private $context;

    /** @var moodle_url Return url. */
    private $returnurl;

    /**
     * Create the category renderable object.
     * @param query $query Query object.
     * @param category $category
     * @param context $context Context to check the capability.
     * @param moodle_url $returnurl Return url.
     */
    public function __construct(query $query, category $category, context $context, moodle_url $returnurl) {
        $this->query = $query;
        $this->category = $category;
        $this->context = $context;
        $this->returnurl = $returnurl;
    }

    public function export_for_template(\renderer_base $output) {
        $imgedit = $output->pix_icon('t/edit', get_string('edit'));
        $imgdelete = $output->pix_icon('t/delete', get_string('delete'));

        return [
            'id' => $this->query->get_id(),
            'displayname' => $this->query->get_displayname(),
            'url' => $this->query->get_url()->out(false),
            'canedit' => $this->query->can_edit($this->context),
            'timenote' => $this->query->get_time_note(),
            'editbutton' => [
                'url' => $this->query->get_edit_url($this->returnurl)->out(false),
                'img' => $imgedit
            ],
            'deletebutton' => [
                'url' => $this->query->get_delete_url($this->returnurl)->out(false),
                'img' => $imgdelete
            ],
            'capability' => $this->query->get_capability_string()
        ];
    }
}
