<?php //$Id$

//This library includes all the necessary stuff to use blocks in course pages

define('BLOCK_MOVE_LEFT',   0x01);
define('BLOCK_MOVE_RIGHT',  0x02);
define('BLOCK_MOVE_UP',     0x04);
define('BLOCK_MOVE_DOWN',   0x08);
define('BLOCK_CONFIGURE',   0x10);

define('BLOCK_POS_LEFT',  'l');
define('BLOCK_POS_RIGHT', 'r');

define('BLOCKS_PINNED_TRUE',0);
define('BLOCKS_PINNED_FALSE',1);
define('BLOCKS_PINNED_BOTH',2);

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot.'/course/lib.php'); // needed to solve all those: Call to undefined function: print_recent_activity() when adding Recent Activity

// Returns false if this block is incompatible with the current version of Moodle.
function block_is_compatible($blockname) {
    global $CFG;

    $file = @file($CFG->dirroot.'/blocks/'.$blockname.'/block_'.$blockname.'.php'); // ignore errors when file does not exist
    if(empty($file)) {
        return NULL;
    }

    foreach($file as $line) {
        // If you find MoodleBlock (appearing in the class declaration) it's not compatible
        if(strpos($line, 'MoodleBlock')) {
            return false;
        }
        // But if we find a { it means the class declaration is over, so it's compatible
        else if(strpos($line, '{')) {
            return true;
        }
    }

    return NULL;
}

// Returns the case-sensitive name of the class' constructor function. This includes both
// PHP5- and PHP4-style constructors. If no appropriate constructor can be found, returns NULL.
// If there is no such class, returns boolean false.
function get_class_constructor($classname) {
    // Caching
    static $constructors = array();

    if(!class_exists($classname)) {
        return false;
    }

    // Tests indicate this doesn't hurt even in PHP5.
    $classname = strtolower($classname);

    // Return cached value, if exists
    if(isset($constructors[$classname])) {
        return $constructors[$classname];
    }

    // Get a list of methods. After examining several different ways of
    // doing the check, (is_callable, method_exists, function_exists etc)
    // it seems that this is the most reliable one.
    $methods = get_class_methods($classname);

    // PHP5 constructor?
    if(phpversion() >= '5') {
        if(in_array('__construct', $methods)) {
            return $constructors[$classname] = '__construct';
        }
    }

    // If we have PHP5 but no magic constructor, we have to lowercase the methods
    $methods = array_map('strtolower', $methods);

    if(in_array($classname, $methods)) {
        return $constructors[$classname] = $classname;
    }

    return $constructors[$classname] = NULL;
}

//This function retrieves a method-defined property of a class WITHOUT instantiating an object
function block_method_result($blockname, $method, $param = NULL) {
    if(!block_load_class($blockname)) {
        return NULL;
    }
    return call_user_func(array('block_'.$blockname, $method), $param);
}

//This function creates a new object of the specified block class
function block_instance($blockname, $instance = NULL) {
    if(!block_load_class($blockname)) {
        return false;
    }
    $classname = 'block_'.$blockname;
    $retval = new $classname;
    if($instance !== NULL) {
        $retval->_load_instance($instance);
    }
    return $retval;
}

//This function loads the necessary class files for a block
//Whenever you want to load a block, use this first
function block_load_class($blockname) {
    global $CFG;

    if(empty($blockname)) {
        return false;
    }

    $classname = 'block_'.$blockname;

    if(class_exists($classname)) {
        return true;
    }

    require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    @include_once($CFG->dirroot.'/blocks/'.$blockname.'/block_'.$blockname.'.php'); // do not throw errors if block code not present

    return class_exists($classname);
}

// This function returns an array with the IDs of any blocks that you can add to your page.
// Parameters are passed by reference for speed; they are not modified at all.
function blocks_get_missing(&$page, &$pageblocks) {

    $missingblocks = array();
    $allblocks = blocks_get_record();
    $pageformat = $page->get_format_name();

    if(!empty($allblocks)) {
        foreach($allblocks as $block) {
            if($block->visible && (!blocks_find_block($block->id, $pageblocks) || $block->multiple)) {
                // And if it's applicable for display in this format...
                if(blocks_name_allowed_in_format($block->name, $pageformat)) {
                    // ...add it to the missing blocks
                    $missingblocks[] = $block->id;
                }
            }
        }
    }
    return $missingblocks;
}

function blocks_remove_inappropriate($page) {
    $pageblocks = blocks_get_by_page($page);

    if(empty($pageblocks)) {
        return;
    }

    if(($pageformat = $page->get_format_name()) == NULL) {
        return;
    }

    foreach($pageblocks as $position) {
        foreach($position as $instance) {
            $block = blocks_get_record($instance->blockid);
            if(!blocks_name_allowed_in_format($block->name, $pageformat)) {
               blocks_delete_instance($instance);
            }
        }
    }
}

function blocks_name_allowed_in_format($name, $pageformat) {

    $accept  = NULL;
    $depth   = -1;
    if ($formats = block_method_result($name, 'applicable_formats')) {
        foreach($formats as $format => $allowed) {
            $thisformat = '^'.str_replace('*', '[^-]*', $format).'.*$';
            if(ereg($thisformat, $pageformat)) {
                if(($scount = substr_count($format, '-')) > $depth) {
                    $depth  = $scount;
                    $accept = $allowed;
                }
            }
        }
    }
    if($accept === NULL) {
        $accept = !empty($formats['all']);
    }
    return $accept;
}

