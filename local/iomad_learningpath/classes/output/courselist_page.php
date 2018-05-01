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
 * Course list management for Iomad Learning Paths
 *
 * @package    local_iomad_learninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_iomad_learningpath\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

class courselist_page implements renderable, templatable {

    protected $context;

    protected $path;

    protected $groups;

    protected $categories;

    public function __construct($context, $path, $groups, $categories) {
        $this->context = $context;
        $this->path = $path;
        $this->groups = $groups;
        $this->categories = $categories;
    }

    /**
     * Export page contents for template
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        
        // fix courses list inside groups
        $groups = $this->groups;
        foreach ($groups as $group) {
            $group->courses = array_values($group->courses);
            $group->showdelete = (count($group->courses) == 0) && (count($groups) > 1);
        }

        $data = new stdClass();
        $data->path = $this->path;
        $data->groups = array_values($groups);
        $data->categories = array_values($this->categories);
        $data->iscourses = !empty($this->courses);
        $data->done = $output->single_button(
            new \moodle_url('/local/iomad_learningpath/manage.php'),
            get_string('done', 'local_iomad_learningpath')
        );

        return $data;
    }

}

