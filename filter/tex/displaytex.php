<?php // $Id$
      // This script displays tex source code.

    $texexp = urldecode($_SERVER['QUERY_STRING']);
    // entities are usually encoded twice, first in HTML editor then in tex/filter.php
    $texexp = html_entity_decode(html_entity_decode($texexp));
    // encode all entities
    $texexp = htmlentities($texexp);
?>
<html>
  <head>
    <title>TeX Source</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
  </head>
  <body bgcolor="#FFFFFF">
    <?php echo $texexp; ?>
  </body>
</html>