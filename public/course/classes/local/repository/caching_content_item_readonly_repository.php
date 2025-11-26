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
 * Contains class caching_content_item_repository, for fetching content_items, with additional caching.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\repository;

defined('MOODLE_INTERNAL') || die();

/**
 * The class caching_content_item_repository, for fetching content_items, with additional caching.
 *
 * This class decorates the content_item_repository and uses the supplied cache to store content items for user and course
 * combinations. The content items for subsequent calls are returned from the cache if present, else are retrieved from the wrapped
 * content_item_repository.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class caching_content_item_readonly_repository implements content_item_readonly_repository_interface {

    /** @var \cache $cachestore the cache to use. */
    private $cachestore;

    /** @var content_item_readonly_repository $contentitemrepository a content item repository. */
    private $contentitemrepository;

    /**
     * The caching_content_item_readonly_repository constructor.
     *
     * @param \cache $cachestore a cache to use.
     * @param content_item_readonly_repository $contentitemrepository the repository to use as a fallback, after a cache miss.
     */
    public function __construct(\cache $cachestore, content_item_readonly_repository $contentitemrepository) {
        $this->cachestore = $cachestore;
        $this->contentitemrepository = $contentitemrepository;
    }

    /**
     * Find all the content items for a given course and user.
     *
     * @param \stdClass $course The course to find content items for.
     * @param \stdClass $user the user to pass to plugins.
     * @return array the array of content items.
     */
    public function find_all_for_course(\stdClass $course, \stdClass $user): array {
        // Try to find this data in the cache first.
        $key = $user->id . '_' . $course->id;
        $contentitems = $this->cachestore->get($key);
        if ($contentitems !== false) {
            return $contentitems;
        }

        // If we can't find it there, we must get it from the slow data store, updating the cache in the process.
        $contentitems = $this->contentitemrepository->find_all_for_course($course, $user);
        $this->cachestore->set($key, $contentitems);
        return $contentitems;
    }

    /**
     * Find all the content items made available by core and plugins.
     *
     * @return array
     */
    public function find_all(): array {
        return $this->contentitemrepository->find_all();
    }
}
