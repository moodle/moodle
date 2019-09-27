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

const getWholeForumFunctions = (cmid) => {
    const getPostContextFunction = () => {
        return (userid) => {
            return Repository.getDiscussionByUserID(userid, cmid);
        };
    };

    const getContentForUserIdFunction = () => {
        const postContextFunction = getPostContextFunction(cmid);
        return userid => {
            return postContextFunction(userid)
            .then(context => {
                // Rebuild the returned data for the template.
                context.discussions = context.discussions.map(discussionPostMapper);

                return Templates.render(templateNames.contentRegion, context);
            })
            .catch(Notification.exception);
        };
    };

    const getUsersForCmidFunction = () => {
        return () => {
            return CourseRepository.getUsersFromCourseModuleID(cmid)
                .then((context) => {
                    return context.users;
                })
                .catch(Notification.exception);
        };
    };

    return {
        getContentForUserId: getContentForUserIdFunction(),
        getUsers: getUsersForCmidFunction()
    };
};

const findGradableNode = (node) => {
    return node.closest(Selectors.gradableItem);
};

const discussionPostMapper = discussion => {
    // Map postid => post.
    const parentMap = new Map();
    discussion.posts.parentposts.forEach(post => parentMap.set(post.id, post));

    const userPosts = discussion.posts.userposts.map(post => {
        post.subject = null;
        post.readonly = true;
        post.starter = !post.parentid;
        post.parent = parentMap.get(post.parentid);

        return post;
    });

    return {
        id: discussion.id,
        name: discussion.name,
        posts: userPosts,
    };
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

                const data = rootNode.dataset;
                const wholeForumFunctions = getWholeForumFunctions(data.cmid);
                const gradingPanelFunctions = await Grader.getGradingPanelFunctions(
                    'mod_forum',
                    data.contextid,
                    data.gradingComponent,
                    data.gradingComponentSubtype,
                    data.gradableItemtype
                );

                Grader.launch(
                    wholeForumFunctions.getUsers,
                    wholeForumFunctions.getContentForUserId,
                    gradingPanelFunctions.getter,
                    gradingPanelFunctions.setter,
                    {
                        groupid: data.groupid,
                        initialUserId: data.initialuserid,
                        moduleName: data.name
                    }
                );
            } else {
                throw Error('Unable to find a valid gradable item');
            }
        }
    });
};
