<?php

/**
 * An attempt at a general/abstract moodle1 structure step converter
 */
abstract class moodle1_structure_step extends convert_structure_step {
    /**
     * @var xml_writer
     */
    protected $xmlwriter = NULL;

    /**
     * Return the relative path to the XML file that
     * this step writes out to.  Example: course/course.xml
     *
     * @abstract
     * @return string
     */
    abstract public function get_xml_filename();

    /**
     * Opens the XML writer - after calling, one is free
     * to use $xmlwriter
     *
     * @return void
     */
    public function open_xml_writer() {
        if (!$this->xmlwriter instanceof xml_writer) {
            $fullpath  = $this->get_basepath().'/'.$this->get_xml_filename();
            $directory = pathinfo($fullpath, PATHINFO_DIRNAME);

            if (!check_dir_exists($directory)) {
                throw new backup_exception('failedtomakeconvertdir'); // @todo Define this string
            }
            $this->xmlwriter = new xml_writer(
                new file_xml_output($fullpath)
            );
            $this->xmlwriter->start();
        }
    }

    /**
     * Close the XML writer
     *
     * At the moment, must close all tags before calling
     *
     * @return void
     */
    public function close_xml_writer() {
        if ($this->xmlwriter instanceof xml_writer) {
            $this->xmlwriter->stop();
            unset($this->xmlwriter);
            $this->xmlwriter = NULL;
            // var_dump(file_get_contents($this->get_basepath().'/'.$this->get_xml_filename())); // DEBUG
        }
    }

    /**
     * Return deprecated fields
     *
     * @return array
     */
    public function get_deprecated() {
        return array();
    }

    /**
     * Return renamed fields
     *
     * The key is the old name and the value is the new name.
     *
     * @return array
     */
    public function get_renamed() {
        return array();
    }

    /**
     * Return new fields and their values
     *
     * The key is the new field name and the value
     * is the value to write to the XML file.
     *
     * @return array
     */
    public function get_new() {
        return array();
    }

    /**
     * Last chance to modify the datum before
     * it is written to the XML file.
     *
     * @param string $name The tag/field name
     * @param mixed $datum The data belonging to $name
     * @return mixed
     */
    public function mutate_datum($name, $datum) {
        return $datum;
    }

