<?php
/**
 * Convert Step
 *
 * @throws backup_exception
 */
abstract class convert_step extends base_step {

    public function __construct($name, convert_task $task = null) {
        parent::__construct($name, $task);
    }

    protected function get_convertid() {
        if (!$this->task instanceof convert_task) {
            throw new backup_exception('not_specified_convert_task'); // @todo Define string
        }
        return $this->task->get_convertid();
    }

    /**
     * @throws backup_exception
     * @return plan_converter
     */
    protected function get_converter() {
        if (!$this->task instanceof convert_task) {
            throw new backup_exception('not_specified_convert_task'); // @todo Define string
        }
        return $this->task->get_converter();
    }

    public function execute_after_convert() {
        // Default nothing
    }
}