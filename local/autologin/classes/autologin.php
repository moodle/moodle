<?php

defined('MOODLE_INTERNAL') || die();

class local_autologin {

    private static $secretKey = 'YBUXSVAS9xWhLuWrlo79u6F4oltdBKZTzfRp1vIDQm0OVBoHdIfWAvBFq4Vr9WPZPELKkDte6rPmDLQBCEx0ayU3jkpf9A0RNhb6HpIcWZDwrtPZVbXF1WxMRhNd5FW2RtDMTMxOL1CVdfZ4WeflodqIalWWjUvm7FYgebxpdDMRebJnZuIT9qAuZKCAOpzdpuUJvGWnYdNMkMe2LqWj6kGf0w01kdQy8XY2whPJ7rPucpLQXwlM2oVQvYcZ1aId';

    public static function obfuscate($idnumber) {
        $hashed = hash_hmac('sha256', $idnumber, self::$secretKey, true);
        $base64 = base64_encode($hashed);
        return $base64;
    }

    public static function deobfuscate($obfuscatedIdnumber) {
        $decoded = base64_decode($obfuscatedIdnumber);
        $idnumber = hash_hmac('sha256', $decoded, self::$secretKey, true);

        // If you have additional validation for the idnumber, you can perform it here.

        return $idnumber;
    }

    public static function attempt_autologin() {
        error_log('Hello World');

        global $CFG, $DB, $USER;
    
        // Check if the request contains the idnumber parameter.
        $obfuscatedIdnumber = optional_param('nin', '', PARAM_TEXT);

        error_log($obfuscatedIdnumber);
    
        // De-Obfuscate ID Number
        if (!empty($obfuscatedIdnumber)) {
            $idnumber = self::deobfuscate($obfuscatedIdnumber);

            error_log($idnumber);
    
            // Attempt to find the user with the provided idnumber.
            $user = $DB->get_record('user', array('idnumber' => $idnumber));
    
            if ($user) {
                error_log($idnumber);

                // Log in the user.
                complete_user_login($user);
                redirect($CFG->wwwroot);
            }
        }
    }
}