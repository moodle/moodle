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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/scorm/datamodels/scormlib.php');
require_once($CFG->dirroot.'/mod/scorm/datamodels/sequencinglib.php');

function scorm_seq_overall ($scoid, $userid, $request, $attempt) {
    $seq = scorm_seq_navigation($scoid, $userid, $request, $attempt);
    if ($seq->navigation) {
        if ($seq->termination != null) {
            $seq = scorm_seq_termination($scoid, $userid, $seq);
        }
        if ($seq->sequencing != null) {
            $seq = scorm_seq_sequencing($scoid, $userid, $seq);
            if ($seq->sequencing == 'exit') { // Return the control to the LTS.
                return 'true';
            }
        }
        if ($seq->delivery != null) {
            $seq = scorm_sequencing_delivery($scoid, $userid, $seq);
            $seq = scorm_content_delivery_environment ($seq, $userid);
        }
    }
    if ($seq->exception != null) {
        $seq = scorm_sequencing_exception($seq);
    }
    return 'true';
}

function scorm_seq_navigation ($scoid, $userid, $request, $attempt=0) {
    global $DB;

    // Sequencing structure.
    $seq = new stdClass();
    $seq->currentactivity = scorm_get_sco($scoid);
    $seq->traversaldir = null;
    $seq->nextactivity = null;
    $seq->deliveryvalid = null;
    $seq->attempt = $attempt;

    $seq->identifiedactivity = null;
    $seq->delivery = null;
    $seq->deliverable = false;
    $seq->active = scorm_seq_is('active', $scoid, $userid);
    $seq->suspended = scorm_seq_is('suspended', $scoid, $userid);
    $seq->navigation = null;
    $seq->termination = null;
    $seq->sequencing = null;
    $seq->target = null;
    $seq->endsession = null;
    $seq->exception = null;
    $seq->reachable = true;
    $seq->prevact = true;

    $sco = scorm_get_sco($scoid);

    switch ($request) {
        case 'start_':
            if (empty($seq->currentactivity)) {
                $seq->navigation = true;
                $seq->sequencing = 'start';
            } else {
                $seq->exception = 'NB.2.1-1'; // Sequencing session already begun.
            }
        break;
        case 'resumeall_':
            if (empty($seq->currentactivity)) {
                // TODO: I think it's suspend instead of suspendedactivity.
                if ($track = $DB->get_record('scorm_scoes_track',
                    array('scoid' => $scoid, 'userid' => $userid, 'element' => 'suspendedactivity'))) {

                    $seq->navigation = true;
                    $seq->sequencing = 'resumeall';
                } else {
                    $seq->exception = 'NB.2.1-3'; // No suspended activity found.
                }
            } else {
                $seq->exception = 'NB.2.1-1'; // Sequencing session already begun.
            }
        break;
        case 'continue_':
        case 'previous_':
            if (!empty($seq->currentactivity)) {
                $sco = $seq->currentactivity;
                if ($sco->parent != '/') {
                    if ($parentsco = scorm_get_parent($sco)) {

                        if (isset($parentsco->flow) && ($parentsco->flow == true)) { // I think it's parentsco.
                            // Current activity is active!
                            if (scorm_seq_is('active', $sco->id, $userid)) {
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
                                        $seq->exception = 'NB.2.1-5'; // Violates control mode.
                                    }
                                }
                            }
                        }

                    }
                }
            } else {
                $seq->exception = 'NB.2.1-2'; // Current activity not defined.
            }
        break;
        case 'forward_':
        case 'backward_':
            $seq->exception = 'NB.2.1-7'; // None to be done, behavior not defined.
        break;
        case 'exit_':
        case 'abandon_':
            if (!empty($seq->currentactivity)) {
                // Current activity is active !
                $seq->navigation = true;
                $seq->termination = substr($request, 0, -1);
                $seq->sequencing = 'exit';
            } else {
                $seq->exception = 'NB.2.1-2'; // Current activity not defined.
            }
        case 'exitall_':
        case 'abandonall_':
        case 'suspendall_':
            if (!empty($seq->currentactivity)) {
                $seq->navigation = true;
                $seq->termination = substr($request, 0, -1);
                $seq->sequencing = 'exit';
            } else {
                $seq->exception = 'NB.2.1-2'; // Current activity not defined.
            }
        break;
        default: // Example {target=<STRING>}choice.
            if ($targetsco = $DB->get_record('scorm_scoes', array('scorm' => $sco->scorm, 'identifier' => $request))) {
                if ($targetsco->parent != '/') {
                    $seq->target = $request;
                } else {
                    if ($parentsco = scorm_get_parent($targetsco)) {
                        if (!isset($parentsco->choice) || ($parentsco->choice == true)) {
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
                        if ($sco->parent != $targetsco->parent) {
                            $ancestors = scorm_get_ancestors($sco);
                            $commonpos = scorm_find_common_ancestor($ancestors, $targetsco);
                            if ($commonpos !== false) {
                                if ($activitypath = array_slice($ancestors, 0, $commonpos)) {
                                    foreach ($activitypath as $activity) {
                                        if (scorm_seq_is('active', $activity->id, $userid) &&
                                            (isset($activity->choiceexit) && ($activity->choiceexit == false))) {
                                            $seq->navigation = false;
                                            $seq->termination = null;
                                            $seq->sequencing = null;
                                            $seq->target = null;
                                            $seq->exception = 'NB.2.1-8'; // Violates control mode.
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
                    $seq->exception = 'NB.2.1-10';  // Violates control mode.
                }
            } else {
                $seq->exception = 'NB.2.1-11';  // Target activity does not exists.
            }
        break;
    }
    return $seq;
}

function scorm_seq_termination ($seq, $userid) {
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
            scorm_seq_end_attempt($sco, $userid, $seq);
            $seq = scorm_seq_exit_action_rules($seq, $userid);
            do {
                $exit = false;// I think this is false. Originally this was true.
                $seq = scorm_seq_post_cond_rules($seq, $userid);
                if ($seq->termination == 'exitparent') {
                    if ($sco->parent != '/') {
                        $sco = scorm_get_parent($sco);
                        $seq->currentactivity = $sco;
                        $seq->active = scorm_seq_is('active', $sco->id, $userid);
                        scorm_seq_end_attempt($sco, $userid, $seq);
                        $exit = true; // I think it's true. Originally this was false.
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
                scorm_seq_end_attempt($sco, $userid, $seq);
            }
            // Terminate Descendent Attempts Process.

            if ($ancestors = scorm_get_ancestors($sco)) {
                foreach ($ancestors as $ancestor) {
                    scorm_seq_end_attempt($ancestor, $userid, $seq);
                    $seq->currentactivity = $ancestor;
                }
            }

            $seq->active = scorm_seq_is('active', $seq->currentactivity->id, $userid);
            $seq->termination = true;
            $seq->sequencing = 'exit';
        break;
        case 'suspendall':
            if (($seq->active) || ($seq->suspended)) {
                scorm_seq_set('suspended', $sco->id, $userid, $attempt);
            } else {
                if ($sco->parent != '/') {
                    $parentsco = scorm_get_parent($sco);
                    scorm_seq_set('suspended', $parentsco->id, $userid, $attempt);
                } else {
                    $seq->termination = false;
                    $seq->exception = 'TB.2.3-3';
                }
            }
            if ($ancestors = scorm_get_ancestors($sco)) {
                foreach ($ancestors as $ancestor) {
                    scorm_seq_set('active', $ancestor->id, $userid, $attempt, false);
                    scorm_seq_set('suspended', $ancestor->id, $userid, $attempt);
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
            scorm_seq_set('active', $sco->id, $userid, $attempt, false);
            $seq->active = null;
            $seq->termination = true;
        break;
        case 'abandonall':
            if ($ancestors = scorm_get_ancestors($sco)) {
                foreach ($ancestors as $ancestor) {
                    scorm_seq_set('active', $ancestor->id, $userid, $attempt, false);
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

function scorm_seq_end_attempt($sco, $userid, $seq) {
    global $DB;
    if (scorm_is_leaf($sco)) {
        if (!isset($sco->tracked) || ($sco->tracked == 1)) {
            if (!scorm_seq_is('suspended', $sco->id, $userid)) {
                if (!isset($sco->completionsetbycontent) || ($sco->completionsetbycontent == 0)) {
                    if (!scorm_seq_is('attemptprogressstatus', $sco->id, $userid, $seq->attempt)) {
                        $incomplete = $DB->get_field('scorm_scoes_track', 'value',
                                                        array('scoid' => $sco->id,
                                                                'userid' => $userid,
                                                                'element' => 'cmi.completion_status'));
                        if ($incomplete != 'incomplete') {
                            scorm_seq_set('attemptprogressstatus', $sco->id, $userid, $seq->attempt);
                            scorm_seq_set('attemptcompletionstatus', $sco->id, $userid, $seq->attempt);
                        }
                    }
                }
                if (!isset($sco->objectivesetbycontent) || ($sco->objectivesetbycontent == 0)) {
                    if ($objectives = $DB->get_records('scorm_seq_objective', array('scoid' => $sco->id))) {
                        foreach ($objectives as $objective) {
                            if ($objective->primaryobj) {
                                if (!scorm_seq_is('objectiveprogressstatus', $sco->id, $userid, $seq->attempt)) {
                                    scorm_seq_set('objectiveprogressstatus', $sco->id, $userid, $seq->attempt);
                                    scorm_seq_set('objectivesatisfiedstatus', $sco->id, $userid, $seq->attempt);
                                }
                            }
                        }
                    }
                }
            }
        }
    } else if ($children = scorm_get_children($sco)) {
        $suspended = false;
        foreach ($children as $child) {
            if (scorm_seq_is('suspended', $child, $userid, $seq->attempt)) {
                $suspended = true;
                break;
            }
        }
        if ($suspended) {
            scorm_seq_set('suspended', $sco, $userid, $seq->attempt);
        } else {
            scorm_seq_set('suspended', $sco, $userid, $seq->attempt, false);
        }
    }
    scorm_seq_set('active', $sco->id, $userid, $seq->attempt, false);
    scorm_seq_overall_rollup($sco, $userid, $seq);
}

function scorm_seq_is($what, $scoid, $userid, $attempt=0) {
    global $DB;

    // Check if passed activity $what is active.
    $active = false;
    if ($track = $DB->get_record('scorm_scoes_track',
            array('scoid' => $scoid, 'userid' => $userid, 'attempt' => $attempt, 'element' => $what))) {
        $active = true;
    }
    return $active;
}

function scorm_seq_set($what, $scoid, $userid, $attempt=0, $value='true') {
    global $DB;

    $sco = scorm_get_sco($scoid);

    // Set passed activity to active or not.
    if ($value == false) {
        $DB->delete_records('scorm_scoes_track', array('scoid' => $scoid, 'userid' => $userid,
                                                        'attempt' => $attempt, 'element' => $what));
    } else {
        scorm_insert_track($userid, $sco->scorm, $sco->id, $attempt, $what, $value);
    }

    // Update grades in gradebook.
    $scorm = $DB->get_record('scorm', array('id' => $sco->scorm));
    scorm_update_grades($scorm, $userid, true);
}

function scorm_evaluate_condition ($rollupruleconds, $sco, $userid) {
    global $DB;

    $res = false;

    if (strpos($rollupruleconds, 'and ')) {
        $rollupruleconds = array_filter(explode(' and ', $rollupruleconds));
        $conditioncombination = 'all';
    } else {
        $rollupruleconds = array_filter(explode(' or ', $rollupruleconds));
        $conditioncombination = 'or';
    }
    foreach ($rollupruleconds as $rolluprulecond) {
        $notflag = false;
        if (strpos($rolluprulecond, 'not') !== false) {
            $rolluprulecond = str_replace('not', '', $rolluprulecond);
            $notflag = true;
        }
        $conditionarray['condition'] = $rolluprulecond;
        $conditionarray['notflag'] = $notflag;
        $conditions[] = $conditionarray;
    }
    foreach ($conditions as $condition) {
        $checknot = true;
        $res = false;
        if ($condition['notflag']) {
            $checknot = false;
        }
        switch ($condition['condition']) {
            case 'satisfied':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'objectivesatisfiedstatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'objectiveprogressstatus'));
                    if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                        $res = true;
                    }
                }
                break;

            case 'objectiveStatusKnown':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'objectiveprogressstatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $res = true;
                }
                break;

            case 'notobjectiveStatusKnown':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'objectiveprogressstatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $res = true;
                }
                break;

            case 'objectiveMeasureKnown':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'objectivemeasurestatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $res = true;
                }
                break;

            case 'notobjectiveMeasureKnown':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'objectivemeasurestatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $res = true;
                }
                break;

            case 'completed':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'attemptcompletionstatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $r = $DB->get_record('scorm_scoes_track',
                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'attemptprogressstatus'));
                    if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                        $res = true;
                    }
                }
                break;

            case 'attempted':
                $attempt = $DB->get_field('scorm_scoes_track', 'attempt',
                                            array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'x.start.time'));
                if ($checknot && $attempt > 0) {
                    $res = true;
                } else if (!$checknot && $attempt <= 0) {
                    $res = true;
                }
                break;

            case 'attemptLimitExceeded':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'activityprogressstatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $r = $DB->get_record('scorm_scoes_track',
                                            array('scoid' => $sco->id, 'userid' => $userid,
                                                    'element' => 'limitconditionattemptlimitcontrol'));
                    if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                        if ($r = $DB->get_field('scorm_scoes_track', 'attempt', array('scoid' => $sco->id, 'userid' => $userid)) &&
                            $r2 = $DB->get_record('scorm_scoes_track', array('scoid' => $sco->id, 'userid' => $userid,
                                                                                'element' => 'limitconditionattemptlimit')) ) {

                            if ($checknot && ($r->value >= $r2->value)) {
                                $res = true;
                            } else if (!$checknot && ($r->value < $r2->value)) {
                                $res = true;
                            }
                        }
                    }
                }
                break;

            case 'activityProgressKnown':
                $r = $DB->get_record('scorm_scoes_track',
                                        array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'activityprogressstatus'));
                if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                    $r = $DB->get_record('scorm_scoes_track',
                                            array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'attemptprogressstatus'));
                    if ((!isset($r->value) && !$checknot) || (isset($r->value) && ($r->value == $checknot))) {
                        $res = true;
                    }
                }
                break;
        }

        if ($conditioncombination == 'all' && !$res) {
            break;
        } else if ($conditioncombination == 'or' && $res) {
            break;
        }
    }

    return $res;
}

