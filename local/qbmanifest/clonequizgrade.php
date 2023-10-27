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
// http://qubits.localhost.com/local/qbmanifest/clonequizgrade.php?cshortname=DPL06&cohortid=ajyalmbz
// Example http://qubits.localhost.com/local/qbmanifest/clonequizgrade.php?cshortname=DCL03&cohortid=bfsajman

require_login();

if(!is_siteadmin()){
    throw new \moodle_exception('accessdenied');
}

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

$pc_ctxt_sql = "SELECT qbc.id ctxt_id, qc.cmdl_id, qc.course_id, qc.qb_quiz_id, qc.uid FROM {context} qbc
JOIN (SELECT cmdls.id cmdl_id, cmdls.course course_id, qba.id qb_quiz_id, qba.uid FROM {course_modules} cmdls  
LEFT JOIN  {quiz} qba ON  cmdls.course = qba.course AND cmdls.instance = qba.id
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
           "old_qb_quiz_id" => $v["qb_quiz_id"],
           "new_qb_quiz_id" => $nrow["qb_quiz_id"]
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
            "old_qb_quiz_id" => $v["qb_quiz_id"],
            "new_qb_quiz_id" => $nrow["qb_quiz_id"]
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
        $oqzid = $combinedrec["old_qb_quiz_id"];
        $nqzid = $combinedrec["new_qb_quiz_id"];
        $octxt_id = $combinedrec["old_ctxt_id"];
        $nctxt_id = $combinedrec["new_ctxt_id"];

        // Quiz Grades
        $old_qgrade = $DB->get_record("quiz_grades",[
            "quiz" => $oqzid,
            "userid" => $cusrid
        ]);

        if($old_qgrade){
            $new_qgrade = $DB->get_record("quiz_grades",[
                "quiz" => $nqzid,
                "userid" => $cusrid
            ]);
            if(empty($new_qgrade)){
                unset($old_qgrade->id);
                $old_qgrade->quiz = $nqzid;
                $DB->insert_record("quiz_grades", $old_qgrade);
            }
        }

        // End Quiz Grades
        // Start Quiz Attempts quiz_attempts
        $old_attempts = $DB->get_records("quiz_attempts",[
            "quiz" => $oqzid,
            "userid" => $cusrid
        ]);

        if($old_attempts){
            foreach($old_attempts as $old_attempt){
                $new_attempt = $DB->get_record("quiz_attempts",[
                    "quiz" => $nqzid,
                    "userid" => $cusrid,
                    "attempt" => $old_attempt->attempt
                ]);
                if(empty($new_attempt)){
                    unset($old_attempt->id);
                    $old_attempt->quiz = $nqzid;
                    $DB->insert_record("quiz_attempts", $old_attempt);
                    //$DB->update_record("quiz_attempts", $old_attempt);
                }
            }
        }

        // End Quiz Attempts
        // Grade Items and Grades
        $oldgradeitem = $DB->get_record("grade_items",
            [
                "iteminstance" => $oqzid,
                "itemtype" => "mod",
                "itemmodule" => "quiz",
                "courseid" => $pcourse_id
            ]
        );
        $ogrditmid = 0;
        $ngrditmid = 0;
        if($oldgradeitem){
            $ogrditmid = $oldgradeitem->id;
            $newgradeitem = $DB->get_record("grade_items",
                [
                    "iteminstance" => $nqzid,
                    "itemtype" => "mod",
                    "itemmodule" => "quiz",
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
                "source" => "mod/quiz"
            ]
        );

        if($oldgradehistories){
            foreach($oldgradehistories as $oldgradehistory){
                $newgradehistory = $DB->get_record("grade_grades_history",
                    [
                        "itemid" => $ngrditmid,
                        "userid" => $cusrid,
                        "source" => "mod/quiz",
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
        if($cv["old_qb_quiz_id"]==$old_assgn_id)
        {
           $new_assgn_id = $cv["new_qb_quiz_id"];
           break;
        }
    }
    return $new_assgn_id;
}