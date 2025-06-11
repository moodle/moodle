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

use core\event\moodlenet_resource_exported;
use core\oauth2\client;
use moodle_exception;
use stored_file;

/**
 * API for sharing Moodle LMS courses to MoodleNet instances.
 *
 * @package   core
 * @copyright 2023 Safat Shahin <safat.shahin@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_sender extends resource_sender {

    /**
     * @var \core\context\course|false The course context.
     */
    protected \core\context\course|false $coursecontext;

    /**
     * Constructor for course sender.
     *
     * @param int $courseid The course ID of the course being shared
     * @param int $userid The user ID who is sharing the activity
     * @param moodlenet_client $moodlenetclient The moodlenet_client object used to perform the share
     * @param client $oauthclient The OAuth 2 client for the MoodleNet instance
     * @param int $shareformat The data format to share in. Defaults to a Moodle backup (SHARE_FORMAT_BACKUP)
     */
    public function __construct(
        int $courseid,
        protected int $userid,
        protected moodlenet_client $moodlenetclient,
        protected client $oauthclient,
        protected int $shareformat = self::SHARE_FORMAT_BACKUP,
    ) {
        parent::__construct($courseid, $userid, $moodlenetclient, $oauthclient, $shareformat);
        $this->course = get_course($courseid);
        $this->coursecontext = \core\context\course::instance($courseid);
        $this->packager = new course_packager($this->course, $this->userid);
    }

    /**
     * Share a course to MoodleNet.
     *
     * @return array The HTTP response code from MoodleNet and the MoodleNet draft resource URL (URL empty string on fail).
     *               Format: ['responsecode' => 201, 'drafturl' => 'https://draft.mnurl/here']
     */
    public function share_resource(): array {

        $accesstoken = '';
        $issuer = $this->oauthclient->get_issuer();

        // Check user can share to the requested MoodleNet instance.
        $usercanshare = utilities::can_user_share($this->coursecontext, $this->userid, 'course');

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
        $resourcedescription = $this->get_resource_description();

        $response = $this->moodlenetclient->create_resource_from_stored_file(
            $filedata,
            $this->course->fullname,
            $resourcedescription,
        );
        $responsecode = $response->getStatusCode();

        $responsebody = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        $resourceurl = $responsebody->homepage ?? '';

        // Delete the generated file now it is no longer required.
        // (It has either been sent, or failed - retries not currently supported).
        $filedata->delete();

        // Log every attempt to share (and whether it was successful).
        $this->log_event($resourceurl, $responsecode);

        return [
            'responsecode' => $responsecode,
            'drafturl' => $resourceurl,
        ];
    }

    /**
     * Log an event to the admin logs for an outbound share attempt.
     *
     * @param string $resourceurl The URL of the draft resource if it was created
     * @param int $responsecode The HTTP response code describing the outcome of the attempt
     * @return void
     */
    protected function log_event(
        string $resourceurl,
        int $responsecode,
    ): void {
        $event = moodlenet_resource_exported::create([
            'context' => $this->coursecontext,
            'other' => [
                'courseid' => [$this->course->id],
                'resourceurl' => $resourceurl,
                'success' => ($responsecode === 201),
            ],
        ]);
        $event->trigger();
    }

    /**
     * Return the list of supported share formats.
     *
     * @return array Array of supported share format values.
     */
    protected static function get_allowed_share_formats(): array {
        return [
            self::SHARE_FORMAT_BACKUP,
        ];
    }

    /**
     * Fetch the description for the resource being created, in a supported text format.
     *
     * @return string Converted course description.
     */
    protected function get_resource_description(): string {
        global $PAGE;

        // We need to set the page context here because content_to_text and format_text will need the page context to work.
        $PAGE->set_context($this->coursecontext);

        $processeddescription = strip_tags($this->course->summary);
        $processeddescription = content_to_text
        (
            format_text(
                $processeddescription,
                $this->course->summaryformat,
            ),
            $this->course->summaryformat
        );

        return $processeddescription;
    }
}
