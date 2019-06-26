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
 * Strings for component 'rating', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   core_rating
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['aggregatetype'] = 'Aggregate type';
$string['aggregateavg'] = 'Average of ratings';
$string['aggregatecount'] = 'Count of ratings';
$string['aggregatemax'] = 'Maximum rating';
$string['aggregatemin'] = 'Minimum rating';
$string['aggregatenone'] = 'No ratings';
$string['aggregatesum'] = 'Sum of ratings';
$string['aggregatetype_help'] = 'The aggregate type defines how ratings are combined to form the final grade in the gradebook.

* Average of ratings - The mean of all ratings
* Count of ratings - The number of rated items becomes the final grade. Note that the total cannot exceed the maximum grade for the activity.
* Maximum - The highest rating becomes the final grade
* Minimum - The smallest rating becomes the final grade
* Sum - All ratings are added together. Note that the total cannot exceed the maximum grade for the activity.

If "No ratings" is selected, then the activity will not appear in the gradebook.';
$string['allowratings'] = 'Allow items to be rated?';
$string['allratingsforitem'] = 'All submitted ratings';
$string['capabilitychecknotavailable'] = 'Capability check not available until activity is saved';
$string['couldnotdeleteratings'] = 'Sorry, that cannot be deleted as people have already rated it';
$string['norate'] = 'Rating of items not allowed!';
$string['noratings'] = 'No ratings submitted';
$string['noviewanyrate'] = 'You can only look at results for items that you made';
$string['noviewrate'] = 'You do not have the capability to view item ratings';
$string['rate'] = 'Rate';
$string['ratepermissiondenied'] = 'You do not have permission to rate this item';
$string['rating'] = 'Rating';
$string['ratinginvalid'] = 'Rating is invalid';
$string['ratingtime'] = 'Restrict ratings to items with dates in this range:';
$string['ratings'] = 'Ratings';
$string['rolewarning'] = 'Roles with permission to rate';
$string['rolewarning_help'] = 'To submit ratings users require the moodle/rating:rate capability and any module specific capabilities. Users assigned the following roles should be able to rate items. The list of roles may be amended via the permissions link in the actions menu or administration block, depending on the theme.';
$string['scaleselectionrequired'] = 'When selecting a ratings aggregate type you must also select to use either a scale or set a maximum points.';
$string['privacy:metadata:rating'] = 'The user-entered rating is stored alongside a mapping of the item which was rated.';
$string['privacy:metadata:rating:userid'] = 'The user who made the rating.';
$string['privacy:metadata:rating:rating'] = 'The numeric rating that the user entered.';
$string['privacy:metadata:rating:timecreated'] = 'The time that the rating was first made.';
$string['privacy:metadata:rating:timemodified'] = 'The time that the rating was last updated.';
