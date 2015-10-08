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
 * Class containing data for a competency.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use tool_lp\api;

/**
 * Class containing data for related competencies.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class related_competencies implements renderable, templatable {

    var $relatedcompetencies = null;

    /**
     * Construct this renderable.
     *
     * @param int $competencyid
     */
    public function __construct($competencyid) {
        $this->relatedcompetencies = api::list_related_competencies($competencyid);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->relatedcompetencies = array();
        if ($this->relatedcompetencies) {
            foreach ($this->relatedcompetencies as $competency) {
                $record = $competency->to_record();
                // TODO Format using the context from the framework.
                $record->descriptionformatted = format_text($record->description,
                    $record->descriptionformat, $options);
                $data->relatedcompetencies[] = $record;
            }
        }

        // We checked the user permissions in the constructor.
        $data->showdeleterelatedaction = true;

        return $data;
    }
}
