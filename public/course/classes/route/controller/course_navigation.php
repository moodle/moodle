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
use core_course\cm_info;
use core_course\modinfo;
use core_course\section_info;
use core_courseformat\sectiondelegate;
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
     * @param \stdClass $cmdata
     * @return ResponseInterface
     */
    #[route(
        path: '/cms/{cm}/next',
        pathtypes: [
            new \core\router\parameters\path_coursemodule(name: 'cm'),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function cm_next_element(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \stdClass $cmdata,
    ): ResponseInterface {
        // The pathinfo module returns a stdClass and not a cm_info, so we need to
        // get the cm_info instance from the course modinfo.
        $cm = cm_info::create($cmdata);
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
                return $this->redirect($response, $nextcm->get_navigation_url());
            }
        }

        // If there is no next module, redirect to the next section.
        return $this->redirect_to_next_section($response, $modinfo, $section);
    }

    /**
     * Go to the previous element of the course.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param \stdClass $cmdata
     * @return ResponseInterface
     */
    #[route(
        path: '/cms/{cm}/previous',
        pathtypes: [
            new \core\router\parameters\path_coursemodule(name: 'cm'),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            courseattributename: 'course',
        ),
    )]
    public function cm_previous_element(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \stdClass $cmdata,
    ): ResponseInterface {
        // The pathinfo module returns a stdClass and not a cm_info, so we need to
        // get the cm_info instance from the course modinfo.
        $cm = cm_info::create($cmdata);
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
                return $this->redirect($response, $prevcm->get_navigation_url());
            }
        }

        // If there is no previous module, redirect to the previous section.
        return $this->redirect_to_previous_section($response, $modinfo, $section);
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
            !empty($cm->get_navigation_url())
            // Skip modules that are not visible to the user.
            && $cm->is_visible_on_course_page()
            // Skip stealth modules if the user lacks hidden activity permissions, as they are not meant to be accessed directly.
            && (!$cm->is_stealth() || has_capability('moodle/course:viewhiddenactivities', $cm->context))
            // Skip modules that are not displayable.
            && modinfo::is_mod_type_visible_on_course($cm->modname);
    }

    /**
     * Get the section of a course module; if the section is delegated, get the parent section.
     *
     * @param cm_info $cm
     * @return section_info|null
     */
    public function get_section(cm_info $cm): ?section_info {
        $section = $cm->get_section_info();
        if (!$section->is_delegated()) {
            return $section;
        }

        // If the section is delegated, we need to get the parent section.
        $delegated = sectiondelegate::instance($section);
        return $delegated->get_parent_section();
    }

    /**
     * Get all course modules in a section in order, including also activities inside sub-sections.
     *
     * @param modinfo $modinfo
     * @param section_info $section
     * @return cm_info[]
     */
    public function get_all_section_cms(modinfo $modinfo, section_info $section): array {
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
     * Get an adjacent section of a course in the given direction.
     * If currentsection is the first for 'previous' direction,
     * or the last for 'next' direction, return null.
     * If the adjacent section is delegated or not available, continue in the same direction.
     *
     * @param modinfo $modinfo
     * @param section_info $currentsection
     * @param string $direction 'next' or 'previous'.
     * @return section_info|null The adjacent section, or null if there are no more sections.
     */
    public function get_adjacent_section(
        modinfo $modinfo,
        section_info $currentsection,
        string $direction,
    ): ?section_info {
        if ($direction !== 'next' && $direction !== 'previous') {
            throw new \coding_exception("Invalid direction '{$direction}'. Expected 'next' or 'previous'.");
        }

        if ($direction === 'previous' && $currentsection->sectionnum <= 0) {
            // Already at the first section.
            return null;
        }

        $offset = ($direction === 'next') ? 1 : -1;
        $section = $modinfo->get_section_info($currentsection->sectionnum + $offset);
        if ($section === null) {
            return null;
        }

        // If the section is delegated, hidden, or its restrictions are hidden (eye closed),
        // continue in the same direction to find the next available section.
        if (
            ($section->is_delegated() ||
            (!$section->uservisible && (!$section->visible || !$section->availableinfo))
            )
        ) {
            return $this->get_adjacent_section($modinfo, $section, $direction);
        }
        return $section;
    }

    /**
     * Determine whether a course module is the first accessible element in the course.
     *
     * A module is considered the first accessible element when there is no valid
     * preceding module in the current section (ignoring non-navigable or unavailable
     * modules) and no accessible previous section exists.
     *
     * The subject cm itself is not required to be navigable. Users can legitimately
     * be viewing a non-listed cm; only preceding/following cms are filtered by is_valid_cm().
     *
     * The function will throw an exception if the module is not part of $allsectioncms.
     *
     * @param cm_info $cm The course module to check.
     * @param modinfo $modinfo The course modinfo instance.
     * @param cm_info[] $allsectioncms Ordered list of section modules to evaluate.
     * @return bool True if the module is the first accessible element, false otherwise.
     * @throws \coding_exception If the provided course module is not present in $allsectioncms.
     */
    public function is_first_navigable(
        cm_info $cm,
        modinfo $modinfo,
        array $allsectioncms,
    ): bool {
        $cmindex = array_search($cm, $allsectioncms, true);
        if ($cmindex === false) {
            throw new \coding_exception('The course module is not part of the given section.');
        }

        // First element in the section checks whether there is a previous section.
        if ($cmindex <= 0) {
            $section = $this->get_section($cm);
            $previoussection = $this->get_adjacent_section($modinfo, $section, 'previous');
            return $previoussection === null;
        }

        $prevcm = $allsectioncms[$cmindex - 1];
        if ($this->is_valid_cm($prevcm)) {
            return false;
        }

        return $this->is_first_navigable($prevcm, $modinfo, $allsectioncms);
    }

    /**
     * Determine whether a course module is the last accessible element in the course.
     *
     * A module is considered the last accessible element when there is no valid
     * following module in the current section (ignoring non-navigable or unavailable
     * modules) and no accessible next section exists.
     *
     * The subject cm itself is not required to be navigable. Users can legitimately
     * be viewing a non-listed cm; only preceding/following cms are filtered by is_valid_cm().
     *
     * The function will throw an exception if the module is not part of $allsectioncms.
     *
     * @param cm_info $cm The course module to check.
     * @param modinfo $modinfo The course modinfo instance.
     * @param cm_info[] $allsectioncms Ordered list of section modules to evaluate.
     * @return bool True if the module is the last accessible element, false otherwise.
     * @throws \coding_exception If the provided course module is not present in $allsectioncms.
     */
    public function is_last_navigable(
        cm_info $cm,
        modinfo $modinfo,
        array $allsectioncms,
    ): bool {
        $cmindex = array_search($cm, $allsectioncms, true);
        if ($cmindex === false) {
            throw new \coding_exception('The course module is not part of the given section.');
        }

        // Last element in the section checks whether there is a next section.
        if ($cmindex + 1 >= count($allsectioncms)) {
            $section = $this->get_section($cm);
            $nextsection = $this->get_adjacent_section($modinfo, $section, 'next');
            return $nextsection === null;
        }

        $nextcm = $allsectioncms[$cmindex + 1];
        if ($this->is_valid_cm($nextcm)) {
            return false;
        }

        return $this->is_last_navigable($nextcm, $modinfo, $allsectioncms);
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
        $adjacentsection = $this->get_adjacent_section($modinfo, $currentsection, $direction);
        if ($adjacentsection === null) {
            // Going to previous on the first section or to next on the last section.
            return $this->redirect_to_course($response, $modinfo->get_course()->id);
        }
        $section = ($direction === 'next') ? $adjacentsection : $currentsection;
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
