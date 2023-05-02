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
 * @package   filter_oembed
 * @copyright Guy Thomas / moodlerooms.com 2016
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Oembed preloader.
 */
define(['jquery'],
    function($) {
        return {
            apply: function() {
                $(".oembed-card-play").on("click", function() {
                    var card = $(this).parent('.oembed-card');
                    var data = $(card.data('embed'));
                    var cardwidth = $(card).width();
                    var cardheight = $(card).height();

                    // Add auto play params.
                    // Because we are using a preloader we ideally want the content to play after clicking the preloader
                    // play button.
                    if ($(data).find('iframe').length) {
                        var iframe = $($(data).find('iframe')[0]);
                        var src = iframe.attr('src');
                        var paramglue = src.indexOf('?') > -1 ? '&' : '?';
                        src += paramglue + 'autoplay=1';
                        src += '&' + 'auto_play=1';
                        iframe.attr('src', src);
                    }

                    // Replace card with oembed html.
                    data.attr('data-card-width', cardwidth);
                    data.attr('data-card-height', cardheight);
                    card.parent('.oembed-card-container').replaceWith(data);
                });
            }
        };
    }
);
