<?php
/**
 * Created on 10/17/2008
 * 
 * Rest Test Client
 *
 * @author David Castro Garcia
 * @author Ferran Recio Calderó
 * @author Jerome Mouneyrac
 */

require_once ('config_rest.php');

start_interface (false);

$links = array( array('getusers.php','getusers()'),
                array('createuser.php','createuser()'),
                array('namedparams_get_users.php','namedparams_get_users()'));

echo '<ul>';
foreach ($links as $link) {
    echo '<li><a href="'.$link[0].'">'.$link[1].'</a></li>';
}
echo '</ul>';

end_interface(false);
?>