function blocks_delete_instance($instance,$pinned=false) {
    global $CFG;

    // Get the block object and call instance_delete() if possible
    if($record = blocks_get_record($instance->blockid)) {
        if($obj = block_instance($record->name, $instance)) {
            // Return value ignored
            $obj->instance_delete();
        }
    }

    if (!empty($pinned)) {
         delete_records('block_pinned', 'id', $instance->id);
        // And now, decrement the weight of all blocks after this one
        execute_sql('UPDATE '.$CFG->prefix.'block_pinned SET weight = weight - 1 WHERE pagetype = \''.$instance->pagetype.
                    '\' AND position = \''.$instance->position.
                    '\' AND weight > '.$instance->weight, false);
    } else {
        // Now kill the db record;
        delete_records('block_instance', 'id', $instance->id);
        delete_context(CONTEXT_BLOCK, $instance->id);
        // And now, decrement the weight of all blocks after this one
        execute_sql('UPDATE '.$CFG->prefix.'block_instance SET weight = weight - 1 WHERE pagetype = \''.$instance->pagetype.
                    '\' AND pageid = '.$instance->pageid.' AND position = \''.$instance->position.
                    '\' AND weight > '.$instance->weight, false);
    }
    return true;
}

// Accepts an array of block instances and checks to see if any of them have content to display
// (causing them to calculate their content in the process). Returns true or false. Parameter passed
// by reference for speed; the array is actually not modified.
function blocks_have_content(&$pageblocks, $position) {

    if (empty($pageblocks) || !is_array($pageblocks) || !array_key_exists($position,$pageblocks)) {
        return false;
    }
    // use a for() loop to get references to the array elements
    // foreach() cannot fetch references in PHP v4.x
    for ($n=0; $n<count($pageblocks[$position]);$n++) {
        $instance = &$pageblocks[$position][$n];
        if (empty($instance->visible)) {
            continue;
        }
        if(!$record = blocks_get_record($instance->blockid)) {
            continue;
        }
        if(!$obj = block_instance($record->name, $instance)) {
            continue;
        }
        if(!$obj->is_empty()) {
            // cache rec and obj
            // for blocks_print_group()
            $instance->rec = $record;
            $instance->obj = $obj;
            return true;
        }
    }

    return false;
}

// This function prints one group of blocks in a page
// Parameters passed by reference for speed; they are not modified.
function blocks_print_group(&$page, &$pageblocks, $position) {
    global $COURSE, $CFG, $USER;
    $isediting = $page->user_is_editing();
    
    if (empty($pageblocks[$position])) {
        $groupblocks = array();
        $maxweight = 0;                        
    } else {
        $groupblocks = $pageblocks[$position];
        $maxweight = max(array_keys($groupblocks));        
    }
    if (!empty($CFG->ajaxcapable) && $CFG->ajaxcapable && !empty($COURSE->javascriptportal) && $isediting) {    
        $COURSE->javascriptportal->currentblocksection = $position;
        $COURSE->javascriptportal->block_add($position.'inst0', FALSE);
    }
    
    foreach ($groupblocks as $instance) {
        if (!empty($instance->pinned)) {
            $maxweight--;
        }
    }

    foreach($groupblocks as $instance) {
        // $instance may have ->rec and ->obj
        // cached from when we walked $pageblocks
        // in blocks_have_content()
        if (empty($instance->rec)) {
            if (empty($instance->blockid)) {
                continue;   // Can't do anything
            }
            $block = blocks_get_record($instance->blockid);
        } else {
            $block = $instance->rec;
        }

        if (empty($block)) {
            // Block doesn't exist! We should delete this instance!
            continue;
        }

        if (empty($block->visible)) {
            // Disabled by the admin
            continue;
        }

        if (empty($instance->obj)) {
            if (!$obj = block_instance($block->name, $instance)) {
                // Invalid block
                continue;
            }
        } else {
            $obj = $instance->obj;
        }

        $editalways = $page->edit_always();


        if (($isediting  && empty($instance->pinned)) || !empty($editalways)) {
            $options = 0;
            // The block can be moved up if it's NOT the first one in its position. If it is, we look at the OR clause:
            // the first block might still be able to move up if the page says so (i.e., it will change position)
            $options |= BLOCK_MOVE_UP    * ($instance->weight != 0          || ($page->blocks_move_position($instance, BLOCK_MOVE_UP)   != $instance->position));
            // Same thing for downward movement
            $options |= BLOCK_MOVE_DOWN  * ($instance->weight != $maxweight || ($page->blocks_move_position($instance, BLOCK_MOVE_DOWN) != $instance->position));
            // For left and right movements, it's up to the page to tell us whether they are allowed
            $options |= BLOCK_MOVE_RIGHT * ($page->blocks_move_position($instance, BLOCK_MOVE_RIGHT) != $instance->position);
            $options |= BLOCK_MOVE_LEFT  * ($page->blocks_move_position($instance, BLOCK_MOVE_LEFT ) != $instance->position);
            // Finally, the block can be configured if the block class either allows multiple instances, or if it specifically
            // allows instance configuration (multiple instances override that one). It doesn't have anything to do with what the
            // administrator has allowed for this block in the site admin options.
            $options |= BLOCK_CONFIGURE * ( $obj->instance_allow_multiple() || $obj->instance_allow_config() );
            $obj->_add_edit_controls($options);
        }

        if (!$instance->visible && empty($COURSE->javascriptportal)) {
            if ($isediting) {
                $obj->_print_shadow();
            }
        } else { 
            global $COURSE;
            if(!empty($COURSE->javascriptportal)) {
                 $COURSE->javascriptportal->currentblocksection = $position;
            }
            $obj->_print_block();
        }
        if (!empty($COURSE->javascriptportal)
                    && (empty($instance->pinned) || !$instance->pinned)) { 
            $COURSE->javascriptportal->block_add('inst'.$instance->id, !$instance->visible);
        }         
    } // End foreach
    
    //  Check if
    //    we are on the default position/side AND
    //    we're editing the page AND
    //    (
    //      we have the capability to manage blocks OR
    //      we are in myMoodle page AND have the capibility to manage myMoodle blocks
    //    )

    // for constant PAGE_MY_MOODLE
    include_once($CFG->dirroot.'/my/pagelib.php');

    $coursecontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    $myownblogpage = (isset($page->filtertype) && isset($page->filterselect) && $page->type=='blog-view' && $page->filtertype=='user' && $page->filterselect == $USER->id);

    $managecourseblocks = has_capability('moodle/site:manageblocks', $coursecontext);
    $editmymoodle = $page->type == PAGE_MY_MOODLE && has_capability('moodle/my:manageblocks', $coursecontext);

    if ($page->blocks_default_position() == $position &&
        $page->user_is_editing() &&
        ($managecourseblocks || $editmymoodle || $myownblogpage || defined('ADMIN_STICKYBLOCKS'))) {
                
        print_side_block(NULL,NULL, NULL, NULL, NULL, array('id'=> BLOCK_POS_RIGHT.'inst0', 'class'=>'tempblockhandler'));
        blocks_print_adminblock($page, $pageblocks);
    } else if ($page->user_is_editing() &&
        ($managecourseblocks || $editmymoodle || $myownblogpage || defined('ADMIN_STICKYBLOCKS'))) {
        print_side_block(NULL,NULL, NULL, NULL, NULL, array('id'=> BLOCK_POS_LEFT.'inst0', 'class'=>'tempblockhandler'));
    }
}

