<?php //$Id$

//This library includes all the necessary stuff to use blocks in course pages

define('BLOCK_MOVE_LEFT',   0x01);
define('BLOCK_MOVE_RIGHT',  0x02);
define('BLOCK_MOVE_UP',     0x04);
define('BLOCK_MOVE_DOWN',   0x08);
define('BLOCK_CONFIGURE',   0x10);

define('BLOCK_POS_LEFT',  'l');
define('BLOCK_POS_RIGHT', 'r');

require_once($CFG->libdir.'/pagelib.php');

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
//It seems that the only way to use the :: operator with variable class names is eval() :(
//For caveats with this technique, see the PHP docs on operator ::
function block_method_result($blockname, $method) {
    if(!block_load_class($blockname)) {
        return NULL;
    }
    return eval('return block_'.$blockname.'::'.$method.'();');
}

//This function creates a new object of the specified block class
function block_instance($blockname, $instance = NULL) {
    if(!block_load_class($blockname)) {
        return false;
    }
    $classname = 'block_'.$blockname;
    $retval = new $classname;
    if($instance !== NULL) {
        $retval->load_instance($instance);
    }
    return $retval;
}

//This function loads the necessary class files for a block
//Whenever you want to load a block, use this first
function block_load_class($blockname) {
    global $CFG;

    @include_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    $classname = 'block_'.$blockname;
    @include_once($CFG->dirroot.'/blocks/'.$blockname.'/block_'.$blockname.'.php');

    // After all this, return value indicating success or failure
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
                $formats = block_method_result($block->name, 'applicable_formats');
                if(isset($formats[$pageformat]) ? $formats[$pageformat] : !empty($formats['all'])) {
                    // Add it to the missing blocks
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
            $formats = block_method_result($block->name, 'applicable_formats');
            if(! (isset($formats[$pageformat]) ? $formats[$pageformat] : !empty($formats['all']))) {
                // Translation: if the course format is explicitly accepted/rejected, use
                // that setting. Otherwise, fallback to the 'all' format. The empty() test
                // uses the trick that empty() fails if 'all' is either !isset() or false.

                blocks_delete_instance($instance);
            }
        }
    }
}

function blocks_delete_instance($instance) {
    global $CFG;

    delete_records('block_instance', 'id', $instance->id);
    // And now, decrement the weight of all blocks after this one
    execute_sql('UPDATE '.$CFG->prefix.'block_instance SET weight = weight - 1 WHERE pagetype = \''.$instance->pagetype.
                '\' AND pageid = '.$instance->pageid.' AND position = \''.$instance->position.
                '\' AND weight > '.$instance->weight, false);
}

// Accepts an array of block instances and checks to see if any of them have content to display
// (causing them to calculate their content in the process). Returns true or false. Parameter passed
// by reference for speed; the array is actually not modified.
function blocks_have_content(&$instances) {
    foreach($instances as $instance) {
        if(!$instance->visible) {
            continue;
        }
        $record = blocks_get_record($instance->blockid);
        $obj = block_instance($record->name, $instance);
        $content = $obj->get_content();
        $type = $obj->get_content_type();
        switch($type) {
            case BLOCK_TYPE_LIST:
                if(!empty($content->items) || !empty($content->footer)) {
                    return true;
                }
            break;
            case BLOCK_TYPE_TEXT:
            case BLOCK_TYPE_NUKE:
                if(!empty($content->text) || !empty($content->footer)) {
                    return true;
                }
            break;
        }
    }

    return false;
}

// This function prints one group of blocks in a page
// Parameters passed by reference for speed; they are not modified.
function blocks_print_group(&$page, &$instances) {
    
    if(empty($instances)) {
        return;
    }

    $isediting = $page->user_is_editing();

    $maxweight = max(array_keys($instances));

    foreach($instances as $instance) {
        $block = blocks_get_record($instance->blockid);
        if(!$block->visible) {
            // Disabled by the admin
            continue;
        }

        if (!$obj = block_instance($block->name, $instance)) {
            // Invalid block
            continue;
        }

        if ($isediting) {
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
            $obj->add_edit_controls($options);
        }

        if(!$instance->visible) {
            if($isediting) {
                $obj->print_shadow();
            }
        }
        else {
            $obj->print_block();
        }
    }
}

