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
 * Calendar extension
 *
 * @package    core_calendar
 * @copyright  2004 Greek School Network (http://www.sch.gr), Jon Papaioannou,
 *             Avgoustos Tsinakos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 *  These are read by the administration component to provide default values
 */

/**
 * CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD - default value of upcoming event preference
 */
define('CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD', 21);

/**
 * CALENDAR_DEFAULT_UPCOMING_MAXEVENTS - default value to display the maximum number of upcoming event
 */
define('CALENDAR_DEFAULT_UPCOMING_MAXEVENTS', 10);

/**
 * CALENDAR_DEFAULT_STARTING_WEEKDAY - default value to display the starting weekday
 */
define('CALENDAR_DEFAULT_STARTING_WEEKDAY', 1);

// This is a packed bitfield: day X is "weekend" if $field & (1 << X) is true
// Default value = 65 = 64 + 1 = 2^6 + 2^0 = Saturday & Sunday

/**
 * CALENDAR_DEFAULT_WEEKEND - default value for weekend (Saturday & Sunday)
 */
define('CALENDAR_DEFAULT_WEEKEND', 65);

/**
 * CALENDAR_URL - path to calendar's folder
 */
define('CALENDAR_URL', $CFG->wwwroot.'/calendar/');

/**
 * CALENDAR_TF_24 - Calendar time in 24 hours format
 */
define('CALENDAR_TF_24', '%H:%M');

/**
 * CALENDAR_TF_12 - Calendar time in 12 hours format
 */
define('CALENDAR_TF_12', '%I:%M %p');

/**
 * CALENDAR_EVENT_GLOBAL - Global calendar event types
 */
define('CALENDAR_EVENT_GLOBAL', 1);

/**
 * CALENDAR_EVENT_COURSE - Course calendar event types
 */
define('CALENDAR_EVENT_COURSE', 2);

/**
 * CALENDAR_EVENT_GROUP - group calendar event types
 */
define('CALENDAR_EVENT_GROUP', 4);

/**
 * CALENDAR_EVENT_USER - user calendar event types
 */
define('CALENDAR_EVENT_USER', 8);

/**
 * CALENDAR_EVENT_COURSECAT - Course category calendar event types
 */
define('CALENDAR_EVENT_COURSECAT', 16);

/**
 * CALENDAR_IMPORT_FROM_FILE - import the calendar from a file
 */
define('CALENDAR_IMPORT_FROM_FILE', 0);

/**
 * CALENDAR_IMPORT_FROM_URL - import the calendar from a URL
 */
define('CALENDAR_IMPORT_FROM_URL',  1);

/**
 * CALENDAR_IMPORT_EVENT_UPDATED - imported event was updated
 */
define('CALENDAR_IMPORT_EVENT_UPDATED',  1);

/**
 * CALENDAR_IMPORT_EVENT_INSERTED - imported event was added by insert
 */
define('CALENDAR_IMPORT_EVENT_INSERTED', 2);

/**
 * CALENDAR_SUBSCRIPTION_UPDATE - Used to represent update action for subscriptions in various forms.
 */
define('CALENDAR_SUBSCRIPTION_UPDATE', 1);

/**
 * CALENDAR_SUBSCRIPTION_REMOVE - Used to represent remove action for subscriptions in various forms.
 */
define('CALENDAR_SUBSCRIPTION_REMOVE', 2);

/**
 * CALENDAR_EVENT_USER_OVERRIDE_PRIORITY - Constant for the user override priority.
 */
define('CALENDAR_EVENT_USER_OVERRIDE_PRIORITY', 0);

/**
 * CALENDAR_EVENT_TYPE_STANDARD - Standard events.
 */
define('CALENDAR_EVENT_TYPE_STANDARD', 0);

/**
 * CALENDAR_EVENT_TYPE_ACTION - Action events.
 */
define('CALENDAR_EVENT_TYPE_ACTION', 1);

/**
 * Manage calendar events.
 *
 * This class provides the required functionality in order to manage calendar events.
 * It was introduced as part of Moodle 2.0 and was created in order to provide a
 * better framework for dealing with calendar events in particular regard to file
 * handling through the new file API.
 *
 * @package    core_calendar
 * @category   calendar
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property int $id The id within the event table
 * @property string $name The name of the event
 * @property string $description The description of the event
 * @property int $format The format of the description FORMAT_?
 * @property int $courseid The course the event is associated with (0 if none)
 * @property int $groupid The group the event is associated with (0 if none)
 * @property int $userid The user the event is associated with (0 if none)
 * @property int $repeatid If this is a repeated event this will be set to the
 *                          id of the original
 * @property string $modulename If added by a module this will be the module name
 * @property int $instance If added by a module this will be the module instance
 * @property string $eventtype The event type
 * @property int $timestart The start time as a timestamp
 * @property int $timeduration The duration of the event in seconds
 * @property int $visible 1 if the event is visible
 * @property int $uuid ?
 * @property int $sequence ?
 * @property int $timemodified The time last modified as a timestamp
 */
class calendar_event {

    /** @var array An object containing the event properties can be accessed via the magic __get/set methods */
    protected $properties = null;

    /** @var string The converted event discription with file paths resolved.
     *              This gets populated when someone requests description for the first time */
    protected $_description = null;

    /** @var array The options to use with this description editor */
    protected $editoroptions = array(
        'subdirs' => false,
        'forcehttps' => false,
        'maxfiles' => -1,
        'maxbytes' => null,
        'trusttext' => false);

    /** @var object The context to use with the description editor */
    protected $editorcontext = null;

    /**
     * Instantiates a new event and optionally populates its properties with the data provided.
     *
     * @param \stdClass $data Optional. An object containing the properties to for
     *                  an event
     */
    public function __construct($data = null) {
        global $CFG, $USER;

        // First convert to object if it is not already (should either be object or assoc array).
        if (!is_object($data)) {
            $data = (object) $data;
        }

        $this->editoroptions['maxbytes'] = $CFG->maxbytes;

        $data->eventrepeats = 0;

        if (empty($data->id)) {
            $data->id = null;
        }

        if (!empty($data->subscriptionid)) {
            $data->subscription = calendar_get_subscription($data->subscriptionid);
        }

        // Default to a user event.
        if (empty($data->eventtype)) {
            $data->eventtype = 'user';
        }

        // Default to the current user.
        if (empty($data->userid)) {
            $data->userid = $USER->id;
        }

        if (!empty($data->timeduration) && is_array($data->timeduration)) {
            $data->timeduration = make_timestamp(
                    $data->timeduration['year'], $data->timeduration['month'], $data->timeduration['day'],
                    $data->timeduration['hour'], $data->timeduration['minute']) - $data->timestart;
        }

        if (!empty($data->description) && is_array($data->description)) {
            $data->format = $data->description['format'];
            $data->description = $data->description['text'];
        } else if (empty($data->description)) {
            $data->description = '';
            $data->format = editors_get_preferred_format();
        }

        // Ensure form is defaulted correctly.
        if (empty($data->format)) {
            $data->format = editors_get_preferred_format();
        }

        $this->properties = $data;
    }

    /**
     * Magic set method.
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property.
     *
     * @param string $key property name
     * @param mixed $value value of the property
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
        $this->properties->{$key} = $value;
    }

    /**
     * Magic get method.
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property.
     *
     * @param string $key property name
     * @return mixed property value
     * @throws \coding_exception
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        if (!property_exists($this->properties, $key)) {
            throw new \coding_exception('Undefined property requested');
        }
        return $this->properties->{$key};
    }

    /**
     * Magic isset method.
     *
     * PHP needs an isset magic method if you use the get magic method and
     * still want empty calls to work.
     *
     * @param string $key $key property name
     * @return bool|mixed property value, false if property is not exist
     */
    public function __isset($key) {
        return !empty($this->properties->{$key});
    }

    /**
     * Calculate the context value needed for an event.
     *
     * Event's type can be determine by the available value store in $data
     * It is important to check for the existence of course/courseid to determine
     * the course event.
     * Default value is set to CONTEXT_USER
     *
     * @return \stdClass The context object.
     */
    protected function calculate_context() {
        global $USER, $DB;

        $context = null;
        if (isset($this->properties->categoryid) && $this->properties->categoryid > 0) {
            $context = \context_coursecat::instance($this->properties->categoryid);
        } else if (isset($this->properties->courseid) && $this->properties->courseid > 0) {
            $context = \context_course::instance($this->properties->courseid);
        } else if (isset($this->properties->course) && $this->properties->course > 0) {
            $context = \context_course::instance($this->properties->course);
        } else if (isset($this->properties->groupid) && $this->properties->groupid > 0) {
            $group = $DB->get_record('groups', array('id' => $this->properties->groupid));
            $context = \context_course::instance($group->courseid);
        } else if (isset($this->properties->userid) && $this->properties->userid > 0
            && $this->properties->userid == $USER->id) {
            $context = \context_user::instance($this->properties->userid);
        } else if (isset($this->properties->userid) && $this->properties->userid > 0
            && $this->properties->userid != $USER->id &&
            isset($this->properties->instance) && $this->properties->instance > 0) {
            $cm = get_coursemodule_from_instance($this->properties->modulename, $this->properties->instance, 0,
                false, MUST_EXIST);
            $context = \context_course::instance($cm->course);
        } else {
            $context = \context_user::instance($this->properties->userid);
        }

        return $context;
    }

    /**
     * Returns the context for this event. The context is calculated
     * the first time is is requested and then stored in a member
     * variable to be returned each subsequent time.
     *
     * This is a magical getter function that will be called when
     * ever the context property is accessed, e.g. $event->context.
     *
     * @return context
     */
    protected function get_context() {
        if (!isset($this->properties->context)) {
            $this->properties->context = $this->calculate_context();
        }

        return $this->properties->context;
    }

    /**
     * Returns an array of editoroptions for this event.
     *
     * @return array event editor options
     */
    protected function get_editoroptions() {
        return $this->editoroptions;
    }

    /**
     * Returns an event description: Called by __get
     * Please use $blah = $event->description;
     *
     * @return string event description
     */
    protected function get_description() {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        if ($this->_description === null) {
            // Check if we have already resolved the context for this event.
            if ($this->editorcontext === null) {
                // Switch on the event type to decide upon the appropriate context to use for this event.
                $this->editorcontext = $this->get_context();
                if (!calendar_is_valid_eventtype($this->properties->eventtype)) {
                    return clean_text($this->properties->description, $this->properties->format);
                }
            }

            // Work out the item id for the editor, if this is a repeated event
            // then the files will be associated with the original.
            if (!empty($this->properties->repeatid) && $this->properties->repeatid > 0) {
                $itemid = $this->properties->repeatid;
            } else {
                $itemid = $this->properties->id;
            }

            // Convert file paths in the description so that things display correctly.
            $this->_description = file_rewrite_pluginfile_urls($this->properties->description, 'pluginfile.php',
                $this->editorcontext->id, 'calendar', 'event_description', $itemid);
            // Clean the text so no nasties get through.
            $this->_description = clean_text($this->_description, $this->properties->format);
        }

        // Finally return the description.
        return $this->_description;
    }

    /**
     * Return the number of repeat events there are in this events series.
     *
     * @return int number of event repeated
     */
    public function count_repeats() {
        global $DB;
        if (!empty($this->properties->repeatid)) {
            $this->properties->eventrepeats = $DB->count_records('event',
                array('repeatid' => $this->properties->repeatid));
            // We don't want to count ourselves.
            $this->properties->eventrepeats--;
        }
        return $this->properties->eventrepeats;
    }

    /**
     * Update or create an event within the database
     *
     * Pass in a object containing the event properties and this function will
     * insert it into the database and deal with any associated files
     *
     * Capability checking should be performed if the user is directly manipulating the event
     * and no other capability has been tested. However if the event is not being manipulated
     * directly by the user and another capability has been checked for them to do this then
     * capabilites should not be checked.
     *
     * For example if a user is editing an event in the calendar the check should be true,
     * but if you are updating an event in an activities settings are changed then the calendar
     * capabilites should not be checked.
     *
     * @see self::create()
     * @see self::update()
     *
     * @param \stdClass $data object of event
     * @param bool $checkcapability If Moodle should check the user can manage the calendar events for this call or not.
     * @return bool event updated
     */
    public function update($data, $checkcapability=true) {
        global $DB, $USER;

        foreach ($data as $key => $value) {
            $this->properties->$key = $value;
        }

        $this->properties->timemodified = time();
        $usingeditor = (!empty($this->properties->description) && is_array($this->properties->description));

        // Prepare event data.
        $eventargs = array(
            'context' => $this->get_context(),
            'objectid' => $this->properties->id,
            'other' => array(
                'repeatid' => empty($this->properties->repeatid) ? 0 : $this->properties->repeatid,
                'timestart' => $this->properties->timestart,
                'name' => $this->properties->name
            )
        );

        if (empty($this->properties->id) || $this->properties->id < 1) {
            if ($checkcapability) {
                if (!calendar_add_event_allowed($this->properties)) {
                    print_error('nopermissiontoupdatecalendar');
                }
            }

            if ($usingeditor) {
                switch ($this->properties->eventtype) {
                    case 'user':
                        $this->properties->courseid = 0;
                        $this->properties->course = 0;
                        $this->properties->groupid = 0;
                        $this->properties->userid = $USER->id;
                        break;
                    case 'site':
                        $this->properties->courseid = SITEID;
                        $this->properties->course = SITEID;
                        $this->properties->groupid = 0;
                        $this->properties->userid = $USER->id;
                        break;
                    case 'course':
                        $this->properties->groupid = 0;
                        $this->properties->userid = $USER->id;
                        break;
                    case 'category':
                        $this->properties->groupid = 0;
                        $this->properties->category = 0;
                        $this->properties->userid = $USER->id;
                        break;
                    case 'group':
                        $this->properties->userid = $USER->id;
                        break;
                    default:
                        // We should NEVER get here, but just incase we do lets fail gracefully.
                        $usingeditor = false;
                        break;
                }

                // If we are actually using the editor, we recalculate the context because some default values
                // were set when calculate_context() was called from the constructor.
                if ($usingeditor) {
                    $this->properties->context = $this->calculate_context();
                    $this->editorcontext = $this->get_context();
                }

                $editor = $this->properties->description;
                $this->properties->format = $this->properties->description['format'];
                $this->properties->description = $this->properties->description['text'];
            }

            // Insert the event into the database.
            $this->properties->id = $DB->insert_record('event', $this->properties);

            if ($usingeditor) {
                $this->properties->description = file_save_draft_area_files(
                    $editor['itemid'],
                    $this->editorcontext->id,
                    'calendar',
                    'event_description',
                    $this->properties->id,
                    $this->editoroptions,
                    $editor['text'],
                    $this->editoroptions['forcehttps']);
                $DB->set_field('event', 'description', $this->properties->description,
                    array('id' => $this->properties->id));
            }

            // Log the event entry.
            $eventargs['objectid'] = $this->properties->id;
            $eventargs['context'] = $this->get_context();
            $event = \core\event\calendar_event_created::create($eventargs);
            $event->trigger();

            $repeatedids = array();

            if (!empty($this->properties->repeat)) {
                $this->properties->repeatid = $this->properties->id;
                $DB->set_field('event', 'repeatid', $this->properties->repeatid, array('id' => $this->properties->id));

                $eventcopy = clone($this->properties);
                unset($eventcopy->id);

                $timestart = new \DateTime('@' . $eventcopy->timestart);
                $timestart->setTimezone(\core_date::get_user_timezone_object());

                for ($i = 1; $i < $eventcopy->repeats; $i++) {

                    $timestart->add(new \DateInterval('P7D'));
                    $eventcopy->timestart = $timestart->getTimestamp();

                    // Get the event id for the log record.
                    $eventcopyid = $DB->insert_record('event', $eventcopy);

                    // If the context has been set delete all associated files.
                    if ($usingeditor) {
                        $fs = get_file_storage();
                        $files = $fs->get_area_files($this->editorcontext->id, 'calendar', 'event_description',
                            $this->properties->id);
                        foreach ($files as $file) {
                            $fs->create_file_from_storedfile(array('itemid' => $eventcopyid), $file);
                        }
                    }

                    $repeatedids[] = $eventcopyid;

                    // Trigger an event.
                    $eventargs['objectid'] = $eventcopyid;
                    $eventargs['other']['timestart'] = $eventcopy->timestart;
                    $event = \core\event\calendar_event_created::create($eventargs);
                    $event->trigger();
                }
            }

            return true;
        } else {

            if ($checkcapability) {
                if (!calendar_edit_event_allowed($this->properties)) {
                    print_error('nopermissiontoupdatecalendar');
                }
            }

            if ($usingeditor) {
                if ($this->editorcontext !== null) {
                    $this->properties->description = file_save_draft_area_files(
                        $this->properties->description['itemid'],
                        $this->editorcontext->id,
                        'calendar',
                        'event_description',
                        $this->properties->id,
                        $this->editoroptions,
                        $this->properties->description['text'],
                        $this->editoroptions['forcehttps']);
                } else {
                    $this->properties->format = $this->properties->description['format'];
                    $this->properties->description = $this->properties->description['text'];
                }
            }

            $event = $DB->get_record('event', array('id' => $this->properties->id));

            $updaterepeated = (!empty($this->properties->repeatid) && !empty($this->properties->repeateditall));

            if ($updaterepeated) {

                $sqlset = 'name = ?,
                           description = ?,
                           timeduration = ?,
                           timemodified = ?,
                           groupid = ?,
                           courseid = ?';

                // Note: Group and course id may not be set. If not, keep their current values.
                $params = [
                    $this->properties->name,
                    $this->properties->description,
                    $this->properties->timeduration,
                    time(),
                    isset($this->properties->groupid) ? $this->properties->groupid : $event->groupid,
                    isset($this->properties->courseid) ? $this->properties->courseid : $event->courseid,
                ];

                // Note: Only update start date, if it was changed by the user.
                if ($this->properties->timestart != $event->timestart) {
                    $timestartoffset = $this->properties->timestart - $event->timestart;
                    $sqlset .= ', timestart = timestart + ?';
                    $params[] = $timestartoffset;
                }

                // Note: Only update location, if it was changed by the user.
                $updatelocation = (!empty($this->properties->location) && $this->properties->location !== $event->location);
                if ($updatelocation) {
                    $sqlset .= ', location = ?';
                    $params[] = $this->properties->location;
                }

                // Update all.
                $sql = "UPDATE {event}
                           SET $sqlset
                         WHERE repeatid = ?";

                $params[] = $event->repeatid;
                $DB->execute($sql, $params);

                // Trigger an update event for each of the calendar event.
                $events = $DB->get_records('event', array('repeatid' => $event->repeatid), '', '*');
                foreach ($events as $calendarevent) {
                    $eventargs['objectid'] = $calendarevent->id;
                    $eventargs['other']['timestart'] = $calendarevent->timestart;
                    $event = \core\event\calendar_event_updated::create($eventargs);
                    $event->add_record_snapshot('event', $calendarevent);
                    $event->trigger();
                }
            } else {
                $DB->update_record('event', $this->properties);
                $event = self::load($this->properties->id);
                $this->properties = $event->properties();

                // Trigger an update event.
                $event = \core\event\calendar_event_updated::create($eventargs);
                $event->add_record_snapshot('event', $this->properties);
                $event->trigger();
            }

            return true;
        }
    }

    /**
     * Deletes an event and if selected an repeated events in the same series
     *
     * This function deletes an event, any associated events if $deleterepeated=true,
     * and cleans up any files associated with the events.
     *
     * @see self::delete()
     *
     * @param bool $deleterepeated  delete event repeatedly
     * @return bool succession of deleting event
     */
    public function delete($deleterepeated = false) {
        global $DB;

        // If $this->properties->id is not set then something is wrong.
        if (empty($this->properties->id)) {
            debugging('Attempting to delete an event before it has been loaded', DEBUG_DEVELOPER);
            return false;
        }
        $calevent = $DB->get_record('event',  array('id' => $this->properties->id), '*', MUST_EXIST);
        // Delete the event.
        $DB->delete_records('event', array('id' => $this->properties->id));

        // Trigger an event for the delete action.
        $eventargs = array(
            'context' => $this->get_context(),
            'objectid' => $this->properties->id,
            'other' => array(
                'repeatid' => empty($this->properties->repeatid) ? 0 : $this->properties->repeatid,
                'timestart' => $this->properties->timestart,
                'name' => $this->properties->name
            ));
        $event = \core\event\calendar_event_deleted::create($eventargs);
        $event->add_record_snapshot('event', $calevent);
        $event->trigger();

        // If we are deleting parent of a repeated event series, promote the next event in the series as parent.
        if (($this->properties->id == $this->properties->repeatid) && !$deleterepeated) {
            $newparent = $DB->get_field_sql("SELECT id from {event} where repeatid = ? order by id ASC",
                array($this->properties->id), IGNORE_MULTIPLE);
            if (!empty($newparent)) {
                $DB->execute("UPDATE {event} SET repeatid = ? WHERE repeatid = ?",
                    array($newparent, $this->properties->id));
                // Get all records where the repeatid is the same as the event being removed.
                $events = $DB->get_records('event', array('repeatid' => $newparent));
                // For each of the returned events trigger an update event.
                foreach ($events as $calendarevent) {
                    // Trigger an event for the update.
                    $eventargs['objectid'] = $calendarevent->id;
                    $eventargs['other']['timestart'] = $calendarevent->timestart;
                    $event = \core\event\calendar_event_updated::create($eventargs);
                    $event->add_record_snapshot('event', $calendarevent);
                    $event->trigger();
                }
            }
        }

        // If the editor context hasn't already been set then set it now.
        if ($this->editorcontext === null) {
            $this->editorcontext = $this->get_context();
        }

        // If the context has been set delete all associated files.
        if ($this->editorcontext !== null) {
            $fs = get_file_storage();
            $files = $fs->get_area_files($this->editorcontext->id, 'calendar', 'event_description', $this->properties->id);
            foreach ($files as $file) {
                $file->delete();
            }
        }

        // If we need to delete repeated events then we will fetch them all and delete one by one.
        if ($deleterepeated && !empty($this->properties->repeatid) && $this->properties->repeatid > 0) {
            // Get all records where the repeatid is the same as the event being removed.
            $events = $DB->get_records('event', array('repeatid' => $this->properties->repeatid));
            // For each of the returned events populate an event object and call delete.
            // make sure the arg passed is false as we are already deleting all repeats.
            foreach ($events as $event) {
                $event = new calendar_event($event);
                $event->delete(false);
            }
        }

        return true;
    }

    /**
     * Fetch all event properties.
     *
     * This function returns all of the events properties as an object and optionally
     * can prepare an editor for the description field at the same time. This is
     * designed to work when the properties are going to be used to set the default
     * values of a moodle forms form.
     *
     * @param bool $prepareeditor If set to true a editor is prepared for use with
     *              the mforms editor element. (for description)
     * @return \stdClass Object containing event properties
     */
    public function properties($prepareeditor = false) {
        global $DB;

        // First take a copy of the properties. We don't want to actually change the
        // properties or we'd forever be converting back and forwards between an
        // editor formatted description and not.
        $properties = clone($this->properties);
        // Clean the description here.
        $properties->description = clean_text($properties->description, $properties->format);

        // If set to true we need to prepare the properties for use with an editor
        // and prepare the file area.
        if ($prepareeditor) {

            // We may or may not have a property id. If we do then we need to work
            // out the context so we can copy the existing files to the draft area.
            if (!empty($properties->id)) {

                if ($properties->eventtype === 'site') {
                    // Site context.
                    $this->editorcontext = $this->get_context();
                } else if ($properties->eventtype === 'user') {
                    // User context.
                    $this->editorcontext = $this->get_context();
                } else if ($properties->eventtype === 'group' || $properties->eventtype === 'course') {
                    // First check the course is valid.
                    $course = $DB->get_record('course', array('id' => $properties->courseid));
                    if (!$course) {
                        print_error('invalidcourse');
                    }
                    // Course context.
                    $this->editorcontext = $this->get_context();
                    // We have a course and are within the course context so we had
                    // better use the courses max bytes value.
                    $this->editoroptions['maxbytes'] = $course->maxbytes;
                } else if ($properties->eventtype === 'category') {
                    // First check the course is valid.
                    \core_course_category::get($properties->categoryid, MUST_EXIST, true);
                    // Course context.
                    $this->editorcontext = $this->get_context();
                } else {
                    // If we get here we have a custom event type as used by some
                    // modules. In this case the event will have been added by
                    // code and we won't need the editor.
                    $this->editoroptions['maxbytes'] = 0;
                    $this->editoroptions['maxfiles'] = 0;
                }

                if (empty($this->editorcontext) || empty($this->editorcontext->id)) {
                    $contextid = false;
                } else {
                    // Get the context id that is what we really want.
                    $contextid = $this->editorcontext->id;
                }
            } else {

                // If we get here then this is a new event in which case we don't need a
                // context as there is no existing files to copy to the draft area.
                $contextid = null;
            }

            // If the contextid === false we don't support files so no preparing
            // a draft area.
            if ($contextid !== false) {
                // Just encase it has already been submitted.
                $draftiddescription = file_get_submitted_draft_itemid('description');
                // Prepare the draft area, this copies existing files to the draft area as well.
                $properties->description = file_prepare_draft_area($draftiddescription, $contextid, 'calendar',
                    'event_description', $properties->id, $this->editoroptions, $properties->description);
            } else {
                $draftiddescription = 0;
            }

            // Structure the description field as the editor requires.
            $properties->description = array('text' => $properties->description, 'format' => $properties->format,
                'itemid' => $draftiddescription);
        }

        // Finally return the properties.
        return $properties;
    }

    /**
     * Toggles the visibility of an event
     *
     * @param null|bool $force If it is left null the events visibility is flipped,
     *                   If it is false the event is made hidden, if it is true it
     *                   is made visible.
     * @return bool if event is successfully updated, toggle will be visible
     */
    public function toggle_visibility($force = null) {
        global $DB;

        // Set visible to the default if it is not already set.
        if (empty($this->properties->visible)) {
            $this->properties->visible = 1;
        }

        if ($force === true || ($force !== false && $this->properties->visible == 0)) {
            // Make this event visible.
            $this->properties->visible = 1;
        } else {
            // Make this event hidden.
            $this->properties->visible = 0;
        }

        // Update the database to reflect this change.
        $success = $DB->set_field('event', 'visible', $this->properties->visible, array('id' => $this->properties->id));
        $calendarevent = $DB->get_record('event',  array('id' => $this->properties->id), '*', MUST_EXIST);

        // Prepare event data.
        $eventargs = array(
            'context' => $this->get_context(),
            'objectid' => $this->properties->id,
            'other' => array(
                'repeatid' => empty($this->properties->repeatid) ? 0 : $this->properties->repeatid,
                'timestart' => $this->properties->timestart,
                'name' => $this->properties->name
            )
        );
        $event = \core\event\calendar_event_updated::create($eventargs);
        $event->add_record_snapshot('event', $calendarevent);
        $event->trigger();

        return $success;
    }

    /**
     * Returns an event object when provided with an event id.
     *
     * This function makes use of MUST_EXIST, if the event id passed in is invalid
     * it will result in an exception being thrown.
     *
     * @param int|object $param event object or event id
     * @return calendar_event
     */
    public static function load($param) {
        global $DB;
        if (is_object($param)) {
            $event = new calendar_event($param);
        } else {
            $event = $DB->get_record('event', array('id' => (int)$param), '*', MUST_EXIST);
            $event = new calendar_event($event);
        }
        return $event;
    }

    /**
     * Creates a new event and returns an event object.
     *
     * Capability checking should be performed if the user is directly creating the event
     * and no other capability has been tested. However if the event is not being created
     * directly by the user and another capability has been checked for them to do this then
     * capabilites should not be checked.
     *
     * For example if a user is creating an event in the calendar the check should be true,
     * but if you are creating an event in an activity when it is created then the calendar
     * capabilites should not be checked.
     *
     * @param \stdClass|array $properties An object containing event properties
     * @param bool $checkcapability If Moodle should check the user can manage the calendar events for this call or not.
     * @throws \coding_exception
     *
     * @return calendar_event|bool The event object or false if it failed
     */
    public static function create($properties, $checkcapability = true) {
        if (is_array($properties)) {
            $properties = (object)$properties;
        }
        if (!is_object($properties)) {
            throw new \coding_exception('When creating an event properties should be either an object or an assoc array');
        }
        $event = new calendar_event($properties);
        if ($event->update($properties, $checkcapability)) {
            return $event;
        } else {
            return false;
        }
    }

    /**
     * Format the text using the external API.
     *
     * This function should we used when text formatting is required in external functions.
     *
     * @return array an array containing the text formatted and the text format
     */
    public function format_external_text() {

        if ($this->editorcontext === null) {
            // Switch on the event type to decide upon the appropriate context to use for this event.
            $this->editorcontext = $this->get_context();

            if (!calendar_is_valid_eventtype($this->properties->eventtype)) {
                // We don't have a context here, do a normal format_text.
                return external_format_text($this->properties->description, $this->properties->format, $this->editorcontext->id);
            }
        }

        // Work out the item id for the editor, if this is a repeated event then the files will be associated with the original.
        if (!empty($this->properties->repeatid) && $this->properties->repeatid > 0) {
            $itemid = $this->properties->repeatid;
        } else {
            $itemid = $this->properties->id;
        }

        return external_format_text($this->properties->description, $this->properties->format, $this->editorcontext->id,
            'calendar', 'event_description', $itemid);
    }
}

/**
 * Calendar information class
 *
 * This class is used simply to organise the information pertaining to a calendar
 * and is used primarily to make information easily available.
 *
 * @package core_calendar
 * @category calendar
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_information {

    /**
     * @var int The timestamp
     *
     * Rather than setting the day, month and year we will set a timestamp which will be able
     * to be used by multiple calendars.
     */
    public $time;

    /** @var int A course id */
    public $courseid = null;

    /** @var array An array of categories */
    public $categories = array();

    /** @var int The current category */
    public $categoryid = null;

    /** @var array An array of courses */
    public $courses = array();

    /** @var array An array of groups */
    public $groups = array();

    /** @var array An array of users */
    public $users = array();

    /** @var context The anticipated context that the calendar is viewed in */
    public $context = null;

    /**
     * Creates a new instance
     *
     * @param int $day the number of the day
     * @param int $month the number of the month
     * @param int $year the number of the year
     * @param int $time the unixtimestamp representing the date we want to view, this is used instead of $calmonth
     *     and $calyear to support multiple calendars
     */
    public function __construct($day = 0, $month = 0, $year = 0, $time = 0) {
        // If a day, month and year were passed then convert it to a timestamp. If these were passed
        // then we can assume the day, month and year are passed as Gregorian, as no where in core
        // should we be passing these values rather than the time. This is done for BC.
        if (!empty($day) || !empty($month) || !empty($year)) {
            $date = usergetdate(time());
            if (empty($day)) {
                $day = $date['mday'];
            }
            if (empty($month)) {
                $month = $date['mon'];
            }
            if (empty($year)) {
                $year =  $date['year'];
            }
            if (checkdate($month, $day, $year)) {
                $time = make_timestamp($year, $month, $day);
            } else {
                $time = time();
            }
        }

        $this->set_time($time);
    }

    /**
     * Creates and set up a instance.
     *
     * @param   int                     $time the unixtimestamp representing the date we want to view.
     * @param   int                     $courseid The ID of the course the user wishes to view.
     * @param   int                     $categoryid The ID of the category the user wishes to view
     *                                  If a courseid is specified, this value is ignored.
     * @return  calendar_information
     */
    public static function create($time, int $courseid, int $categoryid = null) : calendar_information {
        $calendar = new static(0, 0, 0, $time);
        if ($courseid != SITEID && !empty($courseid)) {
            // Course ID must be valid and existing.
            $course = get_course($courseid);
            $calendar->context = context_course::instance($course->id);

            if (!$course->visible && !is_role_switched($course->id)) {
                require_capability('moodle/course:viewhiddencourses', $calendar->context);
            }

            $courses = [$course->id => $course];
            $category = (\core_course_category::get($course->category, MUST_EXIST, true))->get_db_record();
        } else if (!empty($categoryid)) {
            $course = get_site();
            $courses = calendar_get_default_courses(null, 'id, category, groupmode, groupmodeforce');

            // Filter available courses to those within this category or it's children.
            $ids = [$categoryid];
            $category = \core_course_category::get($categoryid);
            $ids = array_merge($ids, array_keys($category->get_children()));
            $courses = array_filter($courses, function($course) use ($ids) {
                return array_search($course->category, $ids) !== false;
            });
            $category = $category->get_db_record();

            $calendar->context = context_coursecat::instance($categoryid);
        } else {
            $course = get_site();
            $courses = calendar_get_default_courses(null, 'id, category, groupmode, groupmodeforce');
            $category = null;

            $calendar->context = context_system::instance();
        }

        $calendar->set_sources($course, $courses, $category);

        return $calendar;
    }

    /**
     * Set the time period of this instance.
     *
     * @param   int $time the unixtimestamp representing the date we want to view.
     * @return  $this
     */
    public function set_time($time = null) {
        if (empty($time)) {
            $this->time = time();
        } else {
            $this->time = $time;
        }

        return $this;
    }

    /**
     * Initialize calendar information
     *
     * @deprecated 3.4
     * @param stdClass $course object
     * @param array $coursestoload An array of courses [$course->id => $course]
     * @param bool $ignorefilters options to use filter
     */
    public function prepare_for_view(stdClass $course, array $coursestoload, $ignorefilters = false) {
        debugging('The prepare_for_view() function has been deprecated. Please update your code to use set_sources()',
                DEBUG_DEVELOPER);
        $this->set_sources($course, $coursestoload);
    }

    /**
     * Set the sources for events within the calendar.
     *
     * If no category is provided, then the category path for the current
     * course will be used.
     *
     * @param   stdClass    $course The current course being viewed.
     * @param   stdClass[]  $courses The list of all courses currently accessible.
     * @param   stdClass    $category The current category to show.
     */
    public function set_sources(stdClass $course, array $courses, stdClass $category = null) {
        global $USER;

        // A cousre must always be specified.
        $this->course = $course;
        $this->courseid = $course->id;

        list($courseids, $group, $user) = calendar_set_filters($courses);
        $this->courses = $courseids;
        $this->groups = $group;
        $this->users = $user;

        // Do not show category events by default.
        $this->categoryid = null;
        $this->categories = null;

        // Determine the correct category information to show.
        // When called with a course, the category of that course is usually included too.
        // When a category was specifically requested, it should be requested with the site id.
        if (SITEID !== $this->courseid) {
            // A specific course was requested.
            // Fetch the category that this course is in, along with all parents.
            // Do not include child categories of this category, as the user many not have enrolments in those siblings or children.
            $category = \core_course_category::get($course->category, MUST_EXIST, true);
            $this->categoryid = $category->id;

            $this->categories = $category->get_parents();
            $this->categories[] = $category->id;
        } else if (null !== $category && $category->id > 0) {
            // A specific category was requested.
            // Fetch all parents of this category, along with all children too.
            $category = \core_course_category::get($category->id);
            $this->categoryid = $category->id;

            // Build the category list.
            // This includes the current category.
            $this->categories = $category->get_parents();
            $this->categories[] = $category->id;
            $this->categories = array_merge($this->categories, $category->get_all_children_ids());
        } else if (SITEID === $this->courseid) {
            // The site was requested.
            // Fetch all categories where this user has any enrolment, and all categories that this user can manage.

            // Grab the list of categories that this user has courses in.
            $coursecategories = array_flip(array_map(function($course) {
                return $course->category;
            }, $courses));

            $calcatcache = cache::make('core', 'calendar_categories');
            $this->categories = $calcatcache->get('site');
            if ($this->categories === false) {
                // Use the category id as the key in the following array. That way we do not have to remove duplicates.
                $categories = [];
                foreach (\core_course_category::get_all() as $category) {
                    if (isset($coursecategories[$category->id]) ||
                            has_capability('moodle/category:manage', $category->get_context(), $USER, false)) {
                        // If the user has access to a course in this category or can manage the category,
                        // then they can see all parent categories too.
                        $categories[$category->id] = true;
                        foreach ($category->get_parents() as $catid) {
                            $categories[$catid] = true;
                        }
                    }
                }
                $this->categories = array_keys($categories);
                $calcatcache->set('site', $this->categories);
            }
        }
    }

    /**
     * Ensures the date for the calendar is correct and either sets it to now
     * or throws a moodle_exception if not
     *
     * @param bool $defaultonow use current time
     * @throws moodle_exception
     * @return bool validation of checkdate
     */
    public function checkdate($defaultonow = true) {
        if (!checkdate($this->month, $this->day, $this->year)) {
            if ($defaultonow) {
                $now = usergetdate(time());
                $this->day = intval($now['mday']);
                $this->month = intval($now['mon']);
                $this->year = intval($now['year']);
                return true;
            } else {
                throw new moodle_exception('invaliddate');
            }
        }
        return true;
    }

    /**
     * Gets todays timestamp for the calendar
     *
     * @return int today timestamp
     */
    public function timestamp_today() {
        return $this->time;
    }
    /**
     * Gets tomorrows timestamp for the calendar
     *
     * @return int tomorrow timestamp
     */
    public function timestamp_tomorrow() {
        return strtotime('+1 day', $this->time);
    }
    /**
     * Adds the pretend blocks for the calendar
     *
     * @param core_calendar_renderer $renderer
     * @param bool $showfilters display filters, false is set as default
     * @param string|null $view preference view options (eg: day, month, upcoming)
     */
    public function add_sidecalendar_blocks(core_calendar_renderer $renderer, $showfilters=false, $view=null) {
        if ($showfilters) {
            $filters = new block_contents();
            $filters->content = $renderer->event_filter();
            $filters->footer = '';
            $filters->title = get_string('eventskey', 'calendar');
            $renderer->add_pretend_calendar_block($filters, BLOCK_POS_RIGHT);
        }
        $block = new block_contents;
        $block->content = $renderer->fake_block_threemonths($this);
        $block->footer = '';
        $block->title = get_string('monthlyview', 'calendar');
        $renderer->add_pretend_calendar_block($block, BLOCK_POS_RIGHT);
    }
}

