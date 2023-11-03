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
 * LearnerScript Reports
 * A Moodle block for configure LearnerScript Reports
 * @package block_learnerscript
 * @author: Arun Kumar Mukka
 * @date: 2018
 */
namespace block_learnerscript\output;
defined('MOODLE_INTERNAL') || die();
use renderable;
use renderer_base;
use templatable;
use stdClass;
class lsconfig implements renderable, templatable {
    public $status;
    public $importstatus;
    public function __construct($status, $importstatus) {
        $this->status = $status;
        $this->importstatus = $importstatus;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT, $PAGE, $CFG;
        $data = new stdClass();
        $lsconfigslideshowimages = $this->lsconfigslideshowimages();
        $data->slideshowimages = $lsconfigslideshowimages['slideshowimages'];
        $data->slideshowimagespath = $lsconfigslideshowimages['slideshowimagespath'];
        $data->importstatus = $this->status == 'import' ? true : false;

        $reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reportdashboard', false);
        if ($reportdashboardblockexists) {
            $redirecturl = $CFG->wwwroot . '/blocks/reportdashboard/dashboard.php';
        } else {
            $redirecturl = $CFG->wwwroot . '/blocks/learnerscript/managereport.php';
        }

        $data->redirecturl = $redirecturl;
        $data->importstatus = $this->importstatus;
        $data->lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
        $data->configurestatus = (($this->status == 'import' && !$data->lsreportconfigstatus)
                                    || $this->status == 'reset') ? true : false;
        return $data;
    }

    public function lsconfigslideshowimages() {
        global $CFG;
        $slideshowimagespath = '/blocks/learnerscript/images/slideshow/';
        $slideshowimages = array();
        if (is_dir($CFG->dirroot . $slideshowimagespath)) {
            $slideshowimages = scandir($CFG->dirroot . $slideshowimagespath,
                                        SCANDIR_SORT_ASCENDING);
        }
        $slideshowimagelist = array();
        if (!empty($slideshowimages)) {
            foreach ($slideshowimages as $slideshowimage) {
                    if ($slideshowimage == '.' || $slideshowimage == '..') {

                    } else {
                // if (exif_imagetype($CFG->wwwroot . $slideshowimagespath . $slideshowimage)) {
                    $slideshowimagelist[] = $CFG->wwwroot . $slideshowimagespath . $slideshowimage;
                // }
                }
            }
        }
        $slideshowimagesdata = array();
        $slideshowimagesdata['slideshowimagespath'] = $slideshowimagespath;
        $slideshowimagesdata['slideshowimages'] = $slideshowimagelist;
        return $slideshowimagesdata;
    }
}