
<?php  // $Id$
define("VALUESCOES",0);
define("VALUEHIGHEST",1);
define("VALUEAVERAGE",2);
define("VALUESUM",3);

/// Local Library of functions and constants for module scorm

/**
* Create a new temporary subdirectory with a random name in the given path
*
* @param string $strpath The scorm data directory
* @return string/boolean
*/
function scorm_datadir($strPath)
{
    global $CFG;

    if (is_dir($strPath)) {
        do {
            // Create a random string of 8 chars
            $randstring = NULL;
            $lchar = '';
            $len = 8;
            for ($i=0; $i<$len; $i++) {
                $char = chr(rand(48,122));
                while (!ereg('[a-zA-Z0-9]', $char)){
                    if ($char == $lchar) continue;
                        $char = chr(rand(48,90));
                    }
                    $randstring .= $char;
                    $lchar = $char;
            } 
            $datadir='/'.$randstring;
        } while (file_exists($strPath.$datadir));
        mkdir($strPath.$datadir, $CFG->directorypermissions);
        @chmod($strPath.$datadir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        return $strPath.$datadir;
    } else {
        return false;
    }
}

/**
* Given a package directory, this function will check if the package is valid
*
* @param string $packagedir The package directory
* @return mixed
*/
function scorm_validate($packagedir) {

    ////$f = "D:\\test.txt";
    ////@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly trong ham scorm_validate \n");    


    $validation = new stdClass();
    if (is_file($packagedir.'/imsmanifest.xml')) {
        $validation->result = 'found';
        $validation->pkgtype = 'SCORM';
    } else {
        if ($handle = opendir($packagedir)) {
            while (($file = readdir($handle)) !== false) {
                $ext = substr($file,strrpos($file,'.'));
                if (strtolower($ext) == '.cst') {
                    $validation->result = 'found';
                    $validation->pkgtype = 'AICC';
                    break;
                }
            }
            closedir($handle);
        }
        if (!isset($validation)) {
            $validation->result = 'nomanifest';
            $validation->pkgtype = 'SCORM';
        }
    }
    return $validation;
}

function scorm_get_user_data($userid) {
/// Gets user info required to display the table of scorm results
/// for report.php

    return get_record('user','id',$userid,'','','','','firstname, lastname, picture');
}

function scorm_string_wrap($stringa, $len=15) {
// Crop the given string into max $len characters lines
    $textlib = textlib_get_instance();
    if ($textlib->strlen($stringa, current_charset()) > $len) {
        $words = explode(' ', $stringa);
        $newstring = '';
        $substring = '';
        foreach ($words as $word) {
           if (($textlib->strlen($substring, current_charset())+$textlib->strlen($word, current_charset())+1) < $len) {
               $substring .= ' '.$word;
           } else {
               $newstring .= ' '.$substring.'<br />';
               $substring = $word;
           }
        }
        if (!empty($substring)) {
            $newstring .= ' '.$substring;
        }
        return $newstring;
    } else {
        return $stringa;
    }
}

function scorm_eval_prerequisites($prerequisites,$usertracks) {

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly trong ham scorm_eval_prerequisites \n");    


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
    while ($i<strlen($prerequisites)) {
        $symbol = $prerequisites[$i];
        switch ($symbol) {
            case '&':
            case '|':
                $symbol .= $symbol;
            case '~':
            case '(':
            case ')':
            case '*':
            //case '{':
            //case '}':
            //case ',':
                $element = trim($element);
                
                if (!empty($element)) {
                    $element = trim($element);
                    if (isset($usertracks[$element])) {
                        $element = '((\''.$usertracks[$element]->status.'\' == \'completed\') || '.
                                  '(\''.$usertracks[$element]->status.'\' == \'passed\'))'; 
                    } else if (($operator = strpos($element,'=')) !== false) {
                        $item = trim(substr($element,0,$operator));
                        if (!isset($usertracks[$item])) {
                            return false;
                        }
                        
                        $value = trim(trim(substr($element,$operator+1)),'"');
                        if (isset($statuses[$value])) {
                            $status = $statuses[$value];
                        } else {
                            return false;
                        }
                                              
                        $element = '(\''.$usertracks[$item]->status.'\' == \''.$status.'\')';
                    } else if (($operator = strpos($element,'<>')) !== false) {
                        $item = trim(substr($element,0,$operator));
                        if (!isset($usertracks[$item])) {
                            return false;
                        }
                        
                        $value = trim(trim(substr($element,$operator+2)),'"');
                        if (isset($statuses[$value])) {
                            $status = $statuses[$value];
                        } else {
                            return false;
                        }
                        
                        $element = '(\''.$usertracks[$item]->status.'\' != \''.$status.'\')';
                    } else if (is_numeric($element)) {
                        if ($symbol == '*') {
                            $symbol = '';
                            $open = strpos($prerequisites,'{',$i);
                            $opened = 1;
                            $closed = 0;
                            for ($close=$open+1; (($opened > $closed) && ($close<strlen($prerequisites))); $close++) { 
                                 if ($prerequisites[$close] == '}') {
                                     $closed++;
                                 } else if ($prerequisites[$close] == '{') {
                                     $opened++;
                                 }
                            } 
                            $i = $close;
                            
                            $setelements = explode(',', substr($prerequisites, $open+1, $close-($open+1)-1));
                            $settrue = 0;
                            foreach ($setelements as $setelement) {
                                if (scorm_eval_prerequisites($setelement,$usertracks)) {
                                    $settrue++;
                                }
                            }
                            
                            if ($settrue >= $element) {
                                $element = 'true'; 
                            } else {
                                $element = 'false';
                            }
                        }
                    } else {
                        return false;
                    }
                    
                    array_push($stack,$element);
                    $element = '';
                }
                if ($symbol == '~') {
                    $symbol = '!';
                }
                if (!empty($symbol)) {
                    array_push($stack,$symbol);
                }
            break;
            default:
                $element .= $symbol;
            break;
        }
        $i++;
    }
    if (!empty($element)) {
        $element = trim($element);
        if (isset($usertracks[$element])) {
            $element = '((\''.$usertracks[$element]->status.'\' == \'completed\') || '.
                       '(\''.$usertracks[$element]->status.'\' == \'passed\'))'; 
        } else if (($operator = strpos($element,'=')) !== false) {
            $item = trim(substr($element,0,$operator));
            if (!isset($usertracks[$item])) {
                return false;
            }
            
            $value = trim(trim(substr($element,$operator+1)),'"');
            if (isset($statuses[$value])) {
                $status = $statuses[$value];
            } else {
                return false;
            }
            
            $element = '(\''.$usertracks[$item]->status.'\' == \''.$status.'\')';
        } else if (($operator = strpos($element,'<>')) !== false) {
            $item = trim(substr($element,0,$operator));
            if (!isset($usertracks[$item])) {
                return false;
            }
            
            $value = trim(trim(substr($element,$operator+1)),'"');
            if (isset($statuses[$value])) {
                $status = $statuses[$value];
            } else {
                return false;
            }
            
            $element = '(\''.$usertracks[$item]->status.'\' != \''.trim($status).'\')';
        } else {
            return false;
        }
        
        array_push($stack,$element);
    }
    return eval('return '.implode($stack).';');
}

function scorm_insert_statistic($statisticInput){

    $id = null;
    if ($statistic = get_record_select('scorm_statistic',"userid='$statisticInput->userid' AND scormid='$statisticInput->scormid'")) {

        $statistic->durationtime = $statisticInput->duration;
        $statistic->accesstime = $statisticInput->accesstime;        
        $statistic->status = $statisticInput->status;        
        $statistic->attemptnumber = $statisticInput->attemptnumber;        
        $id = update_record('scorm_statistic',$statistic);
    } else {
        ////fwrite($ft,"Insert trong ham scorm_insert_track \n");    
        $id = insert_record('scorm_statistic',$statisticInput);
    }
    return $id;

}
function scorm_insert_track($userid,$scormid,$scoid,$attempt,$element,$value) {

//    //$f = "D:\\test.txt";
//    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly trong ham scorm_insert_track \n");    

    $id = null;
    if ($track = get_record_select('scorm_scoes_track',"userid='$userid' AND scormid='$scormid' AND scoid='$scoid' AND attempt='$attempt' AND element='$element'")) {
        $track->value = $value;
        $track->timemodified = time();
        ////fwrite($ft,$userid."Update trong ham scorm_insert_track voi cac gia tri userid = ");    
        $id = update_record('scorm_scoes_track',$track);
    } else {
        $track->userid = $userid;
        $track->scormid = $scormid;
        $track->scoid = $scoid;
        $track->attempt = $attempt;
        $track->element = $element;
        $track->value = addslashes($value);
        $track->timemodified = time();
        ////fwrite($ft,"Insert trong ham scorm_insert_track \n");    
        $id = insert_record('scorm_scoes_track',$track);
    }
    return $id;
}

function scorm_insert_trackmodel($userid,$scormid,$scoid,$attempt) {

//    //$f = "D:\\test.txt";
//    //@$ft = fopen($f,"a");

    $id = null;
    if ($suspendtrack = get_record_select('scorm_suspendtrack',"userid='$userid' AND scormid='$scormid'")) {
        $suspendtrack->suspendscoid = $scoid;
        $suspendtrack->attempt = $attempt;

        $id = update_record('scorm_suspendtrack',$suspendtrack);
    } else {
        $suspendtrack->scormid = $scormid;
        $suspendtrack->suspendscoid = $scoid;
        $suspendtrack->userid = $userid;
        $suspendtrack->attempt = $attempt;
        $id = insert_record('scorm_suspendtrack',$suspendtrack);
    }
    return $id;
}

function scorm_get_suspendscoid($scormid,$userid)
{
        $sco = get_record("scorm_suspendtrack","scormid",$scormid,"userid",$userid);
        $suspendscoid = $sco->suspendscoid;
        return $suspendscoid;
}
function scorm_add_time($a, $b) {
    $aes = explode(':',$a);
    $bes = explode(':',$b);
    $aseconds = explode('.',$aes[2]);
    $bseconds = explode('.',$bes[2]);
    $change = 0;

    $acents = 0;  //Cents
    if (count($aseconds) > 1) {
        $acents = $aseconds[1];
    }
    $bcents = 0;
    if (count($bseconds) > 1) {
        $bcents = $bseconds[1];
    }
    $cents = $acents + $bcents;
    $change = floor($cents / 100);
    $cents = $cents - ($change * 100);
    if (floor($cents) < 10) {
        $cents = '0'. $cents;
    }

    $secs = $aseconds[0] + $bseconds[0] + $change;  //Seconds
    $change = floor($secs / 60);
    $secs = $secs - ($change * 60);
    if (floor($secs) < 10) {
        $secs = '0'. $secs;
    }

    $mins = $aes[1] + $bes[1] + $change;   //Minutes
    $change = floor($mins / 60);
    $mins = $mins - ($change * 60);
    if ($mins < 10) {
        $mins = '0' .  $mins;
    }

    $hours = $aes[0] + $bes[0] + $change;  //Hours
    if ($hours < 10) {
        $hours = '0' . $hours;
    }

    if ($cents != '0') {
        return $hours . ":" . $mins . ":" . $secs . '.' . $cents;
    } else {
        return $hours . ":" . $mins . ":" . $secs;
    }
}

function scorm_external_link($link) {
// check if a link is external
    $result = false;
    $link = strtolower($link);
    if (substr($link,0,7) == 'http://') {
        $result = true;
    } else if (substr($link,0,8) == 'https://') {
        $result = true;
    } else if (substr($link,0,4) == 'www.') {
        $result = true;
    }
    return $result;
}

function scorm_grade_user($scoes, $userid, $grademethod=VALUESCOES) {

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    //fwrite($ft,"\n Xu ly trong ham scorm_grade_user \n");    

    $scores = NULL; 
    $scores->scoes = 0;
    $scores->values = 0;
    $scores->max = 0;
    $scores->sum = 0;

    if (!$scoes) {
        return '';
    }

    $current = current($scoes);
    $attempt = scorm_get_last_attempt($current->scorm, $userid);
    foreach ($scoes as $sco) { 
        if ($userdata=scorm_get_tracks($sco->id, $userid,$attempt)) {
            if (($userdata->status == 'completed') || ($userdata->status == 'passed')) {
                $scores->scoes++;
            }       
            if (!empty($userdata->score_raw)) {
                $scores->values++;
                $scores->sum += $userdata->score_raw;
                $scores->max = ($userdata->score_raw > $scores->max)?$userdata->score_raw:$scores->max;
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
            return $scores->scoes;
        break;  
    }
}

//Lay diem theo Sco cha.. Thuc chat la theo bai kiem tra
function scorm_get_score_from_parent($sco,$userid,$grademethod=VALUESCOES)
{
    
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
    if ($scoes_count > 0)
    {
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

// Lay ra so luong cac scoes duoc user thuc hien xong
function scorm_get_user_sco_count($scormid, $userid)
{
    $scoes_count = 0;
    $attempt = scorm_get_last_attempt($current->scorm, $userid);
    $scoes = get_records('scorm_scoes', 'scorm', $scormid);



    foreach ($scoes as $sco)
    {
           if ($userdata=scorm_get_tracks($sco->id, $userid,$attempt)) {

               if (($userdata->status == 'completed') || ($userdata->success_status == 'passed')) {
                    $scoes_count++;
               }
           }

    }
    return $scoes_count;
    
}

function scorm_grade_user_new($scoes, $userid, $grademethod=VALUESCOES) {

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    //fwrite($ft,"\n Xu ly trong ham scorm_grade_user \n");    

    $scores = NULL; 
    $scores->scoes = 0;
    $scores->values = 0;
    $scores->scaled = 0;
    $scores->max = 0;
    $scores->sum = 0;

    if (!$scoes) {
        //fwrite($ft,"\n Khong xuat hien mot SCO duoc tinh diem \n");    
        return '';
    }

    $current = current($scoes);
    $attempt = scorm_get_last_attempt($current->scorm, $userid);
    //fwrite($ft,"\n ---------------------------------------\n");    
    foreach ($scoes as $sco) { 
        if ($userdata=scorm_get_tracks($sco->id, $userid,$attempt)) {
            if (($userdata->status == 'completed') || ($userdata->success_status == 'passed')) {
                $scores->scoes++;
                //fwrite($ft,"\n Them mot khoa hoan thanh co id la ".$sco->id." co gia tri scaled la ".$userdata->score_scaled." \n");    
        
            }  
            $scaled = $userdata->score_scaled;
            $scoreraw = $userdata->score_raw; 
            if ($scaled ==0){
                $scores->scaled = $scores->scaled / $scores->scoes;
                //fwrite($ft,"\n Ti le chinh xac ".($scores->scaled*100)." phan tram");    

            }

            if (!empty($userdata->score_raw)) {
                $scores->values++;
                $scores->sum += $userdata->score_raw;
                $scores->max = ($userdata->score_raw > $scores->max)?$userdata->score_raw:$scores->max;
            }  
                        
            if (!empty($scaled)) {
                //fwrite($ft,"\n go ---->>> \n");    
                $scores->scaled = (($scores->scaled) * ($scores->scoes-1) + $scaled)/($scores->scoes);
                //fwrite($ft,"\n Ti le chinh xac ".($scores->scaled*100)." phan tram");    

            }       
            
        }       
    }
    //fwrite($ft,"\n ----+++++++++++------\n");    
    //fwrite($ft,"\n Kieu gia tri tra ve la  ".$grademethod);    
    switch ($grademethod) {
        case VALUEHIGHEST:
            //fwrite($ft,"\n Gia tri tra ve thouoc loai cao nhat");    
            return $scores->max;
        break;  
        case VALUEAVERAGE:
            //fwrite($ft,"\n Gia tri tra ve thouoc loai trung binh");    
            if ($scores->values > 0) {
                return $scores->sum/$scores->values;
            } else {
                return 0;
            }       
        break;  
        case VALUESUM:
            //fwrite($ft,"\n Gia tri tra ve thouoc loai tong cong");    
            return $scores->sum;
        break;  
        case VALUESCOES:
            //fwrite($ft,"\n Gia tri tra ve thouoc loai scoes co gia tri".$scores->scaled);    
            return $scores->scaled;
        break;  
    }
}

function scorm_count_launchable($scormid,$organization) {
    return count_records_select('scorm_scoes',"scorm=$scormid AND organization='$organization' AND launch<>''");
}

function scorm_get_toc($user,$scorm,$liststyle,$currentorg='',$scoid='',$mode='normal',$attempt='',$play=false) {
    global $CFG;

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    //fwrite($ft,"\n Xu ly trong ham scorm_get_toc \n");    

    //
    $suspendscoid = scorm_get_suspendscoid($scorm->id,$user->id);
    //

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

    //fwrite($ft,"\n So lan attempt con lai la \n".$result->attemptleft);    
    
    if ($scoes = get_records_select('scorm_scoes',"scorm='$scorm->id' $organizationsql order by id ASC")){
        //
        // Lay du lieu da duoc tracking cho moi doi tuong hoc tap
        // 
        $usertracks = array();
        foreach ($scoes as $sco) {
            //Kiem tra xem $sco co phai la phan muc khong. Neu la trang Asset hoac SCO thi xu ly tiep
            if (!empty($sco->launch)) {
                if ($usertrack=scorm_get_tracks($sco->id,$user->id,$attempt)) {
                    if ($usertrack->status == '') {
                        $usertrack->status = 'notattempted';
                    }
                    // Ghi lai thong tin $usertracks theo tung doi tuong sco
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
                //Neu la sco suspend thi hien thi anh khac
                if ($suspendscoid == $sco->id){
                    $result->toc .= '<img src="'.$scormpixdir.'/suspend.gif" alt="Dang tam dung o day" title="Dang dung o day" />';                
                }
                else{
                //-----------------------
                    if (isset($usertracks[$sco->identifier])) {
                        $usertrack = $usertracks[$sco->identifier];
                        $strstatus = get_string($usertrack->status,'scorm');
                        $result->toc .= '<img src="'.$scormpixdir.'/'.$usertrack->status.'.gif" alt="'.$strstatus.'" title="'.$strstatus.'" />';
                        
                        if (($usertrack->status == 'notattempted') || ($usertrack->status == 'incomplete') || ($usertrack->status == 'browsed')) {
                            //Neu khoa hoc chua duoc attempted hoac chua hoan thanh hoac la chi browsed
                            $incomplete = true;
                            if ($play && empty($scoid)) {
                                $scoid = $sco->id;
                            }
                        }
                        if ($usertrack->score_raw != '') {
                            $score = '('.get_string('score','scorm').':&nbsp;'.$usertrack->score_raw.')';
                        }
                    } else {
                        if ($play && empty($scoid)) {
                            $scoid = $sco->id;
                        }
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= '<img src="'.$scormpixdir.'/notattempted.gif" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                            $incomplete = true;
                        } else {
                            $result->toc .= '<img src="'.$scormpixdir.'/asset.gif" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
                        }
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
                    if (scorm_isChoice($scorm->id,$sco->id) == 1)
                    {
                    $url = $CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;currentorg='.$currentorg.$modestr.'&amp;scoid='.$sco->id;
                    $result->toc .= '&nbsp;'.$startbold.'<a href="'.$url.'">'.format_string($sco->title).'</a>'.$score.$endbold."</li>\n";
                    $tocmenus[$sco->id] = scorm_repeater('&minus;',$level) . '&gt;' . format_string($sco->title);
                    }
                    else
                    {
                    $result->toc .= '&nbsp;'.$startbold.format_string($sco->title).$score.$endbold."</li>\n";
                    $tocmenus[$sco->id] = scorm_repeater('&minus;',$level) . '&gt;' . format_string($sco->title);                    
                    }
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

function scorm_get_last_attempt($scormid, $userid) {

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly trong ham scorm_get_last_attempt \n");    

/// Find the last attempt number for the given user id and scorm id
    if ($lastattempt = get_record('scorm_scoes_track', 'userid', $userid, 'scormid', $scormid, '', '', 'max(attempt) as a')) {
        if (empty($lastattempt->a)) {
            return '1';
        } else {
            return $lastattempt->a;
        }
    }
}

// Khi mot nguoi truy nhap vao mot SCO thi se thiet lap
// nguoi do da no luc thuc hien no
function scorm_set_attempt($scoid,$userid)
{
    //Lay gia tri last attempt
    if ($scormid = get_field('scorm_scoes','scorm','id',$scoid)) {
        $attempt = scorm_get_last_attempt($scormid,$userid);
    } else {
        $attempt = 1;
    }
    //Chi set attempt cho cac SCO
    $scormtype = get_field('scorm_scoes','scormtype','id',$scoid) ;
    if ($scormtype == 'sco'){
        $element = 'cmi.attempt_status';
        $value = 'attempted';
        scorm_insert_track($userid,$scormid,$scoid,$attempt,$element,$value);
    }
}
function scorm_get_tracks($scoid,$userid,$attempt='') {

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly trong ham scorm_get_tracks \n");    

/// Gets all tracks of specified sco and user
    global $CFG;

    if (empty($attempt)) {
        if ($scormid = get_field('scorm_scoes','scorm','id',$scoid)) {
            $attempt = scorm_get_last_attempt($scormid,$userid);
        } else {
            $attempt = 1;
        }
    }
    $attemptsql = ' AND attempt=' . $attempt;
    if ($tracks = get_records_select('scorm_scoes_track',"userid=$userid AND scoid=$scoid".$attemptsql,'element ASC')) {
        $usertrack->userid = $userid;
        $usertrack->scoid = $scoid; 
        $usertrack->score_raw = '';
        $usertrack->score_scaled = '';
        $usertrack->status = '';
        $usertrack->success_status = '';
        $usertrack->attempt_status = '';
        $usertrack->satisfied_status = '';
        $usertrack->total_time = '00:00:00';
        $usertrack->session_time = '00:00:00';
        $usertrack->timemodified = 0;
        foreach ($tracks as $track) {
            $element = $track->element;
            $usertrack->{$element} = $track->value;
            switch ($element) {
                case 'cmi.core.lesson_status':
                case 'cmi.attempt_status':
                    $usertrack->status = $track->value;
                    $usertrack->attempt_status = $track->value;                    
                break;                
                case 'cmi.completion_status':
                    if ($track->value == 'not attempted') {
                        $track->value = 'notattempted';
                        $usertrack->attempt_status = $track->value;
                    }       
                    $usertrack->status = $track->value;
                break;  
                case 'cmi.success_status':
                    $usertrack->success_status = $track->value;
                    if ($track->value=='passed'){
                        $usertrack->satisfied_status = 'satisfied';                    
                    }
                    if ($track->value=='failed'){
                        $usertrack->satisfied_status = 'notSatisfied';                    
                    }                    
                break;
                case 'cmi.core.score.raw':
                case 'cmi.score.raw':
                    $usertrack->score_raw = $track->value;
                break;  
                case 'cmi.score.scaled':
                    $usertrack->score_scaled = $track->value;
                break;  
                case 'cmi.core.session_time':
                case 'cmi.session_time':
                    $usertrack->session_time = $track->value;
                break;  
                case 'cmi.core.total_time':
                case 'cmi.total_time':
                    $usertrack->total_time = $track->value;
                break;  
            }       
            if (isset($track->timemodified) && ($track->timemodified > $usertrack->timemodified)) {
                $usertrack->timemodified = $track->timemodified;
            }       
        }       
        return $usertrack;
    } else {
        return false;
    }
}


function scorm_get_AbsoluteTimeLimit($scoid){
    $sco = get_record("scorm_scoes","id",$scoid);
    if (!empty($sco)){
        return $sco->attemptAbsoluteDurationLimit;
    }
    return 0;
}
//-----------------------------------------------------
/// Library of functions and constants for parsing packages

function scorm_parse($scorm) {
    global $CFG;

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly doc thong tin trong ham scorm_parse \n");

    // Parse scorm manifest
    if ($scorm->pkgtype == 'AICC') {
        $scorm->launch = scorm_parse_aicc($scorm->dir.'/'.$scorm->id,$scorm->id);
    } else {
        if (basename($scorm->reference) != 'imsmanifest.xml') {
            $scorm->launch = scorm_parse_scorm($scorm->dir.'/'.$scorm->id,$scorm->id);
        } else {
            $scorm->launch = scorm_parse_scorm($CFG->dataroot.'/'.$scorm->course.'/'.dirname($scorm->reference),$scorm->id);
        }
    }

    return $scorm->launch;
}

/**
* Take the header row of an AICC definition file
* and returns sequence of columns and a pointer to
* the sco identifier column.
*
* @param string $row AICC header row
* @param string $mastername AICC sco identifier column
* @return mixed
*/
function scorm_get_aicc_columns($row,$mastername='system_id') {
    $tok = strtok(strtolower($row),"\",\n\r");
    $result->columns = array();
    $i=0;
    while ($tok) {
        if ($tok !='') {
            $result->columns[] = $tok;
            if ($tok == $mastername) {
                $result->mastercol = $i;
            }
            $i++;
        }
        $tok = strtok("\",\n\r");
    }
    return $result;
}

/**
* Given a colums array return a string containing the regular
* expression to match the columns in a text row.
*
* @param array $column The header columns
* @param string $remodule The regular expression module for a single column
* @return string
*/
function scorm_forge_cols_regexp($columns,$remodule='(".*")?,') {
    $regexp = '/^';
    foreach ($columns as $column) {
        $regexp .= $remodule;
    }
    $regexp = substr($regexp,0,-1) . '/';
    return $regexp;
}

function scorm_parse_aicc($pkgdir,$scormid){
    
    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly doc thong tin trong ham scorm_parse_aicc \n");    
    
    $version = 'AICC';
    $ids = array();
    $courses = array();
    if ($handle = opendir($pkgdir)) {
        while (($file = readdir($handle)) !== false) {
            $ext = substr($file,strrpos($file,'.'));
            $extension = strtolower(substr($ext,1));
            $id = strtolower(basename($file,$ext));
            $ids[$id]->$extension = $file;
        }
        closedir($handle);
    }
    foreach ($ids as $courseid => $id) {
        if (isset($id->crs)) {
            if (is_file($pkgdir.'/'.$id->crs)) {
                $rows = file($pkgdir.'/'.$id->crs);
                foreach ($rows as $row) {
                    if (preg_match("/^(.+)=(.+)$/",$row,$matches)) {
                        switch (strtolower(trim($matches[1]))) {
                            case 'course_id':
                                $courses[$courseid]->id = trim($matches[2]);
                            break;
                            case 'course_title':
                                $courses[$courseid]->title = trim($matches[2]);
                            break;
                            case 'version':
                                $courses[$courseid]->version = 'AICC_'.trim($matches[2]);
                            break;
                        }
                    }
                }
            }
        }
        if (isset($id->des)) {
            $rows = file($pkgdir.'/'.$id->des);
            $columns = scorm_get_aicc_columns($rows[0]);
            $regexp = scorm_forge_cols_regexp($columns->columns);
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    for ($j=0;$j<count($columns->columns);$j++) {
                        $column = $columns->columns[$j];
                        $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]),1,-1)]->$column = substr(trim($matches[$j+1]),1,-1);
                    }
                }
            }
        }
        if (isset($id->au)) {
            $rows = file($pkgdir.'/'.$id->au);
            $columns = scorm_get_aicc_columns($rows[0]);
            $regexp = scorm_forge_cols_regexp($columns->columns);
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    for ($j=0;$j<count($columns->columns);$j++) {
                        $column = $columns->columns[$j];
                        $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]),1,-1)]->$column = substr(trim($matches[$j+1]),1,-1);
                    }
                }
            }
        }
        if (isset($id->cst)) {
            $rows = file($pkgdir.'/'.$id->cst);
            $columns = scorm_get_aicc_columns($rows[0],'block');
            $regexp = scorm_forge_cols_regexp($columns->columns,'(.+)?,');
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    for ($j=0;$j<count($columns->columns);$j++) {
                        if ($j != $columns->mastercol) {
                            $courses[$courseid]->elements[substr(trim($matches[$j+1]),1,-1)]->parent = substr(trim($matches[$columns->mastercol+1]),1,-1);
                        }
                    }
                }
            }
        }
        if (isset($id->ort)) {
            $rows = file($pkgdir.'/'.$id->ort);
        }
        if (isset($id->pre)) {
            $rows = file($pkgdir.'/'.$id->pre);
            $columns = scorm_get_aicc_columns($rows[0],'structure_element');
            $regexp = scorm_forge_cols_regexp($columns->columns,'(.+),');
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    $courses[$courseid]->elements[$columns->mastercol+1]->prerequisites = substr(trim($matches[1-$columns->mastercol+1]),1,-1);
                }
            }
        }
        if (isset($id->cmp)) {
            $rows = file($pkgdir.'/'.$id->cmp);
        }
    }
    //print_r($courses);
    $launch = 0;
    if (isset($courses)) {
        foreach ($courses as $course) {
            unset($sco);
            $sco->identifier = $course->id;
            $sco->scorm = $scormid;
            $sco->organization = '';
            $sco->title = $course->title;
            $sco->parent = '/';
            $sco->launch = '';
            $sco->scormtype = '';
            //print_r($sco);
            $id = insert_record('scorm_scoes',$sco);
            if ($launch == 0) {
                $launch = $id;
            }
            if (isset($course->elements)) {
                foreach($course->elements as $element) {
                    unset($sco);
                    $sco->identifier = $element->system_id;
                    $sco->scorm = $scormid;
                    $sco->organization = $course->id;
                    $sco->title = $element->title;
                    if (strtolower($element->parent) == 'root') {
                        $sco->parent = '/';
                    } else {
                        $sco->parent = $element->parent;
                    }
                    if (isset($element->file_name)) {
                        $sco->launch = $element->file_name;
                        $sco->scormtype = 'sco';
                    } else {
                        $element->file_name = '';
                        $sco->scormtype = '';
                    }
                    if (!isset($element->prerequisites)) {
                        $element->prerequisites = '';
                    }
                    $sco->prerequisites = $element->prerequisites;
                    if (!isset($element->max_time_allowed)) {
                        $element->max_time_allowed = '';
                    }
                    $sco->maxtimeallowed = $element->max_time_allowed;
                    if (!isset($element->time_limit_action)) {
                        $element->time_limit_action = '';
                    }
                    $sco->timelimitaction = $element->time_limit_action;
                    if (!isset($element->mastery_score)) {
                        $element->mastery_score = '';
                    }
                    $sco->masteryscore = $element->mastery_score;
                    $sco->previous = 0;
                    $sco->next = 0;
                    $id = insert_record('scorm_scoes',$sco);
                    if ($launch==0) {
                        $launch = $id;
                    }
                }
            }
        }
    }
    set_field('scorm','version','AICC','id',$scormid);
    return $launch;
}

