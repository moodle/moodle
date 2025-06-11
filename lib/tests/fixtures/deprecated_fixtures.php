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

use core\attribute\deprecated;

/**
 * A file containing a variety of fixturs for deprecated attribute tests.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

#[deprecated('not_deprecated_class')]
class deprecated_class {
    protected string $notdeprecatedproperty = 'Not deprecated property';

    #[deprecated('$this->notdeprecatedproperty')]
    protected string $deprecatedproperty = 'Deprecated property';

    const NOT_DEPRECATED_CONST = 'Not deprecated const';

    #[deprecated('not_deprecated_class::NEW_CONSTANT')]
    const DEPRECATED_CONST = 'Deprecated const';

    public function not_deprecated_method() {
    }

    #[deprecated(replacement: null, mdl: 'MDL-80677')]
    public function deprecated_method() {
    }
}

class not_deprecated_class {
    protected string $notdeprecatedproperty = 'Not deprecated property';

    #[deprecated('$this->notdeprecatedproperty')]
    protected string $deprecatedproperty = 'Deprecated property';

    const NOT_DEPRECATED_CONST = 'Not deprecated const';

    #[deprecated('self::NOT_DEPRECATED_CONST')]
    const DEPRECATED_CONST = 'Deprecated const';

    public function not_deprecated_method() {
    }

    #[deprecated('$this->not_deprecated_method()')]
    public function deprecated_method() {
    }
}

function not_deprecated_function() {
}

#[deprecated('not_deprecated_class::not_deprecated_method()')]
function deprecated_function() {
}

interface not_deprecated_interface {
    const NOT_DEPRECATED_CONST = 'Not deprecated const';

    #[deprecated('self::NOT_DEPRECATED_CONST')]
    const DEPRECATED_CONST = 'Deprecated const';

    // Note: It does not make sense to deprecate methods in an _interface_ as the interface itself should be deprecated.
}

#[deprecated('not_deprecated_interface')]
interface deprecated_interface {
    const DEPRECATED_CONST = 'Deprecated const';

    public function not_deprecated_method();
}

trait not_deprecated_trait {
    protected string $notdeprecatedproperty = 'Not deprecated property';

    #[deprecated('$this->notdeprecatedproperty')]
    protected string $deprecatedproperty = 'Deprecated property';

    public function not_deprecated_method() {
    }

    #[deprecated('$this->not_deprecated_method()')]
    public function deprecated_method() {
    }
}

#[deprecated(not_deprecated_trait::class)]
trait deprecated_trait {
    protected string $notdeprecatedproperty = 'Not deprecated property';

    #[deprecated('$this->notdeprecatedproperty')]
    protected string $deprecatedproperty = 'Deprecated property';

    public function not_deprecated_method() {
    }

    #[deprecated(replacement: null, mdl: 'MDL-80677')]
    public function deprecated_method() {
    }
}

class not_deprecated_class_using_deprecated_trait_features {
    use deprecated_trait;
}

class not_deprecated_class_implementing_deprecated_interface implements deprecated_interface {
    public function not_deprecated_method() {
    }
}

class not_deprecated_class_using_not_deprecated_trait_features {
    use not_deprecated_trait;
}

class not_deprecated_class_implementing_not_deprecated_interface implements not_deprecated_interface {
    public function not_deprecated_method() {
    }
}
