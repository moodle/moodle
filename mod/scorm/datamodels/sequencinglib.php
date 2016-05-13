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

function scorm_seq_exit_action_rules($seq, $userid) {
    $sco = $seq->currentactivity;
    $ancestors = scorm_get_ancestors($sco);
    $exittarget = null;
    foreach (array_reverse($ancestors) as $ancestor) {
        if (scorm_seq_rules_check($ancestor, 'exit') != null) {
            $exittarget = $ancestor;
            break;
        }
    }
    if ($exittarget != null) {
        $commons = array_slice($ancestors, 0, scorm_find_common_ancestor($ancestors, $exittarget));

        // Terminate Descendent Attempts Process.
        if ($commons) {
            foreach ($commons as $ancestor) {
                scorm_seq_end_attempt($ancestor, $userid, $seq->attempt);
                $seq->currentactivity = $ancestor;
            }
        }
    }
    return $seq;
}

function scorm_seq_post_cond_rules($seq, $userid) {
    $sco = $seq->currentactivity;
    if (!$seq->suspended) {
        if ($action = scorm_seq_rules_check($sco, 'post') != null) {
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


function scorm_seq_evaluate_rollupcond($sco, $conditioncombination, $rollupruleconds, $userid) {
    $bag = Array();
    $con = "";
    $val = false;
    $unk = false;
    foreach ($rollupruleconds as $rolluprulecond) {
        $condit = scorm_evaluate_condition($rolluprulecond, $sco, $userid);
        if ($rolluprulecond->operator == 'not') { // If operator is not, negate the condition.
            if ($rolluprulecond->cond != 'unknown') {
                if ($condit) {
                    $condit = false;
                } else {
                    $condit = true;
                }
            } else {
                $condit = 'unknown';
            }
            array_push($childrenbag, $condit);
        }
    }
    if (empty($bag)) {
        return 'unknown';
    } else {
        $i = 0;
        foreach ($bag as $b) {
            if ($rolluprulecond->conditioncombination == 'all') {
                $val = true;
                if ($b == 'unknown') {
                    $unk = true;
                }
                if ($b === false) {
                    return false;
                }
            } else {
                $val = false;

                if ($b == 'unknown') {
                    $unk = true;
                }
                if ($b === true) {
                    return true;
                }
            }
        }
    }
    if ($unk) {
        return 'unknown';
    }
    return $val;
}

function scorm_seq_check_child ($sco, $action, $userid) {
    global $DB;

    $included = false;
    $sco = scorm_get_sco($sco->id);
    $r = $DB->get_record('scorm_scoes_track', array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'activityattemptcount'));
    if ($action == 'satisfied' || $action == 'notsatisfied') {
        if (!$sco->rollupobjectivesatisfied) {
            $included = true;
            if (($action == 'satisfied' && $sco->requiredforsatisfied == 'ifnotsuspended') ||
                ($action == 'notsatisfied' && $sco->requiredfornotsatisfied == 'ifnotsuspended')) {

                if (!scorm_seq_is('activityprogressstatus', $sco->id, $userid) ||
                    ((($r->value) > 0) && !scorm_seq_is('suspended', $sco->id, $userid))) {
                    $included = false;
                }

            } else {
                if (($action == 'satisfied' && $sco->requiredforsatisfied == 'ifattempted') ||
                    ($action == 'notsatisfied' && $sco->requiredfornotsatisfied == 'ifattempted')) {
                    if (!scorm_seq_is('activityprogressstatus', $sco->id, $userid) || (($r->value) == 0)) {
                        $included = false;
                    }
                } else {
                    if (($action == 'satisfied' && $sco->requiredforsatisfied == 'ifnotskipped') ||
                        ($action == 'notsatisfied' && $sco->requiredfornotsatisfied == 'ifnotskipped')) {
                        $rulch = scorm_seq_rules_check($sco, 'skip');
                        if ($rulch != null) {
                            $included = false;
                        }
                    }
                }
            }
        }
    }
    if ($action == 'completed' || $action == 'incomplete') {
        if (!$sco->rollupprogresscompletion) {
            $included = true;

            if (($action == 'completed' && $sco->requiredforcompleted == 'ifnotsuspended') ||
                ($action == 'incomplete' && $sco->requiredforincomplete == 'ifnotsuspended')) {

                if (!scorm_seq_is('activityprogressstatus', $sco->id, $userid) ||
                    ((($r->value) > 0)&& !scorm_seq_is('suspended', $sco->id, $userid))) {
                    $included = false;
                }

            } else {

                if (($action == 'completed' && $sco->requiredforcompleted == 'ifattempted') ||
                    ($action == 'incomplete' && $sco->requiredforincomplete == 'ifattempted')) {
                    if (!scorm_seq_is('activityprogressstatus', $sco->id, $userid) || (($r->value) == 0)) {
                        $included = false;
                    }

                } else {
                    if (($action == 'completed' && $sco->requiredforsatisfied == 'ifnotskipped') ||
                        ($action == 'incomplete' && $sco->requiredfornotsatisfied == 'ifnotskipped')) {
                        $rulch = scorm_seq_rules_check($sco, 'skip');
                        if ($rulch != null) {
                            $included = false;
                        }
                    }
                }
            }
        }
    }
    return $included;
}

function scorm_seq_sequencing ($scoid, $userid, $seq) {

    switch ($seq->sequencing) {
        case 'start':
            // We'll see the parameters we have to send, this should update delivery and end.
            $seq = scorm_seq_start_sequencing($scoid, $userid, $seq);
            $seq->sequencing = true;
            break;

        case 'resumeall':
            // We'll see the parameters we have to send, this should update delivery and end.
            $seq = scorm_seq_resume_all_sequencing($scoid, $userid, $seq);
            $seq->sequencing = true;
            break;

        case 'exit':
            // We'll see the parameters we have to send, this should update delivery and end.
            $seq = scorm_seq_exit_sequencing($scoid, $userid, $seq);
            $seq->sequencing = true;
            break;

        case 'retry':
            // We'll see the parameters we have to send, this should update delivery and end.
            $seq = scorm_seq_retry_sequencing($scoid, $userid, $seq);
            $seq->sequencing = true;
            break;

        case 'previous':
            // We'll see the parameters we have to send, this should update delivery and end.
            $seq = scorm_seq_previous_sequencing($scoid, $userid, $seq);
            $seq->sequencing = true;
            break;

        case 'choice':
            // We'll see the parameters we have to send, this should update delivery and end.
            $seq = scorm_seq_choice_sequencing($scoid, $userid, $seq);
            $seq->sequencing = true;
            break;
    }

    if ($seq->exception != null) {
        $seq->sequencing = false;
        return $seq;
    }

    $seq->sequencing = true;
    return $seq;
}

function scorm_seq_start_sequencing($scoid, $userid, $seq) {
    global $DB;

    if (!empty($seq->currentactivity)) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.5-1';
        return $seq;
    }
    $sco = $DB->get_record('scorm_scoes', array('scoid' => $scoid, 'userid' => $userid));
    if (($sco->parent == '/') && scorm_is_leaf($sco)) { // If the activity is the root and is leaf.
        $seq->delivery = $sco;
    } else {
        $ancestors = scorm_get_ancestors($sco);
        $ancestorsroot = array_reverse($ancestors);
        $res = scorm_seq_flow($ancestorsroot[0], 'forward', $seq, true, $userid);
        if ($res) {
            return $res;
        }
    }
}

function scorm_seq_resume_all_sequencing($scoid, $userid, $seq) {
    global $DB;

    if (!empty($seq->currentactivity)) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.6-1';
        return $seq;
    }
    $track = $DB->get_record('scorm_scoes_track', array('scoid' => $scoid, 'userid' => $userid, 'element' => 'suspendedactivity'));
    if (!$track) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.6-2';
        return $seq;
    }
    // We assign the sco to the delivery.
    $seq->delivery = $DB->get_record('scorm_scoes', array('scoid' => $scoid, 'userid' => $userid));
}

