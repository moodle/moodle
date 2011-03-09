<?php
/**
 * Convert Plan
 */
class convert_plan extends base_plan implements loggable {

    protected $converter;

    public function __construct(plan_converter $converter) {
        global $CFG;

        $this->converter = $converter;
        $this->basepath   = $CFG->dataroot . '/temp/backup/' . $converter->get_backupid();
        parent::__construct('convert_plan');
    }

    /**
     * This function will be responsible for handling the params, and to call
     * to the corresponding logger->process() once all modifications in params
     * have been performed
     */
    public function log($message, $level, $a = null, $depth = null, $display = false) {
        // TODO: Implement log() method.
    }

    public function get_converterid() {
        return $this->converter->get_id();
    }

    /**
     * Function responsible for building the tasks of any plan
     * with their corresponding settings
     * (must set the $built property to true)
     */
    public function build() {
        // This seems circular for no real reason....
        $this->converter->build_plan();
        $this->built = true;
    }
}