<?PHP //$Id$

//This library includes all the necessary stuff to use blocks in course pages

define('BLOCK_LEFT',        11);
define('BLOCK_RIGHT',       12);
define('BLOCK_MOVE_LEFT',   0x01);
define('BLOCK_MOVE_RIGHT',  0x02);
define('BLOCK_MOVE_UP',     0x04);
define('BLOCK_MOVE_DOWN',   0x08);

define('COURSE_FORMAT_WEEKS',   0x01);
define('COURSE_FORMAT_TOPICS',  0x02);
define('COURSE_FORMAT_SOCIAL',  0x04);

//This function retrieves a method-defined property of a class WITHOUT instantiating an object
//It seems that the only way to use the :: operator with variable class names is eval() :(
//For caveats with this technique, see the PHP docs on operator ::
function block_method_result($blockname, $method) {
    if(!block_load_class($blockname)) {
        return NULL;
    }
    return eval('return CourseBlock_'.$blockname.'::'.$method.'();');
}

//This function creates a new object of the specified block class
function block_instance($blockname, $argument) {
    if(!block_load_class($blockname)) {
        return false;
    }
    $classname = 'CourseBlock_'.$blockname;
    return New $classname($argument);
}

//This function loads the necessary class files for a block
//Whenever you want to load a block, use this first
function block_load_class($blockname) {
    global $CFG;

    @include_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    $classname = 'CourseBlock_'.$blockname;
    @include_once($CFG->dirroot.'/blocks/'.$blockname.'/block_'.$blockname.'.php');

    // After all this, return value indicating success or failure
    return class_exists($classname);
}

//This function determines if there is some active block in an array of blocks
function block_have_active($array) {
    foreach($array as $blockid) {
        if($blockid > 0) {
            return true;
        }
    }
    return false;
}

//This function print the one side of blocks in course main page
function print_course_blocks(&$course, $blocksarray, $side) {
    global $CFG;

    $isediting = isediting($course->id);
    $ismoving = ismoving($course->id);
    $isteacheredit = isteacheredit($course->id);

    if(!empty($blocksarray)) {
        // Include the base class
        @include_once($CFG->dirroot.'/blocks/moodleblock.class.php');
        if(!class_exists('moodleblock')) {
            error('Class MoodleBlock is not defined or file not found for /course/blocks/moodleblock.class.php');
        }

        $blockdata = get_records('blocks', 'visible', 1);
        if($blockdata !== false) {

            $lastblock = end($blocksarray);
            $firstblock = reset($blocksarray);

            foreach($blocksarray as $blockid) {
                if(!isset($blockdata[abs($blockid)])) {
                    // This block is hidden. Don't show it.
                    continue;
                }

                $blockname = $blockdata[abs($blockid)]->name;
                $block = block_instance($blockname, $course);
                if($block === false) {
                    // Something went wrong
                    continue;
                }

                // There are various sanity checks commented out below
                // because the block detection code should have already done them long ago.

                /*
                if(!is_subclass_of($block, 'MoodleBlock')) {
                    // Error: you have to derive from MoodleBlock
                    continue;
                }

                if($content === NULL || $title === NULL) {
                    // Error: This shouldn't have happened
                    continue;
                }
                */
                if ($isediting && !$ismoving && $isteacheredit) {
                    $options = 0;
                    $options |= BLOCK_MOVE_UP * ($blockid != $firstblock);
                    $options |= BLOCK_MOVE_DOWN * ($blockid != $lastblock);
                    $options |= ($side == BLOCK_LEFT) ? BLOCK_MOVE_RIGHT : BLOCK_MOVE_LEFT;
                    $block->add_edit_controls($options, $blockid);
                }

                if($blockid < 0) {
                    // We won't print this block...
                    if($isediting) {
                        // Unless we 're in editing mode, in which case we 'll print a 'shadow'
                        $block->print_shadow();
                    }
                    continue;
                }
                // So simple...
                $block->print_block();
            }
        }
    }
}