function scorm_seq_continue_sequencing($scoid, $userid, $seq) {
    if (empty($seq->currentactivity)) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.7-1';
        return $seq;
    }
    $currentact = $seq->currentactivity;
    if ($currentact->parent != '/') {
        // If the activity is the root and is leaf.
        $parent = scorm_get_parent ($currentact);

        if (!isset($parent->flow) || ($parent->flow == false)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.7-2';
            return $seq;
        }

        $res = scorm_seq_flow($currentact, 'forward', $seq, false, $userid);
        if ($res) {
            return $res;
        }
    }
}

function scorm_seq_previous_sequencing($scoid, $userid, $seq) {
    if (empty($seq->currentactivity)) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.8-1';
        return $seq;
    }

    $currentact = $seq->currentactivity;
    if ($currentact->parent != '/') { // If the activity is the root and is leaf.
        $parent = scorm_get_parent ($currentact);
        if (!isset($parent->flow) || ($parent->flow == false)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.8-2';
            return $seq;
        }

        $res = scorm_seq_flow($currentact, 'backward', $seq, false, $userid);
        if ($res) {
            return $res;
        }
    }
}

function scorm_seq_exit_sequencing($scoid, $userid, $seq) {
    if (empty($seq->currentactivity)) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.11-1';
        return $seq;
    }

    if ($seq->active) {
        $seq->endsession = false;
        $seq->exception = 'SB.2.11-2';
        return $seq;
    }
    $currentact = $seq->currentactivity;
    if ($currentact->parent == '/') {
        $seq->endsession = true;
        return $seq;
    }

    $seq->endsession = false;
    return $seq;
}

