<?php 

// Cac ham danh cho viec thuc thi Sequencing
// --------Ket thuc cac ham danh cho viec thuc thi Sequencing ------------

// Cac ham danh cho viec thuc thi Rollup

//-----------------------------------------------------
function scorm_rollup_updatestatus($scormid,$scoidchild, $userid)
{
    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    //fwrite($ft,"\n >>>>> SCO goi Rollup la ".$scoidchild);
    $scochild = get_record("scorm_scoes","id",$scoidchild);
    $scoparent = get_record("scorm_scoes","scorm",$scormid,"identifier",$scochild->parent);
    //Danh sach cac con cua cha
    $scochildren = get_records_select("scorm_scoes","scorm =".$scormid." and parent ='".$scoparent->identifier."'");
    //Lay gia tri last attempt
    //fwrite($ft,"\n >>>>> Bat dau xu ly Rollup SCO cha ".$scoparent->id);
    $attempt = scorm_get_last_attempt($scormid,$userid);
    
    if(!empty($scoparent)){
        $scoid = $scoparent->id;
        $rolluprules = get_record("scorm_sequencing_rolluprules","scormid",$scormid,"scoid",$scoid);
        if (!empty($rolluprules)){
            $idrolluprules = $rolluprules->id;
            $rules = get_records_select('scorm_sequencing_rolluprule','scoid ='.$scoid.'  and rolluprulesid ='. $idrolluprules);
    
            foreach ($rules as $rule){
                $ruleid = $rule->id;
                $ruleConditions = get_record("scorm_sequencing_rollupruleconditions","scoid",$scoid,"rollupruleid",$ruleid);            
                $idruleConditions = $ruleConditions->id;
                $conditions = get_records_select('scorm_sequencing_rolluprulecondition','scoid ='.$scoid.'  and ruleconditionsid ='.$idruleConditions);    
    
                //Truong hop 1: childactivitySet = all
                //                conditioncombination = any
                if (($rule->childactivityset == 'all') && ($ruleConditions->conditioncombination=='any')){
                    foreach($conditions as $condition){
                        $conditionOK = false;   
                        //Condition 1:    condition = attempted         operator = 'noOp'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'attempted') && ($condition->operator=='noOp')){
                        $conditionOK = true; 
                            foreach ($scochildren as $sco){
                                //fwrite($ft,"\n >>>>> Xu ly Rollup voi dieu kien attempt  \n");
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status != 'attempted'){
                                    //fwrite($ft,"\n >>>>> Co SCO con chua attempted  \n");
                                    $conditionOK = false;
                                }
                            }
                        }
                        //Condition 2:    condition = attempted         operator = 'not'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'attempted') && ($condition->operator=='not')){
                        $conditionOK = true; 
                            foreach ($scochildren as $sco){
                                //fwrite($ft,"\n >>>>> Xu ly Rollup voi dieu kien not attempt  \n");
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status != 'notattempted'){
                                    $conditionOK = false;
                                }
                            }
                        }                    
                        //Condition 3:    condition = satisfied         operator = 'noOp'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'satisfied') && ($condition->operator=='noOp')){
                        $conditionOK = true; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->satisfied_status != 'satisfied'){
                                    $conditionOK = false;
                                }
                            }
                        }
                        //Condition 4:    condition = satisfied         operator = 'not'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'satisfied') && ($condition->operator=='not')){
                        $conditionOK = true; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->satisfied_status != 'notSatisfied'){
                                    $conditionOK = false;
                                }
                            }
                        }                    
                        //Condition 5:    condition = completed         operator = 'noOp'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'completed') && ($condition->operator=='noOp')){
                        $conditionOK = true; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status != 'completed'){
                                    $conditionOK = false;
                                }
                            }
                        }    
                        //Condition 6:    condition = completed         operator = 'not'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'completed') && ($condition->operator=='not')){
                        $conditionOK = true; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status != 'notcompleted'){
                                    $conditionOK = false;
                                }
                            }
                        }                                    
                        //Neu dieu kien van dung sau khi xem xet thi thuc hien action
                        if ($conditionOK == true){
                            if ($ruleConditions->rollupruleaction == 'completed')
                            {
                            scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.completion_status','completed');
                            //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong voi completed cho SCO ".$scoid);
                            }
                            if ($ruleConditions->rollupruleaction == 'satisfied')
                            {
                            scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','passed');
                            //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi satisfied\n");
                            }
                            if ($ruleConditions->rollupruleaction == 'notSatisfied')
                            {
                            scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','failed');
                            //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi notSatisfied\n");
                            }                                                        
                            //echo "<script language='JavaScript'>";
                            //echo "alert('Thuc hien rollup. Trang thai ".$scoparent->identifier." la hoan thanh voi userid".$userid."');";
                            //echo "<script>";
                            
                        }
                    }
                }
                //Ket thuc truong hop 1
                //Truong hop 2: childactivitySet = any
                //                conditioncombination = any
                if (($rule->childactivityset == 'any') && ($ruleConditions->conditioncombination=='any')){
                    $conditionOK = false;  
                    foreach($conditions as $condition){
                        //$conditionOK = false;   
                        //Condition 1:    condition = attempted         operator = 'noOp'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'attempted') && ($condition->operator=='noOp')){
                        $conditionOK = false; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status = 'attempted'){
                                    $conditionOK = true;
                                }
                            }
                        }
                        //Condition 2:    condition = attempted         operator = 'not'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'attempted') && ($condition->operator=='not')){
                        $conditionOK = false; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status = 'notattempted'){
                                    $conditionOK = true;
                                }
                            }
                        }                    
                        //Condition 3:    condition = satisfied         operator = 'noOp'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'satisfied') && ($condition->operator=='noOp')){
                        $conditionOK = false; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->satisfied_status = 'satisfied'){
                                    $conditionOK = true;
                                }
                            }
                        }
                        //Condition 4:    condition = satisfied         operator = 'not'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'satisfied') && ($condition->operator=='not')){
                        $conditionOK = false; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->satisfied_status = 'notSatisfied'){
                                    //fwrite($ft,"\n >>>>> Xu ly Rollup voi notSatisfied\n");
                                    $conditionOK = true;
                                }
                            }
                        }                    
                        //Condition 5:    condition = completed         operator = 'noOp'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'completed') && ($condition->operator=='noOp')){
                        $conditionOK = false; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status = 'completed'){
                                    $conditionOK = true;
                                }
                            }
                        }    
                        //Condition 6:    condition = completed         operator = 'not'
                        //                Thuc hien rollupaction 
                        if (($condition->condition == 'completed') && ($condition->operator=='not')){
                        $conditionOK = false; 
                            foreach ($scochildren as $sco){
                                $usertrack = scorm_get_tracks($sco->id,$userid);
                                if ($usertrack->attempt_status = 'notcompleted'){
                                    $conditionOK = true;
                                }
                            }
                        }                                    
                        //Neu dieu kien van dung sau khi xem xet thi thuc hien action
                        if ($conditionOK == true){
                            if ($ruleConditions->rollupruleaction == 'completed')
                            {
                            scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.completion_status','completed');
                            //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong voi completed\n");
                            }
                            if ($ruleConditions->rollupruleaction == 'satisfied')
                            {
                            scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','passed');
                            //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi satisfied\n");
                            }
                            if ($ruleConditions->rollupruleaction == 'notSatisfied')
                            {
                            scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','failed');
                            //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi notSatisfied\n");
                            }                                                        
                            //echo "<script language='JavaScript'>";
                            //echo "alert('Thuc hien rollup. Trang thai ".$scoparent->identifier." la hoan thanh voi userid".$userid."');";
                            //echo "<script>";
                            
                        }
                    }
                }
                //Ket thuc truong hop 2
                //Truong hop 3: childactivitySet = any
                //                conditioncombination = all
                if (($rule->childactivityset == 'any') && ($ruleConditions->conditioncombination=='all')){
                    foreach ($scochildren as $sco){
                    $usertrack = scorm_get_tracks($sco->id,$userid);
                    $conditionOK = true;  
                        foreach($conditions as $condition){
                            //Condition 1:    condition = attempted         operator = 'noOp'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'attempted') && ($condition->operator=='noOp')){
                                if ($usertrack->attempt_status != 'attempted'){
                                    $conditionOK = false;
                                }
                            }
                            //Condition 2:    condition = attempted         operator = 'not'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'attempted') && ($condition->operator=='not')){
                                if ($usertrack->attempt_status != 'notattempted'){
                                    $conditionOK = false;
                                }
                            }                    
                            //Condition 3:    condition = satisfied         operator = 'noOp'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'satisfied') && ($condition->operator=='noOp')){
                                if ($usertrack->attempt_status != 'satisfied'){
                                    $conditionOK = false;
                                }
                            }
                            //Condition 4:    condition = satisfied         operator = 'not'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'satisfied') && ($condition->operator=='not')){
                                if ($usertrack->attempt_status != 'notSatisfied'){
                                    $conditionOK = false;
                                }
                            }                    
                            //Condition 5:    condition = completed         operator = 'noOp'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'completed') && ($condition->operator=='noOp')){
                                if ($usertrack->attempt_status != 'completed'){
                                    $conditionOK = false;
                                }
                            }    
                            //Condition 6:    condition = completed         operator = 'not'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'completed') && ($condition->operator=='not')){
                                if ($usertrack->attempt_status != 'notcompleted'){
                                    $conditionOK = false;
                                }
                            }                                    
                            //Neu dieu kien van dung sau khi xem xet thi thuc hien action
                        }
                    }
                    if ($conditionOK == true){
                        if ($ruleConditions->rollupruleaction == 'completed')
                        {
                        scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.completion_status','completed');
                        //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong voi completed\n");
                        }
                        if ($ruleConditions->rollupruleaction == 'satisfied')
                        {
                        scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','passed');
                        //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi satisfied\n");
                        }
                        if ($ruleConditions->rollupruleaction == 'notSatisfied')
                        {
                        scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','failed');
                        //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi notSatisfied\n");
                        }                                                        
                        //echo "<script language='JavaScript'>";
                        //echo "alert('Thuc hien rollup. Trang thai ".$scoparent->identifier." la hoan thanh voi userid".$userid."');";
                        //echo "<script>";
                        
                    }                    
                }
                //Ket thuc truong hop 3
                //Truong hop 4: childactivitySet = all
                //                conditioncombination = all
                if (($rule->childactivityset == 'all') && ($ruleConditions->conditioncombination=='all')){
                    $conditionOK = true;                
                    foreach ($scochildren as $sco){
                    $usertrack = scorm_get_tracks($sco->id,$userid);
                        foreach($conditions as $condition){
                            //Condition 1:    condition = attempted         operator = 'noOp'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'attempted') && ($condition->operator=='noOp')){
                                if ($usertrack->attempt_status != 'attempted'){
                                    $conditionOK = false;
                                }
                            }
                            //Condition 2:    condition = attempted         operator = 'not'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'attempted') && ($condition->operator=='not')){
                                if ($usertrack->attempt_status != 'notattempted'){
                                    $conditionOK = false;
                                }
                            }                    
                            //Condition 3:    condition = satisfied         operator = 'noOp'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'satisfied') && ($condition->operator=='noOp')){
                                if ($usertrack->attempt_status != 'satisfied'){
                                    $conditionOK = false;
                                }
                            }
                            //Condition 4:    condition = satisfied         operator = 'not'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'satisfied') && ($condition->operator=='not')){
                                if ($usertrack->attempt_status != 'notSatisfied'){
                                    $conditionOK = false;
                                }
                            }                    
                            //Condition 5:    condition = completed         operator = 'noOp'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'completed') && ($condition->operator=='noOp')){
                                if ($usertrack->attempt_status != 'completed'){
                                    $conditionOK = false;
                                }
                            }    
                            //Condition 6:    condition = completed         operator = 'not'
                            //                Thuc hien rollupaction 
                            if (($condition->condition == 'completed') && ($condition->operator=='not')){
                                if ($usertrack->attempt_status != 'notcompleted'){
                                    $conditionOK = false;
                                }
                            }                                    
                            //Neu dieu kien van dung sau khi xem xet thi thuc hien action
                        }
                    }
                    if ($conditionOK == true){
                        if ($ruleConditions->rollupruleaction == 'completed')
                        {
                        scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.completion_status','completed');
                        //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong voi completed\n");
                        }
                        if ($ruleConditions->rollupruleaction == 'satisfied')
                        {
                        scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','passed');
                        //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi satisfied\n");
                        }
                        if ($ruleConditions->rollupruleaction == 'notSatisfied')
                        {
                        scorm_insert_track($userid,$scormid,$scoid,$attempt,'cmi.success_status','failed');
                        //fwrite($ft,"\n >>>>> Xu ly Rollup thanh cong  voi notSatisfied\n");
                        }                                                        
                        //echo "<script language='JavaScript'>";
                        //echo "alert('Thuc hien rollup. Trang thai ".$scoparent->identifier." la hoan thanh voi userid".$userid."');";
                        //echo "<script>";
                        
                    }                    
                }
                //Ket thuc truong hop 4                                                
            }    
        }
    
        //Thuc hien de qui cho Rollup voi cac muc cha
        $scograndparent = get_record("scorm_scoes","scorm",$scormid,"identifier",$scoparent->parent);
        if (!empty($scograndparent)){
            //fwrite($ft,"\n >>>>> Quay lui Rollup SCO ".$scoparent->id);
            scorm_rollup_updatestatus($scormid,$scoparent->id, $userid);
        }
    }
}

