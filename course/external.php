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
require_once(dirname(dirname(__FILE__)) . '/course/lib.php');

/**
 * course webservice api
 *
 * @author Jerome Mouneyrac
 */
final class course_external {

    /**
     * Retrieve courses
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam integer $params:search->id - the id to search
     * @subparam string $params:search->idnumber - the idnumber to search
     * @subparam string $params:search->shortname - the shortname to search
     * @return object $return
     * @subparam integer $return:course->id
     * @subparam string $return:course->category
     * @subparam string $return:course->summary
     * @subparam string $return:course->format
     * @subparam string $return:course->numsections
     * @subparam string $return:course->startdate
     * @subparam string $return:course->fullname
     * @subparam string $return:course->shortname
     * @subparam string $return:course->idnumber
     */
    static function get_courses($params) {
        global $USER;
        if (has_capability('moodle/course:view', get_context_instance(CONTEXT_SYSTEM))) {
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
     * @subparam integer $params:course->category
     * @subparam string $params:course->summary
     * @subparam string $params:course->format
     * @subparam string $params:course->numsections
     * @subparam string $params:course->startdate
     * @subparam string $params:course->fullname
     * @subparam string $params:course->shortname
     * @subparam string $params:course->idnumber
     * @return array $return ids of new courses
     * @subreturn integer $return:id course id
     */
    static function create_courses($params) {
        global $USER;
        if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) {
            $courses = array();
            foreach ($params as $courseparams) {

                $course = new stdClass();
                if (array_key_exists('category', $courseparams)) {
                    $course->category =  clean_param($courseparams['category'], PARAM_INT);
                }

                if (array_key_exists('idnumber', $courseparams)) {
                    $course->idnumber =  clean_param($courseparams['idnumber'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('shortname', $courseparams)) {
                    $course->shortname =  clean_param($courseparams['shortname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('format', $courseparams)) {
                    $course->format =  clean_param($courseparams['format'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('fullname', $courseparams)) {
                    $course->fullname =  clean_param($courseparams['fullname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('summary', $courseparams)) {
                    $course->summary =  clean_param($courseparams['summary'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('numsections', $courseparams)) {
                    $course->numsections =  clean_param($courseparams['numsections'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('startdate', $courseparams)) {
                    $course->startdate =  clean_param($courseparams['startdate'], PARAM_ALPHANUMEXT);
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
     * @subparam integer $params:course->category
     * @subparam string $params:course->summary
     * @subparam string $params:course->format
     * @subparam string $params:course->numsections
     * @subparam string $params:course->startdate
     * @subparam string $params:course->fullname
     * @subparam string $params:course->shortname
     * @subparam string $params:course->idnumber
     * @return boolean result true if success
     */
    static function update_courses($params) {
        global $DB,$USER;

        if (has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM))) {
            $courses = array();
            $result = true;
            foreach ($params as $courseparams) {

                $course = new stdClass();
                if (array_key_exists('category', $courseparams)) {
                    $course->category =  clean_param($courseparams['category'], PARAM_INT);
                }

                if (array_key_exists('idnumber', $courseparams)) {
                    $course->idnumber =  clean_param($courseparams['idnumber'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('shortname', $courseparams)) {
                    $course->shortname =  clean_param($courseparams['shortname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('format', $courseparams)) {
                    $course->format =  clean_param($courseparams['format'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('fullname', $courseparams)) {
                    $course->fullname =  clean_param($courseparams['fullname'], PARAM_TEXT);
                }

                if (array_key_exists('summary', $courseparams)) {
                    $course->summary =  clean_param($courseparams['summary'], PARAM_TEXT);
                }

                if (array_key_exists('numsections', $courseparams)) {
                    $course->numsections =  clean_param($courseparams['numsections'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('startdate', $courseparams)) {
                    $course->startdate =  clean_param($courseparams['startdate'], PARAM_ALPHANUMEXT);
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

}

?>
