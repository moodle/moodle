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

namespace qbank_previewquestion;

use core_question\local\bank\menu_action_column_base;

/**
 * Question bank columns for the preview action icon.
 *
 * @package   qbank_previewquestion
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview_action_column extends menu_action_column_base {

    /**
     * @var string store this lang string for performance.
     */
    protected $strpreview;

    public function init(): void {
        parent::init();
        $this->strpreview = get_string('preview');
    }

    public function get_name(): string {
        return 'previewaction';
    }

    protected function get_url_icon_and_label(\stdClass $question): array {
        if (!\question_bank::is_qtype_installed($question->qtype)) {
            // It sometimes happens that people end up with junk questions
            // in their question bank of a type that is no longer installed.
            // We cannot do most actions on them, because that leads to errors.
            return [null, null, null];
        }

        if (question_has_capability_on($question, 'use')) {
            $context = $this->qbank->get_most_specific_context();
            // Default previews to always use the latest question version, unless we are previewing specific versions from the
            // question history.
            if ($this->qbank->is_listing_specific_versions()) {
                $requestedversion = $question->version;
            } else {
                $requestedversion = question_preview_options::ALWAYS_LATEST;
            }
            $url = helper::question_preview_url($question->id, null, null,
                                                    null, null, $context, $this->qbank->returnurl, $requestedversion);
            return [$url, 't/preview', $this->strpreview];
        } else {
            return [null, null, null];
        }
    }
}
