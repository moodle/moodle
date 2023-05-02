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
 * @package filter_oembed
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 The POET Group
 */

namespace filter_oembed\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for oembed endpoints.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 The POET Group
 */
class endpoint {
    /**
     * @var array
     */
    protected $schemes = [];

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var boolean
     */
    protected $discovery = false;

    /**
     * @var array
     */
    protected $formats = ['json'];

    /**
     * Constructor.
     * @param $data JSON decoded array or data object containing all endpoint data.
     */
    public function __construct($data = null) {
        if (is_object($data)) {
            $data = (array)$data;
        }
        if (isset($data['schemes'])) {
            $this->schemes = $data['schemes'];
        }
        if (isset($data['url'])) {
            $this->url = $data['url'];
        }
        if (isset($data['discovery'])) {
            $this->discovery = !empty($data['discovery']);
        }
        if (isset($data['formats'])) {
            $this->formats = $data['formats'];
        }
    }

    /**
     * Magic method for getting properties.
     * @param string $name
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($name) {
        $allowed = ['schemes', 'url', 'discovery', 'formats'];
        if (in_array($name, $allowed)) {
            return $this->$name;
        } else {
            throw new \coding_exception($name.' is not a publicly accessible property of '.get_class($this));
        }
    }
}