<?php
// Standard GPL and phpdocs
namespace block_learnerscript\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use block_learnerscript\local\ls;
class design implements renderable, templatable {

    var $reporttype;
    var $reportid;

    public function __construct($report,$reportid) {
        $this->reporttype = $report->type;
        $this->reportid = $reportid;
        $this->report = $report;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT, $CFG;
        $data = new stdClass();
        $comp = '';
        $data->sqlreporttype = $this->reporttype == 'sql' ? true : false;
        require_once($CFG->dirroot . '/blocks/learnerscript/components/conditions/component.class.php');
        $elements = (new ls)->cr_unserialize($this->report->components);
        $elements = isset($elements[$comp]['elements']) ? $elements[$comp]['elements'] : array();
        $componentclassname = 'component_conditions';
        $compclass = new $componentclassname($this->report->id);
        $plugins = get_list_of_plugins('blocks/learnerscript/components/conditions');
        $optionsplugins = array();
        if ($compclass->plugins) {
            $currentplugins = array();
            if ($elements) {
                foreach ($elements as $e) {
                    $currentplugins[] = $e['pluginname'];
                }
            }

            foreach ($plugins as $p) {
                require_once($CFG->dirroot . '/blocks/learnerscript/components/conditions/' . $p . '/plugin.class.php');
                $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $p;
                $pluginclass = new $pluginclassname($this->report);
                if (in_array($this->reporttype, $pluginclass->reporttypes)) {
                    if ($pluginclass->unique && in_array($p, $currentplugins)) {
                        continue;
                    }
                    $optionsplugins[$p] = get_string($p, 'block_learnerscript');
                }
            }
        }


        $data->enableconditions = empty($optionsplugins) ? false : true;
        $data->loading = $OUTPUT->image_url('loading','block_learnerscript');
        $data->reportid = $this->reportid;
        $data->reporttype = $this->reporttype == 'userprofile' || $this->reporttype == 'courseprofile'  ? false : true;
        $debug = optional_param('debug', false, PARAM_BOOL);
        if ($debug) {
            $data->debugdisplay = true;
        }
        $data->params = $_SERVER['QUERY_STRING'];
        return $data;
    }
}