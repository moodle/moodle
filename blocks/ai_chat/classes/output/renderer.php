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
 * Renderer for content of block ai_chat.
 *
 * @package    block_ai_chat
 * @copyright  2024 Tobias Garske, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ai_chat\output;

use plugin_renderer_base;

/**
 * Rendering for block_ai_chat
 */
class renderer extends plugin_renderer_base {
    /**
     * Defer to template.
     *
     * @param block_ai_chat $block
     * @return string html for the page
     */
    public function render_ai_chat_content(\block_ai_chat $block): string {
        global $USER;

        $params = new \stdClass;
        $params->new = get_string('newdialog', 'block_ai_chat');
        $params->history = get_string('history', 'block_ai_chat');
        $params->userid = $USER->id;
        $params->contextid = $block->context->id;
        $params->badge = [
                'text' => get_string('private', 'block_ai_chat'),
                'title' => get_string('badgeprivate', 'block_ai_chat'),
        ];
        $this->page->requires->js_call_amd(
                'block_ai_chat/dialog',
                'init',
                [$params]
        );

        return parent::render_from_template('block_ai_chat/floatingbutton', $params);
    }
}
