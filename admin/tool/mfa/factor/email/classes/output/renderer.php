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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/iplookup/lib.php');

/**
 * Email renderer.
 *
 * @package     factor_email
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_email_renderer extends plugin_renderer_base {

    /**
     * Generates an email
     *
     * @param   int $instanceid
     * @return  string|boolean
     */
    public function generate_email(int $instanceid): string|bool {
        global $DB, $USER, $CFG;;
        $instance = $DB->get_record('tool_mfa', ['id' => $instanceid]);
        $site = get_site();
        $validity = get_config('factor_email', 'duration');
        $authurl = new \moodle_url('/admin/tool/mfa/factor/email/email.php',
            ['instance' => $instance->id, 'pass' => 1, 'secret' => $instance->secret]);
        $authurlstring = \html_writer::link($authurl, get_string('email:link', 'factor_email'));
        $blockurl = new \moodle_url('/admin/tool/mfa/factor/email/email.php', ['instance' => $instanceid]);
        $blockurlstring = \html_writer::link($blockurl, get_string('email:stoploginlink', 'factor_email'));
        $geoinfo = iplookup_find_location($instance->createdfromip);

        $templateinfo = [
            'logo' => $this->get_compact_logo_url(100, 100),
            'name' => $USER->firstname,
            'sitename' => $site->fullname,
            'siteurl' => $CFG->wwwroot,
            'code' => $instance->secret,
            'validity' => format_time($validity),
            'authlink' => get_string('email:loginlink', 'factor_email', $authurlstring),
            'revokelink' => get_string('email:revokelink', 'factor_email', $blockurlstring),
            'ip' => $instance->createdfromip,
            'geocity' => $geoinfo['city'],
            'geocountry' => $geoinfo['country'],
            'ua' => $instance->label,
        ];
        return $this->render_from_template('factor_email/email', $templateinfo);
    }
}