//This iterates over an array of blocks and calculates the preferred width
function blocks_preferred_width($blockarray, $blockinfos) {
    $width = 0;

    if(!is_array($blockarray) || empty($blockarray)) {
        return 0;
    }
    foreach($blockarray as $blockid) {
        if(isset($blockinfos[$blockid])) {
            $blockname = $blockinfos[$blockid]->name;
            $pref = block_method_result($blockname, 'preferred_width');
            if($pref === NULL) {
                continue;
            }
            if($pref > $width) {
                $width = $pref;
            }
        }
    }
    return $width;
}


// $course passed by reference for speed
// $leftblocks, $rightblocks passed by reference because block_action() needs to
// update the arrays so that the change can be shown immediately.

function block_action(&$course, &$leftblocks, &$rightblocks, $blockaction, $blockid) {

    $blockid = abs(intval($blockid)); // Just to make sure

    switch($blockaction) {
        case 'toggle':
            $block = block_find($blockid, $leftblocks, $rightblocks);
            if($block !== false) {
                if($block->side == BLOCK_LEFT) {
                    $leftblocks[$block->position] = -$leftblocks[$block->position];
                }
                else {
                    $rightblocks[$block->position] = -$rightblocks[$block->position];
                }
            }
        break;
        case 'delete':
            $block = block_find($blockid, $leftblocks, $rightblocks);
            if($block !== false) {
                if($block->side == BLOCK_LEFT) {
                    unset($leftblocks[$block->position]);
                }
                else {
                    unset($rightblocks[$block->position]);
                }
            }
        break;
        case 'add':
            // Toggle to enabled, or add it if it doesn't exist at all
            $block = block_find($blockid, $leftblocks, $rightblocks);
            if($block === false) {
                // It doesn't exist at all, so add it
                $rightblocks[] = $blockid;
            }
            else if($block->enabled == false) {
                // Enable it
                if($block->side == BLOCK_LEFT) {
                    $leftblocks[$block->position] = -$leftblocks[$block->position];
                }
                else {
                    $rightblocks[$block->position] = -$rightblocks[$block->position];
                }
            }
        break;
        case 'moveup':
            $block = block_find($blockid, $leftblocks, $rightblocks);
            if($block !== false) {
                if($block->side == BLOCK_LEFT) {
                    if(isset($leftblocks[$block->position - 1])) {
                        // We can move it upwards
                        $oldblock = $leftblocks[$block->position - 1];
                        $leftblocks[$block->position - 1] = $leftblocks[$block->position]; // not $blockid, as this loses the sign
                        $leftblocks[$block->position] = $oldblock;
                    }
                }
                else {
                    if(isset($rightblocks[$block->position - 1])) {
                        // We can move it upwards
                        $oldblock = $rightblocks[$block->position - 1];
                        $rightblocks[$block->position - 1] = $rightblocks[$block->position]; // not $blockid, as this loses the sign
                        $rightblocks[$block->position] = $oldblock;
                    }
                }
            }
        break;
        case 'movedown':
            $block = block_find($blockid, $leftblocks, $rightblocks);
            if($block !== false) {
                if($block->side == BLOCK_LEFT) {
                    if(isset($leftblocks[$block->position + 1])) {
                        // We can move it downwards
                        $oldblock = $leftblocks[$block->position + 1];
                        $leftblocks[$block->position + 1] = $leftblocks[$block->position]; // not $blockid, as this loses the sign
                        $leftblocks[$block->position] = $oldblock;
                    }
                }
                else {
                    if(isset($rightblocks[$block->position + 1])) {
                        // We can move it downwards
                        $oldblock = $rightblocks[$block->position + 1];
                        $rightblocks[$block->position + 1] = $rightblocks[$block->position]; // not $blockid, as this loses the sign
                        $rightblocks[$block->position] = $oldblock;
                    }
                }
            }
        break;
        case 'moveside':
            $block = block_find($blockid, $leftblocks, $rightblocks);
            if($block !== false) {
                if($block->side == BLOCK_LEFT) {
                    unset($leftblocks[$block->position]);
                    $rightblocks[] = $block->enabled ? $blockid : -$blockid;
                }
                else {
                    unset($rightblocks[$block->position]);
                    $leftblocks[] = $block->enabled ? $blockid : -$blockid;
                }
            }
        break;
    }

    $course->blockinfo = implode(',', $leftblocks).':'.implode(',',$rightblocks);
    set_field('course', 'blockinfo', $course->blockinfo, 'id', $course->id);

}