    /**
     * General data converter
     *
     * Can remove deprecated fields, rename fields,
     * and manipulate data values before writing
     * to the XML file.
     *
     * @param array $data The array of data to process
     * @return void
     */
    public function convert_data(array $data) {
        // print_object($data);  // DEBUG

        $this->open_xml_writer();

        $deprecated = $this->get_deprecated();
        $renamed    = $this->get_renamed();

        foreach ($data as $name => $datum) {
            $name = strtolower($name);

            if (in_array($name, $deprecated)) {
                continue;
            }
            if (array_key_exists($name, $renamed)) {
                $name = $renamed[$name];
            }
            $this->xmlwriter->full_tag($name, $this->mutate_datum($name, $datum));
        }
        foreach ($this->get_new() as $name => $datum) {
            $this->xmlwriter->full_tag($name, $datum);
        }
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

/**
 * Converts all of the sections
 */
class moodle1_section_structure_step extends moodle1_structure_step {
    /**
     * Section ID
     * @var int
     */
    protected $id;

    /**
     * Module ID sequence
     * @var array
     */
    protected $sequence = array();

    /**
     * Section data array
     * @var array
     */
    protected $data = array();

    /**
     * Return the relative path to the XML file that
     * this step writes out to.  Example: course/course.xml
     *
     * @return string
     */
    public function get_xml_filename() {
        return "sections/section_$this->id/section.xml";
    }

    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    protected function define_structure() {
        $paths   = array();
        $paths[] = new convert_path_element('section', '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION');
        $paths[] = new convert_path_element('mod', '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');

        return $paths;
    }

    /**
     * This grabs section ID and stores data.
     * We must process the section's modules before
     * the section can be written out to XML
     *
     * @param  $data
     * @return void
     */
    public function convert_section($data) {
        if (!empty($this->id)) {
            $this->write_section_xml();
        }
        // print_object($data); // DEBUG
        $this->id   = $data['ID'];
        $this->data = $data;
    }

    /**
     * Writes out section XML
     *
     * @return void
     */
    public function write_section_xml() {
        $this->open_xml_writer();

        $this->xmlwriter->begin_tag('section', array('id' => $this->data['ID']));
        $this->xmlwriter->full_tag('number', $this->data['NUMBER']);
        $this->xmlwriter->full_tag('name', NULL);
        $this->xmlwriter->full_tag('summary', $this->data['SUMMARY']);
        $this->xmlwriter->full_tag('summaryformat', 1);
        $this->xmlwriter->full_tag('sequence', implode(',', $this->sequence));
        $this->xmlwriter->full_tag('visible', $this->data['VISIBLE']);
        $this->xmlwriter->end_tag('section');

        $this->close_xml_writer();

        // Reset
        $this->id = 0;
        $this->sequence = array();
        $this->data = array();
    }

    /**
     * Converting section module data - store
     * id for section sequence then throw all the
     * module data in the database for later use.
     *
     * @param  $data
     * @return void
     */
    public function convert_mod($data) {
        global $DB;

        // print_object($data); // DEBUG

        unset(
            $data['ROLES_OVERRIDES'],
            $data['ROLES_ASSIGNMENTS']
        );

        $this->sequence[] = $data['ID'];

        $info = new stdClass;
        $info->sectionid     = $this->id;
        $info->sectionnumber = $this->data['NUMBER'];
        foreach ($data as $name => $value) {
            $name = strtolower($name);
            $info->$name = $value;
        }

        $temp = new stdClass;
        $temp->itemname     = $data['TYPE'];
        $temp->itemid       = $data['INSTANCE'];
        $temp->parentitemid = $data['ID'];
        $temp->info         = base64_encode(serialize($info));

        $DB->insert_record('backup_ids_temp', $temp);
    }

    /**
     * Write out any section still left in memory
     */
    public function execute_after_convert() {
        if (!empty($this->id)) {
            $this->write_section_xml();
        }
    }
}

/**
 * Writes out an activity's module.xml file
 */
class moodle1_module_structure_step extends moodle1_structure_step {
    /**
     * Module name
     *
     * @var string
     */
    protected $type;

    /**
     * The current Module ID
     *
     * @var int
     */
    protected $moduleid;

    /**
     * @param string $type The module name
     */
    public function __construct($name, $type, convert_task $task = null) {
        $this->type = $type;
        parent::__construct($name, $task);
    }

    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    protected function define_structure() {
        return array();  // @todo Hack?
    }

    /**
     * Return the relative path to the XML file that
     * this step writes out to.  Example: course/course.xml
     *
     * @return string
     */
    public function get_xml_filename() {
        return "activities/{$this->type}_{$this->moduleid}/module.xml";
    }

    public function get_new() {
        return array(
            'visibleold' => 1,
            'completion' => 0,
            'completiongradeitemnumber' => NULL,
            'completionview' => 0,
            'completionexpected' => 0,
            'availablefrom' => 0,
            'availableuntil' => 0,
            'showavailability' => 1,
            'availability_info' => NULL,
        );
    }

    public function get_renamed() {
        return array(
            'type' => 'modulename',
        );
    }

    public function get_deprecated() {
        return array('id', 'instance');
    }

    public function execute_after_convert() {
        global $DB;

        $records = $DB->get_records('backup_ids_temp', array('backupid' => $this->get_convertid(), 'itemname' => $this->type));
        foreach ($records as $record) {
            $this->moduleid = $record->parentitemid;

            $this->open_xml_writer();
            $this->xmlwriter->begin_tag('module', array('id' => $this->moduleid, 'version' => '?'));  // @todo What to do for version?
            $this->convert_data((array) unserialize(base64_decode($record->info)));
            $this->xmlwriter->end_tag('module');
            $this->close_xml_writer();
        }
    }
}

/**
 * Write out a block's block.xml file
 */
class moodle1_block_structure_step extends moodle1_structure_step {
    /**
     * The block's instance ID
     *
     * @var int
     */
    protected $instanceid;

    /**
     * The block's name
     *
     * @var string
     */
    protected $blockname;

    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    protected function define_structure() {
        return array(
            new convert_path_element('block', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK'),
        );
    }

    /**
     * Return the relative path to the XML file that
     * this step writes out to.  Example: course/course.xml
     *
     * @return string
     */
    public function get_xml_filename() {
        return "course/blocks/{$this->blockname}_{$this->instanceid}/block.xml";
    }

    public function get_new() {
        return array(
            'showinsubcontexts' => 0,
            'subpagepattern' => NULL,
            'block_positions' => NULL,
        );
    }

    public function get_renamed() {
        return array(
            'name' => 'blockname',
            'pageid' => 'parentcontextid',
            'pagetype' => 'pagetypepattern',
            'position' => 'defaultregion',
            'weight' => 'defaultweight',
        );
    }

    public function get_deprecated() {
        return array(
            'id',
            'visible',
            'roles_overrides',
            'roles_assignments',
        );
    }

    public function mutate_datum($name, $datum) {
        if ($name == 'pagetypepattern') {
            if ($datum == 'course-view') {
                $datum = 'course-view-*';
            }

        } else if ($name == 'defaultregion') {
            if ($datum == 'r') {
                $datum = BLOCK_POS_RIGHT;
            } else {
                $datum = BLOCK_POS_LEFT;
            }
        }
        return $datum;
    }

    public function convert_block($data) {
        $this->instanceid = $data['ID'];
        $this->blockname  = $data['NAME'];

        $contextid = convert_helper::get_contextid($this->instanceid, "block_$this->name", $this->get_convertid());

        // Map PAGEID to context ID
        if ($data['PAGETYPE'] == 'course-view') {
            $data['PAGEID'] = convert_helper::get_contextid($data['PAGEID'], 'course', $this->get_convertid());
        } else if (strpos($data['PAGETYPE'], 'mod-') === 0) {  // EG: mod-quiz-view
            $parts = explode('-', $data['PAGETYPE']);
            $data['PAGEID'] = convert_helper::get_contextid($data['PAGEID'], $parts[1], $this->get_convertid());
        }

        $this->open_xml_writer();
        $this->xmlwriter->begin_tag('block', array('id' => $this->instanceid, 'contextid' => $contextid));
        $this->convert_data($data);
        $this->xmlwriter->end_tag('block');
        $this->close_xml_writer();
    }
}