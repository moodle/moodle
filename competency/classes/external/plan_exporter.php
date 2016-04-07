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
 * Class for exporting plan data.
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
use moodle_url;
use core_competency\url;

/**
 * Class for exporting plan data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'core_competency\\plan';
    }

    protected static function define_related() {
        return array('template' => 'core_competency\\template?');
    }

    protected function get_other_values(renderer_base $output) {
        $classname = static::define_class();
        $status = $this->persistent->get_status();

        $values = new stdClass();

        $values->statusname = $this->persistent->get_statusname();
        $values->isbasedontemplate = $this->persistent->is_based_on_template();

        $values->canmanage = $this->persistent->can_manage();
        $values->canrequestreview = $this->persistent->can_request_review();
        $values->canreview = $this->persistent->can_review();
        $values->canbeedited = $this->persistent->can_be_edited();

        $values->isactive = $status == $classname::STATUS_ACTIVE;
        $values->isdraft = $status == $classname::STATUS_DRAFT;
        $values->iscompleted = $status == $classname::STATUS_COMPLETE;
        $values->isinreview = $status == $classname::STATUS_IN_REVIEW;
        $values->iswaitingforreview = $status == $classname::STATUS_WAITING_FOR_REVIEW;

        $values->isreopenallowed = $values->canmanage && $values->iscompleted;
        $values->iscompleteallowed = $values->canmanage && $values->isactive;
        $values->isunlinkallowed = $values->canmanage && !$values->iscompleted && $values->isbasedontemplate;

        $values->isrequestreviewallowed = false;
        $values->iscancelreviewrequestallowed = false;
        $values->isstartreviewallowed = false;
        $values->isstopreviewallowed = false;
        $values->isapproveallowed = false;
        $values->isunapproveallowed = false;
        if (!$values->isbasedontemplate) {
            $values->isrequestreviewallowed = $values->canrequestreview && $values->isdraft;
            $values->iscancelreviewrequestallowed = $values->canrequestreview && $values->iswaitingforreview;
            $values->isstartreviewallowed = $values->canreview && $values->iswaitingforreview;
            $values->isstopreviewallowed = $values->canreview && $values->isinreview;
            $values->isapproveallowed = $values->canreview && !$values->iscompleted && !$values->isactive;
            $values->isunapproveallowed = $values->canreview && $values->isactive;
        }

        $values->duedateformatted = userdate($this->persistent->get_duedate());

        if ($this->persistent->is_based_on_template()) {
            $exporter = new template_exporter($this->related['template']);
            $values->template = $exporter->export($output);
        }

        if (!empty($values->isinreview)) {
            // TODO Make this more efficient.
            $userexporter = new user_summary_exporter(core_user::get_user($this->persistent->get_reviewerid(), '*', MUST_EXIST));
            $values->reviewer = $userexporter->export($output);
        }

        $commentareaexporter = new comment_area_exporter($this->persistent->get_comment_object());
        $values->commentarea = $commentareaexporter->export($output);
        $values->url = url::plan($this->persistent->get_id())->out(false);

        return (array) $values;
    }

    public static function define_other_properties() {
        return array(
            'statusname' => array(
                'type' => PARAM_RAW,
            ),
            'isbasedontemplate' => array(
                'type' => PARAM_BOOL,
            ),
            'canmanage' => array(
                'type' => PARAM_BOOL,
            ),
            'canrequestreview' => array(
                'type' => PARAM_BOOL,
            ),
            'canreview' => array(
                'type' => PARAM_BOOL,
            ),
            'canbeedited' => array(
                'type' => PARAM_BOOL,
            ),
            'isactive' => array(
                'type' => PARAM_BOOL
            ),
            'isdraft' => array(
                'type' => PARAM_BOOL
            ),
            'iscompleted' => array(
                'type' => PARAM_BOOL
            ),
            'isinreview' => array(
                'type' => PARAM_BOOL
            ),
            'iswaitingforreview' => array(
                'type' => PARAM_BOOL
            ),
            'isreopenallowed' => array(
                'type' => PARAM_BOOL
            ),
            'iscompleteallowed' => array(
                'type' => PARAM_BOOL
            ),
            'isunlinkallowed' => array(
                'type' => PARAM_BOOL
            ),
            'isrequestreviewallowed' => array(
                'type' => PARAM_BOOL
            ),
            'iscancelreviewrequestallowed' => array(
                'type' => PARAM_BOOL
            ),
            'isstartreviewallowed' => array(
                'type' => PARAM_BOOL
            ),
            'isstopreviewallowed' => array(
                'type' => PARAM_BOOL
            ),
            'isapproveallowed' => array(
                'type' => PARAM_BOOL
            ),
            'isunapproveallowed' => array(
                'type' => PARAM_BOOL
            ),
            'duedateformatted' => array(
                'type' => PARAM_TEXT
            ),
            'commentarea' => array(
                'type' => comment_area_exporter::read_properties_definition(),
            ),
            'reviewer' => array(
                'type' => user_summary_exporter::read_properties_definition(),
                'optional' => true
            ),
            'template' => array(
                'type' => template_exporter::read_properties_definition(),
                'optional' => true,
            ),
            'url' => array(
                'type' => PARAM_URL
            )
        );
    }
}