/**
 * Get calendar events.
 *
 * @param int $tstart Start time of time range for events
 * @param int $tend End time of time range for events
 * @param array|int|boolean $users array of users, user id or boolean for all/no user events
 * @param array|int|boolean $groups array of groups, group id or boolean for all/no group events
 * @param array|int|boolean $courses array of courses, course id or boolean for all/no course events
 * @param boolean $withduration whether only events starting within time range selected
 *                              or events in progress/already started selected as well
 * @param boolean $ignorehidden whether to select only visible events or all events
 * @param array|int|boolean $categories array of categories, category id or boolean for all/no course events
 * @return array $events of selected events or an empty array if there aren't any (or there was an error)
 */
function calendar_get_events($tstart, $tend, $users, $groups, $courses,
        $withduration = true, $ignorehidden = true, $categories = []) {
    global $DB;

    $whereclause = '';
    $params = array();
    // Quick test.
    if (empty($users) && empty($groups) && empty($courses) && empty($categories)) {
        return array();
    }

    if ((is_array($users) && !empty($users)) or is_numeric($users)) {
        // Events from a number of users
        if(!empty($whereclause)) $whereclause .= ' OR';
        list($insqlusers, $inparamsusers) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED);
        $whereclause .= " (e.userid $insqlusers AND e.courseid = 0 AND e.groupid = 0 AND e.categoryid = 0)";
        $params = array_merge($params, $inparamsusers);
    } else if($users === true) {
        // Events from ALL users
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (e.userid != 0 AND e.courseid = 0 AND e.groupid = 0 AND e.categoryid = 0)';
    } else if($users === false) {
        // No user at all, do nothing
    }

    if ((is_array($groups) && !empty($groups)) or is_numeric($groups)) {
        // Events from a number of groups
        if(!empty($whereclause)) $whereclause .= ' OR';
        list($insqlgroups, $inparamsgroups) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED);
        $whereclause .= " e.groupid $insqlgroups ";
        $params = array_merge($params, $inparamsgroups);
    } else if($groups === true) {
        // Events from ALL groups
        if(!empty($whereclause)) $whereclause .= ' OR ';
        $whereclause .= ' e.groupid != 0';
    }
    // boolean false (no groups at all): we don't need to do anything

    if ((is_array($courses) && !empty($courses)) or is_numeric($courses)) {
        if(!empty($whereclause)) $whereclause .= ' OR';
        list($insqlcourses, $inparamscourses) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $whereclause .= " (e.groupid = 0 AND e.courseid $insqlcourses)";
        $params = array_merge($params, $inparamscourses);
    } else if ($courses === true) {
        // Events from ALL courses
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (e.groupid = 0 AND e.courseid != 0)';
    }

    if ((is_array($categories) && !empty($categories)) || is_numeric($categories)) {
        if (!empty($whereclause)) {
            $whereclause .= ' OR';
        }
        list($insqlcategories, $inparamscategories) = $DB->get_in_or_equal($categories, SQL_PARAMS_NAMED);
        $whereclause .= " (e.groupid = 0 AND e.courseid = 0 AND e.categoryid $insqlcategories)";
        $params = array_merge($params, $inparamscategories);
    } else if ($categories === true) {
        // Events from ALL categories.
        if (!empty($whereclause)) {
            $whereclause .= ' OR';
        }
        $whereclause .= ' (e.groupid = 0 AND e.courseid = 0 AND e.categoryid != 0)';
    }

    // Security check: if, by now, we have NOTHING in $whereclause, then it means
    // that NO event-selecting clauses were defined. Thus, we won't be returning ANY
    // events no matter what. Allowing the code to proceed might return a completely
    // valid query with only time constraints, thus selecting ALL events in that time frame!
    if(empty($whereclause)) {
        return array();
    }

    if($withduration) {
        $timeclause = '(e.timestart >= '.$tstart.' OR e.timestart + e.timeduration > '.$tstart.') AND e.timestart <= '.$tend;
    }
    else {
        $timeclause = 'e.timestart >= '.$tstart.' AND e.timestart <= '.$tend;
    }
    if(!empty($whereclause)) {
        // We have additional constraints
        $whereclause = $timeclause.' AND ('.$whereclause.')';
    }
    else {
        // Just basic time filtering
        $whereclause = $timeclause;
    }

    if ($ignorehidden) {
        $whereclause .= ' AND e.visible = 1';
    }

    $sql = "SELECT e.*
              FROM {event} e
         LEFT JOIN {modules} m ON e.modulename = m.name
                -- Non visible modules will have a value of 0.
             WHERE (m.visible = 1 OR m.visible IS NULL) AND $whereclause
          ORDER BY e.timestart";
    $events = $DB->get_records_sql($sql, $params);

    if ($events === false) {
        $events = array();
    }
    return $events;
}

