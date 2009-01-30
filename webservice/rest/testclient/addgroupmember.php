<?php
/**
 *
 * Rest Test Client
 *
 * @author David Castro Garcia
 * @author Ferran Recio Calderó
 * @author Jordi Piguillem
 */

require_once ('config_rest.php');

$params = array('groupid', 'userid');

foreach ($params as $param) {
	$$param = (isset($_POST[$param]))?$_POST[$param]:'';
}

start_interface("Add group member to group");
?>

<form action="addgroupmember.php" method="post">
<table border="0">
    <tr><td>Group id: </td><td><input type="text" name="groupid" value="<?php echo $groupid; ?>"/></td></tr>
    <tr><td>User id: </td><td><input type="text" name="userid" value="<?php echo $userid; ?>"/></td></tr>
    <tr><td></td><td><input type="submit" value="Add member"></td></tr>
</table>
</form>

<?php

if ($groupid && $userid) {

    var_dump($CFG->serverurl.'/group/tmp_add_groupmember');


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
	$data['userid'] = $userid;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/group/tmp_add_groupmember');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, format_postdata($data));
    $out = curl_exec($ch);

    $res = basicxml_xml_to_object($out);

    show_object($res->result);

    show_xml ($out);
} else {
    echo "<p>Fill the form first</p>";
}

end_interface();

?>
