<?php // Library functions for using AJAX with Moodle

/**
 * Print require statements for javascript libraries.
 * Takes in an array of either full paths or shortnames and it will translate
 * them to full paths.
 **/
function print_require_js($list) {
    global $CFG;

    if (!check_browser_version('MSIE', 6.0) && !check_browser_version('Firefox', '1.5')) {
        // We still have issues with YUI in other browsers.
        return;
    }

    //list of shortname to filepath translations
    $translatelist = array(
            'yui_yahoo' => '/lib/yui/yahoo/yahoo.js',
            'yui_dom' => '/lib/yui/dom/dom.js',
            'yui_event' => '/lib/yui/event/event.js',
            'yui_dragdrop' => '/lib/yui/dragdrop/dragdrop.js',
            'yui_logger' => '/lib/yui/logger/logger.js',
            'yui_connection' => '/lib/yui/connection/connection.js',        
            'ajaxcourse_blocks' => '/lib/ajax/block_classes.js',
            'ajaxcourse_sections' => '/lib/ajax/section_classes.js',
            'ajaxcourse' => '/lib/ajax/ajaxcourse.js'
            );


    for ($i=0; $i<count($list); $i++) {
        if ($translatelist[$list[$i]]) {
            echo "<script type='text/javascript' src='".$CFG->wwwroot.''.$translatelist[$list[$i]]."'></script>\n";
        } else {
            echo "<script type='text/javascript' src='".$CFG->wwwroot.''.$list[$i]."'></script>\n";
        }
    }

    if (debugging('', DEBUG_DEVELOPER)) {
        echo "<script type='text/javascript' src='".$CFG->wwwroot.''.$translatelist['yui_logger']."'></script>\n";
        
        // Dependencies for the logger.
        echo "<link type='text/css' rel='stylesheet' href='{$CFG->wwwroot}/lib/yui/logger/assets/logger.css'>";
        
        // FIXME: Below might get included more than once.
        echo "<script type='text/javascript' src='".$CFG->wwwroot.''.$translatelist['yui_yahoo']."'></script>\n";
        echo "<script type='text/javascript' src='".$CFG->wwwroot.''.$translatelist['yui_dom']."'></script>\n";
        echo "<script type='text/javascript' src='".$CFG->wwwroot.''.$translatelist['yui_event']."'></script>\n";
        echo "<script type='text/javascript' src='".$CFG->wwwroot.''.$translatelist['yui_dragdrop']."'></script>\n";
        ?>
        <script type="text/javascript">
            
            var logcontainer = null;

            var logconfig = {
                left: "60%",
                top: "40px",
            }
            var logreader = new YAHOO.widget.LogReader(logcontainer, logconfig);
            logreader.newestOnTop = false;
            logreader.setTitle('Moodle Debug: YUI Log Console');

        </script>
        <?php
    }
}


/**
 * Used to create view of document to be passed to javascript on pageload.
 */
class jsportal {

    var $currentblocksection = null;
    var $blocks = array();
    var $blocksoutput = '';
    var $output = '';


    /**
     * Takes id of block and adds it
     */
    function block_add($id, $hidden=false ){
        $hidden_binary = 0;

        if ($hidden) {
            $hidden_binary = 1;
        }
        $this->blocks[count($this->blocks)] = array($this->currentblocksection, $id, $hidden_binary);
    }


    function print_javascript($id) {
        global $CFG;

        $blocksoutput = $output = '';
        for ($i=0; $i<count($this->blocks); $i++) {
            $blocksoutput .= "['".$this->blocks[$i][0]."','".$this->blocks[$i][1]."','".$this->blocks[$i][2]."']";
            if ($i != (count($this->blocks)-1)) {
                $blocksoutput .= ',';
            }
        }

        $output .= "<script language='javascript'>\n";
        $output .= " 	main.portal.id = ".$id.";\n";
        $output .= "    main.portal.blocks = new Array(".$blocksoutput.");\n";        
        $output .= "    main.portal.strings['wwwroot']='".$CFG->wwwroot."';\n";        
        $output .= "    main.portal.strings['update']='".get_string('update')."';\n";
        $output .= "    main.portal.strings['deletecheck']='".get_string('deletecheck','','_var_')."';\n"; 
        $output .= "    main.portal.strings['resource']='".get_string('resource')."';\n"; 
        $output .= "    main.portal.strings['activity']='".get_string('activity')."';\n";         
        $output .= "    onload.load();\n";
        $output .= "    main.process_blocks();\n";
        $output .= "</script>";
        echo $output;
    }
}

?>