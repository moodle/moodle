<?php

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->dirroot . '/enrol/externallib.php');

//$ref_csname = 'DCL02'; // Reference Course Short name
//$cohort_idnumber = 'bfsajman';
// http://qubits.localhost.com/local/qbmanifest/clonefilegrade.php?cshortname=DPL06&cohortid=ajyalmbz
// Example http://qubits.localhost.com/local/qbmanifest/clonefilegrade.php?cshortname=DCL03&cohortid=bfsajman

$ref_csname = required_param('cshortname', PARAM_ALPHANUMEXT);
$cohort_idnumber = required_param('cohortid', PARAM_ALPHANUMEXT);

$farr = array(
    "DCL01" => "/books-json/digichamps/dcl01.json",
    "DCL02" => "/books-json/digichamps/dcl02.json",
    "DCL03" => "/books-json/digichamps/dcl03.json",
    "DCL04" => "/books-json/digichamps/dcl04.json",
    "DCL05" => "/books-json/digichamps/dcl05.json",
    "DCL06" => "/books-json/digichamps/dcl06.json",
    "DCL07" => "/books-json/digichamps/dcl07.json",
    "DCL08" => "/books-json/digichamps/dcl08.json",
    "DCL09" => "/books-json/digichamps/dcl09.json",
    "DCL10" => "/books-json/digichamps/dcl10.json",
    "DCL11" => "/books-json/digichamps/dcl11.json",
    "DCL12" => "/books-json/digichamps/dcl12.json",
    "DPL01" => "/books-json/digipro/dpl01.json",
    "DPL02" => "/books-json/digipro/dpl02.json",
    "DPL03" => "/books-json/digipro/dpl03.json",
    "DPL04" => "/books-json/digipro/dpl04.json",
    "DPL05" => "/books-json/digipro/dpl05.json",
    "DPL06" => "/books-json/digipro/dpl06.json",
    "DPL07" => "/books-json/digipro/dpl07.json",
    "DPL08" => "/books-json/digipro/dpl08.json",
    "DPL09" => "/books-json/digipro/dpl09.json",
    "DPL10" => "/books-json/digipro/dpl10.json",
    "DPL11" => "/books-json/digipro/dpl11.json",
    "DPL12" => "/books-json/digipro/dpl12.json",
    "DJL01" => "/books-json/djl01.json"
);


$parent_course = $DB->get_record("course",[
    "shortname" => $ref_csname
]);

$current_course = $DB->get_record("course",[
    "shortname" => $ref_csname.$cohort_idnumber
]);

$cfilename = $farr[$ref_csname];

$coursefile = $CFG->dirroot.$cfilename;
$course_fcontent = file_get_contents($coursefile);
$accnt = json_decode($course_fcontent, true);
$oldmncourse = $accnt[0]["book"];
$newmnfstcourse = $oldmncourse;
$newmnfstcourse["code"] = $newmnfstcourse["code"].$cohort_idnumber;
$newchapters = $newmnfstcourse["chapters"];
$newmnfstcourse["chapters"] = array_map('addcohort_uid_item', $newchapters);

$pc_ctxt_sql = "SELECT qbc.id ctxt_id, qc.cmdl_id, qc.course_id, qc.qb_assgn_id, qc.uid FROM {context} qbc
JOIN (SELECT cmdls.id cmdl_id, cmdls.course course_id, qba.id qb_assgn_id, qba.uid FROM {course_modules} cmdls  
LEFT JOIN  {qbassign} qba ON  cmdls.course = qba.course AND cmdls.instance = qba.id
WHERE qba.course = :cid) qc
ON qbc.instanceid = qc.cmdl_id AND qbc.contextlevel = :ctxt_mdl";

$orecds = $DB->get_records_sql($pc_ctxt_sql,
    [
        "cid" => $parent_course->id,
        "ctxt_mdl" => 70
    ]
);

$nrecds = $DB->get_records_sql($pc_ctxt_sql,
    [
        "cid" => $current_course->id,
        "ctxt_mdl" => 70
    ]
);

