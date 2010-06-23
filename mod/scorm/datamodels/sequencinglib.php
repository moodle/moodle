<?php // $Id$
require ($CFG->dirroot.'/mod/scorm/datamodels/scormlib.php');

function scorm_seq_evaluate($scoid,$usertracks) {
    return true;
}

function scorm_seq_overall ($scoid,$userid,$request,$attempt) {
    $seq = scorm_seq_navigation($scoid,$userid,$request,$attempt);
    if ($seq->navigation) {
        if ($seq->termination != null) {
            $seq = scorm_seq_termination($scoid,$userid,$seq);
        }
        if ($seq->sequencing != null) {
            $seq = scorm_seq_sequencing($scoid,$userid,$seq);
			if($seq->sequencing == 'exit'){//return the control to the LTS
				return 'true';
			}
        }
        if ($seq->delivery != null) {
            $seq = scorm_sequencing_delivery($scoid,$userid,$seq);
			$seq = scorm_content_delivery_environment ($seq,$userid);
        }
    }
    if ($seq->exception != null) {
        $seq = scorm_sequencing_exception($seq);
    }
    return 'true';
}


function scorm_seq_navigation ($scoid,$userid,$request,$attempt=0) {
    /// Sequencing structure
    $seq = new stdClass();
    $seq->currentactivity = scorm_get_sco($scoid);
	$seq->traversaldir = null;
	$seq->nextactivity = null;
	$seq->deliveryvalid = null;
	$seq->attempt = $attempt;
	
	$seq->identifiedactivity = null;
	$seq->delivery = null;
	$seq->deliverable = false;
    $seq->active = scorm_seq_is('active',$scoid,$userid);
    $seq->suspended = scorm_seq_is('suspended',$scoid,$userid);
    $seq->navigation = null;
    $seq->termination = null;
    $seq->sequencing = null;
    $seq->target = null;
	$seq->endsession = null;
    $seq->exception = null;
	$seq->reachable = true;
	$seq->prevact = true;

    switch ($request) {
        case 'start_':
            if (empty($seq->currentactivity)) {
                $seq->navigation = true;
                $seq->sequencing = 'start';
            } else {
                $seq->exception = 'NB.2.1-1'; /// Sequencing session already begun
            } 
        break;
        case 'resumeall_':
            if (empty($seq->currentactivity)) {
                if ($track = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element','suspendedactivity')) {//I think it's suspend instead of suspendedactivity
                    $seq->navigation = true;
                    $seq->sequencing = 'resumeall';
                } else {
                    $seq->exception = 'NB.2.1-3'; /// No suspended activity found
                }
            } else {
                $seq->exception = 'NB.2.1-1'; /// Sequencing session already begun
            } 
        break;
        case 'continue_':
        case 'previous_':
            if (!empty($seq->currentactivity)) {
                $sco = $seq->currentactivity;
                if ($sco->parent != '/') {
                    if ($parentsco = scorm_get_parent($sco)) {
						
						 if (isset($parentsco->flow) && ($parentsco->flow == true)) {//I think it's parentsco
                            // Current activity is active !
							if (scorm_seq_is('active',$sco->id,$userid)) {
                                if ($request == 'continue_') {
                                    $seq->navigation = true;
                                    $seq->termination = 'exit';
                                    $seq->sequencing = 'continue';
                                } else {
                                    if (!isset($parentsco->forwardonly) || ($parentsco->forwardonly == false)) {
                                        $seq->navigation = true;
                                        $seq->termination = 'exit';
                                        $seq->sequencing = 'previous';
                                    } else {
                                        $seq->exception = 'NB.2.1-5'; /// Violates control mode
                                    }
                                }
							}
                        }

                    }
                }
            } else {
                $seq->exception = 'NB.2.1-2'; /// Current activity not defined
            }
        break;
        case 'forward_':
        case 'backward_':
            $seq->exception = 'NB.2.1-7' ; /// None to be done, behavior not defined
        break;
        case 'exit_':
        case 'abandon_':
            if (!empty($seq->currentactivity)) {
                // Current activity is active !
                $seq->navigation = true;
                $seq->termination = substr($request,0,-1);
                $seq->sequencing = 'exit';
            } else {
                $seq->exception = 'NB.2.1-2'; /// Current activity not defined
            }
        case 'exitall_':
        case 'abandonall_':
        case 'suspendall_':
            if (!empty($seq->currentactivity)) {
                $seq->navigation = true;
                $seq->termination = substr($request,0,-1);
                $seq->sequencing = 'exit';
            } else {
                $seq->exception = 'NB.2.1-2'; /// Current activity not defined
            }
        break;
        default: /// {target=<STRING>}choice 
            if ($targetsco = get_record('scorm_scoes','scorm',$sco->scorm,'identifier',$request)) {
                if ($targetsco->parent != '/') {
                    $seq->target = $request;
                } else {
                    if ($parentsco = scorm_get_parent($targetsco)) {
                        if (!isset($parentsco->choice) || ($parent->choice == true)) {
                            $seq->target = $request;
                        }
                    } 
                }
                if ($seq->target != null) {
                    if (empty($seq->currentactivity)) {
                        $seq->navigation = true;
                        $seq->sequencing = 'choice';
                    } else {
                        if (!$sco = scorm_get_sco($scoid)) {
                            return $seq;
                        }
                        if ($sco->parent != $target->parent) {
                            $ancestors = scorm_get_ancestors($sco);
                            $commonpos = scorm_find_common_ancestor($ancestors,$targetsco);
                            if ($commonpos !== false) {
                                if ($activitypath = array_slice($ancestors,0,$commonpos)) {
                                    foreach ($activitypath as $activity) {
                                        if (scorm_seq_is('active',$activity->id,$userid) && (isset($activity->choiceexit) && ($activity->choiceexit == false))) {
                                            $seq->navigation = false;
                                            $seq->termination = null;
                                            $seq->sequencing = null;
                                            $seq->target = null;
                                            $seq->exception = 'NB.2.1-8'; /// Violates control mode
                                            return $seq;
                                        }
                                    } 
                                } else {
                                    $seq->navigation = false;
                                    $seq->termination = null;
                                    $seq->sequencing = null;
                                    $seq->target = null;
                                    $seq->exception = 'NB.2.1-9';
                                }
                            }
                        }
                        // Current activity is active !
                        $seq->navigation = true;
                        $seq->sequencing = 'choice';
                    }
                } else {
                    $seq->exception = 'NB.2.1-10';  /// Violates control mode
                }
            } else {
                $seq->exception = 'NB.2.1-11';  /// Target activity does not exists
            }
        break;
    }
    return $seq;
}

function scorm_seq_termination ($seq,$userid) {
    if (empty($seq->currentactivity)) {
        $seq->termination = false;
        $seq->exception = 'TB.2.3-1';
        return $seq;
    }

    $sco = $seq->currentactivity;

    if ((($seq->termination == 'exit') || ($seq->termination == 'abandon')) && !$seq->active) {
        $seq->termination = false;
        $seq->exception = 'TB.2.3-2';
        return $seq;
    }
    switch ($seq->termination) {
        case 'exit':
            scorm_seq_end_attempt($sco,$userid,$seq);
            $seq = scorm_seq_exit_action_rules($seq,$userid);
            do {
                $exit = false;// I think this is false. Originally this was true
                $seq = scorm_seq_post_cond_rules($seq,$userid);
                if ($seq->termination == 'exitparent') {
                    if ($sco->parent != '/') {
                        $sco = scorm_get_parent($sco);
                        $seq->currentactivity = $sco;
                        $seq->active = scorm_seq_is('active',$sco->id,$userid);
                        scorm_seq_end_attempt($sco,$userid,$seq);
                        $exit = true;//I think it's true. Originally this was false
                    } else {
                        $seq->termination = false;
                        $seq->exception = 'TB.2.3-4';
                        return $seq;
                    }
                }
            } while (($exit == false) && ($seq->termination == 'exit'));
            if ($seq->termination == 'exit') {
                $seq->termination = true;
                return $seq;
            }
        case 'exitall':
            if ($seq->active) {
                scorm_seq_end_attempt($sco,$userid,$seq);
            }
            /// Terminate Descendent Attempts Process

			
            if ($ancestors = scorm_get_ancestors($sco)) { 
                foreach ($ancestors as $ancestor) {
                    scorm_seq_end_attempt($ancestor,$userid,$seq);
                    $seq->currentactivity = $ancestor;
                }
            }

            $seq->active = scorm_seq_is('active',$seq->currentactivity->id,$userid);
            $seq->termination = true;
			$seq->sequencing = exit;
        break;
        case 'suspendall':
            if (($seq->active) || ($seq->suspended)) {
                scorm_seq_set('suspended',$sco->id,$userid);
            } else {
                if ($sco->parent != '/') {
                    $parentsco = scorm_get_parent($sco);
                    scorm_seq_set('suspended',$parentsco->id,$userid);
                } else {
                    $seq->termination = false;
                    $seq->exception = 'TB.2.3-3';
                    // return $seq;
                }
            }
            if ($ancestors = scorm_get_ancestors($sco)) { 
                foreach ($ancestors as $ancestor) {
                    scorm_seq_set('active',$ancestor->id,$userid,false);
                    scorm_seq_set('suspended',$ancestor->id,$userid);
                    $seq->currentactivity = $ancestor;
                }
                $seq->termination = true;
                $seq->sequencing = 'exit';
            } else {
                $seq->termination = false;
                $seq->exception = 'TB.2.3-5';
            }
        break;
        case 'abandon':
            scorm_seq_set('active',$sco->id,$userid,false);
            $seq->active = null;
            $seq->termination = true;
        break;
        case 'abandonall':
            if ($ancestors = scorm_get_ancestors($sco)) { 
                foreach ($ancestors as $ancestor) {
                    scorm_seq_set('active',$ancestor->id,$userid,false);
                    $seq->currentactivity = $ancestor;
                }
                $seq->termination = true;
                $seq->sequencing = 'exit';
            } else {
                $seq->termination = false;
                $seq->exception = 'TB.2.3-6';
            }
        break;
        default:
            $seq->termination = false;
            $seq->exception = 'TB.2.3-7';
        break;
    }
    return $seq;
}

