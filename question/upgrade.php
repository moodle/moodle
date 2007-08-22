<?php
/**
 * Functions question_cwqpfs_to_update, question_cwqpfs_check_children are needed by question_random_check.
 */

/**
 * @param $result the result object that can be modified.
 * @return null if the test is irrelevant, or true or false depending on whether the test passes.
 */
function question_random_check($result){
    global $CFG;
    if (!empty($CFG->running_installer) //no test on first installation, no questions to test yet
            || $CFG->version >= 2007081000){//no test after upgrade seperates question cats into contexts.
        return null;
    }
    if (!$toupdate = question_cwqpfs_to_update()){
        $result->setStatus(true);//pass test
    } else {
        //set the feedback string here and not in xml file since we need something
        //more complex than just a string picked from admin.php lang file
        $a = new object();
        $a->reporturl = "{$CFG->wwwroot}/{$CFG->admin}/report/question/";
        $lang = str_replace('_utf8', '', current_language());
        $a->docsurl = "{$CFG->docroot}/$lang/admin/report/question/index";
        $result->setFeedbackStr(array('questioncwqpfscheck', 'admin', $a));
        $result->setStatus(false);//fail test
    }
    return $result;
}

function question_cwqpfs_to_update($categories = null){
    global $CFG;

    $tofix = array();
    $result = true;

    //any cats with questions picking from subcats?
    if (!$cwqpfs = get_records_sql_menu("SELECT DISTINCT qc.id, 1 ".
                                    "FROM {$CFG->prefix}question q, {$CFG->prefix}question_categories qc ".
                                    "WHERE q.qtype='random' AND qc.id = q.category AND ".
                                     sql_compare_text('q.questiontext'). " = '1'")){
        return array();
    } else {
        if ($categories === null){
            $categories = get_records('question_categories');
        }
        $categorychildparents = array();
        foreach ($categories as $id => $category){
            $categorychildparents[$category->course][$id] = $category->parent;
        }
        foreach ($categories as $id => $category){
            if (FALSE !== array_key_exists($category->parent, $categorychildparents[$category->course])){
                //this is not a top level cat
                continue;//go to next category
            } else{
                $tofix += question_cwqpfs_check_children($id, $categories, $categorychildparents[$category->course], $cwqpfs);
            }
        }
    }

    return $tofix;
}

function question_cwqpfs_check_children($checkid, $categories, $categorychildparents, $cwqpfs){
    $tofix = array();
    if (array_key_exists($checkid, $cwqpfs)){//cwqpfs in this cat
        $getchildren = array();
        $getchildren[] = $checkid;
        //search down tree and find all children
        while ($nextid = array_shift($getchildren)){//repeat until $getchildren
                                                    //empty;
            $childids = array_keys($categorychildparents, $nextid);
            foreach ($childids as $childid){
                if ($categories[$childid]->publish != $categories[$checkid]->publish){
                    $tofix[$childid] = $categories[$checkid]->publish;
                }
            }
            $getchildren = array_merge($getchildren, $childids);
        }
    } else { // check children for cwqpfs
        $childrentocheck = array_keys($categorychildparents, $checkid);
        foreach ($childrentocheck as $childtocheck){
            $tofix += question_cwqpfs_check_children($childtocheck, $categories, $categorychildparents, $cwqpfs);
        }
    }
    return $tofix;
}
/**
 * This test is becuase the RQP question type was included in core
 * up to and including Moodle 1.8, and was removed before Moodle 1.9.
 *
 * Therefore, we want to check whether any rqp questions exist in the database
 * before doing the upgrade. However, the check is not relevant if that
 * question type was never installed, or if the person has chosen to
 * manually reinstall the rqp question type from contrib.
 *
 * @param $result the result object that can be modified.
 * @return null if the test is irrelevant, or true or false depending on whether the test passes.
 */
function question_check_no_rqp_questions($result) {
    global $CFG;

    if (empty($CFG->qtype_rqp_version) || is_dir($CFG->dirroot . '/question/type/rqp')) {
        return null;
    } else {
        $result->setStatus(count_records('question', 'qtype', 'rqp') == 0);
    }
    return $result;
}

?>
