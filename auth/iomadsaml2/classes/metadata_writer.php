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
 * Utility class for writing IDP metadata.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_iomadsaml2;

/**
 * Utility class for writing IDP metadata.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadata_writer {
    /**
     * @var string
     */
    private $certpath;

    /**
     * metadata_writer constructor.
     * @param string $path
     */
    public function __construct($path = '') {
        global $CFG;

        if (empty($path) || strpos($path, $CFG->wwwroot) !== 0) {
            $path = $CFG->dataroot . '/iomadsaml2/';
        }
        $this->certpath = $path;
    }

    /**
     * Write IDP metadata.
     *
     * @param string $filename
     * @param string $content
     * @throws \coding_exception
     */
    public function write($filename, $content) {
        if (empty($filename)) {
            throw new \coding_exception('File name must not be empty');
        }
        if (substr($this->certpath, -1) != '/') {
            $this->certpath = $this->certpath . '/';
        }
        if (!file_exists($this->certpath)) {
            make_writable_directory($this->certpath);
        }
        $result = file_put_contents($this->certpath . $filename , $content);
        if ($result === false) {
            throw new \coding_exception('Could not write to file ' . $filename);
        }
    }
}
