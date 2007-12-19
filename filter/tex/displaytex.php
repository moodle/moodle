<?php // $Id$
      // This script displays tex source code.

    require_once('../../config.php');

    $texexp = urldecode($_SERVER['QUERY_STRING']);
    // entities are usually encoded twice, first in HTML editor then in tex/filter.php
    $texexp = html_entity_decode(html_entity_decode($texexp));
    // encode all entities (saves non-ISO)
    $texexp = htmlentities($texexp,ENT_COMPAT,'utf-8');
?>
<html>
  <head>
    <title>TeX Source</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
  <body bgcolor="#FFFFFF">
    <?php echo $texexp; ?>
  </body>
</html>