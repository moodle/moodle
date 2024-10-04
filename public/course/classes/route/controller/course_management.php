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
use navigation_node;
use Psr\Http\Message\ResponseInterface;

/**
 * Course Management.
 *
 * @package    core_course
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_management {
    use \core\router\route_controller;

    /**
     * Administer a course.
     *
     * @param ResponseInterface $response
     * @param \stdClass $course
     * @return ResponseInterface
     */
    #[route(
        path: '/{course}/manage',
        pathtypes: [
            new \core\router\parameters\path_course(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function administer_course(
        ResponseInterface $response,
        \stdClass $course,
    ): ResponseInterface {
        global $PAGE, $SITE, $OUTPUT;

        $PAGE->set_pagelayout('incourse');

        if ($course->id == $SITE->id) {
            $title = get_string('frontpagesettings');
            $node = $PAGE->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
            $PAGE->set_primary_active_tab('home');
        } else {
            $title = get_string('courseadministration');
            $node = $PAGE->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
        }
        $PAGE->set_title($title);
        $PAGE->set_heading($course->fullname);
        $PAGE->navbar->add($title);
        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($OUTPUT->heading($title));

        if ($node) {
            $response->getBody()->write($OUTPUT->render_from_template('core/settings_link_page', ['node' => $node]));
        }

        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }
}
