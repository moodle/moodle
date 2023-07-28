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
 * External course API
 *
 * @package    core_course
 * @category   external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

class local_qbcourse extends external_api { 


    public static function getCategoryId($data){
        global  $DB;

        $category = $DB->get_record('course_categories', array("idnumber" => trim($data[0]['categoryid'])));

        if(!empty($category))
        return $category->id;
        else{

            $record['name'] = trim($data[0]['category']);
            $record['description'] = trim($data[0]['category']);
            $record['idnumber'] = trim($data[0]['categoryid']);
            $record['path'] = '/'.trim(strtolower($data[0]['categoryid']));

           $category =  core_course_category::create($record);

           return $category->id;
           
        }
    }

   
    public static function create_course($courses) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        require_once($CFG->libdir . '/completionlib.php');
        
        $courseconfig = get_config('moodlecourse');

        $catid = self::getCategoryId($courses);

        $params =  array(
            'courses' => array(
                    array(
                        'fullname' => trim($courses[0]['fullname']),
                        'shortname' => trim($courses[0]['shortname']),
                        'categoryid' => $catid,
                        'idnumber' => strtolower(trim($courses[0]['shortname'])),
                        'summary' => $courses[0]['summary'],
                        'summaryformat' => 1,
                        'format' => $courseconfig->format,
                        'showgrades' => $courseconfig->showgrades,
                        'newsitems' => $courseconfig->newsitems,
                        'startdate' => time(),
                        'enddate' => '',
                        'numsections' => $courses[0]['numsections'],
                        'maxbytes' => $courseconfig->maxbytes,
                        'showreports' => $courseconfig->showreports,
                        'visible' => 1,
                        'hiddensections' => '',
                        'groupmode' => $courseconfig->groupmode,
                        'groupmodeforce' => $courseconfig->groupmodeforce,
                        'defaultgroupingid' => 0,
                        'enablecompletion' => 1,
                        'completionnotify' => 1,
                        'lang' => 'en',
                       
                        'courseformatoptions' =>'',
                        'customfields' => array(
                            array(
                                'shortname'=> 'level',
                                'value' => trim($courses[0]['level'])
                            ),
                            array(
                                'shortname'=> 'cardcolour',
                                'value' => trim($courses[0]['cardcolour'])
                            ),
                            array(
                                'shortname'=> 'course001',
                                'value' => trim($courses[0]['course001'])
                            )
                        )
                
            ))
                        );

           

