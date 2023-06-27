<?php

class test_client {

    protected $t; // target.

    public function __construct() {

        $this->t = new StdClass;

        // Setup this settings for tests
        $this->t->baseurl = 'http://dev.moodle31.fr'; // The remote Moodle url to push in.
        $this->t->wstoken = 'e9dbbab271767af1397dcd89ce0c03b7'; // the service token for access.
        $this->t->filepath = ''; // Some physical location on your system.

        $this->t->uploadservice = '/webservice/upload.php';
        $this->t->service = '/webservice/rest/server.php';
    }

    public function test_get_user_stats($uidsource = 'idnumber', $uid = 0, $cidsource = 'idnumber', $cid = 0, $from = 0, $to = 0, $score = true) {

        if (empty($this->t->wstoken)) {
            echo "No token to proceed\n";
            return;
        }

        $params = array('wstoken' => $this->t->wstoken,
                        'wsfunction' => 'block_use_stats_get_user_stats',
                        'moodlewsrestformat' => 'json',
                        'uidsource' => $uidsource,
                        'uid' => $uid,
                        'cidsource' => $cidsource,
                        'cid' => $cid,
                        'from' => $from,
                        'to' => $to,
                        'score' => $score);

        $serviceurl = $this->t->baseurl.$this->t->service;

        return $this->send($serviceurl, $params);
    }

    public function test_get_users_stats($uidsource = '', $uids = array(), $cidsource = '', $cid = 0, $from = 0, $to = 0, $score = true) {

        if (empty($this->t->wstoken)) {
            echo "No token to proceed\n";
            return;
        }

        $params = array('wstoken' => $this->t->wstoken,
                        'wsfunction' => 'block_use_stats_get_users_stats',
                        'moodlewsrestformat' => 'json',
                        'uidsource' => $uidsource,
                        'uids' => $uids,
                        'cidsource' => $cidsource,
                        'cid' => $cid,
                        'from' => $from,
                        'to' => $to,
                        'score' => $score);

        $serviceurl = $this->t->baseurl.$this->t->service;

        return $this->send($serviceurl, $params);
    }


    protected function send($serviceurl, $params) {
        $ch = curl_init($serviceurl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        echo "Firing CUrl $serviceurl ... \n";
        print_r($params);

        if (!$result = curl_exec($ch)) {
            echo "CURL Error : ".curl_errno($ch).' '.curl_error($ch)."\n";
            return;
        }

        echo $result;
        if (preg_match('/EXCEPTION/', $result)) {
            echo $result;
            return;
        }

        $result = json_decode($result);
        print_r($result);
        return $result;
    }
}

// Effective test scénario

$client = new test_client();

/*
$client->test_get_user_stats('id', 2, 'id', 2); // Test one course one users.
$client->test_get_user_stats('idnumber', 'ADMIN', 'id', 2); // Test one course one users.
$client->test_get_user_stats('username', 'admin', 'id', 2); // Test one course one users.
$client->test_get_user_stats('id', 3, 'id', 2); // Test one course one users.
*/
$client->test_get_users_stats('id', array(3,4,5), 'id', 2); // Test one course one users.
$client->test_get_users_stats('idnumber', array('ADMIN','ID_A1','ID_A2'), 'id', 2); // Test one course one users.

/*
$client->test_get_user_stats('id', 3, 'id', 2, 1489095902, 1489195902, 1); // Test time range with score.
/*
$client->test_get_user_stats('id', 3, 'shortname', 'TESTMODS'); // Test one course one users.
$client->test_get_user_stats('id', 3, 'idnumber', 'TESTMODS'); // Test one course one users.

$client->test_get_user_stats('id', 3, 'id', 2, 1489095902); // Test from time.
$client->test_get_user_stats('id', 3, 'id', 2, 1489095902, 1489195902); // Test time range.
*/