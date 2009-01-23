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

$params = array('username','mnethostid','newusername','firstname');

foreach ($params as $param) {
	$$param = (isset($_POST[$param]))?$_POST[$param]:'';
}

start_interface("Update a user");
?>

<form action="updateuser.php" method="post">
<table border="0">
 <tr><td>Old username: </td><td><input type="text" name="username" value="<?php echo $username; ?>"/></td></tr>
 <tr><td>New username: </td><td><input type="text" name="newusername" value="<?php echo $newusername; ?>"/></td></tr>
 <tr><td>New firstname: </td><td><input type="text" name="firstname" value="<?php echo $firstname; ?>"/></td></tr>
 <tr><td></td><td><input type="hidden" name="mnethostid" value="1"><input type="submit" value="Find Users"></td></tr>
</table>
</form>

<?php

if ($username) {

    //we are asking for a token
    $connectiondata['username'] = 'wsuser';
    $connectiondata['password'] = 'wspassword';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/user/tmp_get_token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, format_postdata($connectiondata));
    $token = curl_exec($ch);
    $data['token'] = $token;

    $data['username'] = $username;
    $data['mnethostid'] = $mnethostid;
    $data['newusername'] = $newusername;
    $data['firstname'] = $firstname;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/user/tmp_update_user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, format_postdata($data));
    $out = curl_exec($ch);

    $res = basicxml_xml_to_object($out);

    //show_object($res->user,2,'auth');
    var_dump($res);
    show_xml ($out);
} else {
    echo "<p>Fill the form first</p>";
}

end_interface();

?>