        $availablethemes = core_component::get_plugin_list('theme');
        $availablelangs = get_string_manager()->get_list_of_translations();

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['courses'] as $course) {

            // Ensure the current user is allowed to run this function
            $context = context_coursecat::instance($course['categoryid'], IGNORE_MISSING);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $exceptionparam = new stdClass();
                $exceptionparam->message = $e->getMessage();
                $exceptionparam->catid = $course['categoryid'];
                throw new moodle_exception('errorcatcontextnotvalid', 'webservice', '', $exceptionparam);
            }
            require_capability('moodle/course:create', $context);

            // Fullname and short name are required to be non-empty.
            if (trim($course['fullname']) === '') {
                throw new moodle_exception('errorinvalidparam', 'webservice', '', 'fullname');
            } else if (trim($course['shortname']) === '') {
                throw new moodle_exception('errorinvalidparam', 'webservice', '', 'shortname');
            }
            
            // Make sure lang is valid
            if (array_key_exists('lang', $course)) {
                if (empty($availablelangs[$course['lang']])) {
                    throw new moodle_exception('errorinvalidparam', 'webservice', '', 'lang');
                }
                
                if (!has_capability('moodle/course:setforcedlanguage', $context)) {
                    unset($course['lang']);
                }
                
            }
           
            // Make sure theme is valid
            if (array_key_exists('forcetheme', $course)) {
                if (!empty($CFG->allowcoursethemes)) {
                    if (empty($availablethemes[$course['forcetheme']])) {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', 'forcetheme');
                    } else {
                        $course['theme'] = $course['forcetheme'];
                    }
                }
            }
           
            //force visibility if ws user doesn't have the permission to set it
            $category = $DB->get_record('course_categories', array('id' => $course['categoryid']));
            if (!has_capability('moodle/course:visibility', $context)) {
                $course['visible'] = $category->visible;
            }
            
            //set default value for completion
            $courseconfig = get_config('moodlecourse');
            if (completion_info::is_enabled_for_site()) {
                if (!array_key_exists('enablecompletion', $course)) {
                    $course['enablecompletion'] = $courseconfig->enablecompletion;
                }
            } else {
                $course['enablecompletion'] = 0;
            }

           

            $course['category'] = $course['categoryid'];

          
            // Summary format.
            $course['summaryformat'] = $course['summaryformat'];
           
            if (!empty($course['courseformatoptions']) and 1 == 2) {
                
                foreach ($course['courseformatoptions'] as $option) {
                    $course[$option['name']] = $option['value'];
                }
            }
            
            
            // Custom fields.
            if (!empty($course['customfields'])) {
                foreach ($course['customfields'] as $field) {
                   
                    $course['customfield_'.$field['shortname']] = $field['value'];
                }
            } 

            //Note: create_course() core function check shortname, idnumber, category
            $course['id'] = create_course((object) $course)->id;

            $resultcourses[] = array('id' => $course['id'], 'shortname' => $course['shortname']);
        }
       // $DB->set_debug(true);
        $transaction->allow_commit();

        return $resultcourses;
    }

    
    public static function updateSections($cid,$sections,$otherfields,$type=1) {
        global  $DB;

        for($s=0;$s<count($sections);$s++)
        {
        
           if($type == 1){

            $DB->set_field('course_sections', 'name', trim($sections[$s]->title), array('course' => $cid,'section'=>$s+1));
            $DB->set_field('course_sections', 'uid', trim($sections[$s]->uid), array('course' => $cid,'section'=>$s+1));
            self::createpageactivity($cid,$sections,$s);
           }           
           else{
            $DB->set_field('course_sections', 'name', trim($sections[$s]->title), array('uid' => trim($sections[$s]->uid)));
            self::updatepageactivity($cid,$sections,$s);
           }
           
        }

        if(!empty($otherfields)){
            
            $level = $DB->get_record('customfield_field', array('shortname' => 'level'));

            if($level){
                $DB->set_field('customfield_data', 'intvalue', trim($otherfields->level), array('fieldid' => $level->id,'instanceid'=>$cid));
                $DB->set_field('customfield_data', 'value', trim($otherfields->level), array('fieldid' => $level->id,'instanceid'=>$cid));
            }

            $cardcolour = $DB->get_record('customfield_field', array('shortname' => 'cardcolour'));
            if($cardcolour){
                $DB->set_field('customfield_data', 'charvalue', trim($otherfields->cardcolour), array('fieldid' => $cardcolour->id,'instanceid'=>$cid));
                $DB->set_field('customfield_data', 'value', trim($otherfields->cardcolour), array('fieldid' => $cardcolour->id,'instanceid'=>$cid));
            }
            

            $course001 = $DB->get_record('customfield_field', array('shortname' => 'course001'));
            if($course001){
                $DB->set_field('customfield_data', 'charvalue', trim($otherfields->course001), array('fieldid' => $course001->id,'instanceid'=>$cid));
                $DB->set_field('customfield_data', 'value', trim($otherfields->course001), array('fieldid' => $course001->id,'instanceid'=>$cid));
            }
            
        }
        
    }


    public static function createpageactivity($cid,$sections,$sid) {
        global  $DB;

           $sec = $DB->get_record('course_sections', array('course' => $cid,'section'=>$sid+1));

           if(isset($sec->id)){
                $activities = $sections[$sid]->children;

                $acts = '';

                for($a=0;$a<count($activities);$a++){

                    if($activities[$a]->type == 'page'){
                        $page = new stdClass();
                        $page->course = $cid;
                        $page->name = trim($activities[$a]->title);

                        if(empty($activities[$a]->description))
                        $page->intro = '<p>.</p>';
                        else
                        $page->intro = $activities[$a]->description;

                        $page->introformat = 1;

                        if(empty($activities[$a]->content))
                        $page->content = '<p>/</p>';
                        else
                        $page->content = $activities[$a]->content;

                        $page->contentformat = 1;
                        $page->legacyfiles = 0;
                        $page->display = 5;
                        $page->displayoptions = 'a:2:{s:10:"printintro";s:1:"0";s:17:"printlastmodified";s:1:"0";}';
                        $page->revision = 1;
                        $page->timemodified = time();
                        $page->uid = $activities[$a]->uid;

                        $page_id = $DB->insert_record('qubitspage', $page);

                        $cm = new stdClass();

                        $cm->course = $cid;
                        $cm->module = self::qbget_module_id();
                        $cm->instance = $page_id;
                        $cm->section = $sec->id;
                        $cm->added = time();
                        $cm->completion = 1;
                        
                        $cm_id =  $DB->insert_record('course_modules', $cm);

                        $acts = $acts.','.$cm_id;
                    }
                }

                if($acts != ''){
                    $acts = preg_replace('/,/', '', $acts, 1);
                    $DB->set_field('course_sections', 'sequence', $acts, array('id' => $sec->id));
                }
            }
    
        
    }

    public static function updatepageactivity($cid,$sections,$sid) {
        global  $DB;

        $activities = $sections[$sid]->children;

                for($a=0;$a<count($activities);$a++){
                    if($activities[$a]->type == 'page'){
                        
                        $DB->set_field('qubitspage', 'name', trim($activities[$a]->title), array('uid' => $activities[$a]->uid));

                        if(empty($activities[$a]->description))
                        $DB->set_field('qubitspage', 'intro', '<p>.</p>', array('uid' => $activities[$a]->uid));                        
                        else
                        $DB->set_field('qubitspage', 'intro', $activities[$a]->description, array('uid' => $activities[$a]->uid));
                       
                        if(empty($activities[$a]->content))
                        $DB->set_field('qubitspage', 'content', '<p>/</p>', array('uid' => $activities[$a]->uid));
                        else
                        $DB->set_field('qubitspage', 'content', $activities[$a]->content, array('uid' => $activities[$a]->uid));
                        

                    }
                 }
    }

    public static function qbget_module_id($module="qubitspage") {
        global $DB;
        $rec = $DB->get_record('modules', array('name' => $module));
    
        if ($rec) {
            return $rec->id;
        } else {
            return 0;
        }
    }

}