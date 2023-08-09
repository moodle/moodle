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
global $CFG;
require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/moodlelib.php");
require_once("$CFG->libdir/enrollib.php");
require_once($CFG->dirroot . '/mod/qbassign/lib.php');
require_once($CFG->dirroot . '/mod/qbassign/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once("$CFG->dirroot/user/externallib.php");


class local_qubitsbook_external extends external_api {

    public static function get_chapter_content_parameters() {
        return new external_function_parameters(
            array('bookname' => new external_value(PARAM_TEXT, 'Book Name'),
                'chaptername' => new external_value(PARAM_TEXT, 'Chapter Name')
            )
        );
    }

    public static function get_chapter_content($bookname, $chaptername){
        global $CFG, $DB, $USER, $PAGE;

        //validate parameter
        $params = self::validate_parameters(self::get_chapter_content_parameters(),
                        array('bookname' => $bookname, 'chaptername' => $chaptername));

        $bookname = strtolower($bookname);
        $chaptername = strtolower($chaptername);
        $data = "";
        $dfpath = "$CFG->dirroot/local/qubitsbook/data";

        if($bookname=="science"){
            switch($chaptername){
                case "chapter1":
                    $data = file_get_contents("$dfpath/python.mdx");
                    break;
                case "chapter2":
                    $data = file_get_contents("$dfpath/datascience.mdx");
                    break;
                case "chapter3":
                    $data = file_get_contents("$dfpath/sql.mdx");
                    break;
                default:
                    $data = "";
                    break;
            }
        }
        
        $result = array(
            "data" => $data
        );

        return $result;
    }

    public static function get_chapter_content_returns() {
        return new external_single_structure(
            array(
                "data" => new external_value(PARAM_RAW, 'Chapter Data')
            )
        );
    }

    // CREATE ASSIGNMENT SERVICE CALL
    public static function create_assignment_service_parameters()
    {
        return new external_function_parameters(
            array(
            'courseid' => new external_value(PARAM_TEXT, 'Course Id'),
            'siteid' => new external_value(PARAM_TEXT, 'Site Id'),
            'chapterid' => new external_value(PARAM_TEXT, 'Section Id'),
            'assign_name' => new external_value(PARAM_TEXT, 'Assignment Name'),
            'duedate' => new external_value(PARAM_TEXT, 'Due date'),
            'submissionfrom' => new external_value(PARAM_TEXT, 'Submission From'),
            'grade_duedate' => new external_value(PARAM_TEXT, 'Grade Due Date'),
            'grade' => new external_value(PARAM_TEXT, 'Grade'),
            'description' => new external_value(PARAM_TEXT, 'Description'),
            'submission_type' => new external_value(PARAM_TEXT, 'Submission Type'),
            'submissionstatus' => new external_value(PARAM_TEXT, 'Submission Status',VALUE_OPTIONAL),
            'wrdlmit' => new external_value(PARAM_TEXT, 'Word Limit',VALUE_OPTIONAL),
            'uniquefield' => new external_value(PARAM_TEXT, 'Unique Field'),
            )
        );
    }

    public static function create_assignment_service($courseid,$siteid,$chapterid,$assign_name,$duedate,$submissionfrom,$grade_duedate,$grade,$description,$submission_type,$submissionstatus,$wrdlmit,$uniquefield)
    {
        global $DB,$CFG;
        $check_uniquefield = $DB->get_record('qbassign', array('uid' => $uniquefield));
        if(isset($check_uniquefield->id))
        { 
            //Update assignment details if unique field already present
            $check_coursemodulefield = $DB->get_record('course_modules', array('instance' => $check_uniquefield->id,'course'=>$courseid));

            //PASS our web service values to the lib file
            $formdata = (object) array(
            'name' => $assign_name,
            'timemodified' => time(),
            'duedate' => strtotime($duedate),
            'course' => $courseid,
            'introformat'=>'1',
            'intro' => $description,
            'coursemodule' => $check_coursemodulefield->id,
            'submissiondrafts' =>0,
            'requiresubmissionstatement' =>0,
            'sendnotifications' => 0,
            'sendlatenotifications' =>0,
            'cutoffdate' => 0,
            'gradingduedate' => strtotime($grade_duedate),
            'allowsubmissionsfromdate' => strtotime($submissionfrom),
            'grade' =>$grade,
            'teamsubmission' =>0,
            'requireallteammemberssubmit' =>0,
            'blindmarking' => 0,
            'markingworkflow' =>0,
            'instance' => $check_uniquefield->id,
            'add' => 0,
            'update' => $check_coursemodulefield->id
            );
            $returnid = qbassign_update_instance($formdata,null);


            $wrdlmit = ($wrdlmit!='')?$wrdlmit:'0';

            if($submission_type == 'onlinetext')
            {
                $sqlupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=1 WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='enabled' AND qbassignment=".$check_uniquefield->id;
                $getpluginconfigtxt = $DB->execute($sqlupdate);
                $submission_status = ($submissionstatus=='yes')?1:0;

                $sqlwrdupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=".$submission_status." WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='wordlimitenabled' AND qbassignment=".$check_uniquefield->id;
                $updatewrd = $DB->execute($sqlwrdupdate);

                $sqlwrdmlmtupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=".$wrdlmit." WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='wordlimit' AND qbassignment=".$check_uniquefield->id;
                $updatewrdlmt = $DB->execute($sqlwrdmlmtupdate);                                          
            }   
            if($submission_filetype == 'onlinefile') 
            {
                $submission_filestatus = ($submissionfilestatus=='yes')?1:0;
                $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'file','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$check_uniquefield->id));

                if(isset($getactive_online))
                {                
                   $updateactivityonline = new stdClass();
                   $updateactivityonline->id = $getactive_online->id;
                   $updateactivityonline->value = $submission_filestatus;           
                   $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                }
                else
                { 
                    $updateactivityonline =  array(
                    'qbassignment' => $returnid,
                    'plugin' => 'file',
                    'subtype' => 'qbassignsubmission',
                    'name' => 'enabled',
                    'value' => $submission_filestatus
                    );
                    $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updatesactivityonline);
                }
               
                $submissionfilelimit =  array(
                'qbassignment' => $check_uniquefield->id,
                'plugin' => 'file',
                'subtype' => 'qbassignsubmission',
                'name' => 'maxfilesubmissions',
                'value' => 10
                );
                $onlinetext_flimit = $DB->insert_record('qbassign_plugin_config', $submissionfilelimit);

                $submissionfiletype =  array(
                'qbassignment' => $check_uniquefield->id,
                'plugin' => 'file',
                'subtype' => 'qbassignsubmission',
                'name' => 'filetypeslist',
                'value' => '*'
                );
                $onlinetext_tyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfiletype);

                $returnbytes = self::getbytevalue('40mb');

                $submissionfilebytetype =  array(
                'qbassignment' => $check_uniquefield->id,
                'plugin' => 'file',
                'subtype' => 'qbassignsubmission',
                'name' => 'maxsubmissionsizebytes',
                'value' => $returnbytes
                );
                $onlinetext_tybyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfilebytetype);
            } 
            if($submission_codetype == 'codeblock') 
            {
                //CODE BLOCK
                $submission_codestatus = ($submissioncodestatus=='yes')?1:0;
                $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'codeblock','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$check_uniquefield->id));
                if(isset($getactive_online))
                {
                   $updateactivityonline = new stdClass();
                   $updateactivityonline->id = $getactive_online->id;
                   $updateactivityonline->value = $submission_codestatus;           
                   $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                }
                else
                {
                    $updateactivityonline =  array(
                    'qbassignment' => $check_uniquefield->id,
                    'plugin' => 'codeblock',
                    'subtype' => 'qbassignsubmission',
                    'name' => 'enabled',
                    'value' => $submission_codestatus
                    );
                    $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updatesactivityonline);
                }           
            } 
            $lti_updated = [                        
                        'message'=>'Success update here',
                        'assignment_id' =>$check_uniquefield->id,
                        'uniquefield' => $uniquefield
                        ];
            return $lti_updated;
        }        
        else
        {  
            //Add assignment details if unique field not present
            $getcoursemoduleslist_courses = get_coursemodules_in_course('qbassign', $courseid, '');
            $getcoursemoduleslist_courses_last = end($getcoursemoduleslist_courses);
            $sections = $DB->get_record('course_sections', array('course'=>$courseid,'section' => $chapterid));
            $section_id = $sections->id;
            $sequence_column = $sections->sequence;
            
            $sequencing = array();
            $coursemodule = $getcoursemoduleslist_courses_last->id +1;

            //Get QBassign Module
            $get_modulelist = $DB->get_record('modules', array('name' => 'qbassign'));

            //INSERT instance into course modules
            $flags = array(
            'course' => $courseid,
            'module' => $get_modulelist->id,
            'instance' => '',
            'section' => $section_id,
            'added' => time()
            );       
            $courseinsertid = $DB->insert_record('course_modules', $flags);

            $updatedata = new stdClass();
            $updatedata->id = $section_id;
            if($sequence_column=='')
            {
               $updatedata->sequence = $courseinsertid;        
            }
            else
            {
                $sequencing = explode(",",$sequence_column);
                array_push($sequencing,$courseinsertid);
                $updatedata->sequence = implode(',', $sequencing);
            }
            
            $updatedata->section = $chapterid;        
            $coursesectionupdate = $DB->update_record('course_sections', $updatedata);


            $getcoursecontext = $DB->get_record('context', array('instanceid' => $courseid,'depth'=> 3));
            $coursepath = $getcoursecontext->path;

            $recorder =  array(
                'contextlevel' => 70, //CONTEXT_MODULE = 70,CONTEXT_SYSTEM = 10,CONTEXT_BLOCK = 80
                'instanceid' => $courseinsertid,
                'path' => $coursepath.'/',
                'depth' => 4,
                'locked' => 0
            );
            $coursecontextinsertid = $DB->insert_record('context', $recorder);

            $getcoursecontextpath = $DB->get_record('context', array('id' => $coursecontextinsertid,'depth' => 4));

            $updatecontextdata = new stdClass();
            $updatecontextdata->id = $coursecontextinsertid;
            $updatecontextdata->path = $getcoursecontextpath->path.$coursecontextinsertid; 
            $coursesectionupdate = $DB->update_record('context', $updatecontextdata);

            $gradeareas = array(
                'contextid' =>$coursecontextinsertid,
                'component' =>'mod_qbassign',
                'areaname' =>'submissions'
            );
            $grading_areasupdate = $DB->insert_record('grading_areas', $gradeareas);
            
            //PASS our web service values to the lib file
            $formdata = (object) array(
                'name' => $assign_name,
                'timemodified' => time(),
                'duedate' => strtotime($duedate),
                'course' => $courseid,
                'introformat'=>'1',
                'intro' => $description,
                'coursemodule' => $courseinsertid,
                'submissiondrafts' =>0,
                'requiresubmissionstatement' =>0,
                'sendnotifications' => 0,
                'sendlatenotifications' =>0,
                'cutoffdate' => 0,
                'gradingduedate' => strtotime($grade_duedate),
                'allowsubmissionsfromdate' => strtotime($submissionfrom),
                'grade' =>$grade,
                'teamsubmission' =>0,
                'requireallteammemberssubmit' =>0,
                'blindmarking' => 0,
                'markingworkflow' =>0,
            );
            $returnid = qbassign_add_instance($formdata,null);

            //update assignment id into course modules
            $updatecoursemoduledata = new stdClass();
            $updatecoursemoduledata->id = $courseinsertid;
            $updatecoursemoduledata->instance = $returnid;
            $coursesectionupdate = $DB->update_record('course_modules', $updatecoursemoduledata);

            $wrdlmit = (isset($wrdlmit))?$wrdlmit:'';

            if($submission_type == 'onlinetext')
            {
               $sqlupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=1 WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='enabled' AND qbassignment=".$returnid;
                $getpluginconfigtxt = $DB->execute($sqlupdate);
                $submission_status = ($submissionstatus=='yes')?1:0;
                $submissionlimit =  array(
                'qbassignment' => $returnid,
                'plugin' => 'onlinetex',
                'subtype' => 'qbassignsubmission',
                'name' => 'wordlimit',
                'value' => $wrdlmit
                );
                $onlinetext_limit = $DB->insert_record('qbassign_plugin_config', $submissionlimit);
                $submissionlimits =  array(
                'qbassignment' => $returnid,
                'plugin' => 'onlinetex',
                'subtype' => 'qbassignsubmission',
                'name' => 'wordlimitenabled',
                'value' => $submission_status
                );
                $onlinetext_limiter = $DB->insert_record('qbassign_plugin_config', $submissionlimits);                                   
            }   
            if($submission_filetype == 'onlinefile') 
            {
                $submission_filestatus = ($submissionfilestatus=='yes')?1:0;
                $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'file','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$returnid));

                if(isset($getactive_online))
                {                
                   $updateactivityonline = new stdClass();
                   $updateactivityonline->id = $getactive_online->id;
                   $updateactivityonline->value = $submission_filestatus;           
                   $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                }
                else
                { 
                    $updateactivityonline =  array(
                    'qbassignment' => $returnid,
                    'plugin' => 'file',
                    'subtype' => 'qbassignsubmission',
                    'name' => 'enabled',
                    'value' => $submission_filestatus
                    );
                    $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updatesactivityonline);
                }
               
                $submissionfilelimit =  array(
                'qbassignment' => $returnid,
                'plugin' => 'file',
                'subtype' => 'qbassignsubmission',
                'name' => 'maxfilesubmissions',
                'value' => 10
                );
                $onlinetext_flimit = $DB->insert_record('qbassign_plugin_config', $submissionfilelimit);

                $submissionfiletype =  array(
                'qbassignment' => $returnid,
                'plugin' => 'file',
                'subtype' => 'qbassignsubmission',
                'name' => 'filetypeslist',
                'value' => '*'
                );
                $onlinetext_tyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfiletype);

                $returnbytes = self::getbytevalue('40mb');

                $submissionfilebytetype =  array(
                'qbassignment' => $returnid,
                'plugin' => 'file',
                'subtype' => 'qbassignsubmission',
                'name' => 'maxsubmissionsizebytes',
                'value' => $returnbytes
                );
                $onlinetext_tybyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfilebytetype);
            } 
            if($submission_codetype == 'codeblock') 
            {
                //CODE BLOCK
                $submission_codestatus = ($submissioncodestatus=='yes')?1:0;
                $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'codeblock','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$returnid));
                if(isset($getactive_online))
                {
                   $updateactivityonline = new stdClass();
                   $updateactivityonline->id = $getactive_online->id;
                   $updateactivityonline->value = $submission_codestatus;           
                   $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                }
                else
                {
                    $updateactivityonline =  array(
                    'qbassignment' => $returnid,
                    'plugin' => 'codeblock',
                    'subtype' => 'qbassignsubmission',
                    'name' => 'enabled',
                    'value' => $submission_codestatus
                    );
                    $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updatesactivityonline);
                }           
            } 

            $modl = $DB->get_record('course_modules', array('course' => $courseid,'instance'=>$returnid));
            $mod_id = $modl->id;

            $contxtl = $DB->get_record('context', array('instanceid'=>$mod_id));
            $DB->set_field('qbassign', 'uid', $uniquefield, array('id' => $returnid));
            $lti_updated = [                        
                            'message'=>'Success here',
                            'assignment_id' =>$returnid,
                            'uniquefield' => $uniquefield
                            ];
            return $lti_updated;
        }
    }

    public static function create_assignment_service_returns()
    {
        return new external_single_structure(
                array(
                    'assignment_id' => new external_value(PARAM_TEXT, 'assignment id'),
                    'message'=> new external_value(PARAM_TEXT, 'success message'),
                    'uniquefield'=> new external_value(PARAM_TEXT, 'Unique Field')
                )
            );
    }

    public function getbytevalue($val)
    {
        $bytearray = array('41943040'=>'40mb','20mb'=>'20971520','10485760'=>'10mb','5242880'=>'5mb','2097152'=>'2mb','1048576'=>'1mb','512000'=>'500kb','102400'=>'100kb','51200'=>'50kb','10240'=>'10kb');
        $byteval = array_search($val,$bytearray);
        return $byteval;
    }

    

    public static function getsingleassignment($uniquefield_assign)
    {
        require_once('../../config.php');
        global $DB,$CFG,$USER,$CONTEXT;
       
        //Get activity unique field details
        $get_customfield = $DB->get_record('customfield_data', array('charvalue' => $uniquefield_assign));

        if(isset($get_customfield))
        {
            $customfield_activity = $get_customfield->instanceid;

            //Get activity Module details
            $get_coursefield = $DB->get_record('course_modules', array('id' => $customfield_activity));
            $instance_id = $get_coursefield->instance;

            //Get assignment Module details
            $get_assignmentdetails = $DB->get_record('qbassign', array('id' => $instance_id));

            //Get assignment submission details
            $get_assignmentsubmission_details = $DB->get_record('qbassign_submission', array('userid' => $USER->id,'qbassignment'=>$get_assignmentdetails->id));

            //Get submission type details (file,onlinetex,codeblock)
            $sql = "SELECT * FROM ".$CFG->prefix."qbassign_plugin_config WHERE `qbassignment`=".$get_assignmentdetails->id." AND `subtype`='qbassignsubmission' AND name='enabled' AND value=1 AND (`plugin`='file' OR plugin='onlinetex' OR plugin='codeblock')";
            $getpluginconfig = $DB->get_records_sql($sql);
            $countsql = count($getpluginconfig);
            if($countsql>0)
            { 
                foreach($getpluginconfig as $config)
                {
                    if($config->plugin=='onlinetex')
                    {
                       $get_qbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'wordlimit','plugin'=>'onlinetex'));

                       $submissintype[] = array(
                        'type'=> $config->plugin,
                        'wordlimit' => ($config->plugin=='onlinetex')?$get_qbdetails->value:''                    
                        ); 
                    }
                    if($config->plugin=='file')
                    {
                        $get_fbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'maxfilesubmissions','plugin'=>'file'));

                        $get_fmbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'maxsubmissionsizebytes','plugin'=>'file'));

                           $submissintype[] = array(
                            'type'=> $config->plugin,
                            'maxfileallowed' => ($config->plugin=='file')?$get_fbdetails->value:'',
                            'maxfilesize' => ($config->plugin=='file')?$get_fmbdetails->value:''                    
                            ); 
                    }
                }
            }
            $context = context_course::instance($get_assignmentdetails->course);
            $roles = get_user_roles($context, $USER->id, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;

            //print_r($rolename);
            $userdetails = array(
                'userid' => $USER->id,
                'email' => $USER->email,
                'username' => $USER->username,
                'sesskey' => $USER->sesskey,
                'role' => $rolename
            );

            $returnarray = array(
                'course_id' => $get_assignmentdetails->course,            
                'assignmentid' => $get_assignmentdetails->id,
                'assignment_title' => $get_assignmentdetails->name,
                'assignment_activitydesc' => $get_assignmentdetails->intro,
                'duedate' => $get_assignmentdetails->duedate,
                'allowsubmissionsfromdate' => $get_assignmentdetails->allowsubmissionsfromdate,
                'assign_uniquefield' => $uniquefield_assign,
                'submission_status' => $get_assignmentsubmission_details->status,
                'submissiontypes' => $submissintype
            );
            echo '<pre>';


            $contextsystem = context_module::instance($customfield_activity); // course module
            $checkenrol = is_enrolled($contextsystem, $USER, 'mod/assignment:submit');
            if($checkenrol)
            return json_encode(['status' => 1,'message' => 'success','userdetails'=>$userdetails,'assignmentdetails'=>$returnarray]);
            else
                return json_encode(['status' => 0,'message' => 'Not enrol']);
        }
        else
        {           
            return json_encode(['status' => 0,'message' => 'Unique Field not match']);
        }
    }

    //get_assignment_service

    public static function get_assignment_service_parameters()
    {
         return new external_function_parameters(
            array(
            'uniquefield' => new external_value(PARAM_TEXT, 'Unique Field')
         )
        );
    }

    public static function get_assignment_service($uniquefield)
    {        
        require_once('../../config.php');
        global $DB,$CFG,$USER,$CONTEXT;
       
        //Get activity unique field details       
        $get_assignmentdetails = $DB->get_record('qbassign', array('uid' => $uniquefield));

        if($get_assignmentdetails->id!='')
        { 
            $assignid = $get_assignmentdetails->id;
            $courseid = $get_assignmentdetails->course;

            //Get activity Module details
            $get_coursefield = $DB->get_record('course_modules', array('instance' => $assignid,'course' => $courseid));
            $moduleid = $get_coursefield->id;

            //Get assignment submission details
            $get_assignmentsubmission_details = $DB->get_record('qbassign_submission', array('userid' => $USER->id,'qbassignment'=>$get_assignmentdetails->id));

            $getonline_content = $DB->get_record('qbassignsubmission_onlinetex', array('submission' => $get_assignmentsubmission_details->id,'qbassignment'=>$get_assignmentdetails->id));

            //Get submission type details (file,onlinetex,codeblock)
           // $sql = "SELECT * FROM ".$CFG->prefix."qbassign_plugin_config WHERE `qbassignment`=".$get_assignmentdetails->id." AND `subtype`='qbassignsubmission' AND name='enabled' AND value=1 AND (`plugin`='file' OR plugin='onlinetex' OR plugin='codeblock')";
            $sql = "SELECT * FROM {qbassign_plugin_config} WHERE qbassignment = :qbdetid AND subtype = :subtype ";
            $sql .= " AND name = :name AND value = :value ";
            $sql .= " AND (plugin = :type1 OR plugin = :type2 OR plugin = :type3)";
            $getpluginconfig = $DB->get_records_sql($sql,
            [
                'qbdetid' => $get_assignmentdetails->id,
                'subtype' => 'qbassignsubmission',
                'name' => 'enabled',
                'value' => '1',
                'type1' => 'file',
                'type2' => 'onlinetex',
                'type3' => 'codeblock'
            ]
        );

            $countsql = count($getpluginconfig);
            if($countsql>0)
            { 
                foreach($getpluginconfig as $config)
                {
                    if($config->plugin=='onlinetex')
                    { 
                       $get_qbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'wordlimit','plugin'=>'onlinetex'));

                       $submissintype = array(
                        'type'=> $config->plugin,
                        'wordlimit' => ($config->plugin=='onlinetex')?$get_qbdetails->value:''                    
                        ); 
                    }
                    if($config->plugin=='file')
                    {
                        $get_fbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'maxfilesubmissions','plugin'=>'file'));

                        $get_fmbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'maxsubmissionsizebytes','plugin'=>'file'));

                           $submissintype = array(
                            'type'=> $config->plugin,
                            'maxfileallowed' => ($config->plugin=='file')?$get_fbdetails->value:'',
                            'maxfilesize' => ($config->plugin=='file')?$get_fmbdetails->value:''                    
                            ); 
                    }
                }
            }
            $context = context_course::instance($get_assignmentdetails->course);
            $roles = get_user_roles($context, $USER->id, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;

            
            $userdetails = array(
                'userid' => $USER->id,
                'email' => $USER->email,
                'username' => $USER->username,
                'sesskey' => $USER->sesskey,
                'role' => $rolename
            );
            $returnarray = array(
                'course_id' => $get_assignmentdetails->course,            
                'assignmentid' => $get_assignmentdetails->id,
                'assignment_title' => $get_assignmentdetails->name,
                'assignment_activitydesc' => $get_assignmentdetails->intro,
                'duedate' => $get_assignmentdetails->duedate,
                'allowsubmissionsfromdate' => $get_assignmentdetails->allowsubmissionsfromdate,
                'assign_uniquefield' => $uniquefield,
                'submission_status' => ($get_assignmentsubmission_details->status=='new')?0:1,
                'studentsubmitted_content' => $getonline_content->onlinetex,
                'submissiontypes' => $submissintype
            );

            $contextsystem = context_module::instance($moduleid);
            $checkenrol = is_enrolled($contextsystem, $USER, 'mod/assignment:submit');
            if($checkenrol)
            { 
                $lti_updated = [                        
                        'message'=>'Assignment details',
                        'userdetails' => $userdetails,
                        'assignmentdetails' => $returnarray
                        ]; 
                return $lti_updated;                      
            }            
            else
            { 
                throw new moodle_exception('This user not enrolled', 'error');
            }
            
        }
        else
        { 
            throw new moodle_exception('Invalid assignment uniqueid', 'error');
        }
        
    }

    public static function get_assignment_service_returns()
    {
        return new external_single_structure(
                        array(
                        'message' => new external_value(PARAM_RAW, 'success'),
                        'userdetails' => new external_single_structure(
                                    array(
                                    'userid' => new external_value(PARAM_RAW, 'USER id',VALUE_OPTIONAL),
                                    'email' => new external_value(PARAM_RAW, 'User Email',VALUE_OPTIONAL),
                                    'username' => new external_value(PARAM_RAW, 'Username',VALUE_OPTIONAL),
                                    'sesskey' => new external_value(PARAM_RAW, 'Session Key',VALUE_OPTIONAL),
                                    'role' => new external_value(PARAM_RAW, 'User Role',VALUE_OPTIONAL)
                                    )
                                ),
                                'User Details', VALUE_OPTIONAL,
                        'assignmentdetails' => new external_single_structure(
                                    array(
                                    'course_id' => new external_value(PARAM_RAW, 'course id',VALUE_OPTIONAL),
                                    'assignmentid' => new external_value(PARAM_RAW, 'Assignment ID',VALUE_OPTIONAL),
                                    'assignment_title' => new external_value(PARAM_RAW, 'Assignment Name',VALUE_OPTIONAL),
                                    'assignment_activitydesc' => new external_value(PARAM_RAW, 'Assignment Question',VALUE_OPTIONAL),
                                    'duedate' => new external_value(PARAM_RAW, 'Last date',VALUE_OPTIONAL),
                                    'allowsubmissionsfromdate' => new external_value(PARAM_RAW, 'Start Submission date',VALUE_OPTIONAL),
                                    'assign_uniquefield' => new external_value(PARAM_RAW, 'Unique field',VALUE_OPTIONAL),
                                    'submission_status' => new external_value(PARAM_RAW, 'Submission Status (New,submitted)',VALUE_OPTIONAL),
                                    'studentsubmitted_content' => new external_value(PARAM_RAW, 'Submission Content',VALUE_OPTIONAL),
                                    'submissiontypes' => new external_single_structure(
                                        array(
                                         'type' => new external_value(PARAM_RAW, 'Submission Type (text,file,codblock)',VALUE_OPTIONAL),
                                         'wordlimit' =>new external_value(PARAM_RAW, 'Text Limit',VALUE_OPTIONAL)
                                        )
                                    ),
                                    'Submission Type Details', VALUE_OPTIONAL
                                    )
                                ),
                                'Assignment Details', VALUE_OPTIONAL
                        )
                );

 
       
    }
}