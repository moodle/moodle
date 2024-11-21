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
 * Compat.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No declared namespace, on purpose!
defined('MOODLE_INTERNAL') || die();

// This file will never be autoloaded, and should never be included either. Its content
// will never be executed, even if it is loaded by accident. It is only here for compatibility
// reasons since Moodle has deprecated the top-level context classes and Intelephense does not
// understand the class_alias function (https://github.com/bmewburn/vscode-intelephense/issues/600).
if (false) {

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    abstract class context extends core\context {
    }

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    abstract class context_helper extends core\context_helper {
    }

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    class context_block extends core\context\block {
    }

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    class context_course extends core\context\course {
    }

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    class context_coursecat extends core\context\coursecat {
    }

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    class context_module extends core\context\module {
    }

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    class context_system extends core\context\system {
    }

    /**
     * Fake class to mitigate IDE's failure to identify class_alias.
     */
    class context_user extends core\context\user {
    }

}
