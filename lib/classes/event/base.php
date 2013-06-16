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

namespace core\event;

/**
 * Base event class.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * All other event classes must extend this class.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read string $eventname Name of the event (=== class name with leading \)
 * @property-read string $component Full frankenstyle component name
 * @property-read string $action what happened
 * @property-read string $object what/who was object of the action (usually similar to database table name)
 * @property-read int $objectid optional id of the object
 * @property-read string $crud letter indicating event type
 * @property-read int $level log level
 * @property-read int $contextid
 * @property-read int $contextlevel
 * @property-read int $contextinstanceid
 * @property-read int $userid who did this?
 * @property-read int $courseid
 * @property-read int $relateduserid
 * @property-read mixed $extra array or scalar, can not contain objects
 * @property-read int $realuserid who really did this?
 * @property-read string $origin
 * @property-read int $timecreated
 */
abstract class base {
    /** @var array event data */
    protected $data;

    /** @var \context of this event */
    protected $context;

    /** @var bool indicates if event was already triggered */
    private $triggered;

    /** @var bool indicates if event was restored from storage */
    private $restored;

    /** @var array list of event properties */
    private static $fields = array(
        'eventname', 'component', 'action', 'object', 'objectid', 'crud', 'level', 'contextid',
        'contextlevel', 'contextinstanceid', 'userid', 'courseid', 'relateduserid', 'extra',
        'realuserid', 'origin', 'timecreated');

    /** @var array simple record cache */
    protected $cachedrecords = array();

    /**
     * Private constructor, use create() or restore() methods instead.
     */
    private final function __construct() {
        $this->data = array_fill_keys(self::$fields, null);
    }

    /**
     * Create new event.
     *
     * The optional data keys as:
     * 1/ objectid - the id of the object specified in class name
     * 2/ context - the context of this event
     * 3/ extra - the extra data describing the event, can not contain objects
     * 4/ relateduserid - the id of user which is somehow related to this event
     *
     * @param array $data
     * @return \core\event\base returns instance of new event
     *
     * @throws \coding_exception
     */
    public static final function create(array $data = null) {
        global $PAGE, $USER;

        $data = (array)$data;

        /** @var \core\event\base $event */
        $event = new static();
        $event->triggered = false;
        $event->restored = false;

        $classname = get_class($event);
        $parts = explode('\\', $classname);
        if (count($parts) !== 3 or $parts[1] !== 'event') {
            throw new \coding_exception("Invalid event class name '$classname', it must be defined in component\\event\\ namespace");
        }
        $event->data['eventname'] = '\\'.$classname;
        $event->data['component'] = $parts[0];

        $pos = strrpos($parts[2], '_');
        if ($pos === false) {
            throw new \coding_exception("Invalid event class name '$classname', there must be at least one underscore separating object and action words");
        }
        $event->data['object'] = substr($parts[2], 0, $pos);
        $event->data['action'] = substr($parts[2], $pos+1);

        // Do not let developers to something crazy.
        if (debugging('', DEBUG_DEVELOPER)) {
            $keys = array('eventname', 'component', 'action', 'object', 'realuserid', 'origin', 'timecreated');
            foreach ($keys as $key) {
                if (array_key_exists($key, $data)) {
                    debugging("Data key '$key' is no allowed in event \\core\\event\\base::create() method, it is set automatically.", DEBUG_DEVELOPER);
                }
            }
            $keys = array('crud', 'level');
            foreach ($keys as $key) {
                if (array_key_exists($key, $data)) {
                    debugging("Data key '$key' is no allowed in event \\core\\event\\base::create() method, you need to set it in init method.", DEBUG_DEVELOPER);
                }
            }
        }
        unset($data['eventname']);
        unset($data['component']);
        unset($data['action']);
        unset($data['object']);
        unset($data['crud']);
        unset($data['level']);
        unset($data['realuserid']);
        unset($data['origin']);
        unset($data['timecreated']);

        // Set optional data.
        $event->data['objectid'] = isset($data['objectid']) ? $data['objectid'] : null;
        $event->data['courseid'] = isset($data['courseid']) ? $data['courseid'] : null;
        $event->data['userid'] = isset($data['userid']) ? $data['userid'] : $USER->id;
        $event->data['extra'] = isset($data['extra']) ? $data['extra'] : null;
        $event->data['relateduserid'] = isset($data['relateduserid']) ? $data['relateduserid'] : null;

        $event->context = null;
        if (isset($data['context'])) {
            $event->context = $data['context'];
        } else if (isset($data['contextid'])) {
            $event->context = \context::instance_by_id($data['contextid']);
        } else if ($event->data['courseid']) {
            $event->context = \context_course::instance($event->data['courseid']);
        } else if (isset($PAGE)) {
            $event->context = $PAGE->context;
        }
        if (!$event->context) {
            $event->context = \context_system::instance();
        }
        unset($data['context']);
        $event->data['contextid'] = $event->context->id;
        $event->data['contextlevel'] = $event->context->contextlevel;
        $event->data['contextinstanceid'] = $event->context->instanceid;

        if (!isset($event->data['courseid'])) {
            if ($coursecontext = $event->context->get_course_context(false)) {
                $event->data['courseid'] = $coursecontext->id;
            } else {
                $event->data['courseid'] = 0;
            }
        }

        if (!array_key_exists('relateduserid', $data) and $event->context->contextlevel == CONTEXT_USER) {
            $event->data['relateduserid'] = $event->context->instanceid;
        }

        if (CLI_SCRIPT) {
            $event->data['origin'] = 'cli';
        } else if (AJAX_SCRIPT) {
            $event->data['origin'] = 'ajax:'.getremoteaddr();
        } else {
            $event->data['origin'] = 'web:'.getremoteaddr();
            // TODO: detect web services somehow, for now it is logged separately.
        }

        if (debugging('', DEBUG_DEVELOPER)) {
            foreach (array_keys($data) as $key) {
                if (!in_array($key, self::$fields)) {
                    debugging("Unsupported event data field '$key' detected.");
                }
            }
        }

        $event->init();

        return $event;
    }

