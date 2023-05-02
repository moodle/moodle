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
 * Main responsive content function.
 */
define(['jquery'], function($) {

    /**
     * Apply responsive video to non HTML5 video elements.
     */
    var ResponsiveContent = function() {

        /**
         * Apply to specific node / nodes or use selector.
         * @param {jQuery|null} nodes- jquery node / collection of nodes or null
         */
        this.apply = function(nodes) {
            if (!nodes){
                var selectortoprocess = '.oembed-content:not(.oembed-responsive) > *:not(video):first-child,';
                selectortoprocess += ' .oembed-card:not(.oembed-processed)';
                nodes = $(selectortoprocess);
            }
            // Apply aspect ratio to height for all nodes or single node.
            $(nodes).each(function() {

                var parent = $(this).parent();
                if (parent.hasClass('oembed-responsive')) {
                    // Already processed.
                    return;
                }

                var width,
                    height,
                    aspectratio;

                aspectratio = this.getAttribute('data-aspect-ratio');
                if (aspectratio === null || aspectratio === '0') { // Note, an empty attribute should evaluate to null.
                    // Calculate aspect ratio.
                    width = this.width || this.offsetWidth;
                    height = this.height || this.offsetHeight;

                    // If only the width or height contains percentages then we can't use it and will have to fall back
                    // on the card size OR offsets.
                    if (width.indexOf('%') > -1 && height.indexOf('%') == -1
                        || width.indexOf('%') == -1 && height.indexOf('%') > -1
                    ) {
                        if ($(this).parent().attr('data-card-width') && $(this).parent().attr('data-card-height')) {
                            width = $(this).parent().attr('data-card-width');
                            height = $(this).parent().attr('data-card-height');
                        } else {
                            width = this.offsetWidth;
                            height = this.offsetHeight;
                        }
                    }

                    width = parseInt(width);
                    height = parseInt(height);
                    aspectratio = height / width;
                    this.setAttribute('data-aspect-ratio', aspectratio);
                }

                var tagname = this.tagName.toLowerCase();
                if (tagname === 'iframe') {
                    // Remove attributes.
                    $(this).removeAttr('width');
                    $(this).removeAttr('height');
                }

                // Get width again.
                width = parseInt(this.offsetWidth);
                // Set width.
                var style = {width: '100%'};
                $(this).css(style);

                // Make sure parent has a padding element.
                if (!parent.find('.oembed-responsive-pad').length) {
                    var aspectPerc = aspectratio * 100;
                    var responsivePad = '<div class="oembed-responsive-pad" style="padding-top:' + aspectPerc + '%"></div>';
                    parent.append(responsivePad);
                }

                // Add responsive class to parent element.
                parent.addClass('oembed-responsive');
            });
        };

    };

    return new ResponsiveContent();
});
