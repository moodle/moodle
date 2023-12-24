<?php

namespace local_useradditionapi;

class useradditionapi_observer {
  public static function usercreated(\core\event\user_created $event) {
    // Get the user information from the event
    $user = $event->get_record_snapshot('user', $event->objectid);

    self::senduserrequest($user);
  }

  public static function userupdated(\core\event\user_updated $event) {
    // Get the user information from the event
    $user = $event->get_record_snapshot('user', $event->objectid);

    self::senduserrequest($user);
  }

  public static function senduserrequest($user){
    // Get the current domain from Moodle configuration
    $wwwroot = get_config('moodle', 'wwwroot');

    // Construct the link using the current domain
    $link = $wwwroot . '/login/index.php?nin=';

    // Prepare the data to send
    $data = array('userId' => $user->idnumber, 'link' => $link);
    $json_data = json_encode($data);

    // Initialize a cURL session
    $ch = curl_init();

    // Set the URL, headers, and POST data as JSON
    curl_setopt($ch, CURLOPT_URL, "http://ec2-51-20-2-134.eu-north-1.compute.amazonaws.com/api/user/addUser");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Execute the request
    curl_exec($ch);

    // Close the cURL session
    curl_close($ch);
  }
}