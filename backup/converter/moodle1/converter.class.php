<?php
/**
 * All of the task and step classes specific to moodle1 conversion
 */
require_once($CFG->dirroot.'/backup/converter/moodle1/taskslib.php');
require_once($CFG->dirroot.'/backup/converter/moodle1/stepslib.php');

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

        $this->get_plan()->add_task(new moodle1_root_task('root_task'));
        $this->get_plan()->add_task(new moodle1_course_task('courseinfo'));
        $this->get_plan()->add_task(new moodle1_final_task('final_task'));
    }
}
