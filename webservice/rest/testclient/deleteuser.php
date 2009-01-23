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

$params = array('username','mnethostid');

foreach ($params as $param) {
	$$param = (isset($_POST[$param]))?$_POST[$param]:'';
}

start_interface("Delete a user");
?>

<form action="deleteuser.php" method="post">
<table border="0">
    <tr><td>Search: </td><td><input type="text" name="username" value="<?php echo $username; ?>"/></td></tr>
    <tr><td></td><td><input type="hidden" name="mnethostid" value="1"><input type="submit" value="Find Users"></td></tr>
</table>
</form>

<?php

if ($username) {
  
     //we are asking for a token
    $connectiondata['username'] = 'admin';
    $connectiondata['password'] = 'admin';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/user/tmp_get_token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, format_postdata($connectiondata));
    $token = curl_exec($ch);
    $data['token'] = $token;

    $data['username'] = $username;
    $data['mnethostid'] = $mnethostid;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/user/tmp_delete_user');
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
