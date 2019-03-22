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
 * Author data mapper.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\data_mappers\legacy;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\author as author_entity;
use stdClass;

/**
 * Convert an author entity into an stdClass.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class author {
    /**
     * Convert a list of author entities into stdClasses.
     *
     * @param author_entity[] $authors The authors to convert.
     * @return stdClass[]
     */
    public function to_legacy_objects(array $authors) : array {
        return array_map(function(author_entity $author) {
            return (object) [
                'id' => $author->get_id(),
                'picture' => $author->get_picture_item_id(),
                'firstname' => $author->get_first_name(),
                'lastname' => $author->get_last_name(),
                'fullname' => $author->get_full_name(),
                'email' => $author->get_email(),
                'middlename' => $author->get_middle_name(),
                'firstnamephonetic' => $author->get_first_name_phonetic(),
                'lastnamephonetic' => $author->get_last_name_phonetic(),
                'alternatename' => $author->get_alternate_name(),
                'imagealt' => $author->get_image_alt()
            ];
        }, $authors);
    }

    /**
     * Convert an author entity into an stdClass.
     *
     * @param author_entity $author The author to convert.
     * @return stdClass
     */
    public function to_legacy_object(author_entity $author) : stdClass {
        return $this->to_legacy_objects([$author])[0];
    }
}
