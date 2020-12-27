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
 * The definition of an item which can be exported.
 *
 * @package     core
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core\content\export;

use context;
use core\content\export\exported_item;
use core\content\export\zipwriter;

/**
 * An object used to represent content which can be served.
 *
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class exportable_item {

    /** @var context The context associated with this exportable item */
    protected $context = null;

    /** @var string The component being exported */
    protected $component = null;

    /** @var string The name displayed to the user */
    protected $uservisiblename = null;

    /**
     * Create a new exportable_item instance.
     *
     * @param   context $context The context that this content belongs to
     * @param   string $component The component that this content relates to
     * @param   string $uservisiblename The name displayed in the export
     */
    public function __construct(context $context, string $component, string $uservisiblename) {
        $this->context = $context;
        $this->component = $component;
        $this->uservisiblename = $uservisiblename;
    }

    /**
     * Get the context that this exportable item is for.
     *
     * @return  context
     */
    public function get_context(): context {
        return $this->context;
    }

    /**
     * Get the component that this exportable item relates to.
     *
     * @return  string
     */
    public function get_component(): string {
        return $this->component;
    }

    /**
     * Get the user visible name for the exportable item.
     *
     * @return  string
     */
    public function get_user_visible_name(): string {
        return $this->uservisiblename;
    }

    /**
     * Add the content to the archive.
     *
     * @param   zipwriter $archive
     */
    abstract public function add_to_archive(zipwriter $archive): ?exported_item;
}
