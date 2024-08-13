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

namespace core_contentbank\output;

use renderable;
use templatable;
use renderer_base;
use core_contentbank\content;

/**
 * Content bank Custom fields renderable class.
 *
 * @package   core_contentbank
 * @copyright 2024 Daniel Neis Araujo <daniel@adapta.online>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customfields implements renderable, templatable {

    /**
     * Constructor.
     *
     * @param \core_contentbank\content $content The content object.
     */
    public function __construct(content $content) {
        $this->content = $content;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $DB;

        $context = new \stdClass();

        $context->url = $this->content->get_file_url();
        $context->name = $this->content->get_name();

        $handler = \core_contentbank\customfield\content_handler::create();
        $customfields = $handler->get_instance_data($this->content->get_id());
        $context->data = $handler->display_custom_fields_data($customfields);

        return $context;
    }
}
