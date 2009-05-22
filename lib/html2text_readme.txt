html2text.php is a modified copy of a file shipped with the RoundCube project:

  http://trac.roundcube.net/log/trunk/roundcubemail/program/lib/html2text.php


Modifications
--------------

1- fix for these warnings in cron:

  "html_entity_decode bug - cannot yet handle MBCS in html_entity_decode()!"

by using this code:

  utf8_encode(html_entity_decode($string));

instead of:

  html_entity_decode($string, ENT_COMPAT, 'UTF-8');

(see http://nz.php.net/manual/en/function.html-entity-decode.php#89483)


 -- Francois Marier <francois@catalyst.net.nz>  2009-05-22
