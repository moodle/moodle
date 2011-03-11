<?php
/**
 * Convert Task
 */
abstract class convert_task  extends base_task {
    public function __construct($name, convert_plan $plan = null) {
        parent::__construct($name, $plan);
    }

    public function get_convertid() {
        return $this->plan->get_backupid();
    }

    /**
     * @return plan_converter
     */
    public function get_converter() {
        return $this->plan->get_converter();
    }

    protected function define_settings() {
        // None
    }
}