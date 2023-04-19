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

namespace core\moodlenet;

use cm_info;
use core\event\moodlenet_resource_exported;
use core\oauth2\client;
use moodle_exception;
use stdClass;
use stored_file;

/**
 * API for sharing Moodle LMS activities to MoodleNet instances.
 *
 * @package   core
 * @copyright 2023 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_sender {
    /**
     * @var int Backup share format - the content is being shared as a Moodle backup file.
     */
    public const SHARE_FORMAT_BACKUP = 0;

    /**
     * @var int Maximum upload file size (1.07 GB).
     */
    public const MAX_FILESIZE = 1070000000;

    /**
     * @var cm_info The context module info object for the activity being shared.
     */
    protected cm_info $cminfo;

    /**
     * @var stdClass The course where the activity is located.
     */
    protected stdClass $course;

    /**
     * Class constructor.
     *
     * @param int $cmid The course module ID of the activity being shared.
     * @param int $userid The user ID who is sharing the activity.
     * @param moodlenet_client $moodlenetclient The moodlenet_client object used to perform the share.
     * @param client $oauthclient The OAuth 2 client for the MoodleNet instance.
     * @param int $shareformat The data format to share in. Defaults to a Moodle backup (SHARE_FORMAT_BACKUP).
     * @throws moodle_exception
     */
    public function __construct(
        int $cmid,
        protected int $userid,
        protected moodlenet_client $moodlenetclient,
        protected client $oauthclient,
        protected int $shareformat = self::SHARE_FORMAT_BACKUP,
    ) {
        [$this->course, $this->cminfo] = get_course_and_cm_from_cmid($cmid);

        if (!in_array($shareformat, $this->get_allowed_share_formats())) {
            throw new moodle_exception('moodlenet:invalidshareformat');
        }
    }

    /**
     * Share an activity/resource to MoodleNet.
     *
     * @return array The HTTP response code from MoodleNet and the MoodleNet draft resource URL (URL empty string on fail).
     *               Format: ['responsecode' => 201, 'drafturl' => 'https://draft.mnurl/here']
     * @throws moodle_exception
     */
    public function share_activity(): array {
        global $DB;

        $accesstoken = '';
        $resourceurl = '';
        $issuer = $this->oauthclient->get_issuer();

        // Check user can share to the requested MoodleNet instance.
        $coursecontext = \core\context\course::instance($this->course->id);
        $usercanshare = utilities::can_user_share($coursecontext, $this->userid);

        if ($usercanshare && utilities::is_valid_instance($issuer) && $this->oauthclient->is_logged_in()) {
            $accesstoken = $this->oauthclient->get_accesstoken()->token;
        }

        // Throw an exception if the user is not currently set up to be able to share to MoodleNet.
        if (!$accesstoken) {
            throw new moodle_exception('moodlenet:usernotconfigured');
        }

        // Attempt to prepare and send the resource if validation has passed and we have an OAuth 2 token.

        // Prepare file in requested format.
        $filedata = $this->prepare_share_contents();

        // If we have successfully prepared a file to share of permitted size, share it to MoodleNet.
        if (!empty($filedata)) {
            // Avoid sending a file larger than the defined limit.
            $filesize = $filedata->get_filesize();
            if ($filesize > self::MAX_FILESIZE) {
                $filedata->delete();
                throw new moodle_exception('moodlenet:sharefilesizelimitexceeded', 'core', '', [
                    'filesize' => $filesize,
                    'filesizelimit' => self::MAX_FILESIZE,
                ]);
            }

            // MoodleNet only accept plaintext descriptions.
            $resourcedescription = $this->get_resource_description($coursecontext);

            $response = $this->moodlenetclient->create_resource_from_stored_file(
                $filedata,
                $this->cminfo->name,
                $resourcedescription,
            );
            $responsecode = $response->getStatusCode();

            $responsebody = json_decode($response->getBody());
            $resourceurl = $responsebody->homepage ?? '';

            // TODO: Store consumable information about completed share - to be completed in MDL-77296.

            // Delete the generated file now it is no longer required.
            // (It has either been sent, or failed - retries not currently supported).
            $filedata->delete();
        }

        // Log every attempt to share (and whether or not it was successful).
        $this->log_event($coursecontext, $this->cminfo->id, $resourceurl, $responsecode);

        return [
            'responsecode' => $responsecode,
            'drafturl' => $resourceurl,
        ];
    }

    /**
     * Prepare the data for sharing, in the format specified.
     *
     * @return stored_file
     */
    protected function prepare_share_contents(): stored_file {
        switch ($this->shareformat) {
            case self::SHARE_FORMAT_BACKUP:
                // If sharing the activity as a backup, prepare the packaged backup.
                $packager = new activity_packager($this->cminfo, $this->userid);
                return $packager->get_package();
            default:
                throw new \coding_exception("Unknown share format: {$this->shareformat}'");
        };
    }

    /**
     * Log an event to the admin logs for an outbound share attempt.
     *
     * @param \context $coursecontext The course context being shared from.
     * @param int $cmid The CMID of the activity being shared.
     * @param string $resourceurl The URL of the draft resource if it was created.
     * @param int $responsecode The HTTP response code describing the outcome of the attempt.
     * @return void
     */
    protected function log_event(
        \core\context $coursecontext,
        int $cmid,
        string $resourceurl,
        int $responsecode,
    ): void {
        $event = moodlenet_resource_exported::create([
            'context' => $coursecontext,
            'other' => [
                'cmids' => [$cmid],
                'resourceurl' => $resourceurl,
                'success' => ($responsecode == 201),
            ],
        ]);
        $event->trigger();
    }

    /**
     * Return the list of supported share formats.
     *
     * @return array Array of supported share format values.
     */
    protected function get_allowed_share_formats(): array {
        return [
            self::SHARE_FORMAT_BACKUP,
        ];
    }

    /**
     * Fetch the description for the resource being created, in a supported text format.
     *
     * @param \context $coursecontext The course context being shared from.
     * @return string Converted activity description.
     */
    protected function get_resource_description(
        \context $coursecontext,
    ): string {
        global $PAGE, $DB;
        // We need to set the page context here because content_to_text and format_text will need the page context to work.
        $PAGE->set_context($coursecontext);

        $intro = $DB->get_record($this->cminfo->modname, ['id' => $this->cminfo->instance], 'intro, introformat', MUST_EXIST);
        $processeddescription = strip_tags($intro->intro);
        $processeddescription = content_to_text(format_text(
            $processeddescription,
            $intro->introformat,
        ), $intro->introformat);

        return $processeddescription;
    }
}
