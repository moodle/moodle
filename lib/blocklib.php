<?php //$Id$

//This library includes all the necessary stuff to use blocks in course pages

define('BLOCK_MOVE_LEFT',   0x01);
define('BLOCK_MOVE_RIGHT',  0x02);
define('BLOCK_MOVE_UP',     0x04);
define('BLOCK_MOVE_DOWN',   0x08);
define('BLOCK_CONFIGURE',   0x10);

define('MOODLE_PAGE_COURSE', 'course');
define('BLOCK_POS_LEFT',  'l');
define('BLOCK_POS_RIGHT', 'r');

function page_get_format($page) {
    switch($page->type) {
        case MOODLE_PAGE_COURSE:
            if($page->id == SITEID) {
                return 'site';
            }
            else {
                $course = get_record('course', 'id', $page->id);
                return $course->format;
            }
        break;
    }
    return NULL;
}

function blocks_get_missing($page, $pageblocks) {
    $missingblocks = array();
    $allblocks = blocks_get_record();

    if(!empty($allblocks)) {
        foreach($allblocks as $block) {
            if($block->visible && (!blocks_find_block($block->id, $pageblocks) || $block->multiple)) {
                // And if it's applicable for display in this format...
                $formats = block_method_result($block->name, 'applicable_formats');
                $pageformat = page_get_format($page);
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

    switch($page->type) {
        case MOODLE_PAGE_COURSE:
            $course = get_record('course', 'id', $page->id);
            if($page->id == SITEID) {
                $pageformat = 'site';
            }
            else {
                $pageformat = $course->format;
            }
        break;
        default:
            return;
        break;
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
    return eval('return CourseBlock_'.$blockname.'::'.$method.'();');
}

//This function creates a new object of the specified block class
function block_instance($blockname, $instance = NULL) {
    if(!block_load_class($blockname)) {
        return false;
    }
    $classname = 'CourseBlock_'.$blockname;
    $retval = New $classname;
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
    $classname = 'CourseBlock_'.$blockname;
    @include_once($CFG->dirroot.'/blocks/'.$blockname.'/block_'.$blockname.'.php');

    // After all this, return value indicating success or failure
    return class_exists($classname);
}

function blocks_have_content($instances) {
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

//This function print the one side of blocks in course main page
function blocks_print_group($page, $instances) {
    
    if(empty($instances)) {
        return;
    }

    switch($page->type) {
        case MOODLE_PAGE_COURSE:
            $isediting     = isediting($page->id);
            $ismoving      = ismoving($page->id);
            $isteacheredit = isteacheredit($page->id);
        break;
    }

    // Include the base class
    @include_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    if(!class_exists('moodleblock')) {
        error('Class MoodleBlock is not defined or file not found for /course/blocks/moodleblock.class.php');
    }

    $maxweight = max(array_keys($instances));

    foreach($instances as $instance) {
        $block = blocks_get_record($instance->blockid);
        if(!$block->visible) {
            // Disabled by the admin
            continue;
        }

        $obj = block_instance($block->name, $instance);

        if ($isediting && !$ismoving && $isteacheredit) {
            $options = 0;
            $options |= BLOCK_MOVE_UP * ($instance->weight != 0);
            $options |= BLOCK_MOVE_DOWN * ($instance->weight != $maxweight);
            $options |= BLOCK_MOVE_RIGHT * ($instance->position != BLOCK_POS_RIGHT);
            $options |= BLOCK_MOVE_LEFT * ($instance->position != BLOCK_POS_LEFT);
            // DH - users can configure this instance if the block class allows multiple instances, not just if the administrator has allowed this block class to display multiple for the given site as would be found in $block->multiple
            $options |= BLOCK_CONFIGURE * ( $obj->instance_allow_multiple() );
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

//This iterates over an array of blocks and calculates the preferred width
function blocks_preferred_width($instances) {
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
            // Series of ugly hacks following...
            global $course, $USER; // First hack; we need $course to print out the headers
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
                'id' => $course->id,
                'instanceid' => $instance->id,
                'blockaction' => 'config'
            );
            // The 'id' thing is a crude hack in all its glory...
            // Redirecting the form submission back to ourself with qualified_me() was a good idea since otherwise
            // we'd need to have an "extra" script that would have to infer where to redirect us back just from
            // the data in $instance (pagetype and pageid). But, "ourself" is most likely course/view.php and it needs
            // a course id. Hence the hack.

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
                $loggedinas = '<p class="logininfo">'. user_login_string($course, $USER) .'</p>';
                print_header(get_string('blockconfigin', 'moodle', $course->fullname), $course->fullname, $course->shortname,
                     '', '', true, update_course_icon($course->id), $loggedinas);
                print_heading(get_string('blockconfiga', 'moodle', $block->name));
                echo '<form method="post" action="'. strip_querystring(qualified_me()) .'">'; // This I wouldn't call a hack but it sure looks cheeky
                echo '<p>';
                foreach($hiddendata as $name => $val) {
                    echo '<input type="hidden" name="'. $name .'" value="'. $val .'" />';
                }
                echo '</p>';
                $blockobject->instance_config_print();
                echo '</form>';
                print_footer();
                die(); // Do not go on with the other course-related stuff
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
        break;
        case 'movedown':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }
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
        break;
        case 'moveleft':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }
            $sql = '';
            switch($instance->position) {
                case BLOCK_POS_RIGHT:
                    // To preserve the continuity of block weights
                    $sql = 'UPDATE '. $CFG->prefix .'block_instance SET weight = weight - 1 WHERE pagetype = \''. $instance->pagetype.
                           '\' AND pageid = '. $instance->pageid .' AND position = \'' .$instance->position.
                           '\' AND weight > '. $instance->weight;

                    $instance->position = BLOCK_POS_LEFT;
                    $maxweight = max(array_keys($pageblocks[$instance->position]));
                    $instance->weight = $maxweight + 1;
                break;
            }
            if($sql) {
                update_record('block_instance', $instance);
                execute_sql($sql, false);
            }
        break;
        case 'moveright':
            if(empty($instance))  {
                error('Invalid block instance for '. $blockaction);
            }
            $sql = '';
            switch($instance->position) {
                case BLOCK_POS_LEFT:
                    // To preserve the continuity of block weights
                    $sql = 'UPDATE '. $CFG->prefix .'block_instance SET weight = weight - 1 WHERE pagetype = \''. $instance->pagetype.
                           '\' AND pageid = '. $instance->pageid .' AND position = \''. $instance->position.
                           '\' AND weight > '. $instance->weight;

                    $instance->position = BLOCK_POS_RIGHT;
                    $maxweight = max(array_keys($pageblocks[$instance->position]));
                    $instance->weight = $maxweight + 1;
                break;
            }
            if($sql) {
                update_record('block_instance', $instance);
                execute_sql($sql, false);
            }
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

            $weight = get_record_sql('SELECT 1, max(weight) + 1 AS nextfree FROM '. $CFG->prefix .'block_instance WHERE pageid = '. $page->id .' AND pagetype = \''. $page->type .'\' AND position = \''. BLOCK_POS_RIGHT .'\''); 

            $newinstance = new stdClass;
            $newinstance->blockid    = $blockid;
            $newinstance->pageid     = $page->id;
            $newinstance->pagetype   = $page->type;
            $newinstance->position   = BLOCK_POS_RIGHT;
            $newinstance->weight     = $weight->nextfree;
            $newinstance->visible    = 1;
            $newinstance->configdata = '';
            insert_record('block_instance', $newinstance);
        break;
    }
}

function blocks_get_by_page($page) {
    $blocks = get_records_select('block_instance', 'pageid = '. $page->id .' AND pagetype = \''. $page->type .'\'', 'position, weight');

    $arr = array(BLOCK_POS_LEFT => array(), BLOCK_POS_RIGHT => array());
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

            switch($page->type) {
                case MOODLE_PAGE_COURSE:
                    $course = get_record('course', 'id', $page->id);
                break;
                default: die('unknown pagetype: '. $page->type);
            }

            $blockobject = block_instance($block->name);
            if ($blockobject === false) {
                continue;
            }
            $menu[$block->id] = $blockobject->get_title();
        }

        if($page->id == SITEID) {
            $target = 'index.php';
        }
        else {
            $target = 'view.php';
        }
        $content = popup_form($target .'?id='. $course->id .'&amp;sesskey='. $USER->sesskey .'&amp;blockaction=add&amp;blockid=',
                              $menu, 'add_block', '', $stradd .'...', '', '', true);
        $content = '<div align="center">'. $content .'</div>';
        print_side_block($strblocks, $content, NULL, NULL, NULL);
    }
}

function blocks_repopulate_page($page) {
    global $CFG;

    /// If the site override has been defined, it is the only valid one.
    if (!empty($CFG->defaultblocks_override)) {
        $blocknames = $CFG->defaultblocks_override;
    }
    /// Else, try to find out what page this is 
    else {
        switch($page->type) {
            case MOODLE_PAGE_COURSE:
                // Is it the site?
                if($page->id == SITEID) {
                    if (!empty($CFG->defaultblocks_site)) {
                        $blocknames = $CFG->defaultblocks_site;
                    }
                    /// Failsafe - in case nothing was defined.
                    else {
                        $blocknames = 'site_main_menu,admin,course_list:course_summary,calendar_month';
                    }
                }
                // It's a normal course, so do it accodring to the course format
                else {
                    $course = get_record('course', 'id', $page->id);
                    if (!empty($CFG->{'defaultblocks_'. $course->format})) {
                        $blocknames = $CFG->{'defaultblocks_'. $course->format};
                    }
                    else {
                        $format_config = $CFG->dirroot.'/course/format/'.$course->format.'/config.php';
                        if (@is_file($format_config) && is_readable($format_config)) {
                            require($format_config);
                        }
                        if (!empty($format['defaultblocks'])) {
                            $blocknames = $format['defaultblocks'];
                        }
                        else if (!empty($CFG->defaultblocks)){
                            $blocknames = $CFG->defaultblocks;
                        }
                        /// Failsafe - in case nothing was defined.
                        else {
                            $blocknames = 'participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity';
                        }
                    }
                }
            break;
            default:
                error('Invalid page type: '. $page->type);
            break;
        }
    }

    $allblocks = blocks_get_record();

    if(empty($allblocks)) {
        error('Could not retrieve blocks from the database');
    }

    // We have the blocks, make up two arrays
    $left  = '';
    $right = '';
    @list($left, $right) = explode(':', $blocknames);
    $instances = array(BLOCK_POS_LEFT => explode(',', $left), BLOCK_POS_RIGHT => explode(',', $right));

    // Arrays are fine, now we have to correlate block names to ids
    $idforname = array();
    foreach($allblocks as $block) {
        $idforname[$block->name] = $block->id;
    }

    // Ready to start creating block instances, but first drop any existing ones
    delete_records('block_instance', 'pageid', $page->id, 'pagetype', $page->type);

    foreach($instances as $position => $blocknames) {
        $weight = 0;
        foreach($blocknames as $blockname) {
            $newinstance = new stdClass;
            $newinstance->blockid    = $idforname[$blockname];
            $newinstance->pageid     = $page->id;
            $newinstance->pagetype   = $page->type;
            $newinstance->position   = $position;
            $newinstance->weight     = $weight;
            $newinstance->visible    = 1;
            $newinstance->configdata = '';
            insert_record('block_instance', $newinstance);
            $weight++;
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
    if(!class_exists('moodleblock')) {
        error('Class MoodleBlock is not defined or file not found for /blocks/moodleblock.class.php');
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

        $classname = 'CourseBlock_'.$blockname;
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

        // Inherits from MoodleBlock?
        if(!is_subclass_of($blockobj, 'moodleblock')) {
            $notices[] = 'Block '. $blockname .': class does not inherit from MoodleBlock';
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
                $page = new stdClass;
                $page->type = MOODLE_PAGE_COURSE;
                $page->id   = $course->id;
                blocks_repopulate_page($page);
            }
        }
    }

    if (!empty($CFG->siteblocksadded)) {     /// This is a once-off hack to make a proper upgrade
        $page = new stdClass;
        $page->type = MOODLE_PAGE_COURSE;
        $page->id   = SITEID;
        blocks_repopulate_page($page);
        delete_records('config', 'name', 'siteblocksadded');
    }

    if (!empty($updated_blocks)) {
        print_continue($continueto);
        die;
    }
}

?>
