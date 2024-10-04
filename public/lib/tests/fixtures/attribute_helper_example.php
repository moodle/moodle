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

namespace core;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_ALL)]
class attribute_helper_attribute_a {
    public function __construct(
        public readonly string $value,
    ) {
    }
}

#[\Attribute]
class attribute_helper_attribute_b {
}

/**
 * Helper for loading attributes.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[attribute_helper_attribute_a('a')]
#[attribute_helper_attribute_a('b')]
#[attribute_helper_attribute_b]
class attribute_helper_example {
    #[attribute_helper_attribute_a('a')]
    #[attribute_helper_attribute_a('b')]
    #[attribute_helper_attribute_b]
    public const WITH_ATTRIBUTES = 'examplevalue';

    public const WITHOUT_ATTRIBUTE = 'examplevalue';

    #[attribute_helper_attribute_a('a')]
    #[attribute_helper_attribute_a('b')]
    #[attribute_helper_attribute_b]
    public string $withattributes = 'With attributes';

    public string $withoutattributes = 'Without attributes';

    #[attribute_helper_attribute_a('a')]
    #[attribute_helper_attribute_a('b')]
    #[attribute_helper_attribute_b]
    public function with_attributes(): void {
    }

    public function without_attributes(): void {
    }
}

class attribute_helper_example_without {
}

#[attribute_helper_attribute_a('a')]
#[attribute_helper_attribute_a('b')]
#[attribute_helper_attribute_b]
enum attribute_helper_enum: string {
    #[attribute_helper_attribute_a('a')]
    #[attribute_helper_attribute_a('b')]
    #[attribute_helper_attribute_b]
    case WITH_ATTRIBUTES = 'With attributes';

    case WITHOUT_ATTRIBUTE = 'Without attributes';
}

#[attribute_helper_attribute_a('a')]
#[attribute_helper_attribute_a('b')]
#[attribute_helper_attribute_b]
function attribute_helper_method_with(): void {
}

function attribute_helper_method_without(): void {
}
