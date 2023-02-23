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

        $showhomenode = empty($this->page->theme->removedprimarynavitems) ||
            !in_array('home', $this->page->theme->removedprimarynavitems);
        // We do not need to change the text for the home/dashboard depending on the set homepage.
        if ($showhomenode) {
            $sitehome = $this->add(get_string('home'), new \moodle_url('/'), self::TYPE_SYSTEM,
                null, 'home', new \pix_icon('i/home', ''));
        }
        if (isloggedin() && !isguestuser()) {
            $homepage = get_home_page();
            if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
                // We need to stop automatic redirection.
                if ($showhomenode) {
                    $sitehome->action->param('redirect', '0');
                }
            }

            // Add the dashboard link.
            $showmyhomenode = !empty($CFG->enabledashboard) && (empty($this->page->theme->removedprimarynavitems) ||
                !in_array('myhome', $this->page->theme->removedprimarynavitems));
            if ($showmyhomenode) {
                $this->add(get_string('myhome'), new \moodle_url('/my/'),
                    self::TYPE_SETTING, null, 'myhome', new \pix_icon('i/dashboard', ''));
            }

            // Add the mycourses link.
            $showcoursesnode = empty($this->page->theme->removedprimarynavitems) ||
                !in_array('courses', $this->page->theme->removedprimarynavitems);
            if ($showcoursesnode) {
                $this->add(get_string('mycourses'), new \moodle_url('/my/courses.php'), self::TYPE_ROOTNODE, null, 'mycourses');
            }
        }

        $showsiteadminnode = empty($this->page->theme->removedprimarynavitems) ||
            !in_array('siteadminnode', $this->page->theme->removedprimarynavitems);

        if ($showsiteadminnode && $node = $this->get_site_admin_node()) {
            // We don't need everything from the node just the initial link.
            $this->add($node->text, $node->action(), self::TYPE_SITE_ADMIN, null, 'siteadminnode', $node->icon);
        }

        // IOMAD.
        $systemcontext = \context_system::instance();
        if (\iomad::has_capability('block/iomad_company_admin:companymanagement_view', $systemcontext) ||
            \iomad::has_capability('block/iomad_company_admin:usermanagement_view', $systemcontext) ||
            \iomad::has_capability('block/iomad_company_admin:coursemanagement_view', $systemcontext) ||
            \iomad::has_capability('block/iomad_company_admin:licensemanagement_view', $systemcontext) ||
            \iomad::has_capability('block/iomad_company_admin:competencymanagement_view', $systemcontext) ||
            \iomad::has_capability('block/iomad_commerce:admin_view', $systemcontext) ||
            \iomad::has_capability('block/iomad_microlearning:view', $systemcontext) ||
            \iomad::has_capability('block/iomad_reports:view', $systemcontext)) {
            $this->add(get_string('dashboard', 'block_iomad_company_admin'),
                       new \moodle_url('/blocks/iomad_company_admin/index.php'),
                       self::TYPE_SITE_ADMIN,
                       null,
                       'ioaddashboardnode',
                       new \pix_icon('i/dashboard', ''));
        }

        // Search and set the active node.
        $this->set_active_node();
        $this->initialised = true;
    }

    /**
     * Get the site admin node if available.
     *
     * @return navigation_node|null
     */
    private function get_site_admin_node(): ?navigation_node {
        // Add the site admin node. We are using the settingsnav so as to avoid rechecking permissions again.
        $settingsnav = $this->page->settingsnav;
        $node = $settingsnav->find('siteadministration', self::TYPE_SITE_ADMIN);
        if (!$node) {
            // Try again. This node can exist with 2 different keys.
            $node = $settingsnav->find('root', self::TYPE_SITE_ADMIN);
        }

        return $node ?: null;
    }

    /**
     * Find and set the active node. Initially searches based on URL/explicitly set active node.
     * If nothing is found, it checks the following:
     *      - If the node is a site page, set 'Home' as active
     *      - If within a course context, set 'My courses' as active
     *      - If within a course category context, set 'Site Admin' (if available) else set 'Home'
     *      - Else if available set site admin as active
     *      - Fallback, set 'Home' as active
     */
    private function set_active_node(): void {
        global $SITE;
        $activenode = $this->search_and_set_active_node($this);
        // If we haven't found an active node based on the standard search. Follow the criteria above.
        if (!$activenode) {
            $children = $this->get_children_key_list();
            $navactivenode = $this->page->navigation->find_active_node();
            $activekey = 'home';
            if (isset($navactivenode->parent) && $navactivenode->parent->text == get_string('sitepages')) {
                $activekey = 'home';
            } else if (in_array($this->context->contextlevel, [CONTEXT_COURSE, CONTEXT_MODULE])) {
                if ($this->page->course->id != $SITE->id) {
                    $activekey = 'courses';
                }
            } else if (in_array('siteadminnode', $children) && $node = $this->get_site_admin_node()) {
                if ($this->context->contextlevel == CONTEXT_COURSECAT || $node->search_for_active_node(URL_MATCH_EXACT)) {
                    $activekey = 'siteadminnode';
                }
            }

            if ($activekey && $activenode = $this->find($activekey, null)) {
                $activenode->make_active();
            }
        }
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
        } else if ($node->check_if_active()) {
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
