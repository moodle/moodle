<?php
    require_once("../../config.php");
    require_once("$CFG->dirroot/course/lib.php"); // For side-blocks
    require_once('wiki.class.php');

    echo "Test Harness<br>";
    
    $wiki = new cWiki(9);
    
    print_object($wiki);
?>