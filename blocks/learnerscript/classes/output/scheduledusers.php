<?php
// Standard GPL and phpdocs
namespace block_learnerscript\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
class scheduledusers implements renderable, templatable {
    var $reportid;
    var $reqimage;
    var $roles_list;
    var $selectedusers;
    var $scheduleid;
    var $reportinstance;
    public function __construct($reportid, $reqimage, $roles_list, $selectedusers,$scheduleid, $reportinstance) {
        $this->reportid = $reportid;
        $this->reqimage = $reqimage;
        $this->roles_list = $roles_list;
        $this->selectedusers = $selectedusers;
        $this->scheduleid = $scheduleid;
        $this->reportinstance = $reportinstance;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->reportid = $this->reportid;
        $data->reqimage = $this->reqimage;
        $data->roles_list = $this->roles_list;
        $data->selectedusers = $this->selectedusers;
        $data->scheduleid = $this->scheduleid;
        $data->reportinstance = $this->reportinstance;
        return $data;
    }
}