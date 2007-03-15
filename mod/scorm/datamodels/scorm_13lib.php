<?php
/*                // Added by Pham Minh Duc
                case 'ADLNAV:PRESENTATION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    foreach ($block['children'] as $adlnav) {
                        if ($adlnav['name'] == 'ADLNAV:NAVIGATIONINTERFACE') {
                            foreach ($adlnav['children'] as $adlnavInterface) {
                                if ($adlnavInterface['name'] == 'ADLNAV:HIDELMSUI') {
                                    if ($adlnavInterface['tagData'] == 'continue') {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->next = 1; 
                                    }
                                    if ($adlnavInterface['tagData'] == 'previous') {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->previous = 1; 
                                    }
                                }

                            }
                        }
                    }
                break;
                case 'IMSSS:SEQUENCING':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    foreach ($block['children'] as $sequencing) {
                        if ($sequencing['name']=='IMSSS:CONTROLMODE') {
                            if ($sequencing['attrs']['CHOICE'] == 'false') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choice = 0;
                            }
                            if ($sequencing['attrs']['CHOICEEXIT'] == 'false') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choiceexit = 0;
                            }
                            if ($sequencing['attrs']['FLOW'] == 'true') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->flow = 1;
                            }
                            if ($sequencing['attrs']['FORWARDONLY'] == 'true') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->forwardonly = 1;
                            }
                            if ($sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'] == 'true') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptobjectinfo = 1;
                            }
                            if ($sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'] == 'true') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptprogressinfo = 1;
                            }
                        }
                        if ($sequencing['name']=='ADLSEQ:CONSTRAINEDCHOICECONSIDERATIONS') {
                            if ($sequencing['attrs']['CONSTRAINCHOICE'] == 'true') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->constrainChoice = 1;
                            }
                            if ($sequencing['attrs']['PREVENTACTIVATION'] == 'true') {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->preventactivation = 1;
                            }

                        }
                        if ($sequencing['name']=='IMSSS:OBJECTIVES') {
                            foreach ($sequencing['children'] as $objective) {
                                if ($objective['name']=='IMSSS:PRIMARYOBJECTIVE') {
                                    foreach ($objective['children'] as $primaryobjective) {
                                        if ($primaryobjective['name']=='IMSSS:MINNORMALIZEDMEASURE') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->minnormalizedmeasure = $primaryobjective['tagData'];
                                        }
                                    }
                                }
                            }
                        }
                        if ($sequencing['name']=='IMSSS:LIMITCONDITIONS') {
                            if (!empty($sequencing['attrs']['ATTEMPTLIMIT'])) {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptLimit = $sequencing['attrs']['ATTEMPTLIMIT'];                                
                            }
                            if (!empty($sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'])) {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptAbsoluteDurationLimit = $sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'];                                
                            }                            
                        }                        
                        if ($sequencing['name']=='IMSSS:ROLLUPRULES') {
                            $rolluprules = array();
                            if (!empty($sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'])) {
                                if ($sequencing['attrs']['ROLLUPOBJECTIVESATISFIED']== 'false') {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupobjectivesatisfied = 0;
                                }
                            }
                            if (!empty($sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'])) {
                                if ($sequencing['attrs']['ROLLUPPROGRESSCOMPLETION']== 'false') {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupprogresscompletion = 0; 
                                }
                            }
                            if (!empty($sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'])) {
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectivemeasureweight = $sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'];                    
                            }

                            if (!empty($sequencing['children'])){
                                foreach ($sequencing['children'] as $sequencingrolluprule) {
                                    if ($sequencingrolluprule['name']=='IMSSS:ROLLUPRULE' ) {
                                        $rolluprule = new stdClass();
                                        if ($sequencingrolluprule['attrs']['CHILDACTIVITYSET'] !=' ') {
                                            $rolluprule->childactivityset = $sequencingrolluprule['attrs']['CHILDACTIVITYSET'];
                                            if (!empty($sequencingrolluprule['children'])) {
                                                foreach ($sequencingrolluprule['children'] as $rolluproleconditions) {
                                                    if ($rolluproleconditions['name']=='IMSSS:ROLLUPCONDITIONS') {
                                                        $conditions = array();
                                                        if (!empty($rolluproleconditions['attrs']['conditionCombination'])) {
                                                            $rolluprule->conditionCombination = $rolluproleconditions['attrs']['conditionCombination'];
                                                        }
                                                        foreach ($rolluproleconditions['children'] as $rolluprulecondition) {
                                                            if ($rolluprulecondition['name']=='IMSSS:ROLLUPCONDITION') {
                                                                $condition = new stdClass();
                                                                if (!empty($rolluprulecondition['attrs']['OPERATOR'])) {
                                                                    $condition->operator = $rolluprulecondition['attrs']['OPERATOR'];
                                                                }
                                                                if (!empty($rolluprulecondition['attrs']['CONDITION'])) {
                                                                    $condition->condition = $rolluprulecondition['attrs']['CONDITION'];
                                                                }
                                                                array_push($conditions,$condition);    
                                                            }
                                                        }
                                                        $rolluprule->conditions = $conditions;
                                                    }
                                                    if ($rolluproleconditions['name']=='IMSSS:ROLLUPACTION') {
                                                        $rolluprule->rollupruleaction = $rolluproleconditions['attrs']['ACTION'];
                                                    }
                                                }
                                            }
                                        }
                                        array_push($rolluprules, $rolluprule);
                                    }
                                }
                            }
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rolluprules = $rolluprules;
                        }
                        
                        if ($sequencing['name']=='IMSSS:SEQUENCINGRULES') {
                            $sequencingrules = array();
                            foreach ($sequencing['children'] as $conditionrules) {
                                if ($conditionrules['name']=='IMSSS:EXITCONDITIONRULE') {
                                    $sequencingrule = new stdClass();
                                    if (!empty($conditionrules['children'])) {
                                        foreach ($conditionrules['children'] as $conditionrule) {
                                            if ($conditionrule['name']=='IMSSS:RULECONDITIONS') {
                                                $ruleconditions = array();
                                                if (!empty($conditionrule['attrs']['conditionCombination'])) {
                                                    $sequencingrule->conditionCombination = $conditionrule['attrs']['conditionCombination'];
                                                }
                                                foreach ($conditionrule['children'] as $rulecondition) {
                                                    if ($rulecondition['name']=='IMSSS:RULECONDITION') {
                                                        $condition = new stdClass();
                                                        if (!empty($rulecondition['attrs']['OPERATOR'])) {
                                                            $condition->operator = $rulecondition['attrs']['OPERATOR'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['CONDITION'])) {
                                                            $condition->condition = $rulecondition['attrs']['CONDITION'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['MEASURETHRESHOLD'])) {
                                                            $condition->measurethreshold = $rulecondition['attrs']['MEASURETHRESHOLD'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['REFERENCEDOBJECTIVE'])) {
                                                            $condition->referencedobjective = $rulecondition['attrs']['REFERENCEDOBJECTIVE'];
                                                        } 
                                                        array_push($ruleconditions,$condition);
                                                    }
                                                }
                                                $sequencingrule->ruleconditions = $ruleconditions;
                                            }
                                            if ($conditionrule['name']=='IMSSS:RULEACTION') {
                                                $sequencingrule->exitconditionruleaction = $conditionrule['attrs']['ACTION'];
                                            }
                                        }
                                    }
                                    array_push($sequencingrules,$sequencingrule);                                        
                                }
                                if ($conditionrules['name']=='IMSSS:PRECONDITIONRULE') {
                                    $sequencingrule = new stdClass();
                                    if (!empty($conditionrules['children'])) {
                                        foreach ($conditionrules['children'] as $conditionrule) {
                                            if ($conditionrule['name']=='IMSSS:RULECONDITIONS') {
                                                $ruleconditions = array();
                                                if (!empty($conditionrule['attrs']['conditionCombination'])) {
                                                    $sequencingrule->conditionCombination = $conditionrule['attrs']['conditionCombination'];
                                                }
                                                foreach ($conditionrule['children'] as $rulecondition) {
                                                    if ($rulecondition['name']=='IMSSS:RULECONDITION') {
                                                        $condition = new stdClass();
                                                        if (!empty($rulecondition['attrs']['OPERATOR'])) {
                                                            $condition->operator = $rulecondition['attrs']['OPERATOR'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['CONDITION'])) {
                                                            $condition->condition = $rulecondition['attrs']['CONDITION'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['MEASURETHRESHOLD'])) {
                                                            $condition->measurethreshold = $rulecondition['attrs']['MEASURETHRESHOLD'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['REFERENCEDOBJECTIVE'])) {
                                                            $condition->referencedobjective = $rulecondition['attrs']['REFERENCEDOBJECTIVE'];
                                                        } 
                                                        array_push($ruleconditions,$condition);    
                                                    }
                                                }
                                                $sequencingrule->ruleconditions = $ruleconditions;
                                            }
                                            if ($conditionrule['name']=='IMSSS:RULEACTION') {
                                                $sequencingrule->preconditionruleaction = $conditionrule['attrs']['ACTION'];
                                            }
                                        }
                                    }
                                    array_push($sequencingrules,$sequencingrule);                                
                                }
                                if ($conditionrules['name']=='IMSSS:POSTCONDITIONRULE') {
                                    $sequencingrule = new stdClass();
                                    if (!empty($conditionrules['children'])) {
                                        foreach ($conditionrules['children'] as $conditionrule) {
                                            if ($conditionrule['name']=='IMSSS:RULECONDITIONS'){
                                                $ruleconditions = array();
                                                if (!empty($conditionrule['attrs']['conditionCombination'])){
                                                    $sequencingrule->conditionCombination = $conditionrule['attrs']['conditionCombination'];
                                                }
                                                foreach ($conditionrule['children'] as $rulecondition){
                                                    if ($rulecondition['name']=='IMSSS:RULECONDITION'){
                                                        $condition = new stdClass();
                                                        if (!empty($rulecondition['attrs']['OPERATOR'])){
                                                            $condition->operator = $rulecondition['attrs']['OPERATOR'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['CONDITION'])){
                                                            $condition->condition = $rulecondition['attrs']['CONDITION'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['MEASURETHRESHOLD'])){
                                                            $condition->measurethreshold = $rulecondition['attrs']['MEASURETHRESHOLD'];
                                                        }
                                                        if (!empty($rulecondition['attrs']['REFERENCEDOBJECTIVE'])){
                                                            $condition->referencedobjective = $rulecondition['attrs']['REFERENCEDOBJECTIVE'];
                                                        } 
                                                        array_push($ruleconditions,$condition);    
                                                    }
                                                }
                                                $sequencingrule->ruleconditions = $ruleconditions;
                                            }
                                            if ($conditionrule['name']=='IMSSS:RULEACTION'){
                                                $sequencingrule->postconditionruleaction = $conditionrule['attrs']['ACTION'];
                                            }
                                        }
                                    }
                                    array_push($sequencingrules,$sequencingrule);                                
                                }
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->sequencingrules = $sequencingrules;
                            }
                        }
                    }
                break;
*/ 

