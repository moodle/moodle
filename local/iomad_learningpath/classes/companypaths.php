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
 * "Business" class for Iomad Learning Paths
 *
 * @package    local_iomadlearninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_iomad_learningpath;

defined('MOODLE_INTERNAL') || die();

class companypaths {

    protected $context;

    protected $companyid;

    public function __construct($companyid, $context) {
        $this->context = $context;
        $this->companyid = $companyid;
    }

    /**
     * Get learning paths for company.
     * @return array
     */
    public function get_paths() {
        global $DB;

        $paths = $DB->get_records('local_iomad_learningpath', array('company' => $this->companyid));
        
        return $paths;
    }

    /**
     * Get/check path
     * @param in $id (0 = new/empty)
     * @return object $path
     */
    public function get_path($id) {
        global $DB;

        if ($path = $DB->get_record('local_iomad_learningpath', array('id' => $id))) {
            if ($path->company != $this->companyid) {
                throw new \Exception("Company id does not match expected");
            }

            return $path;
        } else {
            $path = new \stdClass;
            $path->company = $this->companyid;
            $path->timecreated = time();
            $path->timeupdated = time();
            $path->name = '';
            $path->description = '';
            $path->active = 0;

            return $path;
        }
    }

    /**
     * Take image uploaded on learning path form and 
     * process for size and thumbnail
     * @param object $context
     * @param int $id learning path id
     */
    public function process_image($context, $id) {
        global $CFG;

        // Get file storage
        $fs = get_file_storage();

        // find the files
        $files = $fs->get_area_files($context->id, 'local_iomad_learningpath', 'picture', $id);
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }

            // Process main picture
            $picture = $file->resize_image(null, 150);

            // store mainpicture
            if ($oldfile = $fs->get_file($context->id, 'local_iomad_learningpath', 'mainpicture', $id, '/', 'picture.png')) {
                $oldfile->delete();
            }
            $fileinfo = [
                'contextid' => $context->id,
                'component' => 'local_iomad_learningpath',
                'filearea' => 'mainpicture',
                'itemid' => $id,
                'filepath' => '/',
                'filename' => 'picture.png',
            ];
            $fs->create_file_from_string($fileinfo, $picture);

            // Process thumbnail
            $thumb = $file->resize_image(null, 50);

            // store thumbnail
            if ($oldfile = $fs->get_file($context->id, 'local_iomad_learningpath', 'thumbnail', $id, '/', 'thumbnail.png')) {
                $oldfile->delete();
            }
            $fileinfo = [
                'contextid' => $context->id,
                'component' => 'local_iomad_learningpath',
                'filearea' => 'thumbnail',
                'itemid' => $id,
                'filepath' => '/',
                'filename' => 'thumbnail.png',
            ];
            $fs->create_file_from_string($fileinfo, $thumb);
        }
    }

}
