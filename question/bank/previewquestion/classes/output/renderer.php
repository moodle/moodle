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

namespace qbank_previewquestion\output;

use context;
use qbank_previewquestion\helper;

/**
 * Class renderer for rendering preview url
 *
 * @package    qbank_previewquestion
 * @copyright  2009 The Open University
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render an icon, optionally with the word 'Preview' beside it, to preview a given question.
     *
     * @param int $questionid the id of the question to be previewed.
     * @param context $context the context in which the preview is happening.
     *      Must be a course or category context.
     * @param bool $showlabel if true, show the word 'Preview' after the icon.
     *      If false, just show the icon.
     */
    public function question_preview_link($questionid, context $context, $showlabel) {
        if ($showlabel) {
            $alt = '';
            $label = get_string('preview');
            $attributes = [];
        } else {
            $alt = get_string('preview');
            $label = '';
            $attributes = ['title' => $alt];
        }

        $image = $this->pix_icon('t/preview', $alt, '', ['class' => 'iconsmall']);
        $link = helper::question_preview_url($questionid, null, null, null, null, $context);
        $action = new \popup_action('click', $link, 'questionpreview', helper::question_preview_popup_params());

        return $this->action_link($link, $image . $label, $action, $attributes);
    }

    /**
     * Render the preview page.
     *
     * @param array $previewdata
     */
    public function render_preview_page($previewdata) {
        return $this->render_from_template('qbank_previewquestion/preview_question', $previewdata);
    }

}