function scorm_seq_retry_sequencing($scoid, $userid, $seq) {
    if (empty($seq->currentactivity)) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.10-1';
        return $seq;
    }
    if ($seq->active || $seq->suspended) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.10-2';
        return $seq;
    }

    if (!scorm_is_leaf($seq->currentactivity)) {
        $res = scorm_seq_flow($seq->currentactivity, 'forward', $seq, true, $userid);
        if ($res != null) {
            return $res;
        } else {
            // Return deliver.
            $seq->delivery = null;
            $seq->exception = 'SB.2.10-3';
            return $seq;
        }
    } else {
        $seq->delivery = $seq->currentactivity;
        return $seq;
    }

}

function scorm_seq_choice_sequencing($sco, $userid, $seq) {

    $avchildren = Array ();
    $comancestor = null;
    $traverse = null;

    if ($sco == null) {
        $seq->delivery = null;
        $seq->exception = 'SB.2.9-1';
        return $seq;
    }

    $ancestors = scorm_get_ancestors($sco);
    $arrpath = array_reverse($ancestors);
    array_push ($arrpath, $sco); // Path from the root to the target.

    foreach ($arrpath as $activity) {
        if ($activity->parent != '/') {
            $avchildren = scorm_get_available_children (scorm_get_parent($activity));
            $position = array_search($avchildren, $activity);
            if ($position !== false) {
                $seq->delivery = null;
                $seq->exception = 'SB.2.9-2';
                return $seq;
            }
        }

        if (scorm_seq_rules_check($activity, 'hidefromchoice' != null)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.9-3';
            return $seq;
        }
    }

    if ($sco->parent != '/') {
        $parent = scorm_sco_get_parent ($sco);
        if ( isset($parent->choice) && ($parent->choice == false)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.9-4';
            return $seq;
        }
    }

    if ($seq->currentactivity != null) {
        $commonpos = scorm_find_common_ancestor($ancestors, $seq->currentactivity);
        $comancestor = $arrpath [$commonpos];
    } else {
        $comancestor = $arrpath [0];
    }

    if ($seq->currentactivity === $sco) {
        // MDL-51757 - this part of the SCORM 2004 sequencing and navigation was not completed.
        throw new \coding_exception('Unexpected state encountered');
    }

    $sib = scorm_get_siblings($seq->currentactivity);
    $pos = array_search($sib, $sco);

    if ($pos !== false) {
        $siblings = array_slice($sib, 0, $pos - 1);
        if (empty($siblings)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.9-5';
            return $seq;
        }

        $children = scorm_get_children (scorm_get_parent ($sco));
        $pos1 = array_search($children, $sco);
        $pos2 = array_search($seq->currentactivity, $sco);
        if ($pos1 > $pos2) {
            $traverse = 'forward';
        } else {
            $traverse = 'backward';
        }

        foreach ($siblings as $sibling) {
            $seq = scorm_seq_choice_activity_traversal($sibling, $userid, $seq, $traverse);
            if (!$seq->reachable) {
                $seq->delivery = null;
                return $seq;
            }
        }
        // MDL-51757 - this part of the SCORM 2004 sequencing and navigation was not completed.
        throw new \coding_exception('Unexpected state encountered');
    }

    if ($seq->currentactivity == null || $seq->currentactivity == $comancestor) {
        $commonpos = scorm_find_common_ancestor($ancestors, $seq->currentactivity);
        // Path from the common ancestor to the target activity.
        $comtarget = array_slice($ancestors, 1, $commonpos - 1);
        $comtarget = array_reverse($comtarget);

        if (empty($comtarget)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.9-5';
            return $seq;
        }
        foreach ($comtarget as $act) {
            $seq = scorm_seq_choice_activity_traversal($act, $userid, $seq, 'forward');
            if (!$seq->reachable) {
                $seq->delivery = null;
                return $seq;
            }
            $act = scorm_get_sco ($acti->id);
            if (scorm_seq_is('active', $act->id, $userid) && ($act->id != $comancestor->id && $act->preventactivation)) {
                $seq->delivery = null;
                $seq->exception = 'SB.2.9-6';
                return $seq;
            }
        }
        // MDL-51757 - this part of the SCORM 2004 sequencing and navigation was not completed.
        throw new \coding_exception('Unexpected state encountered');
    }

    if ($comancestor->id == $sco->id) {

        $ancestorscurrent = scorm_get_ancestors($seq->currentactivity);
        $possco = array_search($ancestorscurrent, $sco);
        // Path from the current activity to the target.
        $curtarget = array_slice($ancestorscurrent, 0, $possco);

        if (empty($curtarget)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.9-5';
            return $seq;
        }
        $i = 0;
        foreach ($curtarget as $activ) {
            $i++;
            if ($i != count($curtarget)) {
                if (isset($activ->choiceexit) && ($activ->choiceexit == false)) {
                    $seq->delivery = null;
                    $seq->exception = 'SB.2.9-7';
                    return $seq;
                }
            }
        }
        // MDL-51757 - this part of the SCORM 2004 sequencing and navigation was not completed.
        throw new \coding_exception('Unexpected state encountered');
    }

    if (array_search($ancestors, $comancestor) !== false) {
        $ancestorscurrent = scorm_get_ancestors($seq->currentactivity);
        $commonpos = scorm_find_common_ancestor($ancestors, $sco);
        $curcommon = array_slice($ancestorscurrent, 0, $commonpos - 1);
        if (empty($curcommon)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.9-5';
            return $seq;
        }

        $constrained = null;
        foreach ($curcommon as $acti) {
            $acti = scorm_get_sco($acti->id);
            if (isset($acti->choiceexit) && ($acti->choiceexit == false)) {
                    $seq->delivery = null;
                    $seq->exception = 'SB.2.9-7';
                    return $seq;
            }
            if ($constrained == null) {
                if ($acti->constrainchoice == true) {
                    $constrained = $acti;
                }
            }
        }
        if ($constrained != null) {
            $fwdir = scorm_get_preorder($constrained);

            if (array_search($fwdir, $sco) !== false) {
                $traverse = 'forward';
            } else {
                $traverse = 'backward';
            }
            $seq = scorm_seq_choice_flow($constrained, $traverse, $seq);
            $actconsider = $seq->identifiedactivity;
            $avdescendents = Array();
            $avdescendents = scorm_get_available_descendents($actconsider);
            if (array_search ($avdescendents, $sco) !== false && $sco->id != $actconsider->id && $constrained->id != $sco->id ) {
                $seq->delivery = null;
                $seq->exception = 'SB.2.9-8';
                return $seq;
            }
            // CONTINUE 11.5.5 !
        }

        $commonpos = scorm_find_common_ancestor($ancestors, $seq->currentactivity);
        $comtarget = array_slice($ancestors, 1, $commonpos - 1);// Path from the common ancestor to the target activity.
        $comtarget = array_reverse($comtarget);

        if (empty($comtarget)) {
            $seq->delivery = null;
            $seq->exception = 'SB.2.9-5';
            return $seq;
        }

        $fwdir = scorm_get_preorder($seq->currentactivity);

        if (array_search($fwdir, $sco) !== false) {
            foreach ($comtarget as $act) {
                $seq = scorm_seq_choice_activity_traversal($act, $userid, $seq, 'forward');
                if (!$seq->reachable) {
                    $seq->delivery = null;
                    return $seq;
                }
                $act = scorm_get_sco($act->id);
                if (scorm_seq_is('active', $act->id, $userid) && ($act->id != $comancestor->id &&
                        ($act->preventactivation == true))) {
                    $seq->delivery = null;
                    $seq->exception = 'SB.2.9-6';
                    return $seq;
                }
            }

        } else {
            foreach ($comtarget as $act) {
                $act = scorm_get_sco($act->id);
                if (scorm_seq_is('active', $act->id, $userid) && $act->id != $comancestor->id && $act->preventactivation == true) {
                    $seq->delivery = null;
                    $seq->exception = 'SB.2.9-6';
                    return $seq;
                }
            }
        }
        // MDL-51757 - this part of the SCORM 2004 sequencing and navigation was not completed.
        throw new \coding_exception('Unexpected state encountered');
    }

    if (scorm_is_leaf ($sco)) {
        $seq->delivery = $sco;
        $seq->exception = 'SB.2.9-6';
        return $seq;
    }

    $seq = scorm_seq_flow($sco, 'forward', $seq, true, $userid);
    if ($seq->deliverable == false) {
        scorm_terminate_descendent_attempts($comancestor, $userid, $seq);
        scorm_seq_end_attempt($comancestor, $userid, $seq->attempt);
        $seq->currentactivity = $sco;
        $seq->delivery = null;
        $seq->exception = 'SB.2.9-9';
        return $seq;
    } else {
        return $seq;
    }

}