function scorm_seq_end_attempt($sco,$userid,$seq) {
    if (scorm_is_leaf($sco)) {
        if (!isset($sco->tracked) || ($sco->tracked == 1)) {
            if (!scorm_seq_is('suspended',$sco->id,$userid)) {
                if (!isset($sco->completionsetbycontent) || ($sco->completionsetbycontent == 0)) {
                    if (!scorm_seq_is('attemptprogressstatus',$sco->id,$userid,$seq->attempt)) {
                  // if (!scorm_seq_is('attemptprogressstatus',$sco->id,$userid)) { 
                        scorm_seq_set('attemptprogressstatus',$sco->id,$userid);
                        scorm_seq_set('attemptcompletionstatus',$sco->id,$userid);
                    }
                }
                if (!isset($sco->objectivesetbycontent) || ($sco->objectivesetbycontent == 0)) {
                    if ($objectives = get_records('scorm_seq_objective','scoid',$sco->id)) {
                        foreach ($objectives as $objective) {
                            if ($objective->primaryobj) {
                                //if (!scorm_seq_objective_progress_status($sco,$userid,$objective)) {
                                if (!scorm_seq_is('objectiveprogressstatus',$sco->id,$userid)) {
                                    scorm_seq_set('objectiveprogressstatus',$sco->id,$userid);
                                    scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid);
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        if ($children = scorm_get_children($sco)) {
            $suspended = false;
            foreach ($children as $child) {
                if (scorm_seq_is('suspended',$child,$userid)) {
                    $suspended = true;
                    break;
                }
            }
            if ($suspended) {
                scorm_seq_set('suspended',$sco,$userid);
            } else { 
                scorm_seq_set('suspended',$sco,$userid,false);
            }
        }
    }
    scorm_seq_set('active',$sco,$userid,0,false);
    scorm_seq_overall_rollup($sco,$userid);
}

function scorm_seq_is($what, $scoid, $userid, $attempt=0) {

    /// Check if passed activity $what is active
    $active = false;
    if ($track = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'attempt',$attempt,'element',$what)) {
        $active = true;
    }
    return $active;
}

function scorm_seq_set($what, $scoid, $userid, $attempt=0, $value='true') {
    $sco = scorm_get_sco($scoid);

    /// set passed activity to active or not
    if ($value == false) {
        delete_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'attempt',$attempt,'element',$what);
    } else {
        scorm_insert_track($userid, $sco->scorm, $sco->id, 0, $what, $value);
    }

    // update grades in gradebook
    $scorm = get_record('scorm', 'id', $sco->scorm);
    scorm_update_grades($scorm, $userid, true);
}

function scorm_seq_exit_action_rules($seq,$userid) {
    $sco = $seq->currentactivity;
    $ancestors = scorm_get_ancestors($sco);
    $exittarget = null;
    foreach (array_reverse($ancestors) as $ancestor) {
        if (scorm_seq_rules_check($ancestor,'exit') != null) {
            $exittarget = $ancestor;
            break;
        }
    }
    if ($exittarget != null) {
        $commons = array_slice($ancestors,0,scorm_find_common_ancestor($ancestors,$exittarget)); 
 
        /// Terminate Descendent Attempts Process
        if ($commons) { 
            foreach ($commons as $ancestor) {
				
                scorm_seq_end_attempt($ancestor,$userid,$seq->attempt);
                $seq->currentactivity = $ancestor;
            }
        }
    }
    return $seq;
}

function scorm_seq_post_cond_rules($seq,$userid) {
    $sco = $seq->currentactivity;
    if (!$seq->suspended) {
        if ($action = scorm_seq_rules_check($sco,'post') != null) {
            switch($action) {
                case 'retry':
                case 'continue':
                case 'previous':
                    $seq->sequencing = $action;
                break;
                case 'exitparent':
                case 'exitall':
                    $seq->termination = $action;
                break;
                case 'retryall':
                    $seq->termination = 'exitall';
                    $seq->sequencing = 'retry';
                break;
            }
        }
    }
    return $seq;
}

function scorm_seq_rules_check ($sco, $action){
	$act = null;
	if($rules = get_records('scorm_seq_ruleconds','scoid',$sco->id,'action',$action)){
		foreach ($rules as $rule){
			if($act = scorm_seq_rule_check($sco,$rule)){
				return $act;
			}
		}
	}
	return $act;

}

function scorm_seq_rule_check ($sco, $rule){
	$bag = Array();
	$cond = '';
	$ruleconds = get_records('scorm_seq_rulecond','scoid',$sco->id,'ruleconditionsid',$rule->id);
	foreach ($ruleconds as $rulecond){
		 if ($rulecond->operator = 'not') {
		     if ($rulecond->cond != 'unknown' ){
				$rulecond->cond = 'not'.$rulecond;
			 }
		 }
		 $bag [$rule->id] = $rulecond->cond;
    
	}
	if (empty($bag)){
		$cond = 'unknown';
		return $cond;
	}

	$size= sizeof($bag);
	$i=0;

	if ($rule->conditioncombination = 'all'){
		foreach ($bag as $con){
			    $cond = $cond.' and '.$con;
			
		}
	}
	else{
		foreach ($bag as $con){
			$cond = $cond.' or '.$con;
		}
	}
	return $cond;
}


function scorm_seq_overall_rollup($sco,$userid){//Carlos

	 if ($ancestors = scorm_get_ancestors($sco)) { 
            foreach ($ancestors as $ancestor) {
				if(!scorm_is_leaf($ancestor)){
					scorm_seq_measure_rollup($sco,$userid);
				}
				scorm_seq_objective_rollup($sco,$userid);
				scorm_seq_activity_progress_rollup($sco,$userid);

            }

     } 
}

/* For this next function I have defined measure weight and measure status as records with the attempt = 0 on the scorm_scoes_track table. According to the page 89 of the SeqNav.pdf those datas give us some information about the progress of the objective*/

function scorm_seq_measure_rollup($sco,$userid){

	$totalmeasure = 0; //Check if there is something similar in the database
	$valid = false;//Same as in the last line
	$countedmeasures = 0;//Same too
	$targetobjective = null;
	$readable = true;//to check if status and measure weight are readable
	$objectives = get_records('scorm_seq_objective','scoid',$sco->id);

    foreach ($objective as $objective){

		if ($objective->primaryobj == true){//Objective contributes to rollup I'm using primaryobj field, but not 
		    $targetobjective = $objective;
			break;
		}

	}

	if ($targetobjective != null){
		$children = scorm_get_children($sco);
        foreach ($children as $child){
		    $child = scorm_get_sco ($child);
			if (!isset($child->tracked) || ($child->tracked == 1)){
	
			    $rolledupobjective = null;// we set the rolled up activity to undefined
				$objectives = get_records('scorm_seq_objective','scoid',$child->id);
                foreach ($objective as $objective){
		            if ($objective->primaryobj == true){//Objective contributes to rollup I'm using primaryobj field, but not 
		                $rolledupobjective = $objective;
			            break;
		            }
 	            }
				if ($rolledupobjective != null){
                    $child = scorm_get_sco($child->id);
				
					$countedmeasures = $countedmeasures + ($child->measureweight);
					if (!scorm_seq_is('objectivemeasurestatus',$sco->id,$userid)) {
						$normalizedmeasure = get_record('scorm_scoes_track','scoid',$child->id,'userid',$userid,'element','objectivenormalizedmeasure');
						$totalmeasure = $totalmeasure + (($normalizedmeasure->value) * ($child->measureweight));
						$valid = true;
					}

					
                    
				}
		    }
		}

						 
		if(!$valid){

			scorm_seq_set('objectivemeasurestatus',$sco->id,$userid,false);

		}
		else{
			if($countedmeasures>0){
				scorm_seq_set('objectivemeasurestatus',$sco->id,$userid);
				$val=$totalmeasure/$countedmeasure;
				scorm_seq_set('objectivenormalizedmeasure',$sco->id,$userid,$val);
		        
			}
			else{
				scorm_seq_set('objectivemeasurestatus',$sco->id,$userid,false);
				
			}
		}

	}
	
}

function scorm_seq_objective_rollup($sco,$userid){
	
    scorm_seq_objective_rollup_measure($sco,$userid);
    scorm_seq_objective_rollup_rules($sco,$userid);
    scorm_seq_objective_rollup_default($sco,$userid);

/*
	if($targetobjective->satisfiedbymeasure){
		scorm_seq_objective_rollup_measure($sco,$userid);
	}
	else{
		if ((scorm_seq_rollup_rule_check($sco,$userid,'incomplete'))|| (scorm_seq_rollup_rule_check($sco,$userid,'completed'))){
			scorm_seq_objective_rollup_rules($sco,$userid);
		}
		else{

            $rolluprules = get_record('scorm_seq_rolluprule','scoid',$sco->id,'userid',$userid);
            foreach($rolluprules as $rolluprule){
                $rollupruleconds = get_records('scorm_seq_rolluprulecond','rollupruleid',$rolluprule->id);
			    foreach($rollupruleconds as $rolluprulecond){
                 
                    switch ($rolluprulecond->cond!='satisfied' && $rolluprulecond->cond!='completed' && $rolluprulecond->cond!='attempted'){
							
						   scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid, false);

				        break;
			        }
			    }

	
		}
	}
*/	
}

function scorm_seq_objective_rollup_measure($sco,$userid){
	$targetobjective = null;
	

	$objectives = get_records('scorm_seq_objective','scoid',$sco->id);
    foreach ($objectives as $objective){
	    if ($objective->primaryobj == true){
		    $targetobjective = $objective;
			break;
		}
 	}
	if ($targetobjective != null){

		if($targetobjective->satisfiedbymeasure){

            
            if (!scorm_seq_is('objectiveprogressstatus',$sco->id,$userid)) {

                scorm_seq_set('objectiveprogressstatus',$sco->id,$userid,false);
                                
            }

			else{
				if (scorm_seq_is('active',$sco->id,$userid)) {
				    $isactive = true;
				}
				else{
					$isactive = false;
				}

				$normalizedmeasure = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','objectivenormalizedmeasure');

				$sco = scorm_get_sco ($sco->id);

				if (!$isactive || ($isactive && (!isset($sco->measuresatisfactionifactive) || $sco->measuresatisfactionifactive == true))){
				    if($normalizedmeasure->value >= $targetobjective->minnormalizedmeasure){
					    scorm_seq_set('objectiveprogressstatus',$sco->id,$userid);
					    scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid);
					}
					else{
					    scorm_seq_set('objectiveprogressstatus',$sco->id,$userid);
					    scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid,false);
			        }
			    }
				else{

					scorm_seq_set('objectiveprogressstatus',$sco->id,$userid,false);
				
			    }
			}
		}
	}

}

function scorm_seq_objective_rollup_default($sco,$userid){
	if (!(scorm_seq_rollup_rule_check($sco,$userid,'incomplete')) && !(scorm_seq_rollup_rule_check($sco,$userid,'completed'))){
		
            $rolluprules = get_record('scorm_seq_rolluprule','scoid',$sco->id,'userid',$userid);
            foreach($rolluprules as $rolluprule){
                $rollupruleconds = get_records('scorm_seq_rolluprulecond','rollupruleid',$rolluprule->id);
			    foreach($rollupruleconds as $rolluprulecond){
                 
                    if ($rolluprulecond->cond!='satisfied' && $rolluprulecond->cond!='completed' && $rolluprulecond->cond!='attempted'){
							
						   scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid, false);

				        break;
			        }
			    }

	
            }
	}
	
}


function scorm_seq_objective_rollup_rules($sco,$userid){
	$targetobjective = null;

	$objectives = get_records('scorm_seq_objective','scoid',$sco->id);
    foreach ($objective as $objective){
	    if ($objective->primaryobj == true){//Objective contributes to rollup I'm using primaryobj field, but not 
		    $targetobjective = $objective;
			break;
		}
 	}
	if ($targetobjective != null){

        

		if(scorm_seq_rollup_rule_check($sco,$userid,'notsatisfied')){//with not satisfied rollup for the activity

		   
			scorm_seq_set('objectiveprogressstatus',$sco->id,$userid);
    		scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid,false);
		}
		if(scorm_seq_rollup_rule_check($sco,$userid,'satisfied')){//with satisfied rollup for the activity
			scorm_seq_set('objectiveprogressstatus',$sco->id,$userid);
    		scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid);
		}

	}

}

