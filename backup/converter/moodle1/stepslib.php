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

class moodle1_info_structure_step extends moodle1_structure_step {
    /**
     * @var array
     */
    protected $info = array();

    /**
     * @var array
     */
    protected $details = array();

    /**
     * @var array
     */
    protected $mods = array();

    /**
     * @var string
     */
    protected $currentmod;

    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    protected function define_structure() {
        return array(
            new convert_path_element('info', '/MOODLE_BACKUP/INFO'),
            new convert_path_element('details', '/MOODLE_BACKUP/INFO/DETAILS'),
            new convert_path_element('mod', '/MOODLE_BACKUP/INFO/DETAILS/MOD'),
            new convert_path_element('instance', '/MOODLE_BACKUP/INFO/DETAILS/MOD/INSTANCES/INSTANCE'),
        );
    }

    /**
     * Return the relative path to the XML file that
     * this step writes out to.  Example: course/course.xml
     *
     * @return string
     */
    public function get_xml_filename() {
        return 'moodle_backup.xml';
    }

    public function get_new() {
        global $DB;

        $course = $DB->get_record('backup_ids_temp', array('itemname' => 'course'), '*', MUST_EXIST);
        $info   = unserialize(base64_decode($course->info));

        return array(
            'mnet_remoteusers' => 0,
            'original_site_identifier_hash' => '?', // @todo What is this?
            'original_course_id' => $course->itemid,
            'original_course_fullname' => $info['fullname'],
            'original_course_shortname' => $info['shortname'],
            'original_course_startdate' => $info['startdate'],
            'original_course_contextid' => $info['contextid'],
            'original_system_contextid' => convert_helper::get_contextid(0, 'system', $this->get_convertid()),
        );
    }

    public function get_renamed() {
        return parent::get_renamed();
    }

    public function get_deprecated() {
        return parent::get_deprecated();
    }

    public function convert_info($data) {
        // print_object($data); // DEBUG
        $this->info = $data;
    }

    public function convert_details($data) {
        // print_object($data); // DEBUG
        $this->details = $data;
    }

    public function convert_mod($data) {
        // print_object($data); // DEBUG
        $this->currentmod = $data['NAME'];
        $this->mods[$this->currentmod] = array();
    }

    public function convert_instance($data) {
        // print_object($data); // DEBUG
        $this->mods[$this->currentmod][$data['ID']] = $data;
    }

    public function execute_after_convert() {
        global $DB;

        $rootsettings = array(
            'filename'         => $this->info['NAME'],
            'users'            => 0, // @todo Add support for users
            'anonymize'        => 0, // @todo Correct?
            'role_assignments' => 0, // @todo Add support for users
            'user_files'       => 0, // @todo Add support for users
            'activities'       => 1,
            'blocks'           => 1,
            'filters'          => 0, // @todo Add support for filters
            'comments'         => 0,
            'userscompletion'  => 0,
            'logs'             => 0,
            'grade_histories'  => 0, // @todo Add support for users
        );

        $settings = array();
        foreach ($rootsettings as $name => $value) {
            $settings[] = array(
                'level' => 'root',
                'name' => $name,
                'value' => $value,
            );
        }

        $this->open_xml_writer();
        $this->xmlwriter->begin_tag('moodle_backup');
        $this->xmlwriter->begin_tag('information');

        $this->convert_data($this->info);

        $this->xmlwriter->begin_tag('details');
        $this->xmlwriter->begin_tag('detail', array('backup_id' => $this->get_convertid()));
        $this->xmlwriter->full_tag('type', 'course');
        $this->xmlwriter->full_tag('format', 'moodle2');
        $this->xmlwriter->full_tag('interactive', 0); // @todo Correct?
        $this->xmlwriter->full_tag('mode', backup::MODE_GENERAL);
        $this->xmlwriter->full_tag('execution', 1);  // @todo Correct?
        $this->xmlwriter->full_tag('executiontime', 0);
        $this->xmlwriter->end_tag('detail');
        $this->xmlwriter->end_tag('details');

        $this->xmlwriter->begin_tag('contents');
        $this->xmlwriter->begin_tag('activities');

        foreach ($this->mods as $type => $instances) {
            $records = $DB->get_records('backup_ids_temp', array('backupid' => $this->get_convertid(), 'itemname' => $type));

            foreach ($records as $record) {
                $info = unserialize(base64_decode($record->info));
                $key  = "{$record->itemname}_{$record->parentitemid}";

                if (array_key_exists($record->itemid, $instances)) {
                    $title    = $instances[$record->itemid]['NAME'];
                    $included = (int) $instances[$record->itemid]['INCLUDED'];
                } else {
                    $title    = 'UNKNOWN';
                    $included = 1;
                }
                $settings[] = array(
                    'level'    => 'activity',
                    'activity' => $key,
                    'name'     => "{$key}_included",
                    'value'    => $included,
                );
                $this->xmlwriter->begin_tag('activity');
                $this->xmlwriter->full_tag('moduleid', $record->parentitemid);
                $this->xmlwriter->full_tag('sectionid', $info['sectionid']);
                $this->xmlwriter->full_tag('modulename', $record->itemname);
                $this->xmlwriter->full_tag('title', $title);
                $this->xmlwriter->full_tag('directory', "activities/$key");
                $this->xmlwriter->end_tag('activity');
            }
        }
        $this->xmlwriter->end_tag('activities');
        $this->xmlwriter->begin_tag('sections');

        $records = $DB->get_records('backup_ids_temp', array('backupid' => $this->get_convertid(), 'itemname' => 'section'));
        foreach ($records as $record) {
            $info = unserialize(base64_decode($record->info));

            $settings[] = array(
                'level'   => 'section',
                'section' => "section_$record->itemid",
                'name'    => "section_{$record->itemid}_userinfo",
                'value'   => 0, // @todo Add support for user info
            );

            $this->xmlwriter->begin_tag('section');
            $this->xmlwriter->full_tag('sectionid', $record->itemid);
            $this->xmlwriter->full_tag('title', $info['title']);
            $this->xmlwriter->full_tag('directory', $info['directory']);
            $this->xmlwriter->end_tag('section');
        }
        $this->xmlwriter->end_tag('sections');

        $course = $DB->get_record('backup_ids_temp', array('itemname' => 'course'), '*', MUST_EXIST);
        $info   = unserialize(base64_decode($course->info));

        $this->xmlwriter->begin_tag('course');
        $this->xmlwriter->full_tag('courseid', $course->itemid);
        $this->xmlwriter->full_tag('title', $info['fullname']);
        $this->xmlwriter->full_tag('directory', 'course');
        $this->xmlwriter->end_tag('course');

        $this->xmlwriter->end_tag('contents');

        $this->xmlwriter->begin_tag('settings');
        foreach ($settings as $setting) {
            $this->xmlwriter->begin_tag('setting');
            foreach ($setting as $name => $value) {
                $this->xmlwriter->full_tag($name, $value);
            }
            $this->xmlwriter->end_tag('setting');
        }
        $this->xmlwriter->end_tag('settings');
        $this->xmlwriter->end_tag('information');
        $this->xmlwriter->end_tag('moodle_backup');
        $this->close_xml_writer();
    }
}