function scorm_check_activity ($activity, $userid) {
    $act = scorm_seq_rules_check($activity, 'disabled');
    if ($act != null) {
        return true;
    }
    if (scorm_limit_cond_check ($activity, $userid)) {
        return true;
    }
    return false;
}

function scorm_limit_cond_check ($activity, $userid) {
    global $DB;

    if (isset($activity->tracked) && ($activity->tracked == 0)) {
        return false;
    }

    if (scorm_seq_is('active', $activity->id, $userid) || scorm_seq_is('suspended', $activity->id, $userid)) {
        return false;
    }

    if (!isset($activity->limitcontrol) || ($activity->limitcontrol == 1)) {
        $r = $DB->get_record('scorm_scoes_track',
                                array('scoid' => $activity->id, 'userid' => $userid, 'element' => 'activityattemptcount'));
        if (scorm_seq_is('activityprogressstatus', $activity->id, $userid) && ($r->value >= $activity->limitattempt)) {
            return true;
        }
    }

    if (!isset($activity->limitabsdurcontrol) || ($activity->limitabsdurcontrol == 1)) {
        $r = $DB->get_record('scorm_scoes_track',
                                array('scoid' => $activity->id, 'userid' => $userid, 'element' => 'activityabsoluteduration'));
        if (scorm_seq_is('activityprogressstatus', $activity->id, $userid) && ($r->value >= $activity->limitabsduration)) {
            return true;
        }
    }

    if (!isset($activity->limitexpdurcontrol) || ($activity->limitexpdurcontrol == 1)) {
        $r = $DB->get_record('scorm_scoes_track',
                                array('scoid' => $activity->id, 'userid' => $userid, 'element' => 'activityexperiencedduration'));
        if (scorm_seq_is('activityprogressstatus', $activity->id, $userid) && ($r->value >= $activity->limitexpduration)) {
            return true;
        }
    }

    if (!isset($activity->limitattabsdurcontrol) || ($activity->limitattabsdurcontrol == 1)) {
        $r = $DB->get_record('scorm_scoes_track',
                                array('scoid' => $activity->id, 'userid' => $userid, 'element' => 'attemptabsoluteduration'));
        if (scorm_seq_is('activityprogressstatus', $activity->id, $userid) && ($r->value >= $activity->limitattabsduration)) {
            return true;
        }
    }

    if (!isset($activity->limitattexpdurcontrol) || ($activity->limitattexpdurcontrol == 1)) {
        $r = $DB->get_record('scorm_scoes_track',
                                array('scoid' => $activity->id, 'userid' => $userid, 'element' => 'attemptexperiencedduration'));
        if (scorm_seq_is('activityprogressstatus', $activity->id, $userid) && ($r->value >= $activity->limitattexpduration)) {
            return true;
        }
    }

    if (!isset($activity->limitbegincontrol) || ($activity->limitbegincontrol == 1)) {
        $r = $DB->get_record('scorm_scoes_track',
                                array('scoid' => $activity->id, 'userid' => $userid, 'element' => 'begintime'));
        if (isset($activity->limitbegintime) && time() >= $activity->limitbegintime) {
            return true;
        }
    }

    if (!isset($activity->limitbegincontrol) || ($activity->limitbegincontrol == 1)) {
        if (isset($activity->limitbegintime) && time() < $activity->limitbegintime) {
            return true;
        }
    }

    if (!isset($activity->limitendcontrol) || ($activity->limitendcontrol == 1)) {
        if (isset($activity->limitendtime) && time() > $activity->limitendtime) {
            return true;
        }
    }
    return false;
}

