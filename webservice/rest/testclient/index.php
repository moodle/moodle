<?php
/**
 * Created on 10/17/2008
 *
 * Rest Test Client
 *
 * @author David Castro Garcia
 * @author Ferran Recio Calderó
 * @author Jerome Mouneyrac
 * @author Jordi Piguillem
 */

require_once ('config_rest.php');

start_interface (false);

$links = array( array('USERS'),
				array('getusers.php','get_users()'),
                array('createuser.php','create_user()'),
                array('deleteuser.php','delete_user()'),
                array('updateuser.php','update_user()'),
                array('GROUPS'),
                array('creategroup.php','create_group()'),
                array('addgroupmember.php', 'add_groupmember()'),
                array('getgroup.php', 'get_group()'));

echo '<ul>';
foreach ($links as $link) {
	if (sizeof($link)==2){
    	echo '<li><a href="'.$link[0].'">'.$link[1].'</a></li>';
	} else {
		echo '</ul><h2>'.$link[0].'</h2><ul>';
	}
}
echo '</ul>';

end_interface(false);
?>