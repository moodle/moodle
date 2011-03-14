<?php
/**
 * Convert Plan
 */
class convert_plan extends base_plan implements loggable {
    /**
     * @var plan_converter
     */
    protected $converter;

    public function __construct(plan_converter $converter) {
        $this->converter = $converter;
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

    public function get_basepath() {
        return $this->converter->get_convertdir();
    }

    /**
     * @return plan_converter
     */
    public function get_converter() {
        return $this->converter;
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

    /**
     * Execute the after_restore methods of all the executed tasks in the plan
     */
    public function execute_after_convert() {
        // Simply iterate over each task in the plan and delegate to them the execution
        foreach ($this->tasks as $task) {
            $task->execute_after_convert();
        }
    }
}