// This iterates over an array of blocks and calculates the preferred width
// Parameter passed by reference for speed; it's not modified.
function blocks_preferred_width(&$instances) {
    $width = 0;

    if(empty($instances) || !is_array($instances)) {
        return 0;
    }

    $blocks = blocks_get_record();

    foreach($instances as $instance) {
        if(!$instance->visible) {
            continue;
        }

        if (!array_key_exists($instance->blockid, $blocks)) {
            // Block doesn't exist! We should delete this instance!
            continue;
        }

        if(!$blocks[$instance->blockid]->visible) {
            continue;
        }
        $pref = block_method_result($blocks[$instance->blockid]->name, 'preferred_width');
        if($pref === NULL) {
            continue;
        }
        if($pref > $width) {
            $width = $pref;
        }
    }
    return $width;
}

function blocks_get_record($blockid = NULL, $invalidate = false) {
    static $cache = NULL;

    if($invalidate || empty($cache)) {
        $cache = get_records('block');
    }

    if($blockid === NULL) {
        return $cache;
    }

    return (isset($cache[$blockid])? $cache[$blockid] : false);
}

function blocks_find_block($blockid, $blocksarray) {
    if (empty($blocksarray)) {
        return false;
    }
    foreach($blocksarray as $blockgroup) {
        if (empty($blockgroup)) {
            continue;
        }
        foreach($blockgroup as $instance) {
            if($instance->blockid == $blockid) {
                return $instance;
            }
        }
    }
    return false;
}

function blocks_find_instance($instanceid, $blocksarray) {
    foreach($blocksarray as $subarray) {
        foreach($subarray as $instance) {
            if($instance->id == $instanceid) {
                return $instance;
            }
        }
    }
    return false;
}

// Simple entry point for anyone that wants to use blocks
function blocks_setup(&$PAGE,$pinned=BLOCKS_PINNED_FALSE) {
    switch ($pinned) {
    case BLOCKS_PINNED_TRUE:
        $pageblocks = blocks_get_pinned($PAGE);
        break;
    case BLOCKS_PINNED_BOTH:
        $pageblocks = blocks_get_by_page_pinned($PAGE);
        break;
    case BLOCKS_PINNED_FALSE:
    default:
        $pageblocks = blocks_get_by_page($PAGE);
        break;
    }
    blocks_execute_url_action($PAGE, $pageblocks,($pinned==BLOCKS_PINNED_TRUE));
    return $pageblocks;
}