// Searches for the block with ID $blockid in one or more of the two
// blocks arrays. If not found, returns boolean false. Otherwise,
// returns an object $finding where:
//      $finding->side = BLOCK_LEFT or BLOCK_RIGHT
//      $finding->enabled = true or false
//      $finding->position = index of corresponding array where found

function block_find($blockid, $leftblocks, $rightblocks) {

    if(($blockid = abs($blockid)) == 0) {
        return false;
    }

    $finding->side = BLOCK_LEFT;
    $finding->enabled = true;
    $finding->position = NULL;

    // First, search for the "enabled" block, since that's what we
    // will be doing most of the time.

    $key = array_search($blockid, $leftblocks);
    if($key !== false && $key !== NULL) {
        $finding->position = $key;
        return $finding;
    }
    $key = array_search($blockid, $rightblocks);
    if($key !== false && $key !== NULL) {
        $finding->position = $key;
        $finding->side = BLOCK_RIGHT;
        return $finding;
    }

    // "enabled" block not found. Now search for the disabled block.
    $finding->enabled = false;
    $blockid = -$blockid;

    $key = array_search($blockid, $leftblocks);
    if($key !== false && $key !== NULL) {
        $finding->position = $key;
        return $finding;
    }
    $key = array_search($blockid, $rightblocks);
    if($key !== false && $key !== NULL) {
        $finding->position = $key;
        $finding->side = BLOCK_RIGHT;
        return $finding;
    }

    // Nothing found :(

    return false;
}

//This function prints the block to admin blocks as necessary
function block_print_blocks_admin($courseid, $missingblocks) {
    if (isediting($courseid)) {
        $strblocks = get_string('blocks');
        $stradd    = get_string('add');
        if (!empty($missingblocks)) {
            $blockdata = get_records_list('blocks', 'id', implode(',', $missingblocks));
            if ($blockdata !== false) {
                foreach ($blockdata as $block) {
                    $blockobject = block_instance($block->name, NULL);
                    if ($blockobject === false) {
                        continue;
                    }
                    $menu[$block->id] = $blockobject->get_title();
                }
                $content = popup_form('view.php?id='.$courseid.'&amp;blockaction=add&amp;blockid=',
                                      $menu, 'add_block', '', "$stradd...", '', '', true);
                $content = '<div align="center">'.$content.'</div>';
                print_side_block($strblocks, $content, NULL, NULL, NULL);
            }
        }
    }
}

function upgrade_blocks_db($continueto) {
/// This function upgrades the blocks tables, if necessary
/// It's called from admin/index.php

    global $CFG, $db;

    require_once ("$CFG->dirroot/blocks/version.php");  // Get code versions

    if (empty($CFG->blocks_version)) {                  // Blocks have never been installed.
        $strdatabaseupgrades = get_string("databaseupgrades");
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades,
                     "", "", false, "&nbsp;", "&nbsp;");

        $db->debug=true;
        if (modify_database("$CFG->dirroot/blocks/db/$CFG->dbtype.sql")) {
            $db->debug = false;
            if (set_config("blocks_version", $blocks_version)) {
                notify(get_string("databasesuccess"), "green");
                notify(get_string("databaseupgradeblocks", "", $blocks_version));
                print_continue($continueto);
                exit;
            } else {
                error("Upgrade of blocks system failed! (Could not update version in config table)");
            }
        } else {
            error("Blocks tables could NOT be set up successfully!");
        }
    }


    if ($blocks_version > $CFG->blocks_version) {       // Upgrade tables
        $strdatabaseupgrades = get_string("databaseupgrades");
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);

        require_once ("$CFG->dirroot/blocks/db/$CFG->dbtype.php");

        $db->debug=true;
        if (blocks_upgrade($CFG->blocks_version)) {
            $db->debug=false;
            if (set_config("blocks_version", $blocks_version)) {
                notify(get_string("databasesuccess"), "green");
                notify(get_string("databaseupgradeblocks", "", $blocks_version));
                print_continue($continueto);
                exit;
            } else {
                error("Upgrade of blocks system failed! (Could not update version in config table)");
            }
        } else {
            $db->debug=false;
            error("Upgrade failed!  See blocks/version.php");
        }

    } else if ($blocks_version < $CFG->blocks_version) {
        notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
    }
}

