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

namespace core_external;

use context;
use context_course;
use context_helper;
use context_system;
use core_user;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * Utility functions for the external API.
 *
 * @package    core_webservice
 * @copyright  2015 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.0
 */
class util {

    /**
     * Validate a list of courses, returning the complete course objects for valid courses.
     *
     * Each course has an additional 'contextvalidated' field, this will be set to true unless
     * you set $keepfails, in which case it will be false if validation fails for a course.
     *
     * @param  array $courseids A list of course ids
     * @param  array $courses   An array of courses already pre-fetched, indexed by course id.
     * @param  bool $addcontext True if the returned course object should include the full context object.
     * @param  bool $keepfails  True to keep all the course objects even if validation fails
     * @return array            An array of courses and the validation warnings
     */
    public static function validate_courses($courseids, $courses = array(), $addcontext = false,
            $keepfails = false) {
        global $DB;

        // Delete duplicates.
        $courseids = array_unique($courseids);
        $warnings = array();

        // Remove courses which are not even requested.
        $courses = array_intersect_key($courses, array_flip($courseids));

        // For any courses NOT loaded already, get them in a single query (and preload contexts)
        // for performance. Preserve ordering because some tests depend on it.
        $newcourseids = [];
        foreach ($courseids as $cid) {
            if (!array_key_exists($cid, $courses)) {
                $newcourseids[] = $cid;
            }
        }
        if ($newcourseids) {
            list ($listsql, $listparams) = $DB->get_in_or_equal($newcourseids);

            // Load list of courses, and preload associated contexts.
            $contextselect = context_helper::get_preload_record_columns_sql('x');
            $newcourses = $DB->get_records_sql("
                            SELECT c.*, $contextselect
                              FROM {course} c
                              JOIN {context} x ON x.instanceid = c.id
                             WHERE x.contextlevel = ? AND c.id $listsql",
                    array_merge([CONTEXT_COURSE], $listparams));
            foreach ($newcourseids as $cid) {
                if (array_key_exists($cid, $newcourses)) {
                    $course = $newcourses[$cid];
                    context_helper::preload_from_record($course);
                    $courses[$course->id] = $course;
                }
            }
        }

        foreach ($courseids as $cid) {
            // Check the user can function in this context.
            try {
                $context = context_course::instance($cid);
                external_api::validate_context($context);

                if ($addcontext) {
                    $courses[$cid]->context = $context;
                }
                $courses[$cid]->contextvalidated = true;
            } catch (Exception $e) {
                if ($keepfails) {
                    $courses[$cid]->contextvalidated = false;
                } else {
                    unset($courses[$cid]);
                }
                $warnings[] = array(
                    'item' => 'course',
                    'itemid' => $cid,
                    'warningcode' => '1',
                    'message' => 'No access rights in course context'
                );
            }
        }

        return array($courses, $warnings);
    }