    /**
     * Override in subclass.
     *
     * Set all required data properties:
     *  1/ crud
     *  2/ level
     *
     * @return void
     */
    protected abstract function init();

    /**
     * Restore event from existing historic data.
     *
     * @param array $data
     * @return bool|\core\event\base
     */
    public static final function restore(array $data = null) {
        $classname = $data['eventname'];
        $component = $data['component'];
        $action = $data['action'];
        $object = $data['object'];

        // Security: make 100% sure this really is an event class.
        if ($classname !== "\\{$component}\\event\\{$object}_{$action}") {
            return false;
        }

        if (!class_exists($classname)) {
            return false;
        }
        $event = new $classname();
        if (!($event instanceof \core\event\base)) {
            return false;
        }

        $event->triggered = true;
        $event->restored = true;

        foreach (self::$fields as $key) {
            if (array_key_exists($key, $data)) {
                $event->data[$key] = $data[$key];
            } else {
                debugging("Event restore data must contain key $key");
                $event->data[$key] = null;
            }
        }

        return $event;
    }

    /**
     * Returns localised event name.
     *
     * Note: override in child class.
     *
     * @return string
     */
    public function get_name() {
        return $this->data['eventname'];
    }

    /**
     * Returns event context.
     * @return \context
     */
    public function get_context() {
        if (isset($this->context)) {
            return $this->context;
        }
        $this->context = \context::instance_by_id($this->data['contextid'], false);
        return $this->context;
    }

    /**
     * Returns relevant URL, override in subclasses.
     */
    public function get_url() {
        return null;
    }

