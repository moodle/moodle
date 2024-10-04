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

namespace core\output\local\properties;

/**
 * Enum badge.
 *
 * This enum is used to define the different types of badges that can be used in the application.
 *
 * @package    core
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum badge: string {
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case SUCCESS = 'success';
    case DANGER = 'danger';
    case WARNING = 'warning';
    case INFO = 'info';

    /**
     * Returns the CSS classes for the property based on its type.
     *
     * @return string The CSS classes.
     */
    public function classes(): string {
        return match ($this) {
            self::PRIMARY => ' badge rounded-pill text-bg-primary',
            self::SECONDARY => ' badge rounded-pill text-bg-secondary',
            self::SUCCESS => ' badge rounded-pill text-bg-success',
            self::DANGER => ' badge rounded-pill text-bg-danger',
            self::WARNING => ' badge rounded-pill text-bg-warning',
            self::INFO => ' badge rounded-pill text-bg-info',
        };
    }
}
