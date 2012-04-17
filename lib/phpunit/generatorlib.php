<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * PHPUnit data generator class
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Data generator for unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_data_generator {
    protected $usercounter = 0;
    protected $categorycount = 0;
    protected $coursecount = 0;
    protected $scalecount = 0;

    /** @var array list of plugin generators */
    protected $generators = array();

    /** @var array lis of common last names */
    public $lastnames = array(
        'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'García', 'Rodríguez', 'Wilson',
        'Müller', 'Schmidt', 'Schneider', 'Fischer', 'Meyer', 'Weber', 'Schulz', 'Wagner', 'Becker', 'Hoffmann',
        'Novák', 'Svoboda', 'Novotný', 'Dvořák', 'Černý', 'Procházková', 'Kučerová', 'Veselá', 'Horáková', 'Němcová',
        'Смирнов', 'Иванов', 'Кузнецов', 'Соколов', 'Попов', 'Лебедева', 'Козлова', 'Новикова', 'Морозова', 'Петрова',
        '王', '李', '张', '刘', '陈', '楊', '黃', '趙', '吳', '周',
        '佐藤', '鈴木', '高橋', '田中', '渡辺', '伊藤', '山本', '中村', '小林', '斎藤',
    );

    /** @var array lis of common first names */
    public $firstnames = array(
        'Jacob', 'Ethan', 'Michael', 'Jayden', 'William', 'Isabella', 'Sophia', 'Emma', 'Olivia', 'Ava',
        'Lukas', 'Leon', 'Luca', 'Timm', 'Paul', 'Leonie', 'Leah', 'Lena', 'Hanna', 'Laura',
        'Jakub', 'Jan', 'Tomáš', 'Lukáš', 'Matěj', 'Tereza', 'Eliška', 'Anna', 'Adéla', 'Karolína',
        'Даниил', 'Максим', 'Артем', 'Иван', 'Александр', 'София', 'Анастасия', 'Дарья', 'Мария', 'Полина',
        '伟', '伟', '芳', '伟', '秀英', '秀英', '娜', '秀英', '伟', '敏',
        '翔', '大翔', '拓海', '翔太', '颯太', '陽菜', 'さくら', '美咲', '葵', '美羽',
    );

    public $loremipsum = <<<EOD
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla non arcu lacinia neque faucibus fringilla. Vivamus porttitor turpis ac leo. Integer in sapien. Nullam eget nisl. Aliquam erat volutpat. Cras elementum. Mauris suscipit, ligula sit amet pharetra semper, nibh ante cursus purus, vel sagittis velit mauris vel metus. Integer malesuada. Nullam lectus justo, vulputate eget mollis sed, tempor sed magna. Mauris elementum mauris vitae tortor. Aliquam erat volutpat.
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Pellentesque ipsum. Cras pede libero, dapibus nec, pretium sit amet, tempor quis. Aliquam ante. Proin in tellus sit amet nibh dignissim sagittis. Vivamus porttitor turpis ac leo. Duis bibendum, lectus ut viverra rhoncus, dolor nunc faucibus libero, eget facilisis enim ipsum id lacus. In sem justo, commodo ut, suscipit at, pharetra vitae, orci. Aliquam erat volutpat. Nulla est.
Vivamus luctus egestas leo. Aenean fermentum risus id tortor. Mauris dictum facilisis augue. Aliquam erat volutpat. Aliquam ornare wisi eu metus. Aliquam id dolor. Duis condimentum augue id magna semper rutrum. Donec iaculis gravida nulla. Pellentesque ipsum. Etiam dictum tincidunt diam. Quisque tincidunt scelerisque libero. Etiam egestas wisi a erat.
Integer lacinia. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Mauris tincidunt sem sed arcu. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Aliquam id dolor. Maecenas sollicitudin. Et harum quidem rerum facilis est et expedita distinctio. Mauris suscipit, ligula sit amet pharetra semper, nibh ante cursus purus, vel sagittis velit mauris vel metus. Nullam dapibus fermentum ipsum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Pellentesque sapien. Duis risus. Mauris elementum mauris vitae tortor. Suspendisse nisl. Integer rutrum, orci vestibulum ullamcorper ultricies, lacus quam ultricies odio, vitae placerat pede sem sit amet enim.
In laoreet, magna id viverra tincidunt, sem odio bibendum justo, vel imperdiet sapien wisi sed libero. Proin pede metus, vulputate nec, fermentum fringilla, vehicula vitae, justo. Nullam justo enim, consectetuer nec, ullamcorper ac, vestibulum in, elit. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Maecenas lorem. Etiam posuere lacus quis dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Curabitur ligula sapien, pulvinar a vestibulum quis, facilisis vel sapien. Nam sed tellus id magna elementum tincidunt. Suspendisse nisl. Vivamus luctus egestas leo. Nulla non arcu lacinia neque faucibus fringilla. Etiam dui sem, fermentum vitae, sagittis id, malesuada in, quam. Etiam dictum tincidunt diam. Etiam commodo dui eget wisi. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Proin pede metus, vulputate nec, fermentum fringilla, vehicula vitae, justo. Duis ante orci, molestie vitae vehicula venenatis, tincidunt ac pede. Pellentesque sapien.
EOD;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->usercounter = 0;
        $this->categorycount = 0;
        $this->coursecount = 0;
        $this->scalecount = 0;

        foreach($this->generators as $generator) {
            $generator->reset();
        }
    }

    /**
     * Return generator for given plugin
     * @param string $component
     * @return mixed plugin data generator
     */
    public function get_plugin_generator($component) {
        list($type, $plugin) = normalize_component($component);

        if ($type !== 'mod' and $type !== 'block') {
            throw new coding_exception("Plugin type $type does not support generators yet");
        }

        $dir = get_plugin_directory($type, $plugin);

        if (!isset($this->generators[$type.'_'.$plugin])) {
            $lib = "$dir/tests/generator/lib.php";
            if (!include_once($lib)) {
                throw new coding_exception("Plugin $component does not support data generator, missing tests/generator/lib");
            }
            $classname = $type.'_'.$plugin.'_generator';
            $this->generators[$type.'_'.$plugin] = new $classname($this);
        }

        return $this->generators[$type.'_'.$plugin];
    }

    /**
     * Create a test user
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass user record
     */
    public function create_user($record=null, array $options=null) {
        global $DB, $CFG;

        $this->usercounter++;
        $i = $this->usercounter;

        $record = (array)$record;

        if (!isset($record['auth'])) {
            $record['auth'] = 'manual';
        }

        if (!isset($record['firstname']) and !isset($record['lastname'])) {
            $country = rand(0, 5);
            $firstname = rand(0, 4);
            $lastname = rand(0, 4);
            $female = rand(0, 1);
            $record['firstname'] = $this->firstnames[($country*10) + $firstname + ($female*5)];
            $record['lastname'] = $this->lastnames[($country*10) + $lastname + ($female*5)];

        } else if (!isset($record['firstname'])) {
            $record['firstname'] = 'Firstname'.$i;

        } else if (!isset($record['lastname'])) {
            $record['lastname'] = 'Lastname'.$i;
        }

        if (!isset($record['idnumber'])) {
            $record['idnumber'] = '';
        }

        if (!isset($record['mnethostid'])) {
            $record['mnethostid'] = $CFG->mnet_localhost_id;
        }

        if (!isset($record['username'])) {
            $record['username'] = textlib::strtolower($record['firstname']).textlib::strtolower($record['lastname']);
            while ($DB->record_exists('user', array('username'=>$record['username'], 'mnethostid'=>$record['mnethostid']))) {
                $record['username'] = $record['username'].'_'.$i;
            }
        }

        if (!isset($record['password'])) {
            $record['password'] = 'lala';
        }

        if (!isset($record['email'])) {
            $record['email'] = $record['username'].'@example.com';
        }

        if (!isset($record['confirmed'])) {
            $record['confirmed'] = 1;
        }

        if (!isset($record['lang'])) {
            $record['lang'] = 'en';
        }

        if (!isset($record['maildisplay'])) {
            $record['maildisplay'] = 1;
        }

        if (!isset($record['deleted'])) {
            $record['deleted'] = 0;
        }

        $record['timecreated'] = time();
        $record['timemodified'] = $record['timecreated'];
        $record['lastip'] = '0.0.0.0';

        $record['password'] = hash_internal_user_password($record['password']);

        if ($record['deleted']) {
            $delname = $record['email'].'.'.time();
            while ($DB->record_exists('user', array('username'=>$delname))) {
                $delname++;
            }
            $record['idnumber'] = '';
            $record['email']    = md5($record['username']);
            $record['username'] = $delname;
        }

        $userid = $DB->insert_record('user', $record);

        if (!$record['deleted']) {
            context_user::instance($userid);
        }

        return $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
    }

    /**
     * Create a test course category
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass course category record
     */
    function create_category($record=null, array $options=null) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $this->categorycount++;
        $i = $this->categorycount;

        $record = (array)$record;

        if (!isset($record['name'])) {
            $record['name'] = 'Course category '.$i;
        }

        if (!isset($record['idnumber'])) {
            $record['idnumber'] = '';
        }

        if (!isset($record['description'])) {
            $record['description'] = "Test course category $i\n$this->loremipsum";
        }

        if (!isset($record['descriptionformat'])) {
            $record['description'] = FORMAT_MOODLE;
        }

        if (!isset($record['parent'])) {
            $record['descriptionformat'] = 0;
        }

        if (empty($record['parent'])) {
            $parent = new stdClass();
            $parent->path = '';
            $parent->depth = 0;
        } else {
            $parent = $DB->get_record('course_categories', array('id'=>$record['parent']), '*', MUST_EXIST);
        }
        $record['depth'] = $parent->depth+1;

        $record['sortorder'] = 0;
        $record['timemodified'] = time();
        $record['timecreated'] = $record['timemodified'];

        $catid = $DB->insert_record('course_categories', $record);
        $path = $parent->path . '/' . $catid;
        $DB->set_field('course_categories', 'path', $path, array('id'=>$catid));
        context_coursecat::instance($catid);

        fix_course_sortorder();

        return $DB->get_record('course_categories', array('id'=>$catid), '*', MUST_EXIST);
    }

    /**
     * Create a test course
     * @param array|stdClass $record
     * @param array $options with keys:
     *      'createsections'=>bool precreate all sections
     * @return stdClass course record
     */
    function create_course($record=null, array $options=null) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $this->coursecount++;
        $i = $this->coursecount;

        $record = (array)$record;

        if (!isset($record['fullname'])) {
            $record['fullname'] = 'Test course '.$i;
        }

        if (!isset($record['shortname'])) {
            $record['shortname'] = 'tc_'.$i;
        }

        if (!isset($record['idnumber'])) {
            $record['idnumber'] = '';
        }

        if (!isset($record['format'])) {
            $record['format'] = 'topics';
        }

        if (!isset($record['newsitems'])) {
            $record['newsitems'] = 0;
        }

        if (!isset($record['numsections'])) {
            $record['numsections'] = 5;
        }

        if (!isset($record['description'])) {
            $record['description'] = "Test course $i\n$this->loremipsum";
        }

        if (!isset($record['descriptionformat'])) {
            $record['description'] = FORMAT_MOODLE;
        }

        if (!isset($record['category'])) {
            $record['category'] = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");
        }

        $course = create_course((object)$record);
        context_course::instance($course->id);

        if (!empty($options['createsections'])) {
            for($i=1; $i<$record['numsections']; $i++) {
                self::create_course_section(array('course'=>$course->id, 'section'=>$i));
            }
        }

        return $course;
    }

    /**
     * Create course section if does not exist yet
     * @param mixed $record
     * @param array|null $options
     * @return stdClass
     * @throws coding_exception
     */
    public function create_course_section($record = null, array $options = null) {
        global $DB;

        $record = (array)$record;

        if (empty($record['course'])) {
            throw new coding_exception('course must be present in phpunit_util::create_course_section() $record');
        }

        if (!isset($record['section'])) {
            throw new coding_exception('section must be present in phpunit_util::create_course_section() $record');
        }

        if (!isset($record['name'])) {
            $record['name'] = '';
        }

        if (!isset($record['summary'])) {
            $record['summary'] = '';
        }

        if (!isset($record['summaryformat'])) {
            $record['summaryformat'] = FORMAT_MOODLE;
        }

        if ($section = $DB->get_record('course_sections', array('course'=>$record['course'], 'section'=>$record['section']))) {
            return $section;
        }

        $section = new stdClass();
        $section->course        = $record['course'];
        $section->section       = $record['section'];
        $section->name          = $record['name'];
        $section->summary       = $record['summary'];
        $section->summaryformat = $record['summaryformat'];
        $id = $DB->insert_record('course_sections', $section);

        return $DB->get_record('course_sections', array('id'=>$id));
    }

    /**
     * Create a test block
     * @param string $blockname
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass block instance record
     */
    public function create_block($blockname, $record=null, array $options=null) {
        $generator = $this->get_plugin_generator('block_'.$blockname);
        return $generator->create_instance($record, $options);
    }

    /**
     * Create a test module
     * @param string $modulename
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record
     */
    public function create_module($modulename, $record=null, array $options=null) {
        $generator = $this->get_plugin_generator('mod_'.$modulename);
        return $generator->create_instance($record, $options);
    }

    /**
     * Create a test scale
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass block instance record
     */
    public function create_scale($record=null, array $options=null) {
        global $DB;

        $this->scalecount++;
        $i = $this->scalecount;

        $record = (array)$record;

        if (!isset($record['name'])) {
            $record['name'] = 'Test scale '.$i;
        }

        if (!isset($record['scale'])) {
            $record['scale'] = 'A,B,C,D,F';
        }

        if (!isset($record['courseid'])) {
            $record['courseid'] = 0;
        }

        if (!isset($record['userid'])) {
            $record['userid'] = 0;
        }

        if (!isset($record['description'])) {
            $record['description'] = 'Test scale description '.$i;
        }

        if (!isset($record['descriptionformat'])) {
            $record['descriptionformat'] = FORMAT_MOODLE;
        }

        $record['timemodified'] = time();

        if (isset($record['id'])) {
            $DB->import_record('scale', $record);
            $DB->get_manager()->reset_sequence('scale');
            $id = $record['id'];
        } else {
            $id = $DB->insert_record('scale', $record);
        }

        return $DB->get_record('scale', array('id'=>$id), '*', MUST_EXIST);
    }
}


