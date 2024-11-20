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

namespace qbank_managecategories\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem for qbank_managecategories implementing null_provider.
 *
 * @package   qbank_managecategories
 * @category  privacy
 * @copyright 2021 Catalyst IT Australia Pty Ltd
 * @author    Guillermo Gomez Arias <guillermogomez@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('qbank_managecategories_showdescriptions', 'privacy:preference:showdescriptions');
        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $showdescription = get_user_preferences('qbank_managecategories_showdescriptions', null, $userid);
        if ($showdescription !== null) {
            $displaydescription = $showdescription ? 'displaydescription' : 'descriptionnotdisplayed';
            writer::export_user_preference(
                'qbank_managecategories',
                'showdescr',
                $showdescription,
                get_string($displaydescription, 'qbank_managecategories')
            );
        }
    }
}