function scorm_seq_choice_flow ($constrained, $traverse, $seq) {
    $seq = scorm_seq_choice_flow_tree ($constrained, $traverse, $seq);
    if ($seq->identifiedactivity == null) {
        $seq->identifiedactivity = $constrained;
        return $seq;
    } else {
        return $seq;
    }
}

function scorm_seq_choice_flow_tree ($constrained, $traverse, $seq) {
    $islast = false;
    $parent = scorm_get_parent ($constrained);
    if ($traverse == 'forward') {
        $preord = scorm_get_preorder($constrained);
        if (count($preorder) == 0 || (count($preorder) == 0 && $preorder[0]->id = $constrained->id)) {
            // TODO: undefined.
            $islast = true; // The function is the last activity available.
        }
        if ($constrained->parent == '/' || $islast) {
            $seq->nextactivity = null;
            return $seq;
        }
        $avchildren = scorm_get_available_children($parent); // Available children.
        if ($avchildren[count($avchildren) - 1]->id == $constrained->id) {
            $seq = scorm_seq_choice_flow_tree ($parent, 'forward', $seq);
            return $seq;
        } else {
            $i = 0;
            while ($i < count($avchildren)) {
                if ($avchildren [$i]->id == $constrained->id) {
                    $seq->nextactivity = $avchildren [$i + 1];
                    return $seq;
                } else {
                    $i++;
                }
            }
        }
    }

    if ($traverse == 'backward') {
        if ($constrained->parent == '/' ) {
            $seq->nextactivity = null;
            return $seq;
        }

        $avchildren = scorm_get_available_children($parent); // Available children.
        if ($avchildren [0]->id == $constrained->id) {
            $seq = scorm_seq_choice_flow_tree ($parent, 'backward', $seq);
            return $seq;
        } else {
            $i = count($avchildren) - 1;
            while ($i >= 0) {
                if ($avchildren [$i]->id == $constrained->id) {
                    $seq->nextactivity = $avchildren [$i - 1];
                    return $seq;
                } else {
                    $i--;
                }
            }
        }
    }
}

