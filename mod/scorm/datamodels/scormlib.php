<?php
function scorm_get_resources($blocks) {
    $resources = array();
    foreach ($blocks as $block) {
        if ($block['name'] == 'RESOURCES') {
            foreach ($block['children'] as $resource) {
                if ($resource['name'] == 'RESOURCE') {
                    $resources[addslashes_js($resource['attrs']['IDENTIFIER'])] = $resource['attrs'];
                }
            }
        }
    }
    return $resources;
}

function scorm_get_manifest($blocks,$scoes) {
    static $parents = array();
    static $resources;

    static $manifest;
    static $organization;

    if (count($blocks) > 0) {
        foreach ($blocks as $block) {
            switch ($block['name']) {
                case 'METADATA':
                    if (isset($block['children'])) {
                        foreach ($block['children'] as $metadata) {
                            if ($metadata['name'] == 'SCHEMAVERSION') {
                                if (empty($scoes->version)) {
                                    if (isset($metadata['tagData']) && (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/",$metadata['tagData'],$matches))) {
                                        $scoes->version = 'SCORM_'.$matches[count($matches)-1];
                                    } else {
                                        if (isset($metadata['tagData']) && (preg_match("/^2004 3rd Edition$/",$metadata['tagData'],$matches))) {
                                            $scoes->version = 'SCORM_1.3';
                                        } else {
                                            $scoes->version = 'SCORM_1.2';
                                        }
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'MANIFEST':
                    $manifest = addslashes_js($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $resources = array();
                    $resources = scorm_get_resources($block['children']);
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    if (count($scoes->elements) <= 0) {
                        foreach ($resources as $item => $resource) {
                            if (!empty($resource['HREF'])) {
                                $sco = new stdClass();
                                $sco->identifier = $item;
                                $sco->title = $item;
                                $sco->parent = '/';
                                $sco->launch = addslashes_js($resource['HREF']);
                                $sco->scormtype = addslashes_js($resource['ADLCP:SCORMTYPE']);
                                $scoes->elements[$manifest][$organization][$item] = $sco;
                            }
                        }
                    }
                break;
                case 'ORGANIZATIONS':
                    if (!isset($scoes->defaultorg) && isset($block['attrs']['DEFAULT'])) {
                        $scoes->defaultorg = addslashes_js($block['attrs']['DEFAULT']);
                    }
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                break;
                case 'ORGANIZATION':
                    $identifier = addslashes_js($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = '/';
                    $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                    $scoes->elements[$manifest][$organization][$identifier]->scormtype = '';

                    $parents = array();
                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);
                    $organization = $identifier;

                    $scoes = scorm_get_manifest($block['children'],$scoes);

                    array_pop($parents);
                break;
                case 'ITEM':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);

                    $identifier = addslashes_js($block['attrs']['IDENTIFIER']);
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = $parent->identifier;
                    if (!isset($block['attrs']['ISVISIBLE'])) {
                        $block['attrs']['ISVISIBLE'] = 'true';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->isvisible = addslashes_js($block['attrs']['ISVISIBLE']);
                    if (!isset($block['attrs']['PARAMETERS'])) {
                        $block['attrs']['PARAMETERS'] = '';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->parameters = addslashes_js($block['attrs']['PARAMETERS']);
                    if (!isset($block['attrs']['IDENTIFIERREF'])) {
                        $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = 'asset';
                    } else {
                        $idref = addslashes_js($block['attrs']['IDENTIFIERREF']);
                        $base = '';
                        if (isset($resources[$idref]['XML:BASE'])) {
                            $base = $resources[$idref]['XML:BASE'];
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->launch = addslashes_js($base.$resources[$idref]['HREF']);
                        if (empty($resources[$idref]['ADLCP:SCORMTYPE'])) {
                            $resources[$idref]['ADLCP:SCORMTYPE'] = 'asset';
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = addslashes_js($resources[$idref]['ADLCP:SCORMTYPE']);
                    }

                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);

                    $scoes = scorm_get_manifest($block['children'],$scoes);

                    array_pop($parents);
                break;
                case 'TITLE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = addslashes_js($block['tagData']);
                break;
                case 'ADLCP:PREREQUISITES':
                    if ($block['attrs']['TYPE'] == 'aicc_script') {
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = addslashes_js($block['tagData']);
                    }
                break;
                case 'ADLCP:MAXTIMEALLOWED':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = addslashes_js($block['tagData']);
                break;
                case 'ADLCP:TIMELIMITACTION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = addslashes_js($block['tagData']);
                break;
                case 'ADLCP:DATAFROMLMS':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = addslashes_js($block['tagData']);
                break;
                case 'ADLCP:MASTERYSCORE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = addslashes_js($block['tagData']);
                break;
                case 'ADLCP:COMPLETIONTHRESHOLD':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->threshold = addslashes_js($block['tagData']);
                break;
                case 'ADLNAV:PRESENTATION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!empty($block['children'])) {
                        foreach ($block['children'] as $adlnav) {
                            if ($adlnav['name'] == 'ADLNAV:NAVIGATIONINTERFACE') {
                                foreach ($adlnav['children'] as $adlnavInterface) {
                                    if ($adlnavInterface['name'] == 'ADLNAV:HIDELMSUI') {
                                        if ($adlnavInterface['tagData'] == 'continue') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hidecontinue = 1;
                                        }
                                        if ($adlnavInterface['tagData'] == 'previous') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideprevious = 1;
                                        }
                                        if ($adlnavInterface['tagData'] == 'exit') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideexit = 1;
                                        }
                                        if ($adlnavInterface['tagData'] == 'exitAll') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideexitall = 1;
                                        }
                                        if ($adlnavInterface['tagData'] == 'abandon') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideabandon = 1;
                                        }
                                        if ($adlnavInterface['tagData'] == 'abandonAll') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideabandonall = 1;
                                        }
                                        if ($adlnavInterface['tagData'] == 'suspendAll') {
                                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hidesuspendall = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'IMSSS:SEQUENCING':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!empty($block['children'])) {
                        foreach ($block['children'] as $sequencing) {
                            if ($sequencing['name']=='IMSSS:CONTROLMODE') {
                                if (isset($sequencing['attrs']['CHOICE'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choice = $sequencing['attrs']['CHOICE'] == 'true'?1:0;
                                }
                                if (isset($sequencing['attrs']['CHOICEEXIT'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choiceexit = $sequencing['attrs']['CHOICEEXIT'] == 'true'?1:0;
                                }
                                if (isset($sequencing['attrs']['FLOW'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->flow = $sequencing['attrs']['FLOW'] == 'true'?1:0;
                                }
                                if (isset($sequencing['attrs']['FORWARDONLY'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->forwardonly = $sequencing['attrs']['FORWARDONLY'] == 'true'?1:0;
                                }
                                if (isset($sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptobjectinfo = $sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'] == 'true'?1:0;
                                }
                                if (isset($sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptprogressinfo = $sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'] == 'true'?1:0;
                                }
                            }
                            if ($sequencing['name']=='ADLSEQ:CONSTRAINEDCHOICECONSIDERATIONS') {
                                if (isset($sequencing['attrs']['CONSTRAINCHOICE'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->constrainChoice = $sequencing['attrs']['CONSTRAINCHOICE'] == 'true'?1:0;
                                }
                                if (isset($sequencing['attrs']['PREVENTACTIVATION'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->preventactivation = $sequencing['attrs']['PREVENTACTIVATION'] == 'true'?1:0;
                                }
                            }
                            if ($sequencing['name']=='IMSSS:OBJECTIVES') {
                                $objectives = array();
                                foreach ($sequencing['children'] as $objective) {
                                    $objectivedata = new stdClass();
                                    $objectivedata->primaryobj = 0;
                                    switch ($objective['name']) {
                                        case 'IMSSS:PRIMARYOBJECTIVE':
                                            $objectivedata->primaryobj = 1;
                                        case 'IMSSS:OBJECTIVE':
                                            $objectivedata->satisfiedbymeasure = 0;
                                            if (isset($objective['attrs']['SATISFIEDBYMEASURE'])) {
                                                $objectivedata->satisfiedbymeasure = $objective['attrs']['SATISFIEDBYMEASURE']== 'true'?1:0;
                                            }
                                            $objectivedata->objectiveid = '';
                                            if (isset($objective['attrs']['OBJECTIVEID'])) {
                                                $objectivedata->objectiveid = $objective['attrs']['OBJECTIVEID'];
                                            }
                                            $objectivedata->minnormalizedmeasure = 1.0;
                                            if (!empty($objective['children'])) {
                                                $mapinfos = array();
                                                foreach ($objective['children'] as $objectiveparam) {
                                                    if ($objectiveparam['name']=='IMSSS:MINNORMALIZEDMEASURE') {
                                                        if (isset($objectiveparam['tagData'])) {
                                                        	$objectivedata->minnormalizedmeasure = $objectiveparam['tagData'];
                                                        } else {
                                                            $objectivedata->minnormalizedmeasure = 0;
                                                        }
                                                    }
                                                    if ($objectiveparam['name']=='IMSSS:MAPINFO') {
                                                        $mapinfo = new stdClass();
                                                        $mapinfo->targetobjectiveid = '';
                                                        if (isset($objectiveparam['attrs']['TARGETOBJECTIVEID'])) {
                                                            $mapinfo->targetobjectiveid = $objectiveparam['attrs']['TARGETOBJECTIVEID'];
                                                        }
                                                        $mapinfo->readsatisfiedstatus = 1;
                                                        if (isset($objectiveparam['attrs']['READSATISFIEDSTATUS'])) {
                                                            $mapinfo->readsatisfiedstatus = $objectiveparam['attrs']['READSATISFIEDSTATUS'] == 'true'?1:0;
                                                        }
                                                        $mapinfo->writesatisfiedstatus = 0;
                                                        if (isset($objectiveparam['attrs']['WRITESATISFIEDSTATUS'])) {
                                                            $mapinfo->writesatisfiedstatus = $objectiveparam['attrs']['WRITESATISFIEDSTATUS'] == 'true'?1:0;
                                                        }
                                                        $mapinfo->readnormalizemeasure = 1;
                                                        if (isset($objectiveparam['attrs']['READNORMALIZEDMEASURE'])) {
                                                            $mapinfo->readnormalizemeasure = $objectiveparam['attrs']['READNORMALIZEDMEASURE'] == 'true'?1:0;
                                                        }
                                                        $mapinfo->writenormalizemeasure = 0;
                                                        if (isset($objectiveparam['attrs']['WRITENORMALIZEDMEASURE'])) {
                                                            $mapinfo->writenormalizemeasure = $objectiveparam['attrs']['WRITENORMALIZEDMEASURE'] == 'true'?1:0;
                                                        }
                                                        array_push($mapinfos,$mapinfo);
                                                    }
                                                }
                                                if (!empty($mapinfos)) {
                                                    $objectivesdata->mapinfos = $mapinfos;
                                                }
                                            }
                                        break;
                                    }
//print_object($objectivedata);
                                    array_push($objectives,$objectivedata);
                                }
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectives = $objectives;
                            }
                            if ($sequencing['name']=='IMSSS:LIMITCONDITIONS') {
                                if (isset($sequencing['attrs']['ATTEMPTLIMIT'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptLimit = $sequencing['attrs']['ATTEMPTLIMIT'];
                                }
                                if (isset($sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptAbsoluteDurationLimit = $sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'];
                                }
                            }
                            if ($sequencing['name']=='IMSSS:ROLLUPRULES') {
                                if (isset($sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupobjectivesatisfied = $sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'] == 'true'?1:0;;
                                }
                                if (isset($sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupprogresscompletion = $sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'] == 'true'?1:0;
                                }
                                if (isset($sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'])) {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectivemeasureweight = $sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'];
                                }

                                if (!empty($sequencing['children'])){
                                    $rolluprules = array();
                                    foreach ($sequencing['children'] as $sequencingrolluprule) {
                                        if ($sequencingrolluprule['name']=='IMSSS:ROLLUPRULE' ) {
                                            $rolluprule = new stdClass();
                                            $rolluprule->childactivityset = 'all';
                                            if (isset($sequencingrolluprule['attrs']['CHILDACTIVITYSET'])) {
                                                $rolluprule->childactivityset = $sequencingrolluprule['attrs']['CHILDACTIVITYSET'];
                                            }
                                            $rolluprule->minimumcount = 0;
                                            if (isset($sequencingrolluprule['attrs']['MINIMUMCOUNT'])) {
                                                $rolluprule->minimumcount = $sequencingrolluprule['attrs']['MINIMUMCOUNT'];
                                            }
                                            $rolluprule->minimumpercent = 0.0000;
                                            if (isset($sequencingrolluprule['attrs']['MINIMUMPERCENT'])) {
                                                $rolluprule->minimumpercent = $sequencingrolluprule['attrs']['MINIMUMPERCENT'];
                                            }
                                            if (!empty($sequencingrolluprule['children'])) {
                                                foreach ($sequencingrolluprule['children'] as $rolluproleconditions) {
                                                    if ($rolluproleconditions['name']=='IMSSS:ROLLUPCONDITIONS') {
                                                        $conditions = array();
                                                        $rolluprule->conditioncombination = 'all';
                                                        if (isset($rolluproleconditions['attrs']['CONDITIONCOMBINATION'])) {
                                                            $rolluprule->conditioncombination = $rolluproleconditions['attrs']['CONDITIONCOMBINATION'];
                                                        }
                                                        foreach ($rolluproleconditions['children'] as $rolluprulecondition) {
                                                            if ($rolluprulecondition['name']=='IMSSS:ROLLUPCONDITION') {
                                                                $condition = new stdClass();
                                                                if (isset($rolluprulecondition['attrs']['CONDITION'])) {
                                                                    $condition->cond = $rolluprulecondition['attrs']['CONDITION'];
                                                                }
                                                                $condition->operator = 'noOp';
                                                                if (isset($rolluprulecondition['attrs']['OPERATOR'])) {
                                                                    $condition->operator = $rolluprulecondition['attrs']['OPERATOR'];
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
                                            array_push($rolluprules, $rolluprule);
                                        }
                                    }
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rolluprules = $rolluprules;
                                }
                            }

                            if ($sequencing['name']=='IMSSS:SEQUENCINGRULES') {
                                if (!empty($sequencing['children'])) {
                                    $sequencingrules = array();
                                    foreach ($sequencing['children'] as $conditionrules) {
                                        $conditiontype = -1;
                                        switch($conditionrules['name']) {
                                            case 'IMSSS:PRECONDITIONRULE':
                                                $conditiontype = 0;
                                            break;
                                            case 'IMSSS:POSTCONDITIONRULE':
                                                $conditiontype = 1;
                                            break;
                                            case 'IMSSS:EXITCONDITIONRULE':
                                                $conditiontype = 2;
                                            break;
                                        }
                                        if (!empty($conditionrules['children'])) {
                                            $sequencingrule = new stdClass();
                                            foreach ($conditionrules['children'] as $conditionrule) {
                                                if ($conditionrule['name']=='IMSSS:RULECONDITIONS') {
                                                    $ruleconditions = array();
                                                    $sequencingrule->conditioncombination = 'all';
                                                    if (isset($conditionrule['attrs']['CONDITIONCOMBINATION'])) {
                                                        $sequencingrule->conditioncombination = $conditionrule['attrs']['CONDITIONCOMBINATION'];
                                                    }
                                                    foreach ($conditionrule['children'] as $rulecondition) {
                                                        if ($rulecondition['name']=='IMSSS:RULECONDITION') {
                                                            $condition = new stdClass();
                                                            if (isset($rulecondition['attrs']['CONDITION'])) {
                                                                $condition->cond = $rulecondition['attrs']['CONDITION'];
                                                            }
                                                            $condition->operator = 'noOp';
                                                            if (isset($rulecondition['attrs']['OPERATOR'])) {
                                                                $condition->operator = $rulecondition['attrs']['OPERATOR'];
                                                            }
                                                            $condition->measurethreshold = 0.0000;
                                                            if (isset($rulecondition['attrs']['MEASURETHRESHOLD'])) {
                                                                $condition->measurethreshold = $rulecondition['attrs']['MEASURETHRESHOLD'];
                                                            }
                                                            $condition->referencedobjective = '';
                                                            if (isset($rulecondition['attrs']['REFERENCEDOBJECTIVE'])) {
                                                                $condition->referencedobjective = $rulecondition['attrs']['REFERENCEDOBJECTIVE'];
                                                            }
                                                            array_push($ruleconditions,$condition);
                                                        }
                                                    }
                                                    $sequencingrule->ruleconditions = $ruleconditions;
                                                }
                                                if ($conditionrule['name']=='IMSSS:RULEACTION') {
                                                    $sequencingrule->action = $conditionrule['attrs']['ACTION'];
                                                }
                                                $sequencingrule->type = $conditiontype;
                                            }
                                            array_push($sequencingrules,$sequencingrule);
                                        }
                                    }
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->sequencingrules = $sequencingrules;
                                }
                            }
                        }
                    }
                break;
            }
        }
    }
    return $scoes;
}

function scorm_parse_scorm($pkgdir,$scormid) {
    global $CFG;

    $launch = 0;
    $manifestfile = $pkgdir.'/imsmanifest.xml';

    if (is_file($manifestfile)) {

        $xmltext = file_get_contents($manifestfile);

        $pattern = '/&(?!\w{2,6};)/';
        $replacement = '&amp;';
        $xmltext = preg_replace($pattern, $replacement, $xmltext);

        $objXML = new xml2Array();
        $manifests = $objXML->parse($xmltext);
//print_object($manifests);
        $scoes = new stdClass();
        $scoes->version = '';
        $scoes = scorm_get_manifest($manifests,$scoes);
//print_object($scoes);
        if (count($scoes->elements) > 0) {
            $olditems = get_records('scorm_scoes','scorm',$scormid);
            foreach ($scoes->elements as $manifest => $organizations) {
                foreach ($organizations as $organization => $items) {
                    foreach ($items as $identifier => $item) {
                        // This new db mngt will support all SCORM future extensions
                        $newitem = new stdClass();
                        $newitem->scorm = $scormid;
                        $newitem->manifest = $manifest;
                        $newitem->organization = $organization;
                        $standarddatas = array('parent', 'identifier', 'launch', 'scormtype', 'title');
                        foreach ($standarddatas as $standarddata) {
                            if (isset($item->$standarddata)) {
                                $newitem->$standarddata = addslashes_js($item->$standarddata);
                            }
                        }

                        // Insert the new SCO, and retain the link between the old and new for later adjustment
                        $id = insert_record('scorm_scoes',$newitem);
                        if (!empty($olditems) && ($olditemid = scorm_array_search('identifier',$newitem->identifier,$olditems))) {
                            $olditems[$olditemid]->newid = $id;
                        }

                        if ($optionaldatas = scorm_optionals_data($item,$standarddatas)) {
                            $data = new stdClass();
                            $data->scoid = $id;
                            foreach ($optionaldatas as $optionaldata) {
                                if (isset($item->$optionaldata)) {
                                    $data->name =  $optionaldata;
                                    $data->value = addslashes_js($item->$optionaldata);
                                    $dataid = insert_record('scorm_scoes_data',$data);
                                }
                            }
                        }

                        if (isset($item->sequencingrules)) {
                            foreach($item->sequencingrules as $sequencingrule) {
                                $rule = new stdClass();
                                $rule->scoid = $id;
                                $rule->ruletype = $sequencingrule->type;
                                $rule->conditioncombination = $sequencingrule->conditioncombination;
                                $rule->action = $sequencingrule->action;
                                $ruleid = insert_record('scorm_seq_ruleconds',$rule);
                                if (isset($sequencingrule->ruleconditions)) {
                                    foreach($sequencingrule->ruleconditions as $rulecondition) {
                                        $rulecond = new stdClass();
                                        $rulecond->scoid = $id;
                                        $rulecond->ruleconditionsid = $ruleid;
                                        $rulecond->referencedobjective = $rulecondition->referencedobjective;
                                        $rulecond->measurethreshold = $rulecondition->measurethreshold;
                                        $rulecond->cond = $rulecondition->cond;
                                        $rulecondid = insert_record('scorm_seq_rulecond',$rulecond);
                                    }
                                }
                            }
                        }

                        if (isset($item->rolluprules)) {
                            foreach($item->rolluprules as $rolluprule) {
                                $rollup = new stdClass();
                                $rollup->scoid =  $id;
                                $rollup->childactivityset = $rolluprule->childactivityset;
                                $rollup->minimumcount = $rolluprule->minimumcount;
                                $rollup->minimumpercent = $rolluprule->minimumpercent;
                                $rollup->rollupruleaction = $rolluprule->rollupruleaction;
                                $rollup->conditioncombination = $rolluprule->conditioncombination;

                                $rollupruleid = insert_record('scorm_seq_rolluprule',$rollup);
                                if (isset($rollup->conditions)) {
                                    foreach($rollup->conditions as $condition){
                                        $cond = new stdClass();
                                        $cond->scoid = $rollup->scoid;
                                        $cond->rollupruleid = $rollupruleid;
                                        $cond->operator = $condition->operator;
                                        $cond->cond = $condition->cond;
                                        $conditionid = insert_record('scorm_seq_rolluprulecond',$cond);
                                    }
                                }
                            }
                        }

                        if (isset($item->objectives)) {
                            foreach($item->objectives as $objective) {
                                $obj = new stdClass();
                                $obj->scoid = $id;
                                $obj->primaryobj = $objective->primaryobj;
                                $obj->satisfiedbumeasure = $objective->satisfiedbymeasure;
                                $obj->objectiveid = $objective->objectiveid;
                                $obj->minnormalizedmeasure = $objective->minnormalizedmeasure;
                                $objectiveid = insert_record('scorm_seq_objective',$obj);
                                if (isset($objective->mapinfos)) {
//print_object($objective->mapinfos);
                                    foreach($objective->mapinfos as $objmapinfo) {
                                        $mapinfo = new stdClass();
                                        $mapinfo->scoid = $id;
                                        $mapinfo->objectiveid = $objectiveid;
                                        $mapinfo->targetobjectiveid = $objmapinfo->targetobjectiveid;
                                        $mapinfo->readsatisfiedstatus = $objmapinfo->readsatisfiedstatus;
                                        $mapinfo->writesatisfiedstatus = $objmapinfo->writesatisfiedstatus;
                                        $mapinfo->readnormalizedmeasure = $objmapinfo->readnormalizedmeasure;
                                        $mapinfo->writenormalizedmeasure = $objmapinfo->writenormalizedmeasure;
                                        $mapinfoid = insert_record('scorm_seq_mapinfo',$mapinfo);
                                    }
                                }
                            }
                        }
//print_object($item);
                        if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier))) {
                            $launch = $id;
                        }
                    }
                }
            }
            if (!empty($olditems)) {
                foreach($olditems as $olditem) {
                    delete_records('scorm_scoes','id',$olditem->id);
                    delete_records('scorm_scoes_data','scoid',$olditem->id);
                    if (isset($olditem->newid)) {
                        set_field('scorm_scoes_track', 'scoid', $olditem->newid, 'scoid', $olditem->id);
                    }
                    delete_records('scorm_scoes_track','scoid',$olditem->id);
                    delete_records('scorm_seq_objective','scoid',$olditem->id);
                    delete_records('scorm_seq_mapinfo','scoid',$olditem->id);
                    delete_records('scorm_seq_ruleconds','scoid',$olditem->id);
                    delete_records('scorm_seq_rulecond','scoid',$olditem->id);
                    delete_records('scorm_seq_rolluprule','scoid',$olditem->id);
                    delete_records('scorm_seq_rolluprulecond','scoid',$olditem->id);
                }
            }
            if (empty($scoes->version)) {
                $scoes->version = 'SCORM_1.2';
            }
            set_field('scorm','version',$scoes->version,'id',$scormid);
            $scorm->version = $scoes->version;
        }
    }

    return $launch;
}

function scorm_optionals_data($item, $standarddata) {
    $result = array();
    $sequencingdata = array('sequencingrules','rolluprules','objectives');
    foreach ($item as $element => $value) {
        if (! in_array($element, $standarddata)) {
            if (! in_array($element, $sequencingdata)) {
                $result[] = $element;
            }
        }
    }
    return $result;
}

function scorm_is_leaf($sco) {
    if (get_record('scorm_scoes','scorm',$sco->scorm,'parent',$sco->identifier)) {
        return false;
    }
    return true;
}

function scorm_get_parent($sco) {
    if ($sco->parent != '/') {
        if ($parent = get_record('scorm_scoes','scorm',$sco->scorm,'identifier',$sco->parent)) {
            return scorm_get_sco($parent->id);
        }
    }
    return null;
}

function scorm_get_children($sco) {
    if ($children = get_records('scorm_scoes','scorm',$sco->scorm,'parent',$sco->identifier)) {//originally this said parent instead of childrean
        return $children;
    }
    return null;
}

function scorm_get_available_children($sco){
    $res = get_record('scorm_scoes_track','scoid',$scoid,'userid',$userid,'element','availablechildren');
    if(!$res || $res == null){
        return false;
    }
    else{
        return unserialize($res->value);
    }
}

function scorm_get_available_descendent($descend = array(),$sco){
    if($sco == null){
        return $descend;
    }
    else{
        $avchildren = scorm_get_available_children($sco);
        foreach($avchildren as $avchild){
            array_push($descend,$avchild);
        }
        foreach($avchildren as $avchild){
            scorm_get_available_descendent($descend,$avchild);
        }
    }
}

function scorm_get_siblings($sco) {
    if ($siblings = get_records('scorm_scoes','scorm',$sco->scorm,'parent',$sco->parent)) {
        unset($siblings[$sco->id]);
        if (!empty($siblings)) {
            return $siblings;
        }
    }
    return null;
}

function scorm_get_ancestors($sco) {
    if ($sco->parent != '/') {
        return array_push(scorm_get_ancestors(scorm_get_parent($sco)));
    } else {
        return $sco;
    }
}

function scorm_get_preorder($preorder=array(),$sco) {


    if ($sco != null) {
        array_push($preorder,$sco);
        $children = scorm_get_children($sco);
        foreach ($children as $child){
            scorm_get_preorder($sco);
        }
    } else {
        return $preorder;
    }
}

function scorm_find_common_ancestor($ancestors, $sco) {
    $pos = scorm_array_search('identifier',$sco->parent,$ancestors);
    if ($sco->parent != '/') {
        if ($pos === false) {
            return scorm_find_common_ancestor($ancestors,scorm_get_parent($sco));
        }
    }
    return $pos;
}

/* Usage
 Grab some XML data, either from a file, URL, etc. however you want. Assume storage in $strYourXML;

 $objXML = new xml2Array();
 $arrOutput = $objXML->parse($strYourXML);
 print_r($arrOutput); //print it out, or do whatever!

*/
class xml2Array {

   var $arrOutput = array();
   var $resParser;
   var $strXmlData;

   /**
   * Convert a utf-8 string to html entities
   *
   * @param string $str The UTF-8 string
   * @return string
   */
   function utf8_to_entities($str) {
       global $CFG;

       $entities = '';
       $values = array();
       $lookingfor = 1;

       return $str;
   }

   /**
   * Parse an XML text string and create an array tree that rapresent the XML structure
   *
   * @param string $strInputXML The XML string
   * @return array
   */
   function parse($strInputXML) {
           $this->resParser = xml_parser_create ('UTF-8');
           xml_set_object($this->resParser,$this);
           xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

           xml_set_character_data_handler($this->resParser, "tagData");

           $this->strXmlData = xml_parse($this->resParser,$strInputXML );
           if(!$this->strXmlData) {
               die(sprintf("XML error: %s at line %d",
                           xml_error_string(xml_get_error_code($this->resParser)),
                           xml_get_current_line_number($this->resParser)));
           }

           xml_parser_free($this->resParser);

           return $this->arrOutput;
   }

   function tagOpen($parser, $name, $attrs) {
       $tag=array("name"=>$name,"attrs"=>$attrs);
       array_push($this->arrOutput,$tag);
   }

   function tagData($parser, $tagData) {
       if(trim($tagData)) {
           if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $this->utf8_to_entities($tagData);
           } else {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $this->utf8_to_entities($tagData);
           }
       }
   }

   function tagClosed($parser, $name) {
       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
       array_pop($this->arrOutput);
   }

}

?>