function blocks_execute_action($page, &$pageblocks, $blockaction, $instanceorid, $pinned=false, $redirect=true) {
    global $CFG;

    if (is_int($instanceorid)) {
        $blockid = $instanceorid;
    } else if (is_object($instanceorid)) {
        $instance = $instanceorid;
    }

    switch($blockaction) {
        case 'config':
            global $USER;
            $block = blocks_get_record($instance->blockid);
            // Hacky hacky tricky stuff to get the original human readable block title,
            // even if the block has configured its title to be something else.
            // Create the object WITHOUT instance data.
            $blockobject = block_instance($block->name);
            if ($blockobject === false) {
                break;
            }

            // First of all check to see if the block wants to be edited
            if(!$blockobject->user_can_edit()) {
                break;
            }

            // Now get the title and AFTER that load up the instance
            $blocktitle = $blockobject->get_title();
            $blockobject->_load_instance($instance);

            optional_param('submitted', 0, PARAM_INT);

            // Define the data we're going to silently include in the instance config form here,
            // so we can strip them from the submitted data BEFORE serializing it.
            $hiddendata = array(
                'sesskey' => $USER->sesskey,
                'instanceid' => $instance->id,
                'blockaction' => 'config'
            );

            // To this data, add anything the page itself needs to display
            $hiddendata = array_merge($hiddendata, $page->url_get_parameters());

            if ($data = data_submitted()) {
                $remove = array_keys($hiddendata);
                foreach($remove as $item) {
                    unset($data->$item);
                }
                if(!$blockobject->instance_config_save($data,$pinned)) {
                    error('Error saving block configuration');
                }
                // And nothing more, continue with displaying the page
            }
            else {
                // We need to show the config screen, so we highjack the display logic and then die
                $strheading = get_string('blockconfiga', 'moodle', $blocktitle);
                $page->print_header(get_string('pageheaderconfigablock', 'moodle'), array($strheading => ''));

                echo '<div class="block-config" id="'.$block->name.'">';   /// Make CSS easier

                print_heading($strheading);
                echo '<form method="post" name="block-config" action="'. $page->url_get_path() .'">';
                echo '<p>';
                foreach($hiddendata as $name => $val) {
                    echo '<input type="hidden" name="'. $name .'" value="'. $val .'" />';
                }
                echo '</p>';
                $blockobject->instance_config_print();
                echo '</form>';

                echo '</div>';
                $CFG->pagepath = 'blocks/' . $block->name;
                print_footer();
                die(); // Do not go on with the other page-related stuff
            }
        break;
        case 'toggle':
            if(empty($instance))  {
                error('Invalid block instance for '.$blockaction);
            }
            $instance->visible = ($instance->visible) ? 0 : 1;
            if (!empty($pinned)) {
                update_record('block_pinned', $instance);
            } else {
                update_record('block_instance', $instance);
            }
        break;
        case 'delete':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }
            blocks_delete_instance($instance, $pinned);
        break;
        case 'moveup':
            if (empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }

            if ($instance->weight == 0) {
                // The block is the first one, so a move "up" probably means it changes position
                // Where is the instance going to be moved?
                $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_UP);
                $newweight = (empty($pageblocks[$newpos]) ? 0 : max(array_keys($pageblocks[$newpos])) + 1);

                blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
            } else {
                // The block is just moving upwards in the same position.
                // This configuration will make sure that even if somehow the weights
                // become not continuous, block move operations will eventually bring
                // the situation back to normal without printing any warnings.               
                if (!empty($pageblocks[$instance->position][$instance->weight - 1])) {
                    //define instance's position in the array
                    foreach ($pageblocks[$instance->position] as $instancekeysindex => $index ){
                        if ($pageblocks[$instance->position][$instancekeysindex]->id == $instance->id){
                            $instanceindex = $instancekeysindex;
                        }
                    }
                    $other = $pageblocks[$instance->position][$instanceindex - 1];
                }
                if (!empty($other)) {
                    ++$other->weight;
                    if (!empty($pinned)) {         
                        update_record('block_pinned', $other);
                    } else {                       
                        update_record('block_instance', $other);
                    }
                }
                --$instance->weight;
                if (!empty($pinned)) {
                    update_record('block_pinned', $instance);
                } else {
                    update_record('block_instance', $instance);
                }                
            }
        break;
        case 'movedown':
            if (empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }
            if ($instance->weight == max(array_keys($pageblocks[$instance->position]))) {
                // The block is the last one, so a move "down" probably means it changes position
                // Where is the instance going to be moved?
                $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_DOWN);
                $newweight = (empty($pageblocks[$newpos]) ? 0 : max(array_keys($pageblocks[$newpos])) + 1);

                blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
            }
            else {
                // The block is just moving downwards in the same position.
                // This configuration will make sure that even if somehow the weights
                // become not continuous, block move operations will eventually bring
                // the situation back to normal without printing any warnings.
                if (!empty($pageblocks[$instance->position][$instance->weight + 1])) {
                    //define instance's position in the array
                    foreach ($pageblocks[$instance->position] as $instancekeysindex => $index ){
                        if ($pageblocks[$instance->position][$instancekeysindex]->id == $instance->id){
                            $instanceindex = $instancekeysindex;
                        }
                    }
                    $other = $pageblocks[$instance->position][$instanceindex + 1];
                }                
                if (!empty($other)) {
                    --$other->weight;
                    if (!empty($pinned)) {
                        update_record('block_pinned', $other);
                    } else {
                        update_record('block_instance', $other);
                    }
                }
                ++$instance->weight;
                if (!empty($pinned)) {
                    update_record('block_pinned', $instance);
                } else {
                    update_record('block_instance', $instance);
                }                
            }
        break;
        case 'moveleft':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }
            // Where is the instance going to be moved?
            $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_LEFT);
            $newweight = 0;

            if (!empty($pinned) && !empty($pageblocks[$newpos]) ){
                $newweight = $pageblocks[$newpos][max(array_keys($pageblocks[$newpos])) ]->weight + 1;            
            } else if(!empty($pageblocks[$newpos]) && (!array_key_exists('pinned', $pageblocks[$newpos][max(array_keys($pageblocks[$newpos]))])) ){
                $newweight = $pageblocks[$newpos][max(array_keys($pageblocks[$newpos])) ]->weight + 1;                
            }            
            blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
        break;
        case 'moveright':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }

            // Where is the instance going to be moved?
            $newpos    = $page->blocks_move_position($instance, BLOCK_MOVE_RIGHT);
            $newweight = 0; 

            if (!empty($pinned) && !empty($pageblocks[$newpos]) ){
                $newweight = $pageblocks[$newpos][max(array_keys($pageblocks[$newpos])) ]->weight + 1;
            }else if(!empty($pageblocks[$newpos]) && (!array_key_exists('pinned', $pageblocks[$newpos][max(array_keys($pageblocks[$newpos]))])) ){
                $newweight = $pageblocks[$newpos][max(array_keys($pageblocks[$newpos])) ]->weight + 1;
            }
            blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
        break;
        case 'add':
            // Add a new instance of this block, if allowed
            $block = blocks_get_record($blockid);

            if(empty($block) || !$block->visible) {
                // Only allow adding if the block exists and is enabled
                break;
            }

            if(!$block->multiple && blocks_find_block($blockid, $pageblocks) !== false) {
                // If no multiples are allowed and we already have one, return now
                break;
            }

            if(!block_method_result($block->name, 'user_can_addto', $page)) {
                // If the block doesn't want to be added...
                break;
            }

            $newpos = $page->blocks_default_position();
            if (!empty($pinned)) {
                $sql = 'SELECT 1, max(weight) + 1 AS nextfree FROM '. $CFG->prefix .'block_pinned WHERE '
                    .' pagetype = \''. $page->get_type() .'\' AND position = \''. $newpos .'\'';
            } else {
                $sql = 'SELECT 1, max(weight) + 1 AS nextfree FROM '. $CFG->prefix .'block_instance WHERE pageid = '. $page->get_id()
                    .' AND pagetype = \''. $page->get_type() .'\' AND position = \''. $newpos .'\'';
            }
            $weight = get_record_sql($sql);

            $newinstance = new stdClass;
            $newinstance->blockid    = $blockid;
            if (empty($pinned)) {
                $newinstance->pageid = $page->get_id();
            }
            $newinstance->pagetype   = $page->get_type();
            $newinstance->position   = $newpos;
            $newinstance->weight     = empty($weight->nextfree) ? 0 : $weight->nextfree;
            $newinstance->visible    = 1;
            $newinstance->configdata = '';
            if (!empty($pinned)) {
                $newinstance->id = insert_record('block_pinned', $newinstance);
            } else {
                $newinstance->id = insert_record('block_instance', $newinstance);
            }

            // If the new instance was created, allow it to do additional setup
            if($newinstance && ($obj = block_instance($block->name, $newinstance))) {
                // Return value ignored
                $obj->instance_create();
            }

        break;
    }

    if ($redirect) { 
        // In order to prevent accidental duplicate actions, redirect to a page with a clean url
        redirect($page->url_get_full());
    }
}