function scorm_seq_choice_activity_traversal($activity, $userid, $seq, $direction) {
    if ($direction == 'forward') {
        $act = scorm_seq_rules_check($activity, 'stopforwardtraversal');

        if ($act != null) {
            $seq->reachable = false;
            $seq->exception = 'SB.2.4-1';
            return $seq;
        }
        $seq->reachable = false;
        return $seq;
    }

    if ($direction == 'backward') {
        $parentsco = scorm_get_parent($activity);
        if ($parentsco != null) {
            if (isset($parentsco->forwardonly) && ($parentsco->forwardonly == true)) {
                $seq->reachable = false;
                $seq->exception = 'SB.2.4-2';
                return $seq;
            } else {
                $seq->reachable = false;
                $seq->exception = 'SB.2.4-3';
                return $seq;
            }
        }
    }
    $seq->reachable = true;
    return $seq;
}

// Delivery Request Process.

function scorm_sequencing_delivery($scoid, $userid, $seq) {

    if (!scorm_is_leaf($seq->delivery)) {
        $seq->deliveryvalid = false;
        $seq->exception = 'DB.1.1-1';
        return $seq;
    }
    $ancestors = scorm_get_ancestors($seq->delivery);
    $arrpath = array_reverse($ancestors);
    array_push ($arrpath, $seq->delivery); // Path from the root to the target.

    if (empty($arrpath)) {
        $seq->deliveryvalid = false;
        $seq->exception = 'DB.1.1-2';
        return $seq;
    }

    foreach ($arrpath as $activity) {
        if (scorm_check_activity($activity, $userid)) {
            $seq->deliveryvalid = false;
            $seq->exception = 'DB.1.1-3';
            return $seq;
        }
    }

    $seq->deliveryvalid = true;
    return $seq;

}