/**
 * Return the days of the week.
 *
 * @return array array of days
 */
function calendar_get_days() {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->get_weekdays();
}

/**
 * Get the subscription from a given id.
 *
 * @since Moodle 2.5
 * @param int $id id of the subscription
 * @return stdClass Subscription record from DB
 * @throws moodle_exception for an invalid id
 */
function calendar_get_subscription($id) {
    global $DB;

    $cache = \cache::make('core', 'calendar_subscriptions');
    $subscription = $cache->get($id);
    if (empty($subscription)) {
        $subscription = $DB->get_record('event_subscriptions', array('id' => $id), '*', MUST_EXIST);
        $cache->set($id, $subscription);
    }

    return $subscription;
}

/**
 * Gets the first day of the week.
 *
 * Used to be define('CALENDAR_STARTING_WEEKDAY', blah);
 *
 * @return int
 */
function calendar_get_starting_weekday() {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->get_starting_weekday();
}

/**
 * Get a HTML link to a course.
 *
 * @param int|stdClass $course the course id or course object
 * @return string a link to the course (as HTML); empty if the course id is invalid
 */
function calendar_get_courselink($course) {
    if (!$course) {
        return '';
    }

    if (!is_object($course)) {
        $course = calendar_get_course_cached($coursecache, $course);
    }
    $context = \context_course::instance($course->id);
    $fullname = format_string($course->fullname, true, array('context' => $context));
    $url = new \moodle_url('/course/view.php', array('id' => $course->id));
    $link = \html_writer::link($url, $fullname);

    return $link;
}

/**
 * Get current module cache.
 *
 * Only use this method if you do not know courseid. Otherwise use:
 * get_fast_modinfo($courseid)->instances[$modulename][$instance]
 *
 * @param array $modulecache in memory module cache
 * @param string $modulename name of the module
 * @param int $instance module instance number
 * @return stdClass|bool $module information
 */
function calendar_get_module_cached(&$modulecache, $modulename, $instance) {
    if (!isset($modulecache[$modulename . '_' . $instance])) {
        $modulecache[$modulename . '_' . $instance] = get_coursemodule_from_instance($modulename, $instance);
    }

    return $modulecache[$modulename . '_' . $instance];
}

/**
 * Get current course cache.
 *
 * @param array $coursecache list of course cache
 * @param int $courseid id of the course
 * @return stdClass $coursecache[$courseid] return the specific course cache
 */
function calendar_get_course_cached(&$coursecache, $courseid) {
    if (!isset($coursecache[$courseid])) {
        $coursecache[$courseid] = get_course($courseid);
    }
    return $coursecache[$courseid];
}

/**
 * Get group from groupid for calendar display
 *
 * @param int $groupid
 * @return stdClass group object with fields 'id', 'name' and 'courseid'
 */
function calendar_get_group_cached($groupid) {
    static $groupscache = array();
    if (!isset($groupscache[$groupid])) {
        $groupscache[$groupid] = groups_get_group($groupid, 'id,name,courseid');
    }
    return $groupscache[$groupid];
}

/**
 * Add calendar event metadata
 *
 * @param stdClass $event event info
 * @return stdClass $event metadata
 */
