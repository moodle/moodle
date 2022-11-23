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

defined('MOODLE_INTERNAL') || die();

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
 * @since      Moodle 2.6
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read string $eventname Name of the event (=== class name with leading \)
 * @property-read string $component Full frankenstyle component name
 * @property-read string $action what happened
 * @property-read string $target what/who was target of the action
 * @property-read string $objecttable name of database table where is object record stored
 * @property-read int $objectid optional id of the object
 * @property-read string $crud letter indicating event type
 * @property-read int $edulevel log level (one of the constants LEVEL_)
 * @property-read int $contextid
 * @property-read int $contextlevel
 * @property-read int $contextinstanceid
 * @property-read int $userid who did this?
 * @property-read int $courseid the courseid of the event context, 0 for contexts above course
 * @property-read int $relateduserid
 * @property-read int $anonymous 1 means event should not be visible in reports, 0 means normal event,
 *                    create() argument may be also true/false.
 * @property-read mixed $other array or scalar, can not contain objects
 * @property-read int $timecreated
 */
abstract class base implements \IteratorAggregate {

    /**
     * Other level.
     */
    const LEVEL_OTHER = 0;

    /**
     * Teaching level.
     *
     * Any event that is performed by someone (typically a teacher) and has a teaching value,
     * anything that is affecting the learning experience/environment of the students.
     */
    const LEVEL_TEACHING = 1;

    /**
     * Participating level.
     *
     * Any event that is performed by a user, and is related (or could be related) to his learning experience.
     */
    const LEVEL_PARTICIPATING = 2;

    /**
     * The value used when an id can not be mapped during a restore.
     */
    const NOT_MAPPED = -31337;

    /**
     * The value used when an id can not be found during a restore.
     */
    const NOT_FOUND = -31338;

    /**
     * User id to use when the user is not logged in.
     */
    const USER_NOTLOGGEDIN = 0;

    /**
     * User id to use when actor is not an actual user but system, cli or cron.
     */
    const USER_OTHER = -1;

    /** @var array event data */
    protected $data;

    /** @var array the format is standardised by logging API */
    protected $logextra;

    /** @var \context of this event */
    protected $context;

    /**
     * @var bool indicates if event was already triggered,
     *           this prevents second attempt to trigger event.
     */
    private $triggered;

    /**
     * @var bool indicates if event was already dispatched,
     *           this prevents direct calling of manager::dispatch($event).
     */
    private $dispatched;

    /**
     * @var bool indicates if event was restored from storage,
     *           this prevents triggering of restored events.
     */
    private $restored;

    /** @var array list of event properties */
    private static $fields = array(
        'eventname', 'component', 'action', 'target', 'objecttable', 'objectid', 'crud', 'edulevel', 'contextid',
        'contextlevel', 'contextinstanceid', 'userid', 'courseid', 'relateduserid', 'anonymous', 'other',
        'timecreated');

    /** @var array simple record cache */
    private $recordsnapshots = array();