    /**
     * Returns all area files (optionally limited by itemid).
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID or all files if not specified
     * @param bool $useitemidinurl wether to use the item id in the file URL (modules intro don't use it)
     * @return array of files, compatible with the external_files structure.
     * @since Moodle 3.2
     */
    public static function get_area_files($contextid, $component, $filearea, $itemid = false, $useitemidinurl = true) {
        $files = array();
        $fs = get_file_storage();

        if ($areafiles = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'itemid, filepath, filename', false)) {
            foreach ($areafiles as $areafile) {
                $file = array();
                $file['filename'] = $areafile->get_filename();
                $file['filepath'] = $areafile->get_filepath();
                $file['mimetype'] = $areafile->get_mimetype();
                $file['filesize'] = $areafile->get_filesize();
                $file['timemodified'] = $areafile->get_timemodified();
                $file['isexternalfile'] = $areafile->is_external_file();
                if ($file['isexternalfile']) {
                    $file['repositorytype'] = $areafile->get_repository_type();
                }
                $fileitemid = $useitemidinurl ? $areafile->get_itemid() : null;
                $file['fileurl'] = moodle_url::make_webservice_pluginfile_url($contextid, $component, $filearea,
                                    $fileitemid, $areafile->get_filepath(), $areafile->get_filename())->out(false);
                $files[] = $file;
            }
        }
        return $files;
    }


    /**
     * Create and return a session linked token. Token to be used for html embedded client apps that want to communicate
     * with the Moodle server through web services. The token is linked to the current session for the current page request.
     * It is expected this will be called in the script generating the html page that is embedding the client app and that the
     * returned token will be somehow passed into the client app being embedded in the page.
     *
     * @param int $tokentype EXTERNAL_TOKEN_EMBEDDED|EXTERNAL_TOKEN_PERMANENT
     * @param stdClass|int $serviceorid service linked to the token
     * @param int $userid user linked to the token
     * @param context $contextorid
     * @param int $validuntil date when the token expired
     * @param string $iprestriction allowed ip - if 0 or empty then all ips are allowed
     * @return string generated token
     */
    public static function generate_token(
        int $tokentype,
        stdClass $service,
        int $userid,
        context $contextorid,
        int $validuntil = 0,
        string $iprestriction = ''
    ): string {
        global $DB, $USER;

        // Make sure the token doesn't exist (even if it should be almost impossible with the random generation).
        $numtries = 0;
        do {
            $numtries ++;
            $generatedtoken = md5(uniqid((string) rand(), true));
            if ($numtries > 5) {
                throw new moodle_exception('tokengenerationfailed');
            }
        } while ($DB->record_exists('external_tokens', ['token' => $generatedtoken]));
        $newtoken = (object) [
            'token' => $generatedtoken,
        ];

        if (empty($service->requiredcapability) || has_capability($service->requiredcapability, $context, $userid)) {
            $newtoken->externalserviceid = $service->id;
        } else {
            throw new moodle_exception('nocapabilitytousethisservice');
        }

        $newtoken->tokentype = $tokentype;
        $newtoken->userid = $userid;
        if ($tokentype == EXTERNAL_TOKEN_EMBEDDED) {
            $newtoken->sid = session_id();
        }

        $newtoken->contextid = $context->id;
        $newtoken->creatorid = $USER->id;
        $newtoken->timecreated = time();
        $newtoken->validuntil = $validuntil;
        if (!empty($iprestriction)) {
            $newtoken->iprestriction = $iprestriction;
        }

        // Generate the private token, it must be transmitted only via https.
        $newtoken->privatetoken = random_string(64);
        $DB->insert_record('external_tokens', $newtoken);
        return $newtoken->token;
    }

    /**
     * Get a service by its id.
     *
     * @param int $serviceid
     * @return stdClass
     */
    public static function get_service_by_id(int $serviceid): stdClass {
        global $DB;

        return $DB->get_record('external_services', ['id' => $serviceid], '*', MUST_EXIST);
    }

    /**
     * Get a service by its name.
     *
     * @param string $name The service name.
     * @return stdClass
     */
    public static function get_service_by_name(string $name): stdClass {
        global $DB;

        return $DB->get_record('external_services', ['name' => $name], '*', MUST_EXIST);
    }

    /**
     * Set the last time a token was sent and trigger the \core\event\webservice_token_sent event.
     *
     * This function is used when a token is generated by the user via login/token.php or admin/tool/mobile/launch.php.
     * In order to protect the privatetoken, we remove it from the event params.
     *
     * @param  stdClass $token token object
     */
    public static function log_token_request(stdClass $token): void {
        global $DB, $USER;

        $token->privatetoken = null;

        // Log token access.
        $DB->set_field('external_tokens', 'lastaccess', time(), ['id' => $token->id]);

        $event = \core\event\webservice_token_sent::create([
            'objectid' => $token->id,
        ]);
        $event->add_record_snapshot('external_tokens', $token);
        $event->trigger();

        // Check if we need to notify the user about the new login via token.
        $loginip = getremoteaddr();
        if ($USER->lastip === $loginip) {
            return;
        }

        $shouldskip = WS_SERVER || CLI_SCRIPT || !NO_MOODLE_COOKIES;
        if ($shouldskip && !PHPUNIT_TEST) {
            return;
        }

        $useragent = \core_useragent::get_user_agent_string();
        $ismoodleapp = \core_useragent::is_moodle_app();

        // Schedule adhoc task to sent a login notification to the user.
        $task = new \core\task\send_login_notifications();
        $task->set_userid($USER->id);
        $logintime = time();
        $task->set_custom_data([
            'useragent' => \core_useragent::get_user_agent_string(),
            'ismoodleapp' => \core_useragent::is_moodle_app(),
            'loginip' => $loginip,
            'logintime' => $logintime,
        ]);
        $task->set_component('core');
        // We need sometime so the mobile app will send to Moodle the device information after login.
        $task->set_next_run_time(time() + (2 * MINSECS));
        \core\task\manager::reschedule_or_queue_adhoc_task($task);
    }

    /**
     * Generate or return an existing token for the current authenticated user.
     * This function is used for creating a valid token for users authenticathing via places, including:
     * - login/token.php
     * - admin/tool/mobile/launch.php.
     *
     * @param stdClass $service external service object
     * @return stdClass token object
     * @throws moodle_exception
     */
    public static function generate_token_for_current_user(stdClass $service) {
        global $DB, $USER, $CFG;

        core_user::require_active_user($USER, true, true);

        // Check if there is any required system capability.
        if ($service->requiredcapability && !has_capability($service->requiredcapability, context_system::instance())) {
            throw new moodle_exception('missingrequiredcapability', 'webservice', '', $service->requiredcapability);
        }

        // Specific checks related to user restricted service.
        if ($service->restrictedusers) {
            $authoriseduser = $DB->get_record('external_services_users', [
                'externalserviceid' => $service->id,
                'userid' => $USER->id,
            ]);

            if (empty($authoriseduser)) {
                throw new moodle_exception('usernotallowed', 'webservice', '', $service->shortname);
            }

            if (!empty($authoriseduser->validuntil) && $authoriseduser->validuntil < time()) {
                throw new moodle_exception('invalidtimedtoken', 'webservice');
            }

            if (!empty($authoriseduser->iprestriction) && !address_in_subnet(getremoteaddr(), $authoriseduser->iprestriction)) {
                throw new moodle_exception('invalidiptoken', 'webservice');
            }
        }

        // Check if a token has already been created for this user and this service.
        $conditions = [
            'userid' => $USER->id,
            'externalserviceid' => $service->id,
            'tokentype' => EXTERNAL_TOKEN_PERMANENT,
        ];
        $tokens = $DB->get_records('external_tokens', $conditions, 'timecreated ASC');

        // A bit of sanity checks.
        foreach ($tokens as $key => $token) {

            // Checks related to a specific token. (script execution continue).
            $unsettoken = false;
            // If sid is set then there must be a valid associated session no matter the token type.
            if (!empty($token->sid)) {
                if (!\core\session\manager::session_exists($token->sid)) {
                    // This token will never be valid anymore, delete it.
                    $DB->delete_records('external_tokens', ['sid' => $token->sid]);
                    $unsettoken = true;
                }
            }

            // Remove token is not valid anymore.
            if (!empty($token->validuntil) && $token->validuntil < time()) {
                $DB->delete_records('external_tokens', ['token' => $token->token, 'tokentype' => EXTERNAL_TOKEN_PERMANENT]);
                $unsettoken = true;
            }

            // Remove token if its IP is restricted.
            if (isset($token->iprestriction) && !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
                $unsettoken = true;
            }

            if ($unsettoken) {
                unset($tokens[$key]);
            }
        }

        // If some valid tokens exist then use the most recent.
        if (count($tokens) > 0) {
            $token = array_pop($tokens);
        } else {
            $context = context_system::instance();
            $isofficialservice = $service->shortname == MOODLE_OFFICIAL_MOBILE_SERVICE;

            if (
                ($isofficialservice && has_capability('moodle/webservice:createmobiletoken', $context)) ||
                (!is_siteadmin($USER) && has_capability('moodle/webservice:createtoken', $context))
            ) {

                // Create a new token.
                $token = new stdClass;
                $token->token = md5(uniqid((string) rand(), true));
                $token->userid = $USER->id;
                $token->tokentype = EXTERNAL_TOKEN_PERMANENT;
                $token->contextid = context_system::instance()->id;
                $token->creatorid = $USER->id;
                $token->timecreated = time();
                $token->externalserviceid = $service->id;
                // By default tokens are valid for 12 weeks.
                $token->validuntil = $token->timecreated + $CFG->tokenduration;
                $token->iprestriction = null;
                $token->sid = null;
                $token->lastaccess = null;
                // Generate the private token, it must be transmitted only via https.
                $token->privatetoken = random_string(64);
                $token->id = $DB->insert_record('external_tokens', $token);

                $eventtoken = clone $token;
                $eventtoken->privatetoken = null;
                $params = [
                    'objectid' => $eventtoken->id,
                    'relateduserid' => $USER->id,
                    'other' => [
                        'auto' => true,
                    ],
                ];
                $event = \core\event\webservice_token_created::create($params);
                $event->add_record_snapshot('external_tokens', $eventtoken);
                $event->trigger();
            } else {
                throw new moodle_exception('cannotcreatetoken', 'webservice', '', $service->shortname);
            }
        }
        return $token;
    }
}