function scorm_seq_activity_progress_rollup ($sco, $userid){

	if(scorm_seq_rollup_rule_check($sco,$userid,'incomplete')){
		//incomplete rollup action
		scorm_seq_set('attemptcompletionstatus',$sco->id,$userid,false,$seq->attempt);
		scorm_seq_set('attemptprogressstatus',$sco->id,$userid,true,$seq->attempt);

	}
    if(scorm_seq_rollup_rule_check($sco,$userid,'completed')){
		//incomplete rollup action
		scorm_seq_set('attemptcompletionstatus',$sco->id,true,$userid);
		scorm_seq_set('attemptprogressstatus',$sco->id,true,$userid);
	}

}

function scorm_seq_rollup_rule_check ($sco,$userid,$action){

	 if($rolluprules = get_record('scorm_seq_rolluprule','scoid',$sco->id,'userid',$userid,'action',$action)){

        $childrenbag = Array ();
		$children = scorm_get_children ($sco);

		foreach($rolluprules as $rolluprule){

			foreach ($children as $child){

				/*$tracked = get_records('scorm_scoes_track','scoid',$child->id,'userid',$userid);
			    if($tracked && $tracked->attemp != 0){*/
				 $child = scorm_get_sco ($child);
			if (!isset($child->tracked) || ($child->tracked == 1)){

					if(scorm_seq_check_child ($child,$action,$userid)){

                        $rollupruleconds = get_records('scorm_seq_rolluprulecond','rollupruleid',$rolluprule->id);
						$evaluate = scorm_seq_evaluate_rollupcond($child,$rolluprule->conditioncombination,$rollupruleconds,$userid);
						if ($evaluate=='unknown'){
							array_push($childrenbag,'unknown');
						}
						else{
							if($evaluate == true){
								array_push($childrenbag,true);
							}
							else{
								array_push($childrenbag,false);
							}
						}
					}
				}
				
			}
			$change = false;

			switch ($rolluprule->childactivityset){

				case 'all':
					if((array_search(false,$childrenbag)===false)&&(array_search('unknown',$childrenbag)===false)){//I think I can use this condition instead equivalent to OR
					    $change = true;
				    }
				break;

				case 'any':
					if(array_search(true,$childrenbag)!==false){//I think I can use this condition instead equivalent to OR
					    $change = true;
				    }
				break;

				case 'none':
					if((array_search(true,$childrenbag)===false)&&(array_search('unknown',$childrenbag)===false)){//I think I can use this condition instead equivalent to OR
					    $change = true;
				    }
				break;

				case 'atleastcount':
					foreach ($childrenbag as $itm){//I think I can use this condition instead equivalent to OR
				        $cont = 0;
				        if($itm === true){
							$cont++;
						}
						if($cont >= $roullprule->minimumcount){
					        $change = true;
				        }
				    }
				break;

				case 'atleastcount':
					foreach ($childrenbag as $itm){//I think I can use this condition instead equivalent to OR
				        $cont = 0;
				        if($itm === true){
							$cont++;
						}
						if($cont >= $roullprule->minimumcount){
					        $change = true;
				        }
				    }
				break;

				case 'atleastpercent':
					foreach ($childrenbag as $itm){//I think I can use this condition instead equivalent to OR
				        $cont = 0;
				        if($itm === true){
							$cont++;
						}
						if(($cont/sizeof($childrenbag)) >= $roullprule->minimumcount){
					        $change = true;
				        }
				    }
				break;
			}
			if ($change==true){
				return true;
			}
		}
	 }
	 return false;
}


