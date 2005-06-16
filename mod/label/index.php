<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = require_param('id',PARAM_INT);   // course

    redirect("$CFG->wwwroot/course/view.php?id=$id");

?>
