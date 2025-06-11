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
 * Some general functions for the adminer plugin.
 *
 * @package    local_adminer
 * @author Andreas Grabs <moodle@grabs-edv.de>
 * @copyright  Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Allow plugins to provide some content to be rendered in the navbar.
 * The plugin must define a PLUGIN_render_navbar_output function that returns
 * the HTML they wish to add to the navbar.
 *
 * @return string HTML for the navbar
 */
function local_adminer_render_navbar_output() {
    global $OUTPUT, $CFG;

    $adminersecret = $CFG->local_adminer_secret ?? '';
    if ($adminersecret === \local_adminer\util::DISABLED_SECRET) {
        return '';
    }


    if (!has_capability('local/adminer:useadminer', context_system::instance())) {
        return '';
    }

    $mycfg = get_config('local_adminer');
    if (empty($mycfg->showquicklink)) {
        return '';
    }

    $content = new \stdClass();
    $content->url = \local_adminer\util::get_adminer_url();

    return $OUTPUT->render_from_template('local_adminer/navbar_action', $content);

}
