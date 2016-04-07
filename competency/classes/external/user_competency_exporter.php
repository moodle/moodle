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
 * Class for exporting user competency data.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency\external;
defined('MOODLE_INTERNAL') || die();

use core_user;
use renderer_base;
use stdClass;
use core_competency\url;
use core_competency\user_competency;

/**
 * Class for exporting user competency data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'core_competency\\user_competency';
    }

    protected static function define_related() {
        // We cache the scale so it does not need to be retrieved from the framework every time.
        return array('scale' => 'grade_scale');
    }

    protected function get_other_values(renderer_base $output) {
        $result = new stdClass();

        if ($this->persistent->get_grade() === null) {
            $gradename = '-';
        } else {
            $gradename = $this->related['scale']->scale_items[$this->persistent->get_grade() - 1];
        }
        $result->gradename = $gradename;

        if ($this->persistent->get_proficiency() === null) {
            $proficiencyname = get_string('no');
        } else {
            $proficiencyname = get_string($this->persistent->get_proficiency() ? 'yes' : 'no');
        }
        $result->proficiencyname = $proficiencyname;

        $statusname = '-';
        if ($this->persistent->get_status() != user_competency::STATUS_IDLE) {
            $statusname = (string) user_competency::get_status_name($this->persistent->get_status());
        }
        $result->statusname = $statusname;

        $result->canrequestreview = $this->persistent->can_request_review();
        $result->canreview = $this->persistent->can_review();

        $result->isstatusidle = $this->persistent->get_status() == user_competency::STATUS_IDLE;
        $result->isstatusinreview = $this->persistent->get_status() == user_competency::STATUS_IN_REVIEW;
        $result->isstatuswaitingforreview = $this->persistent->get_status() == user_competency::STATUS_WAITING_FOR_REVIEW;

        $result->isrequestreviewallowed = $result->canrequestreview && $result->isstatusidle;
        $result->iscancelreviewrequestallowed = $result->canrequestreview && $result->isstatuswaitingforreview;
        $result->isstartreviewallowed = $result->canreview && $result->isstatuswaitingforreview;
        $result->isstopreviewallowed = $result->canreview && $result->isstatusinreview;

        if (!empty($result->isstatusinreview)) {
            // TODO Make this more efficient.
            $userexporter = new user_summary_exporter(core_user::get_user($this->persistent->get_reviewerid(), '*', MUST_EXIST));
            $result->reviewer = $userexporter->export($output);
        }

        $result->url = url::user_competency($this->persistent->get_id())->out(false);

        return (array) $result;
    }

    protected static function define_other_properties() {
        return array(
            'canrequestreview' => array(
                'type' => PARAM_BOOL,
            ),
            'canreview' => array(
                'type' => PARAM_BOOL,
            ),
            'gradename' => array(
                'type' => PARAM_TEXT
            ),
            'isrequestreviewallowed' => array(
                'type' => PARAM_BOOL,
            ),
            'iscancelreviewrequestallowed' => array(
                'type' => PARAM_BOOL,
            ),
            'isstartreviewallowed' => array(
                'type' => PARAM_BOOL,
            ),
            'isstopreviewallowed' => array(
                'type' => PARAM_BOOL,
            ),
            'isstatusidle' => array(
                'type' => PARAM_BOOL,
            ),
            'isstatusinreview' => array(
                'type' => PARAM_BOOL,
            ),
            'isstatuswaitingforreview' => array(
                'type' => PARAM_BOOL,
            ),
            'proficiencyname' => array(
                'type' => PARAM_RAW
            ),
            'reviewer' => array(
                'type' => user_summary_exporter::read_properties_definition(),
                'optional' => true
            ),
            'statusname' => array(
                'type' => PARAM_RAW
            ),
            'url' => array(
                'type' => PARAM_URL
            ),
        );
    }
}
