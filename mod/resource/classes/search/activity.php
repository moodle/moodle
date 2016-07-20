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
 * Search area for mod_resource activities.
 *
 * @package    mod_resource
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_resource\search;

defined('MOODLE_INTERNAL') || die();

/**
 * Search area for mod_resource activities.
 *
 * @package    mod_resource
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity extends \core_search\base_activity {
    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Add the main file to the index.
     *
     * @param document $document The current document
     * @return null
     */
    public function attach_files($document) {
        $fs = get_file_storage();

        $cm = $this->get_cm($this->get_module_name(), $document->get('itemid'), $document->get('courseid'));
        $context = \context_module::instance($cm->id);

        // Order by sortorder desc, the first is consided the main file.
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);

        $mainfile = $files ? reset($files) : null;
        if ($mainfile && $mainfile->get_sortorder() > 0) {
            $document->add_stored_file($mainfile);
        }
    }

}