// You can use this to get the blocks to respond to URL actions without much hassle
function blocks_execute_url_action(&$PAGE, &$pageblocks,$pinned=false) {
    $blockaction = optional_param('blockaction', '', PARAM_ALPHA);
    
    if (empty($blockaction) || !$PAGE->user_allowed_editing() || !confirm_sesskey()) {
        return;
    }

    $instanceid  = optional_param('instanceid', 0, PARAM_INT);
    $blockid     = optional_param('blockid',    0, PARAM_INT);

    if (!empty($blockid)) {
        blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), $blockid, $pinned);

    }
    else if (!empty($instanceid)) {
        $instance = blocks_find_instance($instanceid, $pageblocks);
        blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), $instance, $pinned);
    }
}

// This shouldn't be used externally at all, it's here for use by blocks_execute_action()
// in order to reduce code repetition.
function blocks_execute_repositioning(&$instance, $newpos, $newweight, $pinned=false) {
    global $CFG;

    // If it's staying where it is, don't do anything, unless overridden
    if ($newpos == $instance->position) {
        return;
    }

    // Close the weight gap we 'll leave behind
    if (!empty($pinned)) {        
        $sql = 'UPDATE '. $CFG->prefix .'block_pinned SET weight = weight - 1 '.
                        'WHERE pagetype = \''. $instance->pagetype.
                        '\' AND position = \'' .$instance->position.
                        '\' AND weight > '. $instance->weight;
    } else {
        $sql = 'UPDATE '. $CFG->prefix .'block_instance SET weight = weight - 1 '.
                        'WHERE pagetype = \''. $instance->pagetype.
                        '\' AND pageid = '. $instance->pageid .
                        ' AND position = \'' .$instance->position.
                        '\' AND weight > '. $instance->weight;
    }
    execute_sql($sql,false);

    $instance->position = $newpos;
    $instance->weight   = $newweight;

    if (!empty($pinned)) {
        update_record('block_pinned', $instance);
    } else {
        update_record('block_instance', $instance);
    }
}


/**
 * Moves a block to the new position (column) and weight (sort order).
 * @param $instance - The block instance to be moved.
 * @param $destpos - BLOCK_POS_LEFT or BLOCK_POS_RIGHT. The destination column.
 * @param $destweight - The destination sort order. If NULL, we add to the end
 *                      of the destination column.
 * @param $pinned - Are we moving pinned blocks? We can only move pinned blocks
 *                  to a new position withing the pinned list. Likewise, we
 *                  can only moved non-pinned blocks to a new position within
 *                  the non-pinned list.
 * @return boolean (success or failure).
 */
function blocks_move_block($page, &$instance, $destpos, $destweight=NULL, $pinned=false) {
    global $CFG;

    if ($pinned) {
        $blocklist = blocks_get_pinned($page);
    } else {
        $blocklist = blocks_get_by_page($page);
    }

    if ($blocklist[$instance->position][$instance->weight]->id != $instance->id) {
        // The source block instance is not where we think it is.
        return false;
    }

    // First we close the gap that will be left behind when we take out the
    // block from it's current column.
    if ($pinned) {
        $closegapsql = "UPDATE {$CFG->prefix}block_pinned
                           SET weight = weight - 1
                         WHERE weight > '$instance->weight'
                           AND position = '$instance->position'
                           AND pagetype = '$instance->pagetype'";
    } else {
        $closegapsql = "UPDATE {$CFG->prefix}block_instance
                           SET weight = weight - 1
                         WHERE weight > '$instance->weight'
                           AND position = '$instance->position'
                           AND pagetype = '$instance->pagetype'
                           AND pageid = '$instance->pageid'";
    }
    if (!execute_sql($closegapsql, false)) {
        return false;
    }

    // Now let's make space for the block being moved.
    if ($pinned) {
        $opengapsql = "UPDATE {$CFG->prefix}block_pinned
                           SET weight = weight + 1
                         WHERE weight >= '$destweight'
                           AND position = '$destpos'
                           AND pagetype = '$instance->pagetype'";
    } else {
        $opengapsql = "UPDATE {$CFG->prefix}block_instance
                           SET weight = weight + 1
                         WHERE weight >= '$destweight'
                           AND position = '$destpos'
                           AND pagetype = '$instance->pagetype'
                           AND pageid = '$instance->pageid'";
    }
    if (!execute_sql($opengapsql, false)) {
        return false;
    }

    // Move the block.
    $instance->position = $destpos;
    $instance->weight   = $destweight;

    if ($pinned) {
        $table = 'block_pinned';
    } else {
        $table = 'block_instance';
    }
    return update_record($table, $instance);
}


/**
 * Returns an array consisting of 2 arrays:
 * 1) Array of pinned blocks for position BLOCK_POS_LEFT
 * 2) Array of pinned blocks for position BLOCK_POS_RIGHT
 */
function blocks_get_pinned($page) {

    $visible = true;

    if (method_exists($page,'edit_always')) {
        if ($page->edit_always()) {
            $visible = false;
        }
    }

    $blocks = get_records_select('block_pinned', 'pagetype = \''. $page->get_type() .
                    '\''.(($visible) ? 'AND visible = 1' : ''), 'position, weight');

    $positions = $page->blocks_get_positions();
    $arr = array();

    foreach($positions as $key => $position) {
        $arr[$position] = array();
    }

    if(empty($blocks)) {
        return $arr;
    }

    foreach($blocks as $block) {
        $block->pinned = true; // so we know we can't move it.
        // make up an instanceid if we can..
        $block->pageid = $page->get_id();
        $arr[$block->position][$block->weight] = $block;
    }

    return $arr;
}


/**
 * Similar to blocks_get_by_page(), except that, the array returned includes
 * pinned blocks as well. Pinned blocks are always appended before normal
 * block instances.
 */