function scorm_content_delivery_environment($seq, $userid) {
    global $DB;

    $act = $seq->currentactivity;
    if (scorm_seq_is('active', $act->id, $userid)) {
        $seq->exception = 'DB.2-1';
        return $seq;
    }
    $track = $DB->get_record('scorm_scoes_track', array('scoid' => $act->id,
                                                        'userid' => $userid,
                                                        'element' => 'suspendedactivity'));
    if ($track != null) {
        $seq = scorm_clear_suspended_activity($seq->delivery, $seq, $userid);

    }
    $seq = scorm_terminate_descendent_attempts ($seq->delivery, $userid, $seq);
    $ancestors = scorm_get_ancestors($seq->delivery);
    $arrpath = array_reverse($ancestors);
    array_push ($arrpath, $seq->delivery);
    foreach ($arrpath as $activity) {
        if (!scorm_seq_is('active', $activity->id, $userid)) {
            if (!isset($activity->tracked) || ($activity->tracked == 1)) {
                if (!scorm_seq_is('suspended', $activity->id, $userid)) {
                    $r = $DB->get_record('scorm_scoes_track', array('scoid' => $activity->id,
                                                                    'userid' => $userid,
                                                                    'element' => 'activityattemptcount'));
                    $r->value = ($r->value) + 1;
                    $DB->update_record('scorm_scoes_track', $r);
                    if ($r->value == 1) {
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

    // ONCE THE DELIVERY BEGINS (How should I check that?).

    if (isset($activity->tracked) || ($activity->tracked == 0)) {
        // How should I track the info and what should I do to not record the information for the activity during delivery?
        $atabsdur = $DB->get_record('scorm_scoes_track', array('scoid' => $activity->id,
                                                                'userid' => $userid,
                                                                'element' => 'attemptabsoluteduration'));
        $atexpdur = $DB->get_record('scorm_scoes_track', array('scoid' => $activity->id,
                                                                'userid' => $userid,
                                                                'element' => 'attemptexperiencedduration'));
    }
    return $seq;
}

function scorm_clear_suspended_activity($act, $seq, $userid) {
    global $DB;
    $currentact = $seq->currentactivity;
    $track = $DB->get_record('scorm_scoes_track', array('scoid' => $currentact->id,
                                                        'userid' => $userid,
                                                        'element' => 'suspendedactivity'));
    if ($track != null) {
        $ancestors = scorm_get_ancestors($act);
        $commonpos = scorm_find_common_ancestor($ancestors, $currentact);
        if ($commonpos !== false) {
            if ($activitypath = array_slice($ancestors, 0, $commonpos)) {
                if (!empty($activitypath)) {

                    foreach ($activitypath as $activity) {
                        if (scorm_is_leaf($activity)) {
                            scorm_seq_set('suspended', $activity->id, $userid, false);
                        } else {
                            $children = scorm_get_children($activity);
                            $bool = false;
                            foreach ($children as $child) {
                                if (scorm_seq_is('suspended', $child->id, $userid)) {
                                    $bool = true;
                                }
                            }
                            if (!$bool) {
                                scorm_seq_set('suspended', $activity->id, $userid, false);
                            }
                        }
                    }
                }
            }
        }
        scorm_seq_set('suspendedactivity', $act->id, $userid, false);
    }
}

function scorm_select_children_process($scoid, $userid) {
    global $DB;

    $sco = scorm_get_sco($scoid);
    if (!scorm_is_leaf($sco)) {
        if (!scorm_seq_is('suspended', $scoid, $userid) && !scorm_seq_is('active', $scoid, $userid)) {
            $r = $DB->get_record('scorm_scoes_track', array('scoid' => $scoid,
                                                            'userid' => $userid,
                                                            'element' => 'selectiontiming'));

            switch ($r->value) {
                case 'oneachnewattempt':
                case 'never':
                break;

                case 'once':
                    if (!scorm_seq_is('activityprogressstatus', $scoid, $userid)) {
                        if (scorm_seq_is('selectioncountsstatus', $scoid, $userid)) {
                            $childlist = '';
                            $res = $DB->get_record('scorm_scoes_track', array('scoid' => $scoid,
                                                                                'userid' => $userid,
                                                                                'element' => 'selectioncount'));
                            $i = ($res->value) - 1;
                            $children = scorm_get_children($sco);

                            while ($i >= 0) {
                                $pos = array_rand($children);
                                array_push($childlist, $children[$pos]);
                                array_splice($children, $pos, 1);
                                $i--;
                            }
                            sort($childlist);
                            $clist = serialize($childlist);
                            scorm_seq_set('availablechildren', $scoid, $userid, false);
                            scorm_seq_set('availablechildren', $scoid, $userid, $clist);
                        }
                    }
                break;
            }
        }
    }
}

function scorm_randomize_children_process($scoid, $userid) {
    global $DB;

    $sco = scorm_get_sco($scoid);
    if (!scorm_is_leaf($sco)) {
        if (!scorm_seq_is('suspended', $scoid, $userid) && !scorm_seq_is('active', $scoid, $userid)) {
            $r = $DB->get_record('scorm_scoes_track', array('scoid' => $scoid,
                                                            'userid' => $userid,
                                                            'element' => 'randomizationtiming'));

            switch ($r->value) {
                case 'never':
                break;

                case 'oneachnewattempt':
                case 'once':
                    if (!scorm_seq_is('activityprogressstatus', $scoid, $userid)) {
                        if (scorm_seq_is('randomizechildren', $scoid, $userid)) {
                            $childlist = array();
                            $res = scorm_get_available_children($sco);
                            $i = count($res) - 1;
                            $children = $res->value;

                            while ($i >= 0) {
                                $pos = array_rand($children);
                                array_push($childlist, $children[$pos]);
                                array_splice($children, $pos, 1);
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

function scorm_terminate_descendent_attempts($activity, $userid, $seq) {
    $ancestors = scorm_get_ancestors($seq->currentactivity);
    $commonpos = scorm_find_common_ancestor($ancestors, $activity);
    if ($commonpos !== false) {
        if ($activitypath = array_slice($ancestors, 1, $commonpos - 2)) {
            if (!empty($activitypath)) {
                foreach ($activitypath as $sco) {
                    scorm_seq_end_attempt($sco, $userid, $seq->attempt);
                }
            }
        }
    }
}

function scorm_sequencing_exception($seq) {
    global $OUTPUT;
    if ($seq->exception != null) {
        switch ($seq->exception) {
            case 'NB.2.1-1':
                echo $OUTPUT->notification("Sequencing session has already begun");
            break;
            case 'NB.2.1-2':
                echo $OUTPUT->notification("Sequencing session has not begun");
            break;
            case 'NB.2.1-3':
                echo $OUTPUT->notification("Suspended activity is not defined");
            break;
            case 'NB.2.1-4':
                echo $OUTPUT->notification("Flow Sequencing Control Model Violation");
            break;
            case 'NB.2.1-5':
                echo $OUTPUT->notification("Flow or Forward only Sequencing Control Model Violation");
            break;
            case 'NB.2.1-6':
                echo $OUTPUT->notification("No activity is previous to the root");
            break;
            case 'NB.2.1-7':
                echo $OUTPUT->notification("Unsupported Navigation Request");
            break;
            case 'NB.2.1-8':
                echo $OUTPUT->notification("Choice Exit Sequencing Control Model Violation");
            break;
            case 'NB.2.1-9':
                echo $OUTPUT->notification("No activities to consider");
            break;
            case 'NB.2.1-10':
                echo $OUTPUT->notification("Choice Sequencing Control Model Violation");
            break;
            case 'NB.2.1-11':
                echo $OUTPUT->notification("Target Activity does not exist");
            break;
            case 'NB.2.1-12':
                echo $OUTPUT->notification("Current Activity already terminated");
            break;
            case 'NB.2.1-13':
                echo $OUTPUT->notification("Undefined Navigation Request");
            break;

            case 'TB.2.3-1':
                echo $OUTPUT->notification("Current Activity already terminated");
            break;
            case 'TB.2.3-2':
                echo $OUTPUT->notification("Current Activity already terminated");
            break;
            case 'TB.2.3-4':
                echo $OUTPUT->notification("Current Activity already terminated");
            break;
            case 'TB.2.3-5':
                echo $OUTPUT->notification("Nothing to suspend; No active activities");
            break;
            case 'TB.2.3-6':
                echo $OUTPUT->notification("Nothing to abandon; No active activities");
            break;

            case 'SB.2.1-1':
                echo $OUTPUT->notification("Last activity in the tree");
            break;
            case 'SB.2.1-2':
                echo $OUTPUT->notification("Cluster has no available children");
            break;
            case 'SB.2.1-3':
                echo $OUTPUT->notification("No activity is previous to the root");
            break;
            case 'SB.2.1-4':
                echo $OUTPUT->notification("Forward Only Sequencing Control Model Violation");
            break;

            case 'SB.2.2-1':
                echo $OUTPUT->notification("Flow Sequencing Control Model Violation");
            break;
            case 'SB.2.2-2':
                echo $OUTPUT->notification("Activity unavailable");
            break;

            case 'SB.2.3-1':
                echo $OUTPUT->notification("Forward Traversal Blocked");
            break;
            case 'SB.2.3-2':
                echo $OUTPUT->notification("Forward Only Sequencing Control Model Violation");
            break;
            case 'SB.2.3-3':
                echo $OUTPUT->notification("No activity is previous to the root");
            break;

            case 'SB.2.5-1':
                echo $OUTPUT->notification("Sequencing session has already begun");
            break;

            case 'SB.2.6-1':
                echo $OUTPUT->notification("Sequencing session has already begun");
            break;
            case 'SB.2.6-2':
                echo $OUTPUT->notification("No Suspended activity is defined");
            break;

            case 'SB.2.7-1':
                echo $OUTPUT->notification("Sequencing session has not begun");
            break;
            case 'SB.2.7-2':
                echo $OUTPUT->notification("Flow Sequencing Control Model Violation");
            break;

            case 'SB.2.8-1':
                echo $OUTPUT->notification("Sequencing session has not begun");
            break;
            case 'SB.2.8-2':
                echo $OUTPUT->notification("Flow Sequencing Control Model Violation");
            break;

            case 'SB.2.9-1':
                echo $OUTPUT->notification("No target for Choice");
            break;
            case 'SB.2.9-2':
                echo $OUTPUT->notification("Target Activity does not exist or is unavailable");
            break;
            case 'SB.2.9-3':
                echo $OUTPUT->notification("Target Activity hidden from choice");
            break;
            case 'SB.2.9-4':
                echo $OUTPUT->notification("Choice Sequencing Control Model Violation");
            break;
            case 'SB.2.9-5':
                echo $OUTPUT->notification("No activities to consider");
            break;
            case 'SB.2.9-6':
                echo $OUTPUT->notification("Unable to activate target; target is not a child of the Current Activity");
            break;
            case 'SB.2.9-7':
                echo $OUTPUT->notification("Choice Exit Sequencing Control Model Violation");
            break;
            case 'SB.2.9-8':
                echo $OUTPUT->notification("Unable to choose target activity - constrained choice");
            break;
            case 'SB.2.9-9':
                echo $OUTPUT->notification("Choice Request Prevented by Flow-only Activity");
            break;

            case 'SB.2.10-1':
                echo $OUTPUT->notification("Sequencing session has not begun");
            break;
            case 'SB.2.10-2':
                echo $OUTPUT->notification("Current Activity is active or suspended");
            break;
            case 'SB.2.10-3':
                echo $OUTPUT->notification("Flow Sequencing Control Model Violation");
            break;

            case 'SB.2.11-1':
                echo $OUTPUT->notification("Sequencing session has not begun");
            break;
            case 'SB.2.11-2':
                echo $OUTPUT->notification("Current Activity has not been terminated");
            break;

            case 'SB.2.12-2':
                echo $OUTPUT->notification("Undefined Sequencing Request");
            break;

            case 'DB.1.1-1':
                echo $OUTPUT->notification("Cannot deliver a non-leaf activity");
            break;
            case 'DB.1.1-2':
                echo $OUTPUT->notification("Nothing to deliver");
            break;
            case 'DB.1.1-3':
                echo $OUTPUT->notification("Activity unavailable");
            break;

            case 'DB.2-1':
                echo $OUTPUT->notification("Identified activity is already active");
            break;

        }

    }
}
