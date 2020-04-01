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
 * core_contentbank specific renderers
 *
 * @package   core_contentbank
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class containing data for bank content
 *
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bankcontent implements renderable, templatable {

    /**
     * @var \core_contentbank\content[]    Array of content bank contents.
     */
    private $contents;

    /**
     * @var array   $toolbar object.
     */
    private $toolbar;

    /**
     * @var \context    Given context. Null by default.
     */
    private $context;

    /**
     * Construct this renderable.
     *
     * @param \core_contentbank\content[] $contents   Array of content bank contents.
     * @param array $toolbar     List of content bank toolbar options.
     * @param \context $context Optional context to check (default null)
     */
    public function __construct(array $contents, array $toolbar, \context $context = null) {
        $this->contents = $contents;
        $this->toolbar = $toolbar;
        $this->context = $context;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $contentdata = array();
        foreach ($this->contents as $content) {
            $record = $content->get_content();
            $managerclass = $content->get_content_type().'\\contenttype';
            if (class_exists($managerclass)) {
                $manager = new $managerclass($this->context);
                if ($manager->can_access()) {
                    $name = $content->get_name();
                    $contentdata[] = array(
                        'name' => $name,
                        'link' => $manager->get_view_url($record),
                        'icon' => $manager->get_icon($name)
                    );
                }
            }
        }
        $data->contents = $contentdata;
        $data->tools = $this->toolbar;
        return $data;
    }
}
