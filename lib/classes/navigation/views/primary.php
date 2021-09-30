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

use navigation_node;

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
        global $CFG;
        if (during_initial_install() || $this->initialised) {
            return;
        }
        $this->id = 'primary_navigation';
        if (isloggedin() && !isguestuser()) {
            $homepage = get_home_page();
            if ($homepage === HOMEPAGE_SITE) {
                $this->add(get_string('home'), new \moodle_url('/'), self::TYPE_SYSTEM,
                        null, 'home', new \pix_icon('i/home', ''));
                $this->rootnodes['home'] = $this->add(get_string('myhome'), new \moodle_url('/my/'),
                        self::TYPE_SETTING, null, 'myhome', new \pix_icon('i/dashboard', ''));
            } else if ($homepage === HOMEPAGE_MY) {
                $this->add(get_string('myhome'), new \moodle_url('/my/'), self::TYPE_SYSTEM,
                        null, 'myhome', new \pix_icon('i/dashboard', ''));
                $this->rootnodes['home'] = $this->add(get_string('sitehome'), new \moodle_url('/'),
                        self::TYPE_SETTING, null, 'home', new \pix_icon('i/home', ''));
                if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MY)) {
                    // We need to stop automatic redirection.
                    $this->rootnodes['home']->action->param('redirect', '0');
                }
            }
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
        $this->search_and_set_active_node($this);
        $this->initialised = true;
    }

    /**
     * Searches all children for the matching active node
     *
     * This method recursively traverse through the node tree to
     * find the node to activate/highlight:
     * 1. If the user had set primary node key to highlight, it
     *    tries to match this key with the node(s). Hence it would
     *    travel all the nodes.
     * 2. If no primary key is provided by the dev, then it would
     *    check for the active node set in the tree.
     *
     * @param navigation_node $node
     * @param array $actionnodes navigation nodes array to set active and inactive.
     * @return navigation_node|null
     */
    private function search_and_set_active_node(navigation_node $node,
        array &$actionnodes = []): ?navigation_node {
        global $PAGE;

        $activekey = $PAGE->get_primary_activate_tab();
        if ($activekey) {
            if ($node->key && ($activekey === $node->key)) {
                return $node;
            }
        } else if ($node->check_if_active(URL_MATCH_BASE)) {
            return $node;
        }

        foreach ($node->children as $child) {
            $outcome = $this->search_and_set_active_node($child, $actionnodes);
            if ($outcome !== null) {
                $outcome->make_active();
                $actionnodes['active'] = $outcome;
                if ($activekey === null) {
                    return $actionnodes['active'];
                }
            } else {
                // If the child is active then make it inactive.
                if ($child->isactive) {
                    $actionnodes['set_inactive'][] = $child;
                }
            }
        }

        // If we have successfully found an active node then reset any other nodes to inactive.
        if (isset($actionnodes['set_inactive']) && isset($actionnodes['active'])) {
            foreach ($actionnodes['set_inactive'] as $inactivenode) {
                $inactivenode->make_inactive();
            }
            $actionnodes['set_inactive'] = [];
        }
        return ($actionnodes['active'] ?? null);
    }
}