function calendar_add_event_metadata($event) {
    global $CFG, $OUTPUT;

    // Support multilang in event->name.
    $event->name = format_string($event->name, true);

    if (!empty($event->modulename)) { // Activity event.
        // The module name is set. I will assume that it has to be displayed, and
        // also that it is an automatically-generated event. And of course that the
        // instace id and modulename are set correctly.
        $instances = get_fast_modinfo($event->courseid)->get_instances_of($event->modulename);
        if (!array_key_exists($event->instance, $instances)) {
            return;
        }
        $module = $instances[$event->instance];

        $modulename = $module->get_module_type_name(false);
        if (get_string_manager()->string_exists($event->eventtype, $event->modulename)) {
            // Will be used as alt text if the event icon.
            $eventtype = get_string($event->eventtype, $event->modulename);
        } else {
            $eventtype = '';
        }

        $event->icon = '<img src="' . s($module->get_icon_url()) . '" alt="' . s($eventtype) .
            '" title="' . s($modulename) . '" class="icon" />';
        $event->referer = html_writer::link($module->url, $event->name);
        $event->courselink = calendar_get_courselink($module->get_course());
        $event->cmid = $module->id;
    } else if ($event->courseid == SITEID) { // Site event.
        $event->icon = '<img src="' . $OUTPUT->image_url('i/siteevent') . '" alt="' .
            get_string('globalevent', 'calendar') . '" class="icon" />';
        $event->cssclass = 'calendar_event_global';
    } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) { // Course event.
        $event->icon = '<img src="' . $OUTPUT->image_url('i/courseevent') . '" alt="' .
            get_string('courseevent', 'calendar') . '" class="icon" />';
        $event->courselink = calendar_get_courselink($event->courseid);
        $event->cssclass = 'calendar_event_course';
    } else if ($event->groupid) { // Group event.
        if ($group = calendar_get_group_cached($event->groupid)) {
            $groupname = format_string($group->name, true, \context_course::instance($group->courseid));
        } else {
            $groupname = '';
        }
        $event->icon = \html_writer::empty_tag('image', array('src' => $OUTPUT->image_url('i/groupevent'),
            'alt' => get_string('groupevent', 'calendar'), 'title' => $groupname, 'class' => 'icon'));
        $event->courselink = calendar_get_courselink($event->courseid) . ', ' . $groupname;
        $event->cssclass = 'calendar_event_group';
    } else if ($event->userid) { // User event.
        $event->icon = '<img src="' . $OUTPUT->image_url('i/userevent') . '" alt="' .
            get_string('userevent', 'calendar') . '" class="icon" />';
        $event->cssclass = 'calendar_event_user';
    }

    return $event;
}

/**
 * Get calendar events by id.
 *
 * @since Moodle 2.5
 * @param array $eventids list of event ids
 * @return array Array of event entries, empty array if nothing found
 */
function calendar_get_events_by_id($eventids) {
    global $DB;

    if (!is_array($eventids) || empty($eventids)) {
        return array();
    }

    list($wheresql, $params) = $DB->get_in_or_equal($eventids);
    $wheresql = "id $wheresql";

    return $DB->get_records_select('event', $wheresql, $params);
}

/**
 * Get control options for calendar.
 *
 * @param string $type of calendar
 * @param array $data calendar information
 * @return string $content return available control for the calender in html
 */
function calendar_top_controls($type, $data) {
    global $PAGE, $OUTPUT;

    // Get the calendar type we are using.
    $calendartype = \core_calendar\type_factory::get_calendar_instance();

    $content = '';

    // Ensure course id passed if relevant.
    $courseid = '';
    if (!empty($data['id'])) {
        $courseid = '&amp;course=' . $data['id'];
    }

    // If we are passing a month and year then we need to convert this to a timestamp to
    // support multiple calendars. No where in core should these be passed, this logic
    // here is for third party plugins that may use this function.
    if (!empty($data['m']) && !empty($date['y'])) {
        if (!isset($data['d'])) {
            $data['d'] = 1;
        }
        if (!checkdate($data['m'], $data['d'], $data['y'])) {
            $time = time();
        } else {
            $time = make_timestamp($data['y'], $data['m'], $data['d']);
        }
    } else if (!empty($data['time'])) {
        $time = $data['time'];
    } else {
        $time = time();
    }

    // Get the date for the calendar type.
    $date = $calendartype->timestamp_to_date_array($time);

    $urlbase = $PAGE->url;

    // We need to get the previous and next months in certain cases.
    if ($type == 'frontpage' || $type == 'course' || $type == 'month') {
        $prevmonth = calendar_sub_month($date['mon'], $date['year']);
        $prevmonthtime = $calendartype->convert_to_gregorian($prevmonth[1], $prevmonth[0], 1);
        $prevmonthtime = make_timestamp($prevmonthtime['year'], $prevmonthtime['month'], $prevmonthtime['day'],
            $prevmonthtime['hour'], $prevmonthtime['minute']);

        $nextmonth = calendar_add_month($date['mon'], $date['year']);
        $nextmonthtime = $calendartype->convert_to_gregorian($nextmonth[1], $nextmonth[0], 1);
        $nextmonthtime = make_timestamp($nextmonthtime['year'], $nextmonthtime['month'], $nextmonthtime['day'],
            $nextmonthtime['hour'], $nextmonthtime['minute']);
    }

    switch ($type) {
        case 'frontpage':
            $prevlink = calendar_get_link_previous(get_string('monthprev', 'access'), $urlbase, false, false, false,
                true, $prevmonthtime);
            $nextlink = calendar_get_link_next(get_string('monthnext', 'access'), $urlbase, false, false, false, true,
                $nextmonthtime);
            $calendarlink = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'month')),
                false, false, false, $time);

            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }

            $right = $nextlink;

            $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
            $content .= $prevlink . '<span class="hide"> | </span>';
            $content .= \html_writer::tag('span', \html_writer::link($calendarlink,
                userdate($time, get_string('strftimemonthyear')), array('title' => get_string('monththis', 'calendar'))
            ), array('class' => 'current'));
            $content .= '<span class="hide"> | </span>' . $right;
            $content .= "<span class=\"clearer\"><!-- --></span>\n";
            $content .= \html_writer::end_tag('div');

            break;
        case 'course':
            $prevlink = calendar_get_link_previous(get_string('monthprev', 'access'), $urlbase, false, false, false,
                true, $prevmonthtime);
            $nextlink = calendar_get_link_next(get_string('monthnext', 'access'), $urlbase, false, false, false,
                true, $nextmonthtime);
            $calendarlink = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'month')),
                false, false, false, $time);

            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }

            $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
            $content .= $prevlink . '<span class="hide"> | </span>';
            $content .= \html_writer::tag('span', \html_writer::link($calendarlink,
                userdate($time, get_string('strftimemonthyear')), array('title' => get_string('monththis', 'calendar'))
            ), array('class' => 'current'));
            $content .= '<span class="hide"> | </span>' . $nextlink;
            $content .= "<span class=\"clearer\"><!-- --></span>";
            $content .= \html_writer::end_tag('div');
            break;
        case 'upcoming':
            $calendarlink = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'upcoming')),
                false, false, false, $time);
            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }
            $calendarlink = \html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')));
            $content .= \html_writer::tag('div', $calendarlink, array('class' => 'centered'));
            break;
        case 'display':
            $calendarlink = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'month')),
                false, false, false, $time);
            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }
            $calendarlink = \html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')));
            $content .= \html_writer::tag('h3', $calendarlink);
            break;
        case 'month':
            $prevlink = calendar_get_link_previous(userdate($prevmonthtime, get_string('strftimemonthyear')),
                'view.php?view=month' . $courseid . '&amp;', false, false, false, false, $prevmonthtime);
            $nextlink = calendar_get_link_next(userdate($nextmonthtime, get_string('strftimemonthyear')),
                'view.php?view=month' . $courseid . '&amp;', false, false, false, false, $nextmonthtime);

            $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
            $content .= $prevlink . '<span class="hide"> | </span>';
            $content .= $OUTPUT->heading(userdate($time, get_string('strftimemonthyear')), 2, 'current');
            $content .= '<span class="hide"> | </span>' . $nextlink;
            $content .= '<span class="clearer"><!-- --></span>';
            $content .= \html_writer::end_tag('div')."\n";
            break;
        case 'day':
            $days = calendar_get_days();

            $prevtimestamp = strtotime('-1 day', $time);
            $nexttimestamp = strtotime('+1 day', $time);

            $prevdate = $calendartype->timestamp_to_date_array($prevtimestamp);
            $nextdate = $calendartype->timestamp_to_date_array($nexttimestamp);

            $prevname = $days[$prevdate['wday']]['fullname'];
            $nextname = $days[$nextdate['wday']]['fullname'];
            $prevlink = calendar_get_link_previous($prevname, 'view.php?view=day' . $courseid . '&amp;', false, false,
                false, false, $prevtimestamp);
            $nextlink = calendar_get_link_next($nextname, 'view.php?view=day' . $courseid . '&amp;', false, false, false,
                false, $nexttimestamp);

            $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
            $content .= $prevlink;
            $content .= '<span class="hide"> | </span><span class="current">' .userdate($time,
                    get_string('strftimedaydate')) . '</span>';
            $content .= '<span class="hide"> | </span>' . $nextlink;
            $content .= "<span class=\"clearer\"><!-- --></span>";
            $content .= \html_writer::end_tag('div') . "\n";

            break;
    }

    return $content;
}

/**
 * Return the representation day.
 *
 * @param int $tstamp Timestamp in GMT
 * @param int|bool $now current Unix timestamp
 * @param bool $usecommonwords
 * @return string the formatted date/time
 */
function calendar_day_representation($tstamp, $now = false, $usecommonwords = true) {
    static $shortformat;

    if (empty($shortformat)) {
        $shortformat = get_string('strftimedayshort');
    }

    if ($now === false) {
        $now = time();
    }

    // To have it in one place, if a change is needed.
    $formal = userdate($tstamp, $shortformat);

    $datestamp = usergetdate($tstamp);
    $datenow = usergetdate($now);

    if ($usecommonwords == false) {
        // We don't want words, just a date.
        return $formal;
    } else if ($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday']) {
        return get_string('today', 'calendar');
    } else if (($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday'] - 1 ) ||
        ($datestamp['year'] == $datenow['year'] - 1 && $datestamp['mday'] == 31 && $datestamp['mon'] == 12
            && $datenow['yday'] == 1)) {
        return get_string('yesterday', 'calendar');
    } else if (($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday'] + 1 ) ||
        ($datestamp['year'] == $datenow['year'] + 1 && $datenow['mday'] == 31 && $datenow['mon'] == 12
            && $datestamp['yday'] == 1)) {
        return get_string('tomorrow', 'calendar');
    } else {
        return $formal;
    }
}

/**
 * return the formatted representation time.
 *

 * @param int $time the timestamp in UTC, as obtained from the database
 * @return string the formatted date/time
 */
function calendar_time_representation($time) {
    static $langtimeformat = null;

    if ($langtimeformat === null) {
        $langtimeformat = get_string('strftimetime');
    }

    $timeformat = get_user_preferences('calendar_timeformat');
    if (empty($timeformat)) {
        $timeformat = get_config(null, 'calendar_site_timeformat');
    }

    // Allow language customization of selected time format.
    if ($timeformat === CALENDAR_TF_12) {
        $timeformat = get_string('strftimetime12', 'langconfig');
    } else if ($timeformat === CALENDAR_TF_24) {
        $timeformat = get_string('strftimetime24', 'langconfig');
    }

    return userdate($time, empty($timeformat) ? $langtimeformat : $timeformat);
}

/**
 * Adds day, month, year arguments to a URL and returns a moodle_url object.
 *
 * @param string|moodle_url $linkbase
 * @param int $d The number of the day.
 * @param int $m The number of the month.
 * @param int $y The number of the year.
 * @param int $time the unixtime, used for multiple calendar support. The values $d,
 *     $m and $y are kept for backwards compatibility.
 * @return moodle_url|null $linkbase
 */
function calendar_get_link_href($linkbase, $d, $m, $y, $time = 0) {
    if (empty($linkbase)) {
        return null;
    }

    if (!($linkbase instanceof \moodle_url)) {
        $linkbase = new \moodle_url($linkbase);
    }

    $linkbase->param('time', calendar_get_timestamp($d, $m, $y, $time));

    return $linkbase;
}

/**
 * Build and return a previous month HTML link, with an arrow.
 *
 * @param string $text The text label.
 * @param string|moodle_url $linkbase The URL stub.
 * @param int $d The number of the date.
 * @param int $m The number of the month.
 * @param int $y year The number of the year.
 * @param bool $accesshide Default visible, or hide from all except screenreaders.
 * @param int $time the unixtime, used for multiple calendar support. The values $d,
 *     $m and $y are kept for backwards compatibility.
 * @return string HTML string.
 */
function calendar_get_link_previous($text, $linkbase, $d, $m, $y, $accesshide = false, $time = 0) {
    $href = calendar_get_link_href(new \moodle_url($linkbase), $d, $m, $y, $time);

    if (empty($href)) {
        return $text;
    }

    $attrs = [
        'data-time' => calendar_get_timestamp($d, $m, $y, $time),
        'data-drop-zone' => 'nav-link',
    ];

    return link_arrow_left($text, $href->out(false), $accesshide, 'previous', $attrs);
}

/**
 * Build and return a next month HTML link, with an arrow.
 *
 * @param string $text The text label.
 * @param string|moodle_url $linkbase The URL stub.
 * @param int $d the number of the Day
 * @param int $m The number of the month.
 * @param int $y The number of the year.
 * @param bool $accesshide Default visible, or hide from all except screenreaders.
 * @param int $time the unixtime, used for multiple calendar support. The values $d,
 *     $m and $y are kept for backwards compatibility.
 * @return string HTML string.
 */
function calendar_get_link_next($text, $linkbase, $d, $m, $y, $accesshide = false, $time = 0) {
    $href = calendar_get_link_href(new \moodle_url($linkbase), $d, $m, $y, $time);

    if (empty($href)) {
        return $text;
    }

    $attrs = [
        'data-time' => calendar_get_timestamp($d, $m, $y, $time),
        'data-drop-zone' => 'nav-link',
    ];

    return link_arrow_right($text, $href->out(false), $accesshide, 'next', $attrs);
}

/**
 * Return the number of days in month.
 *
 * @param int $month the number of the month.
 * @param int $year the number of the year
 * @return int
 */
function calendar_days_in_month($month, $year) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->get_num_days_in_month($year, $month);
}

/**
 * Get the next following month.
 *
 * @param int $month the number of the month.
 * @param int $year the number of the year.
 * @return array the following month
 */
function calendar_add_month($month, $year) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->get_next_month($year, $month);
}

/**
 * Get the previous month.
 *
 * @param int $month the number of the month.
 * @param int $year the number of the year.
 * @return array previous month
 */
function calendar_sub_month($month, $year) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->get_prev_month($year, $month);
}

/**
 * Get per-day basis events
 *
 * @param array $events list of events
 * @param int $month the number of the month
 * @param int $year the number of the year
 * @param array $eventsbyday event on specific day
 * @param array $durationbyday duration of the event in days
 * @param array $typesbyday event type (eg: global, course, user, or group)
 * @param array $courses list of courses
 * @return void
 */
