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

        $this->get_plan()->add_task(new moodle1_root_task('root_task'));
        $this->get_plan()->add_task(new moodle1_course_task('courseinfo'));
        $this->get_plan()->add_task(new moodle1_final_task('final_task'));
    }
}

// @todo Where to store these classes?

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

        // At the end, mark it as built
        $this->built = true;
    }
}

class moodle1_course_structure_step extends convert_structure_step {
    protected $id;
    /**
     * @var xml_writer
     */
    protected $xmlwriter;

    protected $deprecated = array(
        'roles_overrides',
        'roles_assignments',
        'cost',
        'currancy',
        'defaultrole',
        'enrol',
        'enrolenddate',
        'enrollable',
        'enrolperiod',
        'enrolstartdate',
        'expirynotify',
        'expirythreshold',
        'guest',
        'notifystudents',
        'password',
        'student',
        'students',
        'teacher',
        'teachers',
        'metacourse',
    );

    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    protected function define_structure() {
        $paths   = array();
        $paths[] = new convert_path_element('course', '/MOODLE_BACKUP/COURSE/HEADER');
        $paths[] = new convert_path_element('category', '/MOODLE_BACKUP/COURSE/HEADER/CATEGORY');

        return $paths;
    }

    public function open_writer() {
        if (!$this->xmlwriter instanceof xml_writer) {
            if (empty($this->id)) {
               throw new backup_exception('noidfound'); // @todo define string or dynamically make id
            }
            $directory = $this->get_converter()->get_convertdir().'/course';
            if (!check_dir_exists($directory)) {
                throw new backup_exception('failedtomakeconvertdir'); // @todo Define this string
            }
            $this->xmlwriter = new xml_writer(
                new file_xml_output($directory.'/course.xml')
            );
            $this->xmlwriter->start();
            $this->xmlwriter->begin_tag('course', array('id' => $this->id, 'contextid' => 'TODO')); // @todo make contextid
        }
    }

    /**
     * This is actually called twice because category is defined
     * right after ID in the XML... any way around that?  Only
     * idea is to patch Moodle 1.9
     *
     * @throws backup_exception
     * @param  $data
     * @return void
     */
    public function convert_course($data) {
        // print_object($data);  // DEBUG
        if (array_key_exists('ID', $data)) {
            $this->id = $data['ID'];
            unset($data['ID']);
        }
        if (empty($data)) {
            return;
        }
        $this->open_writer();

        foreach ($data as $name => $value) {
            $name = strtolower($name);

            if (in_array($name, $this->deprecated)) {
                continue;
            }
            $this->xmlwriter->full_tag($name, $value);
        }
    }

    public function convert_category($data) {
        // print_object($data);  // DEBUG
        $this->open_writer();
        $this->xmlwriter->begin_tag('category', array('id' => $data['ID']));
        $this->xmlwriter->full_tag('name', $data['NAME']);
        $this->xmlwriter->end_tag('category');
    }

    public function execute_after_convert() {
        if ($this->xmlwriter instanceof xml_writer) {
            $this->xmlwriter->end_tag('course');
            $this->xmlwriter->stop();
            unset($this->xmlwriter);
            // var_dump(file_get_contents($this->get_converter()->get_convertdir().'/course/course.xml')); // DEBUG
        }
    }
}