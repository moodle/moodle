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

// @todo finnish this class...
abstract class moodle1_activity_task extends convert_task {
    // @todo Implement methods that will for example write out the activities/type_cmid/module.xml
}

// @todo finnish this class...
abstract class moodle1_block_task extends convert_task {
}