// This iterates over an array of blocks and calculates the preferred width
// Parameter passed by reference for speed; it's not modified.
function blocks_preferred_width(&$instances) {
    $width = 0;

    if(empty($instances) || !is_array($instances)) {
        return 0;
    }
    foreach($instances as $instance) {
        if(!$instance->visible) {
            continue;
        }
        $block = blocks_get_record($instance->blockid);
        $pref = block_method_result($block->name, 'preferred_width');
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
    foreach($blocksarray as $blockgroup) {
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

function blocks_execute_action($page, &$pageblocks, $blockaction, $instanceorid) {
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
            $blockobject = block_instance($block->name, $instance);
            if ($blockobject === false) {
                continue;
            }
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

            if($data = data_submitted()) {
                $remove = array_keys($hiddendata);
                foreach($remove as $item) {
                    unset($data->$item);
                }
                if(!$blockobject->instance_config_save($data)) {
                    error('Error saving block configuration');
                }
                // And nothing more, continue with displaying the page
            }
            else {
                // We need to show the config screen, so we highjack the display logic and then die
                $strheading = get_string('blockconfiga', 'moodle', $block->name);
                $page->print_header(get_string('pageheaderconfigablock', 'moodle'), array($strheading => ''));
                print_heading($strheading);
                echo '<form method="post" action="'. $page->url_get_path() .'">';
                echo '<p>';
                foreach($hiddendata as $name => $val) {
                    echo '<input type="hidden" name="'. $name .'" value="'. $val .'" />';
                }
                echo '</p>';
                $blockobject->instance_config_print();
                echo '</form>';
                print_footer();
                die(); // Do not go on with the other page-related stuff
            }
        break;
        case 'toggle':
            if(empty($instance))  {
                error('Invalid block instance for '.$blockaction);
            }
            $instance->visible = ($instance->visible) ? 0 : 1;
            update_record('block_instance', $instance);
        break;
        case 'delete':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }
            blocks_delete_instance($instance);
        break;
        case 'moveup':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }

            if($instance->weight == 0) {
                // The block is the first one, so a move "up" probably means it changes position
                // Where is the instance going to be moved?
                $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_UP);
                $newweight = (empty($pageblocks[$newpos]) ? 0 : max(array_keys($pageblocks[$newpos])) + 1);

                blocks_execute_repositioning($instance, $newpos, $newweight);
            }
            else {
                // The block is just moving upwards in the same position.
                // This configuration will make sure that even if somehow the weights
                // become not continuous, block move operations will eventually bring
                // the situation back to normal without printing any warnings.
                if(!empty($pageblocks[$instance->position][$instance->weight - 1])) {
                    $other = $pageblocks[$instance->position][$instance->weight - 1];
                }
                if(!empty($other)) {
                    ++$other->weight;
                    update_record('block_instance', $other);
                }
                --$instance->weight;
                update_record('block_instance', $instance);
            }
        break;
        case 'movedown':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }

            if($instance->weight == max(array_keys($pageblocks[$instance->position]))) {
                // The block is the last one, so a move "down" probably means it changes position
                // Where is the instance going to be moved?
                $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_DOWN);
                $newweight = (empty($pageblocks[$newpos]) ? 0 : max(array_keys($pageblocks[$newpos])) + 1);

                blocks_execute_repositioning($instance, $newpos, $newweight);
            }
            else {
                // The block is just moving downwards in the same position.
                // This configuration will make sure that even if somehow the weights
                // become not continuous, block move operations will eventually bring
                // the situation back to normal without printing any warnings.
                if(!empty($pageblocks[$instance->position][$instance->weight + 1])) {
                    $other = $pageblocks[$instance->position][$instance->weight + 1];
                }
                if(!empty($other)) {
                    --$other->weight;
                    update_record('block_instance', $other);
                }
                ++$instance->weight;
                update_record('block_instance', $instance);
            }
        break;
        case 'moveleft':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }

            // Where is the instance going to be moved?
            $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_LEFT);
            $newweight = (empty($pageblocks[$newpos]) ? 0 : max(array_keys($pageblocks[$newpos])) + 1);

            blocks_execute_repositioning($instance, $newpos, $newweight);
        break;
        case 'moveright':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }

            // Where is the instance going to be moved?
            $newpos    = $page->blocks_move_position($instance, BLOCK_MOVE_RIGHT);
            $newweight = (empty($pageblocks[$newpos]) ? 0 : max(array_keys($pageblocks[$newpos])) + 1);

            blocks_execute_repositioning($instance, $newpos, $newweight);
        break;
        case 'add':
            // Add a new instance of this block, if allowed
            $block = blocks_get_record($blockid);

            if(!$block->visible) {
                // Only allow adding if the block is enabled
                return false;
            }

            if(!$block->multiple && blocks_find_block($blockid, $pageblocks) !== false) {
                // If no multiples are allowed and we already have one, return now
                return false;
            }

            $newpos = $page->blocks_default_position();
            $weight = get_record_sql('SELECT 1, max(weight) + 1 AS nextfree FROM '. $CFG->prefix .'block_instance WHERE pageid = '. $page->get_id() .' AND pagetype = \''. $page->get_type() .'\' AND position = \''. $newpos .'\''); 

            $newinstance = new stdClass;
            $newinstance->blockid    = $blockid;
            $newinstance->pageid     = $page->get_id();
            $newinstance->pagetype   = $page->get_type();
            $newinstance->position   = $newpos;
            $newinstance->weight     = $weight->nextfree;
            $newinstance->visible    = 1;
            $newinstance->configdata = '';
            insert_record('block_instance', $newinstance);
        break;
    }

    // In order to prevent accidental duplicate actions, redirect to a page with a clean url
    redirect($page->url_get_full());
}

