<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Part of mod_plugnmeet.
 *
 * @package     mod_plugnmeet
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class PlugNmeetHelper {
    /**
     * @param $items
     * @param $fieldname
     * @param $data
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    private static function format_html($items, $fieldname, $data, $mform) {
        foreach ($items as $key => $item) {
            if ($item["type"] === "select") {
                $select = $mform->addElement(
                    'select',
                    "{$fieldname}[{$key}]",
                    $item['label'],
                    $item["options"]
                );

                $value = $item["selected"];
                if (isset($data[$key])) {
                    $value = $data[$key];
                }

                foreach (array_keys($item["options"]) as $k) {
                    if ($k == $value) {
                        $select->setSelected($k);
                    }
                }
            } else if ($item["type"] === "text" || $item["type"] === "number") {
                $value = $item["default"];
                if (isset($data[$key])) {
                    $value = $data[$key];
                }
                $mform->addElement("text", "{$fieldname}[{$key}]", $item['label']);
                $mform->setDefault("{$fieldname}[{$key}]", $value);

                if ($item["type"] === "text") {
                    $mform->setType("{$fieldname}[{$key}]", PARAM_NOTAGS);
                } else if ($item["type"] === "number") {
                    $mform->setType("{$fieldname}[{$key}]", PARAM_INT);
                }
            } else if ($item["type"] === "textarea") {
                $value = $item["default"];
                if (isset($data[$key])) {
                    $value = $data[$key];
                }
                $mform->addElement(
                    'textarea',
                    "{$fieldname}[{$key}]",
                    $item['label'],
                    'wrap="virtual" rows="5" cols="50"'
                );
                $mform->setDefault("{$fieldname}[{$key}]", $value);
                $mform->setType("{$fieldname}[{$key}]", PARAM_CLEANHTML);
            }
        }
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_room_features($roommetadata, $mform) {
        $roomfeatures = array(
            "allow_webcams" => array(
                "label" => get_string("allow_webcams", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "mute_on_start" => array(
                "label" => get_string("mute_on_start", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "allow_screen_share" => array(
                "label" => get_string("allow_screen_share", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "allow_recording" => array(
                "label" => get_string("allow_recording", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "allow_rtmp" => array(
                "label" => get_string("allow_rtmp", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "allow_view_other_webcams" => array(
                "label" => get_string("allow_view_other_webcams", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "allow_view_other_users_list" => array(
                "label" => get_string("allow_view_other_users_list", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "admin_only_webcams" => array(
                "label" => get_string("admin_only_webcams", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "allow_polls" => array(
                "label" => get_string("allow_polls", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "room_duration" => array(
                "label" => get_string("room_duration", "mod_plugnmeet"),
                "default" => 0,
                "type" => "number"
            ),
        );

        $data = [];
        if (isset($roommetadata['room_features'])) {
            $data = $roommetadata['room_features'];
        }

        self::format_html($roomfeatures, "room_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_chat_features($roommetadata, $mform) {
        $chatfeatures = array(
            "allow_chat" => array(
                "label" => get_string("allow_chat", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "allow_file_upload" => array(
                "label" => get_string("allow_file_upload", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
        );

        $data = [];
        if (isset($roommetadata["chat_features"])) {
            $data = $roommetadata["chat_features"];
        }

        self::format_html($chatfeatures, "chat_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_shared_note_pad_features($roommetadata, $mform) {
        $sharednotepadfeatures = array(
            "allowed_shared_note_pad" => array(
                "label" => get_string("allow_shared_notepad", "plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            )
        );

        $data = [];
        if (isset($roommetadata["shared_note_pad_features"])) {
            $data = $roommetadata["shared_note_pad_features"];
        }

        self::format_html($sharednotepadfeatures, "shared_note_pad_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_whiteboard_features($roommetadata, $mform) {
        $whiteboardfeatures = array(
            "allowed_whiteboard" => array(
                "label" => get_string("allow_whiteboard", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            )
        );

        $data = [];
        if (isset($roommetadata["whiteboard_features"])) {
            $data = $roommetadata["whiteboard_features"];
        }

        self::format_html($whiteboardfeatures, "whiteboard_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_external_media_player_features($roommetadata, $mform) {
        $externalmediaplayerfeatures = array(
            "allowed_external_media_player" => array(
                "label" => get_string("allowed_external_media_player", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            )
        );

        $data = [];
        if (isset($roommetadata["external_media_player_features"])) {
            $data = $roommetadata["external_media_player_features"];
        }

        self::format_html($externalmediaplayerfeatures, "external_media_player_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_waiting_room_features($roommetadata, $mform) {
        $waitingroomfeatures = array(
            "is_active" => array(
                "label" => get_string("activate_waiting_room", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "waiting_room_msg" => array(
                "label" => get_string("waiting_room_msg", "mod_plugnmeet"),
                "default" => "",
                "type" => "textarea"
            )
        );

        $data = [];
        if (isset($roommetadata["waiting_room_features"])) {
            $data = $roommetadata["waiting_room_features"];
        }

        self::format_html($waitingroomfeatures, "waiting_room_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_breakout_room_features($roommetadata, $mform) {
        $breakoutroomfeatures = array(
            "is_allow" => array(
                "label" => get_string("allow_breakout_rooms", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "allowed_number_rooms" => array(
                "label" => get_string("allowed_number_rooms", "mod_plugnmeet"),
                "default" => 6,
                "type" => "number"
            )
        );

        $data = [];
        if (isset($roommetadata["breakout_room_features"])) {
            $data = $roommetadata["breakout_room_features"];
        }

        self::format_html($breakoutroomfeatures, "breakout_room_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_display_external_link_features($roommetadata, $mform) {
        $displayexternallinkfeatures = array(
            "is_allow" => array(
                "label" => get_string("allow_display_external_link_features", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
        );

        $data = [];
        if (isset($roommetadata["display_external_link_features"])) {
            $data = $roommetadata["display_external_link_features"];
        }

        self::format_html($displayexternallinkfeatures, "display_external_link_features", $data, $mform);
    }

    /**
     * @param $roommetadata
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public static function get_default_lock_settings($roommetadata, $mform) {
        $defaultlocksettings = array(
            "lock_microphone" => array(
                "label" => get_string("lock_microphone", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "lock_webcam" => array(
                "label" => get_string("lock_webcam", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "lock_screen_sharing" => array(
                "label" => get_string("lock_screen_sharing", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "lock_whiteboard" => array(
                "label" => get_string("lock_whiteboard", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "lock_shared_notepad" => array(
                "label" => get_string("lock_shared_notepad", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 1,
                "type" => "select"
            ),
            "lock_chat" => array(
                "label" => get_string("lock_chat", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "lock_chat_send_message" => array(
                "label" => get_string("lock_chat_send_message", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "lock_chat_file_share" => array(
                "label" => get_string("lock_chat_file_share", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
            "lock_private_chat" => array(
                "label" => get_string("lock_private_chat", "mod_plugnmeet"),
                "options" => array(
                    0 => get_string("no", "mod_plugnmeet"),
                    1 => get_string("yes", "mod_plugnmeet")
                ),
                "selected" => 0,
                "type" => "select"
            ),
        );

        $data = [];
        if (isset($roommetadata["default_lock_settings"])) {
            $data = $roommetadata["default_lock_settings"];
        }

        self::format_html($defaultlocksettings, "default_lock_settings", $data, $mform);
    }

    /**
     * @param $id
     * @return void
     */
    public static function show_join_button($id) {
        echo "<a href='/mod/plugnmeet/conference.php?id={$id}' class='btn btn-success'>" . get_string("join", "mod_plugnmeet") . "</a>";
    }
}