$orecds = json_decode(json_encode($orecds), true);
$nrecds = json_decode(json_encode($nrecds), true);
$orecds1 = array();
$nrecds1 = array();
$combinedrecs = array();
$pcourse_id = $parent_course->id;
$ccourse_id = $current_course->id;

foreach($nrecds as $k1 => $v1){
    $cuid1 = $v1["uid"];
    $nrecds1[$cuid1] = $v1;
}

foreach($orecds as $k => $v){
    $cuid = $v["uid"];
    $orecds1[$cuid] = $v;
    $ky1 = $cuid.'-'.$cohort_idnumber;
    $ky2 = $cuid.'00-'.$cohort_idnumber;
    if(isset($nrecds1[$ky1])){
        $nrow = $nrecds1[$ky1];
        $combinedrecs[] = array(
           "olduid" => $cuid,
           "newuid" => $ky1,
           "old_course_id" => $v["course_id"],
           "new_course_id" => $nrow["course_id"],
           "old_ctxt_id" => $v["ctxt_id"],
           "new_ctxt_id" => $nrow["ctxt_id"],
           "old_cmdl_id" => $v["cmdl_id"],
           "new_cmdl_id" => $nrow["cmdl_id"],
           "old_qb_assgn_id" => $v["qb_assgn_id"],
           "new_qb_assgn_id" => $nrow["qb_assgn_id"]
        );
    }elseif(isset($nrecds1[$ky2])){
        $nrow = $nrecds1[$ky2];
        $combinedrecs[] = array(
            "olduid" => $cuid,
            "newuid" => $ky2,
            "old_course_id" => $v["course_id"],
            "new_course_id" => $nrow["course_id"],
            "old_ctxt_id" => $v["ctxt_id"],
            "new_ctxt_id" => $nrow["ctxt_id"],
            "old_cmdl_id" => $v["cmdl_id"],
            "new_cmdl_id" => $nrow["cmdl_id"],
            "old_qb_assgn_id" => $v["qb_assgn_id"],
            "new_qb_assgn_id" => $nrow["qb_assgn_id"]
         );
    }
}

$options = array(
    array('name' => 'userfields', 'value' => 'id')
);

$enrolledusers = core_enrol_external::get_enrolled_users($ccourse_id, $options);

