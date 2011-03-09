<?php
/**
 * Executable Step for Converters
 */
abstract class convert_execution_step extends convert_step {

    public function execute() {
        return $this->define_execution();
    }

    /**
     * Function that will contain all the code to be executed
     */
    abstract protected function define_execution();
}