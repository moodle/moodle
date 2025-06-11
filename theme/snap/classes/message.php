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

namespace theme_snap;

/**
 * Message Model.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message implements \renderable {

    /**
     * @var int
     */
    public $useridfrom;

    /**
     * @var int
     */
    public $useridto;

    /**
     * @var int
     */
    public $uniqueid;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $fullmessage;

    /**
     * @var int
     */
    public $fullmessageformat;

    /**
     * @var string
     */
    public $fullmessagehtml;

    /**
     * @var string
     */
    public $smallmessage;

    /**
     * @var int
     */
    public $notification;

    /**
     * @var string
     */
    public $contexturl;

    /**
     * @var string
     */
    public $contexturlname;

    /**
     * @var int
     */
    public $timecreated;

    /**
     * @var int
     */
    public $unread;

    /**
     * The user that the message is from (usually partial object)
     *
     * @var stdClass
     */
    protected $fromuser;

    public function __construct($options = array()) {
        $this->set_options($options);
    }

    /**
     * @param stdClass $user
     * @return message
     */
    public function set_fromuser(\stdClass $user) {
        if ($user->id != $this->useridfrom) {
            throw new \coding_exception("The passed user->id ($user->id) != message->useridfrom ($this->useridfrom)");
        }
        $this->fromuser = $user;
        return $this;
    }

    /**
     * Will go to the DB and grab the user if not already set
     *
     * @throws coding_exception
     * @return stdClass
     */
    public function get_fromuser() {
        global $DB;

        if (is_null($this->fromuser)) {
            if (empty($this->useridfrom)) {
                throw new \coding_exception('The message useridfrom is not set');
            }
            $this->set_fromuser(
                $DB->get_record('user', array('id' => $this->useridfrom), \core_user\fields::for_userpic()
                    ->get_sql('', false, '', '', false)->selects, MUST_EXIST)
            );
        }
        return $this->fromuser;
    }

    /**
     * A way to bulk set model properties
     *
     * @param array|object $options
     * @return message_output_badge_model_message
     */
    public function set_options($options) {
        foreach ($options as $name => $value) {
            // Ignore things that are not a property of this model.
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
        return $this;
    }
}
