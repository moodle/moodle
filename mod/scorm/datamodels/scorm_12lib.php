<?php
/**
* This is really a little language parser for AICC_SCRIPT
* evaluates the expression and returns a boolean answer
* see 2.3.2.5.1. Sequencing/Navigation Today  - from the SCORM 1.2 spec (CAM).
*
* @param string $prerequisites the aicc_script prerequisites expression
* @param array  $usertracks the tracked user data of each SCO visited
* @return boolean
*/
function scorm_eval_prerequisites($prerequisites, $usertracks) {

    // this is really a little language parser - AICC_SCRIPT is the reference
    // see 2.3.2.5.1. Sequencing/Navigation Today  - from the SCORM 1.2 spec
    $element = '';
    $stack = array();
    $statuses = array(
                'passed' => 'passed',
                'completed' => 'completed',
                'failed' => 'failed',
                'incomplete' => 'incomplete',
                'browsed' => 'browsed',
                'not attempted' => 'notattempted',
                'p' => 'passed',
                'c' => 'completed',
                'f' => 'failed',
                'i' => 'incomplete',
                'b' => 'browsed',
                'n' => 'notattempted'
                );
    $i=0;

    // expand the amp entities
    $prerequisites = preg_replace('/&amp;/', '&', $prerequisites);
    // find all my parsable tokens
    $prerequisites = preg_replace('/(&|\||\(|\)|\~)/', '\t$1\t', $prerequisites);
    // expand operators
    $prerequisites = preg_replace('/&/', '&&', $prerequisites);
    $prerequisites = preg_replace('/\|/', '||', $prerequisites);
    // now - grab all the tokens
    $elements = explode('\t', trim($prerequisites));

    // process each token to build an expression to be evaluated
    $stack = array();
    foreach ($elements as $element) {
        $element = trim($element);
        if (empty($element)) {
            continue;
        }
        if (!preg_match('/^(&&|\|\||\(|\))$/', $element)) {
            // create each individual expression
            // search for ~ = <> X*{}

            // sets like 3*{S34, S36, S37, S39}
            if (preg_match('/^(\d+)\*\{(.+)\}$/', $element, $matches)) {
                $repeat = $matches[1];
                $set = explode(',', $matches[2]);
                $count = 0;
                foreach ($set as $setelement) {
                  if (isset($usertracks[$setelement]) &&
                      ($usertracks[$setelement]->status == 'completed' || $usertracks[$setelement]->status == 'passed')) {
                      $count++;
                  }
                }
                if ($count >= $repeat) {
                    $element = 'true';
                } else {
                    $element = 'false';
                }

            // ~ Not
            } else if ($element == '~') {
                $element = '!';

            // = | <>
            } else if (preg_match('/^(.+)(\=|\<\>)(.+)$/', $element, $matches)) {
                $element = trim($matches[1]);
                if (isset($usertracks[$element])) {
                    $value = trim(preg_replace('/(\'|\")/', '', $matches[3]));
                    if (isset($statuses[$value])) {
                        $value = $statuses[$value];
                    }
                    if ($matches[2] == '<>') {
                        $oper = '!=';
                    } else {
                      $oper = '==';
                    }
                    $element = '(\''.$usertracks[$element]->status.'\' '.$oper.' \''.$value.'\')';
                } else {
                  $element = 'false';
                }

            // everything else must be an element defined like S45 ...
            } else {
                if (isset($usertracks[$element]) &&
                    ($usertracks[$element]->status == 'completed' || $usertracks[$element]->status == 'passed')) {
                    $element = 'true';
                } else {
                    $element = 'false';
                }
            }

        }
        $stack []= ' '.$element.' ';
    }
    return eval('return '.implode($stack).';');
}

