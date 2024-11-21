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
 * User state.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use block_xp\local\utils\user_utils;
use moodle_url;
use renderable;
use stdClass;
use user_picture;

/**
 * User state.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_state implements renderable, state, state_with_subject {

    /** @var int The course ID. */
    protected $courseid;
    /** @var stdClass The user object. */
    protected $user;
    /** @var int The user's XP. */
    protected $xp;
    /** @var levels_info The levels info. */
    protected $levelsinfo;
    /** @var level The level. */
    protected $level;
    /** @var level The next level. */
    protected $nextlevel;
    /**
     * Constructor.
     *
     * @param stdClass $user The user object.
     * @param int $xp The user XP.
     * @param levels_info $levelsinfo Levels info.
     * @param int $courseid The course ID.
     */
    public function __construct(stdClass $user, $xp, levels_info $levelsinfo, $courseid = null) {
        $this->user = $user;
        $this->xp = $xp;
        $this->levelsinfo = $levelsinfo;
        $this->courseid = !empty($courseid) ? $courseid : SITEID;
    }

    public function get_id() {
        return $this->user->id;
    }

    public function get_level() {
        if (!$this->level) {
            $this->level = $this->levelsinfo->get_level_from_xp($this->xp);
        }
        return $this->level;
    }

    public function get_link() {
        $userid = $this->user->id;
        $profileurl = new moodle_url('/user/profile.php', ['id' => $userid]);
        if ($this->courseid != SITEID) {
            $profileurl = new moodle_url('/user/view.php', ['id' => $userid, 'course' => $this->courseid]);
        }
        return $profileurl;
    }

    public function get_name() {
        return fullname($this->user);
    }

    public function get_picture() {
        return user_utils::user_picture($this->user);
    }

    public function get_ratio_in_level() {
        $total = $this->get_total_xp_in_level();
        if ($total <= 0) {
            return 1;
        }
        return $this->get_xp_in_level() / $total;
    }

    public function get_total_xp_in_level() {
        $nextlevel = $this->get_next_level();
        if (!$nextlevel) {
            return $this->get_xp_in_level();
        }

        $level = $this->get_level();
        return $nextlevel->get_xp_required() - $level->get_xp_required();
    }

    /**
     * Return the user object.
     *
     * @return stdClass
     */
    public function get_user() {
        return $this->user;
    }

    public function get_xp() {
        return $this->xp;
    }

    public function get_xp_in_level() {
        return $this->xp - $this->get_level()->get_xp_required();
    }

    /**
     * Get the next level, if any.
     *
     * @return null|level
     */
    protected function get_next_level() {
        if ($this->nextlevel === null) {
            $levelnum = $this->get_level()->get_level() + 1;
            if ($levelnum > $this->levelsinfo->get_count()) {
                $this->nextlevel = false;
            } else {
                $this->nextlevel = $this->levelsinfo->get_level($levelnum);
            }
        }

        if ($this->nextlevel === false) {
            return null;
        }
        return $this->nextlevel;
    }

}