function blocks_get_by_page_pinned($page) {
    $pinned = blocks_get_pinned($page);
    $user = blocks_get_by_page($page);

    $weights = array();

    foreach ($pinned as $pos => $arr) {
        $weights[$pos] = count($arr);
    }

    foreach ($user as $pos => $blocks) {
        if (!array_key_exists($pos,$pinned)) {
             $pinned[$pos] = array();
        }
        if (!array_key_exists($pos,$weights)) {
            $weights[$pos] = 0;
        }
        foreach ($blocks as $block) {
            $pinned[$pos][$weights[$pos]] = $block;
            $weights[$pos]++;
        }
    }
    return $pinned;
}


/**
 * Returns an array of blocks for the page. Pinned blocks are excluded.
 */
function blocks_get_by_page($page) {
    $blocks = get_records_select('block_instance', "pageid = '". $page->get_id() .
                "' AND pagetype = '". $page->get_type() ."'", 'position, weight');

    $positions = $page->blocks_get_positions();
    $arr = array();
    foreach($positions as $key => $position) {
        $arr[$position] = array();
    }

    if(empty($blocks)) {
        return $arr;
    }

    foreach($blocks as $block) {
        $arr[$block->position][$block->weight] = $block;
    }
    return $arr;
}


//This function prints the block to admin blocks as necessary
function blocks_print_adminblock(&$page, &$pageblocks) {
    global $USER;

    $missingblocks = blocks_get_missing($page, $pageblocks);
    
    if (!empty($missingblocks)) {
        $strblocks = '<div class="title"><h2>';
        $strblocks .= get_string('blocks');
        $strblocks .= '</h2></div>';
        $stradd    = get_string('add');
        foreach ($missingblocks as $blockid) {
            $block = blocks_get_record($blockid);
            $blockobject = block_instance($block->name);
            if ($blockobject === false) {
                continue;
            }
            if(!$blockobject->user_can_addto($page)) {
                continue;
            }
            $menu[$block->id] = $blockobject->get_title();
        }
        asort($menu);

        $target = $page->url_get_full(array('sesskey' => $USER->sesskey, 'blockaction' => 'add'));
        $content = popup_form($target.'&amp;blockid=', $menu, 'add_block', '', $stradd .'...', '', '', true);       
        print_side_block($strblocks, $content, NULL, NULL, NULL, array('class' => 'block_adminblock'));
    }
}

/**
 * Delete all the blocks from a particular page.
 *
 * @param string $pagetype the page type.
 * @param integer $pageid the page id.
 * @return success of failure.
 */
function blocks_delete_all_on_page($pagetype, $pageid) {
    if ($instances = get_records_select('block_instance', "pageid = $pageid AND pagetype = '$pagetype'")) {
        foreach ($instances as $instance) {
            delete_context(CONTEXT_BLOCK, $instance->id); // Ingore any failures here.
        }
    }
    return delete_records('block_instance', 'pageid', $pageid, 'pagetype', $pagetype);
}

// Dispite what this function is called, it seems to be mostly used to populate
// the default blocks when a new course (or whatever) is created.
function blocks_repopulate_page($page) {
    global $CFG;

    $allblocks = blocks_get_record();

    if(empty($allblocks)) {
        error('Could not retrieve blocks from the database');
    }

    // Assemble the information to correlate block names to ids
    $idforname = array();
    foreach($allblocks as $block) {
        $idforname[$block->name] = $block->id;
    }

    /// If the site override has been defined, it is the only valid one.
    if (!empty($CFG->defaultblocks_override)) {
        $blocknames = $CFG->defaultblocks_override;
    }
    else {
        $blocknames = $page->blocks_get_default();
    }

    $positions = $page->blocks_get_positions();
    $posblocks = explode(':', $blocknames);

    // Now one array holds the names of the positions, and the other one holds the blocks
    // that are going to go in each position. Luckily for us, both arrays are numerically
    // indexed and the indexes match, so we can work straight away... but CAREFULLY!

    // Ready to start creating block instances, but first drop any existing ones
    blocks_delete_all_on_page($page->get_type(), $page->get_id());

    // Here we slyly count $posblocks and NOT $positions. This can actually make a difference
    // if the textual representation has undefined slots in the end. So we only work with as many
    // positions were retrieved, not with all the page says it has available.
    $numpositions = count($posblocks);
    for($i = 0; $i < $numpositions; ++$i) {
        $position = $positions[$i];
        $blocknames = explode(',', $posblocks[$i]);
        $weight = 0;
        foreach($blocknames as $blockname) {
            $newinstance = new stdClass;
            $newinstance->blockid    = $idforname[$blockname];
            $newinstance->pageid     = $page->get_id();
            $newinstance->pagetype   = $page->get_type();
            $newinstance->position   = $position;
            $newinstance->weight     = $weight;
            $newinstance->visible    = 1;
            $newinstance->configdata = '';

            if(!empty($newinstance->blockid)) {
                // Only add block if it was recognized
                insert_record('block_instance', $newinstance);
                ++$weight;
            }
        }
    }

    return true;
}