function scorm_seq_evaluate_rollupcond($sco,$conditioncombination,$rollupruleconds,$userid){
	$bag = Array();
    $con = "";
	$val = false;
	$unk = false;
    foreach($rollupruleconds as $rolluprulecond){

		$condit = scorm_evaluate_cond($rolluprulecond,$sco,$userid);

		if($rule->operator=='not'){// If operator is not, negate the condition
			if ($rule->cond != 'unknown'){            
				if ($condit){
					$condit = false;
				}
				else{
					$condit = true;
				}
			}
			else{
				$condit = 'unknown';
		    }
			array_push($childrenbag,$condit);
		}

	}
	if (empty($bag)){
		return 'unknown';
	}
	else{
		$i = 0;
		foreach ($bag as $b){

			 if ($rolluprule->conditioncombination == 'all'){

				 $val = true;
				 if($b == 'unknown'){
					 $unk = true;
				 }
				 if($b === false){
					 return false;
				 }
			 }

			 else{

                $val = false;
				 
				if($b == 'unknown'){
					 $unk = true;
				}
				if($b === true){
					return true;
				}
			 }


		}
	}
	if ($unk){
		return 'unknown';
	}
	return $val;

}

function scorm_evaluate_condition ($rolluprulecond,$sco,$userid){
	
	        $res = false;

            switch ($rolluprulecond->cond){
				
				case 'satisfied':
					 if($r=get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','objectivesatisfiedstatus')){
						if($r->value == true){
					        if ($r=get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','objectiveprogressstatus')){
						        if($r->value == true){
					               $res= true;
						        }
				            }
						}
				    }
				break;

				case 'objectiveStatusKnown':
                    if ($r=get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','objectiveprogressstatus')){
						if($r->value == true){
					        $res= true;
						}
				    }
				break;

				case 'objectiveMeasureKnown':
					if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','objectivemeasurestatus')){
					    if($r->value == true){
							$res = true;
						}
	
				    }

				break;

				case 'completed':
					if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','attemptcompletionstatus')){
					    if($r->value){
							if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','attemptprogressstatus')){
					           if($r->value){
							      $res = true;
						       }
	
				            }
						}
	
				    }
				break;

				case 'attempted':
					if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','activityprogressstatus')){
					    if($r->value){
							if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','activityattemptcount')){
					            if($r->value > 0){
							        $res = true;
						        }
	
				            }
						}
	
				    }
				break;
				

				case 'attemptLimitExceeded':
					if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','activityprogressstatus')){
					    if($r->value){
							if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','limitconditionattemptlimitcontrol')){
					            if($r->value){
							       if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','activityattemptcount') && $r2 = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','limitconditionattemptlimit') ){
					                   if($r->value >= $r2->value){
							               $res = true;
						               }
	
				                   }
					
						        }
	
				            }
					
						}
	
				    }
					
				break;

				case 'activityProgressKnown':

					if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','activityprogressstatus')){
					    if($r->value){
					        if ($r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','attemptprogressstatus')){
					            if($r->value){
							        $res = true;
						        }
	
				            }
					
						}
	
				    }
					
				break;
			}
			return $res;

}

function scorm_seq_check_child ($sco, $action, $userid){
	$included = false;
	$sco=scorm_get_sco($sco->id);
	$r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','activityattemptcount');
	if ($action == 'satisfied' || $action == 'notsatisfied'){
	  if (!$sco->rollupobjectivesatisfied){
		$included = true;
		if (($action == 'satisfied' && $sco->requiredforsatisfied == 'ifnotsuspended') || ($action == 'notsatisfied' && $sco->requiredfornotsatisfied == 'ifnotsuspended')){
			
			if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || ((($r->value)>0)&& !scorm_seq_is('suspended',$sco->id,$userid))){
				$included = false;
			}

		}
		else{
			if (($action == 'satisfied' && $sco->requiredforsatisfied == 'ifattempted') || ($action == 'notsatisfied' && $sco->requiredfornotsatisfied == 'ifattempted')){
			    if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || (($r->value) == 0)){
				    $included = false;
			    }
            }
			else{
				if (($action == 'satisfied' && $sco->requiredforsatisfied == 'ifnotskipped') || ($action == 'notsatisfied' && $sco->requiredfornotsatisfied == 'ifnotskipped')){
					$rulch = scorm_seq_rules_check($sco, 'skip');
					if ($rulch != null){
						$included = false;
					}
			    }
			}
		}
      }
	}
    if ($action == 'completed' || $action == 'incomplete'){
		if (!$sco->rollupprogresscompletion){
		    $included = true;

            if (($action == 'completed' && $sco->requiredforcompleted == 'ifnotsuspended') || ($action == 'incomplete' && $sco->requiredforincomplete == 'ifnotsuspended')){

			    if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || ( (($r->value)>0)&& !scorm_seq_is('suspended',$sco->id,$userid))){
				    $included = false;
			    }

		    }
			else{
				
				if (($action == 'completed' && $sco->requiredforcompleted == 'ifattempted') || ($action == 'incomplete' && $sco->requiredforincomplete == 'ifattempted')){
			        if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || (($r->value)==0)){
				        $included = false;
			        }

		        }
				else{
					if (($action == 'completed' && $sco->requiredforsatisfied == 'ifnotskipped') || ($action == 'incomplete' && $sco->requiredfornotsatisfied == 'ifnotskipped')){
					    $rulch = scorm_seq_rules_check($sco, 'skip');
					    if ($rulch != null){
						    $included = false;
					    }
			        }
				}


			}

		}
	}
    return $included;


}
function scorm_seq_sequencing ($scoid,$userid,$seq) {

    switch ($seq->sequencing) {

        case 'start':
			 $seq = scorm_seq_start_sequencing($sco,$userid,$seq); //We'll see the parameters we have to send, this should update delivery and end
			$seq->sequencing = true;
           
			
		    break;
			
        case 'resumeall':
			$seq = scorm_seq_resume_sequencing($sco,$userid,$seq); //We'll see the parameters we have to send, this should update delivery and end
			$seq->sequencing = true;
			
		    
          
            break;

        case 'exit':
			 $seq = scorm_seq_exit_sequencing($sco,$userid,$seq); //We'll see the parameters we have to send, this should update delivery and end
			$seq->sequencing = true;

           
          
            break;

        case 'retry':
            $seq = scorm_seq_retry_sequencing($sco,$userid,$seq); //We'll see the parameters we have to send, this should update delivery and end
            $seq->sequencing = true;
			
          
            break;

		case 'previous':
			$seq = scorm_seq_previous_sequencing($sco,$userid,$seq);// We'll see the parameters we have to send, this should update delivery and end
			$seq->sequencing = true;

         
            break;

		case 'choice':
			$seq = scorm_seq_choice_sequencing($sco,$userid,$seq);// We'll see the parameters we have to send, this should update delivery and end
             $seq->sequencing = true; 
			 
      
		    break;

    }

	if ($seq->exception != null){
		$seq->sequencing = false;
		return $seq;
	}
	
	$seq->sequencing= true;
    return $seq;
}


function scorm_seq_start_sequencing($scoid,$userid,$seq){
	if (!empty($seq->currentactivity)) {
		$seq->delivery = null;
		$seq->exception = 'SB.2.5-1';
		return $seq;
	}
	$sco = get_record('scorm_scoes','scoid',$scoid,'userid',$userid);
	if (($sco->parent == '/') && scorm_is_leaf($sco)) {//if the activity is the root and is leaf
		$seq->delivery = $sco;
	}
	else{
		$ancestors = scorm_get_ancestors($sco);
		$ancestorsroot = array_reverse($ancestors);
		$res = scorm_seq_flow($ancestorsroot[0],'forward',$seq,true,$userid);
		if($res){
			return $res;
		}
		else{
			//return end and exception
		}
	}
}

function scorm_seq_resume_all_sequencing($scoid,$userid,$seq){
	if (!empty($seq->currentactivity)){
		$seq->delivery = null;
		$seq->exception = 'SB.2.6-1';
		return $seq;
	}
	$track = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element','suspendedactivity');
    if (!$track) {
		$seq->delivery = null;
	    $seq->exception = 'SB.2.6-2';
	    return $seq;
	}
	$seq->delivery = get_record('scorm_scoes','scoid',$scoid,'userid',$userid);//we assign the sco to the delivery
	
}

function scorm_seq_continue_sequencing($scoid,$userid,$seq){
	if (empty($seq->currentactivity)) {
		$seq->delivery = null;
		$seq->exception = 'SB.2.7-1';
		return $seq;
	}
	$currentact= $seq->currentactivity;
	if ($currentact->parent != '/') {//if the activity is the root and is leaf
	    $parent = scorm_get_parent ($currentact);

		 if (!isset($parent->flow) || ($parent->flow == false)) {
			$seq->delivery = null;
	        $seq->exception = 'SB.2.7-2';
	        return $seq;
		}

		$res = scorm_seq_flow($currentact,'forward',$seq,false,$userid);
		if($res){
			return $res;
		}
		else{
			//return end and exception
		}

	}
}

