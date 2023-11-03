<?php
// Standard GPL and phpdocs
namespace block_learnerscript\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
class reporttable implements renderable, templatable {
    var $tablehead;
    var $tableid;
    var $exports;
    var $reportid;
    var $reportsql;
    var $debugsql;
    var $includeexport;
    var $instanceid;
    var $reporttype;
    public function __construct($tabledetails,$tableid,$exports,$reportid,$reportsql,$debugsql = false,$includeexport=false, $instanceid = null, $reporttype) {
        isset($tabledetails['tablehead']) ? $this->tablehead = $tabledetails['tablehead'] : null;
        $this->tableproperties = $tabledetails['tableproperties'];
        $this->tableid = $tableid;
        $this->exports = $exports;
        $this->reportid = $reportid;
        $this->reportsql = $reportsql;
        $this->debugsql = $debugsql;
        $this->includeexport = $includeexport;
        $this->instanceid = $instanceid;
        $this->reporttype = $reporttype;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT;
        $data = new stdClass();
        $data->tablehead = $this->tablehead;
        $data->tableid = $this->tableid;
        $data->loading = $OUTPUT->image_url('loading','block_learnerscript');
        $data->exports = $this->exports;
        $data->reportid = $this->reportid;
        $data->reportsql = $this->reportsql;
        $data->debugsql = $this->debugsql;
        $data->includeexport = $this->includeexport;
        $data->download_icon = $OUTPUT->image_url('download_icon', 'block_learnerscript');
        $data->reportinstance = $this->instanceid ? $this->instanceid : $this->reportid;
        $data->reporttype = $this->reporttype;
        $exportparams = '';
        unset($_GET['id']);
        if(!empty($_GET)) {
            foreach ($_GET as $key => $val){
                $exportparams .= "&$key=$val";
            }
        }
        if(!empty($_POST)) {
            foreach ($_POST as $key => $val){
                if (strpos($key, 'filter_') !== false){
                    $exportparams .= "&$key=$val";
                }
            }
        }
        $data->exportparams = $exportparams;
        $arraydata = (array)$data + $this->tableproperties;
        return $arraydata;
    }
}