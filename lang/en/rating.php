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
 * @package   rating
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['aggregatetype'] = 'Aggregate type';
$string['aggregatetype_help'] = 'Forum aggregation defines how all the ratings given to posts in a forum are combined to form the final grade (for each post and for the whole forum activity).

There are 5 aggregate types:

* Average - The mean of all the ratings given to posts in the forum (useful with peer grading when there are a lot of ratings being made)
* Count - The number of rated posts becomes the final grade (useful when the number of posts is important). Note that the total cannot exceed the maximum grade for the forum.
* Max - The highest rating is returned as the final grade (useful for emphasising the best post)
* Min - The smallest rating is returned as the final grade (for promoting a culture of high quality for all posts)
* Sum - All ratings for a particular student are added together. Note that the total cannot exceed the maximum grade for the forum.

If "No ratings" is selected, then the forum activity will not appear in the gradebook.';
$string['allowratings'] = 'Allow items to be rated?';
$string['norate'] = 'Rating of items not allowed!';
$string['noviewanyrate'] = 'You can only look at results for posts that you made';
$string['noviewrate'] = 'You do not have the capability to view post ratings';
$string['rate'] = 'Rate';
$string['ratepermissiondenied'] = 'You do not have permission to rate this item';
$string['rating'] = 'Rating';
$string['ratingtime'] = 'Restrict ratings to items with dates in this range:';
$string['ratings'] = 'Ratings';
$string['rolewarning'] = 'Roles with permission to rate';
$string['rolewarning_help'] = 'Aggregated ratings appear in the gradebook. Click on Permissions under module administration to change who can submit ratings.';
