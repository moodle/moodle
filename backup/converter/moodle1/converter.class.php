<?php
/**
 * This will be the Moodle 1 to Moodle 2 Converter
 */
class moodle1_converter extends plan_converter {
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

    public function build_plan() {
        $this->xmlparser = new progressive_parser();
        $this->xmlparser->set_file($this->get_tempdir() . '/moodle.xml');
        $this->xmlprocessor = new convert_structure_parser_processor($this); // @todo Probably move this
        $this->xmlparser->set_processor($this->xmlprocessor);

        $this->get_plan()->add_task(new moodle1_course_task('courseinfo'));
    }
}

// @todo Where to store these classes?

class moodle1_course_task extends convert_task {
    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        $this->add_step(new moodle1_course_structure_step('course_info', $this));

        // At the end, mark it as built
        $this->built = true;
    }
}

class moodle1_course_structure_step extends convert_structure_step {
    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    protected function define_structure() {
        $paths   = array();
        $paths[] = new convert_path_element('courseinfo', '/MOODLE_BACKUP/COURSE/HEADER');

        return $paths;
    }

    public function convert_courseinfo($data) {
        print_object($data);
    }
}