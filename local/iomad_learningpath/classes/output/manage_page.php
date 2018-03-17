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
 * Manage page for Iomad Learning Paths
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

class manage_page implements renderable, templatable {

    protected $context;

    protected $paths;

    public function __construct($context, $paths) {
        $this->context = $context;
        $this->paths = $paths;
    }

    /**
     * Add various links to paths
     * @param renderer_base $output
     */
    protected function munge_paths(renderer_base $output) {
        $fs = get_file_storage();
        foreach ($this->paths as $path) {
            $thumb = $fs->get_file($this->context->id, 'local_iomad_learningpath', 'thumbnail', $path->id, '/', 'thumbnail.png');
            $path->linkedit = new \moodle_url('/local/iomad_learningpath/editpath.php', ['id' => $path->id]);
            if ($thumb) {
                $path->linkthumbnail = \moodle_url::make_pluginfile_url($thumb->get_contextid(), $thumb->get_component(), $thumb->get_filearea(), 
                    $thumb->get_itemid(), $thumb->get_filepath(), $thumb->get_filename());
            } else {
                $path->linkthumbnail = $output->image_url('learningpath', 'local_iomad_learningpath');
            }
            $path->linkstudents = new \moodle_url('/local/iomad_learningpath/students.php', ['id' => $path->id]);
            $path->linkcourses = new \moodle_url('/local/iomad_learningpath/courselist.php', ['id' => $path->id]);
        }
    }

    /**
     * Export page contents for template
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $this->munge_paths($output);
        $data = new stdClass();
        $data->paths = array_values($this->paths);
        $data->ispaths = !empty($this->paths);
        $data->linknew = new \moodle_url('/local/iomad_learningpath/editpath.php');

        return $data;
    }

}