function scorm_seq_previous_sequencing($scoid,$userid,$seq){
	if (empty($seq->currentactivity)) {
		$seq->delivery = null;
		$seq->exception = 'SB.2.8-1';
		return $seq;
	}
	
	$currentact= $seq->currentactivity;
	if ($currentact->parent != '/') {//if the activity is the root and is leaf
	    $parent = scorm_get_parent ($activity);
		if (!isset($parent->flow) || ($parent->flow == false)) {
			$seq->delivery = null;
	        $seq->exception = 'SB.2.8-2';
	        return $seq;
		}

		$res = scorm_seq_flow($currentact,'backward',$seq,false,$userid);
		if($res){
			return $res;
		}
		else{
			//return end and exception
		}

	}

}

function scorm_seq_exit_sequencing($scoid,$userid,$seq){
	if (empty($seq->currentactivity)) {
		$seq->delivery = null;
		$seq->exception = 'SB.2.11-1';
		return $seq;
	}

	 if ($seq->active){
		 $seq->endsession = false;
		 $seq->exception = 'SB.2.11-2';
		 return $seq;
	 }
	 $currentact= $seq->currentactivity;
	 if ($currentact->parent == '/'){
		 $seq->endsession = true;
		 return $seq;
	 }
		
	$seq->endsession = false;
	return $seq;
}


function scorm_seq_retry_sequencing($scoid,$userid,$seq){
	if (empty($seq->currentactivity)) {
		$seq->delivery = null;
		$seq->exception = 'SB.2.10-1';
		return $seq;
	}
	if ($seq->active || $seq->suspended){
		$seq->delivery = null;
		$seq->exception = 'SB.2.10-2';
		return $seq;
	}
	
	if (!scorm_is_leaf($seq->currentactivity)){
	    $res = scorm_seq_flow($seq->currentactivity,'forward',$seq,true,$userid);
		if($res != null){
			return $res;
			//return deliver
		}
		else{
			$seq->delivery = null;
		    $seq->exception = 'SB.2.10-3';
		    return $seq;
		}
	}
	else{
		$seq->delivery = $seq->currentactivity;
		return $seq;
	}

}

function scorm_seq_flow ($candidate,$direction,$seq,$childrenflag,$userid){
	//$PREVDIRECTION NOT DEFINED YET

	$activity=$candidate;
	$deliverable=false;
	$previdirection = null;
	$seq = scorm_seq_flow_tree_traversal ($activity,$direction,$childrenflag,$prevdirection,$seq,$userid);
	if($seq->identifiedactivity == null){//if identifies
		$seq->identifiedactivity = $candidate;
		$seq->deliverable = false;
		return $seq;
	}
	else{
		$activity = $seq->identifiedactivity;
		$seq = scorm_seq_flow_activity_traversal($activity,$userid,$direction,$childrenflag,$prevdirection,$seq,$userid);//
		return $seq;

	}
}

function scorm_seq_flow_activity_traversal ($activity, $userid, $direction, $childrenflag, $prevdirection, $seq,$userid){//returns the next activity on the tree, traversal direction, control returned to the LTS, (may) exception
    $activity = scorm_get_sco ($activity);
    $parent = scorm_get_parent ($activity);
   if (!isset($parent->flow) || ($parent->flow == false)) {
		$seq->deliverable = false;
	    $seq->exception = 'SB.2.2-1';
		$seq->nextactivity = $activity;
	    return $seq;
	}
     
    $rulch = scorm_seq_rules_check($sco, 'skipped');
	if ($rulch != null){
	    $seq = scorm_seq_flow_tree_traversal ($activity, $direction, false, $prevdirection, $seq,$userid);//endsession and exception
		if ($seq->identifiedactivity == null){
			$seq->deliverable = false;
			$seq->nextactivity = $activity;
			return $seq;
		}
		else{
			
			if ($prevdirection = 'backward' && $seq->traversaldir == 'backward'){
				$seq = scorm_seq_flow_tree_traversal ($activity,$direction,false,null,$seq,$userid);
				$seq = scorm_seq_flow_activity($seq->identifiedactivity, $userid, $direction, $childrenflag, $prevdirection, $seq,$userid);
			}
			else{
				$seq = scorm_seq_flow_tree_traversal ($activity,$direction,false,null,$seq,$userid);
				$seq = scorm_seq_flow_activity($seq->identifiedactivity, $userid, $direction, $childrenflag, $prevdirection, $seq,$userid);
			}
			return $seq;
		}
	}

	$ch=scorm_check_activity ($activity,$userid);

	if ($ch){

		$seq->deliverable = false;
	    $seq->exception = 'SB.2.2-2';
		$seq->nextactivity = $activity;
	    return $seq;

	}

	if (!scorm_is_leaf($activity)){

		$seq = scorm_seq_flow_tree_traversal ($activity,$direction,true,null,$seq,$userid);

		if ($seq->identifiedactivity == null){
			$seq->deliverable = false;
			$seq->nextactivity = $activity;
			return $seq;
		}

		else{
		    if($direction == 'backward' && $seq->traversaldir == 'forward'){
				$seq = scorm_seq_flow_activity($seq->identifiedactivity, $userid, 'forward', $childrenflag, 'backward', $seq,$userid);
			}
			else{
				scorm_seq_flow_activity($seq->identifiedactivity, $userid, $direction, $childrenflag, null, $seq,$userid);
			}
			return $seq;
		}

	}

    $seq->deliverable = true;
	$seq->nextactivity = $activity;
    return $seq;

}
function scorm_seq_flow_tree_traversal ($activity,$direction,$childrenflag,$prevdirection,$seq,$userid){

	$revdirection = false;
	$parent = scorm_get_parent ($activity);
	$children = scorm_get_available_children ($parent);
	$siz = sizeof ($children);

    if (($prevdirection != null && $prevdirection == 'backward') && ($children[$siz-1]->id == $activity->id)){
		$direction = 'backward';
		$children[0] = $activity;
		$revdirection = true;
	}

	if($direction = 'forward'){
		$ancestors = scorm_get_ancestors($activity);
		$ancestorsroot = array_reverse($ancestors);
		$preorder = scorm_get_preorder ($ancestorsroot);
		$siz= sizeof ($preorder);
		if (($activity->id == $preorder[$siz-1]->id) || (($activity->parent == '/') && !($childrenflag))){
			scorm_seq_terminate_descent($ancestorsroot,$userid);
			$seq->endsession = true;
			$seq->nextactivity = null;
			return $seq;
		}
		if (scorm_is_leaf ($activity) || !$childrenflag){
			if ($children[$siz-1]->id == $activity->id){
	
				$seq = scorm_seq_flow_tree_traversal ($parent, $direction, false, null, $seq,$userid);
				// I think it's not necessary to do a return in here
			}
			else{
				$parent = scorm_get_parent($activity);
				$children = scorm_get_available_children($parent);
				$seq->traversaldir = $direction;
				$sib = scorm_get_siblings($activity);
				$pos = array_search($sib, $activity);
				if ($pos !== false) {
				    if ($pos != sizeof ($sib)){
				        $seq->nextactivity = $sib [$pos+1];
						return $seq;
				    }
				    else{
					    $ch = scorm_get_children($sib[0]);
					    $seq->nextactivity = $ch[0];
						return $seq;
				    }
				}
		    }
		}
		else{
			if (!empty ($children)){
				$seq->traversaldir = $direction;
                $seq->nextactivity = $children[0];
				return $seq;
			}
			else{
				$seq->traversaldir = null;
                $seq->nextactivity = $children[0];
				$seq->exception = 'SB.2.1-2';
				return $seq;
			}
		}
		
	}
	if($direction = 'backward'){
		
		 if ($activity->parent == '/'){
			$seq->traversaldir = null;
            $seq->nextactivity = null;
			$seq->exception = 'SB.2.1-3';
			return $seq;
		 }
		 if (scorm_is_leaf ($activity) || !$childrenflag){
			 if (!$revdirection){
				 if (isset($parent->forwardonly) && ($parent->forwardonly == true)) {
					 $seq->traversaldir = null;
                     $seq->nextactivity = null;
			         $seq->exception = 'SB.2.1-4';
					 return $seq;
				 }
			 }
			 if ($children[0]->id == $activity->id){
				$seq = scorm_seq_flow_tree_traversal ($parent, 'backward', false, null, $seq);
				return $seq;
			 }
			 else{
				$ancestors = scorm_get_ancestors($activity);
		        $ancestorsroot = array_reverse ($ancestors);
				$preorder = scorm_get_preorder ($ancestorsroot);
				$pos = array_search($preorder, $children[$siz]);
				$preord = array_slice($preorder, 0, $pos-1);
				$revpreorder = array_reverse($preord);
				$position = array_search($revpreorder, $activity);
				$seq->nextactivity = $revpreorder[$pos+1];
				$seq->traversaldir = $direction;
				return $seq;
			 }
		 }
		 else{
			 if (!empty($children)){
				 $activity = scorm_get_sco($activity->id);
				 if (isset($parent->flow) && ($parent->flow == true)) {
					 $children = scorm_get_children ($activity);
					 $seq->traversaldir = 'forward';
                     $seq->nextactivity = $children[0];
					 return $seq;
			         
				 }
				 else{
					 $children = scorm_get_children ($activity);
					 $seq->traversaldir = 'backward';
                     $seq->nextactivity = $children[sizeof($children)-1];
					 return $seq;
				 }

			 }
			 else{
				 
					 $seq->traversaldir = null;
                     $seq->nextactivity = null;
					 $seq->exception = 'SB.2.1-2';
					 return $seq;
			 }
		 }

	}


}
function scorm_check_activity ($activity,$userid){
	$act = scorm_seq_rules_check($activity,'disabled');
	if ($act != null){
		return true;
	}
    if(scorm_limit_cond_check ($activity,$userid)){
		return true;
	}
	return false;


}