function calendar_events_by_day($events, $month, $year, &$eventsbyday, &$durationbyday, &$typesbyday, &$courses) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();

    $eventsbyday = array();
    $typesbyday = array();
    $durationbyday = array();

    if ($events === false) {
        return;
    }

    foreach ($events as $event) {
        $startdate = $calendartype->timestamp_to_date_array($event->timestart);
        if ($event->timeduration) {
            $enddate = $calendartype->timestamp_to_date_array($event->timestart + $event->timeduration - 1);
        } else {
            $enddate = $startdate;
        }

        // Simple arithmetic: $year * 13 + $month is a distinct integer for each distinct ($year, $month) pair.
        if (!($startdate['year'] * 13 + $startdate['mon'] <= $year * 13 + $month) &&
            ($enddate['year'] * 13 + $enddate['mon'] >= $year * 13 + $month)) {
            continue;
        }

        $eventdaystart = intval($startdate['mday']);

        if ($startdate['mon'] == $month && $startdate['year'] == $year) {
            // Give the event to its day.
            $eventsbyday[$eventdaystart][] = $event->id;

            // Mark the day as having such an event.
            if ($event->courseid == SITEID && $event->groupid == 0) {
                $typesbyday[$eventdaystart]['startglobal'] = true;
                // Set event class for global event.
                $events[$event->id]->class = 'calendar_event_global';
            } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                $typesbyday[$eventdaystart]['startcourse'] = true;
                // Set event class for course event.
                $events[$event->id]->class = 'calendar_event_course';
            } else if ($event->groupid) {
                $typesbyday[$eventdaystart]['startgroup'] = true;
                // Set event class for group event.
                $events[$event->id]->class = 'calendar_event_group';
            } else if ($event->userid) {
                $typesbyday[$eventdaystart]['startuser'] = true;
                // Set event class for user event.
                $events[$event->id]->class = 'calendar_event_user';
            }
        }

        if ($event->timeduration == 0) {
            // Proceed with the next.
            continue;
        }

        // The event starts on $month $year or before.
        if ($startdate['mon'] == $month && $startdate['year'] == $year) {
            $lowerbound = intval($startdate['mday']);
        } else {
            $lowerbound = 0;
        }

        // Also, it ends on $month $year or later.
        if ($enddate['mon'] == $month && $enddate['year'] == $year) {
            $upperbound = intval($enddate['mday']);
        } else {
            $upperbound = calendar_days_in_month($month, $year);
        }

        // Mark all days between $lowerbound and $upperbound (inclusive) as duration.
        for ($i = $lowerbound + 1; $i <= $upperbound; ++$i) {
            $durationbyday[$i][] = $event->id;
            if ($event->courseid == SITEID && $event->groupid == 0) {
                $typesbyday[$i]['durationglobal'] = true;
            } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                $typesbyday[$i]['durationcourse'] = true;
            } else if ($event->groupid) {
                $typesbyday[$i]['durationgroup'] = true;
            } else if ($event->userid) {
                $typesbyday[$i]['durationuser'] = true;
            }
        }

    }
    return;
}

/**
 * Returns the courses to load events for.
 *
 * @param array $courseeventsfrom An array of courses to load calendar events for
 * @param bool $ignorefilters specify the use of filters, false is set as default
 * @param stdClass $user The user object. This defaults to the global $USER object.
 * @return array An array of courses, groups, and user to load calendar events for based upon filters
 */
function calendar_set_filters(array $courseeventsfrom, $ignorefilters = false, stdClass $user = null) {
    global $CFG, $USER;

    if (is_null($user)) {
        $user = $USER;
    }

    $courses = array();
    $userid = false;
    $group = false;

    // Get the capabilities that allow seeing group events from all groups.
    $allgroupscaps = array('moodle/site:accessallgroups', 'moodle/calendar:manageentries');

    $isvaliduser = !empty($user->id);

    if ($ignorefilters || calendar_show_event_type(CALENDAR_EVENT_COURSE, $user)) {
        $courses = array_keys($courseeventsfrom);
    }
    if ($ignorefilters || calendar_show_event_type(CALENDAR_EVENT_GLOBAL, $user)) {
        $courses[] = SITEID;
    }
    $courses = array_unique($courses);
    sort($courses);

    if (!empty($courses) && in_array(SITEID, $courses)) {
        // Sort courses for consistent colour highlighting.
        // Effectively ignoring SITEID as setting as last course id.
        $key = array_search(SITEID, $courses);
        unset($courses[$key]);
        $courses[] = SITEID;
    }

    if ($ignorefilters || ($isvaliduser && calendar_show_event_type(CALENDAR_EVENT_USER, $user))) {
        $userid = $user->id;
    }

    if (!empty($courseeventsfrom) && (calendar_show_event_type(CALENDAR_EVENT_GROUP, $user) || $ignorefilters)) {

        if (count($courseeventsfrom) == 1) {
            $course = reset($courseeventsfrom);
            if (has_any_capability($allgroupscaps, \context_course::instance($course->id))) {
                $coursegroups = groups_get_all_groups($course->id, 0, 0, 'g.id');
                $group = array_keys($coursegroups);
            }
        }
        if ($group === false) {
            if (!empty($CFG->calendar_adminseesall) && has_any_capability($allgroupscaps, \context_system::instance())) {
                $group = true;
            } else if ($isvaliduser) {
                $groupids = array();
                foreach ($courseeventsfrom as $courseid => $course) {
                    // If the user is an editing teacher in there.
                    if (!empty($user->groupmember[$course->id])) {
                        // We've already cached the users groups for this course so we can just use that.
                        $groupids = array_merge($groupids, $user->groupmember[$course->id]);
                    } else if ($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
                        // If this course has groups, show events from all of those related to the current user.
                        $coursegroups = groups_get_user_groups($course->id, $user->id);
                        $groupids = array_merge($groupids, $coursegroups['0']);
                    }
                }
                if (!empty($groupids)) {
                    $group = $groupids;
                }
            }
        }
    }
    if (empty($courses)) {
        $courses = false;
    }

    return array($courses, $group, $userid);
}

/**
 * Return the capability for viewing a calendar event.
 *
 * @param calendar_event $event event object
 * @return boolean
 */
function calendar_view_event_allowed(calendar_event $event) {
    global $USER;

    // Anyone can see site events.
    if ($event->courseid && $event->courseid == SITEID) {
        return true;
    }

    // If a user can manage events at the site level they can see any event.
    $sitecontext = \context_system::instance();
    // If user has manageentries at site level, return true.
    if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
        return true;
    }

    if (!empty($event->groupid)) {
        // If it is a group event we need to be able to manage events in the course, or be in the group.
        if (has_capability('moodle/calendar:manageentries', $event->context) ||
                has_capability('moodle/calendar:managegroupentries', $event->context)) {
            return true;
        }

        $mycourses = enrol_get_my_courses('id');
        return isset($mycourses[$event->courseid]) && groups_is_member($event->groupid);
    } else if ($event->modulename) {
        // If this is a module event we need to be able to see the module.
        $coursemodules = [];
        $courseid = 0;
        // Override events do not have the courseid set.
        if ($event->courseid) {
            $courseid = $event->courseid;
            $coursemodules = get_fast_modinfo($event->courseid)->instances;
        } else {
            $cmraw = get_coursemodule_from_instance($event->modulename, $event->instance, 0, false, MUST_EXIST);
            $courseid = $cmraw->course;
            $coursemodules = get_fast_modinfo($cmraw->course)->instances;
        }
        $hasmodule = isset($coursemodules[$event->modulename]);
        $hasinstance = isset($coursemodules[$event->modulename][$event->instance]);

        // If modinfo doesn't know about the module, return false to be safe.
        if (!$hasmodule || !$hasinstance) {
            return false;
        }

        // Must be able to see the course and the module - MDL-59304.
        $cm = $coursemodules[$event->modulename][$event->instance];
        if (!$cm->uservisible) {
            return false;
        }
        $mycourses = enrol_get_my_courses('id');
        return isset($mycourses[$courseid]);
    } else if ($event->categoryid) {
        // If this is a category we need to be able to see the category.
        $cat = \core_course_category::get($event->categoryid, IGNORE_MISSING);
        if (!$cat) {
            return false;
        }
        return true;
    } else if (!empty($event->courseid)) {
        // If it is a course event we need to be able to manage events in the course, or be in the course.
        if (has_capability('moodle/calendar:manageentries', $event->context)) {
            return true;
        }
        $mycourses = enrol_get_my_courses('id');
        return isset($mycourses[$event->courseid]);
    } else if ($event->userid) {
        if ($event->userid != $USER->id) {
            // No-one can ever see another users events.
            return false;
        }
        return true;
    } else {
        throw new moodle_exception('unknown event type');
    }

    return false;
}

/**
 * Return the capability for editing calendar event.
 *
 * @param calendar_event $event event object
 * @param bool $manualedit is the event being edited manually by the user
 * @return bool capability to edit event
 */
function calendar_edit_event_allowed($event, $manualedit = false) {
    global $USER, $DB;

    // Must be logged in.
    if (!isloggedin()) {
        return false;
    }

    // Can not be using guest account.
    if (isguestuser()) {
        return false;
    }

    if ($manualedit && !empty($event->modulename)) {
        $hascallback = component_callback_exists(
            'mod_' . $event->modulename,
            'core_calendar_event_timestart_updated'
        );

        if (!$hascallback) {
            // If the activity hasn't implemented the correct callback
            // to handle changes to it's events then don't allow any
            // manual changes to them.
            return false;
        }

        $coursemodules = get_fast_modinfo($event->courseid)->instances;
        $hasmodule = isset($coursemodules[$event->modulename]);
        $hasinstance = isset($coursemodules[$event->modulename][$event->instance]);

        // If modinfo doesn't know about the module, return false to be safe.
        if (!$hasmodule || !$hasinstance) {
            return false;
        }

        $coursemodule = $coursemodules[$event->modulename][$event->instance];
        $context = context_module::instance($coursemodule->id);
        // This is the capability that allows a user to modify the activity
        // settings. Since the activity generated this event we need to check
        // that the current user has the same capability before allowing them
        // to update the event because the changes to the event will be
        // reflected within the activity.
        return has_capability('moodle/course:manageactivities', $context);
    }

    // You cannot edit URL based calendar subscription events presently.
    if (!empty($event->subscriptionid)) {
        if (!empty($event->subscription->url)) {
            // This event can be updated externally, so it cannot be edited.
            return false;
        }
    }

    $sitecontext = \context_system::instance();

    // If user has manageentries at site level, return true.
    if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
        return true;
    }

    // If groupid is set, it's definitely a group event.
    if (!empty($event->groupid)) {
        // Allow users to add/edit group events if -
        // 1) They have manageentries for the course OR
        // 2) They have managegroupentries AND are in the group.
        $group = $DB->get_record('groups', array('id' => $event->groupid));
        return $group && (
                has_capability('moodle/calendar:manageentries', $event->context) ||
                (has_capability('moodle/calendar:managegroupentries', $event->context)
                    && groups_is_member($event->groupid)));
    } else if (!empty($event->courseid)) {
        // If groupid is not set, but course is set, it's definitely a course event.
        return has_capability('moodle/calendar:manageentries', $event->context);
    } else if (!empty($event->categoryid)) {
        // If groupid is not set, but category is set, it's definitely a category event.
        return has_capability('moodle/calendar:manageentries', $event->context);
    } else if (!empty($event->userid) && $event->userid == $USER->id) {
        // If course is not set, but userid id set, it's a user event.
        return (has_capability('moodle/calendar:manageownentries', $event->context));
    } else if (!empty($event->userid)) {
        return (has_capability('moodle/calendar:manageentries', $event->context));
    }

    return false;
}

/**
 * Return the capability for deleting a calendar event.
 *
 * @param calendar_event $event The event object
 * @return bool Whether the user has permission to delete the event or not.
 */
function calendar_delete_event_allowed($event) {
    // Only allow delete if you have capabilities and it is not an module event.
    return (calendar_edit_event_allowed($event) && empty($event->modulename));
}

/**
 * Returns the default courses to display on the calendar when there isn't a specific
 * course to display.
 *
 * @param int $courseid (optional) If passed, an additional course can be returned for admins (the current course).
 * @param string $fields Comma separated list of course fields to return.
 * @param bool $canmanage If true, this will return the list of courses the user can create events in, rather
 *                        than the list of courses they see events from (an admin can always add events in a course
 *                        calendar, even if they are not enrolled in the course).
 * @param int $userid (optional) The user which this function returns the default courses for.
 *                        By default the current user.
 * @return array $courses Array of courses to display
 */
function calendar_get_default_courses($courseid = null, $fields = '*', $canmanage = false, int $userid = null) {
    global $CFG, $USER;

    if (!$userid) {
        if (!isloggedin()) {
            return array();
        }
        $userid = $USER->id;
    }

    if ((!empty($CFG->calendar_adminseesall) || $canmanage) &&
            has_capability('moodle/calendar:manageentries', context_system::instance(), $userid)) {

        // Add a c. prefix to every field as expected by get_courses function.
        $fieldlist = explode(',', $fields);

        $prefixedfields = array_map(function($value) {
            return 'c.' . trim(strtolower($value));
        }, $fieldlist);
        if (!in_array('c.visible', $prefixedfields) && !in_array('c.*', $prefixedfields)) {
            $prefixedfields[] = 'c.visible';
        }
        $courses = get_courses('all', 'c.shortname', implode(',', $prefixedfields));
    } else {
        $courses = enrol_get_users_courses($userid, true, $fields);
    }

    if ($courseid && $courseid != SITEID) {
        if (empty($courses[$courseid]) && has_capability('moodle/calendar:manageentries', context_system::instance(), $userid)) {
            // Allow a site admin to see calendars from courses he is not enrolled in.
            // This will come from $COURSE.
            $courses[$courseid] = get_course($courseid);
        }
    }

    return $courses;
}

/**
 * Get event format time.
 *
 * @param calendar_event $event event object
 * @param int $now current time in gmt
 * @param array $linkparams list of params for event link
 * @param bool $usecommonwords the words as formatted date/time.
 * @param int $showtime determine the show time GMT timestamp
 * @return string $eventtime link/string for event time
 */
function calendar_format_event_time($event, $now, $linkparams = null, $usecommonwords = true, $showtime = 0) {
    $starttime = $event->timestart;
    $endtime = $event->timestart + $event->timeduration;

    if (empty($linkparams) || !is_array($linkparams)) {
        $linkparams = array();
    }

    $linkparams['view'] = 'day';

    // OK, now to get a meaningful display.
    // Check if there is a duration for this event.
    if ($event->timeduration) {
        // Get the midnight of the day the event will start.
        $usermidnightstart = usergetmidnight($starttime);
        // Get the midnight of the day the event will end.
        $usermidnightend = usergetmidnight($endtime);
        // Check if we will still be on the same day.
        if ($usermidnightstart == $usermidnightend) {
            // Check if we are running all day.
            if ($event->timeduration == DAYSECS) {
                $time = get_string('allday', 'calendar');
            } else { // Specify the time we will be running this from.
                $datestart = calendar_time_representation($starttime);
                $dateend = calendar_time_representation($endtime);
                $time = $datestart . ' <strong>&raquo;</strong> ' . $dateend;
            }

            // Set printable representation.
            if (!$showtime) {
                $day = calendar_day_representation($event->timestart, $now, $usecommonwords);
                $url = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams), 0, 0, 0, $endtime);
                $eventtime = \html_writer::link($url, $day) . ', ' . $time;
            } else {
                $eventtime = $time;
            }
        } else { // It must spans two or more days.
            $daystart = calendar_day_representation($event->timestart, $now, $usecommonwords) . ', ';
            if ($showtime == $usermidnightstart) {
                $daystart = '';
            }
            $timestart = calendar_time_representation($event->timestart);
            $dayend = calendar_day_representation($event->timestart + $event->timeduration, $now, $usecommonwords) . ', ';
            if ($showtime == $usermidnightend) {
                $dayend = '';
            }
            $timeend = calendar_time_representation($event->timestart + $event->timeduration);

            // Set printable representation.
            if ($now >= $usermidnightstart && $now < strtotime('+1 day', $usermidnightstart)) {
                $url = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams), 0, 0, 0, $endtime);
                $eventtime = $timestart . ' <strong>&raquo;</strong> ' . \html_writer::link($url, $dayend) . $timeend;
            } else {
                // The event is in the future, print start and end links.
                $url = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams), 0, 0, 0, $starttime);
                $eventtime = \html_writer::link($url, $daystart) . $timestart . ' <strong>&raquo;</strong> ';

                $url = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams),  0, 0, 0, $endtime);
                $eventtime .= \html_writer::link($url, $dayend) . $timeend;
            }
        }
    } else { // There is no time duration.
        $time = calendar_time_representation($event->timestart);
        // Set printable representation.
        if (!$showtime) {
            $day = calendar_day_representation($event->timestart, $now, $usecommonwords);
            $url = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams),  0, 0, 0, $starttime);
            $eventtime = \html_writer::link($url, $day) . ', ' . trim($time);
        } else {
            $eventtime = $time;
        }
    }

    // Check if It has expired.
    if ($event->timestart + $event->timeduration < $now) {
        $eventtime = '<span class="dimmed_text">' . str_replace(' href=', ' class="dimmed" href=', $eventtime) . '</span>';
    }

    return $eventtime;
}