// --------Ket thuc cac ham danh cho viec thuc thi Rollup -------

//---------Thuc hien sequencing rule -----------------
function scorm_sequecingrule_implement($scormid,$scoidchild, $userid)
{
    $sequencingResult->rule = '';   //Rule co 3 truong hop exit, pre va post
    $sequencingResult->action = '';
    
    
    $f = "D:\\test.txt";
    @$ft = fopen($f,"a");
    //fwrite($ft,"\n >>>>> Kiem tra Sequencing \n");

    $scochild = get_record("scorm_scoes","id",$scoidchild);
    $scoparent = get_record("scorm_scoes","scorm",$scormid,"identifier",$scochild->parent);
    //Danh sach cac con cua cha
    $scochildren = get_records_select("scorm_scoes","scorm =".$scormid." and parent ='".$scoparent->identifier."'");
    //Lay gia tri last attempt
    
    $attempt = scorm_get_last_attempt($scormid,$userid);
    
    if(!empty($scoparent)){
        //fwrite($ft,"\n >>>>> Kiem tra Sequencing : Co Parent\n");
    
        $scoid = $scoparent->id;
        //Lay trang thai cua SCO cha
        $usertrack = scorm_get_tracks($scoid,$userid);
        //fwrite($ft,"\n >>>>> Kiem tra Sequencing : id Parent ".$scoid);                
        //fwrite($ft,"\n >>>>> Kiem tra Sequencing : usertrack ".$usertrack->status);                        
        $sequencingrules = get_records_select("scorm_sequencing_ruleconditions","scormid=".$scormid." and scoid=".$scoid);
        if (!empty($sequencingrules)){
            foreach($sequencingrules as $sequencingrule){
                //fwrite($ft,"\n >>>>> Kiem tra Sequencing : Co Sequencing o SCO".$sequencingrule->scoid);        
                
                $idsequencingrule = $sequencingrule->id;
                $ruleconditions = get_records_select('scorm_sequencing_rulecondition','scoid ='.$scoid.'  and ruleconditionsid ='. $idsequencingrule);
        
                $conditionOK = true;
                //Truong hop 1: conditioncombination = all            
                if ($sequencingrule->conditioncombination =='all'){
                    //fwrite($ft,"\n >>>>> Kiem tra Sequencing :conditioncombination la all \n");        
                    $conditionOK = true;
                    //fwrite($ft,"\n >>>>> Usertrack->status la: ".$usertrack->status);
                    foreach ($ruleconditions as $rulecondition){
                        //Neu co mot dieu kien khong thoa man thi se khong dung
                        if (($rulecondition->condition != $usertrack->status)&&($rulecondition->condition != $usertrack->success_status)&&($rulecondition->condition != $usertrack->satisfied_status)){
                            $conditionOK = false;                
                        }
                    }    
                }
                //Truong hop 2: conditioncombination = any                        
                if ($sequencingrule->conditioncombination =='any'){
                    $conditionOK = false;            
                    foreach ($ruleconditions as $rulecondition){
                        //Neu co mot dieu kien thoa man thi se dung
                        if (($rulecondition->condition == $usertrack->status) || ($rulecondition->condition == $usertrack->success_status) || ($rulecondition->condition == $usertrack->satisfied_status) ){
                            $conditionOK = true;                
                        }
                    }    
                }
    
                //fwrite($ft,"\n >>>>> Gia tri conditionOK sau khi kiem tra dk la: ".$conditionOK);
                //Neu dieu kien van dung thi thuc hien Action            
                if ($conditionOK == true){
                    //fwrite($ft,"\n >>>>> Dieu kien Sequencing OK..Thuc hien Action \n");                
                    //Truong hop 1: ExitAction la Exit
                    if ($sequencingrule->exitconditionruleaction=='exit')
                    {
                    //fwrite($ft,"\n >>>>> Xu ly Sequencing thanh cong -- Thuc hien su kien exit \n");
                    echo "<script type=\"text/javascript\">";
                    echo "alert('Thuc hien sequen. Do Trang thai ".$scoparent->identifier." la hoan thanh. Tien hanh EXIT');";
                    echo "</script>";
                    $sequencingResult->rule = 'exit';
                    $sequencingResult->action = 'exit';                                        
                    }
                    if ($sequencingrule->preconditionruleaction=='disabled')
                    {
                    //fwrite($ft,"\n >>>>> Xu ly Sequencing thanh cong -- Thuc hien su kien disable \n");
                    echo "<script type=\"text/javascript\">";
                    echo "alert('Thuc hien sequen. Do Trang thai ".$scoparent->identifier." la hoan thanh. Tien hanh Disable');";
                    echo "</script>";                    
                    $sequencingResult->rule = 'pre';
                    $sequencingResult->action = 'disable';                                        
                    
                    }                    
                    
                }
            }
        }
    }
    return $sequencingResult;
}
function get_sco_after_exit($scoid,$scormid){
    $scochild = get_record("scorm_scoes","id",$scoid);
    $scoparent = get_record("scorm_scoes","scorm",$scormid,"identifier",$scochild->parent);
    $exitscoid = $scoid++;
    $exitscochild = get_record("scorm_scoes","id",$exitscoid,"scorm",$scormid);
    if (empty($exitscochild)){
    //Da ra ngoai vung scoid. Hay day chinh la sco cuoi cung
        return 0;
    }
    else{
        $exitscoparent = get_record("scorm_scoes","scorm",$scormid,"identifier",$exitscochild->parent);
        //Neu chua ra khoi activity do thi tiep tuc
        while ($exitscoparent->id == $scoparent->id){
            $exitscoid++;
            $exitscochild = get_record("scorm_scoes","id",$exitscoid);
                if (empty($exitscochild)){
                //Da ra ngoai vung scoid. Hay day chinh la sco cuoi cung
                return 0;
                }
                else{
                $exitscoparent = get_record("scorm_scoes","scorm",$scormid,"identifier",$exitscochild->parent);
                }
        }
    }
    return $exitscoid;    
}

?>
