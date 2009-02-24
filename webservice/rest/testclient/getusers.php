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

$params = array('search');

foreach ($params as $param) {
    $$param = (isset($_POST[$param]))?$_POST[$param]:'';
}

start_interface("List of Users");
?>

<form action="getusers.php" method="post">
    <table border="0">
        <tr><td>Search: </td><td><input type="text" name="search" value="<?php echo $search; ?>"/></td></tr>
        <tr><td></td><td><input type="submit" value="Find Users"></td></tr>
    </table>
</form>

<?php

if ($search) {
    $data['search'] = $search;

    var_dump($CFG->serverurl.'/user/tmp_get_users');


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

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->serverurl.'/user/tmp_get_users');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, format_postdata($data));
    $out = curl_exec($ch);

    $res = basicxml_xml_to_object($out);
    show_object($res->user,2,'auth');

    show_xml ($out);
} else {
    echo "<p>Fill the form first</p>";
}

end_interface();

?>