function scorm_limit_cond_check ($activity,$userid){

    if (isset($activity->tracked) && ($activity->tracked == 0)){
		
		return false;
	}

	if (scorm_seq_is('active',$activity->id,$userid) || scorm_seq_is('suspended',$activity->id,$userid)){
		return false;
	}

    if (!isset($activity->limitcontrol) || ($activity->limitcontrol == 1)){	
		$r = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','activityattemptcount');
		if (scorm_seq_is('activityprogressstatus',$activity->id,$userid) && ($r->value >=$activity->limitattempt)){
			return true;
		}
	}

	 if (!isset($activity->limitabsdurcontrol) || ($activity->limitabsdurcontrol == 1)){	
		$r = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','activityabsoluteduration');
		if (scorm_seq_is('activityprogressstatus',$activity->id,$userid) && ($r->value >=$activity->limitabsduration)){
			return true;
		}
	}

	if (!isset($activity->limitexpdurcontrol) || ($activity->limitexpdurcontrol == 1)){	
		$r = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','activityexperiencedduration');
		if (scorm_seq_is('activityprogressstatus',$activity->id,$userid) && ($r->value >=$activity->limitexpduration)){
			return true;
		}
	}
    
	 if (!isset($activity->limitattabsdurcontrol) || ($activity->limitattabsdurcontrol == 1)){	
		$r = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','attemptabsoluteduration');
		if (scorm_seq_is('activityprogressstatus',$activity->id,$userid) && ($r->value >=$activity->limitattabsduration)){
			return true;
		}
	}

	if (!isset($activity->limitattexpdurcontrol) || ($activity->limitattexpdurcontrol == 1)){	
		$r = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','attemptexperiencedduration');
		if (scorm_seq_is('activityprogressstatus',$activity->id,$userid) && ($r->value >=$activity->limitattexpduration)){
			return true;
		}
	}

	if (!isset($activity->limitbegincontrol) || ($activity->limitbegincontrol == 1)){	
		$r = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','begintime');
		if (time()>=$activity->limitbegintime){
			return true;
		}
	}

	if (!isset($activity->limitbegincontrol) || ($activity->limitbegincontrol == 1)){	
		if (time()<$activity->limitbegintime){
			return true;
		}
	}

	if (!isset($activity->limitendcontrol) || ($activity->limitendcontrol == 1)){	

		if (time()>$activity->limitendtime){
			return true;
		}
	}
	return false;


}


function scorm_seq_choice_sequencing($sco,$userid,$seq){

	$avchildren = Array ();
	$comancestor = null;
	$traverse = null;

	if ($sco == null){
		$seq->delivery = null;
		$seq->exception = 'SB.2.9-1';
		return $seq;
	}

    $ancestors = scorm_get_ancestors($sco);
    $arrpath = array_reverse($ancestors);
	array_push ($arrpath,$sco);//path from the root to the target

	foreach ($arrpath as $activity){

        if ($activity->parent != '/') {
			$avchildren = scorm_get_available_children (scorm_get_parent($activity));
			$position = array_search($avchildren, $activity);
            if ($position !== false){
				$seq->delivery = null;
		        $seq->exception = 'SB.2.9-2';
		        return $seq;
			}
		}

		if (scorm_seq_rules_check($activity,'hidefromchoice' != null)){

			$seq->delivery = null;
		    $seq->exception = 'SB.2.9-3';
		    return $seq;

		}

	}

	if ($sco->parent != '/') {
		$parent = scorm_sco_get_parent ($sco);
		if ( isset($parent->choice) && ($parent->choice == false)){
			$seq->delivery = null;
		    $seq->exception = 'SB.2.9-4';
		    return $seq;
		}
	}

	if ($seq->currentactivity != null){
        $commonpos = scorm_find_common_ancestor($ancestors,$seq->currentactivity);
		$comancestor = $arrpath [$commonpos];
	}
	else{
		$comancestor = $arrpath [0];
	}

	if($seq->currentactivity === $sco) {
        break;
	}

	$sib = scorm_get_siblings($seq->currentactivity);
	$pos = array_search($sib, $sco);

	if ($pos !== false){

		$siblings = array_slice($sib, 0, $pos-1);

		if (empty($siblings)){

			$seq->delivery = null;
		    $seq->exception = 'SB.2.9-5';
		    return $seq;

		}
        
		$children = scorm_get_children (scorm_get_parent ($sco));
		$pos1 = array_search($children, $sco);
		$pos2 = array_search($seq->currentactivity, $sco);
		if ($pos1>$pos2){
			$traverse = 'forward';
		}
		else{
			$traverse = 'backward';
		}

		foreach ($siblings as $sibling){
			$seq = scorm_seq_choice_activity_traversal($sibling,$userid,$seq,$traverse);
		    if(!$seq->reachable){
			    $seq->delivery = null;
			    return $seq;
		    }
		}
		break;

	}

    if($seq->currentactivity == null || $seq->currentactivity == $comancestor){
		$commonpos = scorm_find_common_ancestor($ancestors,$seq->currentactivity);
		$comtarget = array_slice($ancestors, 1,$commonpos-1);//path from the common ancestor to the target activity
		$comtarget = array_reverse($comtarget);

		if (empty($comtarget)){
			$seq->delivery = null;
		    $seq->exception = 'SB.2.9-5';
		    return $seq;
		}
		foreach ($comtarget as $act){
			$seq = scorm_seq_choice_activity_traversal($act,$userid,$seq,'forward');
		    if(!$seq->reachable){
			    $seq->delivery = null;
			    return $seq;
		    }
            $act = scorm_get_sco ($acti->id);
			if(scorm_seq_is('active',$act->id,$userid) && ($act->id != $comancestor->id && $act->preventactivation)){//adlseq:can i write it like another property for the $seq object?
				$seq->delivery = null;
		        $seq->exception = 'SB.2.9-6';
		        return $seq;
			}
		}
		break;

	}

	if ($comancestor->id == $sco->id){

        $ancestorscurrent = scorm_get_ancestors($seq->currentactivity);
		$possco = array_search ($ancestorscurrent, $sco);
		$curtarget = array_slice($ancestorscurrent,0,$possco);//path from the current activity to the target

		if (empty($curtarget)){
			$seq->delivery = null;
		    $seq->exception = 'SB.2.9-5';
		    return $seq;
		}
        $i=0;
		foreach ($curtarget as $activ){
			$i++;
			if ($i != sizeof($curtarget)){
				if ( isset($activ->choiceexit) && ($activ->choiceexit == false)){
					$seq->delivery = null;
		            $seq->exception = 'SB.2.9-7';
		            return $seq;
				}
			}
		}
		break;
	}

	if (array_search ($ancestors, $comancestor)!== false){
		$ancestorscurrent = scorm_get_ancestors($seq->currentactivity);
		$commonpos = scorm_find_common_ancestor($ancestors,$sco);
		$curcommon = array_slice($ancestorscurrent,0,$commonpos-1);
		if(empty($curcommon)){
			$seq->delivery = null;
		    $seq->exception = 'SB.2.9-5';
		    return $seq;
		}

		$constrained = null;
		foreach ($curcommon as $acti){
			$acti = scorm_get_sco($acti->id);
			if ( isset($acti->choiceexit) && ($acti->choiceexit == false)){
					$seq->delivery = null;
		            $seq->exception = 'SB.2.9-7';
		            return $seq;
			}
			if ($constrained == null){
				if($acti->constrainchoice == true){
					$constrained = $acti;
				}
			}
		}
		if ($constrained != null){
            $fwdir = scorm_get_preorder($constrained);

		    if(array_search ($fwdir, $sco)!== false){
				$traverse = 'forward';
			}
			else{
				$traverse = 'backward';
			}
			$seq = scorm_seq_choice_flow ($constrained, $traverse, $seq);
			$actconsider = $seq->identifiedactivity;
			$avdescendents = Array();
			$avdescendents= scorm_get_available_descendents ($actconsider);
			if (array_search ($avdescendents, $sco) !== false && $sco->id != $actconsider->id && $constrained->id != $sco->id ){
				$seq->delivery = null;
		        $seq->exception = 'SB.2.9-8';
		        return $seq;
			}

//CONTINUE 11.5.5
		}

		$commonpos = scorm_find_common_ancestor($ancestors,$seq->currentactivity);
		$comtarget = array_slice($ancestors, 1,$commonpos-1);//path from the common ancestor to the target activity
		$comtarget = array_reverse($comtarget);

		if (empty($comtarget)){
			$seq->delivery = null;
		    $seq->exception = 'SB.2.9-5';
		    return $seq;
		}

		$fwdir = scorm_get_preorder($seq->currentactivity);

		if(array_search ($fwdir, $sco)!== false){

		    foreach ($comtarget as $act){
			    $seq = scorm_seq_choice_activity_traversal($act,$userid,$seq,'forward');
		        if(!$seq->reachable){
			        $seq->delivery = null;
			        return $seq;
		        }
                $act = scorm_get_sco($act->id);
			    if(scorm_seq_is('active',$act->id,$userid) && ($act->id != $comancestor->id && ($act->preventactivation == true))){
				    $seq->delivery = null;
		            $seq->exception = 'SB.2.9-6';
		            return $seq;
			    }
		    }

		}
		else{
			foreach ($comtarget as $act){
				$act = scorm_get_sco($act->id);
			    if(scorm_seq_is('active',$act->id,$userid) && ($act->id != $comancestor->id && ($act->preventactivation==true))){
				    $seq->delivery = null;
		            $seq->exception = 'SB.2.9-6';
		            return $seq;
			    }
		    }
		}
	    break;	
	}

	if(scorm_is_leaf ($sco)){
		$seq->delivery = $sco;
		$seq->exception = 'SB.2.9-6';
		return $seq;
	}

    $seq = scorm_seq_flow ($sco,'forward',$seq,true,$userid);
    if ($seq->deliverable == false){
		scorm_terminate_descendent_attempts($comancestor,$userid,$seq);
		scorm_seq_end_attempt($comancestor,$userid,$seq->attempt);
		$seq->currentactivity = $sco;
		$seq->delivery = null;
		$seq->exception = 'SB.2.9-9';
		return $seq;

	}
	else{
		return $seq;
	}
  
}

