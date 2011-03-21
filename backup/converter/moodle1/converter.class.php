<?php
/**
 * All of the task and step classes specific to moodle1 conversion
 */
require_once($CFG->dirroot.'/backup/converter/moodle1/taskslib.php');
require_once($CFG->dirroot.'/backup/converter/moodle1/stepslib.php');

/**
 * This will be the Moodle 1 to Moodle 2 Converter
 */
class moodle1_converter extends plan_converter {
    /**
     * The current module being processed
     *
     * @var string
     */
    protected $currentmod = '';

    /**
     * The current block being processed
     *
     * @var string
     */
    protected $currentblock = '';

    /**
     * @return boolean
     */
    public function can_convert() {
        // Then look for MOODLE1 (moodle1) format
        $filepath = $this->get_tempdir() . '/moodle.xml';
        if (file_exists($filepath)) { // Looks promising, lets load some information
            $handle = fopen($filepath, "r");
            $first_chars = fread($handle,200);
            fclose($handle);

            // Check if it has the required strings
            if (strpos($first_chars,'<?xml version="1.0" encoding="UTF-8"?>') !== false &&
                strpos($first_chars,'<MOODLE_BACKUP>') !== false &&
                strpos($first_chars,'<INFO>') !== false) {

                return true;
            }
        }
        return false;
    }


    /**
     * Path transformation for modules and blocks.  Here we
     * are collapsing paths that use the plugin's name.
     */
    public function add_structures($processingobject, array $structures) {
        parent::add_structures($processingobject, $structures);

        foreach ($structures as $element) {
            $path = $element->get_path();

            // @todo Add same for blocks
            $path = preg_replace('/^\/MOODLE_BACKUP\/COURSE\/MODULES\/MOD\/(\w+)\//', '/MOODLE_BACKUP/COURSE/MODULES/MOD/', $path);
            if (!empty($path) and $path != $element->get_path()) {
                $this->xmlprocessor->add_path($path, false);
            }
        }
    }

    /**
     * Path transformation for modules and blocks.  Here we
     * are expanding paths to include the plugin's name.
     */
    public function process($data) {
        $path = $data['path'];

        // @todo Same path manipulation for blocks
        if ($path == '/MOODLE_BACKUP/COURSE/MODULES/MOD') {
            $this->currentmod = strtoupper($data['tags']['MODTYPE']);
            $path = '/MOODLE_BACKUP/COURSE/MODULES/MOD/'.$this->currentmod;

        } else if (strpos($path, '/MOODLE_BACKUP/COURSE/MODULES/MOD') === 0) {
            $path = str_replace('/MOODLE_BACKUP/COURSE/MODULES/MOD', '/MOODLE_BACKUP/COURSE/MODULES/MOD/'.$this->currentmod, $path);
        }
        if ($path != $data['path']) {
            // Have relaxed error handling on path transformations...
            if (!array_key_exists($path, $this->pathelements)) {
                debugging("Path transformation error, $path is not registered, probably similar to another plugin");
                return;
            }
            $data['path'] = $path;
        }
        parent::process($data);
    }

    public function build_plan() {
        $this->xmlparser = new progressive_parser();
        $this->xmlparser->set_file($this->get_tempdir() . '/moodle.xml');
        $this->xmlprocessor = new convert_structure_parser_processor($this); // @todo Probably move this
        $this->xmlparser->set_processor($this->xmlprocessor);

        // These paths are dispatched by the converter through path transformation
        $this->xmlprocessor->add_path('/MOODLE_BACKUP/COURSE/MODULES/MOD', false);
        // @todo Add the same for blocks

        $this->get_plan()->add_task(new moodle1_root_task('root_task'));
        $this->get_plan()->add_task(new moodle1_course_task('courseinfo'));

        // Build plugin tasks
        convert_factory::build_plugin_tasks($this, 'mod', 'activity');
        convert_factory::build_plugin_tasks($this, 'block');

        $this->get_plan()->add_task(new moodle1_final_task('final_task'));
    }
}