/**
 * Checks to see if the requested type of event should be shown for the given user.
 *
 * @param int $type The type to check the display for (default is to display all)
 * @param stdClass|int|null $user The user to check for - by default the current user
 * @return bool True if the tyep should be displayed false otherwise
 */
function calendar_show_event_type($type, $user = null) {
    $default = CALENDAR_EVENT_GLOBAL + CALENDAR_EVENT_COURSE + CALENDAR_EVENT_GROUP + CALENDAR_EVENT_USER;

    if (get_user_preferences('calendar_persistflt', 0, $user) === 0) {
        global $SESSION;
        if (!isset($SESSION->calendarshoweventtype)) {
            $SESSION->calendarshoweventtype = $default;
        }
        return $SESSION->calendarshoweventtype & $type;
    } else {
        return get_user_preferences('calendar_savedflt', $default, $user) & $type;
    }
}

/**
 * Sets the display of the event type given $display.
 *
 * If $display = true the event type will be shown.
 * If $display = false the event type will NOT be shown.
 * If $display = null the current value will be toggled and saved.
 *
 * @param int $type object of CALENDAR_EVENT_XXX
 * @param bool $display option to display event type
 * @param stdClass|int $user moodle user object or id, null means current user
 */
function calendar_set_event_type_display($type, $display = null, $user = null) {
    $persist = get_user_preferences('calendar_persistflt', 0, $user);
    $default = CALENDAR_EVENT_GLOBAL + CALENDAR_EVENT_COURSE + CALENDAR_EVENT_GROUP
            + CALENDAR_EVENT_USER + CALENDAR_EVENT_COURSECAT;
    if ($persist === 0) {
        global $SESSION;
        if (!isset($SESSION->calendarshoweventtype)) {
            $SESSION->calendarshoweventtype = $default;
        }
        $preference = $SESSION->calendarshoweventtype;
    } else {
        $preference = get_user_preferences('calendar_savedflt', $default, $user);
    }
    $current = $preference & $type;
    if ($display === null) {
        $display = !$current;
    }
    if ($display && !$current) {
        $preference += $type;
    } else if (!$display && $current) {
        $preference -= $type;
    }
    if ($persist === 0) {
        $SESSION->calendarshoweventtype = $preference;
    } else {
        if ($preference == $default) {
            unset_user_preference('calendar_savedflt', $user);
        } else {
            set_user_preference('calendar_savedflt', $preference, $user);
        }
    }
}

/**
 * Get calendar's allowed types.
 *
 * @param stdClass $allowed list of allowed edit for event  type
 * @param stdClass|int $course object of a course or course id
 * @param array $groups array of groups for the given course
 * @param stdClass|int $category object of a category
 */
function calendar_get_allowed_types(&$allowed, $course = null, $groups = null, $category = null) {
    global $USER, $DB;

    $allowed = new \stdClass();
    $allowed->user = has_capability('moodle/calendar:manageownentries', \context_system::instance());
    $allowed->groups = false;
    $allowed->courses = false;
    $allowed->categories = false;
    $allowed->site = has_capability('moodle/calendar:manageentries', \context_course::instance(SITEID));
    $getgroupsfunc = function($course, $context, $user) use ($groups) {
        if ($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
            if (has_capability('moodle/site:accessallgroups', $context)) {
                return is_null($groups) ? groups_get_all_groups($course->id) : $groups;
            } else {
                if (is_null($groups)) {
                    return groups_get_all_groups($course->id, $user->id);
                } else {
                    return array_filter($groups, function($group) use ($user) {
                        return isset($group->members[$user->id]);
                    });
                }
            }
        }

        return false;
    };

    if (!empty($course)) {
        if (!is_object($course)) {
            $course = $DB->get_record('course', array('id' => $course), 'id, groupmode, groupmodeforce', MUST_EXIST);
        }
        if ($course->id != SITEID) {
            $coursecontext = \context_course::instance($course->id);
            $allowed->user = has_capability('moodle/calendar:manageownentries', $coursecontext);

            if (has_capability('moodle/calendar:manageentries', $coursecontext)) {
                $allowed->courses = array($course->id => 1);
                $allowed->groups = $getgroupsfunc($course, $coursecontext, $USER);
            } else if (has_capability('moodle/calendar:managegroupentries', $coursecontext)) {
                $allowed->groups = $getgroupsfunc($course, $coursecontext, $USER);
            }
        }
    }

    if (!empty($category)) {
        $catcontext = \context_coursecat::instance($category->id);
        if (has_capability('moodle/category:manage', $catcontext)) {
            $allowed->categories = [$category->id => 1];
        }
    }
}

/**
 * See if user can add calendar entries at all used to print the "New Event" button.
 *
 * @param stdClass $course object of a course or course id
 * @return bool has the capability to add at least one event type
 */
function calendar_user_can_add_event($course) {
    if (!isloggedin() || isguestuser()) {
        return false;
    }

    calendar_get_allowed_types($allowed, $course);

    return (bool)($allowed->user || $allowed->groups || $allowed->courses || $allowed->categories || $allowed->site);
}

/**
 * Check wether the current user is permitted to add events.
 *
 * @param stdClass $event object of event
 * @return bool has the capability to add event
 */
function calendar_add_event_allowed($event) {
    global $USER, $DB;

    // Can not be using guest account.
    if (!isloggedin() or isguestuser()) {
        return false;
    }

    $sitecontext = \context_system::instance();

    // If user has manageentries at site level, always return true.
    if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
        return true;
    }

    switch ($event->eventtype) {
        case 'category':
            return has_capability('moodle/category:manage', $event->context);
        case 'course':
            return has_capability('moodle/calendar:manageentries', $event->context);
        case 'group':
            // Allow users to add/edit group events if -
            // 1) They have manageentries (= entries for whole course).
            // 2) They have managegroupentries AND are in the group.
            $group = $DB->get_record('groups', array('id' => $event->groupid));
            return $group && (
                    has_capability('moodle/calendar:manageentries', $event->context) ||
                    (has_capability('moodle/calendar:managegroupentries', $event->context)
                        && groups_is_member($event->groupid)));
        case 'user':
            if ($event->userid == $USER->id) {
                return (has_capability('moodle/calendar:manageownentries', $event->context));
            }
        // There is intentionally no 'break'.
        case 'site':
            return has_capability('moodle/calendar:manageentries', $event->context);
        default:
            return has_capability('moodle/calendar:manageentries', $event->context);
    }
}

/**
 * Returns option list for the poll interval setting.
 *
 * @return array An array of poll interval options. Interval => description.
 */
function calendar_get_pollinterval_choices() {
    return array(
        '0' => new \lang_string('never', 'calendar'),
        HOURSECS => new \lang_string('hourly', 'calendar'),
        DAYSECS => new \lang_string('daily', 'calendar'),
        WEEKSECS => new \lang_string('weekly', 'calendar'),
        '2628000' => new \lang_string('monthly', 'calendar'),
        YEARSECS => new \lang_string('annually', 'calendar')
    );
}

/**
 * Returns option list of available options for the calendar event type, given the current user and course.
 *
 * @param int $courseid The id of the course
 * @return array An array containing the event types the user can create.
 */
function calendar_get_eventtype_choices($courseid) {
    $choices = array();
    $allowed = new \stdClass;
    calendar_get_allowed_types($allowed, $courseid);

    if ($allowed->user) {
        $choices['user'] = get_string('userevents', 'calendar');
    }
    if ($allowed->site) {
        $choices['site'] = get_string('siteevents', 'calendar');
    }
    if (!empty($allowed->courses)) {
        $choices['course'] = get_string('courseevents', 'calendar');
    }
    if (!empty($allowed->categories)) {
        $choices['category'] = get_string('categoryevents', 'calendar');
    }
    if (!empty($allowed->groups) and is_array($allowed->groups)) {
        $choices['group'] = get_string('group');
    }

    return array($choices, $allowed->groups);
}

/**
 * Add an iCalendar subscription to the database.
 *
 * @param stdClass $sub The subscription object (e.g. from the form)
 * @return int The insert ID, if any.
 */
function calendar_add_subscription($sub) {
    global $DB, $USER, $SITE;

    // Undo the form definition work around to allow us to have two different
    // course selectors present depending on which event type the user selects.
    if (!empty($sub->groupcourseid)) {
        $sub->courseid = $sub->groupcourseid;
        unset($sub->groupcourseid);
    }

    // Default course id if none is set.
    if (empty($sub->courseid)) {
        if ($sub->eventtype === 'site') {
            $sub->courseid = SITEID;
        } else {
            $sub->courseid = 0;
        }
    }

    if ($sub->eventtype === 'site') {
        $sub->courseid = $SITE->id;
    } else if ($sub->eventtype === 'group' || $sub->eventtype === 'course') {
        $sub->courseid = $sub->courseid;
    } else if ($sub->eventtype === 'category') {
        $sub->categoryid = $sub->categoryid;
    } else {
        // User events.
        $sub->courseid = 0;
    }
    $sub->userid = $USER->id;

    // File subscriptions never update.
    if (empty($sub->url)) {
        $sub->pollinterval = 0;
    }

    if (!empty($sub->name)) {
        if (empty($sub->id)) {
            $id = $DB->insert_record('event_subscriptions', $sub);
            // We cannot cache the data here because $sub is not complete.
            $sub->id = $id;
            // Trigger event, calendar subscription added.
            $eventparams = array('objectid' => $sub->id,
                'context' => calendar_get_calendar_context($sub),
                'other' => array(
                    'eventtype' => $sub->eventtype,
                )
            );
            switch ($sub->eventtype) {
                case 'category':
                    $eventparams['other']['categoryid'] = $sub->categoryid;
                    break;
                case 'course':
                    $eventparams['other']['courseid'] = $sub->courseid;
                    break;
                case 'group':
                    $eventparams['other']['courseid'] = $sub->courseid;
                    $eventparams['other']['groupid'] = $sub->groupid;
                    break;
                default:
                    $eventparams['other']['courseid'] = $sub->courseid;
            }

            $event = \core\event\calendar_subscription_created::create($eventparams);
            $event->trigger();
            return $id;
        } else {
            // Why are we doing an update here?
            calendar_update_subscription($sub);
            return $sub->id;
        }
    } else {
        print_error('errorbadsubscription', 'importcalendar');
    }
}

/**
 * Add an iCalendar event to the Moodle calendar.
 *
 * @param stdClass $event The RFC-2445 iCalendar event
 * @param int $unused Deprecated
 * @param int $subscriptionid The iCalendar subscription ID
 * @param string $timezone The X-WR-TIMEZONE iCalendar property if provided
 * @throws dml_exception A DML specific exception is thrown for invalid subscriptionids.
 * @return int Code: CALENDAR_IMPORT_EVENT_UPDATED = updated,  CALENDAR_IMPORT_EVENT_INSERTED = inserted, 0 = error
 */
function calendar_add_icalendar_event($event, $unused = null, $subscriptionid, $timezone='UTC') {
    global $DB;

    // Probably an unsupported X-MICROSOFT-CDO-BUSYSTATUS event.
    if (empty($event->properties['SUMMARY'])) {
        return 0;
    }

    $name = $event->properties['SUMMARY'][0]->value;
    $name = str_replace('\n', '<br />', $name);
    $name = str_replace('\\', '', $name);
    $name = preg_replace('/\s+/u', ' ', $name);

    $eventrecord = new \stdClass;
    $eventrecord->name = clean_param($name, PARAM_NOTAGS);

    if (empty($event->properties['DESCRIPTION'][0]->value)) {
        $description = '';
    } else {
        $description = $event->properties['DESCRIPTION'][0]->value;
        $description = clean_param($description, PARAM_NOTAGS);
        $description = str_replace('\n', '<br />', $description);
        $description = str_replace('\\', '', $description);
        $description = preg_replace('/\s+/u', ' ', $description);
    }
    $eventrecord->description = $description;

    // Probably a repeating event with RRULE etc. TODO: skip for now.
    if (empty($event->properties['DTSTART'][0]->value)) {
        return 0;
    }

    if (isset($event->properties['DTSTART'][0]->parameters['TZID'])) {
        $tz = $event->properties['DTSTART'][0]->parameters['TZID'];
    } else {
        $tz = $timezone;
    }
    $tz = \core_date::normalise_timezone($tz);
    $eventrecord->timestart = strtotime($event->properties['DTSTART'][0]->value . ' ' . $tz);
    if (empty($event->properties['DTEND'])) {
        $eventrecord->timeduration = 0; // No duration if no end time specified.
    } else {
        if (isset($event->properties['DTEND'][0]->parameters['TZID'])) {
            $endtz = $event->properties['DTEND'][0]->parameters['TZID'];
        } else {
            $endtz = $timezone;
        }
        $endtz = \core_date::normalise_timezone($endtz);
        $eventrecord->timeduration = strtotime($event->properties['DTEND'][0]->value . ' ' . $endtz) - $eventrecord->timestart;
    }

    // Check to see if it should be treated as an all day event.
    if ($eventrecord->timeduration == DAYSECS) {
        // Check to see if the event started at Midnight on the imported calendar.
        date_default_timezone_set($timezone);
        if (date('H:i:s', $eventrecord->timestart) === "00:00:00") {
            // This event should be an all day event. This is not correct, we don't do anything differently for all day events.
            // See MDL-56227.
            $eventrecord->timeduration = 0;
        }
        \core_date::set_default_server_timezone();
    }

    $eventrecord->location = empty($event->properties['LOCATION'][0]->value) ? '' :
            trim(str_replace('\\', '', $event->properties['LOCATION'][0]->value));
    $eventrecord->uuid = $event->properties['UID'][0]->value;
    $eventrecord->timemodified = time();

    // Add the iCal subscription details if required.
    // We should never do anything with an event without a subscription reference.
    $sub = calendar_get_subscription($subscriptionid);
    $eventrecord->subscriptionid = $subscriptionid;
    $eventrecord->userid = $sub->userid;
    $eventrecord->groupid = $sub->groupid;
    $eventrecord->courseid = $sub->courseid;
    $eventrecord->categoryid = $sub->categoryid;
    $eventrecord->eventtype = $sub->eventtype;

    if ($updaterecord = $DB->get_record('event', array('uuid' => $eventrecord->uuid,
        'subscriptionid' => $eventrecord->subscriptionid))) {
        $eventrecord->id = $updaterecord->id;
        $return = CALENDAR_IMPORT_EVENT_UPDATED; // Update.
    } else {
        $return = CALENDAR_IMPORT_EVENT_INSERTED; // Insert.
    }
    if ($createdevent = \calendar_event::create($eventrecord, false)) {
        if (!empty($event->properties['RRULE'])) {
            // Repeating events.
            date_default_timezone_set($tz); // Change time zone to parse all events.
            $rrule = new \core_calendar\rrule_manager($event->properties['RRULE'][0]->value);
            $rrule->parse_rrule();
            $rrule->create_events($createdevent);
            \core_date::set_default_server_timezone(); // Change time zone back to what it was.
        }
        return $return;
    } else {
        return 0;
    }
}