    /**
     * Private constructor, use create() or restore() methods instead.
     */
    final private function __construct() {
        $this->data = array_fill_keys(self::$fields, null);

        // Define some basic details.
        $classname = get_called_class();
        $parts = explode('\\', $classname);
        if (count($parts) !== 3 or $parts[1] !== 'event') {
            throw new \coding_exception("Invalid event class name '$classname', it must be defined in component\\event\\
                    namespace");
        }
        $this->data['eventname'] = '\\'.$classname;
        $this->data['component'] = $parts[0];

        $pos = strrpos($parts[2], '_');
        if ($pos === false) {
            throw new \coding_exception("Invalid event class name '$classname', there must be at least one underscore separating
                    object and action words");
        }
        $this->data['target'] = substr($parts[2], 0, $pos);
        $this->data['action'] = substr($parts[2], $pos + 1);
    }

    /**
     * Create new event.
     *
     * The optional data keys as:
     * 1/ objectid - the id of the object specified in class name
     * 2/ context - the context of this event
     * 3/ other - the other data describing the event, can not contain objects
     * 4/ relateduserid - the id of user which is somehow related to this event
     *
     * @param array $data
     * @return \core\event\base returns instance of new event
     *
     * @throws \coding_exception
     */
    public static final function create(array $data = null) {
        global $USER, $CFG;

        $data = (array)$data;

        /** @var \core\event\base $event */
        $event = new static();
        $event->triggered = false;
        $event->restored = false;
        $event->dispatched = false;

        // By default all events are visible in logs.
        $event->data['anonymous'] = 0;

        // Set static event data specific for child class.
        $event->init();

        if (isset($event->data['level'])) {
            if (!isset($event->data['edulevel'])) {
                debugging('level property is deprecated, use edulevel property instead', DEBUG_DEVELOPER);
                $event->data['edulevel'] = $event->data['level'];
            }
            unset($event->data['level']);
        }

        // Set automatic data.
        $event->data['timecreated'] = time();

        // Set optional data or use defaults.
        $event->data['objectid'] = isset($data['objectid']) ? $data['objectid'] : null;
        $event->data['courseid'] = isset($data['courseid']) ? $data['courseid'] : null;
        $event->data['userid'] = isset($data['userid']) ? $data['userid'] : $USER->id;
        $event->data['other'] = isset($data['other']) ? $data['other'] : null;
        $event->data['relateduserid'] = isset($data['relateduserid']) ? $data['relateduserid'] : null;
        if (isset($data['anonymous'])) {
            $event->data['anonymous'] = $data['anonymous'];
        }
        $event->data['anonymous'] = (int)(bool)$event->data['anonymous'];

        if (isset($event->context)) {
            if (isset($data['context'])) {
                debugging('Context was already set in init() method, ignoring context parameter', DEBUG_DEVELOPER);
            }

        } else if (!empty($data['context'])) {
            $event->context = $data['context'];

        } else if (!empty($data['contextid'])) {
            $event->context = \context::instance_by_id($data['contextid'], MUST_EXIST);

        } else {
            throw new \coding_exception('context (or contextid) is a required event property, system context may be hardcoded in init() method.');
        }

        $event->data['contextid'] = $event->context->id;
        $event->data['contextlevel'] = $event->context->contextlevel;
        $event->data['contextinstanceid'] = $event->context->instanceid;

        if (!isset($event->data['courseid'])) {
            if ($coursecontext = $event->context->get_course_context(false)) {
                $event->data['courseid'] = $coursecontext->instanceid;
            } else {
                $event->data['courseid'] = 0;
            }
        }

        if (!array_key_exists('relateduserid', $data) and $event->context->contextlevel == CONTEXT_USER) {
            $event->data['relateduserid'] = $event->context->instanceid;
        }

        // Warn developers if they do something wrong.
        if ($CFG->debugdeveloper) {
            static $automatickeys = array('eventname', 'component', 'action', 'target', 'contextlevel', 'contextinstanceid', 'timecreated');
            static $initkeys = array('crud', 'level', 'objecttable', 'edulevel');

            foreach ($data as $key => $ignored) {
                if ($key === 'context') {
                    continue;

                } else if (in_array($key, $automatickeys)) {
                    debugging("Data key '$key' is not allowed in \\core\\event\\base::create() method, it is set automatically", DEBUG_DEVELOPER);

                } else if (in_array($key, $initkeys)) {
                    debugging("Data key '$key' is not allowed in \\core\\event\\base::create() method, you need to set it in init() method", DEBUG_DEVELOPER);

                } else if (!in_array($key, self::$fields)) {
                    debugging("Data key '$key' does not exist in \\core\\event\\base");
                }
            }
            $expectedcourseid = 0;
            if ($coursecontext = $event->context->get_course_context(false)) {
                $expectedcourseid = $coursecontext->instanceid;
            }
            if ($expectedcourseid != $event->data['courseid']) {
                debugging("Inconsistent courseid - context combination detected.", DEBUG_DEVELOPER);
            }

            if (method_exists($event, 'get_legacy_logdata') ||
                method_exists($event, 'set_legacy_logdata') ||
                method_exists($event, 'get_legacy_eventname') ||
                method_exists($event, 'get_legacy_eventdata')
            ) {
                debugging("Invalid event functions defined in " . $event->data['eventname'], DEBUG_DEVELOPER);
            }

        }

        // Let developers validate their custom data (such as $this->data['other'], contextlevel, etc.).
        $event->validate_data();

        return $event;
    }

    /**
     * Override in subclass.
     *
     * Set all required data properties:
     *  1/ crud - letter [crud]
     *  2/ edulevel - using a constant self::LEVEL_*.
     *  3/ objecttable - name of database table if objectid specified
     *
     * Optionally it can set:
     * a/ fixed system context
     *
     * @return void
     */
    protected abstract function init();

    /**
     * Let developers validate their custom data (such as $this->data['other'], contextlevel, etc.).
     *
     * Throw \coding_exception or debugging() notice in case of any problems.
     */
    protected function validate_data() {
        // Override if you want to validate event properties when
        // creating new events.
    }

    /**
     * Returns localised general event name.
     *
     * Override in subclass, we can not make it static and abstract at the same time.
     *
     * @return string
     */
    public static function get_name() {
        // Override in subclass with real lang string.
        $parts = explode('\\', get_called_class());
        if (count($parts) !== 3) {
            return get_string('unknownevent', 'error');
        }
        return $parts[0].': '.str_replace('_', ' ', $parts[2]);
    }

    /**
     * Returns the event name complete with metadata information.
     *
     * This includes information about whether the event has been deprecated so should not be used in all situations -
     * for example within reports themselves.
     *
     * If overriding this function, please ensure that you call the parent version too.
     *
     * @return string
     */
    public static function get_name_with_info() {
        $return = static::get_name();

        if (static::is_deprecated()) {
            $return = get_string('deprecatedeventname', 'core', $return);
        }

        return $return;
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return null;
    }

    /**
     * This method was originally intended for granular
     * access control on the event level, unfortunately
     * the proper implementation would be too expensive
     * in many cases.
     *
     * @deprecated since 2.7
     *
     * @param int|\stdClass $user_or_id ID of the user.
     * @return bool True if the user can view the event, false otherwise.
     */
    public function can_view($user_or_id = null) {
        debugging('can_view() method is deprecated, use anonymous flag instead if necessary.', DEBUG_DEVELOPER);
        return is_siteadmin($user_or_id);
    }

    /**
     * Restore event from existing historic data.
     *
     * @param array $data
     * @param array $logextra the format is standardised by logging API
     * @return bool|\core\event\base
     */
    public static final function restore(array $data, array $logextra) {
        $classname = $data['eventname'];
        $component = $data['component'];
        $action = $data['action'];
        $target = $data['target'];

        // Security: make 100% sure this really is an event class.
        if ($classname !== "\\{$component}\\event\\{$target}_{$action}") {
            return false;
        }

        if (!class_exists($classname)) {
            return self::restore_unknown($data, $logextra);
        }
        $event = new $classname();
        if (!($event instanceof \core\event\base)) {
            return false;
        }

        $event->init(); // Init method of events could be setting custom properties.
        $event->restored = true;
        $event->triggered = true;
        $event->dispatched = true;
        $event->logextra = $logextra;

        foreach (self::$fields as $key) {
            if (!array_key_exists($key, $data)) {
                debugging("Event restore data must contain key $key");
                $data[$key] = null;
            }
        }
        if (count($data) != count(self::$fields)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, self::$fields)) {
                    debugging("Event restore data cannot contain key $key");
                    unset($data[$key]);
                }
            }
        }
        $event->data = $data;

