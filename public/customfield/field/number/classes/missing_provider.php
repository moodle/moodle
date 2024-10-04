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

namespace customfield_number;

/**
 * Class missing_provider
 *
 * @package    customfield_number
 * @author     2024 Marina Glancy
 * @copyright  2024 Moodle Pty Ltd <support@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class missing_provider extends provider_base {

    /**
     * {@inheritDoc}
     */
    public function get_name(): string {
        return get_string('invalidprovider', 'customfield_number');
    }

    /**
     * {@inheritDoc}
     */
    public function is_available(): bool {
        return false;
    }
}
