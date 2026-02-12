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
use core_course\section_info;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Restricted section redirection.
 *
 * @package    core_course
 * @copyright  2026 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restricted_section {
    use \core\router\route_controller;

    /**
     * Restricted section.
     *
     * @param ResponseInterface $response
     * @param section_info $sectioninfo
     * @return ResponseInterface
     */
    #[route(
        path: '/sections/{section}/restricted',
        pathtypes: [
            new \core\router\parameters\path_section(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function restricted_section_page(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \stdClass $section,
    ): ResponseInterface {
        global $OUTPUT, $PAGE, $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        $course = get_course($section->course);
        $format = course_get_format($course->id);
        $format->set_sectionid($section->id);
        $outputclass = $format->get_output_classname('content');
        $sectionoutput = new $outputclass($format);
        $PAGE->set_url('/course/section.php', ['id' => $section->id]);
        $PAGE->set_course($course);

        $PAGE->set_pagelayout('course');
        $PAGE->add_body_classes(['limitedwidth', 'single-section-page']);
        $PAGE->set_pagetype('course-view-section-' . $course->format . '-restricted');
        $PAGE->set_context(\context_course::instance($course->id));

        $sectiontitle = $format->get_section_name($section);
        $strtitle = get_string('restrictedtitle', 'course', $sectiontitle);
        $PAGE->set_title($strtitle . \moodle_page::TITLE_SEPARATOR . $course->shortname);
        $PAGE->set_heading($sectiontitle);
        $PAGE->set_secondary_navigation(false);
        $renderer = $format->get_renderer($PAGE);
        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($renderer->render($sectionoutput));
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }
}