        return $event;
    }

    /**
     * Restore unknown event.
     *
     * @param array $data
     * @param array $logextra
     * @return unknown_logged
     */
    protected static final function restore_unknown(array $data, array $logextra) {
        $classname = '\core\event\unknown_logged';

        /** @var unknown_logged $event */
        $event = new $classname();
        $event->restored = true;
        $event->triggered = true;
        $event->dispatched = true;
        $event->data = $data;
        $event->logextra = $logextra;

        return $event;
    }

    /**
     * Create fake event from legacy log data.
     *
     * @param \stdClass $legacy
     * @return base
     */
    public static final function restore_legacy($legacy) {
        $classname = get_called_class();
        /** @var base $event */
        $event = new $classname();
        $event->restored = true;
        $event->triggered = true;
        $event->dispatched = true;

        $context = false;
        $component = 'legacy';
        if ($legacy->cmid) {
            $context = \context_module::instance($legacy->cmid, IGNORE_MISSING);
            $component = 'mod_'.$legacy->module;
        } else if ($legacy->course) {
            $context = \context_course::instance($legacy->course, IGNORE_MISSING);
        }
        if (!$context) {
            $context = \context_system::instance();
        }

        $event->data = array();

        $event->data['eventname'] = $legacy->module.'_'.$legacy->action;
        $event->data['component'] = $component;
        $event->data['action'] = $legacy->action;
        $event->data['target'] = null;
        $event->data['objecttable'] = null;
        $event->data['objectid'] = null;
        if (strpos($legacy->action, 'view') !== false) {
            $event->data['crud'] = 'r';
        } else if (strpos($legacy->action, 'print') !== false) {
            $event->data['crud'] = 'r';
        } else if (strpos($legacy->action, 'update') !== false) {
            $event->data['crud'] = 'u';
        } else if (strpos($legacy->action, 'hide') !== false) {
            $event->data['crud'] = 'u';
        } else if (strpos($legacy->action, 'move') !== false) {
            $event->data['crud'] = 'u';
        } else if (strpos($legacy->action, 'write') !== false) {
            $event->data['crud'] = 'u';
        } else if (strpos($legacy->action, 'tag') !== false) {
            $event->data['crud'] = 'u';
        } else if (strpos($legacy->action, 'remove') !== false) {
            $event->data['crud'] = 'u';
        } else if (strpos($legacy->action, 'delete') !== false) {
            $event->data['crud'] = 'p';
        } else if (strpos($legacy->action, 'create') !== false) {
            $event->data['crud'] = 'c';
        } else if (strpos($legacy->action, 'post') !== false) {
            $event->data['crud'] = 'c';
        } else if (strpos($legacy->action, 'add') !== false) {
            $event->data['crud'] = 'c';
        } else {
            // End of guessing...
            $event->data['crud'] = 'r';
        }
        $event->data['edulevel'] = $event::LEVEL_OTHER;
        $event->data['contextid'] = $context->id;
        $event->data['contextlevel'] = $context->contextlevel;
        $event->data['contextinstanceid'] = $context->instanceid;
        $event->data['userid'] = ($legacy->userid ? $legacy->userid : null);
        $event->data['courseid'] = ($legacy->course ? $legacy->course : null);
        $event->data['relateduserid'] = ($legacy->userid ? $legacy->userid : null);
        $event->data['timecreated'] = $legacy->time;

        $event->logextra = array();
        if ($legacy->ip) {
            $event->logextra['origin'] = 'web';
            $event->logextra['ip'] = $legacy->ip;
        } else {
            $event->logextra['origin'] = 'cli';
            $event->logextra['ip'] = null;
        }
        $event->logextra['realuserid'] = null;

        $event->data['other'] = (array)$legacy;

        return $event;
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the objectid to it's new value in the new course.
     *
     * Does nothing in the base class except display a debugging message warning
     * the user that the event does not contain the required functionality to
     * map this information. For events that do not store an objectid this won't
     * be called, so no debugging message will be displayed.
     *
     * Example of usage:
     *
     * return array('db' => 'assign_submissions', 'restore' => 'submission');
     *
     * If the objectid can not be mapped during restore set the value to \core\event\base::NOT_MAPPED, example -
     *
     * return array('db' => 'some_table', 'restore' => \core\event\base::NOT_MAPPED);
     *
     * Note - it isn't necessary to specify the 'db' and 'restore' values in this case, so you can also use -
     *
     * return \core\event\base::NOT_MAPPED;
     *
     * The 'db' key refers to the database table and the 'restore' key refers to
     * the name of the restore element the objectid is associated with. In many
     * cases these will be the same.
     *
     * @return string the name of the restore mapping the objectid links to
     */
    public static function get_objectid_mapping() {
        debugging('In order to restore course logs accurately the event "' . get_called_class() . '" must define the
            function get_objectid_mapping().', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the information in 'other' to it's new value in the new course.
     *
     * Does nothing in the base class except display a debugging message warning
     * the user that the event does not contain the required functionality to
     * map this information. For events that do not store any other information this
     * won't be called, so no debugging message will be displayed.
     *
     * Example of usage:
     *
     * $othermapped = array();
     * $othermapped['discussionid'] = array('db' => 'forum_discussions', 'restore' => 'forum_discussion');
     * $othermapped['forumid'] = array('db' => 'forum', 'restore' => 'forum');
     * return $othermapped;
     *
     * If an id can not be mapped during restore we set it to \core\event\base::NOT_MAPPED, example -
     *
     * $othermapped = array();
     * $othermapped['someid'] = array('db' => 'some_table', 'restore' => \core\event\base::NOT_MAPPED);
     * return $othermapped;
     *
     * Note - it isn't necessary to specify the 'db' and 'restore' values in this case, so you can also use -
     *
     * $othermapped = array();
     * $othermapped['someid'] = \core\event\base::NOT_MAPPED;
     * return $othermapped;
     *
     * The 'db' key refers to the database table and the 'restore' key refers to
     * the name of the restore element the other value is associated with. In many
     * cases these will be the same.
     *
     * @return array an array of other values and their corresponding mapping
     */
    public static function get_other_mapping() {
        debugging('In order to restore course logs accurately the event "' . get_called_class() . '" must define the
            function get_other_mapping().', DEBUG_DEVELOPER);
    }

    /**
     * Get static information about an event.
     * This is used in reports and is not for general use.
     *
     * @return array Static information about the event.
     */
    public static final function get_static_info() {
        /** Var \core\event\base $event. */
        $event = new static();
        // Set static event data specific for child class.
        $event->init();
        return array(
            'eventname' => $event->data['eventname'],
            'component' => $event->data['component'],
            'target' => $event->data['target'],
            'action' => $event->data['action'],
            'crud' => $event->data['crud'],
            'edulevel' => $event->data['edulevel'],
            'objecttable' => $event->data['objecttable'],
        );
    }

    /**
     * Get an explanation of what the class does.
     * By default returns the phpdocs from the child event class. Ideally this should
     * be overridden to return a translatable get_string style markdown.
     * e.g. return new lang_string('eventyourspecialevent', 'plugin_type');
     *
     * @return string An explanation of the event formatted in markdown style.
     */
    public static function get_explanation() {
        $ref = new \ReflectionClass(get_called_class());
        $docblock = $ref->getDocComment();

        // Check that there is something to work on.
        if (empty($docblock)) {
            return null;
        }

        $docblocklines = explode("\n", $docblock);
        // Remove the bulk of the comment characters.
        $pattern = "/(^\s*\/\*\*|^\s+\*\s|^\s+\*)/";
        $cleanline = array();
        foreach ($docblocklines as $line) {
            $templine = preg_replace($pattern, '', $line);
            // If there is nothing on the line then don't add it to the array.
            if (!empty($templine)) {
                $cleanline[] = rtrim($templine);
            }
            // If we get to a line starting with an @ symbol then we don't want the rest.
            if (preg_match("/^@|\//", $templine)) {
                // Get rid of the last entry (it contains an @ symbol).
                array_pop($cleanline);
                // Break out of this foreach loop.
                break;
            }
        }
        // Add a line break to the sanitised lines.
        $explanation = implode("\n", $cleanline);

        return $explanation;
    }

    /**
     * Returns event context.
     * @return \context
     */
    public function get_context() {
        if (isset($this->context)) {
            return $this->context;
        }
        $this->context = \context::instance_by_id($this->data['contextid'], IGNORE_MISSING);
        return $this->context;
    }

    /**
     * Returns relevant URL, override in subclasses.
     * @return \moodle_url
     */
    public function get_url() {
        return null;
    }

    /**
     * Return standardised event data as array.
     *
     * @return array All elements are scalars except the 'other' field which is array.
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * Return auxiliary data that was stored in logs.
     *
     * List of standard properties:
     *  - origin: IP number, cli,cron
     *  - realuserid: id of the user when logged-in-as
     *
     * @return array the format is standardised by logging API
     */
    public function get_logextra() {
        return $this->logextra;
    }

    /**
     * Validate all properties right before triggering the event.
     *
     * This throws coding exceptions for fatal problems and debugging for minor problems.
     *
     * @throws \coding_exception
     */
    protected function validate_before_trigger() {
        global $DB, $CFG;

        if (empty($this->data['crud'])) {
            throw new \coding_exception('crud must be specified in init() method of each method');
        }
        if (!isset($this->data['edulevel'])) {
            throw new \coding_exception('edulevel must be specified in init() method of each method');
        }
        if (!empty($this->data['objectid']) and empty($this->data['objecttable'])) {
            throw new \coding_exception('objecttable must be specified in init() method if objectid present');
        }

        if ($CFG->debugdeveloper) {
            // Ideally these should be coding exceptions, but we need to skip these for performance reasons
            // on production servers.

            if (!in_array($this->data['crud'], array('c', 'r', 'u', 'd'), true)) {
                debugging("Invalid event crud value specified.", DEBUG_DEVELOPER);
            }
            if (!in_array($this->data['edulevel'], array(self::LEVEL_OTHER, self::LEVEL_TEACHING, self::LEVEL_PARTICIPATING))) {
                // Bitwise combination of levels is not allowed at this stage.
                debugging('Event property edulevel must a constant value, see event_base::LEVEL_*', DEBUG_DEVELOPER);
            }
            if (self::$fields !== array_keys($this->data)) {
                debugging('Number of event data fields must not be changed in event classes', DEBUG_DEVELOPER);
            }
            $encoded = json_encode($this->data['other']);
            // The comparison here is not set to strict. We just need to check if the data is compatible with the JSON encoding
            // or not and we don't need to worry about how the data is encoded. Because in some cases, the data can contain
            // objects, and objects can be converted to a different format during encoding and decoding.
            if ($encoded === false) {
                debugging('other event data must be compatible with json encoding', DEBUG_DEVELOPER);
            }
            if ($this->data['userid'] and !is_number($this->data['userid'])) {
                debugging('Event property userid must be a number', DEBUG_DEVELOPER);
            }
            if ($this->data['courseid'] and !is_number($this->data['courseid'])) {
                debugging('Event property courseid must be a number', DEBUG_DEVELOPER);
            }
            if ($this->data['objectid'] and !is_number($this->data['objectid'])) {
                debugging('Event property objectid must be a number', DEBUG_DEVELOPER);
            }
            if ($this->data['relateduserid'] and !is_number($this->data['relateduserid'])) {
                debugging('Event property relateduserid must be a number', DEBUG_DEVELOPER);
            }
            if ($this->data['objecttable']) {
                if (!$DB->get_manager()->table_exists($this->data['objecttable'])) {
                    debugging('Unknown table specified in objecttable field', DEBUG_DEVELOPER);
                }
                if (!isset($this->data['objectid'])) {
                    debugging('Event property objectid must be set when objecttable is defined', DEBUG_DEVELOPER);
                }
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
        if ($this->triggered or $this->dispatched) {
            throw new \coding_exception('Can not trigger event twice');
        }

        $this->validate_before_trigger();

        $this->triggered = true;

        if (PHPUNIT_TEST and \phpunit_util::is_redirecting_events()) {
            $this->dispatched = true;
            \phpunit_util::event_triggered($this);
            return;
        }

        \core\event\manager::dispatch($this);

        $this->dispatched = true;
    }

    /**
     * Was this event already triggered?
     *
     * @return bool
     */
    public final function is_triggered() {
        return $this->triggered;
    }

    /**
     * Used from event manager to prevent direct access.
     *
     * @return bool
     */
    public final function is_dispatched() {
        return $this->dispatched;
    }

    /**
     * Was this event restored?
     *
     * @return bool
     */
    public final function is_restored() {
        return $this->restored;
    }

    /**
     * Add cached data that will be most probably used in event observers.
     *
     * This is used to improve performance, but it is required for data
     * that was just deleted.
     *
     * @param string $tablename
     * @param \stdClass $record
     *
     * @throws \coding_exception if used after ::trigger()
     */
    public final function add_record_snapshot($tablename, $record) {
        global $DB, $CFG;

        if ($this->triggered) {
            throw new \coding_exception('It is not possible to add snapshots after triggering of events');
        }

        // Special case for course module, allow instance of cm_info to be passed instead of stdClass.
        if ($tablename === 'course_modules' && $record instanceof \cm_info) {
            $record = $record->get_course_module_record();
        }

        // NOTE: this might use some kind of MUC cache,
        //       hopefully we will not run out of memory here...
        if ($CFG->debugdeveloper) {
            if (!($record instanceof \stdClass)) {
                debugging('Argument $record must be an instance of stdClass.', DEBUG_DEVELOPER);
            }
            if (!$DB->get_manager()->table_exists($tablename)) {
                debugging("Invalid table name '$tablename' specified, database table does not exist.", DEBUG_DEVELOPER);
            } else {
                $columns = $DB->get_columns($tablename);
                $missingfields = array_diff(array_keys($columns), array_keys((array)$record));
                if (!empty($missingfields)) {
                    debugging("Fields list in snapshot record does not match fields list in '$tablename'. Record is missing fields: ".
                            join(', ', $missingfields), DEBUG_DEVELOPER);
                }
            }
        }
        $this->recordsnapshots[$tablename][$record->id] = $record;
    }

    /**
     * Returns cached record or fetches data from database if not cached.
     *
     * @param string $tablename
     * @param int $id
     * @return \stdClass
     *
     * @throws \coding_exception if used after ::restore()
     */
    public final function get_record_snapshot($tablename, $id) {
        global $DB;

        if ($this->restored) {
            throw new \coding_exception('It is not possible to get snapshots from restored events');
        }

        if (isset($this->recordsnapshots[$tablename][$id])) {
            return clone($this->recordsnapshots[$tablename][$id]);
        }

        $record = $DB->get_record($tablename, array('id'=>$id));
        $this->recordsnapshots[$tablename][$id] = $record;

        return $record;
    }

    /**
     * Magic getter for read only access.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if ($name === 'level') {
            debugging('level property is deprecated, use edulevel property instead', DEBUG_DEVELOPER);
            return $this->data['edulevel'];
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        debugging("Accessing non-existent event property '$name'");
    }

    /**
     * Magic setter.
     *
     * Note: we must not allow modification of data from outside,
     *       after trigger() the data MUST NOT CHANGE!!!
     *
     * @param string $name
     * @param mixed $value
     *
     * @throws \coding_exception
     */
    public function __set($name, $value) {
        throw new \coding_exception('Event properties must not be modified.');
    }

    /**
     * Is data property set?
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        if ($name === 'level') {
            debugging('level property is deprecated, use edulevel property instead', DEBUG_DEVELOPER);
            return isset($this->data['edulevel']);
        }
        return isset($this->data[$name]);
    }

    /**
     * Create an iterator because magic vars can't be seen by 'foreach'.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->data);
    }

    /**
     * Whether this event has been marked as deprecated.
     *
     * Events cannot be deprecated in the normal fashion as they must remain to support historical data.
     * Once they are deprecated, there is no way to trigger the event, so it does not make sense to list it in some
     * parts of the UI (e.g. Event Monitor).
     *
     * @return boolean
     */
    public static function is_deprecated() {
        return false;
    }
}
