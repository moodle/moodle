<?php  // $Id$

    die; //must be fixed before enabling again, see SC#971

    // All of this is standard Moodle fixtures

    require_once('../config.php');
    require_once($CFG->dirroot .'/course/lib.php');
    require_once($CFG->dirroot .'/lib/blocklib.php');
    require_once($CFG->dirroot .'/mod/resource/lib.php');
    require_once($CFG->dirroot .'/mod/forum/lib.php');

    optional_param('blockaction');
    optional_param('instanceid', 0, PARAM_INT);
    optional_param('blockid',    0, PARAM_INT);

    require_login();

    // Begin snippet -----------------------------------------------------------------
    // This snippet should normally be defined in another file, but I 've put it all
    // in here to keep it simple.

    // First of all define the string identifier for this "type" of page.
    define('MOODLE_PAGE_TEST', 'testpage');

    // Also, define identifiers for any non-standard block positions we want to support.
    define('BLOCK_POS_CENTERUP', 'cu');
    define('BLOCK_POS_CENTERDOWN', 'cd');

    // The actual Page derived class
    class page_test extends page_base {

        // Mandatory; should return our identifier.
        function get_type() {
            return MOODLE_PAGE_TEST;
        }

        // For this test page, only admins are going to be allowed editing (for simplicity).
        function user_allowed_editing() {
            return isadmin();
        }

        // Also, admins are considered to have "always on" editing (I wanted to avoid duplicating
        // the code that turns editing on/off here; you can roll your own or copy course/view.php).
        function user_is_editing() {
            return isadmin();
        }

        // Simple method that accepts one parameter and prints the header. Here we just ignore
        // the parameter entirely.
        function print_header($title) {
            print_header("Page testing page", 'SAMPLE CUSTOM PAGE', 'home');
        }
        
        // This should point to the script that displays us; it's straightforward in this case.
        function url_get_path() {
            global $CFG;
            return $CFG->wwwroot .'/blocks/pagedemo.php';
        }

        // We do not need any special request variables such as ID in this case, so we 're not
        // going to have to override url_get_parameters() here; the default suits us nicely.

        // Having defined all identifiers we need, here we declare which block positions we are
        // going to support.
        function blocks_get_positions() {
           return array(BLOCK_POS_LEFT, BLOCK_POS_RIGHT, BLOCK_POS_CENTERUP, BLOCK_POS_CENTERDOWN);
        }

        // And here we declare where new blocks will appear (arbitrary choice).
        function blocks_default_position() {
            return BLOCK_POS_CENTERUP;
        }

        // Since we 're not going to be creating multiple instances of this "page" (as we do with
        // courses), we don't need  to provide default blocks. Otherwise we 'd need to override
        // the blocks_get_default() method.

        // And finally, a little block move logic. Given a block's previous position and where
        // we want to move it to, return its new position. Pretty self-documenting.
        function blocks_move_position(&$instance, $move) {
            if($instance->position == BLOCK_POS_LEFT && $move == BLOCK_MOVE_RIGHT) {
                return BLOCK_POS_CENTERUP;
            } else if ($instance->position == BLOCK_POS_RIGHT && $move == BLOCK_MOVE_LEFT) {
                return BLOCK_POS_CENTERUP;
            } else if (($instance->position == BLOCK_POS_CENTERUP || $instance->position == BLOCK_POS_CENTERDOWN) && $move == BLOCK_MOVE_LEFT) {
                return BLOCK_POS_LEFT;
            } else if (($instance->position == BLOCK_POS_CENTERUP || $instance->position == BLOCK_POS_CENTERDOWN) && $move == BLOCK_MOVE_RIGHT) {
                return BLOCK_POS_RIGHT;
            } else if ($instance->position == BLOCK_POS_CENTERUP && $move == BLOCK_MOVE_DOWN) {
                return BLOCK_POS_CENTERDOWN;
            } else if ($instance->position == BLOCK_POS_CENTERDOWN && $move == BLOCK_MOVE_UP) {
                return BLOCK_POS_CENTERUP;
            }
            return $instance->position;
        }
    }
    // End snippet -------------------------------------------------------------------

    /// Bounds for block widths on this page
    define('BLOCK_L_MIN_WIDTH', 160);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 160);
    define('BLOCK_R_MAX_WIDTH', 210);
    define('BLOCK_C_MIN_WIDTH', 250);
    define('BLOCK_C_MAX_WIDTH', 350);


    // Before creating our page object, we need to map our page identifier to the actual name
    // of the class which will be handling its operations. Pretty simple, but essential.
    page_map_class(MOODLE_PAGE_TEST, 'page_test');

    // Now, create our page object. The identifier "1" is passed arbitrarily because we don't
    // have multiple "testpages"; if we did, that would be the "testpageid" from the database.
    $PAGE = page_create_object(MOODLE_PAGE_TEST, 1);

    $PAGE->print_header(NULL);
    $editing = $PAGE->user_is_editing();

    // That's it! From now on, everything is simply copy-pasted from course/view.php with a few
    // minor tweaks to display the page layout!

    // Calculate the preferred width for left, right and center (both center positions will use the same)
    if (empty($preferred_width_left)) {
        $preferred_width_left =  blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]);
    }
    if (empty($preferred_width_right)) {
        $preferred_width_right = blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]);
    }
    if (empty($preferred_width_centerup)) {
        $preferred_width_centerup = blocks_preferred_width($pageblocks[BLOCK_POS_CENTERUP]);
    }
    if (empty($preferred_width_centerdown)) {
        $preferred_width_centerdown =  blocks_preferred_width($pageblocks[BLOCK_POS_CENTERDOWN]);
    }
    $preferred_width_left = min($preferred_width_left, BLOCK_L_MAX_WIDTH);
    $preferred_width_left = max($preferred_width_left, BLOCK_L_MIN_WIDTH);
    $preferred_width_right = min($preferred_width_right, BLOCK_R_MAX_WIDTH);
    $preferred_width_right = max($preferred_width_right, BLOCK_R_MIN_WIDTH);
    $preferred_width_center = max($preferred_width_centerup, $preferred_width_centerdown);
    $preferred_width_center = min($preferred_width_center, BLOCK_C_MAX_WIDTH);
    $preferred_width_center = max($preferred_width_center, BLOCK_C_MIN_WIDTH);

    // Display the blocks and allow blocklib to handle any block action requested
    $pageblocks = blocks_get_by_page($PAGE);

    if($editing) {
        if (!empty($blockaction) && confirm_sesskey()) {
            if (!empty($blockid)) {
                blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), intval($blockid));
                
            }
            else if (!empty($instanceid)) {
                $instance = blocks_find_instance($instanceid, $pageblocks);
                blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), $instance);
            }
            // This re-query could be eliminated by judicious programming in blocks_execute_action(),
            // but I'm not sure if it's worth the complexity increase...
            $pageblocks = blocks_get_by_page($PAGE);
        }
    }
    
    // The actual display logic is here
    echo '<table style="width: 100%;"><tr>';

    if(blocks_have_content($pageblocks,BLOCK_POS_LEFT) || $editing) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_left.'px;">';
        blocks_print_group($PAGE, $pageblocks,BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td style="border: 1px black solid; width: '.$preferred_width_center.'px;"><p style="text-align: center; padding: 10px; background: black; color: white;">Center-up position:</p>';
    if(blocks_have_content($pageblocks,BLOCK_POS_CENTERUP) || $editing) {
        blocks_print_group($PAGE, $pageblocks,BLOCK_POS_CENTERUP);
    }

    echo '<div style="padding: 10px; background: gold; text-align: center;">Content Here';
    
    print_object(make_timestamp(2005, 6, 1, 0, 0, 0));

    echo '</div>';

    echo '<p style="text-align: center; padding: 10px; background: black; color: white;">Center-down position:</p>';
    if(blocks_have_content($pageblocks,BLOCK_POS_CENTERDOWN) || $editing) {
        blocks_print_group($PAGE, $pageblocks,BLOCK_POS_CENTERDOWN);
    }
    echo '</td>';

    if(blocks_have_content($pageblocks,BLOCK_POS_RIGHT) || $editing) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_right.'px;">';
        blocks_print_group($PAGE, $pageblocks,BLOCK_POS_RIGHT);
        if ($editing) {
            blocks_print_adminblock($PAGE, $pageblocks);
        }
        echo '</td>';
    }

    // Finished! :-)

    echo '</tr></table>';
    print_footer();


?>