function scorm_seq_rules_check ($sco, $action) {
    global $DB;
    $act = null;

    if ($rules = $DB->get_records('scorm_seq_ruleconds', array('scoid' => $sco->id, 'action' => $action))) {
        foreach ($rules as $rule) {
            if ($act = scorm_seq_rule_check($sco, $rule)) {
                return $act;
            }
        }
    }
    return $act;

}

function scorm_seq_rule_check ($sco, $rule) {
    global $DB;

    $bag = Array();
    $cond = '';
    $ruleconds = $DB->get_records('scorm_seq_rulecond', array('scoid' => $sco->id, 'ruleconditionsid' => $rule->id));
    foreach ($ruleconds as $rulecond) {
        if ($rulecond->operator == 'not') {
            if ($rulecond->cond != 'unknown' ) {
                $rulecond->cond = 'not'.$rulecond->cond;
            }
        }
         $bag[] = $rulecond->cond;
    }
    if (empty($bag)) {
        $cond = 'unknown';
        return $cond;
    }

    if ($rule->conditioncombination == 'all') {
        foreach ($bag as $con) {
                $cond = $cond.' and '.$con;
        }
    } else {
        foreach ($bag as $con) {
            $cond = $cond.' or '.$con;
        }
    }
    return $cond;
}

function scorm_seq_overall_rollup($sco, $userid, $seq) {
    if ($ancestors = scorm_get_ancestors($sco)) {
        foreach ($ancestors as $ancestor) {
            if (!scorm_is_leaf($ancestor)) {
                scorm_seq_measure_rollup($sco, $userid, $seq->attempt);
            }
            scorm_seq_objective_rollup($sco, $userid, $seq->attempt);
            scorm_seq_activity_progress_rollup($sco, $userid, $seq);
        }
    }
}

