<?php

defined('MOODLE_INTERNAL') || die();

class local_autologin {

    private static $secretKey = 'YBUXSVAS9xWhLuWrlo79u6F4oltdBKZTzfRp1vIDQm0OVBoHdIfWAvBFq4Vr9WPZPELKkDte6rPmDLQBCEx0ayU3jkpf9A0RNhb6HpIcWZDwrtPZVbXF1WxMRhNd5FW2RtDMTMxOL1CVdfZ4WeflodqIalWWjUvm7FYgebxpdDMRebJnZuIT9qAuZKCAOpzdpuUJvGWnYdNMkMe2LqWj6kGf0w01kdQy8XY2whPJ7rPucpLQXwlM2oVQvYcZ1aId';

    public static function obfuscate($idnumber) {
        $hashed = hash_hmac('sha256', $idnumber, self::$secretKey, true);
        $base64 = base64_encode($hashed);
        return $base64;
    }

    public static function attempt_autologin() {
        global $CFG, $DB, $USER;

        // Check if the request contains the obfuscated ID parameter.
        $obfuscatedIdnumber = optional_param('nin', '', PARAM_TEXT);

        if (!empty($obfuscatedIdnumber)) {
            // Loop through all users and attempt to find a match.
            $users = $DB->get_records('user');

            foreach ($users as $user) {
                // Obfuscate the user's ID for comparison.
                $obfuscatedUserid = self::obfuscate($user->idnumber);

                if ($obfuscatedUserid === $obfuscatedIdnumber) {
                    // Log in the user.
                    complete_user_login($user);
                    redirect($CFG->wwwroot);
                }
            }
        }
    }
}