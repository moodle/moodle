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

namespace core_course\route\controller;

use core\router\route;
use core\router\require_login;
use core_course\modinfo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Restricted modules rediretion.
 *
 * @package    core_course
 * @copyright  2026 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restricted_module {
    use \core\router\route_controller;

    /**
     * Restricted module.
     *
     * @param ResponseInterface $response
     * @param modinfo $cm
     * @return ResponseInterface
     */
    #[route(
        path: '/{course}/restricted/{cm}',
        pathtypes: [
            new \core\router\parameters\path_course(),
            new \core\router\parameters\path_module(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function restricted_module_page(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \stdClass $course,
        \stdClass $cm,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        $context = \context_module::instance($cm->id);

        $modinfo = get_fast_modinfo($course);
        $cminfo = $modinfo->get_cm($cm->id);
        $sectioninfo = $modinfo->get_section_info_by_id($cm->section);

        $format = course_get_format($course);
        $course->format = $format->get_format();

        $PAGE->set_url('/restricted.php', ['id' => $cm->id]);
        $PAGE->add_body_class('limitedwidth');
        $PAGE->set_context($context);
        $PAGE->set_pagetype('mod-' . $cm->modname . '-restricted');
        $strtitle = get_string('restrictedtitle', 'course', $cminfo->get_name());
        $PAGE->set_title($strtitle . \moodle_page::TITLE_SEPARATOR . $course->shortname);
        $PAGE->set_heading($course->fullname);
        $PAGE->set_cm($cminfo);
        $PAGE->set_secondary_navigation(false);
        $PAGE->set_ai_visibility_hint(false);

        $restrictedclass = $format->get_output_classname('content\\cm\\restricted');
        $cmoutput = new $restrictedclass(
            format: $format,
            section: $sectioninfo,
            mod: $cminfo,
        );
        $renderer = $format->get_renderer($PAGE);

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($renderer->render($cmoutput));
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }
}