function scorm_seq_choice_flow ($constrained, $traverse, $seq){
	$seq = scorm_seq_choice_flow_tree ($constrained, $traverse, $seq);
	if ($seq->identifiedactivity == null){
        $seq->identifiedactivity = $constrained;
		return $seq;
	}
	else{
		return $seq;
	}
}

function scorm_seq_choice_flow_tree ($constrained, $traverse, $seq){
	$islast = false;
	$parent = scorm_get_parent ($constrained);
	if ($traverse== 'forward'){
		$preord = scorm_get_preorder ($constrained);
		if (sizeof($preorder) == 0 || (sizeof($preorder) == 0 && $preorder[0]->id = $constrained->id)){
			$islast = true;//the function is the last activity available
		}
		if ($constrained->parent == '/' || $islast){
			$seq->nextactivity = null;
			return $seq;
		}
		$avchildren = scorm_get_available_children ($parent);//available children
		if ($avchildren [sizeof($avchildren)-1]->id == $constrained->id){
			$seq = scorm_seq_choice_flow_tree ($parent, 'forward', $seq);
			return $seq;
		}
		else{
			$i=0;
			while($i < sizeof($avchildren)){
				if ($avchildren [$i]->id == $constrained->id){
					$seq->nextactivity = $avchildren [$i+1];
					return $seq;
				}
				else{
					$i++;
				}
			}
		}

	}

	if ($traverse== 'backward'){
		if($constrained->parent == '/' ){
			$seq->nextactivity = null;
			return $seq;
		}

		$avchildren = scorm_get_available_children ($parent);//available children
		if ($avchildren [0]->id == $constrained->id){
			$seq = scorm_seq_choice_flow_tree ($parent, 'backward', $seq);
			return $seq;
		}
		else{
			$i=sizeof($avchildren)-1;
			while($i >=0){
				if ($avchildren [$i]->id == $constrained->id){
					$seq->nextactivity = $avchildren [$i-1];
					return $seq;
				}
				else{
					$i--;
				}
			}
		}
	}
}
function scorm_seq_choice_activity_traversal($activity,$userid,$seq,$direction){

	if($direction == 'forward'){

		$act = scorm_seq_rules_check($activity,'stopforwardtraversal');

		if($act != null){
			$seq->reachable = false;
			$seq->exception = 'SB.2.4-1';
		    return $seq;
		}
		$seq->reachable = false;
		return $seq;
	}

	if($direction == 'backward'){
		$parentsco = scorm_get_parent($activity);
		if($parentsco!= null){
			 if (isset($parentsco->forwardonly) && ($parentsco->forwardonly == true)){
				 $seq->reachable = false;
			     $seq->exception = 'SB.2.4-2';
		         return $seq;
			 }
			 else{
			    $seq->reachable = false;
			    $seq->exception = 'SB.2.4-3';
		        return $seq;
		     }
		}
	}
	$seq->reachable = true;
	return $seq;

}

//Delivery Request Process

function scorm_sequencing_delivery($scoid,$userid,$seq){

	if(!scorm_is_leaf ($seq->delivery)){
		$seq->deliveryvalid = false;
		$seq->exception = 'DB.1.1-1';
		return $seq;
	}
	$ancestors = scorm_get_ancestors($seq->delivery);
    $arrpath = array_reverse($ancestors);
	array_push ($arrpath,$seq->delivery);//path from the root to the target

	if (empty($arrpath)){
		$seq->deliveryvalid = false;
		$seq->exception = 'DB.1.1-2';
		return $seq;
	}

	foreach ($arrpath as $activity){
	    if(scorm_check_activity ($activity,$userid)){
		    $seq->deliveryvalid = false;
	        $seq->exception = 'DB.1.1-3';
	        return $seq;
	    }
	}

	$seq->deliveryvalid = true;
	return $seq;

}

function scorm_content_delivery_environment ($seq,$userid){

	$act = $seq->currentactivity;
	if(scorm_seq_is('active',$act->id,$userid)){
		$seq->exception = 'DB.2-1';
	    return $seq;
	}
	$track = get_record('scorm_scoes_track','scoid',$act->id,'userid',$userid,'element','suspendedactivity');
	if ($track != null){
		$seq = scorm_clear_suspended_activity($seq->delivery, $seq);

	}
	$seq = scorm_terminate_descendent_attempts ($seq->delivery,$userid,$seq);
	$ancestors = scorm_get_ancestors($seq->delivery);
    $arrpath = array_reverse($ancestors);
	array_push ($arrpath,$seq->delivery);
	foreach ($arrpath as $activity){
		if(!scorm_seq_is('active',$activity->id,$userid)){
			if(!isset($activity->tracked) || ($activity->tracked == 1)){
				if(!scorm_seq_is('suspended',$activity->id,$userid)){
					$r = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','activityattemptcount');
					$r->value = ($r->value)+1;
					update_record ('scorm_scoes_track',$r);
					if ($r->value == 1){
						scorm_seq_set('activityprogressstatus', $activity->id, $userid, 'true');
					}
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'objectiveprogressstatus', 'false');
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'objectivesatisfiedstatus', 'false');
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'objectivemeasurestatus', 'false');
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'objectivenormalizedmeasure', 0.0);

					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'attemptprogressstatus', 'false');
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'attemptcompletionstatus', 'false');
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'attemptabsoluteduration', 0.0);
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'attemptexperiencedduration', 0.0);
					scorm_insert_track($userid, $activity->scorm, $activity->id, 0, 'attemptcompletionamount', 0.0);
				}
			}
            scorm_seq_set('active', $activity->id, $userid, 'true');
		}
	}
	$seq->delivery = $seq->currentactivity;
	scorm_seq_set('suspendedactivity', $activity->id, $userid, 'false');

	//ONCE THE DELIVERY BEGINS (How should I check that?)

    if(isset($activity->tracked) || ($activity->tracked == 0)){
		//How should I track the info and what should I do to not record the information for the activity during delivery? 
		$atabsdur = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','attemptabsoluteduration');
		$atexpdur = get_record('scorm_scoes_track','scoid',$activity->id,'userid',$userid,'element','attemptexperiencedduration');
	}
	return $seq;

 
}
function scorm_clear_suspended_activity($act,$seq){
	$currentact= $seq->currentactivity;
	$track = get_record('scorm_scoes_track','scoid',$currentact->id,'userid',$userid,'element','suspendedactivity');
	if ($track != null){
		$ancestors = scorm_get_ancestors($act);
        $commonpos = scorm_find_common_ancestor($ancestors,$currentact);
        if ($commonpos !== false) {
            if ($activitypath = array_slice($ancestors,0,$commonpos)) {
				if (!empty ($activitypath)){

                    foreach ($activitypath as $activity) {
					    if (scorm_is_leaf($activity)){
							scorm_seq_set('suspended',$activity->id,$userid,false);
						}
						else{
							$children = scorm_get_children($activity);
							$bool= false; 
							foreach ($children as $child){
								if(scorm_seq_is('suspended',$child->id,$userid)){
									$bool= true;
								}
							}
                             if(!$bool){
							    scorm_seq_set('suspended',$activity->id,$userid,false);
							 }
						}
				    }
				}
			}
		}
		scorm_seq_set('suspendedactivity',$act->id,$userid,false);

	}
}