function scorm_parse_scorm($pkgdir,$scormid) {
    global $CFG;
    
    $launch = 0;
    $manifestfile = $pkgdir.'/imsmanifest.xml';

    if (is_file($manifestfile)) {
    
        $xmlstring = file_get_contents($manifestfile);
        $objXML = new xml2Array();
        $manifests = $objXML->parse($xmlstring);
        //   print_r($manifests); 
        $scoes = new stdClass();
        $scoes->version = '';
        $scoes = scorm_get_manifest($manifests,$scoes);

        if (count($scoes->elements) > 0) {
            $olditems = get_records('scorm_scoes','scorm',$scormid);
            foreach ($scoes->elements as $manifest => $organizations) {
                foreach ($organizations as $organization => $items) {
                    foreach ($items as $identifier => $item) {
                        $item->scorm = $scormid;
                        $item->manifest = $manifest;
                        $item->organization = $organization;
                        if ($olditemid = scorm_array_search('identifier',$item->identifier,$olditems)) {
                            $item->id = $olditemid;
                            $id = update_record('scorm_scoes',$item);
                            unset($olditems[$olditemid]);
                        } else {
                            $id = insert_record('scorm_scoes',$item);
                        }
                        // Added by Pham Minh Duc
                        $item->scormid = $scormid;
                        $item->scoid = $id;
                        $idControlMode = insert_record('scorm_sequencing_controlmode',$item);

                        if (!empty($item->sequencingrules)) {
                            foreach($item->sequencingrules as $sequencingrule) {
                                $sequencingrule->scormid = $scormid;
                                $sequencingrule->scoid = $item->scoid;
                                $idruleconditions = insert_record('scorm_sequencing_ruleconditions',$sequencingrule);
                                foreach($sequencingrule->ruleconditions as $rulecondition) {
                                    $rulecondition->scormid = $sequencingrule->scormid;
                                    $rulecondition->scoid = $sequencingrule->scoid;
                                    $rulecondition->ruleconditionsid = $idruleconditions;
                                    $idrulecondition = insert_record('scorm_sequencing_rulecondition',$rulecondition);
                                }
                            }                        
                        }
                        
                        if (!empty($item->rolluprules)) {
                            $idControlMode = insert_record('scorm_sequencing_rolluprules',$item);
                            foreach($item->rolluprules as $rollup) {
                                $rollup->rolluprulesid =$idControlMode;
                                $rollup->scormid = $scormid;
                                $rollup->scoid =  $item->scoid;

                                $idRollupRule = insert_record('scorm_sequencing_rolluprule',$rollup);
                                $rollup->rollupruleid = $idRollupRule;
                                $idconditions = insert_record('scorm_sequencing_rollupruleconditions',$rollup);
                                foreach($rollup->conditions as $condition){
                                    $condition->ruleconditionsid = $idconditions;
                                    $condition->scormid = $rollup->scormid;
                                    $condition->scoid = $rollup->scoid;
                                    $idcondition = insert_record('scorm_sequencing_rolluprulecondition',$condition);
                                }
                            }
                        }
                        // End Add
                        if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier))) {
                            $launch = $id;
                        }
                    }
                }
            }
            if (!empty($olditems)) {
                foreach($olditems as $olditem) {
                   delete_records('scorm_scoes','id',$olditem->id);
                   delete_records('scorm_scoes_track','scoid',$olditem->id);
                }
            }
            set_field('scorm','version',$scoes->version,'id',$scormid);
        }
    } 
    
    return $launch;
}

