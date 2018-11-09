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
 * Class for loading/storing data requests from the DB.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class for loading/storing competencies from the DB.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_request extends persistent {

    /** The table name this persistent object maps to. */
    const TABLE = 'tool_dataprivacy_request';

    /** Data request created manually. */
    const DATAREQUEST_CREATION_MANUAL = 0;

    /** Data request created automatically. */
    const DATAREQUEST_CREATION_AUTO = 1;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'type' => [
                'choices' => [
                    api::DATAREQUEST_TYPE_EXPORT,
                    api::DATAREQUEST_TYPE_DELETE,
                    api::DATAREQUEST_TYPE_OTHERS,
                ],
                'type' => PARAM_INT
            ],
            'comments' => [
                'type' => PARAM_TEXT,
                'default' => ''
            ],
            'commentsformat' => [
                'choices' => [
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_PLAIN,
                    FORMAT_MARKDOWN
                ],
                'type' => PARAM_INT,
                'default' => FORMAT_PLAIN
            ],
            'userid' => [
                'default' => 0,
                'type' => PARAM_INT
            ],
            'requestedby' => [
                'default' => 0,
                'type' => PARAM_INT
            ],
            'status' => [
                'default' => api::DATAREQUEST_STATUS_AWAITING_APPROVAL,
                'choices' => [
                    api::DATAREQUEST_STATUS_PENDING,
                    api::DATAREQUEST_STATUS_AWAITING_APPROVAL,
                    api::DATAREQUEST_STATUS_APPROVED,
                    api::DATAREQUEST_STATUS_PROCESSING,
                    api::DATAREQUEST_STATUS_COMPLETE,
                    api::DATAREQUEST_STATUS_CANCELLED,
                    api::DATAREQUEST_STATUS_REJECTED,
                    api::DATAREQUEST_STATUS_DOWNLOAD_READY,
                    api::DATAREQUEST_STATUS_EXPIRED,
                    api::DATAREQUEST_STATUS_DELETED,
                ],
                'type' => PARAM_INT
            ],
            'dpo' => [
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ],
            'dpocomment' => [
                'default' => '',
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ],
            'dpocommentformat' => [
                'choices' => [
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_PLAIN,
                    FORMAT_MARKDOWN
                ],
                'type' => PARAM_INT,
                'default' => FORMAT_PLAIN
            ],
            'creationmethod' => [
                'default' => self::DATAREQUEST_CREATION_MANUAL,
                'choices' => [
                    self::DATAREQUEST_CREATION_MANUAL,
                    self::DATAREQUEST_CREATION_AUTO
                ],
                'type' => PARAM_INT
            ],
        ];
    }

    /**
     * Determines whether a completed data export request has expired.
     * The response will be valid regardless of the expiry scheduled task having run.
     *
     * @param data_request $request the data request object whose expiry will be checked.
     * @return bool true if the request has expired.
     */
    public static function is_expired(data_request $request) {
        $result = false;

        // Only export requests expire.
        if ($request->get('type') == api::DATAREQUEST_TYPE_EXPORT) {
            switch ($request->get('status')) {
                // Expired requests are obviously expired.
                case api::DATAREQUEST_STATUS_EXPIRED:
                    $result = true;
                    break;
                // Complete requests are expired if the expiry time has elapsed.
                case api::DATAREQUEST_STATUS_DOWNLOAD_READY:
                    $expiryseconds = get_config('tool_dataprivacy', 'privacyrequestexpiry');
                    if ($expiryseconds > 0 && time() >= ($request->get('timemodified') + $expiryseconds)) {
                        $result = true;
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     * Fetch completed data requests which are due to expire.
     *
     * @param int $userid Optional user ID to filter by.
     *
     * @return array Details of completed requests which are due to expire.
     */
    public static function get_expired_requests($userid = 0) {
        global $DB;

        $expiryseconds = get_config('tool_dataprivacy', 'privacyrequestexpiry');
        $expirytime = strtotime("-{$expiryseconds} second");
        $table = self::TABLE;
        $sqlwhere = 'type = :export_type AND status = :completestatus AND timemodified <= :expirytime';
        $params = array(
            'export_type' => api::DATAREQUEST_TYPE_EXPORT,
            'completestatus' => api::DATAREQUEST_STATUS_DOWNLOAD_READY,
            'expirytime' => $expirytime,
        );
        $sort = 'id';
        $fields = 'id, userid';

        // Filter by user ID if specified.
        if ($userid > 0) {
            $sqlwhere .= ' AND (userid = :userid OR requestedby = :requestedby)';
            $params['userid'] = $userid;
            $params['requestedby'] = $userid;
        }

        return $DB->get_records_select_menu($table, $sqlwhere, $params, $sort, $fields, 0, 2000);
    }

    /**
     * Expire a given set of data requests.
     * Update request status and delete the files.
     *
     * @param array $expiredrequests [requestid => userid]
     *
     * @return void
     */
    public static function expire($expiredrequests) {
        global $DB;

        $ids = array_keys($expiredrequests);

        if (count($ids) > 0) {
            list($insql, $inparams) = $DB->get_in_or_equal($ids);
            $initialparams = array(api::DATAREQUEST_STATUS_EXPIRED, time());
            $params = array_merge($initialparams, $inparams);

            $update = "UPDATE {" . self::TABLE . "}
                          SET status = ?, timemodified = ?
                        WHERE id $insql";

            if ($DB->execute($update, $params)) {
                $fs = get_file_storage();

                foreach ($expiredrequests as $id => $userid) {
                    $usercontext = \context_user::instance($userid);
                    $fs->delete_area_files($usercontext->id, 'tool_dataprivacy', 'export', $id);
                }
            }
        }
    }

    /**
     * Whether this request is in a state appropriate for reset/resubmission.
     *
     * Note: This does not check whether any other completed requests exist for this user.
     *
     * @return  bool
     */
    public function is_resettable() : bool {
        if (api::DATAREQUEST_TYPE_OTHERS == $this->get('type')) {
            // It is not possible to reset 'other' reqeusts.
            return false;
        }

        $resettable = [
            api::DATAREQUEST_STATUS_APPROVED => true,
            api::DATAREQUEST_STATUS_REJECTED => true,
        ];

        return isset($resettable[$this->get('status')]);
    }

    /**
     * Whether this request is 'active'.
     *
     * @return  bool
     */
    public function is_active() : bool {
        $active = [
            api::DATAREQUEST_STATUS_APPROVED => true,
        ];

        return isset($active[$this->get('status')]);
    }

    /**
     * Reject this request and resubmit it as a fresh request.
     *
     * Note: This does not check whether any other completed requests exist for this user.
     *
     * @return  self
     */
    public function resubmit_request() : data_request {
        if ($this->is_active()) {
            $this->set('status', api::DATAREQUEST_STATUS_REJECTED)->save();
        }

        if (!$this->is_resettable()) {
            throw new \moodle_exception('cannotreset', 'tool_dataprivacy');
        }

        $currentdata = $this->to_record();
        unset($currentdata->id);

        // Clone the original request, but do not notify.
        $clone = api::create_data_request(
                $this->get('userid'),
                $this->get('type'),
                $this->get('comments'),
                $this->get('creationmethod'),
                false
            );
        $clone->set('comments', $this->get('comments'));
        $clone->set('dpo', $this->get('dpo'));
        $clone->set('requestedby', $this->get('requestedby'));
        $clone->save();

        return $clone;
    }
}
