<?php
class moodle1_root_task extends convert_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->add_step(new convert_create_and_clean_temp_stuff('create_and_clean_temp_stuff'));

        // At the end, mark it as built
        $this->built = true;
    }

}

/**
 * @todo Not used at the moment
 */
class moodle1_final_task extends convert_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->add_step(new convert_drop_and_clean_temp_stuff('drop_and_clean_temp_stuff'));

        // At the end, mark it as built
        $this->built = true;
    }
}

class moodle1_course_task extends convert_task {
    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        $this->add_step(new moodle1_course_structure_step('course_info'));
        $this->add_step(new moodle1_section_structure_step('course_section'));

        // At the end, mark it as built
        $this->built = true;
    }
}

abstract class moodle1_plugin_task extends convert_task {
    /**
     * Plugin specific steps
     */
    abstract protected function define_my_steps();
}

abstract class moodle1_activity_task extends moodle1_plugin_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->define_my_steps();

        // @todo Risky?
        list($plugin, $name) = explode('_', $this->name);

        $this->add_step(new moodle1_module_structure_step("{$this->name}_module", $name));
        $this->built = true;
    }
}

abstract class moodle1_block_task extends moodle1_plugin_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->define_my_steps();
        $this->built = true;
    }
}