function scorm_select_children_process($scoid,$userid){

	$sco = scorm_get_sco($scoid);
    if (!scorm_is_leaf($sco)){
		if(!scorm_seq_is('suspended',$scoid,$userid) && !scorm_seq_is('active',$scoid,$userid)){
			$r = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element','selectiontiming');

             switch($r->value) {

                case 'oneachnewattempt':
				case 'never':
                break;
             
                case 'once':
                    if(!scorm_seq_is('activityprogressstatus',$scoid,$userid)){
					    if(scorm_seq_is('selectioncountsstatus',$scoid,$userid)){
					        $childlist = '';
							$res = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element','selectioncount');
							$i = ($res->value)-1;
							$children = scorm_get_children ($sco);

							while ($i>=0){
								$pos = array_rand($children);
								array_push($childlist,$children [$pos]);
								array_splice($children,$pos,1);
								$i--;
							}
							sort ($childlist);
							$clist = serialize ($childlist);
							scorm_seq_set('availablechildren', $scoid, $userid, false);
							scorm_seq_set('availablechildren', $scoid, $userid, $clist);


				        }
				    }
                break;
               
            }

		}
	}
}

function scorm_randomize_children_process($scoid,$userid){

	$sco = scorm_get_sco($scoid);
    if (!scorm_is_leaf($sco)){
		if(!scorm_seq_is('suspended',$scoid,$userid) && !scorm_seq_is('active',$scoid,$userid)){
			$r = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element','randomizationtiming');

             switch($r->value) {

                
				case 'never':
                break;
             
                case 'oneachnewattempt':
                case 'once':
                    if(!scorm_seq_is('activityprogressstatus',$scoid,$userid)){
					    if(scorm_seq_is('randomizechildren',$scoid,$userid)){
					        $childlist = array();
							$res = scorm_get_available_children($sco);
							$i = sizeof($res)-1;
							$children = $res->value;

							while ($i>=0){
								$pos = array_rand($children);
								array_push($childlist,$children [$pos]);
								array_splice($children,$pos,1);
								$i--;
							}
							
							$clist = serialize ($childlist);
							scorm_seq_set('availablechildren', $scoid, $userid, false);
							scorm_seq_set('availablechildren', $scoid, $userid, $clist);


				        }
				    }
                break;
			 
               
			   
            }

		}
	}
}

function scorm_terminate_descendent_attempts ($activity,$userid,$seq){
	$ancestors = scorm_get_ancestors($seq->currentactivity);
    $commonpos = scorm_find_common_ancestor($ancestors,$activity);
        if ($commonpos !== false) {
            if ($activitypath = array_slice($ancestors,1,$commonpos-2)) {
				if (!empty ($activitypath)){

                    foreach ($activitypath as $sco) {
						scorm_seq_end_attempt($sco,$userid,$seq->attempt);
					   
				    }
				}
			}
		} 
}

function scorm_sequencing_exception($seq){
    if($seq->exception != null){
		switch($seq->exception){

			case 'NB.2.1-1':
                notify("Sequencing session has already begun");
            break;
            case 'NB.2.1-2':
                notify("Sequencing session has not begun");
            break;
			case 'NB.2.1-3':
                notify("Suspended activity is not defined");
            break;
			case 'NB.2.1-4':
                notify("Flow Sequencing Control Model Violation");
            break;
			case 'NB.2.1-5':
                notify("Flow or Forward only Sequencing Control Model Violation");
            break;
			case 'NB.2.1-6':
                notify("No activity is previous to the root");
            break;
			case 'NB.2.1-7':
                notify("Unsupported Navigation Request");
            break;
			case 'NB.2.1-8':
                notify("Choice Exit Sequencing Control Model Violation");
            break;
			case 'NB.2.1-9':
                notify("No activities to consider");
            break;
			case 'NB.2.1-10':
                notify("Choice Sequencing Control Model Violation");
            break;
			case 'NB.2.1-11':
                notify("Target Activity does not exist");
            break;
			case 'NB.2.1-12':
                notify("Current Activity already terminated");
            break;
			case 'NB.2.1-13':
                notify("Undefined Navigation Request");
            break;

			case 'TB.2.3-1':
                notify("Current Activity already terminated");
            break;
			case 'TB.2.3-2':
                notify("Current Activity already terminated");
            break;
			case 'TB.2.3-4':
                notify("Current Activity already terminated");
            break;
			case 'TB.2.3-5':
                notify("Nothing to suspend; No active activities");
            break;
			case 'TB.2.3-6':
                notify("Nothing to abandon; No active activities");
            break;

			case 'SB.2.1-1':
                notify("Last activity in the tree");
            break;
            case 'SB.2.1-2':
                notify("Cluster has no available children");
            break;
			case 'SB.2.1-3':
                notify("No activity is previous to the root");
            break;
			case 'SB.2.1-4':
                notify("Forward Only Sequencing Control Model Violation");
            break;

			case 'SB.2.2-1':
                notify("Flow Sequencing Control Model Violation");
            break;
			case 'SB.2.2-2':
                notify("Activity unavailable");
            break;

			case 'SB.2.3-1':
                notify("Forward Traversal Blocked");
            break;
            case 'SB.2.3-2':
                notify("Forward Only Sequencing Control Model Violation");
            break;
			case 'SB.2.3-3':
                notify("No activity is previous to the root");
            break;

			case 'SB.2.5-1':
                notify("Sequencing session has already begun");
            break;

			case 'SB.2.6-1':
                notify("Sequencing session has already begun");
            break;
			case 'SB.2.6-2':
                notify("No Suspended activity is defined");
            break;

            case 'SB.2.7-1':
                notify("Sequencing session has not begun");
            break;
			case 'SB.2.7-2':
                notify("Flow Sequencing Control Model Violation");
            break;

			case 'SB.2.8-1':
                notify("Sequencing session has not begun");
            break;
			case 'SB.2.8-2':
                notify("Flow Sequencing Control Model Violation");
            break;

			case 'SB.2.9-1':
                notify("No target for Choice");
            break;
			case 'SB.2.9-2':
                notify("Target Activity does not exist or is unavailable");
            break;
			case 'SB.2.9-3':
                notify("Target Activity hidden from choice");
            break;
			case 'SB.2.9-4':
                notify("Choice Sequencing Control Model Violation");
            break;
			case 'SB.2.9-5':
                notify("No activities to consider");
            break;
			case 'SB.2.9-6':
                notify("Unable to activate target; target is not a child of the Current Activity");
            break;
			case 'SB.2.9-7':
                notify("Choice Exit Sequencing Control Model Violation");
            break;
			case 'SB.2.9-8':
                notify("Unable to choose target activity - constrained choice");
            break;
			case 'SB.2.9-9':
                notify("Choice Request Prevented by Flow-only Activity");
            break;

			case 'SB.2.10-1':
                notify("Sequencing session has not begun");
            break;
			case 'SB.2.10-2':
                notify("Current Activity is active or suspended");
            break;
			case 'SB.2.10-3':
                notify("Flow Sequencing Control Model Violation");
            break;
            
            case 'SB.2.11-1':
                notify("Sequencing session has not begun");
            break;
			case 'SB.2.11-2':
                notify("Current Activity has not been terminated");
            break;

			case 'SB.2.12-2':
                notify("Undefined Sequencing Request");
            break;

			case 'DB.1.1-1':
                notify("Cannot deliver a non-leaf activity");
            break;
			case 'DB.1.1-2':
                notify("Nothing to deliver");
            break;
			case 'DB.1.1-3':
                notify("Activity unavailable");
            break;

			case 'DB.2-1':
                notify("Identified activity is already active");
            break;
					 
		}
               
	}
}




?>