//This function finds all available blocks and install them
//into blocks table or do all the upgrade process if newer
function upgrade_blocks_plugins($continueto) {

    global $CFG;

    $blocktitles = array();
    $invalidblocks = array();
    $validblocks = array();
    $notices = array();

    //Count the number of blocks in db
    $blockcount = count_records("blocks");
    //If there isn't records. This is the first install, so I remember it
    if ($blockcount == 0) {
        $first_install = true;
    } else {
        $first_install = false;
    }

    $site = get_site();

    if (!$blocks = get_list_of_plugins("blocks", "db") ) {
        error("No blocks installed!");
    }

    include_once($CFG->dirroot."/blocks/moodleblock.class.php");
    if(!class_exists('moodleblock')) {
        error('Class MoodleBlock is not defined or file not found for /blocks/moodleblock.class.php');
    }

    foreach ($blocks as $blockname) {

        if ($blockname == "NEWBLOCK") {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullblock = "$CFG->dirroot/blocks/$blockname";

        if ( is_readable($fullblock."/block_".$blockname.".php")) {
            include_once($fullblock."/block_".$blockname.".php");
        } else {
            $notices[] = "Block $blockname: ".$fullblock."/block_".$blockname.".php was not readable";
            continue;
        }

        if ( is_dir("$fullblock/db/")) {
            if ( is_readable("$fullblock/db/$CFG->dbtype.php")) {
                include_once("$fullblock/db/$CFG->dbtype.php");  # defines upgrading function
            } else {
                $notices[] ="Block $blockname: $fullblock/db/$CFG->dbtype.php was not readable";
                continue;
            }
        }

        $classname = 'CourseBlock_'.$blockname;
        if(!class_exists($classname)) {
            $notices[] = "Block $blockname: $classname not implemented";
            continue;
        }

        // Let's see if it supports some basic methods
        $methods = get_class_methods($classname);
        if(!in_array(strtolower($classname), $methods)) {
            // No constructor
            //$notices[] = "Block $blockname: class does not have a constructor";
            $invalidblocks[] = $blockname;
            continue;
        }

        unset($block);

        $blockobj = New $classname($site);

        // Inherits from MoodleBlock?
        if(!is_subclass_of($blockobj, "moodleblock")) {
            $notices[] = "Block $blockname: class does not inherit from MoodleBlock";
            continue;
        }

        // OK, it's as we all hoped. For further tests, the object will do them itself.
        if(!$blockobj->_self_test()) {
            $notices[] = "Block $blockname: self test failed";
            continue;
        }
        $block->version = $blockobj->get_version();

        if (!isset($block->version)) {
            $notices[] = "Block $blockname: hasn't version support";
            continue;
        }

        $block->name = $blockname;   // The name MUST match the directory
        $blocktitle = $blockobj->get_title();

        if ($currblock = get_record("blocks", "name", $block->name)) {
            if ($currblock->version == $block->version) {
                // do nothing
            } else if ($currblock->version < $block->version) {
                if (empty($updated_blocks)) {
                    $strblocksetup    = get_string("blocksetup");
                    print_header($strblocksetup, $strblocksetup, $strblocksetup, "", "", false, "&nbsp;", "&nbsp;");
                }
                print_heading('New version of '.$blocktitle.' ('.$block->name.') exists');
                $upgrade_function = $block->name.'_upgrade';
                if (function_exists($upgrade_function)) {
                    $db->debug=true;
                    if ($upgrade_function($currblock->version, $block)) {

                        $upgradesuccess = true;
                    } else {
                        $upgradesuccess = false;
                    }
                    $db->debug=false;
                }
                else {
                    $upgradesuccess = true;
                }
                if(!$upgradesuccess) {
                    notify("Upgrading block $block->name from $currblock->version to $block->version FAILED!");
                }
                else {
                    // OK so far, now update the blocks record
                    $block->id = $currblock->id;
                    if (! update_record('blocks', $block)) {
                        error("Could not update block $block->name record in blocks table!");
                    }
                    notify(get_string('blocksuccess', '', $blocktitle), 'green');
                    echo '<hr />';
                }
                $updated_blocks = true;
            } else {
                error("Version mismatch: block $block->name can't downgrade $currblock->version -> $block->version !");
            }

        } else {    // block not installed yet, so install it

            // [pj] Normally this would be inline in the if, but we need to
            //      check for NULL (necessary for 4.0.5 <= PHP < 4.2.0)
            $conflictblock = array_search($blocktitle, $blocktitles);
            if($conflictblock !== false && $conflictblock !== NULL) {

                // Duplicate block titles are not allowed, they confuse people
                // AND PHP's associative arrays ;)
                error('<strong>Naming conflict</strong>: block <strong>'.$block->name.'</strong> has the same title with an existing block, <strong>'.$conflictblock.'</strong>!');
            }
            if (empty($updated_blocks)) {
                $strblocksetup    = get_string("blocksetup");
                print_header($strblocksetup, $strblocksetup, $strblocksetup, "", "", false, "&nbsp;", "&nbsp;");
            }
            print_heading($block->name);
            $updated_blocks = true;
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL
            if (!is_dir("$fullblock/db/") || modify_database("$fullblock/db/$CFG->dbtype.sql")) {
                $db->debug = false;
                if ($block->id = insert_record('blocks', $block)) {
                    notify(get_string('blocksuccess', '', $blocktitle), 'green');
                    echo "<HR>";
                } else {
                    error("$block->name block could not be added to the block list!");
                }
            } else {
                error("Block $block->name tables could NOT be set up successfully!");
            }
        }

        $blocktitles[$block->name] = $blocktitle;
    }

    if(!empty($notices)) {
        foreach($notices as $notice) {
            notify($notice);
        }
    }

    //Finally, if we are in the first_install, update every course blockinfo field with
    //default values.
    if ($first_install) {
        //Iterate over each course
        if ($courses = get_records("course")) {
            foreach ($courses as $course) {
                //Dependig of the format, insert some values
                if ($course->format == "social") {
                    $blockinfo = blocks_get_default_blocks ($course->id, "participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,admin,course_list");
                } else {
                    //For topics and weeks formats (default built in the function)
                    $blockinfo = blocks_get_default_blocks($course->id);
                }
                if ($CFG->debug) {
                    echo 'Updating blockinfo for course: '.$course->shortname.'('.$blockinfo.')<br>';
                }
            }
        }
    }

    if (!empty($updated_blocks)) {
        print_continue($continueto);
        die;
    }
}

//This function returns the number of courses currently using the block
function blocks_get_courses_using_block_by_id($blockid) {

    $num = 0;

    if ($courses = get_records("course")) {
        foreach($courses as $course) {
            $blocks = str_replace(":",",",$course->blockinfo);
            $blocksarr = explode(",",$blocks);
            if (block_find($blockid,$blocksarr,array())) {
                $num++;
            }
        }
    }

    return $num;
}

//This function hides a block in all courses using it
function blocks_update_every_block_by_id($blockid,$action) {

    if ($courses = get_records("course")) {
        foreach($courses as $course) {
            //Calculate left and right blocks
            $blocks = $course->blockinfo;
            $delimpos = strpos($blocks, ':');

            if($delimpos === false) {
                // No ':' found, we have all left blocks
                $leftblocks = explode(',', $blocks);
                $rightblocks = array();
            } else if($delimpos === 0) {
                // ':' at start of string, we have all right blocks
                $blocks = substr($blocks, 1);
                $leftblocks = array();
                $rightblocks = explode(',', $blocks);
            }
            else {
                // Both left and right blocks
                $leftpart = substr($blocks, 0, $delimpos);
                $rightpart = substr($blocks, $delimpos + 1);
                $leftblocks = explode(',', $leftpart);
                $rightblocks = explode(',', $rightpart);
            }

            switch($action) {
                case 'show':
                    $block = block_find($blockid, $leftblocks, $rightblocks);
                    if($block !== false) {
                        if($block->side == BLOCK_LEFT) {
                            $leftblocks[$block->position] = abs($leftblocks[$block->position]);
                        }
                        else {
                            $rightblocks[$block->position] = abs($rightblocks[$block->position]);
                        }
                    }
                    break;
                case 'hide':
                    $block = block_find($blockid, $leftblocks, $rightblocks);
                    if($block !== false) {
                        if($block->side == BLOCK_LEFT) {
                            $leftblocks[$block->position] = -abs($leftblocks[$block->position]);
                        }
                        else {
                            $rightblocks[$block->position] = -abs($rightblocks[$block->position]);
                        }
                    }
                    break;
                case 'delete':
                    $block = block_find($blockid, $leftblocks, $rightblocks);
                    if($block !== false) {
                        if($block->side == BLOCK_LEFT) {
                            unset($leftblocks[$block->position]);
                        }
                        else {
                            unset($rightblocks[$block->position]);
                        }
                    }
                    break;
            }
            $course->blockinfo = implode(',', $leftblocks).':'.implode(',',$rightblocks);
            set_field('course', 'blockinfo', $course->blockinfo, 'id', $course->id);
        }
    }
}

// [pj] I didn't like the block_get_X_by_Y() functions because
//      we should be able to do without them with clever coding,
//      so I set out to see if they could be removed somehow.
//      Only block_get_default_blocks() depends on them, and that
//      one is used nowhere at the moment. So I 'm commenting
//      them out until a use IS found.
// [el] Uncommented to be used in the installation process, when
//      inserting new courses and when restoring courses. Perhaps
//      they can be modified, but previously related processes
//      will use them since now.

//This function returns the id of the block, searching it by name
function block_get_id_by_name ($blockname) {

    if ($block = get_record("blocks","name",$blockname)) {
        return $block->id;
    } else {
        return 0;
    }
}

//This function returns the name of the block, searching it by id
function block_get_name_by_id ($blockid) {

    if ($block = get_record("blocks","id",$blockid)) {
        return $block->name;
    } else {
        return NULL;
    }
}

//This function return the necessary contents to update course->blockinfo
//with default values. It accepts a list of block_names as parameter. They
//will be converted to their blockids equivalent. If a course is specified
//then the function will update the field too!

function blocks_get_default_blocks ($courseid = NULL, $blocknames="") {
 
    global $CFG;

    if (empty($blocknames)) {
        if (!empty($CFG->defaultblocks)) {
            $blocknames = $CFG->defaultblocks;
        } else {
            $blocknames = "participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity";
        }
    }

    //Calculate left and right blocks
    $blocksn = $blocknames;
    $delimpos = strpos($blocksn, ':');

    if($delimpos === false) {
        // No ':' found, we have all left blocks
        $leftblocksn = explode(',', $blocksn);
        $rightblocksn = array();
    } else if($delimpos === 0) {
        // ':' at start of string, we have all right blocks
        $blocksn = substr($blocksn, 1);
        $leftblocksn = array();
        $rightblocksn = explode(',', $blocksn);
    }
    else {
        // Both left and right blocks
        $leftpartn = substr($blocksn, 0, $delimpos);
        $rightpartn = substr($blocksn, $delimpos + 1);
        $leftblocksn = explode(',', $leftpartn);
        $rightblocksn = explode(',', $rightpartn);
    }

    //Now I have blocks separated

    $leftblocks = array();
    $rightblocks = array();

    if ($leftblocksn) {
        foreach($leftblocksn as $leftblockn) {
            //Convert blockname to id
            $leftblock = block_get_id_by_name(str_replace("-","",$leftblockn));
            if ($leftblock) {
                //Check it's visible
                if($block = get_record("blocks","id",$leftblock,"visible","1")) {
                    //Check if the module was hidden at course level
                    if (substr($leftblockn,0,1) == "-") {
                        $leftblocks[] = -$leftblock;
                    } else  {
                        $leftblocks[] = $leftblock;
                    }
                }
            }
        }
    }

    if ($rightblocksn) {
        foreach($rightblocksn as $rightblockn) {
            //Convert blockname to id
            $rightblock = block_get_id_by_name(str_replace("-","",$rightblockn));
            if ($rightblock) {
                //Check it's visible
                if($block = get_record("blocks","id",$rightblock,"visible","1")) {
                    //Check if the module was hidden at course level
                    if (substr($rightblockn,0,1) == "-") {
                        $rightblocks[] = -$rightblock;
                    } else {
                        $rightblocks[] = $rightblock;
                    }
                }
            }
        }
    }

    //Calculate the blockinfo field
    if ($leftblocks || $rightblocks) {
        $blockinfo = '';
        if ($leftblocks) {
            $blockinfo .= implode(",", $leftblocks);
        }
        if ($rightblocks) {
            $blockinfo .= ':'.implode(",",$rightblocks);
        }
    } else {
        $blockinfo = '';
    }

    //If a course has been specified, update it
    if ($courseid) {
        set_field('course', "blockinfo", $blockinfo, "id", $courseid);
    }

    //Returns the blockinfo
    return $blockinfo;
}

//This function will return the names representation of the blockinfo field.
//It's used to include that info in backups. To restore we'll use the
//blocks_get_block_ids() function. It makes the opposite conversion
//(from names to ids)
function blocks_get_block_names ($blockinfo) {

    //Calculate left and right blocks
    $blocksn = $blockinfo;
    $delimpos = strpos($blocksn, ':');

    if($delimpos === false) {
        // No ':' found, we have all left blocks
        $leftblocksn = explode(',', $blocksn);
        $rightblocksn = array();
    } else if($delimpos === 0) {
        // ':' at start of string, we have all right blocks
        $blocksn = substr($blocksn, 1);
        $leftblocksn = array();
        $rightblocksn = explode(',', $blocksn);
    }
    else {
        // Both left and right blocks
        $leftpartn = substr($blocksn, 0, $delimpos);
        $rightpartn = substr($blocksn, $delimpos + 1);
        $leftblocksn = explode(',', $leftpartn);
        $rightblocksn = explode(',', $rightpartn);
    }

    //Now I have blocks separated

    $leftblocks = array();
    $rightblocks = array();

    if ($leftblocksn) {
        foreach($leftblocksn as $leftblockn) {
            //Convert id to blockname
            $leftblock = block_get_name_by_id(abs($leftblockn));
            if ($leftblock) {
                //Check it's visible
                if($block = get_record("blocks","name",$leftblock,"visible","1")) {
                    //Check if it's hidden oe no in the course
                    if($leftblockn<0) {
                        $leftblocks[] = '-'.$leftblock;
                    } else {
                        $leftblocks[] = $leftblock;
                    }
                }
            }
        }
    }

    if ($rightblocksn) {
        foreach($rightblocksn as $rightblockn) {
            //Convert id to blockname
            $rightblock = block_get_name_by_id(abs($rightblockn));
            if ($rightblock) {
                //Check it's visible
                if($block = get_record("blocks","name",$rightblock,"visible","1")) {
                    //Check if it's hidden oe no in the course
                    if($rightblockn<0) {
                        $rightblocks[] = '-'.$rightblock;
                    } else {
                        $rightblocks[] = $rightblock;
                    }
                }
            }
        }
    }

    //Calculate the blockinfo field
    if ($leftblocks || $rightblocks) {
        $blockinfo = '';
        if ($leftblocks) {
            $blockinfo .= implode(",", $leftblocks);
        }
        if ($rightblocks) {
            $blockinfo .= ':'.implode(",",$rightblocks);
        }
    } else {
        $blockinfo = '';
    }

    //Returns the blockinfo
    return $blockinfo;
}

//This function will return the ids representation of the blockinfo field.
//It's used to load that info from backups.  This function is the opposite
//to the blocks_get_block_names() used in backup
function blocks_get_block_ids ($blockinfo) {

    //Just call this with the appropiate parammeters.
    return blocks_get_default_blocks(NULL,$blockinfo);
}

// This is used to register the blocks that are displayed in the course page.
// Set in course/view.php, and read from any other place.
function blocks_used($blocks = NULL, $records = NULL) {
    static $used = NULL;

    if(!empty($blocks) && !empty($records)) {
        $used = array();
        foreach($blocks as $val) {
            if($val > 0 && isset($records[$val])) {
                $used[] = $records[$val]->name;
            }
        }
    }

    return $used;
}

?>
