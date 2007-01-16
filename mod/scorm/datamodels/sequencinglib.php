<?php // $Id$

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
        //    scorm_sequencing_sequencing($scoid,$userid,$seq);
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
    $seq->active = scorm_seq_is('active',$scoid,$userid);
    $seq->suspended = scorm_seq_is('suspended',$scoid,$userid);
    $seq->navigation = null;
    $seq->termination = null;
    $seq->sequencing = null;
    $seq->target = null;
    $seq->exception = null;

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
                if ($track = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'name','suspendedactivity')) {
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
                        if (isset($parentsco->flow) && ($parent->flow == true)) {
                            // Current activity is active !
                            if ($request == 'continue_') {
                                $seq->navigation = true;
                                $seq->termination = 'exit';
                                $seq->sequencing = 'continue';
                            } else {
                                if (isset($parentsco->forwardonly) && ($parent->forwardolny == false)) {
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
                        if (isset($parentsco->choice) && ($parent->choice == true)) {
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

function scorm_seq_temination ($seq,$userid) {
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
                $exit = true;
                $seq = scorm_seq_post_cond_rules($seq,$userid);
                if ($seq->termination == 'exitparent') {
                    if ($sco->parent != '/') {
                        $sco = scorm_get_parent($sco);
                        $seq->currentactivity = $sco;
                        $seq->active = scorm_seq_is('active',$sco->id,$userid);
                        scorm_seq_end_attempt($sco,$userid);
                        $exit = false;
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
                    scorm_seq_set('active',$ancestor->id,$userid,0,false);
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
            scorm_seq_set('active',$sco->id,$userid,0,false);
            $seq->active = null;
            $seq->termination = true;
        break;
        case 'abandonall':
            if ($ancestors = scorm_get_ancestors($sco)) { 
                foreach ($ancestors as $ancestor) {
                    scorm_seq_set('active',$ancestor->id,$userid,0,false);
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
                    if (!scorm_seq_is('attemptprogressstatus',$sco->id,$userid,$attempt)) {
                        scorm_seq_set('attemptprogressstatus',$sco->id,$userid,$attempt);
                        scorm_seq_set('attemptcompletionstatus',$sco->id,$userid,$attempt);
                    }
                }
                if (!isset($sco->objectivesetbycontent) || ($sco->objectivesetbycontent == 0)) {
                    if ($sco->objectives) {
                        foreach ($objectives as $objective) {
                            if ($objective->primary) {
                                if (!scorm_seq_objective_progress_status($sco,$userid,$objective)) {
                                    scorm_seq_set('objectiveprogressstatus',$sco->id,$userid,$attempt);
                                    scorm_seq_set('objectivesatisfiedstatus',$sco->id,$userid,$attempt);
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
                scorm_seq_set('suspended',$sco,$userid,0,false);
            }
        }
    }
    scorm_seq_set('active',$sco,$userid,0,false);
    scorm_seq_overall_rollup($sco,$userid);
}

function scorm_seq_is($what, $scoid, $userid, $attempt=0) {
    /// Check if passed activity $what is active
    $active = false;
    if ($track = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element',$what)) {
        $active = true;
    }
    return $active;
}

function scorm_seq_set($what, $scoid, $userid, $attempt=0, $value='true') {
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

?>
