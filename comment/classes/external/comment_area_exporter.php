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
 * Exporting a comment area.
 *
 * A comment area is the set of information about a defined comments area.
 *
 * @package    core_comment
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_comment\external;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/comment/lib.php');

use comment;
use renderer_base;
use stdClass;

/**
 * Class for exporting a comment area.
 *
 * @package    core_comment
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comment_area_exporter extends \core\external\exporter {

    /** @var comment The comment instance. */
    protected $comment = null;

    public function __construct(comment $comment, $related = array()) {
        $this->comment = $comment;
        $data = new stdClass();
        $data->component = $comment->get_component();
        $data->commentarea = $comment->get_commentarea();
        $data->itemid = $comment->get_itemid();
        $data->courseid = $comment->get_courseid();
        $data->contextid = $comment->get_context()->id;
        $data->cid = $comment->get_cid();

        parent::__construct($data, $related);
    }

    protected static function define_properties() {
        return array(
            'component' => array(
                'type' => PARAM_COMPONENT,
            ),
            'commentarea' => array(
                'type' => PARAM_AREA,
            ),
            'itemid' => array(
                'type' => PARAM_INT,
            ),
            'courseid' => array(
                'type' => PARAM_INT,
            ),
            'contextid' => array(
                'type' => PARAM_INT,
            ),
            'cid' => array(
                'type' => PARAM_ALPHANUMEXT,
            ),
        );
    }

    protected static function define_other_properties() {
        return array(
            'autostart' => array(
                'type' => PARAM_BOOL,
            ),
            'canpost' => array(
                'type' => PARAM_BOOL,
            ),
            'canview' => array(
                'type' => PARAM_BOOL,
            ),
            'count' => array(
                'type' => PARAM_INT,
            ),
            'collapsediconkey' => array(
                'type' => PARAM_RAW,
            ),
            'displaytotalcount' => array(
                'type' => PARAM_BOOL,
            ),
            'displaycancel' => array(
                'type' => PARAM_BOOL,
            ),
            'fullwidth' => array(
                'type' => PARAM_BOOL,
            ),
            'linktext' => array(
                'type' => PARAM_RAW,
            ),
            'notoggle' => array(
                'type' => PARAM_BOOL,
            ),
            'template' => array(
                'type' => PARAM_RAW,
            ),
            'canpostorhascomments' => array(
                'type' => PARAM_BOOL
            )
        );
    }

    public function get_other_values(renderer_base $output) {
        $values = array();
        $values['autostart'] = $this->comment->get_autostart();
        $values['canpost'] = $this->comment->can_post();
        $values['canview'] = $this->comment->can_view();
        $values['collapsediconkey'] = right_to_left() ? 't/collapsed_rtl' : 't/collapsed';
        $values['count'] = $this->comment->count();
        $values['displaycancel'] = $this->comment->get_displaycancel();
        $values['displaytotalcount'] = $this->comment->get_displaytotalcount();
        $values['fullwidth'] = $this->comment->get_fullwidth();
        $values['linktext'] = $this->comment->get_linktext();
        $values['notoggle'] = $this->comment->get_notoggle();
        $values['template'] = $this->comment->get_template();
        $values['canpostorhascomments'] = $values['canpost'] || ($values['canview'] && $values['count'] > 0);
        return $values;
    }
}
