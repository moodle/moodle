<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   user
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */
require_once(dirname(dirname(__FILE__)) . '/lib/moodleexternal.php');
require_once(dirname(dirname(__FILE__)) . '/course/lib.php');

/**
 * course webservice api
 *
 * @author Jerome Mouneyrac
 */
final class course_external extends moodle_external {

    /**
     * Retrieve courses
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam integer   $params:search->id - the id to search
     * @subparam string    $params:search->idnumber - the idnumber to search
     * @subparam string    $params:search->shortname - the shortname to search
     * @return object     $return
     * @subreturn integer $return:course->id
     * @subreturn string  $return:course->idnumber
     * @subreturn string  $return:course->shortname
     * @subreturn integer $return:course->category
     * @subreturn string  $return:course->fullname
     * @subreturn string  $return:course->summary
     * @subreturn string  $return:course->format
     * @subreturn integer $return:course->startdate
     * @subreturn integer $return:course->sortorder
     * @subreturn integer $return:course->showgrades
     * @subreturn string  $return:course->modinfo
     * @subreturn string  $return:course->newsitems
     * @subreturn string  $return:course->guest
     * @subreturn integer $return:course->metacourse
     * @subreturn string  $return:course->password
     * @subreturn integer $return:course->enrolperiod
     * @subreturn integer $return:course->defaultrole
     * @subreturn integer $return:course->enrollable
     * @subreturn integer $return:course->numsections
     * @subreturn integer $return:course->expirynotify
     * @subreturn integer $return:course->notifystudents
     * @subreturn integer $return:course->expirythreshold
     * @subreturn integer $return:course->marker
     * @subreturn integer $return:course->maxbytes
     * @subreturn integer $return:course->showreports
     * @subreturn integer $return:course->visible
     * @subreturn integer $return:course->hiddensections
     * @subreturn integer $return:course->groupmode
     * @subreturn integer $return:course->groupmodeforce
     * @subreturn integer $return:course->defaultgroupingid
     * @subreturn string  $return:course->lang
     * @subreturn string  $return:course->theme
     * @subreturn string  $return:course->cost
     * @subreturn string  $return:course->currency
     * @subreturn integer $return:course->timecreated
     * @subreturn integer $return:course->timemodified
     * @subreturn integer $return:course->requested
     * @subreturn integer $return:course->restrictmodules
     * @subreturn integer $return:course->enrolstartdate
     * @subreturn integer $return:course->enrolenddate
     * @subreturn string  $return:course->enrol
     * @subreturn integer $return:course->enablecompletion
     */
    static function get_courses($params) {
        global $USER;
        if (has_capability('moodle/course:participate', get_context_instance(CONTEXT_SYSTEM))) {
            $courses = array();
            foreach ($params as $param) {
                $course = new stdClass();
                if (key_exists('id', $param)) {
                    $param['id'] = clean_param($param['id'], PARAM_INT);
                    $course = get_course_by_id($param['id']);

                } else if (key_exists('idnumber', $param)) {
                    $param['idnumber'] = clean_param($param['idnumber'], PARAM_ALPHANUM);
                    $course = get_course_by_idnumber($param['idnumber']);
                } else if (key_exists('shortname', $param)) {
                    $param['shortname'] = clean_param($param['shortname'], PARAM_ALPHANUM);
                    $course = get_course_by_shortname($param['shortname']);
                }
                if (!empty($course)) {
                    $returnedcourse = new stdClass();
                    $returnedcourse->id =  $course->id;
                    $returnedcourse->idnumber =  $course->idnumber;
                    $returnedcourse->shortname =  $course->shortname;
                    $returnedcourse->summary =  $course->summary;
                    $returnedcourse->format =  $course->format;
                    $returnedcourse->fullname =  $course->fullname;
                    $returnedcourse->numsections =  $course->numsections;
                    $returnedcourse->startdate =  $course->startdate;
                    $returnedcourse->category =  $course->category;
                    $returnedcourse->sortorder =  $course->sortorder;
                    $returnedcourse->password =  $course->password  ;
                    $returnedcourse->showgrades =  $course->showgrades;
                    $returnedcourse->modinfo =  $course->modinfo;
                    $returnedcourse->newsitems =  $course->newsitems;
                    $returnedcourse->guest =  $course->guest;
                    $returnedcourse->enrolperiod =  $course->enrolperiod;
                    $returnedcourse->marker =  $course->marker;
                    $returnedcourse->maxbytes =  $course->maxbytes;
                    $returnedcourse->showreports =  $course->showreports;
                    $returnedcourse->visible =  $course->visible;
                    $returnedcourse->hiddensections =  $course->hiddensections;
                    $returnedcourse->groupmode =  $course->groupmode;
                    $returnedcourse->groupmodeforce =  $course->groupmodeforce;
                    $returnedcourse->defaultgroupingid =  $course->defaultgroupingid;
                    $returnedcourse->lang =  $course->lang;
                    $returnedcourse->theme =  $course->theme;
                    $returnedcourse->cost =  $course->cost;
                    $returnedcourse->currency =  $course->currency;
                    $returnedcourse->timecreated =  $course->timecreated;
                    $returnedcourse->timemodified =  $course->timemodified;
                    $returnedcourse->metacourse =  $course->metacourse;
                    $returnedcourse->requested =  $course->requested;
                    $returnedcourse->restrictmodules =  $course->restrictmodules;
                    $returnedcourse->expirynotify =  $course->expirynotify;
                    $returnedcourse->expirythreshold =  $course->expirythreshold;
                    $returnedcourse->notifystudents =  $course->notifystudents;
                    $returnedcourse->enrollable =  $course->enrollable;
                    $returnedcourse->enrolstartdate =  $course->enrolstartdate;
                    $returnedcourse->enrolenddate =  $course->enrolenddate;
                    $returnedcourse->enrol =  $course->enrol;
                    $returnedcourse->defaultrole =  $course->defaultrole;
                    $returnedcourse->enablecompletion =  $course->enablecompletion;

                    $courses[] = $returnedcourse;
                }
            }
            return $courses;
        }
        else {
            throw new moodle_exception('wscouldnotviewcoursenopermission');
        }
    }