function scorm_seq_measure_rollup($sco, $userid, $attempt = 0) {
    global $DB;

    $totalmeasure = 0; // Check if there is something similar in the database.
    $valid = false; // Same as in the last line.
    $countedmeasures = 0; // Same too.
    $targetobjective = null;
    $objectives = $DB->get_records('scorm_seq_objective', array('scoid' => $sco->id));

    foreach ($objectives as $objective) {
        if ($objective->primaryobj == true) { // Objective contributes to rollup.
            $targetobjective = $objective;
            break;
        }

    }
    if ($targetobjective != null) {
        $children = scorm_get_children($sco);
        if (!empty ($children)) {
            foreach ($children as $child) {
                $child = scorm_get_sco ($child);
                if (!isset($child->tracked) || ($child->tracked == 1)) {
                    $rolledupobjective = null;// We set the rolled up activity to undefined.
                    $objectives = $DB->get_records('scorm_seq_objective', array('scoid' => $child->id));
                    foreach ($objectives as $objective) {
                        if ($objective->primaryobj == true) {// Objective contributes to rollup I'm using primaryobj field, but not.
                            $rolledupobjective = $objective;
                            break;
                        }
                    }
                    if ($rolledupobjective != null) {
                        $child = scorm_get_sco($child->id);
                        $countedmeasures = $countedmeasures + ($child->measureweight);
                        if (!scorm_seq_is('objectivemeasurestatus', $sco->id, $userid, $attempt)) {
                            $normalizedmeasure = $DB->get_record('scorm_scoes_track',
                                array('scoid' => $child->id, 'userid' => $userid, 'element' => 'objectivenormalizedmeasure'));
                            $totalmeasure = $totalmeasure + (($normalizedmeasure->value) * ($child->measureweight));
                            $valid = true;
                        }
                    }
                }
            }
        }

        if (!$valid) {
            scorm_seq_set('objectivemeasurestatus', $sco->id, $userid, $attempt, false);
        } else {
            if ($countedmeasures > 0) {
                scorm_seq_set('objectivemeasurestatus', $sco->id, $userid, $attempt);
                $val = $totalmeasure / $countedmeasures;
                scorm_seq_set('objectivenormalizedmeasure', $sco->id, $userid, $attempt, $val);
            } else {
                scorm_seq_set('objectivemeasurestatus', $sco->id, $userid, $attempt, false);
            }
        }
    }
}

