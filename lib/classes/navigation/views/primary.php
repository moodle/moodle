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

namespace core\navigation\views;

/**
 * Class primary.
 *
 * The primary navigation view is a combination of few components - navigation, output->navbar,
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary extends view {
    /**
     * Initialise the primary navigation node
     */
    public function initialise(): void {
        if (during_initial_install() || $this->initialised) {
            return;
        }
        $this->id = 'primary_navigation';
        $this->add(get_string('home'), new \moodle_url('/'), self::TYPE_SYSTEM,
                null, 'home', new \pix_icon('i/home', ''));

        // Add the dashboard link.
        if (isloggedin() && !isguestuser()) {  // Makes no sense if you aren't logged in.
            $this->rootnodes['home'] = $this->add(get_string('myhome'), new \moodle_url('/my/'),
                self::TYPE_SETTING, null, 'myhome', new \pix_icon('i/dashboard', ''));
        }

        // Add a dummy mycourse link to a mycourses page.
        $this->add(get_string('mycourses'), new \moodle_url('/course/index.php'), self::TYPE_ROOTNODE, null, 'courses');

        // Add the site admin node. We are using the settingsnav so as to avoid rechecking permissions again.
        $settingsnav = $this->page->settingsnav;
        $node = $settingsnav->find('siteadministration', self::TYPE_SITE_ADMIN);
        if (!$node) {
            // Try again. This node can exist with 2 different keys.
            $node = $settingsnav->find('root', self::TYPE_SITE_ADMIN);
        }

        if ($node) {
            // We don't need everything from the node just the initial link.
            $this->add($node->text, $node->action(), self::TYPE_SITE_ADMIN, null, 'siteadminnode', $node->icon);
        }

        // Search and set the active node.
        $this->search_for_active_node();
        $this->initialised = true;
    }
}