    /**
     * Create multiple courses
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string    $params:course->idnumber
     * @subparam string    $params:course->shortname
     * @subparam integer   $params:course->category
     * @subparam string    $params:course->fullname
     * @subparam string    $params:course->summary
     * @subparam string    $params:course->format
     * @subparam integer   $params:course->startdate
     * @subparam integer   $params:course->sortorder
     * @subparam integer   $params:course->showgrades
     * @subparam string    $params:course->modinfo
     * @subparam string    $params:course->newsitems
     * @subparam string    $params:course->guest
     * @subparam integer   $params:course->metacourse
     * @subparam string    $params:course->password
     * @subparam integer   $params:course->enrolperiod
     * @subparam integer   $params:course->defaultrole
     * @subparam integer   $params:course->enrollable
     * @subparam integer   $params:course->numsections
     * @subparam integer   $params:course->expirynotify
     * @subparam integer   $params:course->notifystudents
     * @subparam integer   $params:course->expirythreshold
     * @subparam integer   $params:course->marker
     * @subparam integer   $params:course->maxbytes
     * @subparam integer   $params:course->showreports
     * @subparam integer   $params:course->visible
     * @subparam integer   $params:course->hiddensections
     * @subparam integer   $params:course->groupmode
     * @subparam integer   $params:course->groupmodeforce
     * @subparam integer   $params:course->defaultgroupingid
     * @subparam string    $params:course->lang
     * @subparam string    $params:course->theme
     * @subparam string    $params:course->cost
     * @subparam string    $params:course->currency
     * @subparam integer   $params:course->timecreated
     * @subparam integer   $params:course->timemodified
     * @subparam integer   $params:course->requested
     * @subparam integer   $params:course->restrictmodules
     * @subparam integer   $params:course->enrolstartdate
     * @subparam integer   $params:course->enrolenddate
     * @subparam string    $params:course->enrol
     * @subparam integer   $params:course->enablecompletion
     * @return array $return ids of new courses
     * @subreturn integer $return:id course id
     */
    static function create_courses($params) {
        global $USER;
        if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) {
            $courses = array();
            foreach ($params as $courseparams) {

                $course = new stdClass();
                if (array_key_exists('idnumber', $courseparams)) {
                    $course->idnumber =  clean_param($courseparams['idnumber'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('shortname', $courseparams)) {
                    $course->shortname =  clean_param($courseparams['shortname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('category', $courseparams)) {
                    $course->category =  clean_param($courseparams['category'], PARAM_INT);
                }

                if (array_key_exists('fullname', $courseparams)) {
                    $course->fullname =  clean_param($courseparams['fullname'], PARAM_TEXT);
                }

                if (array_key_exists('summary', $courseparams)) {
                    $course->summary =  clean_param($courseparams['summary'], PARAM_TEXT);
                }

                if (array_key_exists('format', $courseparams)) {
                    $course->format =  clean_param($courseparams['format'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('startdate', $courseparams)) {
                    $course->startdate =  clean_param($courseparams['startdate'], PARAM_INT);
                }


                if (array_key_exists('sortorder', $courseparams)) {
                    $course->sortorder =  clean_param($courseparams['sortorder'], PARAM_INT);
                }

                if (array_key_exists('showgrades', $courseparams)) {
                    $course->showgrades =  clean_param($courseparams['showgrades'], PARAM_INT);
                }

                if (array_key_exists('modinfo', $courseparams)) {
                    $course->modinfo =  clean_param($courseparams['modinfo'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('newsitems', $courseparams)) {
                    $course->newsitems =  clean_param($courseparams['newsitems'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('guest', $courseparams)) {
                    $course->guest =  clean_param($courseparams['guest'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('metacourse', $courseparams)) {
                    $course->metacourse =  clean_param($courseparams['metacourse'], PARAM_INT);
                }

                if (array_key_exists('password', $courseparams)) {
                    $course->password =  clean_param($courseparams['password'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('enrolperiod', $courseparams)) {
                    $course->enrolperiod =  clean_param($courseparams['enrolperiod'], PARAM_INT);
                }

                if (array_key_exists('defaultrole', $courseparams)) {
                    $course->defaultrole =  clean_param($courseparams['defaultrole'], PARAM_INT);
                }

                if (array_key_exists('enrollable', $courseparams)) {
                    $course->enrollable =  clean_param($courseparams['enrollable'], PARAM_INT);
                }

                if (array_key_exists('numsections', $courseparams)) {
                    $course->numsections =  clean_param($courseparams['numsections'], PARAM_INT);
                }

                if (array_key_exists('expirynotify', $courseparams)) {
                    $course->expirynotify =  clean_param($courseparams['expirynotify'], PARAM_INT);
                }

                if (array_key_exists('notifystudents', $courseparams)) {
                    $course->notifystudents =  clean_param($courseparams['notifystudents'], PARAM_INT);
                }

                if (array_key_exists('expirythreshold', $courseparams)) {
                    $course->expirythreshold =  clean_param($courseparams['expirythreshold'], PARAM_INT);
                }

                if (array_key_exists('marker', $courseparams)) {
                    $course->marker =  clean_param($courseparams['marker'], PARAM_INT);
                }

                if (array_key_exists('maxbytes', $courseparams)) {
                    $course->maxbytes =  clean_param($courseparams['maxbytes'], PARAM_INT);
                }

                if (array_key_exists('showreports', $courseparams)) {
                    $course->showreports =  clean_param($courseparams['showreports'], PARAM_INT);
                }

                if (array_key_exists('visible', $courseparams)) {
                    $course->visible =  clean_param($courseparams['visible'], PARAM_INT);
                }

                if (array_key_exists('hiddensections', $courseparams)) {
                    $course->hiddensections =  clean_param($courseparams['hiddensections'], PARAM_INT);
                }


                if (array_key_exists('groupmode', $courseparams)) {
                    $course->groupmode =  clean_param($courseparams['groupmode'], PARAM_INT);
                }

                if (array_key_exists('groupmodeforce', $courseparams)) {
                    $course->groupmodeforce =  clean_param($courseparams['groupmodeforce'], PARAM_INT);
                }

                if (array_key_exists('defaultgroupingid', $courseparams)) {
                    $course->defaultgroupingid =  clean_param($courseparams['defaultgroupingid'], PARAM_INT);
                }

                if (array_key_exists('lang', $courseparams)) {
                    $course->lang =  clean_param($courseparams['lang'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('theme', $courseparams)) {
                    $course->theme =  clean_param($courseparams['theme'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('cost', $courseparams)) {
                    $course->cost =  clean_param($courseparams['cost'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('currency', $courseparams)) {
                    $course->currency =  clean_param($courseparams['currency'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('timecreated', $courseparams)) {
                    $course->timecreated =  clean_param($courseparams['timecreated'], PARAM_INT);
                }

                if (array_key_exists('timemodified', $courseparams)) {
                    $course->timemodified =  clean_param($courseparams['timemodified'], PARAM_INT);
                }

                if (array_key_exists('requested', $courseparams)) {
                    $course->requested =  clean_param($courseparams['requested'], PARAM_INT);
                }

                if (array_key_exists('restrictmodules', $courseparams)) {
                    $course->restrictmodules =  clean_param($courseparams['restrictmodules'], PARAM_INT);
                }

                if (array_key_exists('enrolstartdate', $courseparams)) {
                    $course->enrolstartdate =  clean_param($courseparams['enrolstartdate'], PARAM_INT);
                }

                if (array_key_exists('enrolenddate', $courseparams)) {
                    $course->enrolenddate =  clean_param($courseparams['enrolenddate'], PARAM_INT);
                }

                if (array_key_exists('enrol', $courseparams)) {
                    $course->enrol =  clean_param($courseparams['enrol'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('enablecompletion', $courseparams)) {
                    $course->enablecompletion =  clean_param($courseparams['enablecompletion'], PARAM_INT);
                }

                $courses[] = create_course($course);

            }
            return $courses;
        }
        else {
            throw new moodle_exception('wscouldnotcreateecoursenopermission');
        }
    }

     /**
     * Delete multiple courses
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params:course->shortname
     * @subparam integer $params:course->id
     * @subparam string $params:course->shortname
     * @subparam string $params:course->idnumber
     * @return boolean result true if success
     */
    static function delete_courses($params) {
        global $DB,$USER;
        if (has_capability('moodle/course:delete', get_context_instance(CONTEXT_SYSTEM))) {
            $courses = array();
            $result = true;
            foreach ($params as $param) {
                $course = new stdClass();
                if (key_exists('id', $param)) {
                    $param['id'] = clean_param($param['id'], PARAM_INT);
                    if (!delete_course($param['id'], false)) {
                        $result = false;
                    }
                } else if (key_exists('idnumber', $param)) {
                    $param['idnumber'] = clean_param($param['idnumber'], PARAM_ALPHANUM);
                    //it doesn't cost that much to retrieve the course here
                    //as it would be done into delete_course()
                    $course = $DB->get_record('course', array('idnumber'=>$param['idnumber']));
                    if (!delete_course($course, false)) {
                        $result = false;
                    }
                } else if (key_exists('shortname', $param)) {
                    $param['shortname'] = clean_param($param['shortname'], PARAM_ALPHANUM);
                    $course = $DB->get_record('course', array('shortname'=>$param['shortname']));
                    if (!delete_course($course, false)) {
                        $result = false;
                    }
                }
            }
            return $result;
        }
        else {
            throw new moodle_exception('wscouldnotdeletecoursenopermission');
        }
    }

    /**
     * Update some courses information
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam integer   $params:course->id
     * @subparam string    $params:course->idnumber
     * @subparam string    $params:course->shortname
     * @subparam integer   $params:course->category
     * @subparam string    $params:course->fullname
     * @subparam string    $params:course->summary
     * @subparam string    $params:course->format
     * @subparam integer   $params:course->startdate
     * @subparam integer   $params:course->sortorder
     * @subparam integer   $params:course->showgrades
     * @subparam string    $params:course->modinfo
     * @subparam string    $params:course->newsitems
     * @subparam string    $params:course->guest
     * @subparam integer   $params:course->metacourse
     * @subparam string    $params:course->password
     * @subparam integer   $params:course->enrolperiod
     * @subparam integer   $params:course->defaultrole
     * @subparam integer   $params:course->enrollable
     * @subparam integer   $params:course->numsections
     * @subparam integer   $params:course->expirynotify
     * @subparam integer   $params:course->notifystudents
     * @subparam integer   $params:course->expirythreshold
     * @subparam integer   $params:course->marker
     * @subparam integer   $params:course->maxbytes
     * @subparam integer   $params:course->showreports
     * @subparam integer   $params:course->visible
     * @subparam integer   $params:course->hiddensections
     * @subparam integer   $params:course->groupmode
     * @subparam integer   $params:course->groupmodeforce
     * @subparam integer   $params:course->defaultgroupingid
     * @subparam string    $params:course->lang
     * @subparam string    $params:course->theme
     * @subparam string    $params:course->cost
     * @subparam string    $params:course->currency
     * @subparam integer   $params:course->timecreated
     * @subparam integer   $params:course->timemodified
     * @subparam integer   $params:course->requested
     * @subparam integer   $params:course->restrictmodules
     * @subparam integer   $params:course->enrolstartdate
     * @subparam integer   $params:course->enrolenddate
     * @subparam string    $params:course->enrol
     * @subparam integer   $params:course->enablecompletion
     * @return boolean result true if success
     */
    static function update_courses($params) {
        global $DB,$USER;

        if (has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM))) {
            $courses = array();
            $result = true;
            foreach ($params as $courseparams) {

                $course = new stdClass();

                if (array_key_exists('idnumber', $courseparams)) {
                    $course->idnumber =  clean_param($courseparams['idnumber'], PARAM_ALPHANUM);
                }

                if (array_key_exists('shortname', $courseparams)) {
                    $course->shortname =  clean_param($courseparams['shortname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('category', $courseparams)) {
                    $course->category =  clean_param($courseparams['category'], PARAM_INT);
                }

                if (array_key_exists('fullname', $courseparams)) {
                    $course->fullname =  clean_param($courseparams['fullname'], PARAM_TEXT);
                }

                if (array_key_exists('summary', $courseparams)) {
                    $course->summary =  clean_param($courseparams['summary'], PARAM_TEXT);
                }

                if (array_key_exists('format', $courseparams)) {
                    $course->format =  clean_param($courseparams['format'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('startdate', $courseparams)) {
                    $course->startdate =  clean_param($courseparams['startdate'], PARAM_INT);
                }


                if (array_key_exists('sortorder', $courseparams)) {
                    $course->sortorder =  clean_param($courseparams['sortorder'], PARAM_INT);
                }

                if (array_key_exists('showgrades', $courseparams)) {
                    $course->showgrades =  clean_param($courseparams['showgrades'], PARAM_INT);
                }

                if (array_key_exists('modinfo', $courseparams)) {
                    $course->modinfo =  clean_param($courseparams['modinfo'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('newsitems', $courseparams)) {
                    $course->newsitems =  clean_param($courseparams['newsitems'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('guest', $courseparams)) {
                    $course->guest =  clean_param($courseparams['guest'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('metacourse', $courseparams)) {
                    $course->metacourse =  clean_param($courseparams['metacourse'], PARAM_INT);
                }

                if (array_key_exists('password', $courseparams)) {
                    $course->password =  clean_param($courseparams['password'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('enrolperiod', $courseparams)) {
                    $course->enrolperiod =  clean_param($courseparams['enrolperiod'], PARAM_INT);
                }

                if (array_key_exists('defaultrole', $courseparams)) {
                    $course->defaultrole =  clean_param($courseparams['defaultrole'], PARAM_INT);
                }

                if (array_key_exists('enrollable', $courseparams)) {
                    $course->enrollable =  clean_param($courseparams['enrollable'], PARAM_INT);
                }

                if (array_key_exists('numsections', $courseparams)) {
                    $course->numsections =  clean_param($courseparams['numsections'], PARAM_INT);
                }

                if (array_key_exists('expirynotify', $courseparams)) {
                    $course->expirynotify =  clean_param($courseparams['expirynotify'], PARAM_INT);
                }

                if (array_key_exists('notifystudents', $courseparams)) {
                    $course->notifystudents =  clean_param($courseparams['notifystudents'], PARAM_INT);
                }

                if (array_key_exists('expirythreshold', $courseparams)) {
                    $course->expirythreshold =  clean_param($courseparams['expirythreshold'], PARAM_INT);
                }

                if (array_key_exists('marker', $courseparams)) {
                    $course->marker =  clean_param($courseparams['marker'], PARAM_INT);
                }

                if (array_key_exists('maxbytes', $courseparams)) {
                    $course->maxbytes =  clean_param($courseparams['maxbytes'], PARAM_INT);
                }

                if (array_key_exists('showreports', $courseparams)) {
                    $course->showreports =  clean_param($courseparams['showreports'], PARAM_INT);
                }

                if (array_key_exists('visible', $courseparams)) {
                    $course->visible =  clean_param($courseparams['visible'], PARAM_INT);
                }

                if (array_key_exists('hiddensections', $courseparams)) {
                    $course->hiddensections =  clean_param($courseparams['hiddensections'], PARAM_INT);
                }

                if (array_key_exists('groupmode', $courseparams)) {
                    $course->groupmode =  clean_param($courseparams['groupmode'], PARAM_INT);
                }

                if (array_key_exists('groupmodeforce', $courseparams)) {
                    $course->groupmodeforce =  clean_param($courseparams['groupmodeforce'], PARAM_INT);
                }

                if (array_key_exists('defaultgroupingid', $courseparams)) {
                    $course->defaultgroupingid =  clean_param($courseparams['defaultgroupingid'], PARAM_INT);
                }

                if (array_key_exists('lang', $courseparams)) {
                    $course->lang =  clean_param($courseparams['lang'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('theme', $courseparams)) {
                    $course->theme =  clean_param($courseparams['theme'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('cost', $courseparams)) {
                    $course->cost =  clean_param($courseparams['cost'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('currency', $courseparams)) {
                    $course->currency =  clean_param($courseparams['currency'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('timecreated', $courseparams)) {
                    $course->timecreated =  clean_param($courseparams['timecreated'], PARAM_INT);
                }

                if (array_key_exists('timemodified', $courseparams)) {
                    $course->timemodified =  clean_param($courseparams['timemodified'], PARAM_INT);
                }

                if (array_key_exists('requested', $courseparams)) {
                    $course->requested =  clean_param($courseparams['requested'], PARAM_INT);
                }

                if (array_key_exists('restrictmodules', $courseparams)) {
                    $course->restrictmodules =  clean_param($courseparams['restrictmodules'], PARAM_INT);
                }

                if (array_key_exists('enrolstartdate', $courseparams)) {
                    $course->enrolstartdate =  clean_param($courseparams['enrolstartdate'], PARAM_INT);
                }

                if (array_key_exists('enrolenddate', $courseparams)) {
                    $course->enrolenddate =  clean_param($courseparams['enrolenddate'], PARAM_INT);
                }

                if (array_key_exists('enrol', $courseparams)) {
                    $course->enrol =  clean_param($courseparams['enrol'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('enablecompletion', $courseparams)) {
                    $course->enablecompletion =  clean_param($courseparams['enablecompletion'], PARAM_INT);
                }

                if (array_key_exists('id', $courseparams)) {
                    $course->id =  clean_param($courseparams['id'], PARAM_INT);
                }

                if (!update_course($course)) {
                    $result = false;
                }

            }
            return $result;
        }
        else {
            throw new moodle_exception('wscouldnotupdatecoursenopermission');
        }

    }

    /**
     * Get course modules
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params:course->id
     * @return array $return course modules
     * @subreturn string $return:id module id
     * @subreturn string $return:name module name
     * @subreturn string $return:type module type
     */
    static function get_course_modules($params, $type=null) {
        global $DB;
        if (has_capability('moodle/course:participate', get_context_instance(CONTEXT_SYSTEM))) {
            $modules = array();
            foreach ($params as $courseparams) {
                if (array_key_exists('id', $courseparams)) {
                    $id =  clean_param($courseparams['id'], PARAM_INT);
                }

                $activities = get_array_of_activities($id);

                foreach ($activities as $activity) {
                    if (empty($type)) {
                        $module = array('id' => $activity->id, 'courseid' => $id, 'name' => $activity->name, 'type' => $activity->mod);
                        $modules[] = $module;
                    }
                    else if ($type=="activities") {
                        if ($activity->mod != "resource" && $activity->mod != "label") {
                            $module = array('id' => $activity->id, 'courseid' => $id, 'name' => $activity->name, 'type' => $activity->mod);
                            $modules[] = $module;
                        }
                    }
                    else if ($type=="resources") {
                        if ($activity->mod == "resource" || $activity->mod == "label") {
                            $module = array('id' => $activity->id, 'courseid' => $id, 'resource' => $activity->name, 'type' => $activity->mod);
                            $modules[] = $module;
                        }
                    }
                }
            }
            return $modules;
        }
        else {
            throw new moodle_exception('wscouldnotgetcoursemodulesnopermission');
        }
    }

     /**
     * Get course activities
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params:course->id
     * @return array $return course activities
     * @subreturn string $return:id activity id
     * @subreturn string $return:name activity name
     * @subreturn string $return:type activity type
     */
    static function get_course_activities($params) {
        course_external::get_course_modules($params, "activities");
    }

    /**
     * Get course resources
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params:course->id
     * @return array $return course resources
     * @subreturn integer $return:id resource id
     * @subreturn string $return:name resource name
     * @subreturn string $return:type resource type
     */
    static function get_course_resources($params) {
        course_external::get_course_modules($params, "resources");
    }

}


