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
 * Students assignment management for Iomad Learning Paths
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

class students_page implements renderable, templatable {

    protected $context;

    protected $path;

    public function __construct($context, $path) {
        $this->context = $context;
        $this->path = $path;
    }

    /**
     * Export page contents for template
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->path = $this->path;
        $data->done = $output->single_button(
            new \moodle_url('/local/iomad_learningpath/manage.php'),
            get_string('done', 'local_iomad_learningpath')
        );

        return $data;
    }

}

