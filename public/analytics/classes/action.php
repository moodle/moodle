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
 * Representation of a suggested action.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Representation of a suggested action.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class action {

    /**
     * @var  Action type useful.
     */
    const TYPE_POSITIVE = 'useful';

    /**
     * @var  Action type notuseful.
     */
    const TYPE_NEGATIVE = 'notuseful';

    /**
     * @var  Action type neutral.
     */
    const TYPE_NEUTRAL = 'neutral';

    /**
     * @var string
     */
    protected $actionname = null;

    /**
     * @var \moodle_url
     */
    protected $url = null;

    /**
     * @var \renderable
     */
    protected $actionlink = null;

    /**
     * @var string
     */
    protected $text = null;

    /** @var string Store the action type. */
    protected string $type = '';

    /**
     * Returns the action name.
     *
     * @return string
     */
    public function get_action_name() {
        return $this->actionname;
    }

    /**
     * Returns the url to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Returns the link to the action.
     *
     * @return \renderable
     */
    public function get_action_link() {
        return $this->actionlink;
    }

    /**
     * Returns the action text.
     * @return string
     */
    public function get_text() {
        return $this->text;
    }

    /**
     * Sets the type of the action according to its positiveness.
     *
     * @throws \coding_exception
     * @param string|false $type \core_analytics\action::TYPE_POSITIVE, TYPE_NEGATIVE or TYPE_NEUTRAL
     */
    public function set_type($type = false) {
        if (!$type) {
            // Any non-standard action specified by a target is considered positive by default because that is what
            // they are meant to be.
            $type = self::TYPE_POSITIVE;
        }

        if ($type !== self::TYPE_POSITIVE && $type !== self::TYPE_NEUTRAL &&
                $type !== self::TYPE_NEGATIVE) {
            throw new \coding_exception('The provided type must be ' . self::TYPE_POSITIVE . ', ' . self::TYPE_NEUTRAL .
                ' or ' . self::TYPE_NEGATIVE);
        }
        $this->type = $type;
    }

    /**
     * Returns the type of action.
     *
     * @return string The positiveness of the action (self::TYPE_POSITIVE, self::TYPE_NEGATIVE or self::TYPE_NEUTRAL)
     */
    public function get_type() {
        return $this->type;
    }
}
