<?PHP // $Id$

//  Display the course home page.

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/blocklib.php");

    optional_variable($id);
    optional_variable($name);

    if (!$id and !$name) {
        error("Must specify course id or short name");
    }

    if (!empty($_GET['name'])) {
        if (! ($course = get_record("course", "shortname", $name)) ) {
            error("That's an invalid short course name");
        }
    } else {
        if (! ($course = get_record("course", "id", $id)) ) {
            error("That's an invalid course id");
        }
    }

    require_login($course->id);

    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");

    if (!file_exists($CFG->dirroot.'/course/format/'.$course->format.'/format.php')) {
        $course->format = 'weeks';  // Default format is weeks
    }

    // Can't avoid this... :(
    switch($course->format) {
        case 'weeks':
            $courseformat = COURSE_FORMAT_WEEKS;
        break;
        case 'topics':
            $courseformat = COURSE_FORMAT_TOPICS;
        break;
        case 'social':
            $courseformat = COURSE_FORMAT_SOCIAL;
        break;
        default:
            $courseformat = 0;
    }

    // Doing this now so we can pass the results to block_action()
    // and dodge the overhead of doing the same work twice.

    $blocks = $course->blockinfo;
    $delimpos = strpos($blocks, ':');

    if($delimpos === false) {
        // No ':' found, we have all left blocks
        $leftblocks = explode(',', $blocks);
        $rightblocks = array();
    }
    else if($delimpos === 0) {
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

    if (!isset($USER->editing)) {
        $USER->editing = false;
    }

    $editing = false;

    if (isteacheredit($course->id)) {
       if (isset($edit)) {
            if ($edit == "on") {
                $USER->editing = true;
            } else if ($edit == "off") {
                $USER->editing = false;
            }
        }

        $editing = $USER->editing;

        if (isset($hide)) {
            set_section_visible($course->id, $hide, '0');
        }

        if (isset($show)) {
            set_section_visible($course->id, $show, '1');
        }

        if (isset($_GET['blockaction'])) {
            if (isset($_GET['blockid'])) {
                block_action($course, $leftblocks, $rightblocks, strtolower($_GET['blockaction']), intval($_GET['blockid']));
            }
        }

        // This has to happen after block_action() has possibly updated the two arrays
        $allblocks = array_merge($leftblocks, $rightblocks);

        $missingblocks = array();
        $recblocks = get_records('blocks','visible','1');

        // Note down which blocks are going to get displayed
        blocks_used($allblocks, $recblocks);

        if($editing && $recblocks) {
            foreach($recblocks as $recblock) {
                // If it's not hidden or displayed right now...
                if(!in_array($recblock->id, $allblocks) && !in_array(-($recblock->id), $allblocks)) {
                    // And if it's applicable for display in this format...
                    if(block_method_result($recblock->name, 'applicable_formats') & $courseformat) {
                        // Add it to the missing blocks
                        $missingblocks[] = $recblock->id;
                    }
                }
            }
        }

        if (!empty($section)) {
            if (!empty($move)) {
                if (!move_section($course, $section, $move)) {
                    notify("An error occurred while moving a section");
                }
            }
        }
    } else {
        $USER->editing = false;

        // Note down which blocks are going to get displayed
        $allblocks = array_merge($leftblocks, $rightblocks);
        $recblocks = get_records('blocks','visible','1');
        blocks_used($allblocks, $recblocks);
    }

    $SESSION->fromdiscussion = "$CFG->wwwroot/course/view.php?id=$course->id";

    if (! $course->category) {      // This course is not a real course.
        redirect("$CFG->wwwroot/");
    }

    $strcourse = get_string("course");

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    print_header("$strcourse: $course->fullname", "$course->fullname", "$course->shortname",
                 "", "", true, update_course_icon($course->id), $loggedinas);

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    if (! $sections = get_all_sections($course->id)) {   // No sections found
        // Double-check to be extra sure
        if (! $section = get_record("course_sections", "course", $course->id, "section", 0)) {
            $section->course = $course->id;   // Create a default section.
            $section->section = 0;
            $section->visible = 1;
            $section->id = insert_record("course_sections", $section);
        }
        if (! $sections = get_all_sections($course->id) ) {      // Try again
            error("Error finding or creating section structures for this course");
        }
    }

    if (empty($course->modinfo)) {       // Course cache was never made
        rebuild_course_cache($course->id);
        if (! $course = get_record("course", "id", $course->id) ) {
            error("That's an invalid course id");
        }
    }

    // If the block width cache is not set, set it
    if(!isset($SESSION->blockcache->width->{$course->id}) || $editing) {

        // This query might be optimized away if we 're in editing mode
        if(!isset($recblocks)) {
            $recblocks = get_records('blocks','visible','1');
        }
        $preferred_width_left = blocks_preferred_width($leftblocks, $recblocks);
        $preferred_width_right = blocks_preferred_width($rightblocks, $recblocks);

        // This may be kind of organizational overkill, granted...
        // But is there any real need to simplify the structure?
        $SESSION->blockcache->width->{$course->id}->left = $preferred_width_left;
        $SESSION->blockcache->width->{$course->id}->right = $preferred_width_right;
    }
    else {
        $preferred_width_left = $SESSION->blockcache->width->{$course->id}->left;
        $preferred_width_right = $SESSION->blockcache->width->{$course->id}->right;
    }

    require("$CFG->dirroot/course/format/$course->format/format.php");  // Include the actual course format

    print_footer();

?>
