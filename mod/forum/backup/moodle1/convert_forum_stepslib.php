<?php
/**
 * Convert forum
 */
class moodle1_forum_activity_structure_step extends convert_structure_step {
    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     *
     * NOTE: /MOD/ACTIVITYNAME XML path does not actually exist.  The moodle1_converter
     * class automatically transforms the /MOD path to include the activity name.
     */
    protected function define_structure() {
        return array(
            new convert_path_element('forum', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FORUM'),
            // new convert_path_element('foo', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FORUM/FOO'),  // Example of sub-path
        );
    }

    public function convert_forum($data) {
        print_object($data);
    }

    public function convert_foo($data) {
        print_object($data);
    }
}