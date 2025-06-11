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
namespace theme_snap;
use theme_snap\snap_base_test;

/**
 * Testing TOC Renderer for Snap theme.
 *
 * @package   theme_snap
 * @author    Diego Monroy
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snap_toc_renderer_test extends snap_base_test {

    /**
     * Testing TOC Renderer for Snap.
     *
     * @return void
     */
    public function test_get_path_hiddentoc() {
        $this->resetAfterTest();
        $target = null;

        // Set new Moodle Page and set context.
        $page = new \moodle_page();
        $page->set_context(\CONTEXT_SYSTEM);

        // Get configuration for xp block.
        $blockxp = get_config('block_xp');

        if (!empty($blockxp)) {
            // Use and get object from class core_renderer.
            $courserenderer = new \theme_snap\output\core_renderer($page, $target);

            // Assertion with False value.
            $url = false;
            $toc = $courserenderer->get_path_hiddentoc($url);
            $this->assertFalse($toc);

            // Assertion with a valid path.
            $url = '/course/view.php';
            $toc = $courserenderer->get_path_hiddentoc($url);
            $this->assertFalse($toc);

            // Assertion with a forbidden path value.
            $url = '/blocks/xp/index.php';
            $toc = $courserenderer->get_path_hiddentoc($url);
            $this->assertTrue($toc);
        }
    }
}
