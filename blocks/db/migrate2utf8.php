<?php // $Id$
function migrate2utf8_block_instance_configdata($recordid){
    global $CFG, $globallang;

    $blockinstance = get_record('block_instance','id',$recordid);

    //get block instance type, we only need to worry about HTML blocks... right?????????
    
    $blocktype = get_record('block','id',$blockinstance->blockid);
    
    if ($blocktype -> name == 'html') {

        ///find course
        if ($globallang) {
            $fromenc = $globallang;
        } else {
            $sitelang   = $CFG->lang;
            if ($blockinstance->pagetype == 'course-view') {
                $courselang = get_course_lang($blockinstance->pageid);  //Non existing!
                $userlang   = get_main_teacher_lang($blockinstance->pageid); //N.E.!!
            } else {
                $courselang = false;
                $userlang = false;
            }

            $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
        }
       
        $blah = unserialize(base64_decode($blockinstance->configdata));
 
    /// We are going to use textlib facilities
        
    /// Convert the text
        $blah->title = utfconvert($blah->title, $fromenc, false);
        $blah->text = utfconvert($blah->text, $fromenc, false);
        
        $blockinstance->configdata = base64_encode(serialize($blah));

        migrate2utf8_update_record('block_instance',$blockinstance);

        return $blah;

    } else if ($blocktype -> name == 'rss_client'){

        ///find course
        if ($globallang) {
            $fromenc = $globallang;
        } else {
            $sitelang   = $CFG->lang;
            if ($blockinstance->pagetype == 'course-view') {
                $courselang = get_course_lang($blockinstance->pageid);  //Non existing!
                $userlang   = get_main_teacher_lang($blockinstance->pageid); //N.E.!!
            } else {
                $courselang = false;
                $userlang = false;
            }

            $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
        }

        $blah = unserialize(base64_decode($blockinstance->configdata));

    /// We are going to use textlib facilities
        
    /// Convert the text
        $blah->title = utfconvert($blah->title, $fromenc, false);

        $blockinstance->configdata = base64_encode(serialize($blah));

        migrate2utf8_update_record('block_instance',$blockinstance);

        return $blah;

    } else if ($blocktype -> name == 'glossary_random'){

        ///find course
        if ($globallang) {
            $fromenc = $globallang;
        } else {
            $sitelang   = $CFG->lang;
            if ($blockinstance->pagetype == 'course-view') {
                $courselang = get_course_lang($blockinstance->pageid);  //Non existing!
                $userlang   = get_main_teacher_lang($blockinstance->pageid); //N.E.!!
            } else {
                $courselang = false;
                $userlang = false;
            }

            $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
        }

        $blah = unserialize(base64_decode($blockinstance->configdata));

    /// We are going to use textlib facilities

    /// Convert the text
        $blah->title = utfconvert($blah->title, $fromenc, false);
        $blah->addentry = utfconvert($blah->addentry, $fromenc, false);
        $blah->viewglossary = utfconvert($blah->viewglossary, $fromenc, false);
        $blah->invisible = utfconvert($blah->invisible, $fromenc, false);

        $blockinstance->configdata = base64_encode(serialize($blah));

        migrate2utf8_update_record('block_instance',$blockinstance);

        return $blah;

    }

}

function migrate2utf8_block_pinned_configdata($recordid){
global $CFG, $globallang;

    $blockpinned = get_record('block_pinned','id',$recordid);

    //get block instance type, we only need to worry about HTML blocks... right?????????

    $blocktype = get_record('block','id',$blockpinned->blockid);

    if ($blocktype -> name == 'html') {

        ///find course
        if ($globallang) {
            $fromenc = $globallang;
        } else {
            $sitelang   = $CFG->lang;
            $fromenc = get_original_encoding($sitelang, false, false);
        }

        $blah = unserialize(base64_decode($blockpinned->configdata));

    /// We are going to use textlib facilities

    /// Convert the text
        $blah->title = utfconvert($blah->title, $fromenc, false);
        $blah->text = utfconvert($blah->text, $fromenc, false);

        $blockpinned->configdata = base64_encode(serialize($blah));

        migrate2utf8_update_record('blockpinned',$blockpinned);

        return $blah;

    } else if ($blocktype -> name == 'rss_client'){

        ///find course
        if ($globallang) {
            $fromenc = $globallang;
        } else {
            $sitelang   = $CFG->lang;
            $fromenc = get_original_encoding($sitelang, false, false);
        }

        $blah = unserialize(base64_decode($blockpinned->configdata));

    /// We are going to use textlib facilities

    /// Convert the text
        $blah->title = utfconvert($blah->title, $fromenc, false);

        $blockpinned->configdata = base64_encode(serialize($blah));

        migrate2utf8_update_record('blockpinned',$blockblockpinned);

        return $blah;

    } else if ($blocktype -> name == 'glossary_random'){

        ///find course
        if ($globallang) {
            $fromenc = $globallang;
        } else {
            $sitelang   = $CFG->lang;
            $fromenc = get_original_encoding($sitelang, false, false);
        }

        $blah = unserialize(base64_decode($blockpinned->configdata));

    /// We are going to use textlib facilities

    /// Convert the text
        $blah->title = utfconvert($blah->title, $fromenc, false);
        $blah->addentry = utfconvert($blah->addentry, $fromenc, false);
        $blah->viewglossary = utfconvert($blah->viewglossary, $fromenc, false);
        $blah->invisible = utfconvert($blah->invisible, $fromenc, false);

        $blockinstance->configdata = base64_encode(serialize($blah));

        migrate2utf8_update_record('block_instance',$blockinstance);

        return $blah;

    }
}
?>