function upgrade_blocks_db($continueto) {
/// This function upgrades the blocks tables, if necessary
/// It's called from admin/index.php

    global $CFG, $db;

    require_once ($CFG->dirroot .'/blocks/version.php');  // Get code versions

    if (empty($CFG->blocks_version)) {                  // Blocks have never been installed.
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades,
                build_navigation(array(array('name' => $strdatabaseupgrades, 'link' => null, 'type' => 'misc'))), '',
                upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');

        upgrade_log_start();
        print_heading('blocks');
        $db->debug=true;

    /// Both old .sql files and new install.xml are supported
    /// but we priorize install.xml (XMLDB) if present
        $status = false;
        if (file_exists($CFG->dirroot . '/blocks/db/install.xml')) {
            $status = install_from_xmldb_file($CFG->dirroot . '/blocks/db/install.xml'); //New method
        } else if (file_exists($CFG->dirroot . '/blocks/db/' . $CFG->dbtype . '.sql')) {
            $status = modify_database($CFG->dirroot . '/blocks/db/' . $CFG->dbtype . '.sql'); //Old method
        }

        $db->debug = false;
        if ($status) {
            if (set_config('blocks_version', $blocks_version)) {
                notify(get_string('databasesuccess'), 'notifysuccess');
                notify(get_string('databaseupgradeblocks', '', $blocks_version), 'notifysuccess');
                print_continue($continueto);
                print_footer('none');
                exit;
            } else {
                error('Upgrade of blocks system failed! (Could not update version in config table)');
            }
        } else {
            error('Blocks tables could NOT be set up successfully!');
        }
    }

/// Upgrading code starts here
    $oldupgrade = false;
    $newupgrade = false;
    if (is_readable($CFG->dirroot . '/blocks/db/' . $CFG->dbtype . '.php')) {
        include_once($CFG->dirroot . '/blocks/db/' . $CFG->dbtype . '.php');  // defines old upgrading function
        $oldupgrade = true;
    }
    if (is_readable($CFG->dirroot . '/blocks/db/upgrade.php')) {
        include_once($CFG->dirroot . '/blocks/db/upgrade.php');  // defines new upgrading function
        $newupgrade = true;
    }

    if ($blocks_version > $CFG->blocks_version) {       // Upgrade tables
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades,
                build_navigation(array(array('name' => $strdatabaseupgrades, 'link' => null, 'type' => 'misc'))), '', upgrade_get_javascript());

        upgrade_log_start();
        print_heading('blocks');

    /// Run de old and new upgrade functions for the module
        $oldupgrade_function = 'blocks_upgrade';
        $newupgrade_function = 'xmldb_blocks_upgrade';

    /// First, the old function if exists
        $oldupgrade_status = true;
        if ($oldupgrade && function_exists($oldupgrade_function)) {
            $db->debug = true;
            $oldupgrade_status = $oldupgrade_function($CFG->blocks_version);
        } else if ($oldupgrade) {
            notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                     '/blocks/db/' . $CFG->dbtype . '.php');
        }

    /// Then, the new function if exists and the old one was ok
        $newupgrade_status = true;
        if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
            $db->debug = true;
            $newupgrade_status = $newupgrade_function($CFG->blocks_version);
        } else if ($newupgrade) {
            notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                     '/blocks/db/upgrade.php');
        }

        $db->debug=false;
    /// Now analyze upgrade results
        if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
            if (set_config('blocks_version', $blocks_version)) {
                notify(get_string('databasesuccess'), 'notifysuccess');
                notify(get_string('databaseupgradeblocks', '', $blocks_version), 'notifysuccess');
                print_continue($continueto);
                print_footer('none');
                exit;
            } else {
                error('Upgrade of blocks system failed! (Could not update version in config table)');
            }
        } else {
            error('Upgrade failed!  See blocks/version.php');
        }

    } else if ($blocks_version < $CFG->blocks_version) {
        upgrade_log_start();
        notify('WARNING!!!  The Blocks version you are using is OLDER than the version that made these databases!');
    }
    upgrade_log_finish();
}