function scorm_seq_objective_rollup($sco, $userid, $attempt = 0) {
    global $DB;

    scorm_seq_objective_rollup_measure($sco, $userid, $attempt);
    scorm_seq_objective_rollup_rules($sco, $userid, $attempt);
    scorm_seq_objective_rollup_default($sco, $userid, $attempt);

    /*
    if ($targetobjective->satisfiedbymeasure) {
        scorm_seq_objective_rollup_measure($sco, $userid);
    }
    else{
        if ((scorm_seq_rollup_rule_check($sco, $userid, 'incomplete'))
             || (scorm_seq_rollup_rule_check($sco, $userid, 'completed'))) {
            scorm_seq_objective_rollup_rules($sco, $userid);
        }
        else{

            $rolluprules = $DB->get_record('scorm_seq_rolluprule', array('scoid'=>$sco->id, 'userid'=>$userid));
            foreach ($rolluprules as $rolluprule) {
                $rollupruleconds = $DB->get_records('scorm_seq_rolluprulecond', array('rollupruleid'=>$rolluprule->id));
                foreach ($rollupruleconds as $rolluprulecond) {

                    switch ($rolluprulecond->cond!='satisfied'
                            && $rolluprulecond->cond!='completed' && $rolluprulecond->cond!='attempted') {

                           scorm_seq_set('objectivesatisfiedstatus', $sco->id, $userid, false);

                        break;
                    }
                }


        }
    }
    */
}

function scorm_seq_objective_rollup_measure($sco, $userid, $attempt = 0) {
    global $DB;

    $targetobjective = null;

    $objectives = $DB->get_records('scorm_seq_objective', array('scoid' => $sco->id));
    foreach ($objectives as $objective) {
        if ($objective->primaryobj == true) {
            $targetobjective = $objective;
            break;
        }
    }
    if ($targetobjective != null) {
        if ($targetobjective->satisfiedbymeasure) {
            if (!scorm_seq_is('objectiveprogressstatus', $sco->id, $userid, $attempt)) {
                scorm_seq_set('objectiveprogressstatus', $sco->id, $userid, $attempt, false);
            } else {
                if (scorm_seq_is('active', $sco->id, $userid, $attempt)) {
                    $isactive = true;
                } else {
                    $isactive = false;
                }

                $normalizedmeasure = $DB->get_record('scorm_scoes_track',
                    array('scoid' => $sco->id, 'userid' => $userid, 'element' => 'objectivenormalizedmeasure'));

                $sco = scorm_get_sco ($sco->id);

                if (!$isactive || ($isactive &&
                    (!isset($sco->measuresatisfactionifactive) || $sco->measuresatisfactionifactive == true))) {
                    if (isset($normalizedmeasure->value) && ($normalizedmeasure->value >= $targetobjective->minnormalizedmeasure)) {
                        scorm_seq_set('objectiveprogressstatus', $sco->id, $userid, $attempt);
                        scorm_seq_set('objectivesatisfiedstatus', $sco->id, $userid, $attempt);
                    } else {
                        // TODO: handle the case where cmi.success_status is passed and objectivenormalizedmeasure undefined.
                        scorm_seq_set('objectiveprogressstatus', $sco->id, $userid, $attempt);
                    }
                } else {
                    scorm_seq_set('objectiveprogressstatus', $sco->id, $userid, $attempt, false);
                }
            }
        }
    }
}

function scorm_seq_objective_rollup_default($sco, $userid, $attempt = 0) {
    global $DB;

    if (!(scorm_seq_rollup_rule_check($sco, $userid, 'incomplete')) && !(scorm_seq_rollup_rule_check($sco, $userid, 'completed'))) {
        if ($rolluprules = $DB->get_record('scorm_seq_rolluprule', array('scoid' => $sco->id))) {
            foreach ($rolluprules as $rolluprule) {
                $rollupruleconds = $DB->get_records('scorm_seq_rolluprulecond', array('rollupruleid' => $rolluprule->id));
                foreach ($rollupruleconds as $rolluprulecond) {
                    if ($rolluprulecond->cond != 'satisfied' && $rolluprulecond->cond != 'completed' &&
                            $rolluprulecond->cond != 'attempted') {
                        scorm_seq_set('objectivesatisfiedstatus', $sco->id, $userid, $attempt, false);
                        break;
                    }
                }
            }
        }
    }
}


function scorm_seq_objective_rollup_rules($sco, $userid, $attempt = 0) {
    global $DB;

    $targetobjective = null;

    $objectives = $DB->get_records('scorm_seq_objective', array('scoid' => $sco->id));
    foreach ($objectives as $objective) {
        if ($objective->primaryobj == true) {// Objective contributes to rollup I'm using primaryobj field, but not.
            $targetobjective = $objective;
            break;
        }
    }
    if ($targetobjective != null) {

        if (scorm_seq_rollup_rule_check($sco, $userid, 'notsatisfied')) {// With not satisfied rollup for the activity.
            scorm_seq_set('objectiveprogressstatus', $sco->id, $userid, $attempt);
            scorm_seq_set('objectivesatisfiedstatus', $sco->id, $userid, $attempt, false);
        }
        if (scorm_seq_rollup_rule_check($sco, $userid, 'satisfied')) {// With satisfied rollup for the activity.
            scorm_seq_set('objectiveprogressstatus', $sco->id, $userid, $attempt);
            scorm_seq_set('objectivesatisfiedstatus', $sco->id, $userid, $attempt);
        }

    }

}

