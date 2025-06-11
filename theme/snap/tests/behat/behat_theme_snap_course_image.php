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
 * Check elements in course categories
 * @author    Dayana Pardo
 * @copyright Copyright (c) 2025 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;

/**
 * Check that the course image maintains its aspect ratio
 *
 * @author    Dayana Pardo
 * @copyright Copyright (c) 2025 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_course_image extends behat_base {

    /**
     *
     * @Given /^the image "(?P<selector>[^"]*)" should maintain its aspect ratio$/
     * @param string $selector CSS image selector.
     * @throws ExpectationException If the image is distorted.
     */
    public function the_image_should_maintain_its_aspect_ratio($selector) {
        $session = $this->getSession();
        $page = $session->getPage();
        $image = $page->find('css', $selector);

        if (!$image) {
            throw new ExpectationException("Image not found with selector '$selector'", $session);
        }

        $this->spin(function() use ($image) {
            return $image->isVisible();
        }, 5);

        // Get the actual dimensions
        $script = '(() => {
        const img = document.querySelector("' . $selector . '");
        if (!img || !img.complete) return null;
        return {
            naturalWidth: img.naturalWidth,
            naturalHeight: img.naturalHeight,
            clientWidth: img.clientWidth,
            clientHeight: img.clientHeight
        };
    })()';

        $info = $this->getSession()->evaluateScript($script);

        if (!$info || !$info['naturalWidth'] || !$info['naturalHeight'] || !$info['clientWidth'] || !$info['clientHeight']) {
            throw new ExpectationException("Could not get image dimensions '$selector'.", $session);
        }

        $naturalRatio = $info['naturalWidth'] / $info['naturalHeight'];
        $renderedRatio = $info['clientWidth'] / $info['clientHeight'];

        if (abs($naturalRatio - $renderedRatio) > 0.2) {
            throw new ExpectationException("The image '$selector' is distorted. Natural ratio: $naturalRatio, rendered ratio: $renderedRatio", $session);
        }

    }

}
