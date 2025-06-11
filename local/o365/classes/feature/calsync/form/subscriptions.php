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
 * Microsoft 365 Calendar Sync Subscription Form.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\calsync\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/formslib.php');

\MoodleQuickForm::registerElementType('localo365calendar',
    "$CFG->dirroot/local/o365/classes/feature/calsync/form/element/calendar.php",
    '\local_o365\feature\calsync\form\element\calendar');

/**
 * Microsoft 365 Calendar Sync Subscription Form.
 */
class subscriptions extends \moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        global $USER;

        $mform =& $this->_form;

        $saved = optional_param('saved', 0, PARAM_INT);

        if (!empty($saved)) {
            $mform->addElement('html', \html_writer::div(get_string('changessaved'), 'alert alert-success'));
        }

        $mform->addElement('html', \html_writer::tag('h2', get_string('ucp_calsync_title', 'local_o365')));
        $mform->addElement('html', '<br />');

        $settingcalcustom = $this->_customdata;
        $mform->addElement('html', \html_writer::tag('b', get_string('calendar_setting', 'local_o365')));
        $mform->addElement('checkbox', 'settingcal', '', get_string('calendar_setting', 'local_o365'));
        $mform->setDefault('settingcal', $settingcalcustom['o365calendarcheck']);

        if (!empty($settingcalcustom['o365calendarcheck'])) {
            $mform->addElement('html', '<br />');
            $mform->addElement('html', \html_writer::div(get_string('ucp_calsync_desc', 'local_o365')));
            $mform->addElement('html', '<br />');

            $mform->addElement('html', \html_writer::tag('b', get_string('ucp_calsync_availcal', 'local_o365')));

            $checkboxattrs = ['class' => 'calcheckbox', 'group' => '1'];

            $sitecalcustom = $this->_customdata;
            $sitecalcustom['cansyncin'] = $this->_customdata['cancreatesiteevents'];
            $mform->addElement('localo365calendar', 'sitecal', '', get_string('calendar_site', 'local_o365'), $checkboxattrs,
                $sitecalcustom);

            $usercalcustom = $this->_customdata;
            $usercalcustom['cansyncin'] = true;
            $mform->addElement('localo365calendar', 'usercal', '', get_string('calendar_user', 'local_o365'), $checkboxattrs,
                $usercalcustom);

            foreach ($this->_customdata['usercourses'] as $courseid => $course) {
                $coursecalcustom = $this->_customdata;
                $coursecalcustom['cansyncin'] = (!empty($this->_customdata['cancreatecourseevents'][$courseid])) ? true : false;
                $mform->addElement('localo365calendar', 'coursecal['.$course->id.']', '', $course->fullname, $checkboxattrs,
                    $coursecalcustom);
            }
        }

        $this->add_action_buttons();
    }

    /**
     * Process received form data and update calendar subscriptions.
     *
     * @param \stdClass $fromform Data from the form.
     * @param string $primarycalid The o365 ID of the user's primary calendar.
     * @param bool $cancreatesiteevents Whether the user has permission to create site events.
     * @param array $cancreatecourseevents Array of user courses containing whether the user has permission to create course events.
     * @param string $sitecalenderid The o365 ID of the user's site calendar.
     * @return bool Success/Failure.
     */
    public static function update_subscriptions($fromform, $primarycalid, $cancreatesiteevents,
            $cancreatecourseevents, $sitecalenderid = null) {
        global $DB, $USER;

        // Determine outlook calendar setting check.
        $usersetting = $DB->get_record('local_o365_calsettings', ['user_id' => $USER->id]);
        if (!empty($fromform->settingcal) && empty($usersetting)) {
            // Not currently subscribed.
            $newsetting = [
                    'user_id' => $USER->id,
                    'o365calid' => $sitecalenderid,
                    'timecreated' => time(),
            ];
            $newsetting['id'] = $DB->insert_record('local_o365_calsettings', (object)$newsetting);
        } else if (empty($fromform->settingcal) && !empty($usersetting)) {
            $DB->delete_records('local_o365_calsettings', ['user_id' => $USER->id]);
        }

        // Determine and organize existing subscriptions.
        $currentcaldata = [
            'site' => [
                'subscribed' => false,
                'recid' => null,
                'syncbehav' => null,
                'o365calid' => null,
            ],
            'user' => [
                'subscribed' => false,
                'recid' => null,
                'syncbehav' => null,
                'o365calid' => null,
            ],
            'course' => [],
        ];

        $existingcoursesubs = [];
        $existingsubsrs = $DB->get_recordset('local_o365_calsub', ['user_id' => $USER->id]);
        foreach ($existingsubsrs as $existingsubrec) {
            if ($existingsubrec->caltype === 'site') {
                $currentcaldata['site']['subscribed'] = true;
                $currentcaldata['site']['recid'] = $existingsubrec->id;
                $currentcaldata['site']['syncbehav'] = $existingsubrec->syncbehav;
                $currentcaldata['site']['o365calid'] = $existingsubrec->o365calid;
            } else if ($existingsubrec->caltype === 'user') {
                $currentcaldata['user']['subscribed'] = true;
                $currentcaldata['user']['recid'] = $existingsubrec->id;
                $currentcaldata['user']['syncbehav'] = $existingsubrec->syncbehav;
                $currentcaldata['user']['o365calid'] = $existingsubrec->o365calid;
            } else if ($existingsubrec->caltype === 'course') {
                $existingcoursesubs[$existingsubrec->caltypeid] = $existingsubrec;
            }
        }
        $existingsubsrs->close();

        // Handle changes to site and user calendar subscriptions.
        foreach (['site', 'user'] as $caltype) {
            $formkey = $caltype.'cal';
            $calchecked = false;
            if (!empty($fromform->settingcal)) {
                if (!empty($fromform->$formkey) && is_array($fromform->$formkey) && !empty($fromform->{$formkey}['checked'])) {
                    $calchecked = true;
                }
            }
            $syncwith = ($calchecked === true && !empty($fromform->{$formkey}['syncwith'])) ?
                $fromform->{$formkey}['syncwith'] : '';
            $syncbehav = ($calchecked === true && !empty($fromform->{$formkey}['syncbehav'])) ?
                $fromform->{$formkey}['syncbehav'] : 'out';
            if ($caltype === 'site' && empty($cancreatesiteevents)) {
                $syncbehav = 'out';
            }
            if ($calchecked !== true && $currentcaldata[$caltype]['subscribed'] === true) {
                $DB->delete_records('local_o365_calsub', ['user_id' => $USER->id, 'caltype' => $caltype]);
                $eventdata = [
                    'objectid' => $currentcaldata[$caltype]['recid'],
                    'userid' => $USER->id,
                    'other' => ['caltype' => $caltype],
                ];
                $event = \local_o365\event\calendar_unsubscribed::create($eventdata);
                $event->trigger();
            } else if ($calchecked === true) {
                $changed = false;
                if ($currentcaldata[$caltype]['subscribed'] !== $calchecked) {
                    $changed = true;
                }
                if ($currentcaldata[$caltype]['syncbehav'] !== $syncbehav) {
                    $changed = true;
                }
                if ($currentcaldata[$caltype]['o365calid'] !== $syncwith) {
                    $changed = true;
                }

                if ($changed === true) {
                    if ($currentcaldata[$caltype]['subscribed'] === false) {
                        // Not currently subscribed.
                        $newsub = [
                            'user_id' => $USER->id,
                            'caltype' => $caltype,
                            'caltypeid' => ($caltype === 'site') ? 0 : $USER->id,
                            'o365calid' => $syncwith,
                            'syncbehav' => $syncbehav,
                            'isprimary' => ($syncwith == $primarycalid) ? '1' : '0',
                            'timecreated' => time(),
                        ];
                        $newsub['id'] = $DB->insert_record('local_o365_calsub', (object)$newsub);
                        $eventdata = [
                            'objectid' => $newsub['id'],
                            'userid' => $USER->id,
                            'other' => ['caltype' => $caltype],
                        ];
                    } else {
                        // Already subscribed, update behavior.
                        $updatedinfo = [
                            'id' => $currentcaldata[$caltype]['recid'],
                            'o365calid' => $syncwith,
                            'syncbehav' => $syncbehav,
                            'isprimary' => ($syncwith == $primarycalid) ? '1' : '0',
                        ];
                        $DB->update_record('local_o365_calsub', $updatedinfo);
                        $eventdata = [
                            'objectid' => $currentcaldata[$caltype]['recid'],
                            'userid' => $USER->id,
                            'other' => ['caltype' => $caltype],
                        ];
                    }
                    $event = \local_o365\event\calendar_subscribed::create($eventdata);
                    $event->trigger();
                }
            }
        }

        // The following calculates what courses need to be added or removed from the subscription table.
        $newcoursesubs = [];
        if (!empty($fromform->coursecal) && is_array($fromform->coursecal)) {
            foreach ($fromform->coursecal as $courseid => $coursecaldata) {
                if (!empty($coursecaldata['checked'])) {
                    $newcoursesubs[$courseid] = $coursecaldata;
                }
            }
        }
        $todelete = (empty($fromform->settingcal)) ? $existingcoursesubs : array_diff_key($existingcoursesubs, $newcoursesubs);
        $toadd = (empty($fromform->settingcal)) ? [] : array_diff_key($newcoursesubs, $existingcoursesubs);
        foreach ($todelete as $courseid => $unused) {
            $DB->delete_records('local_o365_calsub', ['user_id' => $USER->id, 'caltype' => 'course', 'caltypeid' => $courseid]);
            $eventdata = [
                'objectid' => $USER->id,
                'userid' => $USER->id,
                'other' => ['caltype' => 'course', 'caltypeid' => $courseid],
            ];
            $event = \local_o365\event\calendar_unsubscribed::create($eventdata);
            $event->trigger();
        }
        foreach ($newcoursesubs as $courseid => $coursecaldata) {
            $syncwith = (!empty($coursecaldata['syncwith'])) ? $coursecaldata['syncwith'] : '';
            $syncbehav = (!empty($coursecaldata['syncbehav'])) ? $coursecaldata['syncbehav'] : 'out';
            if (empty($cancreatecourseevents[$courseid])) {
                $syncbehav = 'out';
            }
            if (isset($toadd[$courseid])) {
                // Not currently subscribed.
                $newsub = [
                    'user_id' => $USER->id,
                    'caltype' => 'course',
                    'caltypeid' => $courseid,
                    'o365calid' => $syncwith,
                    'syncbehav' => $syncbehav,
                    'timecreated' => time(),
                    'isprimary' => ($syncwith == $primarycalid) ? '1' : '0',
                ];
                $DB->insert_record('local_o365_calsub', (object)$newsub);
                $eventdata = [
                    'objectid' => $USER->id,
                    'userid' => $USER->id,
                    'other' => ['caltype' => 'course', 'caltypeid' => $courseid],
                ];
                $event = \local_o365\event\calendar_subscribed::create($eventdata);
                $event->trigger();
            } else if (isset($existingcoursesubs[$courseid])) {
                $changed = false;
                if ($existingcoursesubs[$courseid]->syncbehav !== $syncbehav) {
                    $changed = true;
                }
                if ($existingcoursesubs[$courseid]->o365calid !== $syncwith) {
                    $changed = true;
                }
                if ($changed === true) {
                    // Already subscribed, update behavior.
                    $updatedrec = [
                        'id' => $existingcoursesubs[$courseid]->id,
                        'o365calid' => $syncwith,
                        'syncbehav' => $syncbehav,
                        'isprimary' => ($syncwith == $primarycalid) ? '1' : '0',
                    ];
                    $DB->update_record('local_o365_calsub', (object)$updatedrec);
                    $eventdata = [
                        'objectid' => $USER->id,
                        'userid' => $USER->id,
                        'other' => ['caltype' => 'course', 'caltypeid' => $courseid],
                    ];
                    $event = \local_o365\event\calendar_subscribed::create($eventdata);
                    $event->trigger();
                }
            }
        }
    }
}