//This function finds all available blocks and install them
//into blocks table or do all the upgrade process if newer
function upgrade_blocks_plugins($continueto) {

    global $CFG, $db;

    $blocktitles = array();
    $invalidblocks = array();
    $validblocks = array();
    $notices = array();

    //Count the number of blocks in db
    $blockcount = count_records('block');
    //If there isn't records. This is the first install, so I remember it
    if ($blockcount == 0) {
        $first_install = true;
    } else {
        $first_install = false;
    }

    $site = get_site();

    if (!$blocks = get_list_of_plugins('blocks', 'db') ) {
        error('No blocks installed!');
    }

    include_once($CFG->dirroot .'/blocks/moodleblock.class.php');
    if(!class_exists('block_base')) {
        error('Class block_base is not defined or file not found for /blocks/moodleblock.class.php');
    }

    foreach ($blocks as $blockname) {

        if ($blockname == 'NEWBLOCK') {   // Someone has unzipped the template, ignore it
            continue;
        }

        if(!block_is_compatible($blockname)) {
            // This is an old-style block
            //$notices[] = 'Block '. $blockname .' is not compatible with the current version of Mooodle and needs to be updated by a programmer.';
            $invalidblocks[] = $blockname;
            continue;
        }

        $fullblock = $CFG->dirroot .'/blocks/'. $blockname;

        if ( is_readable($fullblock.'/block_'.$blockname.'.php')) {
            include_once($fullblock.'/block_'.$blockname.'.php');
        } else {
            $notices[] = 'Block '. $blockname .': '. $fullblock .'/block_'. $blockname .'.php was not readable';
            continue;
        }

        $oldupgrade = false;
        $newupgrade = false;
        if ( @is_dir($fullblock .'/db/')) {
            if ( @is_readable($fullblock .'/db/'. $CFG->dbtype .'.php')) {
                include_once($fullblock .'/db/'. $CFG->dbtype .'.php');  // defines old upgrading function
                $oldupgrade = true;
            }
            if ( @is_readable($fullblock .'/db/upgrade.php')) {
                include_once($fullblock .'/db/upgrade.php');  // defines new upgrading function
                $newupgrade = true;
            }
        }

        $classname = 'block_'.$blockname;
        if(!class_exists($classname)) {
            $notices[] = 'Block '. $blockname .': '. $classname .' not implemented';
            continue;
        }

        // Here is the place to see if the block implements a constructor (old style),
        // an init() function (new style) or nothing at all (error time).

        $constructor = get_class_constructor($classname);
        if(empty($constructor)) {
            // No constructor
            $notices[] = 'Block '. $blockname .': class does not have a constructor';
            $invalidblocks[] = $blockname;
            continue;
        }

        $block    = new stdClass;     // This may be used to update the db below
        $blockobj = new $classname;   // This is what we 'll be testing

        // Inherits from block_base?
        if(!is_subclass_of($blockobj, 'block_base')) {
            $notices[] = 'Block '. $blockname .': class does not inherit from block_base';
            continue;
        }

        // OK, it's as we all hoped. For further tests, the object will do them itself.
        if(!$blockobj->_self_test()) {
            $notices[] = 'Block '. $blockname .': self test failed';
            continue;
        }
        $block->version = $blockobj->get_version();

        if (!isset($block->version)) {
            $notices[] = 'Block '. $blockname .': has no version support. It must be updated by a programmer.';
            continue;
        }

        $block->name = $blockname;   // The name MUST match the directory
        $blocktitle = $blockobj->get_title();

        if ($currblock = get_record('block', 'name', $block->name)) {
            if ($currblock->version == $block->version) {
                // do nothing
            } else if ($currblock->version < $block->version) {
                if (empty($updated_blocks)) {
                    $strblocksetup    = get_string('blocksetup');
                    print_header($strblocksetup, $strblocksetup,
                            build_navigation(array(array('name' => $strblocksetup, 'link' => null, 'type' => 'misc'))), '',
                            upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                $updated_blocks = true;
                upgrade_log_start();
                print_heading('New version of '.$blocktitle.' ('.$block->name.') exists');
                @set_time_limit(0);  // To allow slow databases to complete the long SQL

            /// Run de old and new upgrade functions for the module
                $oldupgrade_function = $block->name .'_upgrade';
                $newupgrade_function = 'xmldb_block_' . $block->name .'_upgrade';

            /// First, the old function if exists
                $oldupgrade_status = true;
                if ($oldupgrade && function_exists($oldupgrade_function)) {
                    $db->debug = true;
                    $oldupgrade_status = $oldupgrade_function($currblock->version, $block);
                } else if ($oldupgrade) {
                    notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                             $fullblock . '/db/' . $CFG->dbtype . '.php');
                }

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
                    $db->debug = true;
                    $newupgrade_status = $newupgrade_function($currblock->version, $block);
                } else if ($newupgrade) {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $fullblock . '/db/upgrade.php');
                }

                $db->debug=false;
            /// Now analyze upgrade results
                if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed

                    // Set the block cron on upgrade
                    $block->cron = !empty($blockobj->cron) ? $blockobj->cron : 0;

                    // OK so far, now update the block record
                    $block->id = $currblock->id;
                    if (! update_record('block', $block)) {
                        error('Could not update block '. $block->name .' record in block table!');
                    }
                    $component = 'block/'.$block->name;
                    if (!update_capabilities($component)) {
                        error('Could not update '.$block->name.' capabilities!');
                    }

                    events_update_definition($component);
                    notify(get_string('blocksuccess', '', $blocktitle), 'notifysuccess');
                } else {
                    notify('Upgrading block '. $block->name .' from '. $currblock->version .' to '. $block->version .' FAILED!');
                }
                echo '<hr />';
            } else {
                upgrade_log_start();
                error('Version mismatch: block '. $block->name .' can\'t downgrade '. $currblock->version .' -> '. $block->version .'!');
            }

        } else {    // block not installed yet, so install it

            // If it allows multiples, start with it enabled
            if ($blockobj->instance_allow_multiple()) {
                $block->multiple = 1;
            }

            // Set the block cron on install
            $block->cron = !empty($blockobj->cron) ? $blockobj->cron : 0;

            // [pj] Normally this would be inline in the if, but we need to
            //      check for NULL (necessary for 4.0.5 <= PHP < 4.2.0)
            $conflictblock = array_search($blocktitle, $blocktitles);
            if($conflictblock !== false && $conflictblock !== NULL) {
                // Duplicate block titles are not allowed, they confuse people
                // AND PHP's associative arrays ;)
                error('<strong>Naming conflict</strong>: block <strong>'.$block->name.'</strong> has the same title with an existing block, <strong>'.$conflictblock.'</strong>!');
            }
            if (empty($updated_blocks)) {
                $strblocksetup    = get_string('blocksetup');
                print_header($strblocksetup, $strblocksetup,
                        build_navigation(array(array('name' => $strblocksetup, 'link' => null, 'type' => 'misc'))), '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
            }
            $updated_blocks = true;
            upgrade_log_start();
            print_heading($block->name);
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL

        /// Both old .sql files and new install.xml are supported
        /// but we priorize install.xml (XMLDB) if present
            $status = false;
            if (file_exists($fullblock . '/db/install.xml')) {
                $status = install_from_xmldb_file($fullblock . '/db/install.xml'); //New method
            } else if (file_exists($fullblock .'/db/'. $CFG->dbtype .'.sql')) {
                $status = modify_database($fullblock .'/db/'. $CFG->dbtype .'.sql'); //Old method
            } else {
                $status = true;
            }

            $db->debug = false;
            if ($status) {
                if ($block->id = insert_record('block', $block)) {
                    $blockobj->after_install();
                    $component = 'block/'.$block->name;
                    if (!update_capabilities($component)) {
                        notify('Could not set up '.$block->name.' capabilities!');
                    }

                    events_update_definition($component);
                    notify(get_string('blocksuccess', '', $blocktitle), 'notifysuccess');
                    echo '<hr />';
                } else {
                    error($block->name .' block could not be added to the block list!');
                }
            } else {
                error('Block '. $block->name .' tables could NOT be set up successfully!');
            }
        }

        $blocktitles[$block->name] = $blocktitle;
    }

    if(!empty($notices)) {
        upgrade_log_start();
        foreach($notices as $notice) {
            notify($notice);
        }
    }

    // Finally, if we are in the first_install of BLOCKS (this means that we are
    // upgrading from Moodle < 1.3), put blocks in all existing courses.
    if ($first_install) {
        upgrade_log_start();
        //Iterate over each course
        if ($courses = get_records('course')) {
            foreach ($courses as $course) {
                $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
                blocks_repopulate_page($page);
            }
        }
    }

    if (!empty($CFG->siteblocksadded)) {     /// This is a once-off hack to make a proper upgrade
        upgrade_log_start();
        $page = page_create_object(PAGE_COURSE_VIEW, SITEID);
        blocks_repopulate_page($page);
        delete_records('config', 'name', 'siteblocksadded');
    }

    upgrade_log_finish();

    if (!empty($updated_blocks)) {
        print_continue($continueto);
        print_footer('none');
        die;
    }
}

?>