function scorm_get_resources($blocks) {

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly trong ham scorm_get_resources \n");    

    foreach ($blocks as $block) {
        if ($block['name'] == 'RESOURCES') {
            foreach ($block['children'] as $resource) {
                if ($resource['name'] == 'RESOURCE') {
                    $resources[addslashes($resource['attrs']['IDENTIFIER'])] = $resource['attrs'];
                }
            }
        }
    }
    return $resources;
}

function scorm_get_manifest($blocks,$scoes) {

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n Xu ly doc thong tin trong ham scorm_get_manifest.  \n");    
    ////////fwrite($ft,$blocks." la  gia tri block la  \n");    

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
                                        $scoes->version = 'SCORM_1.2';
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'MANIFEST':
                    $manifest = addslashes($block['attrs']['IDENTIFIER']); //Lay thuoc tinh IDENTFIER cua MANIFEST
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
                                $sco->launch = addslashes($resource['HREF']);
                                $sco->scormtype = addslashes($resource['ADLCP:SCORMTYPE']);
                                $scoes->elements[$manifest][$organization][$item] = $sco;
                            }
                        }
                    }
                break;
                case 'ORGANIZATIONS':
                    if (!isset($scoes->defaultorg)) {
                        $scoes->defaultorg = addslashes($block['attrs']['DEFAULT']);
                    }
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                break;
                case 'ORGANIZATION':
                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
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

                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = $parent->identifier;
                    if (!isset($block['attrs']['ISVISIBLE'])) {
                        $block['attrs']['ISVISIBLE'] = 'true';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->isvisible = addslashes($block['attrs']['ISVISIBLE']);
                    if (!isset($block['attrs']['PARAMETERS'])) {
                        $block['attrs']['PARAMETERS'] = '';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->parameters = addslashes($block['attrs']['PARAMETERS']);
                    if (!isset($block['attrs']['IDENTIFIERREF'])) {
                        $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = 'asset';
                    } else {
                        $idref = addslashes($block['attrs']['IDENTIFIERREF']);
                        $base = '';
                        if (isset($resources[$idref]['XML:BASE'])) {
                            $base = $resources[$idref]['XML:BASE'];
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->launch = addslashes($base.$resources[$idref]['HREF']);
                        if (empty($resources[$idref]['ADLCP:SCORMTYPE'])) {
                            $resources[$idref]['ADLCP:SCORMTYPE'] = 'asset';
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = addslashes($resources[$idref]['ADLCP:SCORMTYPE']);
                    }
                    
                    //////fwrite($ft,"---Dang lam viec voi ITEM co Identifier = ".$identifier);
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
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = addslashes($block['tagData']);
                break;
                case 'ADLCP:PREREQUISITES':
                    if ($block['attrs']['TYPE'] == 'aicc_script') {
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = addslashes($block['tagData']);
                    }
                break;
                case 'ADLCP:MAXTIMEALLOWED':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = addslashes($block['tagData']);
                break;
                case 'ADLCP:TIMELIMITACTION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = addslashes($block['tagData']);
                break;
                case 'ADLCP:DATAFROMLMS':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = addslashes($block['tagData']);
                break;
                case 'ADLCP:MASTERYSCORE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = addslashes($block['tagData']);
                break;
                case 'ADLNAV:PRESENTATION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    foreach ($block['children'] as $adlnav) {
                        if ($adlnav['name'] == 'ADLNAV:NAVIGATIONINTERFACE') {                                    //////fwrite($ft,$scoes->elements[$manifest][$parent->organization][$parent->identifier]->title."  Xuat hien dieu khien NAV \n");
                            foreach ($adlnav['children'] as $adlnavInterface){
                                if ($adlnavInterface['name'] == 'ADLNAV:HIDELMSUI'){
                                //////fwrite($ft,$scoes->elements[$manifest][$parent->organization][$parent->identifier]->title."  Xuat hien dieu khien NAV HIDELMSUI\n");
                                //////fwrite($ft," Gia tri thuoc tinh an la ".$adlnavInterface['tagData']);
                                    if ($adlnavInterface['tagData'] == 'continue')    {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->next = 1; 
//                                    //////fwrite($ft," Thiet lap thuoc tinh an OK ");
                                    }
                                    if ($adlnavInterface['tagData'] == 'previous')    {
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->previous = 1; 
//                                    //////fwrite($ft," Thiet lap thuoc tinh an OK ");
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
                        //////fwrite($ft,"\n Xuat hien IMSSS:SEQUENCING cua ".$parent->identifier);
                        if ($sequencing['name']=='IMSSS:CONTROLMODE'){
                            //Xu ly cac Control Mode voi mot Item trong SCO
                            if ($sequencing['attrs']['CHOICE'] == 'false'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choice = 0;
                            //////fwrite($ft,"\n Xuat hien lua chon choice  \n");
                            }
                            if ($sequencing['attrs']['CHOICEEXIT'] == 'false'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choiceexit = 0;
                            }
                            if ($sequencing['attrs']['FLOW'] == 'true'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->flow = 1;
                            }
                            if ($sequencing['attrs']['FORWARDONLY'] == 'true'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->forwardonly = 1;
                            }
                            if ($sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'] == 'true'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptobjectinfo = 1;
                            }
                            if ($sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'] == 'true'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptprogressinfo = 1;
                            }
                        }
                        if ($sequencing['name']=='ADLSEQ:CONSTRAINEDCHOICECONSIDERATIONS'){
                            //Xu ly cac dieu kien rang buoc thu tu 
                            if ($sequencing['attrs']['CONSTRAINCHOICE'] == 'true'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->constrainChoice = 1;
                            }
                            if ($sequencing['attrs']['PREVENTACTIVATION'] == 'true'){
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->preventactivation = 1;
                            }

                        }
                        if ($sequencing['name']=='IMSSS:OBJECTIVES'){
                            //Xu ly cac cac gia tri muc tieu
                            foreach ($sequencing['children'] as $objective){
                                if($objective['name']=='IMSSS:PRIMARYOBJECTIVE'){
                                    //Xac dinh primary objective de lay thong so
                                    foreach ($objective['children'] as $primaryobjective){
                                        if($primaryobjective['name']=='IMSSS:MINNORMALIZEDMEASURE'){    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->minnormalizedmeasure = $primaryobjective['tagData'];
                                        }
                                    }
                                }
                            }
                        }
                        if ($sequencing['name']=='IMSSS:LIMITCONDITIONS'){
                            //Xu ly cac cac gia tri cac dieu kien gioi han
                            if (!empty($sequencing['attrs']['ATTEMPTLIMIT'])){
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptLimit = $sequencing['attrs']['ATTEMPTLIMIT'];                                
                            }
                            if (!empty($sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'])){
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptAbsoluteDurationLimit = $sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'];                                
                            }                            
                        }                        
                        if ($sequencing['name']=='IMSSS:ROLLUPRULES'){
                            $rolluprules = array();
                            //Phan danh cho RollupRule
                            if (!empty($sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'])){
                                if ($sequencing['attrs']['ROLLUPOBJECTIVESATISFIED']== 'false'){
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupobjectivesatisfied = 0;                                
                                }
                            }
                            if (!empty($sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'])){
                                if ($sequencing['attrs']['ROLLUPPROGRESSCOMPLETION']== 'false'){
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupprogresscompletion = 0;                                
                                }
                            }
                            if (!empty($sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'])){
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectivemeasureweight = $sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'];                    
                            }

                            if (!empty($sequencing['children'])){
                                foreach ($sequencing['children'] as $sequencingrolluprule){
                                    if ($sequencingrolluprule['name']=='IMSSS:ROLLUPRULE' ){
                                        $rolluprule = new stdClass();
                                        if ($sequencingrolluprule['attrs']['CHILDACTIVITYSET'] !=' '){
                                            $rolluprule->childactivityset = $sequencingrolluprule['attrs']['CHILDACTIVITYSET'];
                                            ////fwrite($ft,"\n Thiet lap them 1 childActivitySet la ".$rolluprule->childactivityset);

                                            //Phan xu ly danh sach condition
                                            if (!empty($sequencingrolluprule['children'])){
                                                foreach ($sequencingrolluprule['children'] as $rolluproleconditions)
                                                {
                                                    if ($rolluproleconditions['name']=='IMSSS:ROLLUPCONDITIONS'){
                                                        $conditions = array();
                                                        if (!empty($rolluproleconditions['attrs']['conditionCombination'])){
                                                        $rolluprule->conditionCombination = $rolluproleconditions['attrs']['conditionCombination'];
                                                        }
                                                        foreach ($rolluproleconditions['children'] as $rolluprulecondition){
                                                            if ($rolluprulecondition['name']=='IMSSS:ROLLUPCONDITION'){
                                                                $condition = new stdClass();
                                                                if (!empty($rolluprulecondition['attrs']['OPERATOR'])){
                                                                    $condition->operator = $rolluprulecondition['attrs']['OPERATOR'];
                                                                }
                                                                if (!empty($rolluprulecondition['attrs']['CONDITION'])){
                                                                    $condition->condition = $rolluprulecondition['attrs']['CONDITION'];
                                                                }
                                                            array_push($conditions,$condition);    
                                                            ////fwrite($ft,"Da them mot rolluprulecondition");
                                                            }

                                                        }
                                                    $rolluprule->conditions = $conditions;
                                                    }
                                                    if ($rolluproleconditions['name']=='IMSSS:ROLLUPACTION'){
                                                    $rolluprule->rollupruleaction = $rolluproleconditions['attrs']['ACTION'];
                                                    }
                                                }
                                            }
                                            //Ket thuc xu ly danh sach condition

                                        }
                                        ////fwrite($ft,"\n Dua them 1 rule vao \n");
                                        array_push($rolluprules, $rolluprule);
                                        ////fwrite($ft,"\n Dua them 1 rule vao mang \n");
                                    }

                                }
                            }
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rolluprules = $rolluprules;

//                            ////fwrite($ft,"\n >>>>NOW TEST ");
//                            foreach ($scoes->elements[$manifest][$parent->organization][$parent->identifier]->rolluprules as $rolluptest){
//                            ////fwrite($ft,"\n >>>> Gia tri Test thu duoc la:".$rolluptest->childactivityset);
    
                        }


                        
                        if ($sequencing['name']=='IMSSS:SEQUENCINGRULES'){
                            //Xu ly cac dieu kien Rules cua Sequencing
                            //////fwrite($ft,"\n Xuat hien SEQUENCINGRULES >>>>>>>>>>");                            
                            $sequencingrules = array();
                            foreach ($sequencing['children'] as $conditionrules){
                                if($conditionrules['name']=='IMSSS:EXITCONDITIONRULE'){
                                        $sequencingrule = new stdClass();
                                        //Phan xu ly danh sach condition
                                        //////fwrite($ft,"\n Xuat hien exitrule >>>>>>>>>>");
                                        if (!empty($conditionrules['children'])){
                                            foreach ($conditionrules['children'] as $conditionrule)
                                            {
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
                                                        ////fwrite($ft,"\n Da them mot rulecondition trong exitrule");
                                                        }

                                                    }
                                                $sequencingrule->ruleconditions = $ruleconditions;
                                                }
                                                if ($conditionrule['name']=='IMSSS:RULEACTION'){
                                                $sequencingrule->exitconditionruleaction = $conditionrule['attrs']['ACTION'];
                                                }
                                            }
                                        }
                                        //Ket thuc xu ly danh sach condition
                                array_push($sequencingrules,$sequencingrule);                                        
                                }
                                if ($conditionrules['name']=='IMSSS:PRECONDITIONRULE'){
                                    $sequencingrule = new stdClass();
                                    //Phan xu ly danh sach condition
                                    if (!empty($conditionrules['children'])){
                                        foreach ($conditionrules['children'] as $conditionrule)
                                        {
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
                                                    ////fwrite($ft,"\n Da them mot rulecondition trong prerule");
                                                    }

                                                }
                                            $sequencingrule->ruleconditions = $ruleconditions;
                                            }
                                            if ($conditionrule['name']=='IMSSS:RULEACTION'){
                                            $sequencingrule->preconditionruleaction = $conditionrule['attrs']['ACTION'];
                                            }
                                        }
                                    }
                                    //Ket thuc xu ly danh sach condition
                                array_push($sequencingrules,$sequencingrule);                                
                                }
                                if($conditionrules['name']=='IMSSS:POSTCONDITIONRULE'){
                                        $sequencingrule = new stdClass();
                                        //Phan xu ly danh sach condition
                                        if (!empty($conditionrules['children'])){
                                            foreach ($conditionrules['children'] as $conditionrule)
                                            {
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
                                                        ////fwrite($ft,"\n Da them mot rulecondition trong postrule");
                                                        }

                                                    }
                                                $sequencingrule->ruleconditions = $ruleconditions;
                                                }
                                                if ($conditionrule['name']=='IMSSS:RULEACTION'){
                                                $sequencingrule->postconditionruleaction = $conditionrule['attrs']['ACTION'];
                                                }
                                            }
                                        }
                                        //Ket thuc xu ly danh sach condition
                                array_push($sequencingrules,$sequencingrule);                                
                                }
                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->sequencingrules = $sequencingrules;                                
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

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    //////fwrite($ft,"\n Xu ly doc thong tin trong ham scorm_parse_scorm \n");
    
    $launch = 0;
    $manifestfile = $pkgdir.'/imsmanifest.xml';

    if (is_file($manifestfile)) {
    
        $xmlstring = file_get_contents($manifestfile);
        $objXML = new xml2Array();
        $manifests = $objXML->parse($xmlstring);
            
        $scoes = new stdClass();
        $scoes->version = '';
        $scoes = scorm_get_manifest($manifests,$scoes);

        if (count($scoes->elements) > 0) {
            foreach ($scoes->elements as $manifest => $organizations) {
                foreach ($organizations as $organization => $items) {
                    foreach ($items as $identifier => $item) {
                        $item->scorm = $scormid;
                        $item->manifest = $manifest;
                        $item->organization = $organization;
                        //////fwrite($ft,"\n ---- Item chuan bi dua vao la ".$item->identifier);
                        $id = insert_record('scorm_scoes',$item);
                        //////fwrite($ft,"\n Lay duoc ScoID la ".$id);
                        $item->scormid = $scormid;
                        $item->scoid = $id;
                        $idControlMode = insert_record('scorm_sequencing_controlmode',$item);

                        if (!empty($item->sequencingrules)){
                            ////fwrite($ft,"\n ++++++++Them SequencingRules cho SCO: ".$item->scoid) ;
                            foreach($item->sequencingrules as $sequencingrule){
                                ////fwrite($ft,"\n ----Chuan bi them 1 sequencingrule vao CSDL: ");
                                $sequencingrule->scormid = $scormid;
                                $sequencingrule->scoid = $item->scoid;
                                ////fwrite($ft,"\n ----Thong tin Scormid: ".$sequencingrule->scormid);                                                                                            
                                ////fwrite($ft,"\n ----Thong tin Scoid: ".$sequencingrule->scoid);                                                                                                                            
                                $idruleconditions = insert_record('scorm_sequencing_ruleconditions',$sequencingrule);
                                foreach($sequencingrule->ruleconditions as $rulecondition){
                                    $rulecondition->scormid = $sequencingrule->scormid;
                                    $rulecondition->scoid = $sequencingrule->scoid;
                                    $rulecondition->ruleconditionsid = $idruleconditions;
                                    $idrulecondition = insert_record('scorm_sequencing_rulecondition',$rulecondition);
                                    ////fwrite($ft,"\n ----Da them 1 sequencingrulecondition vao CSDL: ");                                    
                                }
                            
                            }                        
                        }
                        
                        if (!empty($item->rolluprules)){
                            ////fwrite($ft,"\n ++++++++Them RollupRules cho SCO: ".$item->scoid) ;
                            $idControlMode = insert_record('scorm_sequencing_rolluprules',$item);
                            ////fwrite($ft,"\n ----Gia tri idRollupRules \n");
                            foreach($item->rolluprules as $rollup)
                            {
                                ////fwrite($ft,"\n ----Chuan bi them 1 rule vao CSDL ");
                                $rollup->rolluprulesid =$idControlMode;
                                $rollup->scormid = $scormid;
                                $rollup->scoid =  $item->scoid;

                                ////fwrite($ft,"\n ----Cac thong tin cua Rule: \n ");
                                ////fwrite($ft,"\n ----rolluprulesid:  ".$rollup->rolluprulesid);
                                ////fwrite($ft,"\n ----scormid:  ".$rollup->scormid );
                                ////fwrite($ft,"\n ----scoid:  ".$rollup->scoid);
                                ////fwrite($ft,"\n ----activichild:  ".$rollup->childactivityset);
                                ////fwrite($ft,"\n ----rollupaction:  ".$rollup->rollupruleaction);
                                $idRollupRule = insert_record('scorm_sequencing_rolluprule',$rollup);
                                ////fwrite($ft,"\n ----Dua them 1 rule vao CSDL -- Chuan bi them condition vao rule".$idRollupRule);
                                $rollup->rollupruleid = $idRollupRule;
                                $idconditions = insert_record('scorm_sequencing_rollupruleconditions',$rollup);
                                ////fwrite($ft,"\n --Dua cac condition con vao CSDL");
                                foreach($rollup->conditions as $condition){
                                    $condition->ruleconditionsid = $idconditions;
                                    $condition->scormid = $rollup->scormid;
                                    $condition->scoid = $rollup->scoid;
                                    $idcondition = insert_record('scorm_sequencing_rolluprulecondition',$condition);
                                    ////fwrite($ft,"\n --Da dua them 1 condition vao CSDL");
                                }
                                                                
                                
                            }
                        }
                        if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier))) {
                            $launch = $id;
                        }
                    }
                }
            }
            set_field('scorm','version',$scoes->version,'id',$scormid);
        }
    } 
    
    return $launch;
}

function scorm_course_format_display($user,$course) {
    global $CFG;

    $strupdate = get_string('update');
    $strmodule = get_string('modulename','scorm');

    echo '<div class="mod-scorm">';
    if ($scorms = get_all_instances_in_course('scorm', $course)) {
        // The module SCORM activity with the least id is the course  
        $scorm = current($scorms);
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        $colspan = '';
        $headertext = '<table width="100%"><tr><td class="title">'.get_string('name').': <b>'.format_string($scorm->name).'</b>';
        if (isteacher($course->id, $user->id, true)) {
            if (isediting($course->id)) {
                // Display update icon
                $path = $CFG->wwwroot.'/course';
                $headertext .= '<span class="commands">'.
                        '<a title="'.$strupdate.'" href="'.$path.'/mod.php?update='.$cm->id.'&amp;sesskey='.sesskey().'">'.
                        '<img src="'.$CFG->pixpath.'/t/edit.gif" hspace="2" height="11" width="11" border="0" alt="'.$strupdate.'" /></a></span>';
            }
            $headertext .= '</td>';
            // Display report link
            $trackedusers = get_record('scorm_scoes_track', 'scormid', $scorm->id, '', '', '', '', 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                $headertext .= '<td class="reportlink">'.
                              '<a target="'.$CFG->framename.'" href="'.$CFG->wwwroot.'/mod/scorm/report.php?id='.$cm->id.'">'.
                               get_string('viewallreports','scorm',$trackedusers->c).'</a>';
            } else {
                $headertext .= '<td class="reportlink">'.get_string('noreports','scorm');
            }
            $colspan = ' colspan="2"';
        } 
        $headertext .= '</td></tr><tr><td'.$colspan.'>'.format_text(get_string('summary').':<br />'.$scorm->summary).'</td></tr></table>';
        print_simple_box($headertext,'','100%');
        scorm_view_display($user, $scorm, 'view.php?id='.$course->id, $cm, '100%');
    } else {
        if (isteacheredit($course->id, $user->id)) {
            // Create a new activity
            redirect('mod.php?id='.$course->id.'&amp;section=0&sesskey='.sesskey().'&amp;add=scorm');
        } else {
            notify('Could not find a scorm course here');
        }
    }
    echo '</div>';
}

function scorm_view_display ($user, $scorm, $action, $cm, $blockwidth='') {
    global $CFG;
    $organization = optional_param('organization', '', PARAM_INT);

    print_simple_box_start('center',$blockwidth);
?>
        <div class="structurehead"><?php print_string('coursestruct','scorm') ?></div>
<?php
    if (empty($organization)) {
        $organization = $scorm->launch;
    }
    if ($orgs = get_records_select_menu('scorm_scoes',"scorm='$scorm->id' AND organization='' AND launch=''",'id','id,title')) {
        if (count($orgs) > 1) {
 ?>
            <div class='center'>
                <?php print_string('organizations','scorm') ?>
                <form name='changeorg' method='post' action='<?php echo $action ?>'>
                    <?php choose_from_menu($orgs, 'organization', "$organization", '','submit()') ?>
                </form>
            </div>
<?php
        }
    }
    $orgidentifier = '';
    if ($org = get_record('scorm_scoes','id',$organization)) {
        if (($org->organization == '') && ($org->launch == '')) {
            $orgidentifier = $org->identifier;
        } else {
            $orgidentifier = $org->organization;
        }
    }
    $result = scorm_get_toc($user,$scorm,'structlist',$orgidentifier);
    $incomplete = $result->incomplete;
//    echo ("Toc ---");
    echo $result->toc;
//    echo ("Ket thuc");
    print_simple_box_end();
?>
            <div class="center">
                <form name="theform" method="post" action="<?php echo $CFG->wwwroot ?>/mod/scorm/player.php?id=<?php echo $cm->id ?>"<?php echo $scorm->popup == 1?' target="newwin"':'' ?>>
              <?php

// Thiet lap suspend
            $suspend = get_record("scorm_suspendtrack","scormid",$scorm->id,"userid",$user->id);

//------------------
                  if ($scorm->hidebrowse == 0) {
                      print_string("mode","scorm");
                      echo ': <input type="radio" id="b" name="mode" value="browse" /><label for="b">'.get_string('browse','scorm').'</label>'."\n";
                      if ($incomplete === true) {
                          echo '<input type="radio" id="n" name="mode" value="normal" checked="checked" /><label for="n">'.get_string('normal','scorm')."</label>\n";
                            
                            //Neu co luu tru thi co the chon continue de tiep tu
                          if (!empty($suspend))
                          {
                              echo '<input type="radio" id="n" name="mode" value="continue" checked="checked" /><label for="n">'.get_string('continue','scorm')."</label>\n";
                          }

                      } else {
                          echo '<input type="radio" id="r" name="mode" value="review" checked="checked" /><label for="r">'.get_string('review','scorm')."</label>\n";
                      }
                  } else {
                      if ($incomplete === true) {
                          echo '<input type="hidden" name="mode" value="normal" />'."\n";
                      } else {
                          echo '<input type="hidden" name="mode" value="review" />'."\n";
                      }
                  }
                  if (($incomplete === false) && (($result->attemptleft > 0)||($scorm->maxattempt == 0))) {
?>
                  <br />
                  <input type="checkbox" id="a" name="newattempt" />
                  <label for="a"><?php print_string('newattempt','scorm') ?></label>
<?php
                  }
              ?>
              <br />
              <input type="hidden" name="scoid" />
              <input type="hidden" name="currentorg" value="<?php echo $orgidentifier ?>" />
              <input type="submit" value="<?php print_string('entercourse','scorm') ?>" />
              </form>
          </div>
<?php
}

function scorm_update_status($scormid,$scoid)
{
    
}


function scorm_repeater($what, $times) {
    if ($times <= 0) {
        return null;
    }
    $return = '';
    for ($i=0; $i<$times;$i++) {
        $return .= $what;
    }
    return $return;
}

//chuyen toi SCO duoc thuc hien tiep theo
function scorm_get_nextsco($scormid,$scoid)
{



}
//Chuyen toi SCO duoc thuc hien truoc
function scorm_get_presco($scormid,$scoid)
{



}
//Xac dinh xem doi tuong do co cho phep lua chon khong
function scorm_isChoice($scormid,$scoid)
{
//    //$f = "D:\\test.txt";
//    //@$ft = fopen($f,"a");
    $sco = get_record("scorm_sequencing_controlmode","scormid",$scormid,"scoid",$scoid);
//    ////fwrite($ft,"\n Xu ly doc thong tin trong ham scorm_isChoice scormid la ".$scormid." scoid la: ".$scoid);
    $scoparent = get_record("scorm_sequencing_controlmode","scormid",$scormid,"identifier",$sco->parent);
//    ////fwrite($ft,"\n Xu ly doc thong tin trong ham scorm_isChoice scoparent scormid la ".$scormid." scoid la: ".$scoparent->scoid);


//    ////fwrite($ft,"\n Xu ly doc thong tin trong ham scorm_isChoice gia tri la: ".$scoparent->choice);
    return $scoparent->choice;
}

//Xac dinh xem doi tuong do co cho phep lua chon thoat khong
function scorm_isChoiceexit($scormid,$scoid)
{
    $sco = get_record("scorm_sequencing_controlmode","scormid",$scormid,"scoid",$scoid);
    $scoparent = get_record("scorm_sequencing_controlmode","scormid",$scormid,"identifier",$sco->parent);

    return $scoparent->choiceexit;
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
       $entities = '';
       $values = array();
       $lookingfor = 1;

       for ($i = 0; $i < strlen($str); $i++) {
           $thisvalue = ord($str[$i]);
           if ($thisvalue < 128) {
               $entities .= $str[$i]; // Leave ASCII chars unchanged 
           } else {
               if (count($values) == 0) {
                   $lookingfor = ($thisvalue < 224) ? 2 : 3;
               }
               $values[] = $thisvalue;
               if (count($values) == $lookingfor) {
                   $number = ($lookingfor == 3) ?
                       (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64):
                       (($values[0] % 32) * 64) + ($values[1] % 64);
                   $entities .= '&#' . $number . ';';
                   $values = array();
                   $lookingfor = 1;
               }
           }
       }
       return $entities;
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
