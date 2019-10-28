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
 * This module will tie together all of the different calls the gradable module will make.
 *
 * @module     mod_forum/grades/grader
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import * as Selectors from './grader/selectors';
import Repository from 'mod_forum/repository';
import Templates from 'core/templates';
import * as Grader from '../local/grades/grader';
import Notification from 'core/notification';
import CourseRepository from 'core_course/repository';

const templateNames = {
    contentRegion: 'mod_forum/grades/grader/discussion/posts',
};

/**
 * Curried function with CMID set, this is then used in unified grader as a fetch a users content.
 *
 * @param {Number} cmid
 * @return {Function}
 */
const getContentForUserIdFunction = (cmid) => (userid) => {
    /**
     * Given the parent function is called with the second param set execute the partially executed function.
     *
     * @param {Number} userid
     */
    return Repository.getDiscussionByUserID(userid, cmid)
        .then(context => {
            // Rebuild the returned data for the template.
            context.discussions = context.discussions.map(discussionPostMapper);

            return Templates.render(templateNames.contentRegion, context);
        })
        .catch(Notification.exception);
};

/**
 * Curried function with CMID set, this is then used in unified grader as a fetch users call.
 * The function curried fetches all users in a course for a given CMID.
 *
 * @param {Number} cmid
 * @return {Array} Array of users for a given context.
 */
const getUsersForCmidFunction = (cmid) => async() => {
    const context = await CourseRepository.getUsersFromCourseModuleID(cmid);

    return context.users;
};


const findGradableNode = node => node.closest(Selectors.gradableItem);

/**
 * For a discussion we need to manipulate it's posts to hide certain UI elements.
 *
 * @param {Object} discussion
 * @return {Array} name, id, posts
 */
const discussionPostMapper = (discussion) => {
    // Map postid => post.
    const parentMap = new Map();
    discussion.posts.parentposts.forEach(post => parentMap.set(post.id, post));
    const userPosts = discussion.posts.userposts.map(post => {
        post.subject = null;
        post.readonly = true;
        post.starter = !post.parentid;
        post.parent = parentMap.get(post.parentid);
        post.html.rating = null;

        return post;
    });

    return {
        id: discussion.id,
        name: discussion.name,
        posts: userPosts,
    };
};

/**
 * Launch the Grader.
 *
 * @param {HTMLElement} rootNode the root HTML element describing what is to be graded
 */
const launchWholeForumGrading = async(rootNode) => {
    const data = rootNode.dataset;
    const gradingPanelFunctions = await Grader.getGradingPanelFunctions(
        'mod_forum',
        data.contextid,
        data.gradingComponent,
        data.gradingComponentSubtype,
        data.gradableItemtype
    );

    await Grader.launch(
        getUsersForCmidFunction(data.cmid),
        getContentForUserIdFunction(data.cmid),
        gradingPanelFunctions.getter,
        gradingPanelFunctions.setter,
        {
            groupid: data.groupid,
            initialUserId: data.initialuserid,
            moduleName: data.name
        }
    );
};

/**
 * Register listeners to launch the grading panel.
 */
export const registerLaunchListeners = () => {
    document.addEventListener('click', async(e) => {
        if (e.target.matches(Selectors.launch)) {
            const rootNode = findGradableNode(e.target);

            if (!rootNode) {
                throw Error('Unable to find a gradable item');
            }

            if (rootNode.matches(Selectors.gradableItems.wholeForum)) {
                // Note: The preventDefault must be before any async function calls because the function becomes async
                // at that point and the default action is implemented.
                e.preventDefault();
                try {
                    await launchWholeForumGrading(rootNode);
                } catch (error) {
                    Notification.exception(error);
                }
            } else {
                throw Error('Unable to find a valid gradable item');
            }
        }
    });
};
