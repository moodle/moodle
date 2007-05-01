<?php // $Id$
require ('scormlib.php');

function scorm_seq_evaluate($scoid,$usertracks) {
    return true;
}

function scorm_seq_overall ($scoid,$userid,$request) {
    $seq = scorm_seq_navigation($scoid,$userid,$request);
    if ($seq->navigation) {
        if ($seq->termination != null) {
            $seq = scorm_seq_termination($scoid,$userid,$seq);
        }
        if ($seq->sequencing != null) {
        //    scorm_seq_sequencing($scoid,$userid,$seq);
        }
        if ($seq->target != null) {
        //    scorm_sequencing_delivery($scoid,$userid,$seq);
        }
    }
    if ($seq->exception != null) {
    //    scorm_sequencing_exception($seq);
    }
    return 'true';
}

function scorm_seq_navigation ($scoid,$userid,$request) {
    /// Sequencing structure
    $seq = new stdClass();
    $seq->currentactivity = scorm_get_sco($scoid);
	$seq->traversaldir = null;
	$seq->nextactivity = null;
	
	$seq->identifiedactivity = null;
	$seq->availablechildren = scorm_get_children ($seq->currentactivity);//Added by Carlos
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
	$seq->constrainedchoice = true;

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
                if ($track = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element','suspendedactivity')) {
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
						//BOBO'S CODE
                       /* if (isset($parentsco->flow) && ($parent->flow == true)) {//I think it's parentsco
                            // Current activity is active !
                            if ($request == 'continue_') {
                                $seq->navigation = true;
                                $seq->termination = 'exit';
                                $seq->sequencing = 'continue';
                            } else {
                                if (isset($parentsco->forwardonly) && ($parent->forwardonly == false)) {
                                    $seq->navigation = true;
                                    $seq->termination = 'exit';
                                    $seq->sequencing = 'previous';
                                } else {
                                    $seq->exception = 'NB.2.1-5'; /// Violates control mode
                                }
                            }
                        }*/
						// CARLOS' CODE
						 if (!isset($parentsco->flow) || ($parentsco->flow == true)) {//I think it's parentsco
                            // Current activity is active !
							if (scorm_seq_is('active',$sco->id,$userid)) {
                                if ($request == 'continue_') {
                                    $seq->navigation = true;
                                    $seq->termination = 'exit';
                                    $seq->sequencing = 'continue';
                                } else {
                                    if (isset($parentsco->forwardonly) && ($parentsco->forwardonly == false)) {
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
            scorm_seq_end_attempt($sco,$userid);
            $seq = scorm_seq_exit_action_rules($seq,$userid);
            do {
                $exit = false;// I think this is false. Originally this was true
                $seq = scorm_seq_post_cond_rules($seq,$userid);
                if ($seq->termination == 'exitparent') {
                    if ($sco->parent != '/') {
                        $sco = scorm_get_parent($sco);
                        $seq->currentactivity = $sco;
                        $seq->active = scorm_seq_is('active',$sco->id,$userid);
                        scorm_seq_end_attempt($sco,$userid);
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
                scorm_seq_end_attempt($sco,$userid);
            }
            /// Terminate Descendent Attempts Process

			
            if ($ancestors = scorm_get_ancestors($sco)) { 
                foreach ($ancestors as $ancestor) {
                    scorm_seq_end_attempt($ancestor,$userid);
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

function scorm_seq_end_attempt($sco,$userid) {
    if (scorm_is_leaf($sco)) {
        if (!isset($sco->tracked) || ($sco->tracked == 1)) {
            if (!scorm_seq_is('suspended',$sco->id,$userid)) {
                if (!isset($sco->completionsetbycontent) || ($sco->completionsetbycontent == 0)) {
                   // if (!scorm_seq_is('attemptprogressstatus',$sco->id,$userid,$attempt)) {
                   if (!scorm_seq_is('attemptprogressstatus',$sco->id,$userid)) { 
                        scorm_seq_set('attemptprogressstatus',$sco->id,$userid);
                        scorm_seq_set('attemptcompletionstatus',$sco->id,$userid);
                    }
                }
                if (!isset($sco->objectivesetbycontent) || ($sco->objectivesetbycontent == 0)) {
                    if ($objectives = $sco->objectives) {
                        foreach ($objectives as $objective) {
                            if ($objective->primary) {
                                if (!scorm_seq_objective_progress_status($sco,$userid,$objective)) {
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

//function scorm_seq_is($what, $scoid, $userid, $attempt=0) {
function scorm_seq_is($what, $scoid, $userid) {
    /// Check if passed activity $what is active
    $active = false;
    if ($track = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element',$what)) {
        $active = true;
    }
    return $active;
}

//function scorm_seq_set($what, $scoid, $userid, $attempt=0, $value='true') {
function scorm_seq_set($what, $scoid, $userid, $value='true') {
    /// set passed activity to active or not
    if ($value == false) {
        delete_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element',$what);
    } else {
        $sco = scorm_get_sco($scoid);
        scorm_insert_track($userid, $sco->scorm, $sco->id, 0, $what, $value);
    }
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
				
                scorm_seq_end_attempt($ancestor,$userid);
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
		$children = get_children($sco);
        foreach ($children as $child){
		    $child = scorm_get_sco ($child);
			if (!isset($child->tracked) || ($child->tracked == 1)){
				//check if we haven't done any attempt to see if this activity has been tracked
				//it could be $child->tracked == true

			    $rolledupobjective = null;// we set the rolled up activity to undefined
				$objectives = get_records('scorm_seq_objective','scoid',$child->id);
                foreach ($objective as $objective){
		            if ($objective->primaryobj == true){//Objective contributes to rollup I'm using primaryobj field, but not 
		                $rolledupobjective = $objective;
			            break;
		            }
 	            }
				if ($rolledupobjective != null){

					$measureweight = get_record('scorm_scoes_track','scoid',$child->id,'userid',$userid,'element','objectivemeasureweight');
					$countedmeasures = $countedmeasures + ($measureweight->value);
					if (!scorm_seq_objective_measure_status($sco,$userid,$objective)) {
						$normalizedmeasure = get_record('scorm_scoes_track','scoid',$child->id,'userid',$userid,'element','objectivenormalizedmeasure');
						$totalmeasure = $totalmeasure + (($normalizedmeasure->value) * ($measureweight->value));
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
				scorm_seq_set('normalizedmeasure',$sco->id,$userid,$val);
		        
			}
			else{
				scorm_seq_set('objectivemeasurestatus',$sco->id,$userid,false);
				
			}
		}

	}
	
}

function scorm_seq_objective_rollup_measure($sco,$userid){
	$targetobjective = null;
	

	$objectives = get_records('scorm_seq_objective','scoid',$sco->id);
    foreach ($objectives as $objective){
	    if ($objective->primaryobj == true){//Objective contributes to rollup I'm using primaryobj field, but not 
		    $targetobjective = $objective;
			break;
		}
 	}
	if ($targetobjective != null){

		if($targetobjective->satisfiedbymeasure){

            
            if (!scorm_seq_objective_progress_status($sco,$userid,$targetobjective)) {

                scorm_seq_set('objectiveprogressstatus',$sco->id,$userid,false);
                                
            }

			else{
				$active = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','active');
				$isactive = $active->value;

				$normalizedmeasure = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','objectivenormalizedmeasure');

				if (!$isactive || ($isactive /*&&// measuresatisfactionif*/ )){//This condition is really odd. It's in the SeqNav.pdf on page 193, line 3.1.2.1
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
		scorm_seq_set('attemptcompletionstatus',$sco->id,$userid,false);
		scorm_seq_set('attemptprogressstatus',$sco->id,$userid);

	}
    if(scorm_seq_rollup_rule_check($sco,$userid,'completed')){
		//incomplete rollup action
		scorm_seq_set('attemptcompletionstatus',$sco->id,$userid);
		scorm_seq_set('attemptprogressstatus',$sco->id,$userid);
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
	$r = get_record('scorm_scoes_track','scoid',$sco->id,'userid',$userid,'element','activityattemptcount');
	if ($action == 'satisfied' || $action == 'notsatisfied'){
	  if (!scorm_seq_is('rollupobjectivesatisfied',$sco->id,$userid)){
		$included = true;
		if (($action == 'satisfied' /*&& adlseqRequiredforSatisfied == 'ifNotSuspended') || ($action == 'notsatisfied' && adlseqRequiredforNotSatisfied == 'ifNotSuspended'*/)){
			
			if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || ((($r->value)>0)&& !scorm_seq_is('suspended',$sco->id,$userid))){
				$included = false;
			}

		}
		else{
			if (($action == 'satisfied' /*&& adlseqRequiredforSatisfied == 'ifAttempted') || ($action == 'notsatisfied' && adlseqRequiredforNotSatisfied == 'ifAttempted'*/)){
			    if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || (($r->value) == 0)){
				    $included = false;
			    }
            }
			else{
				if (($action == 'satisfied' /*&& adlseqRequiredforSatisfied == 'ifNotSkipped') || ($action == 'notsatisfied' && adlseqRequiredforNotSatisfied == 'ifNotSkipped'*/)){
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
		if (!scorm_seq_is('rollupprogresscompletion',$sco->id,$userid)){
		    $included = true;

            if (($action == 'completed' /*&& adlseqRequiredForCompleted == 'ifNotSuspended') || ($action == 'incomplete' && adlseqRequiredForIncomplete == 'ifNotSuspended'*/)){

			    if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || ( (($r->value)>0)&& !scorm_seq_is('suspended',$sco->id,$userid))){
				    $included = false;
			    }

		    }
			else{
				
				if (($action == 'completed' /*&& adlseqRequiredForCompleted == 'ifAttempted') || ($action == 'incomplete' && adlseqRequiredForIncomplete == 'ifAttempted'*/)){

			        if (!scorm_seq_is('activityprogressstatus',$sco->id,$userid) || (($r->value)==0)){
				        $included = false;
			        }

		        }
				else{
					if (($action == 'completed' /*&& adlseqRequiredforSatisfied == 'ifNotSkipped') || ($action == 'incomplete' && adlseqRequiredforNotSatisfied == 'ifNotSkipped'*/)){
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

		 if (isset($parent->flow) && ($parent->flow == false)) {
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
		if (isset($parent->flow) && ($parent->flow == false)) {
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
	$seq = scorm_seq_flow_tree_traversal ($activity,$direction,$childrenflag,$prevdirection,$seq);
	if($seq->identifiedactivity == null){//if identifies
		$seq->identifiedactivity = $candidate;
		$seq->deliverable = false;
	}
	else{
		$activity = $seq->identifiedactivity;
		$seq = scorm_seq_flow_activity_traversal($activity,$userid,$direction,$childrenflag,$prevdirection,$seq);//
		return $seq;

	}
}

function scorm_seq_flow_activity_traversal ($activity, $userid, $direction, $childrenflag, $prevdirection, $seq){//returns the next activity on the tree, traversal direction, control returned to the LTS, (may) exception
    $activity = scorm_get_sco ($activity);
    $parent = scorm_get_parent ($activity);
   if (isset($parent->flow) && ($parent->flow == false)) {
		$seq->deliverable = false;
	    $seq->exception = 'SB.2.2-1';
		$seq->nextactivity = $activity;
	    return $seq;
	}
     
    $rulch = scorm_seq_rules_check($sco, 'skipped');
	if ($rulch != null){
	    $seq = scorm_seq_flow_tree_traversal ($activity, $direction, false, $prevdirection, $seq);//endsession and exception
		if ($seq->identifiedactivity == null){
			$seq->deliverable = false;
			$seq->nextactivity = $activity;
			return $seq;
		}
		else{
			
			if ($prevdirection = 'backward' && $seq->traversaldir == 'backward'){
				$seq = scorm_seq_flow_tree_traversal ($activity,$direction,false,null,$seq);
				scorm_seq_flow_activity($seq->identifiedactivity, $userid, $direction, $childrenflag, $prevdirection, $seq);
			}
			else{
				$seq = scorm_seq_flow_tree_traversal ($activity,$direction,false,null,$seq);
				$seq = scorm_seq_flow_activity($seq->identifiedactivity, $userid, $direction, $childrenflag, $prevdirection, $seq);
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

		$seq = scorm_seq_flow_tree_traversal ($activity,$direction,true,null,$seq);

		if ($seq->identifiedactivity == null){
			$seq->deliverable = false;
			$seq->nextactivity = $activity;
			return $seq;
		}

		else{
		    if($direction == 'backward' && $seq->traversaldir == 'forward'){
				$seq = scorm_seq_flow_activity($seq->identifiedactivity, $userid, 'forward', $childrenflag, 'backward', $seq);
			}
			else{
				scorm_seq_flow_activity($seq->identifiedactivity, $userid, $direction, $childrenflag, null, $seq);
			}
			return $seq;
		}

	}

    $seq->deliverable = true;
	$seq->nextactivity = $activity;
    return $seq;

}
function scorm_seq_flow_tree_traversal ($activity,$direction,$childrenflag,$prevdirection,$seq){

	$revdirection = false;
	$parent = scorm_get_parent ($activity);
	$children = scorm_get_children ($parent);
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
			scorm_seq_terminate_descent($ancestorsroot);
			$seq->endsession = true;
			$seq->nextactivity = null;
			return $seq;
		}
		if (scorm_is_leaf ($activity) || !$childrenflag){
			if ($children[$siz-1]->id == $activity->id){
	
				$seq = scorm_seq_flow_tree_traversal ($parent, $direction, false, null, $seq);
				// I think it's not necessary to do a return in here
			}
			else{
				$parent = scorm_get_parent($activity);
				$children = scorm_get_children($parent);
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
				 if (!isset($parent->flow) || ($parent->flow == true)) {
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
		if ( !get_record('scorm_scoes_track','scoid',$parentid->id,'userid',$userid,'element','sequencingcontrolchoice')){
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

	if (pos !== false){

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
		$comtarget = array_slice($sib, 1,$commonpos);//path from the common ancestor to the target activity
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

			if(scorm_seq_is('active',$act->id,$userid) && ($act->id != $comancestor->id && $seq->prevact)){//adlseq:can i write it like another property for the $seq object?
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
				if ( !get_record('scorm_scoes_track','scoid',$activ->id,'userid',$userid,'element','sequencingcontrolchoiceexit')){
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
			if ( !get_record('scorm_scoes_track','scoid',$activ->id,'userid',$userid,'element','sequencingcontrolchoiceexit')){
					$seq->delivery = null;
		            $seq->exception = 'SB.2.9-7';
		            return $seq;
			}
			if ($constrained == null){
				if($seq->constrainedchoice){
					$constrained = $acti;//adlseq:can i write it like another property for the $seq object?
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
//CONTINUE 11.5.5
		}
		
	}


    
  
}

?>
