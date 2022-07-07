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
 * Post attachment vault class.
 *
 * @package    mod_forum
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\post as post_entity;
use context;
use file_storage;

/**
 * Post attachment vault class.
 *
 * This should be the only place that accessed the database.
 *
 * This uses the repository pattern. See:
 * https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_attachment {
    /** The component for attachments */
    private const COMPONENT = 'mod_forum';
    /** File area for attachments */
    private const FILE_AREA = 'attachment';
    /** Sort the attachments by filename */
    private const SORT = 'filename';
    /** Don't include directories */
    private const INCLUDE_DIRECTORIES = false;
    /** @var file_storage $filestorage File storage */
    private $filestorage;

    /**
     * Construct.
     *
     * @param file_storage $filestorage File storage
     */
    public function __construct(file_storage $filestorage) {
        $this->filestorage = $filestorage;
    }

    /**
     * Get the attachments for the given posts. The results are indexed by
     * post id.
     *
     * @param context $context The (forum) context that the posts are in
     * @param post_entity[] $posts The list of posts to load attachments for
     * @return array Post attachments indexed by post id
     */
    public function get_attachments_for_posts(context $context, array $posts) {
        $itemids = array_map(function($post) {
            return $post->get_id();
        }, $posts);

        $files = $this->filestorage->get_area_files(
            $context->id,
            self::COMPONENT,
            self::FILE_AREA,
            $itemids,
            self::SORT,
            self::INCLUDE_DIRECTORIES
        );

        $filesbyid = array_reduce($posts, function($carry, $post) {
            $carry[$post->get_id()] = [];
            return $carry;
        }, []);

        return array_reduce($files, function($carry, $file) {
            $itemid = $file->get_itemid();
            $carry[$itemid] = array_merge($carry[$itemid], [$file]);
            return $carry;
        }, $filesbyid);
    }
}
