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

/**
 * Class process
 *
 * @package     tool_uploaduser
 * @copyright   2020 Moodle
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_uploaduser;

defined('MOODLE_INTERNAL') || die();

use tool_uploaduser\local\field_value_validators;

require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/locallib.php');

/**
 * Process CSV file with users data, this will create/update users, enrol them into courses, etc
 *
 * @package     tool_uploaduser
 * @copyright   2020 Moodle
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process {

    /** @var \csv_import_reader  */
    protected $cir;
    /** @var \stdClass  */
    protected $formdata;
    /** @var \uu_progress_tracker  */
    protected $upt;
    /** @var array  */
    protected $filecolumns = null;
    /** @var int  */
    protected $today;
    /** @var \enrol_plugin|null */
    protected $manualenrol = null;
    /** @var array */
    protected $standardfields = [];
    /** @var array */
    protected $profilefields = [];
    /** @var \profile_field_base[] */
    protected $allprofilefields = [];
    /** @var string|\uu_progress_tracker|null  */
    protected $progresstrackerclass = null;

    /** @var int */
    protected $usersnew      = 0;
    /** @var int */
    protected $usersupdated  = 0;
    /** @var int /not printed yet anywhere */
    protected $usersuptodate = 0;
    /** @var int */
    protected $userserrors   = 0;
    /** @var int */
    protected $deletes       = 0;
    /** @var int */
    protected $deleteerrors  = 0;
    /** @var int */
    protected $renames       = 0;
    /** @var int */
    protected $renameerrors  = 0;
    /** @var int */
    protected $usersskipped  = 0;
    /** @var int */
    protected $weakpasswords = 0;

    /** @var array course cache - do not fetch all courses here, we  will not probably use them all anyway */
    protected $ccache         = [];
    /** @var array */
    protected $cohorts        = [];
    /** @var array  Course roles lookup cache. */
    protected $rolecache      = [];
    /** @var array System roles lookup cache. */
    protected $sysrolecache   = [];
    /** @var array cache of used manual enrol plugins in each course */
    protected $manualcache    = [];
    /** @var array officially supported plugins that are enabled */
    protected $supportedauths = [];

    /**
     * process constructor.
     *
     * @param \csv_import_reader $cir
     * @param string|null $progresstrackerclass
     * @throws \coding_exception
     */
    public function __construct(\csv_import_reader $cir, string $progresstrackerclass = null) {
        $this->cir = $cir;
        if ($progresstrackerclass) {
            if (!class_exists($progresstrackerclass) || !is_subclass_of($progresstrackerclass, \uu_progress_tracker::class)) {
                throw new \coding_exception('Progress tracker class must extend \uu_progress_tracker');
            }
            $this->progresstrackerclass = $progresstrackerclass;
        } else {
            $this->progresstrackerclass = \uu_progress_tracker::class;
        }

        // Keep timestamp consistent.
        $today = time();
        $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
        $this->today = $today;

        $this->rolecache      = uu_allowed_roles_cache(); // Course roles lookup cache.
        $this->sysrolecache   = uu_allowed_sysroles_cache(); // System roles lookup cache.
        $this->supportedauths = uu_supported_auths(); // Officially supported plugins that are enabled.

        if (enrol_is_enabled('manual')) {
            // We use only manual enrol plugin here, if it is disabled no enrol is done.
            $this->manualenrol = enrol_get_plugin('manual');
        }

        $this->find_profile_fields();
        $this->find_standard_fields();
    }

    /**
     * Standard user fields.
     */
    protected function find_standard_fields(): void {
        $this->standardfields = array('id', 'username', 'email', 'emailstop',
            'city', 'country', 'lang', 'timezone', 'mailformat',
            'maildisplay', 'maildigest', 'htmleditor', 'autosubscribe',
            'institution', 'department', 'idnumber', 'phone1', 'phone2', 'address',
            'description', 'descriptionformat', 'password',
            'auth',        // Watch out when changing auth type or using external auth plugins!
            'oldusername', // Use when renaming users - this is the original username.
            'suspended',   // 1 means suspend user account, 0 means activate user account, nothing means keep as is.
            'theme',       // Define a theme for user when 'allowuserthemes' is enabled.
            'deleted',     // 1 means delete user
            'mnethostid',  // Can not be used for adding, updating or deleting of users - only for enrolments,
                           // groups, cohorts and suspending.
            'interests',
        );
        // Include all name fields.
        $this->standardfields = array_merge($this->standardfields, \core_user\fields::get_name_fields());
    }

    /**
     * Profile fields
     */
    protected function find_profile_fields(): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        $this->allprofilefields = profile_get_user_fields_with_data(0);
        $this->profilefields = [];
        if ($proffields = $this->allprofilefields) {
            foreach ($proffields as $key => $proffield) {
                $profilefieldname = 'profile_field_'.$proffield->get_shortname();
                $this->profilefields[] = $profilefieldname;
                // Re-index $proffields with key as shortname. This will be
                // used while checking if profile data is key and needs to be converted (eg. menu profile field).
                $proffields[$profilefieldname] = $proffield;
                unset($proffields[$key]);
            }
            $this->allprofilefields = $proffields;
        }
    }

    /**
     * Returns the list of columns in the file
     *
     * @return array
     */
    public function get_file_columns(): array {
        if ($this->filecolumns === null) {
            $returnurl = new \moodle_url('/admin/tool/uploaduser/index.php');
            $this->filecolumns = uu_validate_user_upload_columns($this->cir,
                $this->standardfields, $this->profilefields, $returnurl);
        }
        return $this->filecolumns;
    }

    /**
     * Set data from the form (or from CLI options)
     *
     * @param \stdClass $formdata
     */
    public function set_form_data(\stdClass $formdata): void {
        global $SESSION;
        $this->formdata = $formdata;

        // Clear bulk selection.
        if ($this->get_bulk()) {
            $SESSION->bulk_users = array();
        }
    }

    /**
     * Operation type
     * @return int
     */
    protected function get_operation_type(): int {
        return (int)$this->formdata->uutype;
    }

    /**
     * Setting to allow deletes
     * @return bool
     */
    protected function get_allow_deletes(): bool {
        $optype = $this->get_operation_type();
        return (!empty($this->formdata->uuallowdeletes) and $optype != UU_USER_ADDNEW and $optype != UU_USER_ADDINC);
    }

    /**
     * Setting to allow deletes
     * @return bool
     */
    protected function get_allow_renames(): bool {
        $optype = $this->get_operation_type();
        return (!empty($this->formdata->uuallowrenames) and $optype != UU_USER_ADDNEW and $optype != UU_USER_ADDINC);
    }

    /**
     * Setting to select for bulk actions (not available in CLI)
     * @return bool
     */
    public function get_bulk(): bool {
        return $this->formdata->uubulk ?? false;
    }

    /**
     * Setting for update type
     * @return int
     */
    protected function get_update_type(): int {
        return isset($this->formdata->uuupdatetype) ? $this->formdata->uuupdatetype : 0;
    }

    /**
     * Setting to allow update passwords
     * @return bool
     */
    protected function get_update_passwords(): bool {
        return !empty($this->formdata->uupasswordold)
            and $this->get_operation_type() != UU_USER_ADDNEW
            and $this->get_operation_type() != UU_USER_ADDINC
            and ($this->get_update_type() == UU_UPDATE_FILEOVERRIDE or $this->get_update_type() == UU_UPDATE_ALLOVERRIDE);
    }

    /**
     * Setting to allow email duplicates
     * @return bool
     */
    protected function get_allow_email_duplicates(): bool {
        global $CFG;
        return !(empty($CFG->allowaccountssameemail) ? 1 : $this->formdata->uunoemailduplicates);
    }

    /**
     * Setting for reset password
     * @return int UU_PWRESET_NONE, UU_PWRESET_WEAK, UU_PWRESET_ALL
     */
    protected function get_reset_passwords(): int {
        return isset($this->formdata->uuforcepasswordchange) ? $this->formdata->uuforcepasswordchange : UU_PWRESET_NONE;
    }

    /**
     * Setting to allow create passwords
     * @return bool
     */
    protected function get_create_paswords(): bool {
        return (!empty($this->formdata->uupasswordnew) and $this->get_operation_type() != UU_USER_UPDATE);
    }

    /**
     * Setting to allow suspends
     * @return bool
     */
    protected function get_allow_suspends(): bool {
        return !empty($this->formdata->uuallowsuspends);
    }

    /**
     * Setting to normalise user names
     * @return bool
     */
    protected function get_normalise_user_names(): bool {
        return !empty($this->formdata->uustandardusernames);
    }

    /**
     * Helper method to return Yes/No string
     *
     * @param bool $value
     * @return string
     */
    protected function get_string_yes_no($value): string {
        return $value ? get_string('yes') : get_string('no');
    }

    /**
     * Process the CSV file
     */
    public function process() {
        // Init csv import helper.
        $this->cir->init();

        $classname = $this->progresstrackerclass;
        $this->upt = new $classname();
        $this->upt->start(); // Start table.

        $linenum = 1; // Column header is first line.
        while ($line = $this->cir->next()) {
            $this->upt->flush();
            $linenum++;

            $this->upt->track('line', $linenum);
            $this->process_line($line);
        }

        $this->upt->close(); // Close table.
        $this->cir->close();
        $this->cir->cleanup(true);
    }

    /**
     * Prepare one line from CSV file as a user record
     *
     * @param array $line
     * @return \stdClass|null
     */
    protected function prepare_user_record(array $line): ?\stdClass {
        global $CFG, $USER;

        $user = new \stdClass();

        // Add fields to user object.
        foreach ($line as $keynum => $value) {
            if (!isset($this->get_file_columns()[$keynum])) {
                // This should not happen.
                continue;
            }
            $key = $this->get_file_columns()[$keynum];
            if (strpos($key, 'profile_field_') === 0) {
                // NOTE: bloody mega hack alert!!
                if (isset($USER->$key) and is_array($USER->$key)) {
                    // This must be some hacky field that is abusing arrays to store content and format.
                    $user->$key = array();
                    $user->{$key['text']}   = $value;
                    $user->{$key['format']} = FORMAT_MOODLE;
                } else {
                    $user->$key = trim($value);
                }
            } else {
                $user->$key = trim($value);
            }

            if (in_array($key, $this->upt->columns)) {
                // Default value in progress tracking table, can be changed later.
                $this->upt->track($key, s($value), 'normal');
            }
        }
        if (!isset($user->username)) {
            // Prevent warnings below.
            $user->username = '';
        }

        if ($this->get_operation_type() == UU_USER_ADDNEW or $this->get_operation_type() == UU_USER_ADDINC) {
            // User creation is a special case - the username may be constructed from templates using firstname and lastname
            // better never try this in mixed update types.
            $error = false;
            if (!isset($user->firstname) or $user->firstname === '') {
                $this->upt->track('status', get_string('missingfield', 'error', 'firstname'), 'error');
                $this->upt->track('firstname', get_string('error'), 'error');
                $error = true;
            }
            if (!isset($user->lastname) or $user->lastname === '') {
                $this->upt->track('status', get_string('missingfield', 'error', 'lastname'), 'error');
                $this->upt->track('lastname', get_string('error'), 'error');
                $error = true;
            }
            if ($error) {
                $this->userserrors++;
                return null;
            }
            // We require username too - we might use template for it though.
            if (empty($user->username) and !empty($this->formdata->username)) {
                $user->username = uu_process_template($this->formdata->username, $user);
                $this->upt->track('username', s($user->username));
            }
        }

        // Normalize username.
        $user->originalusername = $user->username;
        if ($this->get_normalise_user_names()) {
            $user->username = \core_user::clean_field($user->username, 'username');
        }

        // Make sure we really have username.
        if (empty($user->username)) {
            $this->upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
            $this->upt->track('username', get_string('error'), 'error');
            $this->userserrors++;
            return null;
        } else if ($user->username === 'guest') {
            $this->upt->track('status', get_string('guestnoeditprofileother', 'error'), 'error');
            $this->userserrors++;
            return null;
        }

        if ($user->username !== \core_user::clean_field($user->username, 'username')) {
            $this->upt->track('status', get_string('invalidusername', 'error', 'username'), 'error');
            $this->upt->track('username', get_string('error'), 'error');
            $this->userserrors++;
        }

        if (empty($user->mnethostid)) {
            $user->mnethostid = $CFG->mnet_localhost_id;
        }

        return $user;
    }

    /**
     * Process one line from CSV file
     *
     * @param array $line
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function process_line(array $line) {
        global $DB, $CFG, $SESSION;

        if (!$user = $this->prepare_user_record($line)) {
            return;
        }

        if ($existinguser = $DB->get_record('user', ['username' => $user->username, 'mnethostid' => $user->mnethostid])) {
            $this->upt->track('id', $existinguser->id, 'normal', false);
        }

        if ($user->mnethostid == $CFG->mnet_localhost_id) {
            $remoteuser = false;

            // Find out if username incrementing required.
            if ($existinguser and $this->get_operation_type() == UU_USER_ADDINC) {
                $user->username = uu_increment_username($user->username);
                $existinguser = false;
            }

        } else {
            if (!$existinguser or $this->get_operation_type() == UU_USER_ADDINC) {
                $this->upt->track('status', get_string('errormnetadd', 'tool_uploaduser'), 'error');
                $this->userserrors++;
                return;
            }

            $remoteuser = true;

            // Make sure there are no changes of existing fields except the suspended status.
            foreach ((array)$existinguser as $k => $v) {
                if ($k === 'suspended') {
                    continue;
                }
                if (property_exists($user, $k)) {
                    $user->$k = $v;
                }
                if (in_array($k, $this->upt->columns)) {
                    if ($k === 'password' or $k === 'oldusername' or $k === 'deleted') {
                        $this->upt->track($k, '', 'normal', false);
                    } else {
                        $this->upt->track($k, s($v), 'normal', false);
                    }
                }
            }
            unset($user->oldusername);
            unset($user->password);
            $user->auth = $existinguser->auth;
        }

        // Notify about nay username changes.
        if ($user->originalusername !== $user->username) {
            $this->upt->track('username', '', 'normal', false); // Clear previous.
            $this->upt->track('username', s($user->originalusername).'-->'.s($user->username), 'info');
        } else {
            $this->upt->track('username', s($user->username), 'normal', false);
        }
        unset($user->originalusername);

        // Verify if the theme is valid and allowed to be set.
        if (isset($user->theme)) {
            list($status, $message) = field_value_validators::validate_theme($user->theme);
            if ($status !== 'normal' && !empty($message)) {
                $this->upt->track('status', $message, $status);
                // Unset the theme when validation fails.
                unset($user->theme);
            }
        }

        // Add default values for remaining fields.
        $formdefaults = array();
        if (!$existinguser ||
                ($this->get_update_type() != UU_UPDATE_FILEOVERRIDE && $this->get_update_type() != UU_UPDATE_NOCHANGES)) {
            foreach ($this->standardfields as $field) {
                if (isset($user->$field)) {
                    continue;
                }
                // All validation moved to form2.
                if (isset($this->formdata->$field)) {
                    // Process templates.
                    $user->$field = uu_process_template($this->formdata->$field, $user);
                    $formdefaults[$field] = true;
                    if (in_array($field, $this->upt->columns)) {
                        $this->upt->track($field, s($user->$field), 'normal');
                    }
                }
            }
            foreach ($this->allprofilefields as $field => $profilefield) {
                if (isset($user->$field)) {
                    continue;
                }
                if (isset($this->formdata->$field)) {
                    // Process templates.
                    $user->$field = uu_process_template($this->formdata->$field, $user);

                    // Form contains key and later code expects value.
                    // Convert key to value for required profile fields.
                    if (method_exists($profilefield, 'convert_external_data')) {
                        $user->$field = $profilefield->edit_save_data_preprocess($user->$field, null);
                    }

                    $formdefaults[$field] = true;
                }
            }
        }

        // Delete user.
        if (!empty($user->deleted)) {
            if (!$this->get_allow_deletes() or $remoteuser) {
                $this->usersskipped++;
                $this->upt->track('status', get_string('usernotdeletedoff', 'error'), 'warning');
                return;
            }
            if ($existinguser) {
                if (is_siteadmin($existinguser->id)) {
                    $this->upt->track('status', get_string('usernotdeletedadmin', 'error'), 'error');
                    $this->deleteerrors++;
                    return;
                }
                if (delete_user($existinguser)) {
                    $this->upt->track('status', get_string('userdeleted', 'tool_uploaduser'));
                    $this->deletes++;
                } else {
                    $this->upt->track('status', get_string('usernotdeletederror', 'error'), 'error');
                    $this->deleteerrors++;
                }
            } else {
                $this->upt->track('status', get_string('usernotdeletedmissing', 'error'), 'error');
                $this->deleteerrors++;
            }
            return;
        }
        // We do not need the deleted flag anymore.
        unset($user->deleted);

        // Renaming requested?
        if (!empty($user->oldusername) ) {
            if (!$this->get_allow_renames()) {
                $this->usersskipped++;
                $this->upt->track('status', get_string('usernotrenamedoff', 'error'), 'warning');
                return;
            }

            if ($existinguser) {
                $this->upt->track('status', get_string('usernotrenamedexists', 'error'), 'error');
                $this->renameerrors++;
                return;
            }

            if ($user->username === 'guest') {
                $this->upt->track('status', get_string('guestnoeditprofileother', 'error'), 'error');
                $this->renameerrors++;
                return;
            }

            if ($this->get_normalise_user_names()) {
                $oldusername = \core_user::clean_field($user->oldusername, 'username');
            } else {
                $oldusername = $user->oldusername;
            }

            // No guessing when looking for old username, it must be exact match.
            if ($olduser = $DB->get_record('user',
                    ['username' => $oldusername, 'mnethostid' => $CFG->mnet_localhost_id])) {
                $this->upt->track('id', $olduser->id, 'normal', false);
                if (is_siteadmin($olduser->id)) {
                    $this->upt->track('status', get_string('usernotrenamedadmin', 'error'), 'error');
                    $this->renameerrors++;
                    return;
                }
                $DB->set_field('user', 'username', $user->username, ['id' => $olduser->id]);
                $this->upt->track('username', '', 'normal', false); // Clear previous.
                $this->upt->track('username', s($oldusername).'-->'.s($user->username), 'info');
                $this->upt->track('status', get_string('userrenamed', 'tool_uploaduser'));
                $this->renames++;
            } else {
                $this->upt->track('status', get_string('usernotrenamedmissing', 'error'), 'error');
                $this->renameerrors++;
                return;
            }
            $existinguser = $olduser;
            $existinguser->username = $user->username;
        }

        // Can we process with update or insert?
        $skip = false;
        switch ($this->get_operation_type()) {
            case UU_USER_ADDNEW:
                if ($existinguser) {
                    $this->usersskipped++;
                    $this->upt->track('status', get_string('usernotaddedregistered', 'error'), 'warning');
                    $skip = true;
                }
                break;

            case UU_USER_ADDINC:
                if ($existinguser) {
                    // This should not happen!
                    $this->upt->track('status', get_string('usernotaddederror', 'error'), 'error');
                    $this->userserrors++;
                    $skip = true;
                }
                break;

            case UU_USER_ADD_UPDATE:
                break;

            case UU_USER_UPDATE:
                if (!$existinguser) {
                    $this->usersskipped++;
                    $this->upt->track('status', get_string('usernotupdatednotexists', 'error'), 'warning');
                    $skip = true;
                }
                break;

            default:
                // Unknown type.
                $skip = true;
        }

        if ($skip) {
            return;
        }

        if ($existinguser) {
            $user->id = $existinguser->id;

            $this->upt->track('username', \html_writer::link(
                new \moodle_url('/user/profile.php', ['id' => $existinguser->id]), s($existinguser->username)), 'normal', false);
            $this->upt->track('suspended', $this->get_string_yes_no($existinguser->suspended) , 'normal', false);
            $this->upt->track('auth', $existinguser->auth, 'normal', false);

            if (is_siteadmin($user->id)) {
                $this->upt->track('status', get_string('usernotupdatedadmin', 'error'), 'error');
                $this->userserrors++;
                return;
            }

            $existinguser->timemodified = time();
            // Do NOT mess with timecreated or firstaccess here!

            // Load existing profile data.
            profile_load_data($existinguser);

            $doupdate = false;
            $dologout = false;

            if ($this->get_update_type() != UU_UPDATE_NOCHANGES and !$remoteuser) {
                if (!empty($user->auth) and $user->auth !== $existinguser->auth) {
                    $this->upt->track('auth', s($existinguser->auth).'-->'.s($user->auth), 'info', false);
                    $existinguser->auth = $user->auth;
                    if (!isset($this->supportedauths[$user->auth])) {
                        $this->upt->track('auth', get_string('userauthunsupported', 'error'), 'warning');
                    }
                    $doupdate = true;
                    if ($existinguser->auth === 'nologin') {
                        $dologout = true;
                    }
                }
                $allcolumns = array_merge($this->standardfields, $this->profilefields);
                foreach ($allcolumns as $column) {
                    if ($column === 'username' or $column === 'password' or $column === 'auth' or $column === 'suspended') {
                        // These can not be changed here.
                        continue;
                    }
                    if (!property_exists($user, $column) or !property_exists($existinguser, $column)) {
                        continue;
                    }
                    if ($this->get_update_type() == UU_UPDATE_MISSING) {
                        if (!is_null($existinguser->$column) and $existinguser->$column !== '') {
                            continue;
                        }
                    } else if ($this->get_update_type() == UU_UPDATE_ALLOVERRIDE) {
                        // We override everything.
                        null;
                    } else if ($this->get_update_type() == UU_UPDATE_FILEOVERRIDE) {
                        if (!empty($formdefaults[$column])) {
                            // Do not override with form defaults.
                            continue;
                        }
                    }
                    if ($existinguser->$column !== $user->$column) {
                        if ($column === 'email') {
                            $select = $DB->sql_like('email', ':email', false, true, false, '|');
                            $params = array('email' => $DB->sql_like_escape($user->email, '|'));
                            if ($DB->record_exists_select('user', $select , $params)) {

                                $changeincase = \core_text::strtolower($existinguser->$column) === \core_text::strtolower(
                                        $user->$column);

                                if ($changeincase) {
                                    // If only case is different then switch to lower case and carry on.
                                    $user->$column = \core_text::strtolower($user->$column);
                                    continue;
                                } else if (!$this->get_allow_email_duplicates()) {
                                    $this->upt->track('email', get_string('useremailduplicate', 'error'), 'error');
                                    $this->upt->track('status', get_string('usernotupdatederror', 'error'), 'error');
                                    $this->userserrors++;
                                    return;
                                } else {
                                    $this->upt->track('email', get_string('useremailduplicate', 'error'), 'warning');
                                }
                            }
                            if (!validate_email($user->email)) {
                                $this->upt->track('email', get_string('invalidemail'), 'warning');
                            }
                        }

                        if ($column === 'lang') {
                            if (empty($user->lang)) {
                                // Do not change to not-set value.
                                continue;
                            } else if (\core_user::clean_field($user->lang, 'lang') === '') {
                                $this->upt->track('status', get_string('cannotfindlang', 'error', $user->lang), 'warning');
                                continue;
                            }
                        }

                        if (in_array($column, $this->upt->columns)) {
                            $this->upt->track($column, s($existinguser->$column).'-->'.s($user->$column), 'info', false);
                        }
                        $existinguser->$column = $user->$column;
                        $doupdate = true;
                    }
                }
            }

            try {
                $auth = get_auth_plugin($existinguser->auth);
            } catch (\Exception $e) {
                $this->upt->track('auth', get_string('userautherror', 'error', s($existinguser->auth)), 'error');
                $this->upt->track('status', get_string('usernotupdatederror', 'error'), 'error');
                $this->userserrors++;
                return;
            }
            $isinternalauth = $auth->is_internal();

            // Deal with suspending and activating of accounts.
            if ($this->get_allow_suspends() and isset($user->suspended) and $user->suspended !== '') {
                $user->suspended = $user->suspended ? 1 : 0;
                if ($existinguser->suspended != $user->suspended) {
                    $this->upt->track('suspended', '', 'normal', false);
                    $this->upt->track('suspended',
                        $this->get_string_yes_no($existinguser->suspended).'-->'.$this->get_string_yes_no($user->suspended),
                        'info', false);
                    $existinguser->suspended = $user->suspended;
                    $doupdate = true;
                    if ($existinguser->suspended) {
                        $dologout = true;
                    }
                }
            }

            // Changing of passwords is a special case
            // do not force password changes for external auth plugins!
            $oldpw = $existinguser->password;

            if ($remoteuser) {
                // Do not mess with passwords of remote users.
                null;
            } else if (!$isinternalauth) {
                $existinguser->password = AUTH_PASSWORD_NOT_CACHED;
                $this->upt->track('password', '-', 'normal', false);
                // Clean up prefs.
                unset_user_preference('create_password', $existinguser);
                unset_user_preference('auth_forcepasswordchange', $existinguser);

            } else if (!empty($user->password)) {
                if ($this->get_update_passwords()) {
                    // Check for passwords that we want to force users to reset next
                    // time they log in.
                    $errmsg = null;
                    $weak = !check_password_policy($user->password, $errmsg, $user);
                    if ($this->get_reset_passwords() == UU_PWRESET_ALL or
                            ($this->get_reset_passwords() == UU_PWRESET_WEAK and $weak)) {
                        if ($weak) {
                            $this->weakpasswords++;
                            $this->upt->track('password', get_string('invalidpasswordpolicy', 'error'), 'warning');
                        }
                        set_user_preference('auth_forcepasswordchange', 1, $existinguser);
                    } else {
                        unset_user_preference('auth_forcepasswordchange', $existinguser);
                    }
                    unset_user_preference('create_password', $existinguser); // No need to create password any more.

                    // Use a low cost factor when generating bcrypt hash otherwise
                    // hashing would be slow when uploading lots of users. Hashes
                    // will be automatically updated to a higher cost factor the first
                    // time the user logs in.
                    $existinguser->password = hash_internal_user_password($user->password, true);
                    $this->upt->track('password', $user->password, 'normal', false);
                } else {
                    // Do not print password when not changed.
                    $this->upt->track('password', '', 'normal', false);
                }
            }

            if ($doupdate or $existinguser->password !== $oldpw) {
                // We want only users that were really updated.
                user_update_user($existinguser, false, false);

                $this->upt->track('status', get_string('useraccountupdated', 'tool_uploaduser'));
                $this->usersupdated++;

                if (!$remoteuser) {
                    // Pre-process custom profile menu fields data from csv file.
                    $existinguser = uu_pre_process_custom_profile_data($existinguser);
                    // Save custom profile fields data from csv file.
                    profile_save_data($existinguser);
                }

                if ($this->get_bulk() == UU_BULK_UPDATED or $this->get_bulk() == UU_BULK_ALL) {
                    if (!in_array($user->id, $SESSION->bulk_users)) {
                        $SESSION->bulk_users[] = $user->id;
                    }
                }

                // Trigger event.
                \core\event\user_updated::create_from_userid($existinguser->id)->trigger();

            } else {
                // No user information changed.
                $this->upt->track('status', get_string('useraccountuptodate', 'tool_uploaduser'));
                $this->usersuptodate++;

                if ($this->get_bulk() == UU_BULK_ALL) {
                    if (!in_array($user->id, $SESSION->bulk_users)) {
                        $SESSION->bulk_users[] = $user->id;
                    }
                }
            }

            if ($dologout) {
                \core\session\manager::kill_user_sessions($existinguser->id);
            }

        } else {
            // Save the new user to the database.
            $user->confirmed    = 1;
            $user->timemodified = time();
            $user->timecreated  = time();
            $user->mnethostid   = $CFG->mnet_localhost_id; // We support ONLY local accounts here, sorry.

            if (!isset($user->suspended) or $user->suspended === '') {
                $user->suspended = 0;
            } else {
                $user->suspended = $user->suspended ? 1 : 0;
            }
            $this->upt->track('suspended', $this->get_string_yes_no($user->suspended), 'normal', false);

            if (empty($user->auth)) {
                $user->auth = 'manual';
            }
            $this->upt->track('auth', $user->auth, 'normal', false);

            // Do not insert record if new auth plugin does not exist!
            try {
                $auth = get_auth_plugin($user->auth);
            } catch (\Exception $e) {
                $this->upt->track('auth', get_string('userautherror', 'error', s($user->auth)), 'error');
                $this->upt->track('status', get_string('usernotaddederror', 'error'), 'error');
                $this->userserrors++;
                return;
            }
            if (!isset($this->supportedauths[$user->auth])) {
                $this->upt->track('auth', get_string('userauthunsupported', 'error'), 'warning');
            }

            $isinternalauth = $auth->is_internal();

            if (empty($user->email)) {
                $this->upt->track('email', get_string('invalidemail'), 'error');
                $this->upt->track('status', get_string('usernotaddederror', 'error'), 'error');
                $this->userserrors++;
                return;

            } else if ($DB->record_exists('user', ['email' => $user->email])) {
                if (!$this->get_allow_email_duplicates()) {
                    $this->upt->track('email', get_string('useremailduplicate', 'error'), 'error');
                    $this->upt->track('status', get_string('usernotaddederror', 'error'), 'error');
                    $this->userserrors++;
                    return;
                } else {
                    $this->upt->track('email', get_string('useremailduplicate', 'error'), 'warning');
                }
            }
            if (!validate_email($user->email)) {
                $this->upt->track('email', get_string('invalidemail'), 'warning');
            }

            if (empty($user->lang)) {
                $user->lang = '';
            } else if (\core_user::clean_field($user->lang, 'lang') === '') {
                $this->upt->track('status', get_string('cannotfindlang', 'error', $user->lang), 'warning');
                $user->lang = '';
            }

            $forcechangepassword = false;

            if ($isinternalauth) {
                if (empty($user->password)) {
                    if ($this->get_create_paswords()) {
                        $user->password = 'to be generated';
                        $this->upt->track('password', '', 'normal', false);
                        $this->upt->track('password', get_string('uupasswordcron', 'tool_uploaduser'), 'warning', false);
                    } else {
                        $this->upt->track('password', '', 'normal', false);
                        $this->upt->track('password', get_string('missingfield', 'error', 'password'), 'error');
                        $this->upt->track('status', get_string('usernotaddederror', 'error'), 'error');
                        $this->userserrors++;
                        return;
                    }
                } else {
                    $errmsg = null;
                    $weak = !check_password_policy($user->password, $errmsg, $user);
                    if ($this->get_reset_passwords() == UU_PWRESET_ALL or
                            ($this->get_reset_passwords() == UU_PWRESET_WEAK and $weak)) {
                        if ($weak) {
                            $this->weakpasswords++;
                            $this->upt->track('password', get_string('invalidpasswordpolicy', 'error'), 'warning');
                        }
                        $forcechangepassword = true;
                    }
                    // Use a low cost factor when generating bcrypt hash otherwise
                    // hashing would be slow when uploading lots of users. Hashes
                    // will be automatically updated to a higher cost factor the first
                    // time the user logs in.
                    $user->password = hash_internal_user_password($user->password, true);
                }
            } else {
                $user->password = AUTH_PASSWORD_NOT_CACHED;
                $this->upt->track('password', '-', 'normal', false);
            }

            $user->id = user_create_user($user, false, false);
            $this->upt->track('username', \html_writer::link(
                new \moodle_url('/user/profile.php', ['id' => $user->id]), s($user->username)), 'normal', false);

            // Pre-process custom profile menu fields data from csv file.
            $user = uu_pre_process_custom_profile_data($user);
            // Save custom profile fields data.
            profile_save_data($user);

            if ($forcechangepassword) {
                set_user_preference('auth_forcepasswordchange', 1, $user);
            }
            if ($user->password === 'to be generated') {
                set_user_preference('create_password', 1, $user);
            }

            // Trigger event.
            \core\event\user_created::create_from_userid($user->id)->trigger();

            $this->upt->track('status', get_string('newuser'));
            $this->upt->track('id', $user->id, 'normal', false);
            $this->usersnew++;

            // Make sure user context exists.
            \context_user::instance($user->id);

            if ($this->get_bulk() == UU_BULK_NEW or $this->get_bulk() == UU_BULK_ALL) {
                if (!in_array($user->id, $SESSION->bulk_users)) {
                    $SESSION->bulk_users[] = $user->id;
                }
            }
        }

        // Update user interests.
        if (isset($user->interests) && strval($user->interests) !== '') {
            useredit_update_interests($user, preg_split('/\s*,\s*/', $user->interests, -1, PREG_SPLIT_NO_EMPTY));
        }

        // Add to cohort first, it might trigger enrolments indirectly - do NOT create cohorts here!
        foreach ($this->get_file_columns() as $column) {
            if (!preg_match('/^cohort\d+$/', $column)) {
                continue;
            }

            if (!empty($user->$column)) {
                $addcohort = $user->$column;
                if (!isset($this->cohorts[$addcohort])) {
                    if (is_number($addcohort)) {
                        // Only non-numeric idnumbers!
                        $cohort = $DB->get_record('cohort', ['id' => $addcohort]);
                    } else {
                        $cohort = $DB->get_record('cohort', ['idnumber' => $addcohort]);
                        if (empty($cohort) && has_capability('moodle/cohort:manage', \context_system::instance())) {
                            // Cohort was not found. Create a new one.
                            $cohortid = cohort_add_cohort((object)array(
                                'idnumber' => $addcohort,
                                'name' => $addcohort,
                                'contextid' => \context_system::instance()->id
                            ));
                            $cohort = $DB->get_record('cohort', ['id' => $cohortid]);
                        }
                    }

                    if (empty($cohort)) {
                        $this->cohorts[$addcohort] = get_string('unknowncohort', 'core_cohort', s($addcohort));
                    } else if (!empty($cohort->component)) {
                        // Cohorts synchronised with external sources must not be modified!
                        $this->cohorts[$addcohort] = get_string('external', 'core_cohort');
                    } else {
                        $this->cohorts[$addcohort] = $cohort;
                    }
                }

                if (is_object($this->cohorts[$addcohort])) {
                    $cohort = $this->cohorts[$addcohort];
                    if (!$DB->record_exists('cohort_members', ['cohortid' => $cohort->id, 'userid' => $user->id])) {
                        cohort_add_member($cohort->id, $user->id);
                        // We might add special column later, for now let's abuse enrolments.
                        $this->upt->track('enrolments', get_string('useradded', 'core_cohort', s($cohort->name)), 'info');
                    }
                } else {
                    // Error message.
                    $this->upt->track('enrolments', $this->cohorts[$addcohort], 'error');
                }
            }
        }

        // Find course enrolments, groups, roles/types and enrol periods
        // this is again a special case, we always do this for any updated or created users.
        foreach ($this->get_file_columns() as $column) {
            if (preg_match('/^sysrole\d+$/', $column)) {

                if (!empty($user->$column)) {
                    $sysrolename = $user->$column;
                    if ($sysrolename[0] == '-') {
                        $removing = true;
                        $sysrolename = substr($sysrolename, 1);
                    } else {
                        $removing = false;
                    }

                    if (array_key_exists($sysrolename, $this->sysrolecache)) {
                        $sysroleid = $this->sysrolecache[$sysrolename]->id;
                    } else {
                        $this->upt->track('enrolments', get_string('unknownrole', 'error', s($sysrolename)), 'error');
                        continue;
                    }

                    if ($removing) {
                        if (user_has_role_assignment($user->id, $sysroleid, SYSCONTEXTID)) {
                            role_unassign($sysroleid, $user->id, SYSCONTEXTID);
                            $this->upt->track('enrolments', get_string('unassignedsysrole',
                                'tool_uploaduser', $this->sysrolecache[$sysroleid]->name), 'info');
                        }
                    } else {
                        if (!user_has_role_assignment($user->id, $sysroleid, SYSCONTEXTID)) {
                            role_assign($sysroleid, $user->id, SYSCONTEXTID);
                            $this->upt->track('enrolments', get_string('assignedsysrole',
                                'tool_uploaduser', $this->sysrolecache[$sysroleid]->name), 'info');
                        }
                    }
                }

                continue;
            }
            if (!preg_match('/^course\d+$/', $column)) {
                continue;
            }
            $i = substr($column, 6);

            if (empty($user->{'course'.$i})) {
                continue;
            }
            $shortname = $user->{'course'.$i};
            if (!array_key_exists($shortname, $this->ccache)) {
                if (!$course = $DB->get_record('course', ['shortname' => $shortname], 'id, shortname')) {
                    $this->upt->track('enrolments', get_string('unknowncourse', 'error', s($shortname)), 'error');
                    continue;
                }
                $this->ccache[$shortname] = $course;
                $this->ccache[$shortname]->groups = null;
            }
            $courseid      = $this->ccache[$shortname]->id;
            $coursecontext = \context_course::instance($courseid);
            if (!isset($this->manualcache[$courseid])) {
                $this->manualcache[$courseid] = false;
                if ($this->manualenrol) {
                    if ($instances = enrol_get_instances($courseid, false)) {
                        foreach ($instances as $instance) {
                            if ($instance->enrol === 'manual') {
                                $this->manualcache[$courseid] = $instance;
                                break;
                            }
                        }
                    }
                }
            }

            if ($courseid == SITEID) {
                // Technically frontpage does not have enrolments, but only role assignments,
                // let's not invent new lang strings here for this rarely used feature.

                if (!empty($user->{'role'.$i})) {
                    $rolename = $user->{'role'.$i};
                    if (array_key_exists($rolename, $this->rolecache)) {
                        $roleid = $this->rolecache[$rolename]->id;
                    } else {
                        $this->upt->track('enrolments', get_string('unknownrole', 'error', s($rolename)), 'error');
                        continue;
                    }

                    role_assign($roleid, $user->id, \context_course::instance($courseid));

                    $a = new \stdClass();
                    $a->course = $shortname;
                    $a->role   = $this->rolecache[$roleid]->name;
                    $this->upt->track('enrolments', get_string('enrolledincourserole', 'enrol_manual', $a), 'info');
                }

            } else if ($this->manualenrol and $this->manualcache[$courseid]) {

                // Find role.
                $roleid = false;
                if (!empty($user->{'role'.$i})) {
                    $rolename = $user->{'role'.$i};
                    if (array_key_exists($rolename, $this->rolecache)) {
                        $roleid = $this->rolecache[$rolename]->id;
                    } else {
                        $this->upt->track('enrolments', get_string('unknownrole', 'error', s($rolename)), 'error');
                        continue;
                    }

                } else if (!empty($user->{'type'.$i})) {
                    // If no role, then find "old" enrolment type.
                    $addtype = $user->{'type'.$i};
                    if ($addtype < 1 or $addtype > 3) {
                        $this->upt->track('enrolments', get_string('error').': typeN = 1|2|3', 'error');
                        continue;
                    } else if (empty($this->formdata->{'uulegacy'.$addtype})) {
                        continue;
                    } else {
                        $roleid = $this->formdata->{'uulegacy'.$addtype};
                    }
                } else {
                    // No role specified, use the default from manual enrol plugin.
                    $roleid = $this->manualcache[$courseid]->roleid;
                }

                if ($roleid) {
                    // Find duration and/or enrol status.
                    $timeend = 0;
                    $timestart = $this->today;
                    $status = null;

                    if (isset($user->{'enrolstatus'.$i})) {
                        $enrolstatus = $user->{'enrolstatus'.$i};
                        if ($enrolstatus == '') {
                            $status = null;
                        } else if ($enrolstatus === (string)ENROL_USER_ACTIVE) {
                            $status = ENROL_USER_ACTIVE;
                        } else if ($enrolstatus === (string)ENROL_USER_SUSPENDED) {
                            $status = ENROL_USER_SUSPENDED;
                        } else {
                            debugging('Unknown enrolment status.');
                        }
                    }

                    if (!empty($user->{'enroltimestart'.$i})) {
                        $parsedtimestart = strtotime($user->{'enroltimestart'.$i});
                        if ($parsedtimestart !== false) {
                            $timestart = $parsedtimestart;
                        }
                    }

                    if (!empty($user->{'enrolperiod'.$i})) {
                        $duration = (int)$user->{'enrolperiod'.$i} * 60 * 60 * 24; // Convert days to seconds.
                        if ($duration > 0) { // Sanity check.
                            $timeend = $timestart + $duration;
                        }
                    } else if ($this->manualcache[$courseid]->enrolperiod > 0) {
                        $timeend = $timestart + $this->manualcache[$courseid]->enrolperiod;
                    }

                    $this->manualenrol->enrol_user($this->manualcache[$courseid], $user->id, $roleid,
                        $timestart, $timeend, $status);

                    $a = new \stdClass();
                    $a->course = $shortname;
                    $a->role   = $this->rolecache[$roleid]->name;
                    $this->upt->track('enrolments', get_string('enrolledincourserole', 'enrol_manual', $a), 'info');
                }
            }

            // Find group to add to.
            if (!empty($user->{'group'.$i})) {
                // Make sure user is enrolled into course before adding into groups.
                if (!is_enrolled($coursecontext, $user->id)) {
                    $this->upt->track('enrolments', get_string('addedtogroupnotenrolled', '', $user->{'group'.$i}), 'error');
                    continue;
                }
                // Build group cache.
                if (is_null($this->ccache[$shortname]->groups)) {
                    $this->ccache[$shortname]->groups = array();
                    if ($groups = groups_get_all_groups($courseid)) {
                        foreach ($groups as $gid => $group) {
                            $this->ccache[$shortname]->groups[$gid] = new \stdClass();
                            $this->ccache[$shortname]->groups[$gid]->id   = $gid;
                            $this->ccache[$shortname]->groups[$gid]->name = $group->name;
                            if (!is_numeric($group->name)) { // Only non-numeric names are supported!!!
                                $this->ccache[$shortname]->groups[$group->name] = new \stdClass();
                                $this->ccache[$shortname]->groups[$group->name]->id   = $gid;
                                $this->ccache[$shortname]->groups[$group->name]->name = $group->name;
                            }
                        }
                    }
                }
                // Group exists?
                $addgroup = $user->{'group'.$i};
                if (!array_key_exists($addgroup, $this->ccache[$shortname]->groups)) {
                    // If group doesn't exist,  create it.
                    $newgroupdata = new \stdClass();
                    $newgroupdata->name = $addgroup;
                    $newgroupdata->courseid = $this->ccache[$shortname]->id;
                    $newgroupdata->description = '';
                    $gid = groups_create_group($newgroupdata);
                    if ($gid) {
                        $this->ccache[$shortname]->groups[$addgroup] = new \stdClass();
                        $this->ccache[$shortname]->groups[$addgroup]->id   = $gid;
                        $this->ccache[$shortname]->groups[$addgroup]->name = $newgroupdata->name;
                    } else {
                        $this->upt->track('enrolments', get_string('unknowngroup', 'error', s($addgroup)), 'error');
                        continue;
                    }
                }
                $gid   = $this->ccache[$shortname]->groups[$addgroup]->id;
                $gname = $this->ccache[$shortname]->groups[$addgroup]->name;

                try {
                    if (groups_add_member($gid, $user->id)) {
                        $this->upt->track('enrolments', get_string('addedtogroup', '', s($gname)), 'info');
                    } else {
                        $this->upt->track('enrolments', get_string('addedtogroupnot', '', s($gname)), 'error');
                    }
                } catch (\moodle_exception $e) {
                    $this->upt->track('enrolments', get_string('addedtogroupnot', '', s($gname)), 'error');
                    continue;
                }
            }
        }
        if (($invalid = \core_user::validate($user)) !== true) {
            $this->upt->track('status', get_string('invaliduserdata', 'tool_uploaduser', s($user->username)), 'warning');
        }
    }

    /**
     * Summary about the whole process (how many users created, skipped, updated, etc)
     *
     * @return array
     */
    public function get_stats() {
        $lines = [];

        if ($this->get_operation_type() != UU_USER_UPDATE) {
            $lines[] = get_string('userscreated', 'tool_uploaduser').': '.$this->usersnew;
        }
        if ($this->get_operation_type() == UU_USER_UPDATE or $this->get_operation_type() == UU_USER_ADD_UPDATE) {
            $lines[] = get_string('usersupdated', 'tool_uploaduser').': '.$this->usersupdated;
        }
        if ($this->get_allow_deletes()) {
            $lines[] = get_string('usersdeleted', 'tool_uploaduser').': '.$this->deletes;
            $lines[] = get_string('deleteerrors', 'tool_uploaduser').': '.$this->deleteerrors;
        }
        if ($this->get_allow_renames()) {
            $lines[] = get_string('usersrenamed', 'tool_uploaduser').': '.$this->renames;
            $lines[] = get_string('renameerrors', 'tool_uploaduser').': '.$this->renameerrors;
        }
        if ($usersskipped = $this->usersskipped) {
            $lines[] = get_string('usersskipped', 'tool_uploaduser').': '.$usersskipped;
        }
        $lines[] = get_string('usersweakpassword', 'tool_uploaduser').': '.$this->weakpasswords;
        $lines[] = get_string('errors', 'tool_uploaduser').': '.$this->userserrors;

        return $lines;
    }
}
