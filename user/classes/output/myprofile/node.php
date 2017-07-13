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
 * Defines a node in my profile page navigation.
 *
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\output\myprofile;
defined('MOODLE_INTERNAL') || die();

/**
 * Defines a node in my profile page navigation.
 *
 * @since     Moodle 2.9
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class node implements \renderable {
    /**
     * @var string Name of parent category.
     */
    private $parentcat;

    /**
     * @var string Name of this node.
     */
    private $name;

    /**
     * @var string Name of the node after which this node should appear.
     */
    private $after;

    /**
     * @var string Title of this node.
     */
    private $title;

    /**
     * @var string|\moodle_url Url that this node should link to.
     */
    private $url;

    /**
     * @var string Content to display under this node.
     */
    private $content;

    /**
     * @var string|\pix_icon Icon for this node.
     */
    private $icon;

    /**
     * @var string HTML class attribute for this node. Classes should be separated by a space, e.g. 'class1 class2'
     */
    private $classes;

    /**
     * @var array list of properties accessible via __get.
     */
    private $properties = array('parentcat', 'after', 'name', 'title', 'url', 'content', 'icon', 'classes');

    /**
     * Constructor for the node.
     *
     * @param string $parentcat Name of parent category.
     * @param string $name Name of this node.
     * @param string $title Title of this node.
     * @param null|string $after Name of the node after which this node should appear.
     * @param null|string|\moodle_url $url Url that this node should link to.
     * @param null|string $content Content to display under this node.
     * @param null|string|\pix_icon $icon Icon for this node.
     * @param null|string $classes a list of css classes.
     */
    public function __construct($parentcat, $name, $title, $after = null, $url = null, $content = null, $icon = null,
                                $classes = null) {
        $this->parentcat = $parentcat;
        $this->after = $after;
        $this->name = $name;
        $this->title = $title;
        $this->url = is_null($url) ? null : new \moodle_url($url);
        $this->content = $content;
        $this->icon = $icon;
        $this->classes = $classes;
    }

    /**
     * Magic get method.
     *
     * @param string $prop property to get.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($prop) {
        if (in_array($prop, $this->properties)) {
            return $this->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }
}
