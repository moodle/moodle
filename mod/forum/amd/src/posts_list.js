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
 * This module is the highest level module for the calendar. It is
 * responsible for initialising all of the components required for
 * the calendar to run. It also coordinates the interaction between
 * components by listening for and responding to different events
 * triggered within the calendar UI.
 *
 * @module     mod_forum/posts_list
 * @copyright  2019 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/templates',
    'core/notification',
    'core/pending',
    'mod_forum/selectors',
    'mod_forum/inpage_reply',
    'core_form/changechecker',
], function(
    $,
    Templates,
    Notification,
    Pending,
    Selectors,
    InPageReply,
    FormChangeChecker
) {

    var registerEventListeners = function(root, throttlingwarningmsg) {
        root.on('click', Selectors.post.inpageReplyLink, function(e) {
            e.preventDefault();
            // After adding a reply a url hash is being generated that scrolls (points) to the newly added reply.
            // The hash being present causes this scrolling behavior to the particular reply to persists even when
            // another, non-related in-page replay link is being clicked which ultimately causes a bad user experience.
            // A particular solution for this problem would be changing the browser's history state when a url hash is
            // present.
            if (window.location.hash) {
                // Remove the fragment identifier from the url.
                var url = window.location.href.split('#')[0];
                history.pushState({}, document.title, url);
            }
            var pending = new Pending('inpage-reply');
            var currentTarget = $(e.currentTarget).parents(Selectors.post.forumCoreContent);
            var currentSubject = currentTarget.find(Selectors.post.forumSubject);
            var currentRoot = $(e.currentTarget).parents(Selectors.post.forumContent);
            var context = {
                postid: $(currentRoot).data('post-id'),
                "reply_url": $(e.currentTarget).attr('href'),
                sesskey: M.cfg.sesskey,
                parentsubject: currentSubject.data('replySubject'),
                canreplyprivately: $(e.currentTarget).data('can-reply-privately'),
                postformat: InPageReply.CONTENT_FORMATS.MOODLE,
                throttlingwarningmsg: throttlingwarningmsg
            };

            if (!currentRoot.find(Selectors.post.inpageReplyContent).length) {
                Templates.render('mod_forum/inpage_reply', context)
                    .then(function(html, js) {
                        return Templates.appendNodeContents(currentTarget, html, js);
                    })
                    .then(function() {
                        return currentRoot.find(Selectors.post.inpageReplyContent)
                            .slideToggle(300, pending.resolve).find('textarea').focus();
                    })
                    .then(function() {
                        FormChangeChecker.watchFormById(`inpage-reply-${context.postid}`);
                        return;
                    })
                    .catch(Notification.exception);
            } else {
                var form = currentRoot.find(Selectors.post.inpageReplyContent);
                form.slideToggle(300, pending.resolve);
                if (form.is(':visible')) {
                    form.find('textarea').focus();
                }
            }
        });
    };

    return {
        init: function(root, throttlingwarningmsg) {
            registerEventListeners(root, throttlingwarningmsg);
            InPageReply.init(root);
        }
    };
});
