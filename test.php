<?php
$things = array('block', 'mod', 'format');
unset($things[array_search('mod', $things)]);
echo '<pre>';
var_dump($things);
echo '</pre>';

?>