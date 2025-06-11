/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/templates'],
    function($, templates) {
        var FooterAlert = function() {

            // Container.
            var containerEl;

            /**
             * Detach the container for footer alert, so it will not appear at the first load of the page,
             * this to fix AX problems with unnecessary empty tags.
             */
            $(document).ready(function() {
                containerEl.detach();
            });

            /**
             * Initialising function.
             */
            (function() {
                containerEl = $('#snap-footer-alert');

                // If the move notice html was not output to the dom via php, then we need to add it here via js.
                // This is necessary for the front page which does not have a renderer that we can override.
                if (containerEl.length === 0) {
                    templates.render('theme_snap/footer_alert', {})
                        .done(function(result) {
                            $('#region-main').append(result);
                            containerEl = $('#snap-footer-alert');
                        });
                }
            })();

            /**
             * Set title element html.
             * @param {string} titleHTML
             */
            this.setTitle = function(titleHTML) {
                $('.snap-footer-alert-title').html(titleHTML);
                this.setSrNotice('');
                    // Focus on container so that it get's red out for accessibility reasons.
                containerEl.focus();
            };

            /**
             * Set screen reader notice.
             * @param {string} srText
             */
            this.setSrNotice = function(srText) {
                containerEl.find('p.sr-only').html(srText);
            };

            /**
             * Add AJAX loading spinner.
             * @param {string} str
             */
            this.addAjaxLoading = function(str) {
                str = !str ? M.util.get_string('loading', 'theme_snap') : str;
                var titleEl = $('.snap-footer-alert-title');
                if (titleEl.find('.loadingstat').length === 0) {
                    titleEl.append('<span class="loadingstat spinner-three-quarters' +
                        '">' + str + '</span>');
                }
            };

            /**
             * Remove AJAX loading spinner.
             */
            this.removeAjaxLoading = function() {
                containerEl.find('.loadingstat').remove();
            };

            /**
             * Show footer alert.
             * @param {function} onCancel
             */
            this.show = function(onCancel) {
                // Re-attach Snap footer alert, so it appears when moving an activity or a section.
                containerEl.prependTo('section#region-main');
                containerEl.addClass('snap-footer-alert-visible');
                if (typeof (onCancel) === 'function') {
                    $('.snap-footer-alert-cancel').click(onCancel);
                    $('.snap-footer-alert-cancel').addClass('state-visible');
                } else {
                    $('.snap-footer-alert-cancel').removeClass('state-visible');
                }
            };

            /**
             * Hide footer alert.
             */
            this.hide = function() {
                containerEl.removeClass('snap-footer-alert-visible');
                $('.snap-footer-alert-cancel').removeClass('state-visible');
                // Detach Snap footer alert after cancel button (When hiding the alert).
                containerEl.detach();
            };

            /**
             * Hide footer alert and reset content.
             */
            this.hideAndReset = function() {
                this.removeAjaxLoading();
                this.setTitle('');
                this.setSrNotice('');
                this.hide();
            };
        };
        return new FooterAlert();
    }
);
