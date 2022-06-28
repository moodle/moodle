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
 * Ajax functions for moodleoverflow
 *
 * @module     mod/moodleoverflow
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/config', 'core/url', 'core/str'],
    function($, ajax, templates, notification, Cfg, Url, str) {

    var RATING_SOLVED = 3;
    var RATING_REMOVE_SOLVED = 30;
    var RATING_HELPFUL = 4;
    var RATING_REMOVE_HELPFUL = 40;

    var t = {

        /**
         * Reoords a upvote / downvote.
         * @param {int} discussionid
         * @param {int} ratingid
         * @param {int} userid
         * @param {event} event
         * @returns {string}
         */
        recordvote: function(discussionid, ratingid, userid, event) {
            var target = $(event.target).closest('.moodleoverflowpost').prev();
            var postid = target.attr('id');
            postid = postid.substring(1);

            var vote = ajax.call([{
                methodname: 'mod_moodleoverflow_record_vote',
                args: {
                    discussionid: discussionid,
                    postid: postid,
                    ratingid: ratingid,
                    sesskey: Cfg.sesskey
                }
            }
            ]);

            vote[0].done(function(response) {

                var parentdiv = $(event.target).parent().parent();
                // Update Votes.
                if (ratingid === 2) {
                    parentdiv.children('a:first-of-type').children().attr(
                        'src', Url.imageUrl('vote/upvoted', 'moodleoverflow'));
                    parentdiv.children('a:nth-of-type(2)').children().attr(
                        'src', Url.imageUrl('vote/downvote', 'moodleoverflow'));
                } else if (ratingid === 1) {
                    parentdiv.children('a:first-of-type').children().attr(
                        'src', Url.imageUrl('vote/upvote', 'moodleoverflow'));
                    parentdiv.children('a:nth-of-type(2)').children().attr(
                        'src', Url.imageUrl('vote/downvoted', 'moodleoverflow'));
                } else {
                    parentdiv.children('a:first-of-type').children().attr(
                        'src', Url.imageUrl('vote/upvote', 'moodleoverflow'));
                    parentdiv.children('a:nth-of-type(2)').children().attr(
                        'src', Url.imageUrl('vote/downvote', 'moodleoverflow'));
                }

                parentdiv.children('p').text(response.postrating);

                // Update user reputation.
                templates.replaceNode($('.user-details,.author').find('a[href*="id=' + userid + '"]')
                    .siblings('span'), '<span>' + response.raterreputation + '</span>', "");
                if (response.ownerid && userid !== response.ownerid) {
                    templates.replaceNode($('.user-details,.author').find('a[href*="id=' + response.ownerid + '"]')
                        .siblings('span'), '<span>' + response.ownerreputation + '</span>', "");
                }
            }).fail(notification.exception);

            return vote;
        },

        /**
         * Initializes the clickevent on upvotes / downvotes.
         * @param {int} discussionid
         * @param {int} userid
         */
        clickevent: function(discussionid, userid) {
            $(".upvote").on("click", function(event) {
                if ($(event.target).is('a')) {
                    event.target = $(event.target).children();
                }

                if ($(event.target).parent().attr('class').indexOf('active') >= 0) {
                    t.recordvote(discussionid, 20, userid, event);
                } else {
                    t.recordvote(discussionid, 2, userid, event);
                }
                $(event.target).parent().toggleClass('active');
                $(event.target).parent().nextAll('a').removeClass('active');
            });

            $(".downvote").on("click", function(event) {
                if ($(event.target).is('a')) {
                    event.target = $(event.target).children();
                }

                if ($(event.target).parent().attr('class').indexOf('active') >= 0) {
                    t.recordvote(discussionid, 10, userid, event);
                } else {
                    t.recordvote(discussionid, 1, userid, event);
                }
                $(event.target).parent().toggleClass('active');
                $(event.target).parent().prevAll('a').removeClass('active');
            });

            $(".marksolved").on("click", function(event) {
                var post = $(event.target).parents('.moodleoverflowpost');

                if (post.hasClass('statusteacher') || post.hasClass('statusboth')) {
                    // Remove solution mark.
                    t.recordvote(discussionid, RATING_REMOVE_SOLVED, userid, event)[0].then(function() {
                        t.removeSolvedFromPost(post);
                    });
                } else {
                    // Add solution mark.
                    t.recordvote(discussionid, RATING_SOLVED, userid, event)[0].then(function() {
                        // Remove other solution mark in dom.
                        t.removeOtherSolved(post.parent().parent());
                        if (post.hasClass('statusstarter')) {
                            post.removeClass('statusstarter');
                            post.addClass('statusboth');
                        } else {
                            post.addClass('statusteacher');
                        }

                        var promiseStringNotSolved = str.get_string('marknotsolved', 'mod_moodleoverflow');
                        $.when(promiseStringNotSolved).done(function(string) {
                            $(event.target).text(string);
                        });
                        t.redoStatus(post);
                    });
                }


            });

            $(".markhelpful").on("click", function(event) {
                var post = $(event.target).parents('.moodleoverflowpost');

                if (post.hasClass('statusstarter') || post.hasClass('statusboth')) {
                    // Remove helpful mark.
                    t.recordvote(discussionid, RATING_REMOVE_HELPFUL, userid, event)[0].then(function() {
                        t.removeHelpfulFromPost(post);
                    });
                } else {
                    // Add helpful mark.
                    t.recordvote(discussionid, RATING_HELPFUL, userid, event)[0].then(function() {
                        // Remove other helpful mark in dom.
                        t.removeOtherHelpful(post.parent().parent());
                        if (post.hasClass('statusteacher')) {
                            post.removeClass('statusteacher');
                            post.addClass('statusboth');
                        } else {
                            post.addClass('statusstarter');
                        }

                        var promiseStringNotHelpful = str.get_string('marknothelpful', 'mod_moodleoverflow');
                        $.when(promiseStringNotHelpful).done(function(string) {
                            $(event.target).text(string);
                        });
                        t.redoStatus(post);
                    });
                }

            });
        },

        removeHelpfulFromPost: function (post) {
            if (post.hasClass('statusstarter')) {
                post.removeClass('statusstarter');
            } else {
                post.removeClass('statusboth');
                post.addClass('statusteacher');
            }

            t.redoStatus(post);

            var promiseHelpful = str.get_string('markhelpful', 'mod_moodleoverflow');
            $.when(promiseHelpful).done(function (string) {
                post.find('.markhelpful').text(string);
            });
        },

        removeOtherHelpful: function(root) {
            var formerhelpful = root.find('.statusstarter, .statusboth');
            if (formerhelpful.length > 0) {
                t.removeHelpfulFromPost(formerhelpful);
            }
        },

        removeSolvedFromPost: function(post) {
            if (post.hasClass('statusteacher')) {
                post.removeClass('statusteacher');
            } else {
                post.removeClass('statusboth');
                post.addClass('statusstarter');
            }

            t.redoStatus(post);

            var promiseHelpful = str.get_string('marksolved', 'mod_moodleoverflow');
            $.when(promiseHelpful).done(function(string) {
                post.find('.marksolved').text(string);
            });
        },

        removeOtherSolved: function(root) {
            var formersolution = root.find('.statusteacher, .statusboth');
            if (formersolution.length > 0) {
                t.removeSolvedFromPost(formersolution);
            }
        },

        /**
         * Redoes the post status
         * @param {object} post dom with .moodleoverflowpost which status should be redone
         */
        redoStatus: function(post) {
            if ($(post).hasClass('statusboth')) {
                var statusBothRequest = [
                    {key: 'teacherrating', component: 'mod_moodleoverflow'},
                    {key: 'starterrating', component: 'mod_moodleoverflow'},
                    {key: 'bestanswer', component: 'mod_moodleoverflow'}
                ];
                str.get_strings(statusBothRequest).then(function(results) {
                    var circle = templates.renderPix('status/c_circle', 'mod_moodleoverflow', results[0]);
                    var box = templates.renderPix('status/b_box', 'mod_moodleoverflow', results[1]);
                    $.when(box, circle).done(function(boxImg, circleImg) {
                        if (screen.width > 600) {
                            post.find('.status').html(boxImg + circleImg + results[2]);
                        } else {
                            post.find('.status').html(boxImg + circleImg);
                        }
                    });
                    return results;
                });
            } else if ($(post).hasClass('statusteacher')) {
                var statusTeacherRequest = [
                    {key: 'teacherrating', component: 'mod_moodleoverflow'},
                    {key: 'solvedanswer', component: 'mod_moodleoverflow'}
                ];
                str.get_strings(statusTeacherRequest).then(function(results) {
                    var circle = templates.renderPix('status/c_outline', 'mod_moodleoverflow', results[0]);
                    $.when(circle).done(function(circleImg) {
                        if (screen.width > 600) {
                            post.find('.status').html(circleImg + results[1]);
                        } else {
                            post.find('.status').html(circleImg);
                        }

                    });
                    return results;
                });
            } else if ($(post).hasClass('statusstarter')) {
                var statusStarterRequest = [
                    {key: 'starterrating', component: 'mod_moodleoverflow'},
                    {key: 'helpfulanswer', component: 'mod_moodleoverflow'}
                ];
                str.get_strings(statusStarterRequest).then(function(results) {
                    var box = templates.renderPix('status/b_outline', 'mod_moodleoverflow', results[0]);
                    $.when(box).done(function(boxImg) {
                        if (screen.width > 600) {
                            post.find('.status').html(boxImg + results[1]);
                        } else {
                            post.find('.status').html(boxImg);
                        }
                    });
                    return results;
                });
            } else {
                post.find('.status').html('');
            }

        }
    };

    return t;
});
