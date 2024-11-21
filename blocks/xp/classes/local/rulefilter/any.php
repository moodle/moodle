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
 * Filter.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rulefilter;

use context;
use lang_string;

/**
 * Filter.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class any implements rulefilter {

    public function get_action_tester(context $effectivecontext, object $config): action_tester {
        return new any_tester();
    }

    public function get_compatible_context_levels(): array {
        return [CONTEXT_SYSTEM, CONTEXT_COURSE];
    }

    public function get_display_name(): lang_string {
        return new lang_string('rulefilterany', 'block_xp');
    }

    public function get_label_for_config(object $config, ?context $effectivecontext = null): string {
        return get_string('rulefilterany', 'block_xp');
    }

    public function get_short_description(): lang_string {
        return new lang_string('rulefilteranydesc', 'block_xp');
    }

    public function is_compatible_with_admin(): bool {
        return true;
    }

    public function is_multiple_allowed(): bool {
        return false;
    }

}