// This shouldn't be used externally at all, it's here for use by blocks_execute_action()
// in order to reduce code repetition.
function blocks_execute_repositioning(&$instance, $newpos, $newweight) {
    global $CFG;

    // If it's staying where it is, don't do anything
    if($newpos == $instance->position) {
        return;
    }

    // Close the weight gap we 'll leave behind
    execute_sql('UPDATE '. $CFG->prefix .'block_instance SET weight = weight - 1 WHERE pagetype = \''. $instance->pagetype.
                      '\' AND pageid = '. $instance->pageid .' AND position = \'' .$instance->position.
                      '\' AND weight > '. $instance->weight,
                false);

    $instance->position = $newpos;
    $instance->weight   = $newweight;

    update_record('block_instance', $instance);
}

function blocks_get_by_page($page) {
    $blocks = get_records_select('block_instance', 'pageid = '. $page->get_id() .' AND pagetype = \''. $page->get_type() .'\'', 'position, weight');

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
function blocks_print_adminblock($page, $missingblocks) {
    global $USER;

    $strblocks = get_string('blocks');
    $stradd    = get_string('add');
    if (!empty($missingblocks)) {
        foreach ($missingblocks as $blockid) {
            $block = blocks_get_record($blockid);
            $blockobject = block_instance($block->name);
            if ($blockobject === false) {
                continue;
            }
            $menu[$block->id] = $blockobject->get_title();
        }

        $target = $page->url_get_full(array('sesskey' => $USER->sesskey, 'blockaction' => 'add'));
        $content = popup_form($target.'&amp;blockid=', $menu, 'add_block', '', $stradd .'...', '', '', true);
        $content = '<div style="text-align: center;">'. $content .'</div>';
        print_side_block($strblocks, $content, NULL, NULL, NULL);
    }
}

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
    delete_records('block_instance', 'pageid', $page->get_id(), 'pagetype', $page->get_type());

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
            insert_record('block_instance', $newinstance);
            ++$weight;
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
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades,
                     '', '', false, '&nbsp;', '&nbsp;');

        $db->debug=true;
        if (modify_database($CFG->dirroot .'/blocks/db/'. $CFG->dbtype .'.sql')) {
            $db->debug = false;
            if (set_config('blocks_version', $blocks_version)) {
                notify(get_string('databasesuccess'), 'green');
                notify(get_string('databaseupgradeblocks', '', $blocks_version));
                print_continue($continueto);
                exit;
            } else {
                error('Upgrade of blocks system failed! (Could not update version in config table)');
            }
        } else {
            error('Blocks tables could NOT be set up successfully!');
        }
    }


    if ($blocks_version > $CFG->blocks_version) {       // Upgrade tables
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);

        require_once ($CFG->dirroot .'/blocks/db/'. $CFG->dbtype .'.php');

        $db->debug=true;
        if (blocks_upgrade($CFG->blocks_version)) {
            $db->debug=false;
            if (set_config('blocks_version', $blocks_version)) {
                notify(get_string('databasesuccess'), 'green');
                notify(get_string('databaseupgradeblocks', '', $blocks_version));
                print_continue($continueto);
                exit;
            } else {
                error('Upgrade of blocks system failed! (Could not update version in config table)');
            }
        } else {
            $db->debug=false;
            error('Upgrade failed!  See blocks/version.php');
        }

    } else if ($blocks_version < $CFG->blocks_version) {
        notify('WARNING!!!  The code you are using is OLDER than the version that made these databases!');
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

        $fullblock = $CFG->dirroot .'/blocks/'. $blockname;

        if ( is_readable($fullblock.'/block_'.$blockname.'.php')) {
            include_once($fullblock.'/block_'.$blockname.'.php');
        } else {
            $notices[] = 'Block '. $blockname .': '. $fullblock .'/block_'. $blockname .'.php was not readable';
            continue;
        }

        if ( @is_dir($fullblock .'/db/')) {
            if ( @is_readable($fullblock .'/db/'. $CFG->dbtype .'.php')) {
                include_once($fullblock .'/db/'. $CFG->dbtype .'.php');  // defines upgrading function
            } else {
                $notices[] ='Block '. $blockname .': '. $fullblock .'/db/'. $CFG->dbtype .'.php was not readable';
                continue;
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
        $methods = get_class_methods($classname);
        if(!in_array('init', $methods)) {
            // This is an old-style block
            $notices[] = 'Block '. $blockname .' is an old style block and needs to be updated by a programmer.';
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
                    print_header($strblocksetup, $strblocksetup, $strblocksetup, '', '', false, '&nbsp;', '&nbsp;');
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
                    notify('Upgrading block '. $block->name .' from '. $currblock->version .' to '. $block->version .' FAILED!');
                }
                else {
                    // OK so far, now update the block record
                    $block->id = $currblock->id;
                    if (! update_record('block', $block)) {
                        error('Could not update block '. $block->name .' record in block table!');
                    }
                    notify(get_string('blocksuccess', '', $blocktitle), 'green');
                    echo '<hr />';
                }
                $updated_blocks = true;
            } else {
                error('Version mismatch: block '. $block->name .' can\'t downgrade '. $currblock->version .' -> '. $block->version .'!');
            }

        } else {    // block not installed yet, so install it

            // If it allows multiples, start with it enabled
            $block->multiple = $blockobj->instance_allow_multiple();

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
                print_header($strblocksetup, $strblocksetup, $strblocksetup, '', '', false, '&nbsp;', '&nbsp;');
            }
            print_heading($block->name);
            $updated_blocks = true;
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL
            if (!is_dir($fullblock .'/db/') || modify_database($fullblock .'/db/'. $CFG->dbtype .'.sql')) {
                $db->debug = false;
                if ($block->id = insert_record('block', $block)) {
                    notify(get_string('blocksuccess', '', $blocktitle), 'green');
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
        foreach($notices as $notice) {
            notify($notice);
        }
    }

    // Finally, if we are in the first_install of BLOCKS (this means that we are
    // upgrading from Moodle < 1.3), put blocks in all existing courses.
    if ($first_install) {
        //Iterate over each course
        if ($courses = get_records('course')) {
            foreach ($courses as $course) {
                $page = page_create_object(MOODLE_PAGE_COURSE, $course->id);
                blocks_repopulate_page($page);
            }
        }
    }

    if (!empty($CFG->siteblocksadded)) {     /// This is a once-off hack to make a proper upgrade
        $page = page_create_object(MOODLE_PAGE_COURSE, SITEID);
        blocks_repopulate_page($page);
        delete_records('config', 'name', 'siteblocksadded');
    }

    if (!empty($updated_blocks)) {
        print_continue($continueto);
        die;
    }
}

?>
