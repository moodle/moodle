<?php
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
            $this->xmlwriter->begin_tag('course', array(
                'id' => $this->id,
                'contextid' => convert_helper::get_contextid($this->id, 'course')
            ));
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