/**
 * Update a subscription from the form data in one of the rows in the existing subscriptions table.
 *
 * @param int $subscriptionid The ID of the subscription we are acting upon.
 * @param int $pollinterval The poll interval to use.
 * @param int $action The action to be performed. One of update or remove.
 * @throws dml_exception if invalid subscriptionid is provided
 * @return string A log of the import progress, including errors
 */
function calendar_process_subscription_row($subscriptionid, $pollinterval, $action) {
    // Fetch the subscription from the database making sure it exists.
    $sub = calendar_get_subscription($subscriptionid);

    // Update or remove the subscription, based on action.
    switch ($action) {
        case CALENDAR_SUBSCRIPTION_UPDATE:
            // Skip updating file subscriptions.
            if (empty($sub->url)) {
                break;
            }
            $sub->pollinterval = $pollinterval;
            calendar_update_subscription($sub);

            // Update the events.
            return "<p>" . get_string('subscriptionupdated', 'calendar', $sub->name) . "</p>" .
                calendar_update_subscription_events($subscriptionid);
        case CALENDAR_SUBSCRIPTION_REMOVE:
            calendar_delete_subscription($subscriptionid);
            return get_string('subscriptionremoved', 'calendar', $sub->name);
            break;
        default:
            break;
    }
    return '';
}

/**
 * Delete subscription and all related events.
 *
 * @param int|stdClass $subscription subscription or it's id, which needs to be deleted.
 */
function calendar_delete_subscription($subscription) {
    global $DB;

    if (!is_object($subscription)) {
        $subscription = $DB->get_record('event_subscriptions', array('id' => $subscription), '*', MUST_EXIST);
    }

    // Delete subscription and related events.
    $DB->delete_records('event', array('subscriptionid' => $subscription->id));
    $DB->delete_records('event_subscriptions', array('id' => $subscription->id));
    \cache_helper::invalidate_by_definition('core', 'calendar_subscriptions', array(), array($subscription->id));

    // Trigger event, calendar subscription deleted.
    $eventparams = array('objectid' => $subscription->id,
        'context' => calendar_get_calendar_context($subscription),
        'other' => array(
            'eventtype' => $subscription->eventtype,
        )
    );
    switch ($subscription->eventtype) {
        case 'category':
            $eventparams['other']['categoryid'] = $subscription->categoryid;
            break;
        case 'course':
            $eventparams['other']['courseid'] = $subscription->courseid;
            break;
        case 'group':
            $eventparams['other']['courseid'] = $subscription->courseid;
            $eventparams['other']['groupid'] = $subscription->groupid;
            break;
        default:
            $eventparams['other']['courseid'] = $subscription->courseid;
    }
    $event = \core\event\calendar_subscription_deleted::create($eventparams);
    $event->trigger();
}

/**
 * From a URL, fetch the calendar and return an iCalendar object.
 *
 * @param string $url The iCalendar URL
 * @return iCalendar The iCalendar object
 */
function calendar_get_icalendar($url) {
    global $CFG;

    require_once($CFG->libdir . '/filelib.php');

    $curl = new \curl();
    $curl->setopt(array('CURLOPT_FOLLOWLOCATION' => 1, 'CURLOPT_MAXREDIRS' => 5));
    $calendar = $curl->get($url);

    // Http code validation should actually be the job of curl class.
    if (!$calendar || $curl->info['http_code'] != 200 || !empty($curl->errorno)) {
        throw new \moodle_exception('errorinvalidicalurl', 'calendar');
    }

    $ical = new \iCalendar();
    $ical->unserialize($calendar);

    return $ical;
}

/**
 * Import events from an iCalendar object into a course calendar.
 *
 * @param iCalendar $ical The iCalendar object.
 * @param int $courseid The course ID for the calendar.
 * @param int $subscriptionid The subscription ID.
 * @return string A log of the import progress, including errors.
 */
function calendar_import_icalendar_events($ical, $unused = null, $subscriptionid = null) {
    global $DB;

    $return = '';
    $eventcount = 0;
    $updatecount = 0;

    // Large calendars take a while...
    if (!CLI_SCRIPT) {
        \core_php_time_limit::raise(300);
    }

    // Mark all events in a subscription with a zero timestamp.
    if (!empty($subscriptionid)) {
        $sql = "UPDATE {event} SET timemodified = :time WHERE subscriptionid = :id";
        $DB->execute($sql, array('time' => 0, 'id' => $subscriptionid));
    }

    // Grab the timezone from the iCalendar file to be used later.
    if (isset($ical->properties['X-WR-TIMEZONE'][0]->value)) {
        $timezone = $ical->properties['X-WR-TIMEZONE'][0]->value;
    } else {
        $timezone = 'UTC';
    }

    $return = '';
    foreach ($ical->components['VEVENT'] as $event) {
        $res = calendar_add_icalendar_event($event, null, $subscriptionid, $timezone);
        switch ($res) {
            case CALENDAR_IMPORT_EVENT_UPDATED:
                $updatecount++;
                break;
            case CALENDAR_IMPORT_EVENT_INSERTED:
                $eventcount++;
                break;
            case 0:
                $return .= '<p>' . get_string('erroraddingevent', 'calendar') . ': ';
                if (empty($event->properties['SUMMARY'])) {
                    $return .= '(' . get_string('notitle', 'calendar') . ')';
                } else {
                    $return .= $event->properties['SUMMARY'][0]->value;
                }
                $return .= "</p>\n";
                break;
        }
    }

    $return .= "<p>" . get_string('eventsimported', 'calendar', $eventcount) . "</p> ";
    $return .= "<p>" . get_string('eventsupdated', 'calendar', $updatecount) . "</p>";

    // Delete remaining zero-marked events since they're not in remote calendar.
    if (!empty($subscriptionid)) {
        $deletecount = $DB->count_records('event', array('timemodified' => 0, 'subscriptionid' => $subscriptionid));
        if (!empty($deletecount)) {
            $DB->delete_records('event', array('timemodified' => 0, 'subscriptionid' => $subscriptionid));
            $return .= "<p> " . get_string('eventsdeleted', 'calendar') . ": {$deletecount} </p>\n";
        }
    }

    return $return;
}

/**
 * Fetch a calendar subscription and update the events in the calendar.
 *
 * @param int $subscriptionid The course ID for the calendar.
 * @return string A log of the import progress, including errors.
 */
function calendar_update_subscription_events($subscriptionid) {
    $sub = calendar_get_subscription($subscriptionid);

    // Don't update a file subscription.
    if (empty($sub->url)) {
        return 'File subscription not updated.';
    }

    $ical = calendar_get_icalendar($sub->url);
    $return = calendar_import_icalendar_events($ical, null, $subscriptionid);
    $sub->lastupdated = time();

    calendar_update_subscription($sub);

    return $return;
}

/**
 * Update a calendar subscription. Also updates the associated cache.
 *
 * @param stdClass|array $subscription Subscription record.
 * @throws coding_exception If something goes wrong
 * @since Moodle 2.5
 */
function calendar_update_subscription($subscription) {
    global $DB;

    if (is_array($subscription)) {
        $subscription = (object)$subscription;
    }
    if (empty($subscription->id) || !$DB->record_exists('event_subscriptions', array('id' => $subscription->id))) {
        throw new \coding_exception('Cannot update a subscription without a valid id');
    }

    $DB->update_record('event_subscriptions', $subscription);

    // Update cache.
    $cache = \cache::make('core', 'calendar_subscriptions');
    $cache->set($subscription->id, $subscription);

    // Trigger event, calendar subscription updated.
    $eventparams = array('userid' => $subscription->userid,
        'objectid' => $subscription->id,
        'context' => calendar_get_calendar_context($subscription),
        'other' => array(
            'eventtype' => $subscription->eventtype,
        )
    );
    switch ($subscription->eventtype) {
        case 'category':
            $eventparams['other']['categoryid'] = $subscription->categoryid;
            break;
        case 'course':
            $eventparams['other']['courseid'] = $subscription->courseid;
            break;
        case 'group':
            $eventparams['other']['courseid'] = $subscription->courseid;
            $eventparams['other']['groupid'] = $subscription->groupid;
            break;
        default:
            $eventparams['other']['courseid'] = $subscription->courseid;
    }
    $event = \core\event\calendar_subscription_updated::create($eventparams);
    $event->trigger();
}

/**
 * Checks to see if the user can edit a given subscription feed.
 *
 * @param mixed $subscriptionorid Subscription object or id
 * @return bool true if current user can edit the subscription else false
 */
function calendar_can_edit_subscription($subscriptionorid) {
    if (is_array($subscriptionorid)) {
        $subscription = (object)$subscriptionorid;
    } else if (is_object($subscriptionorid)) {
        $subscription = $subscriptionorid;
    } else {
        $subscription = calendar_get_subscription($subscriptionorid);
    }

    $allowed = new \stdClass;
    $courseid = $subscription->courseid;
    $categoryid = $subscription->categoryid;
    $groupid = $subscription->groupid;
    $category = null;

    if (!empty($categoryid)) {
        $category = \core_course_category::get($categoryid);
    }
    calendar_get_allowed_types($allowed, $courseid, null, $category);
    switch ($subscription->eventtype) {
        case 'user':
            return $allowed->user;
        case 'course':
            if (isset($allowed->courses[$courseid])) {
                return $allowed->courses[$courseid];
            } else {
                return false;
            }
        case 'category':
            if (isset($allowed->categories[$categoryid])) {
                return $allowed->categories[$categoryid];
            } else {
                return false;
            }
        case 'site':
            return $allowed->site;
        case 'group':
            if (isset($allowed->groups[$groupid])) {
                return $allowed->groups[$groupid];
            } else {
                return false;
            }
        default:
            return false;
    }
}

/**
 * Helper function to determine the context of a calendar subscription.
 * Subscriptions can be created in two contexts COURSE, or USER.
 *
 * @param stdClass $subscription
 * @return context instance
 */
function calendar_get_calendar_context($subscription) {
    // Determine context based on calendar type.
    if ($subscription->eventtype === 'site') {
        $context = \context_course::instance(SITEID);
    } else if ($subscription->eventtype === 'group' || $subscription->eventtype === 'course') {
        $context = \context_course::instance($subscription->courseid);
    } else {
        $context = \context_user::instance($subscription->userid);
    }
    return $context;
}

/**
 * Implements callback user_preferences, whitelists preferences that users are allowed to update directly
 *
 * Used in {@see core_user::fill_preferences_cache()}, see also {@see useredit_update_user_preference()}
 *
 * @return array
 */
function core_calendar_user_preferences() {
    $preferences = [];
    $preferences['calendar_timeformat'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED, 'default' => '0',
        'choices' => array('0', CALENDAR_TF_12, CALENDAR_TF_24)
    );
    $preferences['calendar_startwday'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 0,
        'choices' => array(0, 1, 2, 3, 4, 5, 6));
    $preferences['calendar_maxevents'] = array('type' => PARAM_INT, 'choices' => range(1, 20));
    $preferences['calendar_lookahead'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 365,
        'choices' => array(365, 270, 180, 150, 120, 90, 60, 30, 21, 14, 7, 6, 5, 4, 3, 2, 1));
    $preferences['calendar_persistflt'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 0,
        'choices' => array(0, 1));
    return $preferences;
}

/**
 * Get legacy calendar events
 *
 * @param int $tstart Start time of time range for events
 * @param int $tend End time of time range for events
 * @param array|int|boolean $users array of users, user id or boolean for all/no user events
 * @param array|int|boolean $groups array of groups, group id or boolean for all/no group events
 * @param array|int|boolean $courses array of courses, course id or boolean for all/no course events
 * @param boolean $withduration whether only events starting within time range selected
 *                              or events in progress/already started selected as well
 * @param boolean $ignorehidden whether to select only visible events or all events
 * @param array $categories array of category ids and/or objects.
 * @param int $limitnum Number of events to fetch or zero to fetch all.
 *
 * @return array $events of selected events or an empty array if there aren't any (or there was an error)
 */
function calendar_get_legacy_events($tstart, $tend, $users, $groups, $courses,
        $withduration = true, $ignorehidden = true, $categories = [], $limitnum = 0) {
    // Normalise the users, groups and courses parameters so that they are compliant with \core_calendar\local\api::get_events().
    // Existing functions that were using the old calendar_get_events() were passing a mixture of array, int, boolean for these
    // parameters, but with the new API method, only null and arrays are accepted.
    list($userparam, $groupparam, $courseparam, $categoryparam) = array_map(function($param) {
        // If parameter is true, return null.
        if ($param === true) {
            return null;
        }

        // If parameter is false, return an empty array.
        if ($param === false) {
            return [];
        }

        // If the parameter is a scalar value, enclose it in an array.
        if (!is_array($param)) {
            return [$param];
        }

        // No normalisation required.
        return $param;
    }, [$users, $groups, $courses, $categories]);

    // If a single user is provided, we can use that for capability checks.
    // Otherwise current logged in user is used - See MDL-58768.
    if (is_array($userparam) && count($userparam) == 1) {
        \core_calendar\local\event\container::set_requesting_user($userparam[0]);
    }
    $mapper = \core_calendar\local\event\container::get_event_mapper();
    $events = \core_calendar\local\api::get_events(
        $tstart,
        $tend,
        null,
        null,
        null,
        null,
        $limitnum,
        null,
        $userparam,
        $groupparam,
        $courseparam,
        $categoryparam,
        $withduration,
        $ignorehidden
    );

    return array_reduce($events, function($carry, $event) use ($mapper) {
        return $carry + [$event->get_id() => $mapper->from_event_to_stdclass($event)];
    }, []);
}


/**
 * Get the calendar view output.
 *
 * @param   \calendar_information $calendar The calendar being represented
 * @param   string  $view The type of calendar to have displayed
 * @param   bool    $includenavigation Whether to include navigation
 * @param   bool    $skipevents Whether to load the events or not
 * @param   int     $lookahead Overwrites site and users's lookahead setting.
 * @return  array[array, string]
 */
