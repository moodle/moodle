<?PHP // $Id$

//  Deletes a forum entirely.  

    require_once("../../config.php");
    require_once("mod.php");

    require_variable($f);  // The forum to delete.

    if (! $forum = get_record("forum", "id", $f)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum course was incorrect");
    }

    if (!isteacher($forum->course)) {
        error("You are not allowed to do this");
    }

    if (! delete_instance($forum->id)) {
        error("Could not delete that forum");
    }

    redirect("$CFG->wwwroot/mod/forum/index.php?id=$course->id");
?>