    /**
     * Return standardised event data as array.
     *
     * Useful especially for logging of events.
     *
     * @return array
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * Does this event replace legacy event?
     *
     * @return null|string legacy event name
     */
    public function get_legacy_eventname() {
        return null;
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return mixed
     */
    public function get_legacy_eventdata() {
        return null;
    }

    /**
     * Doest this event replace add_to_log() statement?
     *
     * @return null|array of parameters to be passed to legacy add_to_log() function.
     */
    public function get_legacy_logdata() {
        return null;
    }

    /**
     * Validate all properties right before triggering the event.
     *
     * This throws coding exceptions for fatal problems and debugging for minor problems.
     *
     * @throws \coding_exception
     */
    protected final function validate_before_trigger() {
        if (empty($this->data['crud'])) {
            throw new \coding_exception('crud must be specified in init() method of each method');
        }
        if (empty($this->data['level'])) {
            throw new \coding_exception('level must be specified in init() method of each method');
        }

        if (debugging('', DEBUG_DEVELOPER)) {
            if (!in_array($this->data['crud'], array('c', 'r', 'u', 'd'), true)) {
                debugging("Invalid event crud value specified.", DEBUG_DEVELOPER);
            }
            // Ideally these should be coding exceptions, but we need to skip these for performance reasons
            // on production servers.
            if (self::$fields !== array_keys($this->data)) {
                debugging('Number of event data fields must not be changed in event classes', DEBUG_DEVELOPER);
            }

            $encoded = json_encode($this->data['extra']);
            if ($encoded === false or $this->data['extra'] !== json_decode($encoded, true)) {
                debugging('Extra event data must be compatible with json encoding', DEBUG_DEVELOPER);
            }
        }
    }

    /**
     * Trigger event.
     */
    public final function trigger() {
        global $CFG;

        if ($this->restored) {
            throw new \coding_exception('Can not trigger restored event');
        }
        if ($this->triggered) {
            throw new \coding_exception('Can not trigger event twice');
        }

        $this->triggered = true;

        $this->validate_before_trigger();

        if (!empty($CFG->loglifetime)) {
            if ($data = $this->get_legacy_logdata()) {
                call_user_func_array('add_to_log', $data);
            }
        }

        \core\event\manager::dispatch($this);

        if ($legacyeventname = $this->get_legacy_eventname()) {
            events_trigger($legacyeventname, $this->get_legacy_eventdata());
        }
    }

    /**
     * Was this event already triggered.
     *
     * Note: restored events are considered to be triggered too.
     *
     * @return bool
     */
    public function is_triggered() {
        return $this->triggered;
    }

    /**
     * Was this evetn restored?
     *
     * @return bool
     */
    public function is_restored() {
        return $this->restored;
    }

    /**
     * Add cached data that will be most probably used in event observers.
     *
     * This is used to improve performance, but it is required for data
     * thar was just deleted.
     *
     * @param string $tablename
     * @param \stdClass $record
     */
    public function add_cached_record($tablename, $record) {
        global $DB;

        // NOTE: this might use some kind of MUC cache,
        //       hopefully we will not run out of memory here...
        if (debugging('', DEBUG_DEVELOPER)) {
            if (!$DB->get_manager()->table_exists($tablename)) {
                debugging("Invalid table name '$tablename' specified, database table does not exist.");
            }
        }
        $this->cachedrecords[$tablename][$record->id] = $record;
    }

    /**
     * Returns cached record or fetches data from database if not cached.
     *
     * @param string $tablename
     * @param int $id
     * @return \stdClass
     */
    public function get_cached_record($tablename, $id) {
        global $DB;

        if (isset($this->cachedrecords[$tablename][$id])) {
            return $this->cachedrecords[$tablename][$id];
        }

        $record = $DB->get_record($tablename, array('id'=>$id));
        $this->cachedrecords[$tablename][$id] = $record;

        return $record;
    }

    /**
     * Magic getter for read only access.
     *
     * Note: we must not allow modification of data from outside,
     *       after trigger() the data MUST NOT CHANGE!!!
     *
     * @param string $name
     * @return mixed
     *
     * @throws \coding_exception
     */
    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new \coding_exception("Accessing non-existent property $name from event class");
    }
}
