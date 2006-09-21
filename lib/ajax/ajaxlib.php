<?PHP
    /**
        *   Library functions for using ajax with moodle
        **/
       
  
   /**
    *Print require statements for javascript libraries
    *Takes in an array of either full paths or shortnames and it will translate them to full paths
    **/

   function print_require_js($list){
   global $CFG;
   
   //list of shortname to filepath translations
    $translatelist = Array(
        "yui_yahoo" => "/lib/yui/yahoo/yahoo.js",
        "yui_dom" => "/lib/yui/dom/dom.js",
        "yui_event" => "/lib/yui/event/event.js",
        "yui_dragdrop" => "/lib/yui/dragdrop/dragdrop.js",
        "yui_logger" => "/lib/yui/logger/logger.js",
        "yui_connection" => "/lib/yui/connection/connection.js",        
        "ajaxcourse_blocks" => "/lib/ajax/block_classes.php",
        "ajaxcourse_sections" => "/lib/ajax/section_classes.php",
        "ajaxcourse_topic" => "/course/format/topics/ajaxcourse-topics.js",
        "ajaxcourse_week" => "/course/format/weeks/ajaxcourse-weeks.js"           
        );
        
        
    for($i=0;$i<count($list);$i++)
        if($translatelist[$list[$i]])
                echo "<script language='JavaScript' type='text/javascript' src='".$CFG->wwwroot.''.$translatelist[$list[$i]]."'></script>\n\r";
        else
                echo "<script language='JavaScript' type='text/javascript' src='".$CFG->wwwroot.''.$list[$i]."'></script>\n\r";
   
   }
   
   //used to create view of document to be passed to javascript on pageload
   class jsportal{
    
    var $currentblocksection = null;
    var $blocks = Array();
    var $blocksoutput = '';
    var $output = '';

    
    //takes id of block and adds it
    function block_add($id,$hidden=false){
        $hidden_binary = 0;
        
        if($hidden)
                $hidden_binary = 1;
                
        $this->blocks[count($this->blocks)] = Array($this->currentblocksection,$id,$hidden_binary);
    }
    
        
    function print_javascript($id){
        $blocksoutput = $output = '';
        for($i=0;$i<count($this->blocks);$i++){
            $blocksoutput.="['".$this->blocks[$i][0]."','".$this->blocks[$i][1]."','".$this->blocks[$i][2]."']";
            if($i != (count($this->blocks)-1))
                $blocksoutput.=",";
        }        
        
        global $CFG;       
        $output .="<script language='javascript'>\r";
        $output .=" 	main.portal.id = ".$id."\r";
        $output .="     main.portal.blocks = new Array(".$blocksoutput.");\r";        
        $output .="     main.portal.strings['wwwroot']='".$CFG->wwwroot."';\r";        
        $output .="     main.portal.strings['update']='".get_string('update')."';\r";  
        $output .="     onload.load()\r";
        $output .="     main.process_blocks();\r";        
        $output .="</script>";
        echo $output;
    }
    
   }   
    
?>
