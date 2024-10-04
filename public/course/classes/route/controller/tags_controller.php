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

use core\exception\moodle_exception;
use core\router\parameters\query_returnurl;
use core\router\route;
use core\router\require_login;
use core\router\util;
use core_course\form\tags_form;
use core_tag_tag;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Tag management for courses.
 *
 * @package    core_course
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags_controller {
    use \core\router\route_controller;

    /**
     * Administer course tags.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param \stdClass $course
     * @param \core\context\course $coursecontext
     * @return ResponseInterface
     */
    #[route(
        path: '/{course}/tags',
        method: ['GET', 'POST'],
        pathtypes: [
            new \core\router\parameters\path_course(),
        ],
        queryparams: [
            new query_returnurl(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function administer_tags(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \stdClass $course,
        \core\context\course $coursecontext,
    ): ResponseInterface {
        global $CFG, $OUTPUT, $PAGE;

        if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
            throw new moodle_exception('coursehidden', '', $CFG->wwwroot .'/');
        }
        require_capability('moodle/course:tag', $coursecontext);

        $PAGE->set_course($course);
        $PAGE->set_pagelayout('incourse');
        $PAGE->set_url(util::get_path_for_callable([self::class, 'administer_tags'], [
            'course' => $course->id,
        ]));
        $PAGE->set_title(get_string('coursetags', 'tag'));
        $PAGE->set_heading($course->fullname);

        $form = new tags_form();
        $data = [
            'id' => $course->id,
            'tags' => core_tag_tag::get_item_tags_array('core', 'course', $course->id),
        ];
        $form->set_data($data);

        $redirecturl = $this->get_param($request, 'returnurl') ?? course_get_url($course);
        if ($form->is_cancelled()) {
            return util::redirect($response, $redirecturl);
        } else if ($data = $form->get_data()) {
            core_tag_tag::set_item_tags('core', 'course', $course->id, $coursecontext, $data->tags);
            return util::redirect($response, $redirecturl);
        }

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($OUTPUT->heading(get_string('coursetags', 'tag')));
        $response->getBody()->write($form->render());
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }
}
