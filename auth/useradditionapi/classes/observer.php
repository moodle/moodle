<?php

namespace auth_useradditionapi;

defined('MOODLE_INTERNAL') || die();

class observer {
    public static function user_added($event) {
        // Get the user information from the event
        $user = $event->get_record_snapshot('user', $event->objectid);

        // Get the current domain from Moodle configuration
        $wwwroot = get_config('moodle', 'wwwroot');

        // Construct the link using the current domain
        $link = $wwwroot . '/auth?userId=';

        // Prepare the data to send
        $data = array('userId' => $user->nin, 'link' => $link);

        // Initialize a cURL session
        $ch = curl_init();

        // Set the URL, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, "http://ec2-13-51-199-32.eu-north-1.compute.amazonaws.com/api/user/addUser");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // Execute the request
        curl_exec($ch);

        // Close the cURL session
        curl_close($ch);
    }
}
