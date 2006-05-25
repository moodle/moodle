<?php // $Id$
function migrate2utf8_data_fields_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT d.course
           FROM {$CFG->prefix}data_fields df,
                {$CFG->prefix}data d
           WHERE d.id = df.dataid
                 AND df.id = $recordid";

    if (!$data = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$datafield = get_record('data_fields','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($datafield->name, $fromenc);

        $newdatafield = new object;
        $newdatafield->id = $recordid;
        $newdatafield->name = $result;
        migrate2utf8_update_record('data_fields',$newdatafield);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_fields_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT d.course
           FROM {$CFG->prefix}data_fields df,
                {$CFG->prefix}data d
           WHERE d.id = df.dataid
                 AND df.id = $recordid";

    if (!$data = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$datafield = get_record('data_fields','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($datafield->description, $fromenc);

        $newdatafield = new object;
        $newdatafield->id = $recordid;
        $newdatafield->description = $result;
        migrate2utf8_update_record('data_fields',$newdatafield);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->name, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->name = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_intro($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->intro, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->intro = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_singletemplate($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->singletemplate, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->singletemplate = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_listtemplate($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->listtemplate, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->listtemplate = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_addtemplate($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->addtemplate, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->addtemplate = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_rsstemplate($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->rsstemplate, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->rsstemplate = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_rsstitletemplate($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->rsstitletemplate, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->rsstitletemplate = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}


function migrate2utf8_data_csstemplate($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->csstemplate, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->csstemplate = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}


function migrate2utf8_data_listtemplateheader($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)){
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->listtemplateheader, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->listtemplateheader = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_data_listtemplatefooter($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$data = get_record('data','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($data->course);  //Non existing!
        $userlang   = get_main_teacher_lang($data->course); //N.E.!!
        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($data->listtemplatefooter, $fromenc);

        $newdata= new object;
        $newdata->id = $recordid;
        $newdata->listtemplatefooter = $result;
        migrate2utf8_update_record('data',$newdata);
    }
/// And finally, just return the converted field
    return $result;
}

?>
