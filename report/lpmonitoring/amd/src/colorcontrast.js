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
 * Color contrast.
 *
 * This script analyzes the elements of a selector and adds a class
 * for those whose background color is considered too dark.
 * The goal of applying lighter text color for these elements
 * to improve contrast.
 *
 * @module     report_lpmonitoring/colorcontrast
 * @author     Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @copyright  2016 Université de Montréal
 */

define(['jquery'],
    function($) {

        /**
         * Color contrast object.
         * @param {String} colorContrastSelector The selector of elements whose background color will be analyzed.
         * @param {String} lightColorClassName The name of the class that will be added to the elements considered too dark.
         */
        var ColorContrast = function(colorContrastSelector, lightColorClassName) {
            var self = this;

            if (colorContrastSelector) {
                self.colorContrastSelector = colorContrastSelector;
            }

            if (lightColorClassName) {
                self.lightColorClassName = lightColorClassName;
            }

            self.apply();
        };

        /** @var {String} The color contrast selector. */
        ColorContrast.prototype.colorContrastSelector = '.competencyreport .color-contrast';

        /** @var {String} The light color class name. */
        ColorContrast.prototype.lightColorClassName = 'light-color';

        /**
         * Calculate luminance ratio.
         *
         * @name   luminance
         * @param {Number} r red
         * @param {Number} g green
         * @param {Number} b blue
         * @return {Number}
         * @function
         */
        ColorContrast.prototype.luminance = function (r, g, b) {
            var a = [r,g,b].map(function(v) {
                v /= 255;
                return (v <= 0.03928) ? v / 12.92 : Math.pow( ((v + 0.055) / 1.055), 2.4 );
            });
            return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
        };

        /**
         * Apply the color contrast to desired selector.
         *
         * @name   apply
         * @param {String} colorContrastSelector
         * @return {Void}
         * @function
         */
        ColorContrast.prototype.apply = function(colorContrastSelector) {
            var self = this;
            if (!colorContrastSelector) {
                colorContrastSelector = self.colorContrastSelector;
            }

            $(colorContrastSelector).each(function() {
                var bgc = $(this).css('background-color');

                // Handle 100% transparent background.
                if(bgc === 'transparent' || bgc === 'rgba(0, 0, 0, 0)') {
                    // Scan each parent's background color looking at a non-transparent background.
                    $(this).parents().each(function() {
                        bgc = $(this).css('background-color');
                        if (bgc !== 'transparent' && bgc !== 'rgba(0, 0, 0, 0)') {
                            return false;
                        }
                    });
                    // If all parents is transparent use default and go to next element.
                    if(bgc === 'transparent' || bgc === 'rgba(0, 0, 0, 0)') {
                        return true;
                    }
                }

                // Extract RGB and convert it into luminance from the YIQ equation from https://www.w3.org/TR/AERT#color-contrast.
                var rgb = bgc.replace(/^(rgb|rgba)\(/,'').replace(/\)$/,'').replace(/\s/g,'').split(',');
                var luminancebackground = self.luminance(rgb[0], rgb[1], rgb[2]);
                var luminanceforeground = self.luminance(0, 0, 0);
                var ratio = (luminancebackground + 0.05) / (luminanceforeground + 0.05);

                if(ratio >= 4.5) {
                    $(this).removeClass(self.lightColorClassName);
                } else {
                    $(this).addClass(self.lightColorClassName);
                }
            });
        };

        return {
            /**
             * Main initialisation.
             *
             * @param {String} colorContrastSelector The selector of elements whose background color will be analyzed.
             * @param {String} lightColorClassName The name of the class that will be added to the elements considered too dark.
             * @return {ColorContrast} A new instance of ColorContrast.
             * @method init
             */
            init: function(colorContrastSelector, lightColorClassName) {
                return new ColorContrast(colorContrastSelector, lightColorClassName);
            }

        };

    });
