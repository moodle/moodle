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

namespace mod_bigbluebuttonbn\courseformat;

use core_calendar\output\humandate;
use cm_info;
use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\text_align;
use core\output\local\properties\button;
use core\url;
use mod_bigbluebuttonbn\dates;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\recording;

/**
 * bigbluebuttonbn overview integration.
 *
 * @package    mod_bigbluebuttonbn
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /** @var instance $bigbluebuttonbn the bigbluebuttonbn instance. */
    private instance $bigbluebuttonbn;

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     */
    public function __construct(
        cm_info $cm,
    ) {
        parent::__construct($cm);
        $this->bigbluebuttonbn = instance::get_from_cmid($cm->id);
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!$this->bigbluebuttonbn->is_moderator()) {
            return null;
        }

        $content = new action_link(
            url: new url('/mod/bigbluebuttonbn/view.php', ['id' => $this->cm->id]),
            text: get_string('view'),
            attributes: ['class' => button::BODY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: get_string('actions'),
            value: get_string('view'),
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'opens' => $this->get_extra_date_open(),
            'closes' => $this->get_extra_date_close(),
            'roomtype' => $this->get_extra_room_type_overview(),
            'recordings' => $this->get_extra_recordings_overview(),
        ];
    }

    /**
     * Retrieves the open date overview item.
     *
     * @return overviewitem|null An overview item with the open date, or null if the user lacks the required capability.
     */
    public function get_extra_date_open(): ?overviewitem {
        global $USER;

        $dates = new dates($this->cm, $USER->id);
        $opendate = $dates->get_open_date();

        if (empty($opendate)) {
            return new overviewitem(
                name: get_string('opens', 'bigbluebuttonbn'),
                value: null,
                content: '-',
            );
        }

        $content = humandate::create_from_timestamp($opendate);

        return new overviewitem(
            name: get_string('opens', 'bigbluebuttonbn'),
            value: $opendate,
            content: $content,
        );
    }

    /**
     * Retrieves the close date overview item.
     *
     * @return overviewitem|null An overview item with the open date, or null if the user lacks the required capability.
     */
    public function get_extra_date_close(): ?overviewitem {
        global $USER;

        $dates = new dates($this->cm, $USER->id);
        $closedate = $dates->get_close_date();

        if (empty($closedate)) {
            return new overviewitem(
                name: get_string('closes', 'bigbluebuttonbn'),
                value: null,
                content: '-',
            );
        }

        $content = humandate::create_from_timestamp($closedate);

        return new overviewitem(
            name: get_string('closes', 'bigbluebuttonbn'),
            value: $closedate,
            content: $content,
        );
    }
    /**
     * Retrieves the recording count overview item.
     *
     * @return overviewitem|null An overview item c, or null if the user lacks the required capability.
     */
    private function get_extra_room_type_overview(): ?overviewitem {
        if (!$this->bigbluebuttonbn->is_moderator()) {
            return null;
        }
        $typeprofiles = bigbluebutton_proxy::get_instance_type_profiles();
        $profilename = $typeprofiles[$this->bigbluebuttonbn->get_type()]['name'];
        return new overviewitem(
            name: get_string('mod_form_field_instanceprofiles', 'mod_bigbluebuttonbn'),
            value: $profilename,
            content: $profilename,
            textalign: text_align::START,
        );
    }
    /**
     * Retrieves the recording count overview item.
     *
     * @return overviewitem|null An overview item c, or null if the user lacks the required capability.
     */
    private function get_extra_recordings_overview(): ?overviewitem {
        if (!$this->bigbluebuttonbn->is_moderator()) {
            return null;
        }
        $content = '-';
        $recordingcount = 0;
        if ($this->bigbluebuttonbn->get_type() !== strval(instance::TYPE_ROOM_ONLY)) {
            $groups = $this->get_groups_for_filtering();
            $recordings = recording::get_recordings_for_instance(instance: $this->bigbluebuttonbn, filterbygroups: false);
            if (!empty($groups)) {
                $recordings = array_filter(
                    $recordings,
                    fn($rec) => isset($groups[$rec->get('groupid')]),
                );
            }
            $recordingcount = count($recordings);
            $content = strval($recordingcount);
        }
        return new overviewitem(
            name: get_string('recordings', 'mod_bigbluebuttonbn'),
            value: $recordingcount,
            content: $content,
            textalign: text_align::END,
        );
    }
}
