<?PHP 

    require_once("../../config.php");
    require_variable($id);
		$course = $id;// is there another way of getting the course id? 
    require_login($course);
    if (!(isteacheredit($course))) {
    	error("You're not allowed to edit this course");
    }
    	
    // process data 
		
	if ($prefs = data_submitted()) {
        validate_form($prefs, $err);               //might add this later
		if (count((array)$err) == 0) {
            $prefs->nexttime = usergetmidnight(time())+24*60*60*$prefs->refresh;		
            if (!(update_record("block_glossary_random", $prefs))) {
                error("Could not update record in the database.");
            }
            redirect("$CFG->wwwroot/course/view.php?id=$prefs->course");
        }
    }
    
		// print form
		
    if (!empty($err)) {
        $focus = "form.".array_shift(array_flip(get_object_vars($err)));
    } else {
        $focus = "";
    }

    $prefs = get_record("block_glossary_random", "course", $course);
    if (!$prefs) {
        $prefs->course = $course;
        $prefs->title = get_string('blockname','block_glossary_random');
        $prefs->refresh = 0;
        $prefs->text= get_string("notyetconfigured","block_glossary_random");
        $prefs->addentry=get_string("addentry", "block_glossary_random");
        $prefs->viewglossary=get_string("viewglossary", "block_glossary_random");    	
        $prefs->invisible=get_string("invisible", "block_glossary_random");
        
        if (!(insert_record("block_glossary_random",$prefs))) {
            error("Could not insert new record in database");
        }
    } 	
		
		// select glossaries to put in dropdown box ...
		
		$glossaries = get_records_select_menu("glossary", "course= $course","name","id,name");
		// and quotetypes to put in dropdown box
		
		$type[0] = get_string("random","block_glossary_random");
		$type[1] = get_string("lastmodified","block_glossary_random");
		$type[2] = get_string("nextone","block_glossary_random");
    		      
    print_header(get_string("blockname","block_glossary_random"), 
							get_string("change_configuration","block_glossary_random"));
 	include("prefs.html");
    print_footer();

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($user, &$err) {
    //we might add a check for glossary selected
    return;
}


?>
