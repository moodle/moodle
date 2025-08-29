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

namespace core\output;

use core\context\user as context_user;
use core\exception\coding_exception;
use moodle_page;
use moodle_url;
use stdClass;

/**
 * Data structure representing a user picture.
 *
 * @copyright 2009 Nicolas Connault, 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Modle 2.0
 * @package core
 * @category output
 */
class user_picture implements renderable {
    /**
     * @var stdClass A user object with at least fields all columns specified
     * in $fields array constant set.
     */
    public $user;

    /**
     * @var int The course id. Used when constructing the link to the user's
     * profile, page course id used if not specified.
     */
    public $courseid;

    /**
     * @var bool Add course profile link to image
     */
    public $link = true;

    /**
     * @var int Size in pixels. Special values are (true/1 = 100px) and (false/0 = 35px) for backward compatibility.
     * Recommended values (supporting user initials too): 16, 35, 64 and 100.
     */
    public $size = 35;

    /**
     * @var bool Add non-blank alt-text to the image.
     * Default true, set to false when image alt just duplicates text in screenreaders.
     */
    public $alttext = true;

    /**
     * @var bool Whether or not to open the link in a popup window.
     */
    public $popup = false;

    /**
     * @var string Image class attribute
     */
    public $class = 'userpicture';

    /**
     * @var bool Whether to be visible to screen readers.
     */
    public $visibletoscreenreaders = true;

    /**
     * @var bool Whether to include the fullname in the user picture link.
     */
    public $includefullname = false;

    /**
     * @var mixed Include user authentication token. True indicates to generate a token for current user, and integer value
     * indicates to generate a token for the user whose id is the value indicated.
     */
    public $includetoken = false;

    /**
     * User picture constructor.
     *
     * @param stdClass $user user record with at least id, picture, imagealt, firstname and lastname set.
     *                 It is recommended to add also contextid of the user for performance reasons.
     */
    public function __construct(stdClass $user) {
        global $DB;

        if (empty($user->id)) {
            throw new coding_exception('User id is required when printing user avatar image.');
        }

        // Only touch the DB if we are missing data and complain loudly...
        $needrec = false;
        foreach (\core_user\fields::get_picture_fields() as $field) {
            if (!property_exists($user, $field)) {
                $needrec = true;
                debugging(
                    "Missing '{$field}' property in \$user object, "
                        . "this is a performance problem that needs to be fixed by a developer. "
                        . 'Please use the \core_user\fields API to get the full list of required fields.',
                    DEBUG_DEVELOPER,
                );
                break;
            }
        }

        if ($needrec) {
            $this->user = $DB->get_record(
                'user',
                ['id' => $user->id],
                implode(',', \core_user\fields::get_picture_fields()),
                MUST_EXIST
            );
        } else {
            $this->user = clone($user);
        }
    }

    /**
     * Returns a list of required user fields, useful when fetching required user info from db.
     *
     * In some cases we have to fetch the user data together with some other information,
     * the idalias is useful there because the id would otherwise override the main
     * id of the result record. Please note it has to be converted back to id before rendering.
     *
     * @param string $tableprefix name of database table prefix in query
     * @param null|array $extrafields extra fields to be included in result
     *      Do not include TEXT columns because it would break SELECT DISTINCT in MSSQL.
     * @param string $idalias alias of id field
     * @param string $fieldprefix prefix to add to all columns in their aliases, does not apply to 'id'
     * @return string
     * @deprecated since Moodle 3.11 MDL-45242
     * @see \core_user\fields
     */
    public static function fields(
        $tableprefix = '',
        ?array $extrafields = null,
        $idalias = 'id',
        $fieldprefix = '',
    ) {
        debugging('user_picture::fields() is deprecated. Please use the \core_user\fields API instead.', DEBUG_DEVELOPER);
        $userfields = \core_user\fields::for_userpic();
        if ($extrafields) {
            $userfields->including(...$extrafields);
        }
        $selects = $userfields->get_sql($tableprefix, false, $fieldprefix, $idalias, false)->selects;
        if ($tableprefix === '') {
            // If no table alias is specified, don't add {user}. in front of fields.
            $selects = str_replace('{user}.', '', $selects);
        }
        // Maintain legacy behaviour where the field list was done with 'implode' and no spaces.
        $selects = str_replace(', ', ',', $selects);
        return $selects;
    }

    /**
     * Extract the aliased user fields from a given record
     *
     * Given a record that was previously obtained using {@see self::fields()} with aliases,
     * this method extracts user related unaliased fields.
     *
     * @param stdClass $record containing user picture fields
     * @param null|array $extrafields extra fields included in the $record
     * @param string $idalias alias of the id field
     * @param string $fieldprefix prefix added to all columns in their aliases, does not apply to 'id'
     * @return stdClass object with unaliased user fields
     */
    public static function unalias(
        stdClass $record,
        ?array $extrafields = null,
        $idalias = 'id',
        $fieldprefix = '',
    ) {
        if (empty($idalias)) {
            $idalias = 'id';
        }

        $return = new stdClass();

        foreach (\core_user\fields::get_picture_fields() as $field) {
            if ($field === 'id') {
                if (property_exists($record, $idalias)) {
                    $return->id = $record->{$idalias};
                }
            } else {
                if (property_exists($record, $fieldprefix . $field)) {
                    $return->{$field} = $record->{$fieldprefix . $field};
                }
            }
        }
        // Add extra fields if not already there.
        if ($extrafields) {
            foreach ($extrafields as $e) {
                if ($e === 'id' || property_exists($return, $e)) {
                    continue;
                }
                $return->{$e} = $record->{$fieldprefix . $e};
            }
        }

        return $return;
    }

