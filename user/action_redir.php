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
 * Wrapper script redirecting user operations to correct destination.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once("../config.php");
require_once($CFG->dirroot . '/course/lib.php');

$formaction = required_param('formaction', PARAM_LOCALURL);
$id = required_param('id', PARAM_INT);

$PAGE->set_url('/user/action_redir.php', array('formaction' => $formaction, 'id' => $id));
list($formaction) = explode('?', $formaction, 2);

// This page now only handles the bulk enrolment change actions, other actions are done with ajax.
$actions = array('bulkchange.php');

if (array_search($formaction, $actions) === false) {
    throw new \moodle_exception('unknownuseraction');
}

if (!confirm_sesskey()) {
    throw new \moodle_exception('confirmsesskeybad');
}

if ($formaction == 'bulkchange.php') {
    // Backwards compatibility for enrolment plugins bulk change functionality.
    // This awful code is adapting from the participant page with it's param names and values
    // to the values expected by the bulk enrolment changes forms.
    $formaction = required_param('formaction', PARAM_URL);
    require_once($CFG->dirroot . '/enrol/locallib.php');

    $url = new moodle_url($formaction);
    // Get the enrolment plugin type and bulk action from the url.
    $plugin = $url->param('plugin');
    $operationname = $url->param('operation');
    $dataformat = $url->param('dataformat');

    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
    $context = context_course::instance($id);
    $PAGE->set_context($context);

    $userids = optional_param_array('userid', array(), PARAM_INT);
    $default = new moodle_url('/user/index.php', ['id' => $course->id]);
    $returnurl = new moodle_url(optional_param('returnto', $default, PARAM_LOCALURL));

    if (empty($userids)) {
        $userids = optional_param_array('bulkuser', array(), PARAM_INT);
    }
    if (empty($userids)) {
        // The first time list hack.
        if (empty($userids) and $post = data_submitted()) {
            foreach ($post as $k => $v) {
                if (preg_match('/^user(\d+)$/', $k, $m)) {
                    $userids[] = $m[1];
                }
            }
        }
    }

    if (empty($plugin) AND $operationname == 'download_participants') {
        // Check permissions.
        $pagecontext = ($course->id == SITEID) ? context_system::instance() : $context;
        if (course_can_view_participants($pagecontext)) {
            $plugins = core_plugin_manager::instance()->get_plugins_of_type('dataformat');
            if (isset($plugins[$dataformat])) {
                if ($plugins[$dataformat]->is_enabled()) {
                    if (empty($userids)) {
                        redirect($returnurl, get_string('noselectedusers', 'bulkusers'));
                    }

                    $columnnames = array(
                        'firstname' => get_string('firstname'),
                        'lastname' => get_string('lastname'),
                    );

                    // Get the list of fields we have to hide.
                    $hiddenfields = [];
                    if (!has_capability('moodle/course:viewhiddenuserfields', $context)) {
                        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
                    }

                    // Retrieve all identity fields required for users.
                    $userfieldsapi = \core_user\fields::for_identity($context);
                    $userfields = $userfieldsapi->get_sql('u', true);

                    $identityfields = array_keys($userfields->mappings);
                    foreach ($identityfields as $field) {
                        $columnnames[$field] = \core_user\fields::get_display_name($field);
                    }

                    // Ensure users are enrolled in this course context, further limiting them by selected userids.
                    [$enrolledsql, $enrolledparams] = get_enrolled_sql($context);
                    [$useridsql, $useridparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'userid');
                    [$userordersql, $userorderparams] = users_order_by_sql('u', null, $context);

                    $params = array_merge($userfields->params, $enrolledparams, $useridparams, $userorderparams);

                    // If user can only view their own groups then they can only export users from those groups too.
                    $groupmode = groups_get_course_groupmode($course);
                    if ($groupmode == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $context)) {
                        $groups = groups_get_all_groups($course->id, $USER->id, 0, 'g.id');
                        $groupids = array_column($groups, 'id');

                        [$groupmembersql, $groupmemberparams] = groups_get_members_ids_sql($groupids, $context);
                        $params = array_merge($params, $groupmemberparams);

                        $groupmemberjoin = "JOIN ({$groupmembersql}) jg ON jg.id = u.id";
                    } else {
                        $groupmemberjoin = '';
                    }

                    // Add column for groups if the user can view them.
                    if (!isset($hiddenfields['groups'])) {
                        $columnnames['groupnames'] = get_string('groups');
                        $userfields->selects .= ', gcn.groupnames';

                        [$groupconcatnamesql, $groupconcatnameparams] = groups_get_names_concat_sql($course->id);
                        $groupconcatjoin = "LEFT JOIN ({$groupconcatnamesql}) gcn ON gcn.userid = u.id";
                        $params = array_merge($params, $groupconcatnameparams);
                    } else {
                        $groupconcatjoin = '';
                    }

                    $sql = "SELECT u.firstname, u.lastname {$userfields->selects}
                              FROM {user} u
                                   {$userfields->joins}
                              JOIN ({$enrolledsql}) je ON je.id = u.id
                                   {$groupmemberjoin}
                                   {$groupconcatjoin}
                             WHERE u.id {$useridsql}
                          ORDER BY {$userordersql}";

                    $rs = $DB->get_recordset_sql($sql, $params);

                    // Provide callback to pre-process all records ensuring user identity fields are escaped if HTML supported.
                    \core\dataformat::download_data(
                        'courseid_' . $course->id . '_participants',
                        $dataformat,
                        $columnnames,
                        $rs,
                        function(stdClass $record, bool $supportshtml) use ($identityfields): stdClass {
                            if ($supportshtml) {
                                foreach ($identityfields as $identityfield) {
                                    $record->{$identityfield} = s($record->{$identityfield});
                                }
                            }

                            return $record;
                        }
                    );
                    $rs->close();
                }
            }
        }
    } else {
        $instances = enrol_get_instances($course->id, false);
        $instance = false;
        foreach ($instances as $oneinstance) {
            if ($oneinstance->enrol == $plugin) {
                $instance = $oneinstance;
                break;
            }
        }
        if (!$instance) {
            throw new \moodle_exception('errorwithbulkoperation', 'enrol');
        }

        $manager = new course_enrolment_manager($PAGE, $course, $instance->id);
        $plugins = $manager->get_enrolment_plugins();

        if (!isset($plugins[$plugin])) {
            throw new \moodle_exception('errorwithbulkoperation', 'enrol');
        }

        $plugin = $plugins[$plugin];

        $operations = $plugin->get_bulk_operations($manager);

        if (!isset($operations[$operationname])) {
            throw new \moodle_exception('errorwithbulkoperation', 'enrol');
        }
        $operation = $operations[$operationname];

        if (empty($userids)) {
            redirect($returnurl, get_string('noselectedusers', 'bulkusers'));
        }

        $users = $manager->get_users_enrolments($userids);

        $removed = array_diff($userids, array_keys($users));
        if (!empty($removed)) {
            // This manager does not filter by enrolment method - so we can get the removed users details.
            $removedmanager = new course_enrolment_manager($PAGE, $course);
            $removedusers = $removedmanager->get_users_enrolments($removed);

            foreach ($removedusers as $removeduser) {
                $msg = get_string('userremovedfromselectiona', 'enrol', fullname($removeduser));
                \core\notification::warning($msg);
            }
        }

        // We may have users from any kind of enrolment, we need to filter for the enrolment plugin matching the bulk action.
        $matchesplugin = function($user) use ($plugin) {
            foreach ($user->enrolments as $enrolment) {
                if ($enrolment->enrolmentplugin->get_name() == $plugin->get_name()) {
                    return true;
                }
            }
            return false;
        };
        $filteredusers = array_filter($users, $matchesplugin);

        // If the bulk operation is deleting enrolments, we exclude in any case the current user as it was probably a mistake.
        if ($operationname === 'deleteselectedusers' && (!in_array($USER->id, $removed))) {
            \core\notification::warning(get_string('userremovedfromselectiona', 'enrol', fullname($USER)));
            unset($filteredusers[$USER->id]);
        }

        if (empty($filteredusers)) {
            redirect($returnurl, get_string('noselectedusers', 'bulkusers'));
        }

        $users = $filteredusers;

        // Get the form for the bulk operation.
        $mform = $operation->get_form($PAGE->url, array('users' => $users));
        // If the mform is false then attempt an immediate process. This may be an immediate action that
        // doesn't require user input OR confirmation.... who know what but maybe one day.
        if ($mform === false) {
            if ($operation->process($manager, $users, new stdClass)) {
                redirect($returnurl);
            } else {
                throw new \moodle_exception('errorwithbulkoperation', 'enrol');
            }
        }
        // Check if the bulk operation has been cancelled.
        if ($mform->is_cancelled()) {
            redirect($returnurl);
        }
        if ($mform->is_submitted() && $mform->is_validated() && confirm_sesskey()) {
            if ($operation->process($manager, $users, $mform->get_data())) {
                redirect($returnurl);
            }
        }

        $pagetitle = get_string('bulkuseroperation', 'enrol');

        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pagetitle);
        echo $OUTPUT->header();
        echo $OUTPUT->heading($operation->get_title());
        $mform->display();
        echo $OUTPUT->footer();
        exit();
    }
} else {
    throw new coding_exception('invalidaction');
}