function scorm_get_toc($user,$scorm,$cmid,$toclink=TOCJSLINK,$currentorg='',$scoid='',$mode='normal',$attempt='',$play=false, $tocheader=false) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    $modestr = '';
    if ($mode == 'browse') {
        $modestr = '&amp;mode='.$mode;
    }

    $result = new stdClass();
    if ($tocheader) {
        $result->toc = '<div id="scorm_layout">';
        $result->toc .= '<div id="scorm_toc">';
        $result->toc .= '<div id="scorm_tree">';
    }
    $result->toc .= '<ul>';
    $tocmenus = array();
    $result->prerequisites = true;
    $incomplete = false;

    //
    // Get the current organization infos
    //
    if (!empty($currentorg)) {
        if (($organizationtitle = $DB->get_field('scorm_scoes','title', array('scorm'=>$scorm->id,'identifier'=>$currentorg))) != '') {
            if ($play) {
                $result->toctitle = "$organizationtitle";
            }
            else {
                $result->toc .= "\t<li>$organizationtitle</li>\n";
            }
            $tocmenus[] = $organizationtitle;
        }
    }

    //
    // If not specified retrieve the last attempt number
    //
    if (empty($attempt)) {
        $attempt = scorm_get_attempt_count($user->id, $scorm);
    }
    $result->attemptleft = $scorm->maxattempt == 0 ? 1 : $scorm->maxattempt - $attempt;
    $conditions['scorm'] = $scorm->id;
    if ($scoes = scorm_get_scoes($scorm->id, $currentorg)){
        //
        // Retrieve user tracking data for each learning object
        //
        $usertracks = array();
        foreach ($scoes as $sco) {
            if (!empty($sco->launch)) {
                if ($usertrack = scorm_get_tracks($sco->id,$user->id,$attempt)) {
                    if ($usertrack->status == '') {
                        $usertrack->status = 'notattempted';
                    }
                    $usertracks[$sco->identifier] = $usertrack;
                }
            }
        }

        $level=0;
        $sublist=1;
        $previd = 0;
        $nextid = 0;
        $findnext = false;
        $parents[$level]='/';

        foreach ($scoes as $pos => $sco) {
            $isvisible = false;
            $sco->title = $sco->title;
            if (!isset($sco->isvisible) || (isset($sco->isvisible) && ($sco->isvisible == 'true'))) {
                $isvisible = true;
            }
            if ($parents[$level] != $sco->parent) {
                if ($newlevel = array_search($sco->parent,$parents)) {
                    for ($i=0; $i<($level-$newlevel); $i++) {
                        $result->toc .= "\t\t</li></ul></li>\n";
                    }
                    $level = $newlevel;
                } else {
                    $i = $level;
                    $closelist = '';
                    while (($i > 0) && ($parents[$level] != $sco->parent)) {
                        $closelist .= "\t\t</li></ul></li>\n";
                        $i--;
                    }
                    if (($i == 0) && ($sco->parent != $currentorg)) {
                        $result->toc .= "\t\t<ul>\n";
                        $level++;
                    } else {
                        $result->toc .= $closelist;
                        $level = $i;
                    }
                    $parents[$level] = $sco->parent;
                }
            }
            if ($isvisible) {
                $result->toc .= "<li>";
            }
            if (isset($scoes[$pos+1])) {
                $nextsco = $scoes[$pos+1];
            } else {
                $nextsco = false;
            }
            $nextisvisible = false;
            if (($nextsco !== false) && (!isset($nextsco->isvisible) || (isset($nextsco->isvisible) && ($nextsco->isvisible == 'true')))) {
                $nextisvisible = true;
            }
            if ($nextisvisible && ($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
                $sublist++;
            }
            if (empty($sco->title)) {
                $sco->title = $sco->identifier;
            }
            if (!empty($sco->launch)) {
                if ($isvisible) {
                    $score = '';
                    if (empty($scoid) && ($mode != 'normal')) {
                        $scoid = $sco->id;
                    }
                    if (isset($usertracks[$sco->identifier])) {
                        $usertrack = $usertracks[$sco->identifier];
                        $strstatus = get_string($usertrack->status,'scorm');
                        if ($sco->scormtype == 'sco') {
                            $statusicon = '<img src="'.$OUTPUT->pix_url($usertrack->status, 'scorm').'" alt="'.$strstatus.'" title="'.$strstatus.'" />';
                        } else {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('assetc', 'scorm').'" alt="'.get_string('assetlaunched','scorm').'" title="'.get_string('assetlaunched','scorm').'" />';
                        }

                        if (($usertrack->status == 'notattempted') || ($usertrack->status == 'incomplete') || ($usertrack->status == 'browsed')) {
                            $incomplete = true;
                            if ($play && empty($scoid)) {
                                $scoid = $sco->id;
                            }
                        }
                        if ($usertrack->score_raw != '' && has_capability('mod/scorm:viewscores', get_context_instance(CONTEXT_MODULE,$cmid))) {
                            $score = '('.get_string('score','scorm').':&nbsp;'.$usertrack->score_raw.')';
                        }
                        $strsuspended = get_string('suspended','scorm');
                        if ($incomplete && isset($usertrack->{'cmi.core.exit'}) && ($usertrack->{'cmi.core.exit'} == 'suspend')) {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('suspend', 'scorm').'" alt="'.$strstatus.' - '.$strsuspended.'" title="'.$strstatus.' - '.$strsuspended.'" />';
                        }
                    } else {
                        if ($play && empty($scoid)) {
                            $scoid = $sco->id;
                        }
                        $incomplete = true;
                        if ($sco->scormtype == 'sco') {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('notattempted', 'scorm').'" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                        } else {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('asset', 'scorm').'" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
                        }
                    }
                    if ($sco->id == $scoid) {
                        $findnext = true;
                    }

                    if (($nextid == 0) && (scorm_count_launchable($scorm->id,$currentorg) > 1) && ($nextsco!==false) && (!$findnext)) {
                        if (!empty($sco->launch)) {
                            $previd = $sco->id;
                        }
                    }
                    if (empty($sco->prerequisites) || scorm_eval_prerequisites($sco->prerequisites,$usertracks)) {
                        if ($sco->id == $scoid) {
                            $result->prerequisites = true;
                        }
                        $thisscoidstr = '&scoid='.$sco->id;
                        $link = 'a='.$scorm->id.$thisscoidstr.'&currentorg='.$currentorg.$modestr.'&attempt='.$attempt;

                        if ($toclink == TOCFULLURL) { //display toc with urls for structure page
                            $url = $CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;currentorg='.$currentorg.$modestr.'&amp;scoid='.$sco->id;
                            $result->toc .= $statusicon.'&nbsp;<a href="'.$url.'">'.format_string($sco->title).'</a>'.$score."\n";
                        } else { //display toc for inside scorm player
                            if ($sco->launch) {
                                $result->toc .= '<a title="'.$link.'">'.$statusicon.'&nbsp;'.format_string($sco->title).'&nbsp;'.$score.'</a>';
                            } else {
                                $result->toc .= '<span>'.$statusicon.'&nbsp;'.format_string($sco->title).'</span>';
                            }
                        }
                        $tocmenus[$sco->id] = scorm_repeater('&minus;',$level) . '&gt;' . format_string($sco->title);
                    } else {
                        if ($sco->id == $scoid) {
                            $result->prerequisites = false;
                        }
                        if ($play) {
                            // should be disabled
                            $result->toc .= '<span>'.$statusicon.'&nbsp;'.format_string($sco->title).'</span>';
                        } else {
                            $result->toc .= $statusicon.'&nbsp;'.format_string($sco->title)."\n";
                        }
                    }
                    if (($nextsco === false) || $nextsco->parent == $sco->parent) {
                        $result->toc .= '</li>';
                    }
                }
            } else {
                $result->toc .= '&nbsp;'.format_string($sco->title)."\n";
            }
            if (($nextsco !== false) && ($nextid == 0) && ($findnext)) {
                if (!empty($nextsco->launch)) {
                    $nextid = $nextsco->id;
                }
            }
        }
        for ($i=0;$i<$level;$i++) {
            $result->toc .= "\t\t</ul></li>\n";
        }

        if ($play) {
            // it is possible that $scoid is still not set, in this case we don't want an empty object
            if ($scoid) {
                $sco = scorm_get_sco($scoid);
            }
            $sco->previd = $previd;
            $sco->nextid = $nextid;
            $result->sco = $sco;
            $result->incomplete = $incomplete;
        } else {
            $result->incomplete = $incomplete;
        }
    }
    $result->toc .= '</ul>';

    // NEW IMS TOC
    if ($tocheader) {
        $result->toc .= '</div></div></div>';
        $result->toc .= '<div id="scorm_navpanel"></div>';
    }


    if ($scorm->hidetoc == 0) {
        $PAGE->requires->data_for_js('scormdata', array(
                'plusicon' => $OUTPUT->pix_url('plus', 'scorm'),
                'minusicon' => $OUTPUT->pix_url('minus', 'scorm')));
        $PAGE->requires->js('/lib/cookies.js');
        $PAGE->requires->js('/mod/scorm/datamodels/scorm_datamodels.js');
    }

    $url = new moodle_url('/mod/scorm/player.php?a='.$scorm->id.'&currentorg='.$currentorg.$modestr);
    $result->tocmenu = $OUTPUT->single_select($url, 'scoid', $tocmenus, $sco->id, null, "tocmenu");

    return $result;
}
