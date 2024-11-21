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
 * Level with badge & description.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

/**
 * Level with badge & description.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badged_level extends described_level implements level_with_badge {

    /** @var badge_url_resolver Badge URL resolver. */
    protected $resolver;

    /**
     * Constructor.
     *
     * @param int $level The level.
     * @param int $xprequired The XP required.
     * @param string $desc The description.
     * @param badge_url_resolver $resolver The URL resolver.
     * @param string|null $name The name.
     */
    public function __construct($level, $xprequired, $desc, badge_url_resolver $resolver, $name = null) {
        parent::__construct($level, $xprequired, $desc, $name);
        $this->resolver = $resolver;
    }

    /**
     * Get the badge URL.
     *
     * @return moodle_url|null
     */
    public function get_badge_url() {
        return $this->resolver->get_url_for_level($this->get_level());
    }

}
