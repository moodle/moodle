<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    redirect("$CFG->wwwroot/course/view.php?id=$id");

?>
