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

declare(strict_types=1);

namespace core\output;

use core\output\dynamic_tabs\base;

/**
 * Class dynamic tabs
 *
 * @package     core
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dynamic_tabs implements templatable {
    /** @var base[]  */
    protected $tabs = [];

    /**
     * tabs constructor.
     *
     * @param base[] $tabs array of tab
     */
    public function __construct(array $tabs = []) {
        foreach ($tabs as $tab) {
            $this->add_tab($tab);
        }
    }

    /**
     * Add a tab
     *
     * @param base $tab
     */
    public function add_tab(base $tab): void {
        $this->tabs[] = $tab;
    }

    /**
     * Implementation of exporter from templatable interface
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'tabs' => [],
        ];

        foreach ($this->tabs as $tab) {
            $dataattributes = [];
            foreach ($tab->get_data() as $name => $value) {
                $dataattributes[] = ['name' => $name, 'value' => $value];
            }

            $data['tabs'][] = [
                'shortname' => $tab->get_tab_id(),
                'displayname' => $tab->get_tab_label(),
                'enabled' => $tab->is_available(),
                'tabclass' => get_class($tab),
                'dataattributes' => $dataattributes,
            ];
        }

        $data['showtabsnavigation'] = (count($data['tabs']) > 1) ? 1 : 0;

        return $data;
    }
}
