<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core\authentication;

use core\exception\coding_exception;
use core\exception\moodle_exception;

/**
 * Internal password management.
 *
 * Provides methods for hashing, validating, and updating internally-managed user passwords.
 *
 * Note: This class should be fetched using DI.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class password {
    /**
     * Returns true if the password hash is a legacy bcrypt hash.
     *
     * @param string $hash The stored password hash
     * @return bool
     */
    public function is_legacy_hash(
        #[\SensitiveParameter] string $hash,
    ): bool {
        return (bool) preg_match('/^\$2y\$[\d]{2}\$[A-Za-z0-9\.\/]{53}$/', $hash);
    }

    /**
     * Get the available password peppers.
     *
     * The latest pepper is checked for minimum entropy as part of this function.
     * We only calculate the entropy of the most recent pepper, because passwords
     * are always updated to the latest pepper, and in the past we may have enforced
     * a lower minimum entropy.
     * Also, we allow the latest pepper to be empty, to allow admins to migrate off peppers.
     *
     * @return array The password peppers
     * @throws coding_exception If the entropy of the password pepper is less than the recommended minimum
     */
    public function get_peppers(): array {
        global $CFG;

        // Get all available peppers.
        if (isset($CFG->passwordpeppers) && is_array($CFG->passwordpeppers)) {
            $peppers = $CFG->passwordpeppers;
            krsort($peppers, SORT_NUMERIC);
        } else {
            $peppers = [];
        }

        // Check if the entropy of the most recent pepper is less than the minimum.
        // Also, we allow the most recent pepper to be empty, to allow admins to migrate off peppers.
        $lastpepper = reset($peppers);
        if (!empty($peppers) && $lastpepper !== '' && calculate_entropy($lastpepper) < PEPPER_ENTROPY) {
            throw new coding_exception(
                'password pepper below minimum',
                'The entropy of the password pepper is less than the recommended minimum.',
            );
        }
        return $peppers;
    }

    /**
     * Compare password against hash stored in user object to determine if it is valid.
     *
     * If necessary it also updates the stored hash to the current format.
     *
     * @param \stdClass $user (Password property may be updated)
     * @param string $password Plain text password
     * @return bool True if password is valid
     */
    public function validate(\stdClass $user, #[\SensitiveParameter] string $password): bool {
        if ($this->exceeds_max_length($password)) {
            return false;
        }

        if ($user->password === AUTH_PASSWORD_NOT_CACHED) {
            return false;
        }

        $peppers = $this->get_peppers();
        $islegacy = $this->is_legacy_hash($user->password);

        // If the password is a legacy hash, no peppers were used, so verify and update directly.
        if ($islegacy && password_verify($password, $user->password)) {
            $this->update($user, $password);
            return true;
        }

        // If the password is not a legacy hash, iterate through the peppers.
        $latestpepper = reset($peppers);
        // Add an empty pepper to the beginning of the array to check without any pepper.
        $peppers = [-1 => ''] + $peppers;
        foreach ($peppers as $pepper) {
            $pepperedpassword = $password . $pepper;

            if (password_verify($pepperedpassword, $user->password)) {
                // If the pepper used is not the latest one, update the password.
                if ($pepper !== $latestpepper) {
                    $this->update($user, $password);
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate hash for a plain text password.
     *
     * @param string $password Plain text password to be hashed
     * @param bool $fasthash If true, use a low number of rounds (faster but less secure)
     * @param int $pepperlength Length of the pepper
     * @return string The hashed password
     */
    public function hash(
        #[\SensitiveParameter] string $password,
        bool $fasthash = false,
        int $pepperlength = 0,
    ): string {
        if ($this->exceeds_max_length($password, $pepperlength)) {
            throw new moodle_exception(get_string('passwordexceeded', 'error', MAX_PASSWORD_CHARACTERS));
        }

        // Set the cost factor to 5000 for fast hashing, otherwise use default cost.
        $rounds = $fasthash ? 5000 : 10000;

        // First generate a cryptographically suitable salt.
        $randombytes = random_bytes(16);
        $salt = substr(strtr(base64_encode($randombytes), '+', '.'), 0, 16);

        // Now construct the password string with the salt and number of rounds.
        // The password string is in the format $algorithm$rounds$salt$hash. ($6 is the SHA512 algorithm).
        return crypt($password, implode('$', [
            '',
            '6',
            "rounds={$rounds}",
            $salt,
            '',
        ]));
    }

    /**
     * Update password hash in user object (if necessary).
     *
     * The password is updated if:
     * 1. The password has changed (the hash of $user->password is different
     *    to the hash of $password).
     * 2. The existing hash is using an out-of-date algorithm (or the legacy md5 algorithm).
     *
     * The password is peppered with the latest pepper before hashing, if peppers are available.
     * Updating the password will modify the $user object and the database record to use the
     * current hashing algorithm. It will remove Web Services user tokens too.
     *
     * @param \stdClass $user User object (password property may be updated)
     * @param string|null $password Plain text password
     * @param bool $fasthash If true, use a low cost factor when generating the hash
     * @return bool Always returns true
     */
    public function update(
        \stdClass $user,
        #[\SensitiveParameter] ?string $password,
        bool $fasthash = false,
    ): bool {
        global $CFG, $DB;

        // Add the latest password pepper to the password before further processing.
        $peppers = $this->get_peppers();
        if (!empty($peppers)) {
            $password = $password . reset($peppers);
        }

        // Figure out what the hashed password should be.
        if (!isset($user->auth)) {
            debugging(
                'User record in update_internal_user_password() must include field auth',
                DEBUG_DEVELOPER,
            );
            $user->auth = $DB->get_field('user', 'auth', ['id' => $user->id]);
        }
        $authplugin = \core\di::get(\core\authentication::class)->get_plugin($user->auth);
        if ($authplugin->prevent_local_passwords()) {
            $hashedpassword = AUTH_PASSWORD_NOT_CACHED;
        } else {
            $hashedpassword = $this->hash($password, $fasthash);
        }

        $algorithmchanged = false;

        if ($hashedpassword === AUTH_PASSWORD_NOT_CACHED) {
            $passwordchanged = ($user->password !== $hashedpassword);
        } else if (isset($user->password)) {
            $passwordchanged = !password_verify($password, $user->password);
            $algorithmchanged = $this->is_legacy_hash($user->password);
        } else {
            // While creating new user, password is unset in $user object, to avoid
            // saving it with user_create().
            $passwordchanged = true;
        }

        if ($passwordchanged || $algorithmchanged) {
            $DB->set_field('user', 'password', $hashedpassword, ['id' => $user->id]);
            $user->password = $hashedpassword;

            // Trigger event.
            $user = $DB->get_record('user', ['id' => $user->id]);
            \core\event\user_password_updated::create_from_user($user)->trigger();

            // Remove WS user tokens.
            if (!empty($CFG->passwordchangetokendeletion)) {
                require_once($CFG->dirroot . '/webservice/lib.php');
                \webservice::delete_user_ws_tokens($user->id);
            }
        }

        return true;
    }

    /**
     * Returns true if the password exceeds the maximum allowed length.
     *
     * @param string $password The password to check
     * @param int $pepperlength Additional pepper length to account for
     * @return bool
     */
    public function exceeds_max_length(string $password, int $pepperlength = 0): bool {
        return (strlen($password) > (MAX_PASSWORD_CHARACTERS + $pepperlength));
    }

    /**
     * Check password against the configured password policy.
     *
     * If the password policy is disabled, this always returns true (no errors).
     *
     * @param string $password The password to check
     * @param string|null $errmsg Concatenated error messages (HTML divs), set by reference
     * @param \stdClass|null $user The user object to validate against
     * @return bool True if the password meets the policy
     */
    public function check_policy(
        string $password,
        ?string &$errmsg,
        ?\stdClass $user = null,
    ): bool {
        global $CFG;

        if (!empty($CFG->passwordpolicy) && !isguestuser($user)) {
            $errors = $this->get_policy_errors($password, $user);

            foreach ($errors as $error) {
                $errmsg .= '<div>' . $error . '</div>';
            }
        }

        return $errmsg == '';
    }

    /**
     * Validate a password against the configured password policy.
     *
     * Note: This method is unaffected by whether the password policy is enabled or not.
     *
     * @param string $password The password to be checked against the password policy
     * @param \stdClass|null $user The user object to perform password validation against
     * @return string[] Array of error messages
     */
    public function get_policy_errors(string $password, ?\stdClass $user = null): array {
        global $CFG;

        $errors = [];

        if (\core_text::strlen($password) < $CFG->minpasswordlength) {
            $errors[] = get_string('errorminpasswordlength', 'auth', $CFG->minpasswordlength);
        }
        if (preg_match_all('/[[:digit:]]/u', $password, $matches) < $CFG->minpassworddigits) {
            $errors[] = get_string('errorminpassworddigits', 'auth', $CFG->minpassworddigits);
        }
        if (preg_match_all('/[[:lower:]]/u', $password, $matches) < $CFG->minpasswordlower) {
            $errors[] = get_string('errorminpasswordlower', 'auth', $CFG->minpasswordlower);
        }
        if (preg_match_all('/[[:upper:]]/u', $password, $matches) < $CFG->minpasswordupper) {
            $errors[] = get_string('errorminpasswordupper', 'auth', $CFG->minpasswordupper);
        }
        if (preg_match_all('/[^[:upper:][:lower:][:digit:]]/u', $password, $matches) < $CFG->minpasswordnonalphanum) {
            $errors[] = get_string('errorminpasswordnonalphanum', 'auth', $CFG->minpasswordnonalphanum);
        }
        if (!$this->check_consecutive_identical_characters($password, $CFG->maxconsecutiveidentchars)) {
            $errors[] = get_string('errormaxconsecutiveidentchars', 'auth', $CFG->maxconsecutiveidentchars);
        }

        // Fire any additional password policy functions from plugins.
        $pluginsfunction = get_plugins_with_function('check_password_policy');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginerr = $pluginfunction($password, $user);
                if ($pluginerr) {
                    $errors[] = $pluginerr;
                }
            }
        }

        return $errors;
    }

    /**
     * Generate a random password that meets the configured password policy.
     *
     * @param int $maxlen Maximum length of the generated password
     * @return string The generated password
     */
    public function generate(int $maxlen = 10): string {
        global $CFG;

        if (empty($CFG->passwordpolicy)) {
            $fillers = PASSWORD_DIGITS;
            $wordlist = file($CFG->wordlist);
            $word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
            $word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
            $filler1 = $fillers[rand(0, strlen($fillers) - 1)];
            $password = $word1 . $filler1 . $word2;
        } else {
            $minlen = !empty($CFG->minpasswordlength) ? $CFG->minpasswordlength : 0;
            $digits = $CFG->minpassworddigits;
            $lower = $CFG->minpasswordlower;
            $upper = $CFG->minpasswordupper;
            $nonalphanum = $CFG->minpasswordnonalphanum;
            $total = $lower + $upper + $digits + $nonalphanum;
            // Var minlength should be the greater one of the two ( $minlen and $total ).
            $minlen = $minlen < $total ? $total : $minlen;
            // Var maxlen can never be smaller than minlen.
            $maxlen = $minlen > $maxlen ? $minlen : $maxlen;
            $additional = $maxlen - $total;

            // Make sure we have enough characters to fulfill
            // complexity requirements.
            $passworddigits = PASSWORD_DIGITS;
            while ($digits > strlen($passworddigits)) {
                $passworddigits .= PASSWORD_DIGITS;
            }
            $passwordlower = PASSWORD_LOWER;
            while ($lower > strlen($passwordlower)) {
                $passwordlower .= PASSWORD_LOWER;
            }
            $passwordupper = PASSWORD_UPPER;
            while ($upper > strlen($passwordupper)) {
                $passwordupper .= PASSWORD_UPPER;
            }
            $passwordnonalphanum = PASSWORD_NONALPHANUM;
            while ($nonalphanum > strlen($passwordnonalphanum)) {
                $passwordnonalphanum .= PASSWORD_NONALPHANUM;
            }

            // Now mix and shuffle it all.
            $password = str_shuffle(
                substr(str_shuffle($passwordlower), 0, $lower)
                . substr(str_shuffle($passwordupper), 0, $upper)
                . substr(str_shuffle($passworddigits), 0, $digits)
                . substr(str_shuffle($passwordnonalphanum), 0, $nonalphanum)
                . substr(
                    str_shuffle(
                        "{$passwordlower}{$passwordupper}{$passworddigits}{$passwordnonalphanum}",
                    ),
                    0,
                    $additional,
                ),
            );
        }

        return substr($password, 0, $maxlen);
    }

    /**
     * Check whether the given password has no more than the specified
     * number of consecutive identical characters.
     *
     * @param string $password Password to be checked
     * @param int $maxchars Maximum number of consecutive identical characters
     * @return bool True if the password passes the check
     */
    public function check_consecutive_identical_characters(string $password, int $maxchars): bool {
        if ($maxchars < 1) {
            return true; // Zero 0 is to disable this check.
        }
        if (strlen($password) <= $maxchars) {
            return true; // Too short to fail this test.
        }

        $previouschar = '';
        $consecutivecount = 1;
        foreach (str_split($password) as $char) {
            if ($char != $previouschar) {
                $consecutivecount = 1;
            } else {
                $consecutivecount++;
                if ($consecutivecount > $maxchars) {
                    return false; // Check failed already.
                }
            }

            $previouschar = $char;
        }

        return true;
    }
}