/**
 * Module generator base class.
 *
 * Extend in mod/xxxx/tests/generator/lib.php as class mod_xxxx_generator.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class phpunit_module_generator {
    /** @var phpunit_data_generator@var  */
    protected $datagenerator;

    /** @var number of created instances */
    protected $instancecount = 0;

    public function __construct(phpunit_data_generator $datagenerator) {
        $this->datagenerator = $datagenerator;
    }

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->instancecount = 0;
    }

    /**
     * Returns module name
     * @return string name of module that this class describes
     * @throws coding_exception if class invalid
     */
    public function get_modulename() {
        $matches = null;
        if (!preg_match('/^mod_([a-z0-9]+)_generator$/', get_class($this), $matches)) {
            throw new coding_exception('Invalid module generator class name: '.get_class($this));
        }

        if (empty($matches[1])) {
            throw new coding_exception('Invalid module generator class name: '.get_class($this));
        }
        return $matches[1];
    }

    /**
     * Create course module and link it to course
     * @param stdClass $instance
     * @param array $options: section, visible
     * @return stdClass $cm instance
     */
    protected function create_course_module(stdClass $instance, array $options) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $modulename = $this->get_modulename();

        $cm = new stdClass();
        $cm->course             = $instance->course;
        $cm->module             = $DB->get_field('modules', 'id', array('name'=>$modulename));
        $cm->instance           = $instance->id;
        $cm->section            = isset($options['section']) ? $options['section'] : 0;
        $cm->idnumber           = isset($options['idnumber']) ? $options['idnumber'] : 0;
        $cm->added              = time();

        $columns = $DB->get_columns('course_modules');
        foreach ($options as $key=>$value) {
            if ($key === 'id' or !isset($columns[$key])) {
                continue;
            }
            if (property_exists($cm, $key)) {
                continue;
            }
            $cm->$key = $value;
        }

        $cm->id = $DB->insert_record('course_modules', $cm);
        $cm->coursemodule = $cm->id;

        add_mod_to_section($cm);

        $cm = get_coursemodule_from_id($modulename, $cm->id, $cm->course, true, MUST_EXIST);

        context_module::instance($cm->id);

        return $cm;
    }

    /**
     * Create a test module
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record
     */
    abstract public function create_instance($record = null, array $options = null);
}


