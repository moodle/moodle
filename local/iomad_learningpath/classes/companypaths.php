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

use company;

class companypaths {

    protected $context;

    protected $companyid;

    protected $company;

    public function __construct($companyid, $context) {
        $this->context = $context;
        $this->companyid = $companyid;
        $this->company = new \company($companyid);
    }

    /**
     * Convenience function to return the company
     * @return object
     */
    public function get_company() {
        return $this->company;
    }

    /**
     * Get learning paths for company.
     * @return array
     */
    public function get_paths() {
        global $DB;

        $paths = $DB->get_records('iomad_learningpath', array('company' => $this->companyid));
        
        return $paths;
    }

    /**
     * Get/check path
     * @param int $id (0 = new/empty)
     * @param bool $create new if does not exist
     * @return object $path
     */
    public function get_path($id, $create = true) {
        global $DB;

        if ($path = $DB->get_record('iomad_learningpath', array('id' => $id))) {
            if ($path->company != $this->companyid) {
                print_error('companymismatch', 'local_iomad_learningpath');
            }

            return $path;
        } else {
            if (!$create) {
                print_error('nopath', 'local_iomad_learningpath');
            }
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

    /**
     * Set breadcrumb correctly for learning paths admin
     * @param string $linktext (optional) final link
     * @param moodle_url $linkurl (optional) final link
     */
    public function breadcrumb($linktext = '', $linkurl = null) {
        global $PAGE;

        $PAGE->navbar->ignore_active();
        $PAGE->navbar->add(get_string('administrationsite'));
        $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new \moodle_url('/local/iomad_dashboard/index.php'));
        $PAGE->navbar->add(get_string('managetitle', 'local_iomad_learningpath'), new \moodle_url('/local/iomad_learningpath/manage.php'));
        if ($linktext) {
            $PAGE->navbar->add($linktext, $linkurl);
        }
    }

    /**
     * Get course list for given path
     * @param int $pathid
     * @return array
     */
    public function get_courselist($pathid) {
        global $DB;

        $sql = 'SELECT lpc.*, c.id courseid, c.shortname shortname, c.fullname fullname
            FROM {iomad_learningpathcourse} lpc JOIN {course} c ON lpc.course = c.id
            WHERE lpc.path = :pathid
            ORDER BY lpc.sequence';
        $courses = $DB->get_records_sql($sql, ['pathid' => $pathid]);

        return $courses;
    }

    /**
     * Get prospective course list for company
     * @param array $courses already selected courses
     * @return array of courses
     */
    public function get_prospective_courses($selectedcourses = []) {
        global $DB;

        $topdepartment = company::get_company_parentnode($this->companyid);
        $depcourses = company::get_recursive_department_courses($topdepartment->id);

        $courses = array();
        foreach ($depcourses as $depcourse) {
            
            // Do not include courses already selected
            if (array_key_exists($depcourse->courseid, $selectedcourses)) {
                continue;
            }
            $course = $DB->get_record('course', ['id' => $depcourse->courseid]);
            $courses[] = $course;
        }

        return $courses;
    }

}
