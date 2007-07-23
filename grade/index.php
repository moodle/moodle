<?php //$Id$

/*
 * Compatibility redirection to reports
 */

require '../config.php';

$id = required_param('id', PARAM_INT);
redirect('report/index.php?id='.$id);

?>