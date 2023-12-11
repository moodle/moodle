<?php

namespace auth_useradditionapi;

defined('MOODLE_INTERNAL') || die();

class observer {
    public static function user_added($event) {
        // // Get the user information from the event
        // $user = $event->get_record_snapshot('user', $event->objectid);

        // $link = 'https://example.com/auth?userId=';

        // // Prepare the data to send
        // $data = array('userId' => $user->nin, 'link' => $link);

        // // Initialize a cURL session
        // $ch = curl_init();

        // // Set the URL, number of POST vars, POST data
        // curl_setopt($ch, CURLOPT_URL, "https://localhost:3000/api/user/addUser");
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // // Execute the request
        // curl_exec($ch);

        // // Close the cURL session
        // curl_close($ch);
    }
}
