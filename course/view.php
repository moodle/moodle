<?PHP // $Id$

//  Display the course home page.

    require("../config.php");
    require("lib.php");


    require_login($id);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");

    if ( isteacher($course->id) ) {
        if ($edit == "on") {
            $USER->editing = true;
        } else if ($edit == "off") {
            $USER->editing = false;
        }
    }
    if ($help == "on") {
        $USER->help = true;
    } else if ($help == "off") {
        $USER->help = false;
    }

    if (! $course->category) {      // This course is not a real course.
        redirect("$CFG->wwwroot");
    }

    print_header("Course: $course->fullname", "$course->fullname", "$course->shortname", "search.search", "", true,
                  update_course_icon($course->id));

    if (! $modtypes = get_records_sql_menu("SELECT name,fullname FROM modules ORDER BY fullname") ) {
        error("No modules are installed!");
    }

    if ( $rawmods = get_records_sql("SELECT cm.*, m.name as modname, m.fullname as modfullname
                                   FROM modules m, course_modules cm
                                   WHERE cm.course = '$course->id' 
                                     AND cm.deleted = '0'
                                     AND cm.module = m.id") ) {

        foreach($rawmods as $mod) {    // Index the mods
            $mods[$mod->id] = $mod;
            $modtype[$mod->modname] = $mod->modfullname;
        }
        ksort($modtype);
    }

    switch ($course->format) {
        case "weeks":
            include("weeks.php");
            break;
        case "social":
            include("social.php");
            break;
        case "topics":
            include("topics.php");
            break;
        default:
            error("Course format not defined yet!");
    }

    print_footer($course);


/// FUNCTIONS ////////


function make_editing_buttons($moduleid) {
    return "&nbsp; &nbsp; 
          <A HREF=mod.php?delete=$moduleid><IMG 
             SRC=../pix/t/delete.gif BORDER=0 ALT=Delete></A>
          <A HREF=mod.php?id=$moduleid&move=-1><IMG 
             SRC=../pix/t/up.gif BORDER=0 ALT=\"Move up\"></A>
          <A HREF=mod.php?id=$moduleid&move=1><IMG 
             SRC=../pix/t/down.gif BORDER=0 ALT=\"Move down\"></A>
          <A HREF=mod.php?update=$moduleid><IMG 
             SRC=../pix/t/edit.gif BORDER=0 ALT=Update></A>";
}

function print_side_block($heading="", $list=NULL, $footer="", $icons=NULL) {
    
    echo "<TABLE WIDTH=100%>\n";
    echo "<TR><TD COLSPAN=2><P><B><FONT SIZE=2>$heading</TD></TR>\n";
    if ($list) {
        foreach($list as $key => $string) {
            echo "<TR><TD VALIGN=top WIDTH=12>";
            if ($icons[$key]) {
                echo $icons[$key];
            } else {
                echo "";
            }
            echo "</TD>\n<TD WIDTH=100% VALIGN=top>";
            echo "<P><FONT SIZE=2>$string</FONT></P>";
            echo "</TD></TR>\n";
        }
    }
    if ($footer) {
        echo "<TR><TD></TD><TD ALIGN=left><P><FONT SIZE=2>$footer</TD></TR>\n";
    }
    echo "</TABLE><BR>\n\n";
}

?>
