<?php
/**
 * Created on 10/17/2008
 *
 * Rest Test Client
 *
 * @author David Castro Garcia
 * @author Ferran Recio Calderó
 * @author Jordi Piguillem
 */

require_once ('config_rest.php');

$params = array('groupid');

foreach ($params as $param) {
	$$param = (isset($_POST[$param]))?$_POST[$param]:'';
}

start_interface("Delete a user");
?>

<form action="getgroup.php" method="post">
<table border="0">
    <tr><td>Group id: </td><td><input type="text" name="groupid" value="<?php echo $groupid; ?>"/></td></tr>
    <tr><td></td><td><input type="submit" value="Find Group"></td></tr>
</table>
</form>

<?php

if ($groupid) {

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

    $data['groupid'] = $groupid;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/group/tmp_get_group');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, format_postdata($data));
    $out = curl_exec($ch);

    $res = basicxml_xml_to_object($out);

	show_object($res->group);

    show_xml ($out);
} else {
    echo "<p>Fill the form first</p>";
}

end_interface();

?>
