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
use core\url;
use core_course\cm_info;
use core_course\modinfo;
use core_course\section_info;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class course_navigation
 *
 * @package    core_course
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_navigation {
    use \core\router\route_controller;

    /**
     * Go to the next element of the course.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param \stdClass $cm
     * @return ResponseInterface
     */
    #[route(
        path: '/cms/{cm}/next',
        pathtypes: [
            new \core\router\parameters\path_module(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function cm_next_element(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \stdClass $cm,
    ): ResponseInterface {
        // The pathinfo module returns a stdClass and not a cm_info, so we need to
        // get the cm_info instance from the course modinfo.
        $cm = cm_info::create($cm);
        $modinfo = $cm->get_modinfo();
        $section = $this->get_section($cm);
        $allsectioncms = $this->get_all_section_cms($modinfo, $section);
        $cmindex = array_search($cm, $allsectioncms, true);

        // Last element in the section should redirect to next section page
        // so student can see the next section title and description.
        if ($cmindex + 1 >= count($allsectioncms)) {
            return $this->redirect_to_next_section($response, $modinfo, $section);
        }

        // Search for the next module.
        $cmcount = count($allsectioncms);
        for ($cmindex++; $cmindex < $cmcount; $cmindex++) {
            $nextcm = $allsectioncms[$cmindex];
            if ($this->is_valid_cm($nextcm)) {
                return $this->redirect($response, $nextcm->get_url());
            }
        }
        return $this->redirect_to_course($response, $cm->get_course()->id);
    }

    /**
     * Go to the previous element of the course.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param \stdClass $cm
     * @return ResponseInterface
     */
    #[route(
        path: '/cms/{cm}/previous',
        pathtypes: [
            new \core\router\parameters\path_module(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function cm_previous_element(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \stdClass $cm,
    ): ResponseInterface {
        // The pathinfo module returns a stdClass and not a cm_info, so we need to
        // get the cm_info instance from the course modinfo.
        $cm = cm_info::create($cm);
        $modinfo = $cm->get_modinfo();
        $section = $this->get_section($cm);
        $allsectioncms = $this->get_all_section_cms($modinfo, $section);
        $cmindex = array_search($cm, $allsectioncms, true);

        // First element in the section should redirect to previous section page
        // so student can see the previous section title and description.
        if ($cmindex === 0) {
            if ($result = $this->redirect_to_previous_section($response, $modinfo, $section)) {
                return $result;
            }
        }

        // Search for the previous module.
        for ($cmindex--; $cmindex >= 0; $cmindex--) {
            $prevcm = $allsectioncms[$cmindex];
            if ($this->is_valid_cm($prevcm)) {
                return $this->redirect($response, $prevcm->get_url());
            }
        }
        return $this->page_not_found($request, $response);
    }

    /**
     * Check if a course module is valid (has a URL).
     *
     * @param cm_info $cm The course module to check.
     * @return bool True if the course module is valid, false otherwise.
     */
    private function is_valid_cm(cm_info $cm): bool {
        return
            // Skip modules that don't have a URL (like labels).
            !empty($cm->get_url())
            // Skip modules that are not visible to the user.
            && $cm->is_visible_on_course_page();
    }

    /**
     * Get the section of a course module; if the section is delegated, get the parent section.
     *
     * @param cm_info $cm
     * @return \section_info
     */
    private function get_section(cm_info $cm): section_info {
        $section = $cm->get_section_info();
        if (!$section->is_delegated()) {
            return $section;
        }

        // If the section is delegated, we need to get the parent section.
        return $section->get_component_instance()->get_parent_section();
    }

    /**
     * Get all course modules in a section in order, including also activities inside sub-sections.
     *
     * @param modinfo $modinfo
     * @param section_info $section
     * @return cm_info[]
     */
    private function get_all_section_cms(modinfo $modinfo, section_info $section): array {
        $sectioncms = [];
        foreach ($section->get_sequence_cm_infos() as $cm) {
            $delegatedsection = $cm->get_delegated_section_info();
            if ($delegatedsection) {
                $sectioncms = array_merge(
                    $sectioncms,
                    $this->get_all_section_cms($modinfo, $delegatedsection),
                );
            } else {
                $sectioncms[] = $cm;
            }
        }
        return $sectioncms;
    }

    /**
     * Redirect to the next/previous section view page.
     *
     * @param ResponseInterface $response
     * @param modinfo $modinfo
     * @param section_info $currentsection
     * @param string $direction 'next' or 'previous' to indicate the direction of the redirection.
     * @return ResponseInterface
     */
    private function redirect_to_section(
        ResponseInterface $response,
        modinfo $modinfo,
        section_info $currentsection,
        string $direction = 'next',
    ): ?ResponseInterface {
        if ($direction === 'previous') {
            $section = $modinfo->get_section_info($currentsection->sectionnum);
        } else {
            $section = $modinfo->get_section_info($currentsection->sectionnum + 1);
        }
        if ($section === null) {
            // No more sections.
            return $this->redirect_to_course($response, $modinfo->get_course()->id);
        }

        if (!$section->uservisible || $section->is_delegated()) {
            return $this->redirect_to_section($response, $modinfo, $section, $direction);
        }

        return $this->redirect(
            $response,
            course_get_url($modinfo->get_course(), $section, ['navigation' => true]),
        );
    }

    /**
     * Redirect to the next view page.
     *
     * @param ResponseInterface $response
     * @param modinfo $modinfo
     * @param section_info $currentsection
     * @return ResponseInterface|null
     */
    private function redirect_to_next_section(
        ResponseInterface $response,
        modinfo $modinfo,
        section_info $currentsection,
    ): ResponseInterface {
        return $this->redirect_to_section($response, $modinfo, $currentsection, 'next');
    }

    /**
     * Redirect to the previous view page.
     *
     * @param ResponseInterface $response
     * @param modinfo $modinfo
     * @param section_info $currentsection
     * @return ResponseInterface|null
     */
    private function redirect_to_previous_section(
        ResponseInterface $response,
        modinfo $modinfo,
        section_info $currentsection,
    ): ResponseInterface {
        return $this->redirect_to_section($response, $modinfo, $currentsection, 'previous');
    }

    /**
     * Redirect to the course view page.
     *
     * @param ResponseInterface $response
     * @param int $courseid The course ID to redirect to.
     * @return ResponseInterface
     */
    private function redirect_to_course(
        ResponseInterface $response,
        int $courseid,
    ): ResponseInterface {
        return $this->redirect(
            $response,
            course_get_url($courseid),
        );
    }
}
