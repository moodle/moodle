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
 * This file contains the viee form of course specific reminder settings.
 *
 * @package    local_reminders
 * @copyright  2014 Joannes Burk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/local/reminders/locallib.php');

/**
 * Form class for editing course specific reminder settings.
 *
 * @package    local_reminders
 * @copyright  2014 Joannes Burk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminders_coursesettings_edit_form extends moodleform {

    /**
     * Creates the form definition using received information.
     *
     * @return void.
     */
    public function definition() {
        global $USER, $CFG;

        $mform = $this->_form;

        if (!$CFG->local_reminders_enable) {
            $mform->addElement('static', 'descriptionex2sub', '',
                get_string('plugindisabled', 'local_reminders'));
            return;
        }

        $daysarray = [
            'days7' => ' '.get_string('days7', 'local_reminders'),
            'days3' => ' '.get_string('days3', 'local_reminders'),
            'days1' => ' '.get_string('days1', 'local_reminders'),
            'custom' => ' '.get_string('custom', 'local_reminders'),
        ];

        list($coursesettings) = $this->_customdata;
        $explicitlyenable = $coursesettings->explicitenable;

        if ($explicitlyenable) {
            $mform->addElement('static', 'descriptionex2sub', '',
                get_string('activityconfexplicitenablehint', 'local_reminders'));
        }

        $mform->addElement('advcheckbox', 'status_activities',
            get_string('dueheading', 'local_reminders'),
            get_string('enabled', 'local_reminders'));
        $mform->setDefault('status_activities', 1);

        $mform->addElement('advcheckbox', 'status_course',
            get_string('courseheading', 'local_reminders'),
            get_string('enabled', 'local_reminders'));
        $mform->setDefault('status_course', 1);

        $mform->addElement('advcheckbox', 'status_group',
            get_string('groupheading', 'local_reminders'),
            get_string('enabled', 'local_reminders'));
        $mform->setDefault('status_group', 1);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'explicitenable');
        $mform->setType('explicitenable', PARAM_INT);

        foreach ($daysarray as $dkey => $dvalue) {
            $mform->addElement('hidden', "activityglobal_$dkey");
            $mform->setType("activityglobal_$dkey", PARAM_INT);
        }

        $currtime = time() - (24 * 3600);
        $upcomingactivities = get_upcoming_events_for_course($coursesettings->courseid, $currtime);
        $noactivities = true;
        $mform->addElement('header', 'description',
                get_string('activityconfupcomingactivities', 'local_reminders'));
        $customunitlabel = empty($coursesettings->customunit) ? '' : '(' . $coursesettings->customunit . ')';

        if (!empty($upcomingactivities)) {
            // Group activities by start time.
            $allactivities = [];
            foreach ($upcomingactivities as $activity) {
                $starttime = $activity->timestart - ($activity->timestart % (24 * 3600));
                if (array_key_exists($starttime, $allactivities)) {
                    $allactivities[$starttime][] = $activity;
                } else {
                    $allactivities[$starttime] = [$activity];
                }
            }
            ksort($allactivities);
            $upcomingactivities = $allactivities;

            $mform->addElement('static', 'descriptionsub', '',
                get_string('activityconfupcomingactivitiesdesc', 'local_reminders'));

            $daytimeformat = get_string('strftimedaydate', 'langconfig');
            $tzone = reminders_get_timezone($USER);
            foreach ($upcomingactivities as $daytime => $dailyactivities) {
                $mform->addElement('static', 'header'.$daytime, '<h4><b>'.userdate($daytime, $daytimeformat, $tzone).'</b></h4>');
                $isfirstone = true;

                foreach ($dailyactivities as $activity) {
                    $activitytypename = "";
                    $activityname = $activity->name;
                    if (!isemptystring($activity->modulename)) {
                        $modinfo = fetch_module_instance($activity->modulename, $activity->instance, $coursesettings->courseid);
                        $activitytypename = get_string('pluginname', $activity->modulename).': ';
                        $activityname = isset($modinfo->name) ? $modinfo->name : $activity->name;
                    }

                    if (!$isfirstone) {
                        $mform->addElement('static', 'duetime'.$daytime.$activity->modulename.$activity->instance,
                            '',
                            '<div style="color: #ddd;">'.str_repeat('_', 70).'</div>');
                    }

                    $eventlink = $this->generate_event_link($activity);
                    $friendlyeventtypetext = $this->load_string_safe('eventtype'.$activity->eventtype, '');
                    $mform->addElement('static', 'header'.$daytime.$activity->modulename.$activity->instance,
                        '',
                        '<h5><b><a href="'.$eventlink.'">'.$activitytypename.$activityname
                        ."</a></b></h5>$friendlyeventtypetext");
                    $mform->addElement('static', 'duetime'.$daytime.$activity->modulename.$activity->instance,
                        get_string('activityconfduein', 'local_reminders'),
                        userdate($activity->timestart, get_string('strftimedatetime', 'langconfig'), $tzone));

                    $key = "activity_".$activity->id.'_enabled';
                    $mform->addElement('advcheckbox', $key, get_string('enabled', 'local_reminders'), ' ');
                    $mform->setDefault($key, $explicitlyenable ? 0 : 1);

                    $activitydayarray = [];
                    foreach ($daysarray as $dkey => $dvalue) {
                        $trefkey = "activityglobal_$dkey";
                        $daykey = "activity_".$activity->id."_$dkey";
                        $activitydayarray[] = $mform->createElement('advcheckbox', $daykey, '',
                            $dkey != 'custom' ? $dvalue : $dvalue . ' ' .$customunitlabel);
                        if (!$explicitlyenable) {
                            $mform->disabledIf($daykey, $trefkey, 'eq', 0);
                        }
                        $mform->setDefault($daykey, $coursesettings->$trefkey);
                    }
                    $groupkey = 'reminder_'.$activity->id.'_group';
                    $daysgroup = $mform->addElement('group',
                        $groupkey,
                        get_string('reminderdaysaheadschedule', 'local_reminders'),
                        $activitydayarray, null, false);
                    $mform->disabledIf($groupkey, $key, 'unchecked');

                    $overduesupports = $activity->eventtype != 'course' && $activity->eventtype != 'open';
                    if ($overduesupports) {
                        $keyoverdue = "activity_".$activity->id.'_enabledoverdue';
                        $mform->addElement('advcheckbox', $keyoverdue, get_string('enabledoverdue', 'local_reminders'), ' ');
                        $mform->setDefault($keyoverdue, 1);
                    }

                    $noactivities = false;
                    $isfirstone = false;
                }

                $mform->addElement('html', '<hr/>');
            }
        }

        if ($noactivities) {
            $mform->addElement('static', 'descriptionsubnoact', '',
                get_string('activityconfnoupcomingactivities', 'local_reminders'));
        }

        $this->add_action_buttons(true);

        $this->set_data($coursesettings);
    }

    /**
     * Loads language string safely with default value if not exists.
     *
     * @param string $key key to load for.
     * @param string $defaultvalue default value to load if nx.
     * @return string loaded string.
     */
    private function load_string_safe($key, $defaultvalue='') {
        $stringman = get_string_manager();
        if ($stringman->string_exists($key, 'local_reminders')) {
            return get_string($key, 'local_reminders');
        }
        return $defaultvalue;
    }

    /**
     * Returns the correct link for the calendar event.
     *
     * @param object $event event instance.
     * @return string complete url for the event
     */
    private function generate_event_link($event) {
        $params = ['view' => 'day', 'time' => $event->timestart];
        $calurl = new moodle_url('/calendar/view.php', $params);
        $calurl->set_anchor('event_'.$event->id);

        return $calurl->out(false);
    }
}
