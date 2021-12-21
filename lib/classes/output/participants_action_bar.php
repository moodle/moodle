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

namespace core\output;

use moodle_page;
use navigation_node;
use moodle_url;

/**
 * Class responsible for generating the action bar (tertiary nav) elements in the participants page and related pages.
 *
 * @package    core
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_action_bar implements \renderable {
    /** @var object $course The course we are dealing with. */
    private $course;
    /** @var moodle_page $page The current page. */
    private $page;
    /** @var navigation_node $node The settings node for the participants page. */
    private $node;
    /** @var string|null $renderedcontent Rendered buttons to be displayed in-line with the select box */
    private $renderedcontent;

    /**
     * Constructor participants_action_bar
     * @param object $course The course that we are generating the nav for
     * @param moodle_page $page The page object
     * @param string|null $renderedcontent Any additional rendered content/actions to be displayed in line with the nav
     */
    public function __construct(object $course, moodle_page $page, ?string $renderedcontent) {
        $this->course = $course;
        $this->page = $page;
        $this->node = $this->page->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
        $this->renderedcontent = $renderedcontent;
    }

    /**
     * Return the nodes required to be displayed in the url_select box.
     * The nodes are divided into 3 sections each with the heading as the key.
     *
     * @return array
     */
    protected function get_ordered_nodes(): array {
        return [
            'enrolments:enrol' => [
                'review',
                'manageinstances'
            ],
            'groups:group' => [
                'groups'
            ],
            'permissions:role' => [
                'override',
                'roles',
                'otherusers',
                'permissions'
            ]
        ];
    }

    /**
     * Get the content for the url_select select box.
     *
     * @return array
     */
    protected function get_content_for_select(): array {
        if (!$this->node) {
            return [];
        }

        $nodes = $this->get_ordered_nodes();
        $formattedcontent = [];
        foreach ($nodes as $description => $content) {
            list($stringid, $location) = explode(':', $description);
            $heading = get_string($stringid, $location);
            $items = [];
            foreach ($content as $key) {
                if ($node = $this->node->find($key, null)) {
                    $urldescription = $node->text;
                    $items[$node->action()->out()] = $urldescription;

                    // Additional items to be added.
                    if ($key === 'groups') {
                        $params = ['id' => $this->course->id];
                        $items += [
                            (new moodle_url('/group/groupings.php', $params))->out() => get_string('groupings', 'group'),
                            (new moodle_url('/group/overview.php', $params))->out() => get_string('overview', 'group')
                        ];
                    }
                }
            }
            if ($items) {
                $formattedcontent[][$heading] = $items;
            }
        }

        // TODO: Implement MDL-72930.
        return $formattedcontent;
    }

    /**
     * Recursively tries to find a matching url
     * @param array $urlcontent The content for the url_select
     * @param int $strictness Strictness for the compare criteria
     * @return string The matching active url
     */
    protected function find_active_page(array $urlcontent, int $strictness = URL_MATCH_EXACT): string {
        foreach ($urlcontent as $key => $value) {
            if (is_array($value) && $activeitem = $this->find_active_page($value, $strictness)) {
                return $activeitem;
            } else if ($this->page->url->compare(new moodle_url($key), $strictness)) {
                return $key;
            }
        }

        return "";
    }

    /**
     * Gets the url_select to be displayed in the participants page if available.
     *
     * @param \renderer_base $output
     * @return object|null The content required to render the url_select
     */
    public function get_dropdown(\renderer_base $output): ?object {
        if ($urlselectcontent = $this->get_content_for_select()) {
            $activeurl = $this->find_active_page($urlselectcontent);
            $activeurl = $activeurl ?: $this->find_active_page($urlselectcontent, URL_MATCH_BASE);
            $urlselect = new \url_select($urlselectcontent, $activeurl, null);
            $urlselect->set_label(get_string('participantsnavigation', 'course'), ['class' => 'sr-only']);
            return $urlselect->export_for_template($output);
        }

        return null;
    }

    /**
     * Export the content to be displayed on the participants page.
     *
     * @param \renderer_base $output
     * @return array Consists of the following:
     *              - urlselect A stdclass representing the standard navigation options to be fed into a urlselect
     *              - renderedcontent Rendered content to be displayed in line with the tertiary nav
     */
    public function export_for_template(\renderer_base $output) {
        return [
            'urlselect' => $this->get_dropdown($output),
            'renderedcontent' => $this->renderedcontent,
        ];
    }
}
