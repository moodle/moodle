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
 * File server.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\file;

use context;
use file_storage;

/**
 * File server.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_server implements block_file_server {

    /** @var file_storage File storage. */
    protected $fs;
    /** @var bool For whole site? */
    protected $forwholesite = false;

    /**
     * Constructor.
     *
     * @param file_storage $fs File storage.
     * @param int $contextmode The context mode.
     */
    public function __construct(file_storage $fs, $contextmode) {
        $this->fs = $fs;
        if ($contextmode == CONTEXT_SYSTEM) {
            $this->forwholesite = true;
        }
    }

    /**
     * Serve a block file.
     *
     * @param stdClass $course The course object.
     * @param stdClass $bi Block instance record.
     * @param context $context The context object.
     * @param string $filearea The file area.
     * @param array $args List of arguments.
     * @param bool $forcedownload Whether or not to force the download of the file.
     * @param array $options Array of options.
     * @return void
     */
    public function serve_block_file($course, $bi, context $context, $filearea, $args, $forcedownload, array $options = []) {
        // Check context. We used to check the 'badges' context matched the admin context setting, but this was
        // an overkill and could hide badges when viewing a system context while the plugin is set per course,
        // and vice-versa. The latter should rarely happen but its behaviour was unexpected, and the context
        // check itself did not provide any security or value on its own.
        if ($filearea == 'defaultbadges') {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                return false;
            }
        }

        $fs = $this->fs;
        $file = null;

        if ($filearea == 'badges' || $filearea == 'defaultbadges') {
            // For performance reason, and very low risk, we do not restrict the access to the level badges
            // to the participant of the course, nor do we check if they have the required level, etc... And
            // we allow files to be served from 'defaultbadges' to avoid having to copy them to the other area.
            $itemid = array_shift($args);
            $filename = array_shift($args);
            $filepath = '/';

            // Check we have an expected file name, we do not want to leak other files in the file area.
            if (!preg_match('~^(\d+)\.[a-z]+$~i', $filename)) {
                return false;
            }
            $file = $fs->get_file($context->id, 'block_xp', $filearea, $itemid, $filepath, $filename);

            // Make sure this is an image.
            if (!$file || strpos($file->get_mimetype(), 'image/') !== 0) {
                return false;
            }
        }

        if (!$file) {
            return false;
        }

        send_stored_file($file);
    }

}
