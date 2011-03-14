<?php

abstract class convert_structure_step extends convert_step {

    final public function execute() {

        if (!$this->execute_condition()) { // Check any condition to execute this
            return;
        }

        // Get restore_path elements array adapting and preparing it for processing
        $structure = $this->define_structure();
        if (!is_array($structure)) {
            throw new restore_step_exception('restore_step_structure_not_array', $this->get_name());  // @todo Change exception
        }
        $this->get_converter()->add_structures($this, $structure);
    }

    /**
     * As far as restore structure steps are implementing restore_plugin stuff, they need to
     * have the parent task available for wrapping purposes (get course/context....)
     */
    public function get_task() {
        return $this->task;
    }

    /**
     * To conditionally decide if one step will be executed or no
     *
     * For steps needing to be executed conditionally, based in dynamic
     * conditions (at execution time vs at declaration time) you must
     * override this function. It will return true if the step must be
     * executed and false if not
     */
    protected function execute_condition() {
        return true;
    }

    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    abstract protected function define_structure();
}
