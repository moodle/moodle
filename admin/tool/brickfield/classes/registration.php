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

namespace tool_brickfield;

/**
 * Class registration contains the functions to manage registration validation.
 *
 * @package     tool_brickfield
 * @author      2021 Onwards Mike Churchward <mike@brickfieldlabs.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class registration {

    /** @var int Registration information has not been entered. */
    const NOT_ENTERED = 0;

    /** @var int Registration information has been entered but not externally validated. */
    const PENDING = 1;

    /** @var int Registration information was entered but was not validated within the defined grace periods. */
    const INVALID = 2;

    /** @var int Registration information has been externally validated. */
    const VALIDATED = 3;

    /** @var int Registration information has expired and needs to be revalidated. */
    const EXPIRED = 4;

    /** @var int Registration validation attempted, but failed. */
    const ERROR = 5;

    /** @var string Name of variable storing the registration status. */
    const STATUS = 'bfregstatus';

    /** @var string Name of variable storing the last time the registration information was checked. */
    const VALIDATION_CHECK_TIME = 'bfregvalidationchecktime';

    /** @var string Name of variable storing the time the registration information was validated. */
    const VALIDATION_TIME = 'bfregvalidationtime';

    /** @var string Name of variable storing the time the summary data was last sent. */
    const SUMMARY_TIME = 'bfsummarytime';

    /** @var string Name of variable storing the registration API key. */
    const API_KEY = 'key';

    /** @var string Name of variable storing the registration API key. */
    const SECRET_KEY = 'hash';

    /** @var string Name of the variable storing the site id. */
    const SITEID = 'id';

    /** @var int The current validation status. */
    protected $validation;

    /** @var int The last time the validation was checked. */
    protected $checktime;

    /** @var int The last time the validation time was confirmed. */
    protected $validationtime;

    /** @var int The last time the summary data was sent. */
    protected $summarytime;

    /** @var string The API key required for registration. */
    protected $apikey;

    /** @var string The secret key required for registration. */
    protected $secretkey;

    /** @var string The registered site id. */
    protected $siteid;

    /** @var string The URL to register at. */
    private static $regurl = 'https://account.mybrickfield.ie/register';

    /** @var string The URL to view terms at. */
    private static $termsurl = 'https://account.mybrickfield.ie/terms';

    /**
     * Object registration constructor.
     * @throws \dml_exception
     */
    public function __construct() {
        $this->validation = $this->get_status();
        $this->checktime = $this->get_check_time();
        $this->validationtime = $this->get_validation_time();
        $this->summarytime = $this->get_summary_time();
        $this->apikey = $this->get_api_key();
        $this->secretkey = $this->get_secret_key();
        $this->siteid = $this->get_siteid();
    }

    /**
     * System can be used when it has been validated, or when its still awaiting validation.
     * @return bool
     */
    public function toolkit_is_active(): bool {
        return $this->status_is_validated() || $this->validation_pending();
    }

    /**
     * The "not validated" state also needs the grace period to still be in effect.
     * @return bool
     */
    public function validation_pending(): bool {
        return ($this->status_is_pending() || $this->status_is_error()) && $this->grace_period_valid();
    }

    /**
     * Return true if there was a validation error.
     * @return bool
     */
    public function validation_error(): bool {
        return $this->status_is_error();
    }

    /**
     * Perform all necessary steps when new keys are added. Also check that they actually look like keys.
     * @param string $apikey
     * @param string $secretkey
     * @return bool
     */
    public function set_keys_for_registration(string $apikey, string $secretkey): bool {
        if ($this->keys_are_valid($apikey, $secretkey)) {
            $this->set_api_key($apikey);
            $this->set_secret_key($secretkey);
            $this->set_not_validated();
            if ($this->summarytime <= 0) {
                $this->set_summary_time();
            }
            return true;
        } else {
            $this->set_api_key('');
            $this->set_secret_key('');
            $this->set_not_entered();
            return false;
        }
    }

    /**
     * If the registration is not already valid, validate it. This may connect to the registration site.
     * @return bool
     * @throws \dml_exception
     */
    public function validate(): bool {
        // If this is currently valid, return true, unless its time to check again.
        if ($this->status_is_validated()) {
            // If the summary data has not been sent in over a week, invalidate the registration.
            if ($this->summarydata_grace_period_expired()) {
                $this->set_invalid();
                return false;
            }
            // Confirm registration after the grace period has expired.
            if ($this->grace_period_valid()) {
                return true;
            } else {
                // Recheck the registration.
                return $this->revalidate();
            }
        }

        // Check for valid keys, and possibly move status to validation stage.
        if (!$this->keys_are_valid()) {
            // The current stored keys are not valid format, set the status to "not entered".
            $this->set_not_entered();
            return false;
        } else if ($this->status_is_not_entered()) {
            // If no keys have previously been seen, move to validation stage.
            $this->set_not_validated();
        }

        // If no validation has been confirmed, check the registration site.
        if ($this->validation_pending()) {
            $brickfieldconnect = $this->get_registration_connection();
            $this->set_check_time();
            if ($brickfieldconnect->is_registered() || $brickfieldconnect->update_registration($this->apikey, $this->secretkey)) {
                // Keys are present and have been validated.
                $this->set_valid();
                return true;
            } else {
                // Keys are present but were not validated.
                $this->set_error();
            }
        }

        // If any of the grace periods have passed without a validation, invalidate the registration.
        if (!$this->grace_period_valid() || $this->summarydata_grace_period_expired()) {
            $this->set_invalid();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Even if the regisration is currently valid, validate it again.
     * @return bool
     * @throws \dml_exception
     */
    public function revalidate(): bool {
        if ($this->status_is_validated()) {
            $this->set_not_validated();
        }
        return $this->validate();
    }

    /**
     * Get api key.
     * @return string
     * @throws \dml_exception
     */
    public function get_api_key(): string {
        $key = get_config(manager::PLUGINNAME, self::API_KEY);
        if ($key === false) {
            // Not set in config yet, so default it to "".
            $key = '';
            $this->set_api_key($key);
        }
        return $key;
    }

    /**
     * Get secret key.
     * @return string
     * @throws \dml_exception
     */
    public function get_secret_key(): string {
        $key = get_config(manager::PLUGINNAME, self::SECRET_KEY);
        if ($key === false) {
            // Not set in config yet, so default it to "".
            $key = '';
            $this->set_secret_key($key);
        }
        return $key;
    }

    /**
     * Get the registration URL.
     * @return string
     */
    public function get_regurl(): string {
        return self::$regurl;
    }

    /**
     * Get the terms and conditions URL.
     * @return string
     */
    public function get_termsurl(): string {
        return self::$termsurl;
    }

    /**
     * Perform all actions needed to note that the summary data was sent.
     */
    public function mark_summary_data_sent() {
        $this->set_summary_time();
    }

    /**
     * Set the registered site id.
     * @param int $id
     * @return bool
     */
    public function set_siteid(int $id): bool {
        $this->siteid = $id;
        return set_config(self::SITEID, $id, manager::PLUGINNAME);
    }

    /**
     * Return the registered site id.
     * @return int
     * @throws \dml_exception
     */
    public function get_siteid(): int {
        $siteid = get_config(manager::PLUGINNAME, self::SITEID);
        if ($siteid === false) {
            // Not set in config yet, so default it to 0.
            $siteid = 0;
            $this->set_siteid($siteid);
        }
        return (int)$siteid;
    }

    /**
     * Set the status as keys "not entered".
     * @return bool
     */
    protected function set_not_entered(): bool {
        return $this->set_status(self::NOT_ENTERED);
    }

    /**
     * "Not validated" means we have keys, but have not confirmed them yet. Set the validation time to start the grace period.
     * @return bool
     */
    protected function set_not_validated(): bool {
        $this->set_validation_time();
        return $this->set_status(self::PENDING);
    }

    /**
     * Set the registration as invalid.
     * @return bool
     */
    protected function set_invalid(): bool {
        $this->set_api_key('');
        $this->set_secret_key('');
        $this->set_siteid(0);
        return $this->set_status(self::INVALID);
    }

    /**
     * Set the registration as valid.
     * @return bool
     */
    protected function set_valid(): bool {
        $this->set_validation_time();
        $this->set_summary_time();
        return $this->set_status(self::VALIDATED);
    }

    /**
     * Set the status to "expired".
     * @return bool
     */
    protected function set_expired(): bool {
        return $this->set_status(self::EXPIRED);
    }

    /**
     * Set the status to "error".
     * @return bool
     */
    protected function set_error(): bool {
        return $this->set_status(self::ERROR);
    }

    /**
     * Set the configured api key value.
     * @param string $keyvalue
     * @return bool
     */
    protected function set_api_key(string $keyvalue): bool {
        $this->apikey = $keyvalue;
        return set_config(self::API_KEY, $keyvalue, manager::PLUGINNAME);
    }

    /**
     * Set the configured secret key value.
     * @param string $keyvalue
     * @return bool
     */
    protected function set_secret_key(string $keyvalue): bool {
        $this->secretkey = $keyvalue;
        return set_config(self::SECRET_KEY, $keyvalue, manager::PLUGINNAME);
    }

    /**
     * Return true if the logic says that the registration is valid.
     * @return bool
     */
    protected function status_is_validated(): bool {
        return $this->validation == self::VALIDATED;
    }

    /**
     * Return true if the current status is "not entered".
     * @return bool
     */
    protected function status_is_not_entered(): bool {
        return $this->validation == self::NOT_ENTERED;
    }

    /**
     * Return true if the current status is "pending".
     * @return bool
     */
    protected function status_is_pending(): bool {
        return $this->validation == self::PENDING;
    }

    /**
     * Return true if the current status is "expired".
     * @return bool
     */
    protected function status_is_expired(): bool {
        return $this->validation == self::EXPIRED;
    }

    /**
     * Return true if the current status is "invalid".
     * @return bool
     */
    protected function status_is_invalid(): bool {
        return $this->validation == self::INVALID;
    }

    /**
     * Return true if the current status is "error".
     * @return bool
     */
    protected function status_is_error() {
        return $this->validation == self::ERROR;
    }

    /**
     * Set the current registration status.
     * @param int $status
     * @return bool
     */
    protected function set_status(int $status): bool {
        $this->validation = $status;
        return set_config(self::STATUS, $status, manager::PLUGINNAME);
    }

    /**
     * Return the current registration status.
     * @return int
     * @throws \dml_exception
     */
    protected function get_status(): int {
        $status = get_config(manager::PLUGINNAME, self::STATUS);
        if ($status === false) {
            // Not set in config yet, so default it to "NOT_ENTERED".
            $status = self::NOT_ENTERED;
            $this->set_status($status);
        }
        return (int)$status;
    }

    /**
     * Set the time of the last registration check.
     * @param int $time
     * @return bool
     */
    protected function set_check_time(int $time = 0): bool {
        $time = ($time == 0) ? time() : $time;
        $this->checktime = $time;
        return set_config(self::VALIDATION_CHECK_TIME, $time, manager::PLUGINNAME);
    }

    /**
     * Get the time of the last registration check.
     * @return int
     * @throws \dml_exception
     */
    protected function get_check_time(): int {
        $time = get_config(manager::PLUGINNAME, self::VALIDATION_CHECK_TIME);
        if ($time === false) {
            // Not set in config yet, so default it to 0.
            $time = 0;
            $this->set_check_time($time);
        }
        return (int)$time;
    }

    /**
     * Set the registration validation time.
     * @param int $time
     * @return bool
     */
    protected function set_validation_time(int $time = 0): bool {
        $time = ($time == 0) ? time() : $time;
        $this->validationtime = $time;
        return set_config(self::VALIDATION_TIME, $time, manager::PLUGINNAME);
    }

    /**
     * Return the time of the registration validation.
     * @return int
     * @throws \dml_exception
     */
    protected function get_validation_time(): int {
        $time = get_config(manager::PLUGINNAME, self::VALIDATION_TIME);
        if ($time === false) {
            // Not set in config yet, so default it to 0.
            $time = 0;
            $this->set_validation_time($time);
        }
        return (int)$time;
    }

    /**
     * Set the time of the summary update.
     * @param int $time
     * @return bool
     */
    protected function set_summary_time(int $time = 0): bool {
        $time = ($time == 0) ? time() : $time;
        $this->summarytime = $time;
        return set_config(self::SUMMARY_TIME, $time, manager::PLUGINNAME);
    }

    /**
     * Return the time of the last summary update.
     * @return int
     * @throws \dml_exception
     */
    protected function get_summary_time(): int {
        $time = get_config(manager::PLUGINNAME, self::SUMMARY_TIME);
        if ($time === false) {
            // Not set in config yet, so default it to 0.
            $time = 0;
            $this->set_summary_time($time);
        }
        return (int)$time;
    }

    /**
     * Return true if all keys have valid format.
     * @param string|null $apikey
     * @param string|null $secretkey
     * @return bool
     */
    protected function keys_are_valid(?string $apikey = null, ?string $secretkey = null): bool {
        $apikey = $apikey ?? $this->apikey;
        $secretkey = $secretkey ?? $this->secretkey;
        return $this->apikey_is_valid($apikey) && $this->secretkey_is_valid($secretkey);
    }

    /**
     * Validates that the entered API key is in the expected format.
     * @param string $apikey
     * @return bool
     */
    protected function apikey_is_valid(string $apikey): bool {
        return $this->valid_key_format($apikey);
    }

    /**
     * Validates that the entered Secret key is in the expected format.
     * @param string $secretkey
     * @return bool
     */
    protected function secretkey_is_valid(string $secretkey): bool {
        return $this->valid_key_format($secretkey);
    }

    /**
     * Validates that the passed in key looks like an MD5 hash.
     * @param string $key
     * @return bool
     */
    protected function valid_key_format(string $key): bool {
        return !empty($key) && (preg_match('/^[a-f0-9]{32}$/', $key) === 1);
    }

    /**
     * Get the registration grace period.
     * @return int
     */
    protected function get_grace_period(): int {
        return WEEKSECS;
    }

    /**
     * Check if the unvalidated time is still within the grace period.
     * @return bool
     */
    protected function grace_period_valid(): bool {
        return (time() - $this->validationtime) < $this->get_grace_period();
    }

    /**
     * Check if the last time the summary data was sent is within the grace period.
     * @return bool
     */
    protected function summarydata_grace_period_expired(): bool {
        return (time() - $this->summarytime) > $this->get_grace_period();
    }

    /**
     * Return an instance of the connection class.
     * @return brickfieldconnect
     */
    protected function get_registration_connection(): brickfieldconnect {
        return new brickfieldconnect();
    }
}
