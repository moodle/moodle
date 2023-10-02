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

namespace core\hook\navigation;

use core\hook\described_hook;
use core\hook\stoppable_trait;
use core\navigation\views\primary;

/**
 * Allows plugins to insert nodes into site primary navigation
 *
 * @package    core
 * @copyright  2023 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary_extend implements described_hook,
        \Psr\EventDispatcher\StoppableEventInterface {
    use stoppable_trait;

    /**
     * Creates new hook.
     *
     * @param primary $primaryview Primary navigation view
     */
    public function __construct(protected primary $primaryview) {
    }

    /**
     * Primary navigation view
     *
     * @return primary
     */
    public function get_primaryview(): primary {
        return $this->primaryview;
    }

    /**
     * Describes the hook purpose.
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'Allows plugins to insert nodes into site primary navigation';
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['navigation'];
    }
}