class moodle1_course_structure_step extends moodle1_structure_step {
    /**
     * @var array
     */
    protected $course = array();

    /**
     * @var array
     */
    protected $category = array();

    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     */
    protected function define_structure() {
        return array(
            new convert_path_element('course', '/MOODLE_BACKUP/COURSE/HEADER'),
            new convert_path_element('category', '/MOODLE_BACKUP/COURSE/HEADER/CATEGORY'),
        );
    }

    /**
     * Return the relative path to the XML file that
     * this step writes out to.  Example: course/course.xml
     *
     * @return string
     */
    public function get_xml_filename() {
        return 'course/course.xml';
    }

    public function get_new() {
        return array(
            'summaryformat' => 1,
            'legacyfiles' => 1, // @todo I think this is correct
            'requested' => 0, // @todo Not really new, but maybe never backed up?
            'restrictmodules' => 0,
            'enablecompletion' => 0,
            'completionstartonenrol' => 0,
            'completionnotify' => 0,
        );
    }

    public function get_deprecated() {
        return array(
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
        $this->course = array_merge($this->course, $data);
    }

    public function convert_category($data) {
        // print_object($data);  // DEBUG
        $this->category = $data;
    }

    public function execute_after_convert() {
        global $DB;

        $this->open_xml_writer();

        $contextid = convert_helper::get_contextid($this->course['ID'], 'course');

        $this->xmlwriter->begin_tag('course', array(
            'id' => $this->course['ID'],
            'contextid' => $contextid,
        ));

        $this->convert_data($this->course);

        $DB->insert_record('backup_ids_temp', (object) array(
            'backupid' => $this->get_convertid(),
            'itemname' => 'course',
            'itemid'   => $this->course['ID'],
            'info'     => base64_encode(serialize(array(
                'fullname'  => $this->course['FULLNAME'],
                'shortname' => $this->course['SHORTNAME'],
                'startdate' => $this->course['STARTDATE'],
                'contextid' => $contextid,
            ))),
        ));

        $this->xmlwriter->begin_tag('category', array('id' => $this->category['ID']));
        $this->xmlwriter->full_tag('name', $this->category['NAME']);
        $this->xmlwriter->full_tag('description', NULL);
        $this->xmlwriter->end_tag('category');
        $this->xmlwriter->full_tag('tags', NULL);
        $this->xmlwriter->full_tag('allowed_modules', NULL);
        $this->xmlwriter->end_tag('course');
        $this->close_xml_writer();
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
        global $DB;

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

        $DB->insert_record('backup_ids_temp', (object) array(
            'backupid' => $this->get_convertid(),
            'itemname' => 'section',
            'itemid' => $this->data['ID'],
            'info' => base64_encode(serialize(array(
                'title' => $this->data['NUMBER'], // @todo Correct?
                'directory' => pathinfo($this->get_xml_filename(), PATHINFO_DIRNAME),
            ))),
        ));

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

        $info = array();
        $info['sectionid']     = $this->id;
        $info['sectionnumber'] = $this->data['NUMBER'];
        foreach ($data as $name => $value) {
            $info[strtolower($name)] = $value;
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
            $this->convert_data(unserialize(base64_decode($record->info)));
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