function scorm_seq_activity_progress_rollup ($sco, $userid, $seq) {

    if (scorm_seq_rollup_rule_check($sco, $userid, 'incomplete')) {
        // Incomplete rollup action.
        scorm_seq_set('attemptcompletionstatus', $sco->id, $userid, $seq->attempt, false);
        scorm_seq_set('attemptprogressstatus', $sco->id, $userid, $seq->attempt, true);

    }
    if (scorm_seq_rollup_rule_check($sco, $userid, 'completed')) {
        // Incomplete rollup action.
        scorm_seq_set('attemptcompletionstatus', $sco->id, $userid, $seq->attempt, true);
        scorm_seq_set('attemptprogressstatus', $sco->id, $userid, $seq->attempt, true);
    }

}

function scorm_seq_rollup_rule_check ($sco, $userid, $action) {
    global $DB;

    if ($rolluprules = $DB->get_record('scorm_seq_rolluprule', array('scoid' => $sco->id, 'action' => $action))) {
        $childrenbag = Array ();
        $children = scorm_get_children ($sco);

        foreach ($rolluprules as $rolluprule) {
            foreach ($children as $child) {

                /*$tracked = $DB->get_records('scorm_scoes_track', array('scoid'=>$child->id, 'userid'=>$userid));
                if ($tracked && $tracked->attemp != 0) {*/
                $child = scorm_get_sco ($child);
                if (!isset($child->tracked) || ($child->tracked == 1)) {
                    if (scorm_seq_check_child ($child, $action, $userid)) {
                        $rollupruleconds = $DB->get_records('scorm_seq_rolluprulecond', array('rollupruleid' => $rolluprule->id));
                        $evaluate = scorm_seq_evaluate_rollupcond($child, $rolluprule->conditioncombination,
                                                                  $rollupruleconds, $userid);
                        if ($evaluate == 'unknown') {
                            array_push($childrenbag, 'unknown');
                        } else {
                            if ($evaluate == true) {
                                array_push($childrenbag, true);
                            } else {
                                array_push($childrenbag, false);
                            }
                        }
                    }
                }

            }
            $change = false;

            switch ($rolluprule->childactivityset) {

                case 'all':
                    // I think I can use this condition instead equivalent to OR.
                    if ((array_search(false, $childrenbag) === false) && (array_search('unknown', $childrenbag) === false)) {
                        $change = true;
                    }
                break;

                case 'any':
                    // I think I can use this condition instead equivalent to OR.
                    if (array_search(true, $childrenbag) !== false) {
                        $change = true;
                    }
                break;

                case 'none':
                    // I think I can use this condition instead equivalent to OR.
                    if ((array_search(true, $childrenbag) === false) && (array_search('unknown', $childrenbag) === false)) {
                        $change = true;
                    }
                break;

                case 'atleastcount':
                    // I think I can use this condition instead equivalent to OR.
                    foreach ($childrenbag as $itm) {
                        $cont = 0;
                        if ($itm === true) {
                            $cont++;
                        }
                        if ($cont >= $rolluprule->minimumcount) {
                            $change = true;
                        }
                    }
                break;

                case 'atleastcount':
                    foreach ($childrenbag as $itm) {// I think I can use this condition instead equivalent to OR.
                        $cont = 0;
                        if ($itm === true) {
                            $cont++;
                        }
                        if ($cont >= $rolluprule->minimumcount) {
                            $change = true;
                        }
                    }
                break;

                case 'atleastpercent':
                    foreach ($childrenbag as $itm) {// I think I can use this condition instead equivalent to OR.
                        $cont = 0;
                        if ($itm === true) {
                            $cont++;
                        }
                        if (($cont / count($childrenbag)) >= $rolluprule->minimumcount) {
                            $change = true;
                        }
                    }
                break;
            }
            if ($change == true) {
                return true;
            }
        }
    }
    return false;
}

