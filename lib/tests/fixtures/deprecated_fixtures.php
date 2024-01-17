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

namespace core\fixtures;

use core\deprecated;

/**
 * A file containing a variety of fixturs for deprecated attribute tests.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

#[deprecated('Deprecated class')]
class deprecated_class {
    protected string $notdeprecatedproperty = 'Not deprecated property';

    #[deprecated('Deprecated property')]
    protected string $deprecatedproperty = 'Deprecated property';

    const NOT_DEPRECATED_CONST = 'Not deprecated const';

    #[deprecated('Deprecated const')]
    const DEPRECATED_CONST = 'Deprecated const';

    public function not_deprecated_method() {}

    #[deprecated('Deprecated method')]
    public function deprecated_method() {}
}

class not_deprecated_class {}

function not_deprecated_function() {}

#[deprecated('Deprecated function')]
function deprecated_function() {}
