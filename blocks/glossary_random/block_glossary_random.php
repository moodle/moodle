<?PHP 
define("RANDOMLY",   "0");
define("LASTMODIFIED",     "1");
define("NEXTONE","2");

class block_glossary_random extends block_base {
    function init() {
       
		$this->title = get_string('blockname','block_glossary_random');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2005010300;
		
	}
    
    function specialization() {
        global $CFG;
        $this->course = get_record('course', 'id', $this->instance->pageid);
        
        // load userdefined title and make sure it's never empty
        if (empty($this->config->title)) {
            $this->title = get_string('blockname','block_glossary_random');
        } else {    
            $this->title = $this->config->title;
        }   

		if (empty($this->config->glossary)) {
            return false;
        }

		if (!isset($this->config->nexttime)) {
            $this->config->nexttime = 0;
        }
        
        //check if it's time to put a new entry in cache
		if (time() > $this->config->nexttime) {
			
            // place glossary concept and definition in $pref->cache			
			$numberofentries = count_records("glossary_entries","glossaryid",$this->config->glossary,"approved",1)-1; 
			switch ($this->config->type) {
				
                case RANDOMLY:			
					$i = rand(0,$numberofentries);
					break;
				
                case NEXTONE:
					$i = 1 + $this->config->previous;
					if ($i < $numberofentries) {
                        break;
					} 
					//otherwise fall through					
				
                case LASTMODIFIED:
					$i=$numberofentries;
					break;

                default:
                    $i = 0;
					
			}
							
			if ($entries = get_records_sql("SELECT concept, definition, format FROM {$CFG->prefix}glossary_entries 								 WHERE glossaryid = {$this->config->glossary} and approved = 1 ORDER BY timemodified LIMIT {$i},1")) {
                // get all entries, normally there is only one entry returned                        
				foreach ($entries as $entry) {   
                    $text = "<b> $entry->concept</b><br>";
					$text .= format_text($entry->definition, $entry->format);
                    
				}
						
                $this->config->nexttime = usergetmidnight(time())+60*60*24*$this->config->refresh;
                $this->config->previous = $i;	
                 
			} else {
                $text = get_string('notyetconfigured','block_glossary_random'); 
			}
            // store the text
            $this->config->cache= $text;
            parent::instance_config_save($this->config);    
		}
    }
    
    function instance_allow_multiple() {
    // Are you going to allow multiple instances of each block?
    // If yes, then it is assumed that the block WILL USE per-instance configuration
        return true;
    }
    
    function instance_config_print() {
        global $CFG; 
          
        if (empty($this->config->nexttime)) {
            // ... teacher has not yet configured the block, let's put some default values here to explain things
            $this->config->title = get_string('blockname','block_glossary_random');
            $this->config->refresh = 0;
        
            $this->config->cache= get_string("notyetconfigured","block_glossary_random");
            $this->config->addentry=get_string("addentry", "block_glossary_random");
            $this->config->viewglossary=get_string("viewglossary", "block_glossary_random");    	
            $this->config->invisible=get_string("invisible", "block_glossary_random");
        }
        
        // select glossaries to put in dropdown box ...
        $glossaries = get_records_select_menu("glossary", "course=".$this->course->id,"name","id,name");            
		
        // and select quotetypes to put in dropdown box
		$type[0] = get_string("random","block_glossary_random");
		$type[1] = get_string("lastmodified","block_glossary_random");
		$type[2] = get_string("nextone","block_glossary_random");
        
        $this->config->nexttime = usergetmidnight(time())+24*60*60*$this->config->refresh;	
        
        // display the form
        
        if (is_file($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html')) {
            print_simple_box_start('center', '', '', 5, 'blockconfigglobal');
            include($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html');
            print_simple_box_end();
        } else {
            notice(get_string('blockconfigbad'), str_replace('blockaction=', 'dummy=', qualified_me()));
        }
    
    return true;
    }
   
    function get_content() {
        global $USER, $CFG;
		
        if($this->content !== NULL) {
            return $this->content;
        }
				
		$this->content = new stdClass;
        $this->content->text = $this->config->cache;

		if (empty($this->config->glossary)) {
            return $this->content;
        }
        
        // place link to glossary in the footer if the glossary is visible        
        $glossaryid = $this->config->glossary;
        $glossary=get_record("glossary", "id", $glossaryid);  						  
        $studentcanpost = $glossary->studentcanpost; //needed to decide on which footer
				
        //Create a temp valid module structure (course,id)
         $tempmod->course = $this->course->id;
         $tempmod->id = $glossaryid;
                             
		//Obtain the visible property from the instance
        if (instance_is_visible('glossary', $tempmod)) {
        
            $cm = get_coursemodule_from_instance('glossary',$glossaryid, $this->course->id) ;
            if ($studentcanpost) {
                $footertext = $this->config->addentry;
            } else {
                $footertext = $this->config->viewglossary;
            }    
            $this->content->footer = '<a href="'.$CFG->wwwroot.'/mod/glossary/'
                .(($studentcanpost == 1)?'edit':'view').'.php?id='.$cm->id
                .'" title="'.$footertext.'">'.$footertext.'</a>';
        
        // otherwise just place some text, no link
        } else {  
            $this->content->footer = $this->config->invisible;	
        }
        		
		return $this->content;
    } 
	
    function hide_header() {
		if (empty($this->config->title)) {
            return false;
        }
        if ($this->config->title == "") {
            return true;
        }
        return false;
    }
	
}
?>