function scorm_seq_flow_tree_traversal($activity, $direction, $childrenflag, $prevdirection, $seq, $userid, $skip = false) {
    $revdirection = false;
    $parent = scorm_get_parent($activity);
    if (!empty($parent)) {
        $children = scorm_get_available_children($parent);
    } else {
        $children = array();
    }
    $childrensize = count($children);

    if (($prevdirection != null && $prevdirection == 'backward') && ($children[$childrensize - 1]->id == $activity->id)) {
        $direction = 'backward';
        $activity = $children[0];
        $revdirection = true;
    }

    if ($direction == 'forward') {
        $ancestors = scorm_get_ancestors($activity);
        $ancestorsroot = array_reverse($ancestors);
        $preorder = array();
        $preorder = scorm_get_preorder($preorder, $ancestorsroot[0]);
        $preordersize = count($preorder);
        if (($activity->id == $preorder[$preordersize - 1]->id) || (($activity->parent == '/') && !($childrenflag))) {
            $seq->endsession = true;
            $seq->nextactivity = null;
            return $seq;
        }
        if (scorm_is_leaf ($activity) || !$childrenflag) {
            if ($children[$childrensize - 1]->id == $activity->id) {
                $seq = scorm_seq_flow_tree_traversal ($parent, $direction, false, null, $seq, $userid);
                if ($seq->nextactivity->launch == null) {
                    $seq = scorm_seq_flow_tree_traversal ($seq->nextactivity, $direction, true, null, $seq, $userid);
                }
                return $seq;
            } else {
                $position = 0;
                foreach ($children as $sco) {
                    if ($sco->id == $activity->id) {
                        break;
                    }
                    $position++;
                }
                if ($position != ($childrensize - 1)) {
                    $seq->nextactivity = $children[$position + 1];
                    $seq->traversaldir = $direction;
                    return $seq;
                } else {
                    $siblings = scorm_get_siblings($activity);
                    $children = scorm_get_children($siblings[0]);
                    $seq->nextactivity = $children[0];
                    return $seq;
                }
            }
        } else {
            $children = scorm_get_available_children($activity);
            if (!empty($children)) {
                $seq->traversaldir = $direction;
                $seq->nextactivity = $children[0];
                return $seq;
            } else {
                $seq->traversaldir = null;
                $seq->nextactivity = null;
                $seq->exception = 'SB.2.1-2';
                return $seq;
            }
        }
    } else if ($direction == 'backward') {
        if ($activity->parent == '/') {
            $seq->traversaldir = null;
            $seq->nextactivity = null;
            $seq->exception = 'SB.2.1-3';
            return $seq;
        }
        if (scorm_is_leaf ($activity) || !$childrenflag) {
            if (!$revdirection) {
                if (isset($parent->forwardonly) && ($parent->forwardonly == true && !$skip)) {
                    $seq->traversaldir = null;
                    $seq->nextactivity = null;
                    $seq->exception = 'SB.2.1-4';
                    return $seq;
                }
            }
            if ($children[0]->id == $activity->id) {
                $seq = scorm_seq_flow_tree_traversal($parent, 'backward', false, null, $seq, $userid);
                return $seq;
            } else {
                $ancestors = scorm_get_ancestors($activity);
                $ancestorsroot = array_reverse($ancestors);
                $preorder = array();
                $preorder = scorm_get_preorder($preorder, $ancestorsroot[0]);
                $position = 0;
                foreach ($preorder as $sco) {
                    if ($sco->id == $activity->id) {
                        break;
                    }
                    $position++;
                }
                if (isset($preorder[$position])) {
                    $seq->nextactivity = $preorder[$position - 1];
                    $seq->traversaldir = $direction;
                }
                return $seq;
            }
        } else {
            $children = scorm_get_available_children($activity);
            if (!empty($children)) {
                if (isset($parent->flow) && ($parent->flow == true)) {
                    $seq->traversaldir = 'forward';
                    $seq->nextactivity = $children[0];
                    return $seq;
                } else {
                    $seq->traversaldir = 'backward';
                    $seq->nextactivity = $children[count($children) - 1];
                    return $seq;
                }
            } else {
                $seq->traversaldir = null;
                $seq->nextactivity = null;
                $seq->exception = 'SB.2.1-2';
                return $seq;
            }
        }
    }
}

// Returns the next activity on the tree, traversal direction, control returned to the LTS, (may) exception.
function scorm_seq_flow_activity_traversal ($activity, $userid, $direction, $childrenflag, $prevdirection, $seq, $userid) {
    $parent = scorm_get_parent ($activity);
    if (!isset($parent->flow) || ($parent->flow == false)) {
        $seq->deliverable = false;
        $seq->exception = 'SB.2.2-1';
        $seq->nextactivity = $activity;
        return $seq;
    }

    $rulecheck = scorm_seq_rules_check($activity, 'skip');
    if ($rulecheck != null) {
        $skip = scorm_evaluate_condition ($rulecheck, $activity, $userid);
        if ($skip) {
            $seq = scorm_seq_flow_tree_traversal($activity, $direction, false, $prevdirection, $seq, $userid, $skip);
            $seq = scorm_seq_flow_activity_traversal($seq->nextactivity, $userid, $direction,
                                                     $childrenflag, $prevdirection, $seq, $userid);
        } else if (!empty($seq->identifiedactivity)) {
            $seq->nextactivity = $activity;
        }
        return $seq;
    }

    $ch = scorm_check_activity ($activity, $userid);
    if ($ch) {
        $seq->deliverable = false;
        $seq->exception = 'SB.2.2-2';
        $seq->nextactivity = $activity;
        return $seq;
    }

    if (!scorm_is_leaf($activity)) {
        $seq = scorm_seq_flow_tree_traversal ($activity, $direction, true, null, $seq, $userid);
        if ($seq->identifiedactivity == null) {
            $seq->deliverable = false;
            $seq->nextactivity = $activity;
            return $seq;
        } else {
            if ($direction == 'backward' && $seq->traversaldir == 'forward') {
                $seq = scorm_seq_flow_activity_traversal($seq->identifiedactivity, $userid,
                                                         'forward', $childrenflag, 'backward', $seq, $userid);
            } else {
                $seq = scorm_seq_flow_activity_traversal($seq->identifiedactivity, $userid,
                                                         $direction, $childrenflag, null, $seq, $userid);
            }
            return $seq;
        }

    }

    $seq->deliverable = true;
    $seq->nextactivity = $activity;
    $seq->exception = null;
    return $seq;

}

function scorm_seq_flow ($activity, $direction, $seq, $childrenflag, $userid) {
    // TODO: $PREVDIRECTION NOT DEFINED YET.
    $prevdirection = null;
    $seq = scorm_seq_flow_tree_traversal ($activity, $direction, $childrenflag, $prevdirection, $seq, $userid);
    if ($seq->nextactivity == null) {
        $seq->nextactivity = $activity;
        $seq->deliverable = false;
        return $seq;
    } else {
        $activity = $seq->nextactivity;
        $seq = scorm_seq_flow_activity_traversal($activity, $userid, $direction, $childrenflag, null, $seq, $userid);
        return $seq;
    }
}

