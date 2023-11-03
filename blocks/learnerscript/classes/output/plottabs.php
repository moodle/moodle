<?php
// Standard GPL and phpdocs
namespace block_learnerscript\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
class plottabs implements renderable, templatable {
    var $plottabs;
    var $reportid;
    var $params;
    var $enableplots;
    public $filterform;
    public function __construct($plottabs, $reportid, $params, $enableplots, $filterform = false) {
        $this->plottabs = $plottabs;
        $this->reportid = $reportid;
        $this->params = $params;
        $this->enableplots = $enableplots;
        $this->filterform = $filterform;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT;
        $data = new stdClass();
        // sizeof($this->plottabs) > 1 ? $data->multiplot = 1 : null;
        $data->multiplot = true;
        $data->issiteadmin = is_siteadmin();
        $data->plottabs = $this->plottabs;
        $data->editicon = $OUTPUT->image_url('/t/edit');
        $data->deleteicon = $OUTPUT->image_url('/t/delete');
        $data->loading = $OUTPUT->image_url('loading','block_learnerscript');
        $data->reportid = $this->reportid;
        $data->params = $this->params;
        $data->enableplots = $this->enableplots;
        $data->filterform = $this->filterform;
        return $data;
    }
}