<?php

 # if you cannot manage to include() the core "ewiki.php" library
 # before any plain <HTML> output is made inside "yoursite.php", you
 # could use such a lib wrapper beside yoursites/index.php

 # it is also useful, if you want to keep binary data in a separate
 # database, say a db_flat_files one - because you can then set this up
 # herein without any affect to yoursites/ewiki.php


 # remember to define() inside ewiki.php or yoursite.php:
 define("EWIKI_SCRIPT_BINARY", "binary.php?binary=");


 #-- that's all:
 mysql_connect("localhost", "DBUSER", "DBPASSWORD");
 mysql_query("use DATABASENAME");

 include("ewiki.php");

?>