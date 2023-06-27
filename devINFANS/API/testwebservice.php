<?php
require('../../config.php');
require_once('MoodleRest.php');
// global $USER;
// print_r($USER);
/*
The $parameters variable below translates in URL as:
    userlist[0][userid]=5&
    userlist[0][courseid]=2&
    userlist[1][userid]=4&
    userlist[1][courseid]=2"
*/
// $parameters = array('uidsource' => "administrateur", 'uid' => "2", 'cidsource' => 'idnumber', 'cid' => '0', 'from' => 0, 'to' => 0, 'score' => 1);
// $MoodleRest = new MoodleRest('https://formassmat-moodle.fr/webservice/rest/server.php', '4d155643ab4a0ed26cb2aca9788074a8');
$MoodleRest = new MoodleRest(
    'https://formassmat-moodle.fr/webservice/rest/server.php', 
    '4d155643ab4a0ed26cb2aca9788074a8'
);

// $MoodleRest = new MoodleRest();
// $MoodleRest->setServerAddress("https://formassmat-moodle.fr/webservice/rest/server.php");
// $MoodleRest->setToken('4d155643ab4a0ed26cb2aca9788074a8');
// $MoodleRest->setReturnFormat(MoodleRest::RETURN_JSON);
// $MoodleRest->getUrl();
$MoodleRest->setDebug();
$arr = $MoodleRest->request('core_user_get_users_by_field', array('field' => "id", 'values' => array("2")), MoodleRest::METHOD_GET);
print_r($arr);

?>