/**
 * Sets up $userdata array and default values for SCORM 1.3 .
 *
 * @param stdClass $userdata an empty stdClass variable that should be set up with user values
 * @param object $scorm package record
 * @param string $scoid SCO Id
 * @param string $attempt attempt number for the user
 * @param string $mode scorm display mode type
 * @return array The default values that should be used for SCORM 1.3 package
 */
function get_scorm_default (&$userdata, $scorm, $scoid, $attempt, $mode) {
    global $DB, $USER;

    $userdata->student_id = $USER->username;
    $userdata->student_name = $USER->lastname .', '. $USER->firstname;

    if ($usertrack = scorm_get_tracks($scoid, $USER->id, $attempt)) {
        // According to SCORM 2004(RTE V1, 4.2.8), only cmi.exit==suspend should allow previous datamodel elements on re-launch.
        if (isset($usertrack->{'cmi.exit'}) && ($usertrack->{'cmi.exit'} == 'suspend')) {
            foreach ($usertrack as $key => $value) {
                $userdata->$key = $value;
            }
        } else {
            $userdata->status = '';
            $userdata->score_raw = '';
        }
    } else {
        $userdata->status = '';
        $userdata->score_raw = '';
    }

    if ($scodatas = scorm_get_sco($scoid, SCO_DATA)) {
        foreach ($scodatas as $key => $value) {
            $userdata->$key = $value;
        }
    } else {
        print_error('cannotfindsco', 'scorm');
    }
    if (!$sco = scorm_get_sco($scoid)) {
        print_error('cannotfindsco', 'scorm');
    }

    if (isset($userdata->status)) {
        if (!isset($userdata->{'cmi.exit'}) || $userdata->{'cmi.exit'} == 'time-out' || $userdata->{'cmi.exit'} == 'normal') {
                $userdata->entry = 'ab-initio';
        } else {
            if (isset($userdata->{'cmi.exit'}) && ($userdata->{'cmi.exit'} == 'suspend' || $userdata->{'cmi.exit'} == 'logout')) {
                $userdata->entry = 'resume';
            } else {
                $userdata->entry = '';
            }
        }
    }

    $userdata->mode = 'normal';
    if (!empty($mode)) {
        $userdata->mode = $mode;
    }
    if ($userdata->mode == 'normal') {
        $userdata->credit = 'credit';
    } else {
        $userdata->credit = 'no-credit';
    }

    $objectives = $DB->get_records('scorm_seq_objective', array('scoid' => $scoid));
    $index = 0;
    foreach ($objectives as $objective) {
        if (!empty($objective->minnormalizedmeasure)) {
            $userdata->{'cmi.scaled_passing_score'} = $objective->minnormalizedmeasure;
        }
        if (!empty($objective->objectiveid)) {
            $userdata->{'cmi.objectives.N'.$index.'.id'} = $objective->objectiveid;
            $index++;
        }
    }

    $def = array();
    $def['cmi.learner_id'] = $userdata->student_id;
    $def['cmi.learner_name'] = $userdata->student_name;
    $def['cmi.mode'] = $userdata->mode;
    $def['cmi.entry'] = $userdata->entry;
    $def['cmi.exit'] = scorm_isset($userdata, 'cmi.exit');
    $def['cmi.credit'] = scorm_isset($userdata, 'credit');
    $def['cmi.completion_status'] = scorm_isset($userdata, 'cmi.completion_status', 'unknown');
    $def['cmi.completion_threshold'] = scorm_isset($userdata, 'threshold');
    $def['cmi.learner_preference.audio_level'] = scorm_isset($userdata, 'cmi.learner_preference.audio_level', 1);
    $def['cmi.learner_preference.language'] = scorm_isset($userdata, 'cmi.learner_preference.language');
    $def['cmi.learner_preference.delivery_speed'] = scorm_isset($userdata, 'cmi.learner_preference.delivery_speed');
    $def['cmi.learner_preference.audio_captioning'] = scorm_isset($userdata, 'cmi.learner_preference.audio_captioning', 0);
    $def['cmi.location'] = scorm_isset($userdata, 'cmi.location');
    $def['cmi.max_time_allowed'] = scorm_isset($userdata, 'attemptAbsoluteDurationLimit');
    $def['cmi.progress_measure'] = scorm_isset($userdata, 'cmi.progress_measure');
    $def['cmi.scaled_passing_score'] = scorm_isset($userdata, 'cmi.scaled_passing_score');
    $def['cmi.score.scaled'] = scorm_isset($userdata, 'cmi.score.scaled');
    $def['cmi.score.raw'] = scorm_isset($userdata, 'cmi.score.raw');
    $def['cmi.score.min'] = scorm_isset($userdata, 'cmi.score.min');
    $def['cmi.score.max'] = scorm_isset($userdata, 'cmi.score.max');
    $def['cmi.success_status'] = scorm_isset($userdata, 'cmi.success_status', 'unknown');
    $def['cmi.suspend_data'] = scorm_isset($userdata, 'cmi.suspend_data');
    $def['cmi.time_limit_action'] = scorm_isset($userdata, 'timelimitaction');
    $def['cmi.total_time'] = scorm_isset($userdata, 'cmi.total_time', 'PT0H0M0S');

    return $def;
}