function calendar_get_view(\calendar_information $calendar, $view, $includenavigation = true, bool $skipevents = false,
        ?int $lookahead = null) {
    global $PAGE, $CFG;

    $renderer = $PAGE->get_renderer('core_calendar');
    $type = \core_calendar\type_factory::get_calendar_instance();

    // Calculate the bounds of the month.
    $calendardate = $type->timestamp_to_date_array($calendar->time);

    $date = new \DateTime('now', core_date::get_user_timezone_object(99));
    $eventlimit = 0;

    if ($view === 'day') {
        $tstart = $type->convert_to_timestamp($calendardate['year'], $calendardate['mon'], $calendardate['mday']);
        $date->setTimestamp($tstart);
        $date->modify('+1 day');
    } else if ($view === 'upcoming' || $view === 'upcoming_mini') {
        // Number of days in the future that will be used to fetch events.
        if (!$lookahead) {
            if (isset($CFG->calendar_lookahead)) {
                $defaultlookahead = intval($CFG->calendar_lookahead);
            } else {
                $defaultlookahead = CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD;
            }
            $lookahead = get_user_preferences('calendar_lookahead', $defaultlookahead);
        }

        // Maximum number of events to be displayed on upcoming view.
        $defaultmaxevents = CALENDAR_DEFAULT_UPCOMING_MAXEVENTS;
        if (isset($CFG->calendar_maxevents)) {
            $defaultmaxevents = intval($CFG->calendar_maxevents);
        }
        $eventlimit = get_user_preferences('calendar_maxevents', $defaultmaxevents);

        $tstart = $type->convert_to_timestamp($calendardate['year'], $calendardate['mon'], $calendardate['mday'],
                $calendardate['hours']);
        $date->setTimestamp($tstart);
        $date->modify('+' . $lookahead . ' days');
    } else {
        $tstart = $type->convert_to_timestamp($calendardate['year'], $calendardate['mon'], 1);
        $monthdays = $type->get_num_days_in_month($calendardate['year'], $calendardate['mon']);
        $date->setTimestamp($tstart);
        $date->modify('+' . $monthdays . ' days');

        if ($view === 'mini' || $view === 'minithree') {
            $template = 'core_calendar/calendar_mini';
        } else {
            $template = 'core_calendar/calendar_month';
        }
    }

    // We need to extract 1 second to ensure that we don't get into the next day.
    $date->modify('-1 second');
    $tend = $date->getTimestamp();

    list($userparam, $groupparam, $courseparam, $categoryparam) = array_map(function($param) {
        // If parameter is true, return null.
        if ($param === true) {
            return null;
        }

        // If parameter is false, return an empty array.
        if ($param === false) {
            return [];
        }

        // If the parameter is a scalar value, enclose it in an array.
        if (!is_array($param)) {
            return [$param];
        }

        // No normalisation required.
        return $param;
    }, [$calendar->users, $calendar->groups, $calendar->courses, $calendar->categories]);

    if ($skipevents) {
        $events = [];
    } else {
        $events = \core_calendar\local\api::get_events(
            $tstart,
            $tend,
            null,
            null,
            null,
            null,
            $eventlimit,
            null,
            $userparam,
            $groupparam,
            $courseparam,
            $categoryparam,
            true,
            true,
            function ($event) {
                if ($proxy = $event->get_course_module()) {
                    $cminfo = $proxy->get_proxied_instance();
                    return $cminfo->uservisible;
                }

                if ($proxy = $event->get_category()) {
                    $category = $proxy->get_proxied_instance();

                    return $category->is_uservisible();
                }

                return true;
            }
        );
    }

    $related = [
        'events' => $events,
        'cache' => new \core_calendar\external\events_related_objects_cache($events),
        'type' => $type,
    ];

    $data = [];
    if ($view == "month" || $view == "mini" || $view == "minithree") {
        $month = new \core_calendar\external\month_exporter($calendar, $type, $related);
        $month->set_includenavigation($includenavigation);
        $month->set_initialeventsloaded(!$skipevents);
        $month->set_showcoursefilter($view == "month");
        $data = $month->export($renderer);
    } else if ($view == "day") {
        $day = new \core_calendar\external\calendar_day_exporter($calendar, $related);
        $data = $day->export($renderer);
        $template = 'core_calendar/calendar_day';
    } else if ($view == "upcoming" || $view == "upcoming_mini") {
        $upcoming = new \core_calendar\external\calendar_upcoming_exporter($calendar, $related);
        $data = $upcoming->export($renderer);

        if ($view == "upcoming") {
            $template = 'core_calendar/calendar_upcoming';
        } else if ($view == "upcoming_mini") {
            $template = 'core_calendar/calendar_upcoming_mini';
        }
    }

    return [$data, $template];
}

/**
 * Request and render event form fragment.
 *
 * @param array $args The fragment arguments.
 * @return string The rendered mform fragment.
 */
function calendar_output_fragment_event_form($args) {
    global $CFG, $OUTPUT, $USER;
    require_once($CFG->libdir . '/grouplib.php');
    $html = '';
    $data = [];
    $eventid = isset($args['eventid']) ? clean_param($args['eventid'], PARAM_INT) : null;
    $starttime = isset($args['starttime']) ? clean_param($args['starttime'], PARAM_INT) : null;
    $courseid = (isset($args['courseid']) && $args['courseid'] != SITEID) ? clean_param($args['courseid'], PARAM_INT) : null;
    $categoryid = isset($args['categoryid']) ? clean_param($args['categoryid'], PARAM_INT) : null;
    $event = null;
    $hasformdata = isset($args['formdata']) && !empty($args['formdata']);
    $context = \context_user::instance($USER->id);
    $editoroptions = \core_calendar\local\event\forms\create::build_editor_options($context);
    $formoptions = ['editoroptions' => $editoroptions, 'courseid' => $courseid];
    $draftitemid = 0;

    if ($hasformdata) {
        parse_str(clean_param($args['formdata'], PARAM_TEXT), $data);
        if (isset($data['description']['itemid'])) {
            $draftitemid = $data['description']['itemid'];
        }
    }

    if ($starttime) {
        $formoptions['starttime'] = $starttime;
    }

    if (is_null($eventid)) {
        if (!empty($courseid)) {
            $groupcoursedata = groups_get_course_data($courseid);
            $formoptions['groups'] = [];
            foreach ($groupcoursedata->groups as $groupid => $groupdata) {
                $formoptions['groups'][$groupid] = $groupdata->name;
            }
        }
        $mform = new \core_calendar\local\event\forms\create(
            null,
            $formoptions,
            'post',
            '',
            null,
            true,
            $data
        );

        // Let's check first which event types user can add.
        $eventtypes = calendar_get_allowed_event_types($courseid);

        // If the user is on course context and is allowed to add course events set the event type default to course.
        if (!empty($courseid) && !empty($eventtypes['course'])) {
            $data['eventtype'] = 'course';
            $data['courseid'] = $courseid;
            $data['groupcourseid'] = $courseid;
        } else if (!empty($categoryid) && !empty($eventtypes['category'])) {
            $data['eventtype'] = 'category';
            $data['categoryid'] = $categoryid;
        } else if (!empty($groupcoursedata) && !empty($eventtypes['group'])) {
            $data['groupcourseid'] = $courseid;
            $data['groups'] = $groupcoursedata->groups;
        }
        $mform->set_data($data);
    } else {
        $event = calendar_event::load($eventid);

        if (!calendar_edit_event_allowed($event)) {
            print_error('nopermissiontoupdatecalendar');
        }

        $mapper = new \core_calendar\local\event\mappers\create_update_form_mapper();
        $eventdata = $mapper->from_legacy_event_to_data($event);
        $data = array_merge((array) $eventdata, $data);
        $event->count_repeats();
        $formoptions['event'] = $event;

        if (!empty($event->courseid)) {
            $groupcoursedata = groups_get_course_data($event->courseid);
            $formoptions['groups'] = [];
            foreach ($groupcoursedata->groups as $groupid => $groupdata) {
                $formoptions['groups'][$groupid] = $groupdata->name;
            }
        }

        $data['description']['text'] = file_prepare_draft_area(
            $draftitemid,
            $event->context->id,
            'calendar',
            'event_description',
            $event->id,
            null,
            $data['description']['text']
        );
        $data['description']['itemid'] = $draftitemid;

        $mform = new \core_calendar\local\event\forms\update(
            null,
            $formoptions,
            'post',
            '',
            null,
            true,
            $data
        );
        $mform->set_data($data);

        // Check to see if this event is part of a subscription or import.
        // If so display a warning on edit.
        if (isset($event->subscriptionid) && ($event->subscriptionid != null)) {
            $renderable = new \core\output\notification(
                get_string('eventsubscriptioneditwarning', 'calendar'),
                \core\output\notification::NOTIFY_INFO
            );

            $html .= $OUTPUT->render($renderable);
        }
    }

    if ($hasformdata) {
        $mform->is_validated();
    }

    $html .= $mform->render();
    return $html;
}

/**
 * Calculate the timestamp from the supplied Gregorian Year, Month, and Day.
 *
 * @param   int     $d The day
 * @param   int     $m The month
 * @param   int     $y The year
 * @param   int     $time The timestamp to use instead of a separate y/m/d.
 * @return  int     The timestamp
 */
function calendar_get_timestamp($d, $m, $y, $time = 0) {
    // If a day, month and year were passed then convert it to a timestamp. If these were passed
    // then we can assume the day, month and year are passed as Gregorian, as no where in core
    // should we be passing these values rather than the time.
    if (!empty($d) && !empty($m) && !empty($y)) {
        if (checkdate($m, $d, $y)) {
            $time = make_timestamp($y, $m, $d);
        } else {
            $time = time();
        }
    } else if (empty($time)) {
        $time = time();
    }

    return $time;
}

/**
 * Get the calendar footer options.
 *
 * @param calendar_information $calendar The calendar information object.
 * @return array The data for template and template name.
 */
function calendar_get_footer_options($calendar) {
    global $CFG, $USER, $DB, $PAGE;

    // Generate hash for iCal link.
    $rawhash = $USER->id . $DB->get_field('user', 'password', ['id' => $USER->id]) . $CFG->calendar_exportsalt;
    $authtoken = sha1($rawhash);

    $renderer = $PAGE->get_renderer('core_calendar');
    $footer = new \core_calendar\external\footer_options_exporter($calendar, $USER->id, $authtoken);
    $data = $footer->export($renderer);
    $template = 'core_calendar/footer_options';

    return [$data, $template];
}

/**
 * Get the list of potential calendar filter types as a type => name
 * combination.
 *
 * @return array
 */
function calendar_get_filter_types() {
    $types = [
        'site',
        'category',
        'course',
        'group',
        'user',
    ];

    return array_map(function($type) {
        return [
            'eventtype' => $type,
            'name' => get_string("eventtype{$type}", "calendar"),
        ];
    }, $types);
}

/**
 * Check whether the specified event type is valid.
 *
 * @param string $type
 * @return bool
 */
function calendar_is_valid_eventtype($type) {
    $validtypes = [
        'user',
        'group',
        'course',
        'category',
        'site',
    ];
    return in_array($type, $validtypes);
}

/**
 * Get event types the user can create event based on categories, courses and groups
 * the logged in user belongs to.
 *
 * @param int|null $courseid The course id.
 * @return array The array of allowed types.
 */
function calendar_get_allowed_event_types(int $courseid = null) {
    global $DB, $CFG, $USER;

    $types = [
        'user' => false,
        'site' => false,
        'course' => false,
        'group' => false,
        'category' => false
    ];

    if (!empty($courseid) && $courseid != SITEID) {
        $context = \context_course::instance($courseid);
        $groups = groups_get_all_groups($courseid);

        $types['user'] = has_capability('moodle/calendar:manageownentries', $context);

        if (has_capability('moodle/calendar:manageentries', $context)) {
            $types['course'] = true;

            $types['group'] = (!empty($groups) && has_capability('moodle/site:accessallgroups', $context))
                || array_filter($groups, function($group) use ($USER) {
                    return groups_is_member($group->id);
                });
        } else if (has_capability('moodle/calendar:managegroupentries', $context)) {
            $types['group'] = (!empty($groups) && has_capability('moodle/site:accessallgroups', $context))
                || array_filter($groups, function($group) use ($USER) {
                    return groups_is_member($group->id);
                });
        }
    }

    if (has_capability('moodle/calendar:manageentries', \context_course::instance(SITEID))) {
        $types['site'] = true;
    }

    if (has_capability('moodle/calendar:manageownentries', \context_system::instance())) {
        $types['user'] = true;
    }
    if (core_course_category::has_manage_capability_on_any()) {
        $types['category'] = true;
    }

    // We still don't know if the user can create group and course events, so iterate over the courses to find out
    // if the user has capabilities in one of the courses.
    if ($types['course'] == false || $types['group'] == false) {
        if ($CFG->calendar_adminseesall && has_capability('moodle/calendar:manageentries', context_system::instance())) {
            $sql = "SELECT c.id, " . context_helper::get_preload_record_columns_sql('ctx') . "
                      FROM {course} c
                      JOIN {context} ctx ON ctx.contextlevel = ? AND ctx.instanceid = c.id
                     WHERE c.id IN (
                            SELECT DISTINCT courseid FROM {groups}
                        )";
            $courseswithgroups = $DB->get_recordset_sql($sql, [CONTEXT_COURSE]);
            foreach ($courseswithgroups as $course) {
                context_helper::preload_from_record($course);
                $context = context_course::instance($course->id);

                if (has_capability('moodle/calendar:manageentries', $context)) {
                    if (has_any_capability(['moodle/site:accessallgroups', 'moodle/calendar:managegroupentries'], $context)) {
                        // The user can manage group entries or access any group.
                        $types['group'] = true;
                        $types['course'] = true;
                        break;
                    }
                }
            }
            $courseswithgroups->close();

            if (false === $types['course']) {
                // Course is still not confirmed. There may have been no courses with a group in them.
                $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
                $sql = "SELECT
                            c.id, c.visible, {$ctxfields}
                        FROM {course} c
                        JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
                $params = [
                    'contextlevel' => CONTEXT_COURSE,
                ];
                $courses = $DB->get_recordset_sql($sql, $params);
                foreach ($courses as $course) {
                    context_helper::preload_from_record($course);
                    $context = context_course::instance($course->id);
                    if (has_capability('moodle/calendar:manageentries', $context)) {
                        $types['course'] = true;
                        break;
                    }
                }
                $courses->close();
            }

        } else {
            $courses = calendar_get_default_courses(null, 'id');
            if (empty($courses)) {
                return $types;
            }

            $courseids = array_map(function($c) {
                return $c->id;
            }, $courses);

            // Check whether the user has access to create events within courses which have groups.
            list($insql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
            $sql = "SELECT c.id, " . context_helper::get_preload_record_columns_sql('ctx') . "
                      FROM {course} c
                      JOIN {context} ctx ON ctx.contextlevel = :contextlevel AND ctx.instanceid = c.id
                     WHERE c.id $insql
                       AND c.id IN (SELECT DISTINCT courseid FROM {groups})";
            $params['contextlevel'] = CONTEXT_COURSE;
            $courseswithgroups = $DB->get_recordset_sql($sql, $params);
            foreach ($courseswithgroups as $coursewithgroup) {
                context_helper::preload_from_record($coursewithgroup);
                $context = context_course::instance($coursewithgroup->id);

                if (has_capability('moodle/calendar:manageentries', $context)) {
                    // The user has access to manage calendar entries for the whole course.
                    // This includes groups if they have the accessallgroups capability.
                    $types['course'] = true;
                    if (has_capability('moodle/site:accessallgroups', $context)) {
                        // The user also has access to all groups so they can add calendar entries to any group.
                        // The manageentries capability overrides the managegroupentries capability.
                        $types['group'] = true;
                        break;
                    }

                    if (empty($types['group']) && has_capability('moodle/calendar:managegroupentries', $context)) {
                        // The user has the managegroupentries capability.
                        // If they have access to _any_ group, then they can create calendar entries within that group.
                        $types['group'] = !empty(groups_get_all_groups($coursewithgroup->id, $USER->id));
                    }
                }

                // Okay, course and group event types are allowed, no need to keep the loop iteration.
                if ($types['course'] == true && $types['group'] == true) {
                    break;
                }
            }
            $courseswithgroups->close();

            if (false === $types['course']) {
                list($insql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
                $contextsql = "SELECT c.id, " . context_helper::get_preload_record_columns_sql('ctx') . "
                                FROM {course} c
                                JOIN {context} ctx ON ctx.contextlevel = :contextlevel AND ctx.instanceid = c.id
                                WHERE c.id $insql";
                $params['contextlevel'] = CONTEXT_COURSE;
                $contextrecords = $DB->get_recordset_sql($contextsql, $params);
                foreach ($contextrecords as $course) {
                    context_helper::preload_from_record($course);
                    $coursecontext = context_course::instance($course->id);
                    if (has_capability('moodle/calendar:manageentries', $coursecontext)
                            && ($courseid == $course->id || empty($courseid))) {
                        $types['course'] = true;
                        break;
                    }
                }
                $contextrecords->close();
            }

        }
    }

    return $types;
}
