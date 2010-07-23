<?php

function scorm_get_toc($user,$scorm,$liststyle,$currentorg='',$scoid='',$mode='normal',$attempt='',$play=false, $tocheader=false) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    $strexpand = get_string('expcoll','scorm');
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
        if (($organizationtitle = $DB->get_field('scorm_scoes', 'title', array('scorm'=>$scorm->id,'identifier'=>$currentorg))) != '') {
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
        $attempt = scorm_get_last_attempt($scorm->id, $user->id);
    }
    $result->attemptleft = $scorm->maxattempt - $attempt;
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
            if ($parents[$level]!=$sco->parent) {
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
                        $style = '';
                        if (isset($_COOKIE['hide:SCORMitem'.$sco->id])) {
                            $style = ' style="display: none;"';
                        }
                        //$result->toc .= "\t\t<li><ul id='s$sublist' class='$liststyle'$style>\n";
                        $result->toc .= "\t\t><ul>\n";
                        $level++;
                    } else {
                        $result->toc .= $closelist;
                        $level = $i;
                    }
                    $parents[$level]=$sco->parent;
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
            if (!isset($nextsco->isvisible) || (isset($nextsco->isvisible) && ($nextsco->isvisible == 'true'))) {
                $nextisvisible = true;
            }
            if ($nextisvisible && ($nextsco !== false) && ($sco->parent != $nextsco->parent) &&
               (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
                $sublist++;
                $icon = 'minus';
                if (isset($_COOKIE['hide:SCORMitem'.$nextsco->id])) {
                    $icon = 'plus';
                }
//                $result->toc .= "\t\t".'<li><a href="javascript:expandCollide(\'img'.$sublist.'\',\'s'.$sublist.'\','.$nextsco->id.');">'.
//                                '<img id="img'.$sublist.'" src="'.$OUTPUT->pix_url($icon, 'scorm').'" alt="'.$strexpand.'" title="'.$strexpand.'"/></a>';
            } else if ($isvisible) {
//                $result->toc .= "\t\t".'<li><img src="'.$OUTPUT->pix_url('spacer', 'scorm').'" alt="" />';
            }
            if (empty($sco->title)) {
                $sco->title = $sco->identifier;
            }
            if (!empty($sco->launch)) {
                if ($isvisible) {
                    $startbold = '';
                    $endbold = '';
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
                        if ($usertrack->score_raw != '') {
                            $score = '('.get_string('score','scorm').':&nbsp;'.$usertrack->score_raw.')';
                        }
                        $strsuspended = get_string('suspended','scorm');
                        if (isset($usertrack->{'cmi.core.exit'}) && ($usertrack->{'cmi.core.exit'} == 'suspend')) {
                            if($usertrack->status !='completed') {
                                $statusicon = '<img src="'.$OUTPUT->pix_url('suspend', 'scorm').'" alt="'.$strstatus.' - '.$strsuspended.'" title="'.$strstatus.' - '.$strsuspended.'" />';
                            }
                        }
                    } else {
                        if ($play && empty($scoid)) {
                            $scoid = $sco->id;
                        }
                        if ($sco->scormtype == 'sco') {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('notattempted', 'scorm').'" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                            $incomplete = true;
                        } else {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('asset', 'scorm').'" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
                        }
                    }

                    if ($sco->id == $scoid) {
                        $startbold = '<b>';
                        $endbold = '</b>';
                        $findnext = true;
                        $shownext = isset($sco->next) ? $sco->next : 0;
                        $showprev = isset($sco->prev) ? $sco->prev : 0;
                    }

                    if (($nextid == 0) && (scorm_count_launchable($scorm->id,$currentorg) > 1) && ($nextsco!==false) && (!$findnext)) {
                        if (!empty($sco->launch)) {
                            $previd = $sco->id;
                        }
                    }
                    require_once($CFG->dirroot.'/mod/scorm/datamodels/sequencinglib.php');
                    if (scorm_seq_evaluate($sco->id,$usertracks)) {
                        if ($sco->id == $scoid) {
                            $result->prerequisites = true;
                        }
                            $url = $CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;currentorg='.$currentorg.$modestr.'&amp;scoid='.$sco->id;
                            $thisscoidstr = '&scoid='.$sco->id;
                            //$link = $CFG->wwwroot.'/mod/scorm/loadSCO.php?a='.$scorm->id.$thisscoidstr.$modestr;
                            $link = 'a='.$scorm->id.$thisscoidstr.'&currentorg='.$currentorg.$modestr.'&attempt='.$attempt;
                            //$result->toc .= $statusicon.'&nbsp;'.$startbold.'<a href="'.$url.'">'.format_string($sco->title).'</a>'.$score.$endbold."</li>\n";
                            //$result->toc .= '<li><a title="'.$link.'">'.$statusicon.'&nbsp;'.format_string($sco->title).'&nbsp;'.$score.'</a>'."</li>\n";
                            if ($sco->launch) {
                                $result->toc .= '<a title="'.$link.'">'.$statusicon.'&nbsp;'.format_string($sco->title).'&nbsp;'.$score.'</a>';
                            }
                            else {
                                $result->toc .= '<span>'.$statusicon.'&nbsp;'.format_string($sco->title).'</span>';
                            }
                            $tocmenus[$sco->id] = scorm_repeater('&minus;',$level) . '&gt;' . format_string($sco->title);
                    } else {
                        if ($sco->id == $scoid) {
                            $result->prerequisites = false;
                        }
                        //$result->toc .= $statusicon.'&nbsp;'.format_string($sco->title)."\n";
                        if ($play) {
                            // should be disabled
                            $result->toc .= '<span>'.$statusicon.'&nbsp;'.format_string($sco->title).'</span>';
                        }
                        else {
                            $result->toc .= $statusicon.'&nbsp;'.format_string($sco->title)."\n";
                        }
                    }
                    if (($nextsco === false) || $nextsco->parent == $sco->parent) {
                        $result->toc .= '</li>';
                    }
                }
            } else {
                $result->toc .= '&nbsp;'.format_string($sco->title)."</li>\n";
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
            $sco = $DB->get_record('scorm_scoes', array('id'=>$scoid));
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


