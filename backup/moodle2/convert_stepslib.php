<?php
/**
 * Do convert plan related set up
 */
class convert_create_and_clean_temp_stuff extends convert_execution_step {

    protected function define_execution() {
        backup_controller_dbops::create_backup_ids_temp_table($this->get_convertid()); // Create ids temp table
    }
}

/**
 * Do convert plan related tear down
 */
class convert_drop_and_clean_temp_stuff extends convert_execution_step {

    protected function define_execution() {
        // We want to run after execution
    }

    public function execute_after_convert() {
        backup_controller_dbops::drop_backup_ids_temp_table($this->get_convertid()); // Drop ids temp table
    }


}