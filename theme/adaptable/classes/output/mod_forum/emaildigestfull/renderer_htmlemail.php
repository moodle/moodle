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
 * Forum post renderable.
 *
 * @package    theme_adaptable
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output\mod_forum\emaildigestfull;

/**
 * Forum post renderable.
 */
class renderer_htmlemail extends \mod_forum\output\emaildigestfull\renderer {
    /**
     * Display a forum post in the relevant context.
     *
     * @param \mod_forum\output\forum_post_email $post The post to display.
     *
     * @return string
     */
    public function render_forum_post_email(\mod_forum\output\forum_post_email $post) {
        // Was ($this, $this->target === RENDERER_TARGET_TEXTEMAIL) and as we are already 'htmlemail' it will always be false.
        $data = $post->export_for_template($this, false);

        $templatename = $this->forum_post_template();
        $themeoverride = \theme_adaptable\toolbox::apply_template_override('mod_forum/' . $templatename, $data);
        if ($themeoverride !== false) {
            $output = $themeoverride;
        } else {
            // Use core mechanism.
            $output = $this->render_from_template('mod_forum/' . $templatename, $data);
        }

        return $output;
    }
}
