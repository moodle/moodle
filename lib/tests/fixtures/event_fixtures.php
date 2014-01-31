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

namespace core_tests\event;

/**
 * Fixtures for new event testing.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class unittest_executed extends \core\event\base {
    public $nest = false;

    public static function get_name() {
        return 'xxx';
    }

    public function get_description() {
        return 'yyy';
    }

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public function get_url() {
        return new \moodle_url('/somepath/somefile.php', array('id'=>$this->data['other']['sample']));
    }

    public static function get_legacy_eventname() {
        return 'test_legacy';
    }

    protected function get_legacy_eventdata() {
        return array($this->data['courseid'], $this->data['other']['sample']);
    }

    protected function get_legacy_logdata() {
        return array($this->data['courseid'], 'core_unittest', 'view', 'unittest.php?id='.$this->data['other']['sample']);
    }
}


class unittest_observer {
    public static $info = array();
    public static $event = array();

    public static function reset() {
        self::$info = array();
        self::$event = array();
    }

    public static function observe_one(unittest_executed $event) {
        self::$info[] = 'observe_one-'.$event->courseid;
        self::$event[] = $event;
    }

    public static function external_observer(unittest_executed $event) {
        self::$info[] = 'external_observer-'.$event->courseid;
        self::$event[] = $event;
    }

    public static function broken_observer(unittest_executed $event) {
        self::$info[] = 'broken_observer-'.$event->courseid;
        self::$event[] = $event;
        throw new \Exception('someerror');
    }

    public static function observe_all(\core\event\base $event) {
        if (!($event instanceof unittest_executed)) {
            self::$info[] = 'observe_all-unknown';
            self::$event[] = $event;
            return;
        }
        self::$event[] = $event;
        if (!empty($event->nest)) {
            self::$info[] = 'observe_all-nesting-'.$event->courseid;
            unittest_executed::create(array('courseid'=>3, 'context'=>\context_system::instance(), 'other'=>array('sample'=>666, 'xx'=>666)))->trigger();
        } else {
            self::$info[] = 'observe_all-'.$event->courseid;
        }
    }

    public static function observe_all_alt(\core\event\base $event) {
        self::$info[] = 'observe_all_alt';
        self::$event[] = $event;
    }

    public static function legacy_handler($data) {
        self::$info[] = 'legacy_handler-'.$data[0];
        self::$event[] = $data;
    }
}

class bad_event1 extends \core\event\base {
    protected function init() {
        //$this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
}

class bad_event2 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        //$this->data['edulevel'] = 10;
    }
}

class bad_event2b extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        // Invalid level value.
        $this->data['edulevel'] = -1;
    }
}

class bad_event3 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        unset($this->data['courseid']);
    }
}

class bad_event4 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['xxx'] = 1;
    }
}

class bad_event5 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'x';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
}

class bad_event6 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'xxx_xxx_xx';
    }
}

class bad_event7 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = null;
    }
}

class bad_event8 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'user';
    }
}

class problematic_event1 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
}

class problematic_event2 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }
}

class problematic_event3 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }

    protected function validate_data() {
        if (empty($this->data['other'])) {
            debugging('other is missing');
        }
    }
}

class deprecated_event1 extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['level'] = self::LEVEL_TEACHING; // Tests edulevel hint.
        $this->context = \context_system::instance();
    }
}

class noname_event extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }
}

/**
 * Class content_viewed.
 *
 * Wrapper for testing \core\event\content_viewed .
 */
class content_viewed extends \core\event\content_viewed {
}


/**
 * Class course_module_viewed.
 *
 * Wrapper for testing \core\event\course_module_viewed.
 */
class course_module_viewed extends \core\event\course_module_viewed {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'feedback';
    }
}

/**
 * Class course_module_viewed_noinit.
 *
 * Wrapper for testing \core\event\course_module_viewed.
 */
class course_module_viewed_noinit extends \core\event\course_module_viewed {
}

/**
 * Event to test context used in event functions
 */
class context_used_in_event extends \core\event\base {
    public function get_description() {
        return $this->context->instanceid . " Description";
    }

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->context = \context_system::instance();
    }

    public function get_url() {
        return new \moodle_url('/somepath/somefile.php', array('id' => $this->context->instanceid));
    }

    protected function get_legacy_eventdata() {
        return array($this->data['courseid'], $this->context->instanceid);
    }

    protected function get_legacy_logdata() {
        return array($this->data['courseid'], 'core_unittest', 'view', 'unittest.php?id=' . $this->context->instanceid);
    }
}