/////// User Mapping and Grading Entries ////////
foreach($enrolledusers as $enrolleduser){
    $cusrid = $enrolleduser["id"];
    foreach($combinedrecs as $combinedrec){
        $oqaid = $combinedrec["old_qb_assgn_id"];
        $nqaid = $combinedrec["new_qb_assgn_id"];
        $octxt_id = $combinedrec["old_ctxt_id"];
        $nctxt_id = $combinedrec["new_ctxt_id"];
        $oldmapping = $DB->get_record("qbassign_user_mapping",
            [
                "qbassignment" => $oqaid,
                "userid" => $cusrid
            ]
        );
        if($oldmapping){
            $newmapping = $DB->get_record("qbassign_user_mapping",
                [
                    "qbassignment" => $nqaid,
                    "userid" => $cusrid
                ]
            );
            if(empty($newmapping)){
                $umapdata = new stdClass;
                $umapdata->qbassignment = $nqaid;
                $umapdata->userid = $cusrid;
                $DB->insert_record("qbassign_user_mapping", $umapdata);
            }
        }

        $oldgrade = $DB->get_record("qbassign_grades",
                    [
                        "qbassignment" => $oqaid,
                        "userid" => $cusrid
                    ]
        );
        if($oldgrade){
            $newgrade = $DB->get_record("qbassign_grades",
                    [
                        "qbassignment" => $nqaid,
                        "userid" => $cusrid
                    ]
            );
            if(empty($newgrade)){
                unset($oldgrade->id);
                $oldgrade->qbassignment = $nqaid;
                $DB->insert_record("qbassign_grades", $oldgrade);
            }
        }
        
        // Get Old Submission from Old Assignment ID
        $oldsubmission = $DB->get_record("qbassign_submission",
            [
               "qbassignment" => $oqaid,
               "userid" => $cusrid
            ]
        );

        // qbassign_submission migration //
        $oldsub_id = 0;
        $newsub_id = 0;
        if($oldsubmission){
            $oldsub_id = $oldsubmission->id; // Old submission id important one
            $newsubmission = $DB->get_record("qbassign_submission",
                    [
                    "qbassignment" => $nqaid,
                    "userid" => $cusrid
                    ]
            );

            if(empty($newsubmission)){
                $newsubmission =  new stdClass;
                unset($oldsubmission->id);
                $oldsubmission->qbassignment = $nqaid;
                $newsub_id = $DB->insert_record("qbassign_submission", $oldsubmission);
            }else{
                $newsub_id = $newsubmission->id;
            }
        }

        // qbassignsubmission_file //
        $oldsubmissionfile = $DB->get_record("qbassignsubmission_file",
            [
               "qbassignment" => $oqaid,
               "submission" => $oldsub_id
            ]
        );
        
        if($oldsubmissionfile){
            
            $newsubmissionfile = $DB->get_record("qbassignsubmission_file",
                [
                "qbassignment" => $nqaid,
                "submission" => $newsub_id
                ]
            );
            
            if(empty($newsubmissionfile)){
                unset($oldsubmissionfile->id);
                $oldsubmissionfile->qbassignment = $nqaid;
                $oldsubmissionfile->submission = $newsub_id;
                $newsub_fid = $DB->insert_record("qbassignsubmission_file", $oldsubmissionfile);
            }
        }
        
        $old_files = $DB->get_records("files",
            [
                "contextid" => $octxt_id,
                "component" => "qbassignsubmission_file",
                "filearea" => "submission_files",
                "itemid" => $oldsub_id
            ]
        );
        
        if($old_files){      
            foreach($old_files as $old_file){
                $old_file->contextid = $nctxt_id;
                $old_file->itemid = $newsub_id;
                $DB->update_record("files", $old_file);
            }
        } 

        // If we want to revert new file using the below code
        /* $new_files = $DB->get_records("files",
            [
                "contextid" => $nctxt_id,
                "component" => "qbassignsubmission_file",
                "filearea" => "submission_files",
                "itemid" => $newsub_id
            ]
        );
        
        if($new_files){      
            foreach($new_files as $new_file){
                $new_file->contextid = $octxt_id;
                $new_file->itemid = $oldsub_id;
                $DB->update_record("files", $new_file);
            }
        } */

        // Qb Assign User Flags
        $oqauflag = $DB->get_record("qbassign_user_flags",
            [
               "qbassignment" => $oqaid,
               "userid" => $cusrid
            ]
        );
        
        if( $oqauflag ){
            $nqauflag = $DB->get_record("qbassign_user_flags",
                [
                "qbassignment" => $nqaid,
                "userid" => $cusrid
                ]
            );
            if(empty($nqauflag)){
                unset($oqauflag->id);
                $oqauflag->qbassignment = $nqaid;
                $DB->insert_record("qbassign_user_flags", $oqauflag);
            }
        }

        // Online text

        $oldonline_txt = $DB->get_record("qbassignsubmission_onlinetex",
           [
                "qbassignment" => $oqaid,
                "submission" => $oldsub_id
           ]
        );

        if($oldonline_txt){
            $newonline_txt = $DB->get_record("qbassignsubmission_onlinetex",
                [
                    "qbassignment" => $nqaid,
                    "submission" => $newsub_id
                ]
            );
            if(empty($newonline_txt)){
                unset($oldonline_txt->id);
                $oldonline_txt->qbassignment = $nqaid;
                $oldonline_txt->submission = $newsub_id;
                $DB->insert_record("qbassignsubmission_onlinetex", $oldonline_txt);
            }
        }

        // Code Block qbassignsubmission_codeblock
        $oldcodeblock = $DB->get_record("qbassignsubmission_codeblock",
           [
                "qbassignment" => $oqaid,
                "submission" => $oldsub_id
           ]
        );

        if($oldcodeblock){
            $newcodeblock = $DB->get_record("qbassignsubmission_codeblock",
                [
                    "qbassignment" => $nqaid,
                    "submission" => $newsub_id
                ]
            );
            if(empty($newcodeblock)){
                unset($oldcodeblock->id);
                $oldcodeblock->qbassignment = $nqaid;
                $oldcodeblock->submission = $newsub_id;
                $DB->insert_record("qbassignsubmission_codeblock", $oldcodeblock);
            }
        }
        
        // Scratch qbassignsubmission_scratch

        $oldcodescratch = $DB->get_record("qbassignsubmission_scratch",
           [
                "qbassignment" => $oqaid,
                "submission" => $oldsub_id
           ]
        );

        if($oldcodescratch){
            $newcodescratch = $DB->get_record("qbassignsubmission_scratch",
                [
                    "qbassignment" => $nqaid,
                    "submission" => $newsub_id
                ]
            );
            if(empty($newcodescratch)){
                unset($oldcodescratch->id);
                $oldcodescratch->qbassignment = $nqaid;
                $oldcodescratch->submission = $newsub_id;
                $DB->insert_record("qbassignsubmission_scratch", $oldcodescratch);
            }
        }

        // Grade Items and Grades
        $oldgradeitem = $DB->get_record("grade_items",
            [
                "iteminstance" => $oqaid,
                "itemtype" => "mod",
                "itemmodule" => "qbassign",
                "courseid" => $pcourse_id
            ]
        );
        $ogrditmid = 0;
        $ngrditmid = 0;
        if($oldgradeitem){
            $ogrditmid = $oldgradeitem->id;
            $newgradeitem = $DB->get_record("grade_items",
                [
                    "iteminstance" => $nqaid,
                    "itemtype" => "mod",
                    "itemmodule" => "qbassign",
                    "courseid" => $ccourse_id
                ]
            );

             if($newgradeitem){
                $ngrditmid = $newgradeitem->id;
                // $cusrid
                // Grade of Grades grade_grades
                $og_grades = $DB->get_record("grade_grades",
                    [
                        "itemid" => $ogrditmid,
                        "userid" => $cusrid
                    ]
                );
                if($og_grades){
                    $ng_grades = $DB->get_record("grade_grades",
                        [
                            "itemid" => $ngrditmid,
                            "userid" => $cusrid
                        ]
                    );
                    if(empty($ng_grades)){
                        unset($og_grades->id);
                        $og_grades->itemid = $ngrditmid;
                        $DB->insert_record("grade_grades", $og_grades);
                    }
                }
             }
        }
        // Ended Grade Items and Grades
        // Grades History grade_grades_history
        $oldgradehistories = $DB->get_records("grade_grades_history",
            [
                "itemid" => $ogrditmid,
                "userid" => $cusrid,
                "source" => "mod/qbassign"
            ]
        );

        if($oldgradehistories){
            foreach($oldgradehistories as $oldgradehistory){
                $newgradehistory = $DB->get_record("grade_grades_history",
                    [
                        "itemid" => $ngrditmid,
                        "userid" => $cusrid,
                        "source" => "mod/qbassign",
                        "loggeduser" => $oldgradehistory->loggeduser,
                        "action" => $oldgradehistory->action
                    ]
                );
 
                if(empty($newgradehistory)){
                    unset($oldgradehistory->id);
                    $oldgradehistory->itemid = $ngrditmid;
                    $DB->insert_record("grade_grades_history", $oldgradehistory);         
                }  
            }
        }
        // Ended Grades History
        
    }
}

//echo $enrolledusers[0]["roles"][0]["shortname"]."<br/>";

echo "<pre>";
//print_r($orecds1);
//print_r($nrecds1);
print_r($enrolledusers);
print_r($combinedrecs);
//print_r($parent_course); 
//print_r($newmnfstcourse);
echo "</pre>";
exit;


function addcohort_uid_item($item){
    global $cohort_idnumber;
    $item["uid"] = $item["uid"].'-'.$cohort_idnumber;
    if(isset($item["children"])){
        $children = $item["children"];
        $children = array_map('addcohort_uid_item', $children);
        $item["children"] = $children;
    }
    return $item;
}

function get_new_assign_id($old_assgn_id)
{
    global $combinedrecs;
    $new_assgn_id = "";
    foreach($combinedrecs as $ck => $cv){
        if($cv["old_qb_assgn_id"]==$old_assgn_id)
        {
           $new_assgn_id = $cv["new_qb_assgn_id"];
           break;
        }
    }
    return $new_assgn_id;
}