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
 * Module for viewing a discussion.
 *
 * @module     mod_forum/discussion_new
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import Discussion from 'mod_forum/discussion';
import LockToggle from 'mod_forum/lock_toggle';
import FavouriteToggle from 'mod_forum/favourite_toggle';
import Pin from 'mod_forum/pin_toggle';

export const init = (root) => {
    Discussion.init(root);

    var discussionToolsContainer = $('[data-container="discussion-tools"]');
    LockToggle.init(discussionToolsContainer);
    FavouriteToggle.init(discussionToolsContainer);
    Pin.init(discussionToolsContainer);
};
