<?PHP 
define("RANDOMLY",   "0");
define("LASTMODIFIED",     "1");
define("NEXTONE","2");

class CourseBlock_glossary_random extends MoodleBlock {
    function CourseBlock_glossary_random ($course) {
       
		$this->title = get_string('blockname','block_glossary_random');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004100700;
		if(!empty($course)) {
			if ($prefs=get_record("block_glossary_random","course",$course->id)) {
				$this->title = $prefs->title;
			}	
		}
	}
    function get_content() {
        global $USER, $CFG, $THEME;
		
        if($this->content !== NULL) {
            return $this->content;
        }
		
		
		$this->content = New object;
        $this->content->text = '';
        $this->content->footer = '';
		
		if ($prefs=get_record("block_glossary_random","course",$this->course->id)) {
				
				$glossaryid = $prefs->glossary;
				$glossary=get_record("glossary", "id", $glossaryid);  						  
				$studentcanpost = $glossary->studentcanpost; //needed to decide on which footer
			
			  //check if it's time to put a new entry in cache
				if (time() > $prefs->nexttime)
				 {
					// place glossary concept and definition in $pref->cache			
					$numberofentries = count_records("glossary_entries","glossaryid",$glossaryid,"approved",1)-1; 
					switch ($prefs->type) {
						case RANDOMLY:			
							$i = rand(0,$numberofentries);
							break;
						case NEXTONE:
							$i = 1 + $prefs->previous;
							if ($i < $numberofentries) {
								break;
								} 
							//otherwise fall through					
						case LASTMODIFIED:
							$i=$numberofentries;
							break;
						
						}
							
					if ($entries = get_records_sql("SELECT concept, definition, format
											 FROM {$CFG->prefix}glossary_entries 								
											 WHERE glossaryid = {$glossaryid} and approved = 1
											 ORDER BY timemodified LIMIT {$i},1"))         {
						
						foreach ($entries as $entry) {   //normally only on entry
							$text = "<b> $entry->concept</b><br>";
							$text .= clean_text($entry->definition, $entry->format);
							}
						

						$prefs->nexttime = usergetmidnight(time())+60*60*24*$prefs->refresh;
						$prefs->cache = addslashes($text);
						$prefs->previous = $i;	
						if (!(update_record("block_glossary_random", $prefs))) {
							error("Could not update the database");
							}
					} else {
						$text = get_string('notyetconfigured','block_glossary_random'); 
					}
				}
								
				//otherwise just return the cached text 
				$this->content->text = stripslashes($prefs->cache);
		
				// place link to glossary in the footer if the glossary is visible
			
						//Create a temp valid module structure (course,id)
							 $tempmod->course = $this->course->id;
							 $tempmod->id = $glossaryid;
                             
						//Obtain the visible property from the instance
				if (instance_is_visible('glossary', $tempmod)) {
                
					$cm = get_coursemodule_from_instance('glossary',$glossaryid, $this->course->id) ;
					if ($studentcanpost) {
                        $footertext = $prefs->addentry;
                    } else {
                        $footertext = $prefs->viewglossary;
                    }    
					$this->content->footer = '<a href="'.$CFG->wwwroot.'/mod/glossary/'
						.(($studentcanpost == 1)?'edit':'view').'.php?id='.$cm->id
						.'" title="'.$footertext.'">'.$footertext.'</a>';
				
				// otherwise just place some text, no link
				} else {  
					$this->content->footer = $prefs->invisible;	
				}
                
                
			} 	else { //nothing in the database, this block needs configuration
                $this->content->text = get_string("notyetconfigured","block_glossary_random");
			}
		
		if (isteacheredit($this->course->id)) { //add the option to configure this block
			$this->content->footer .= '<br><a href="'
					.$CFG->wwwroot."/blocks/glossary_random/prefs.php?id="
					.$this->course->id."\">"
					.get_string("configureblock","block_glossary_random")."</a>";
		} 
		
		return $this->content;
    } 
	
	
	}
?>
