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

$params = array('groupname', 'courseid');

foreach ($params as $param) {
	$$param = (isset($_POST[$param]))?$_POST[$param]:'';
}

start_interface("Create Group");
?>

<form action="creategroup.php" method="post">
<table border="0">
    <tr><td>Group name: </td><td><input type="text" name="groupname" value="<?php echo $groupname; ?>"/></td></tr>
    <tr><td>Course id: </td><td><input type="text" name="courseid" value="<?php echo $groupid; ?>"/></td></tr>
    <tr><td></td><td><input type="submit" value="Create Group"></td></tr>
</table>
</form>

<?php

if ($groupname && $courseid) {

    var_dump($CFG->serverurl.'/group/tmp_create_group');


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
	$data['groupname'] = $groupname;
	$data['courseid'] = $courseid;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/group/tmp_create_group');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, format_postdata($data));
    $out = curl_exec($ch);

    $res = basicxml_xml_to_object($out);

    show_object($res->groupid);

    show_xml ($out);
} else {
    echo "<p>Fill the form first</p>";
}

end_interface();

?>
