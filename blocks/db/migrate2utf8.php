<?
function migrate2utf8_block_instance_configdata($recordid){
    global $CFG;

    $blockinstance = get_record('block_instance','id',$recordid);

    //get block instance type, we only need to worry about HTML blocks... right?????????
    
    $blocktype = get_record('block','id',$blockinstance->blockid);
    
    if ($blocktype -> name == 'html') {

        ///find course

        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($blockinstance->pageid);  //Non existing!
        $userlang   = get_main_teacher_lang($blockinstance->pageid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
       
        $blah = unserialize(base64_decode($blockinstance->configdata));
 
    /// We are going to use textlib facilities
        $textlib = textlib_get_instance();
    /// Convert the text
        $blah->title = $textlib->convert($blah->title, $fromenc);
        $blah->text = $textlib->convert($blah->text, $fromenc);
        
        $blockinstance->configdata = base64_encode(serialize($blah));

        update_record('block_instance',$blockinstance);

        return $blah;
    }
}

function migrate2utf8_block_pinned_configdata($recordid){

}
?>
