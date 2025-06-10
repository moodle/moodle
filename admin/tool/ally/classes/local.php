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
 * Local library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

use tool_ally\componentsupport\component_base;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/webservice/lib.php');

/**
 * Local library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local {

    /**
     * Get list of role IDs from admin settings.
     *
     * @return array
     */
    public static function get_roleids() {
        $roles = get_config('tool_ally', 'roles');
        if (empty($roles)) {
            return [];
        }
        $roleids = explode(',', $roles);

        return array_combine($roleids, $roleids);
    }

    /**
     * Get list of admin user IDs.
     *
     * @return array
     */
    public static function get_adminids() {
        $userids = array_keys(get_admins());

        return array_combine($userids, $userids);
    }

    /**
     * Load all course contexts into context cache.
     *
     * @param array $courseids
     */
    public static function preload_course_contexts(array $courseids = []) {
        global $DB;

        $fields = \context_helper::get_preload_record_columns_sql('c');
        $params = ['contextlevel' => CONTEXT_COURSE];
        $insql  = '';

        if (!empty($courseids)) {
            $result = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'cid');
            $insql  = ' AND c.instanceid '.$result[0];
            $params = array_merge($params, $result[1]);
        }
        $rs = $DB->get_recordset_sql("SELECT $fields FROM {context} c WHERE c.contextlevel = :contextlevel$insql", $params);
        foreach ($rs as $context) {
            \context_helper::preload_from_record($context);
        }
        $rs->close();
    }

    /**
     * Format timestamp using ISO-8601 standard.
     *
     * @param int $timestamp
     * @return string
     */
    public static function iso_8601($timestamp) {
        $date = new \DateTime('', new \DateTimeZone('UTC'));
        $date->setTimestamp($timestamp);

        return $date->format('c');
    }

    /**
     * @param string $iso8601
     * @return int
     */
    public static function iso_8601_to_timestamp($iso8601) {
        $dt = \DateTime::createFromFormat(\DateTime::ISO8601, $iso8601, new \DateTimeZone('UTC'));

        return $dt->getTimestamp();
    }

    /**
     * Is this script running during testing?
     *
     * @return bool
     */
    public static function duringtesting() {
        $runningphpunittest = defined('PHPUNIT_TEST') && PHPUNIT_TEST;
        $runningbehattest = defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING;
        return ($runningphpunittest || $runningbehattest);
    }

    /**
     * Strip mod_ from component string if necessary.
     * @param $component
     * @return bool|string
     */
    public static function clean_component_string($component) {
        if (strpos($component, 'mod_') === 0) {
            $component = substr($component, strlen('mod_'));
        }
        return $component;
    }

    /**
     * Get component class with namespace.
     * @param string $component
     * @return string
     */
    public static function get_component_class($component) {
        $component = self::clean_component_string($component);
        $componentclassname = $component . '_component';
        $componentclassname = 'tool_ally\\componentsupport\\'.$componentclassname;
        return $componentclassname;
    }

    /**
     * Get a component instance.
     *
     * @param string $component
     * @return null|component_base
     */
    public static function get_component_instance($component) {
        $class = self::get_component_class($component);
        $instance = null;
        if (class_exists($class)) {
            /** @var component_base $instance */
            $instance = new $class();
        }
        return $instance;
    }

    /**
     * Get type of component support for specific component.
     *
     * @param string $component
     * @return string | bool
     */
    public static function get_component_support_type($component) {
        $componentclassname = self::get_component_class($component);
        if (class_exists($componentclassname)) {
            return $componentclassname::component_type();
        }
        return false;
    }

    /**
     * Counts list of users enrolled given a context, skipping duplicate ids.
     * Inspired by count_enrolled_users found in theme/snap/classes/local.php
     * Core method is counting duplicates because users can be enrolled into a course via different methods, hence,
     * having multiple registered enrollments.
     *
     * @param \context $context
     * @param string $withcapability
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
     * @return int number of enrolled users.
     */
    public static function count_enrolled_users(\context $context, $withcapability = '', $groupid = 0, $onlyactive = false) {
        global $DB;

        $capjoin = get_enrolled_with_capabilities_join(
            $context, '', $withcapability, $groupid, $onlyactive);

        $sql = "SELECT COUNT(*)
                  FROM (SELECT DISTINCT u.id
                          FROM {user} u
                               $capjoin->joins
                         WHERE $capjoin->wheres AND u.deleted = 0) as uids
                ";

        return $DB->count_records_sql($sql, $capjoin->params);
    }

    /**
     * Get ally web user.
     * @return bool|\stdClass
     * @throws \dml_exception
     */
    public static function get_ally_web_user() {
        global $DB, $CFG;
        return $DB->get_record('user', ['username' => 'ally_webuser', 'mnethostid' => $CFG->mnet_localhost_id]);
    }

    /**
     * Get a web service token record.
     *
     * @return stdClass
     * @throws \dml_exception
     * @throws \webservice_access_exception
     */
    public static function get_ws_token() {
        $allyuser = self::get_ally_web_user();
        if (!$allyuser) {
            $msg = 'Ally web user (ally_webuser) does not exist. Has auto configure been run?';
            throw new \webservice_access_exception($msg);
        }
        $webservicelib = new \webservice();
        $tokens = $webservicelib->get_user_ws_tokens($allyuser->id);
        if (empty($tokens)) {
            $msg = 'There are no web service tokens attributed to ally_webuser. Has auto configure been run?';
            throw new \webservice_access_exception($msg);
        }
        if (count($tokens) > 1) {
            $msg = 'There are multiple web service tokens attributed to ally_webuser. There should only be one token.';
            throw new \webservice_access_exception($msg);
        }
        $wstoken = reset($tokens);
        return $wstoken;
    }

    /**
     * Gets the instance id of a course module for a give course module id.
     *
     * @param int $contextid
     * @return int|null
     */
    public static function get_instanceid_for_cmid(int $cmid): ?int {
        global $DB;

        static $staticcache = [];

        if (isset($staticcache[$cmid])) {
            return $staticcache[$cmid];
        }

        try {
            // Sometimes this can get called before the module is available the core functions, I think due to transactions.
            list($course, $cm) = get_course_and_cm_from_cmid($cmid);
            $instanceid = $cm->instance;
        } catch (\Exception $e) {
            // Because of the above transaction issue, we may get here. Try to get it strait out of the DB.
            if (!$instanceid = $DB->get_field('course_modules', 'instance', ['id' => $cmid])) {
                $instanceid = null;
            }
        }

        if (!static::duringtesting() && !is_null($instanceid)) {
            // Don't save to cache during testing, otherwise things break during tests.
            $staticcache[$cmid] = $instanceid;
        }
        return $instanceid;

    }
}
