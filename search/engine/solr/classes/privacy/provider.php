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
 * Privacy class for requesting user data.
 *
 * @package    search_solr
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace search_solr\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;

/**
 * Provider for the search_solr plugin.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This search engine plugin does not store any data itself.
    // It has no database tables, and it purely acts as a conduit, sending data externally.
    \core_privacy\local\metadata\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        return $collection->add_external_location_link('solr', ['data' => 'privacy:metadata:data'],
                                                       'privacy:metadata');
    }
}