function scorm_get_toc($user,$scorm,$liststyle,$currentorg='',$scoid='',$mode='normal',$attempt='',$play=false) {
    global $CFG;

    $strexpand = get_string('expcoll','scorm');
    $modestr = '';
    if ($mode == 'browse') {
        $modestr = '&amp;mode='.$mode;
    } 
    $scormpixdir = $CFG->modpixpath.'/scorm/pix';
    
    $result = new stdClass();
    $result->toc = "<ul id='0' class='$liststyle'>\n";
    $tocmenus = array();
    $result->prerequisites = true;
    $incomplete = false;
    
    //
    // Get the current organization infos
    //
    $organizationsql = '';
    if (!empty($currentorg)) {
        if (($organizationtitle = get_field('scorm_scoes','title','scorm',$scorm->id,'identifier',$currentorg)) != '') {
            $result->toc .= "\t<li>$organizationtitle</li>\n";
            $tocmenus[] = $organizationtitle;
        }
        $organizationsql = "AND organization='$currentorg'";
    }
    //
    // If not specified retrieve the last attempt number
    //
    if (empty($attempt)) {
        $attempt = scorm_get_last_attempt($scorm->id, $user->id);
    }
    $result->attemptleft = $scorm->maxattempt - $attempt;
    if ($scoes = get_records_select('scorm_scoes',"scorm='$scorm->id' $organizationsql order by id ASC")){
        //
        // Retrieve user tracking data for each learning object
        // 
    
        // Added by Pham Minh Duc
         $suspendedscoid = scorm_get_suspendedscoid($scorm->id,$user->id,$attempt);
        // End add
 
        $usertracks = array();
        foreach ($scoes as $sco) {
            if (!empty($sco->launch)) {
                if ($usertrack=scorm_get_tracks($sco->id,$user->id,$attempt)) {
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
        
        foreach ($scoes as $sco) {
            if ($parents[$level]!=$sco->parent) {
                if ($newlevel = array_search($sco->parent,$parents)) {
                    for ($i=0; $i<($level-$newlevel); $i++) {
                        $result->toc .= "\t\t</ul></li>\n";
                    }
                    $level = $newlevel;
                } else {
                    $i = $level;
                    $closelist = '';
                    while (($i > 0) && ($parents[$level] != $sco->parent)) {
                        $closelist .= "\t\t</ul></li>\n";
                        $i--;
                    }
                    if (($i == 0) && ($sco->parent != $currentorg)) {
                        $style = '';
                        if (isset($_COOKIE['hide:SCORMitem'.$sco->id])) {
                            $style = ' style="display: none;"';
                        }
                        $result->toc .= "\t\t<li><ul id='$sublist' class='$liststyle'$style>\n";
                        $level++;
                    } else {
                        $result->toc .= $closelist;
                        $level = $i;
                    }
                    $parents[$level]=$sco->parent;
                }
            }
            $result->toc .= "\t\t<li>";
            $nextsco = next($scoes);
            if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
                $sublist++;
                $icon = 'minus';
                if (isset($_COOKIE['hide:SCORMitem'.$nextsco->id])) {
                    $icon = 'plus';
                }
                $result->toc .= '<a href="javascript:expandCollide(img'.$sublist.','.$sublist.','.$nextsco->id.');"><img id="img'.$sublist.'" src="'.$scormpixdir.'/'.$icon.'.gif" alt="'.$strexpand.'" title="'.$strexpand.'"/></a>';
            } else {
                $result->toc .= '<img src="'.$scormpixdir.'/spacer.gif" />';
            }
            if (empty($sco->title)) {
                $sco->title = $sco->identifier;
            }
            if (!empty($sco->launch)) {
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
                        $statusicon = '<img src="'.$scormpixdir.'/'.$usertrack->status.'.gif" alt="'.$strstatus.'" title="'.$strstatus.'" />';
                    } else {
                        $statusicon = '<img src="'.$scormpixdir.'/assetc.gif" alt="'.get_string('assetlaunched','scorm').'" title="'.get_string('assetlaunched','scorm').'" />';
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
                        $statusicon = '<img src="'.$scormpixdir.'/suspend.gif" alt="'.$strstatus.' - '.$strsuspended.'" title="'.$strstatus.' - '.$strsuspended.'" />';
                    }
                } else {
                    if ($play && empty($scoid)) {
                        $scoid = $sco->id;
                    }
                    $incomplete = true;
                    if ($sco->scormtype == 'sco') {
                        $statusicon = '<img src="'.$scormpixdir.'/notattempted.gif" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                    } else {
                        $statusicon = '<img src="'.$scormpixdir.'/asset.gif" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
                    }
                }

                if ($sco->id == $scoid) {
                    $startbold = '<b>';
                    $endbold = '</b>';
                    $findnext = true;
                    $shownext = $sco->next;
                    $showprev = $sco->previous;
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
                // Modified by Pham Minh Duc
                //    if (scorm_isChoice($scorm->id,$sco->id) == 1) {
                        $url = $CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;currentorg='.$currentorg.$modestr.'&amp;scoid='.$sco->id;
                        $result->toc .= $statusicon.'&nbsp;'.$startbold.'<a href="'.$url.'">'.format_string($sco->title).'</a>'.$score.$endbold."</li>\n";
                        $tocmenus[$sco->id] = scorm_repeater('&minus;',$level) . '&gt;' . format_string($sco->title);
                //    } else {
                //       $result->toc .= '&nbsp;'.$startbold.format_string($sco->title).$score.$endbold."</li>\n";
                //        $tocmenus[$sco->id] = scorm_repeater('&minus;',$level) . '&gt;' . format_string($sco->title);                    
                //    }
                // End modify
                } else {
                    if ($sco->id == $scoid) {
                        $result->prerequisites = false;
                    }
                    $result->toc .= '&nbsp;'.$sco->title."</li>\n";
                }
            } else {
                $result->toc .= '&nbsp;'.$sco->title."</li>\n";
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
            $sco = get_record('scorm_scoes','id',$scoid);
            $sco->previd = $previd;
            $sco->nextid = $nextid;
            $result->sco = $sco;
            $result->incomplete = $incomplete;
        } else {
            $result->incomplete = $incomplete;
        }
    }
    $result->toc .= "\t</ul>\n";
    if ($scorm->hidetoc == 0) {
        $result->toc .= '
          <script language="javascript" type="text/javascript">
          <!--
              function expandCollide(which,list,item) {
                  var nn=document.ids?true:false
                  var w3c=document.getElementById?true:false
                  var beg=nn?"document.ids.":w3c?"document.getElementById(":"document.all.";
                  var mid=w3c?").style":".style";

                  if (eval(beg+list+mid+".display") != "none") {
                      which.src = "'.$scormpixdir.'/plus.gif";
                      eval(beg+list+mid+".display=\'none\';");
                      new cookie("hide:SCORMitem" + item, 1, 356, "/").set();
                  } else {
                      which.src = "'.$scormpixdir.'/minus.gif";
                      eval(beg+list+mid+".display=\'block\';");
                      new cookie("hide:SCORMitem" + item, 1, -1, "/").set();
                  }
              }
          -->
          </script>'."\n";
    }
    
    $url = $CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;currentorg='.$currentorg.$modestr.'&amp;scoid=';
    $result->tocmenu = popup_form($url,$tocmenus, "tocmenu", $sco->id, '', '', '', true);

    return $result;
}

//
// Functions added by Pham Minh Duc
//
function scorm_get_score_from_parent($sco,$userid,$grademethod=VALUESCOES) {
    $scores = NULL; 
    $scores->scoes = 0;
    $scores->values = 0;
    $scores->scaled = 0;
    $scores->max = 0;
    $scores->sum = 0;

    $scoes_total = 0;
    $scoes_count = 0;
    $attempt = scorm_get_last_attempt($sco->scorm, $userid);
    $scoes = get_records('scorm_scoes', 'parent', $sco->identifier);
    foreach ($scoes as $sco)
    {
        $scoes_total++;
        if ($userdata=scorm_get_tracks($sco->id, $userid,$attempt)) {
            if (($userdata->status == 'completed') || ($userdata->success_status == 'passed')) {
                $scoes_count++;
            }

            $scoreraw = $userdata->score_raw; 
            if (!empty($userdata->score_raw)) {
                $scores->values++;
                $scores->sum += $userdata->score_raw;
                $scores->max = ($userdata->score_raw > $scores->max)?$userdata->score_raw:$scores->max;
            }   
            if (!empty($userdata->score_scaled)) {
                $scores->scaled = $scores->scaled + $userdata->score_scaled;
            }       
        }       
    }
    if ($scoes_count > 0) {
        $scores->scaled = ($scores->scaled)/($scoes_count);
    }
    switch ($grademethod) {
        case VALUEHIGHEST:
            return $scores->max;
        break;  
        case VALUEAVERAGE:
            if ($scores->values > 0) {
                return $scores->sum/$scores->values;
            } else {
                return 0;
            }       
        break;  
        case VALUESUM:
            return $scores->sum;
        break;  
        case VALUESCOES:
            return $scores->scaled;
        break;  
    }
}

function scorm_get_user_sco_count($scormid, $userid) {
    $scoes_count = 0;
    $attempt = scorm_get_last_attempt($current->scorm, $userid);
    $scoes = get_records('scorm_scoes', 'scorm', $scormid);

    foreach ($scoes as $sco) {
        if ($userdata=scorm_get_tracks($sco->id, $userid,$attempt)) {
            if (($userdata->status == 'completed') || ($userdata->success_status == 'passed')) {
                $scoes_count++;
            }
        }
    }
    return $scoes_count;
}

function scorm_grade_user_new($scoes, $userid, $grademethod=VALUESCOES) {
    $scores = NULL; 
    $scores->scoes = 0;
    $scores->values = 0;
    $scores->scaled = 0;
    $scores->max = 0;
    $scores->sum = 0;

    if (!$scoes) {
        return '';
    }
    $current = current($scoes);
    $attempt = scorm_get_last_attempt($current->scorm, $userid);
    foreach ($scoes as $sco) { 
        if ($userdata=scorm_get_tracks($sco->id, $userid,$attempt)) {
            if (($userdata->status == 'completed') || ($userdata->success_status == 'passed')) {
                $scores->scoes++;
            }  
            $scaled = $userdata->score_scaled;
            $scoreraw = $userdata->score_raw; 
            if ($scaled ==0){
                $scores->scaled = $scores->scaled / $scores->scoes;
            }
            if (!empty($userdata->score_raw)) {
                $scores->values++;
                $scores->sum += $userdata->score_raw;
                $scores->max = ($userdata->score_raw > $scores->max)?$userdata->score_raw:$scores->max;
            }  
            if (!empty($scaled)) {
                $scores->scaled = (($scores->scaled) * ($scores->scoes-1) + $scaled)/($scores->scoes);
            }       
        }       
    }
    switch ($grademethod) {
        case VALUEHIGHEST:
            return $scores->max;
        break;  
        case VALUEAVERAGE:
            if ($scores->values > 0) {
                return $scores->sum/$scores->values;
            } else {
                return 0;
            }       
        break;  
        case VALUESUM:
            return $scores->sum;
        break;  
        case VALUESCOES:
            return $scores->scaled;
        break;  
    }
}

function scorm_get_suspendedscoid($scormid,$userid,$attempt) {
    if ($sco = get_record_select("scorm_scoes_track","scormid=$scormid AND userid=$userid AND attempt=$attempt AND (element='cmi.exit' OR element='cmi.core.exit') AND value='suspend'")) {
        return $sco->scoid;
    } else {
        return 0;
    }
}

function scorm_set_attempt($scoid,$userid) {
    if ($scormid = get_field('scorm_scoes','scorm','id',$scoid)) {
        $attempt = scorm_get_last_attempt($scormid,$userid);
    } else {
        $attempt = 1;
    }
    $scormtype = get_field('scorm_scoes','scormtype','id',$scoid) ;
    if ($scormtype == 'sco'){
        $element = 'cmi.attempt_status';
        $value = 'attempted';
        scorm_insert_track($userid,$scormid,$scoid,$attempt,$element,$value);
    }
}

function scorm_isChoice($scormid,$scoid)
{
    $sco = get_record("scorm_scoes","id",$scoid);
    $scoparent = get_record("scorm_sequencing_controlmode","scormid",$scormid,"identifier",$sco->parent);

    return $scoparent->choice;
}

function scorm_isChoiceexit($scormid,$scoid)
{
    $sco = get_record("scorm_scoes","id",$scoid);
    $scoparent = get_record("scorm_sequencing_controlmode","scormid",$scormid,"identifier",$sco->parent);

    return $scoparent->choiceexit;
}
// End add

?>