/**
 * Block generator base class.
 *
 * Extend in blocks/xxxx/tests/generator/lib.php as class block_xxxx_generator.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class phpunit_block_generator {
    /** @var phpunit_data_generator@var  */
    protected $datagenerator;

    /** @var number of created instances */
    protected $instancecount = 0;

    public function __construct(phpunit_data_generator $datagenerator) {
        $this->datagenerator = $datagenerator;
    }

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->instancecount = 0;
    }

    /**
     * Returns block name
     * @return string name of block that this class describes
     * @throws coding_exception if class invalid
     */
    public function get_blockname() {
        $matches = null;
        if (!preg_match('/^block_([a-z0-9_]+)_generator$/', get_class($this), $matches)) {
            throw new coding_exception('Invalid block generator class name: '.get_class($this));
        }

        if (empty($matches[1])) {
            throw new coding_exception('Invalid block generator class name: '.get_class($this));
        }
        return $matches[1];
    }

    /**
     * Fill in record defaults
     * @param stdClass $record
     * @return stdClass
     */
    protected function prepare_record(stdClass $record) {
        $record->blockname = $this->get_blockname();
        if (!isset($record->parentcontextid)) {
            $record->parentcontextid = context_system::instance()->id;
        }
        if (!isset($record->showinsubcontexts)) {
            $record->showinsubcontexts = 1;
        }
        if (!isset($record->pagetypepattern)) {
            $record->pagetypepattern = '';
        }
        if (!isset($record->subpagepattern)) {
            $record->subpagepattern = null;
        }
        if (!isset($record->defaultregion)) {
            $record->defaultregion = '';
        }
        if (!isset($record->defaultweight)) {
            $record->defaultweight = '';
        }
        if (!isset($record->configdata)) {
            $record->configdata = null;
        }
        return $record;
    }

    /**
     * Create a test block
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record
     */
    abstract public function create_instance($record = null, array $options = null);
}