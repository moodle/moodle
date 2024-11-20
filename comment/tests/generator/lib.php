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

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/comment/lib.php");

/**
 * Comment test generator
 *
 * @package     core_comment
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_comment_generator extends component_generator_base {

    /**
     * Create comment
     *
     * @param array|stdClass $record
     * @return comment
     */
    public function create_comment($record): comment {
        $record = (array) $record;

        $content = (string) ($record['content'] ?? '');
        unset($record['content']);

        $comment = new comment((object) $record);
        if ($content !== '') {
            $comment->add($content);
        }

        return $comment;
    }
}