    /**
     * Checks if the current user is permitted to view user profile images.
     *
     * This is based on the forcelogin and forceloginforprofileimage config settings, and the
     * moodle/user:viewprofilepictures capability.
     *
     * Logged-in users are allowed to view their own profile image regardless of capability.
     *
     * @param int $imageuserid User id of profile image being viewed
     * @return bool True if current user can view profile images
     */
    public static function allow_view(int $imageuserid): bool {
        global $CFG, $USER;

        // Not allowed to view profile images if forcelogin is enabled and not logged in (guest
        // allowed), or forceloginforprofileimage is enabled and not logged in or guest.
        if (
            (!empty($CFG->forcelogin) && !isloggedin()) ||
            (!empty($CFG->forceloginforprofileimage) && (!isloggedin() || isguestuser()))
        ) {
            return false;
        }

        // Unless one of the forcelogin options is enabled, users can download profile pics
        // without login, so the capability should not be checked as it might lead to a
        // false sense of security (i.e. you log in as a test user, the HTML page doesn't
        // show the picture, but they can still access it if they just log out).
        // When the capability is checked, use system context for performance (if we check at
        // user level, pages that show a lot of user pictures will individually load a lot of
        // user contexts).
        if (
            (!empty($CFG->forcelogin) || !empty($CFG->forceloginforprofileimage)) &&
            $USER->id != $imageuserid &&
            !has_capability('moodle/user:viewprofilepictures', \context_system::instance())
        ) {
            return false;
        }

        return true;
    }

    /**
     * Works out the URL for the users picture.
     *
     * This method is recommended as it avoids costly redirects of user pictures
     * if requests are made for non-existent files etc.
     *
     * @param moodle_page $page
     * @param null|renderer_base $renderer
     * @return moodle_url
     */
    public function get_url(
        moodle_page $page,
        ?renderer_base $renderer = null,
    ) {
        global $CFG;

        if (is_null($renderer)) {
            $renderer = $page->get_renderer('core');
        }

        // Sort out the filename and size. Size is only required for the gravatar
        // implementation presently.
        if (empty($this->size)) {
            $filename = 'f2';
            $size = 35;
        } else if ($this->size === true || $this->size == 1) {
            $filename = 'f1';
            $size = 100;
        } else if ($this->size > 100) {
            $filename = 'f3';
            $size = (int)$this->size;
        } else if ($this->size >= 50) {
            $filename = 'f1';
            $size = (int)$this->size;
        } else {
            $filename = 'f2';
            $size = (int)$this->size;
        }

        $defaulturl = $renderer->image_url('u/' . $filename); // Default image.

        if (!self::allow_view($this->user->id)) {
            return $defaulturl;
        }

        // First try to detect deleted users - but do not read from database for performance reasons!
        if (!empty($this->user->deleted) || !str_contains($this->user->email, '@')) {
            // All deleted users should have email replaced by md5 hash,
            // all active users are expected to have valid email.
            return $defaulturl;
        }

        // Did the user upload a picture?
        if ($this->user->picture > 0) {
            if (!empty($this->user->contextid)) {
                $contextid = $this->user->contextid;
            } else {
                $context = context_user::instance($this->user->id, IGNORE_MISSING);
                if (!$context) {
                    // This must be an incorrectly deleted user, all other users have context.
                    return $defaulturl;
                }
                $contextid = $context->id;
            }

            $path = '/';
            if (clean_param($page->theme->name, PARAM_THEME) == $page->theme->name) {
                // We append the theme name to the file path if we have it so that
                // in the circumstance that the profile picture is not available
                // when the user actually requests it they still get the profile
                // picture for the correct theme.
                $path .= $page->theme->name . '/';
            }
            // Set the image URL to the URL for the uploaded file and return.
            $url = moodle_url::make_pluginfile_url(
                $contextid,
                'user',
                'icon',
                null,
                $path,
                $filename,
                false,
                $this->includetoken
            );
            $url->param('rev', $this->user->picture);
            return $url;
        }

        if ($this->user->picture == 0 && !empty($CFG->enablegravatar)) {
            // Normalise the size variable to acceptable bounds.
            if ($size < 1 || $size > 512) {
                $size = 35;
            }
            // Hash the users email address.
            $md5 = md5(strtolower(trim($this->user->email)));
            // Build a gravatar URL with what we know.

            // Find the best default image URL we can (MDL-35669).
            if (empty($CFG->gravatardefaulturl)) {
                $absoluteimagepath = $page->theme->resolve_image_location('u/' . $filename, 'core');
                if (strpos($absoluteimagepath, $CFG->dirroot) === 0) {
                    $gravatardefault = $CFG->wwwroot . substr($absoluteimagepath, strlen($CFG->dirroot));
                } else {
                    $gravatardefault = $CFG->wwwroot . '/pix/u/' . $filename . '.png';
                }
            } else {
                $gravatardefault = $CFG->gravatardefaulturl;
            }

            // If the currently requested page is https then we'll return an
            // https gravatar page.
            if (is_https()) {
                return new moodle_url("https://secure.gravatar.com/avatar/{$md5}", ['s' => $size, 'd' => $gravatardefault]);
            } else {
                return new moodle_url("http://www.gravatar.com/avatar/{$md5}", ['s' => $size, 'd' => $gravatardefault]);
            }
        }

        return $defaulturl;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(user_picture::class, \user_picture::class);
