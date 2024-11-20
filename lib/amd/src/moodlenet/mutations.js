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
 * MoodleNet mutations.
 * An instance of this class will be used to add custom mutations to the course editor.
 *
 * @module     core/moodlenet/mutations
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.3
 */

import DefaultMutations from 'core_courseformat/local/courseeditor/mutations';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import CourseActions from 'core_courseformat/local/content/actions';
import {subscribe} from 'core/pubsub';
import {handleModal} from 'core/moodlenet/send_resource';
import MoodleNetEvents from 'core/moodlenet/events';

class MoodleNetMutations extends DefaultMutations {

    /**
     * Share to MoodleNet.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} cmIds Course module ids.
     */
    shareToMoodleNet = async function(stateManager, cmIds) {
        if (cmIds.length == 0) {
            return;
        }
        this.cmLock(stateManager, cmIds, true);
        handleModal('partial', cmIds);
        this.cmLock(stateManager, cmIds, false);
        subscribe(MoodleNetEvents.MOODLENET_SHARE_STARTED, () => {
            // Only clear the selection if the user starts the sharing.
            this.bulkReset(stateManager);
        });
    };
}

/**
 * Initialize.
 */
export const init = () => {
    const courseEditor = getCurrentCourseEditor();
    courseEditor.addMutations(new MoodleNetMutations());
    // Add direct mutation content actions.
    CourseActions.addActions({
        cmShareToMoodleNet: 'shareToMoodleNet'
    });
};
