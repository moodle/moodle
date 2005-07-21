<?php // $Id$
      // This script displays tex source code.

    require_once('../../config.php');
    require_once($CFG->libdir.'/moodlelib.php');

    $texexp = urldecode($_SERVER['QUERY_STRING']);
    // entities are usually encoded twice, first in HTML editor then in tex/filter.php
    $texexp = html_entity_decode(html_entity_decode($texexp));
    // encode all entities (saves non-ISO)
    $texexp = htmlentities($texexp,ENT_COMPAT,get_string("thischarset","moodle"));
?>
<html>
  <head>
    <title>TeX Source</title>
    <meta http-equiv="content-type" content="text/html; charset=<?php echo get_string('thischarset'); ?>" />
  </head>
  <body bgcolor="#FFFFFF">
    <?php echo $texexp; ?>
  </body>
</html>