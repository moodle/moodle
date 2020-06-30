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
 * Contain the logic for accessibility bar.
 *
 * @package    theme_moove
 * @copyright  2020 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax'], function(jQuery, Ajax) {

    window.jQuery = jQuery;

    var SELECTORS = {
        FONT_SIZE: '#fontsize_dec, #fontsize_reset, #fontsize_inc',
        SITE_COLOR: '#sitecolor_color1, #sitecolor_color2, #sitecolor_color3, #sitecolor_color4'
    };

    var fontsizeClass = null;
    var fontsizeClassOp = null;
    var fontsizeClassSize = null;
    var fontsizeCurrentAction = null;
    var sitecolorCurrentAction = null;

    var AccessibilityBar = function() {
        var classList = jQuery('body').attr('class').split(/\s+/);
        jQuery.each(classList, function(index, item) {
            if (item.includes('fontsize-inc-') || item.includes('fontsize-dec-')) {
                var itemarr = item.split('-');

                fontsizeClass = item;
                fontsizeClassOp = itemarr[1];
                fontsizeClassSize = itemarr[2];
            }
        });

        this.toggleFontsizeButtons();

        this.registerEventListeners();
    };

    AccessibilityBar.prototype.registerEventListeners = function() {
        jQuery(SELECTORS.FONT_SIZE).click(function(element) {
            var btn = jQuery(element.currentTarget);

            fontsizeCurrentAction = btn.data('action');

            this.fontSize();
        }.bind(this));

        jQuery(SELECTORS.SITE_COLOR).click(function(element) {
            var btn = jQuery(element.currentTarget);

            sitecolorCurrentAction = btn.data('action');

            this.siteColor();
        }.bind(this));
    };

    AccessibilityBar.prototype.fontSize = function() {
        var request = Ajax.call([{
            methodname: 'theme_moove_fontsize',
            args: {
                action: fontsizeCurrentAction
            }
        }]);

        request[0].done(function() {
            this.reloadFontsizeClass();
        }.bind(this));
    };

    AccessibilityBar.prototype.reloadFontsizeClass = function() {
        if (fontsizeCurrentAction === 'reset'
            || (fontsizeCurrentAction === 'increase' && fontsizeClass === 'fontsize-dec-1')
            || (fontsizeCurrentAction === 'decrease' && fontsizeClass === 'fontsize-inc-1')
        ) {
            jQuery('body').removeClass(fontsizeClass);
            fontsizeClass = null;
            fontsizeClassOp = null;
            fontsizeClassSize = null;

            this.toggleFontsizeButtons();

            return;
        }

        if (fontsizeCurrentAction === 'increase') {
            if (fontsizeClassSize === null) {
                fontsizeClass = 'fontsize-inc-1';
                fontsizeClassOp = 'inc';
                fontsizeClassSize = 1;
            } else if (fontsizeClassOp === 'inc' && fontsizeClassSize < 6) {
                jQuery('body').removeClass(fontsizeClass);
                fontsizeClassSize++;
                fontsizeClass = 'fontsize-inc-' + fontsizeClassSize;
            } else if (fontsizeClassOp === 'dec') {
                jQuery('body').removeClass(fontsizeClass);
                fontsizeClassSize--;
                fontsizeClass = 'fontsize-dec-' + fontsizeClassSize;
            }

            jQuery('body').addClass(fontsizeClass);
        }

        if (fontsizeCurrentAction === 'decrease') {
            if (fontsizeClassSize === null) {
                fontsizeClass = 'fontsize-dec-1';
                fontsizeClassOp = 'dec';
                fontsizeClassSize = 1;
            } else if (fontsizeClassOp === 'dec' && fontsizeClassSize < 6) {
                jQuery('body').removeClass(fontsizeClass);
                fontsizeClassSize++;
                fontsizeClass = 'fontsize-dec-' + fontsizeClassSize;
            } else if (fontsizeClassOp === 'inc') {
                jQuery('body').removeClass(fontsizeClass);
                fontsizeClassSize--;
                fontsizeClass = 'fontsize-inc-' + fontsizeClassSize;
            }

            jQuery('body').addClass(fontsizeClass);
        }

        this.toggleFontsizeButtons();
    };

    AccessibilityBar.prototype.toggleFontsizeButtons = function() {
        if (fontsizeClass === null) {
            jQuery('#fontsize_reset').addClass('disabled');
            jQuery('#fontsize_inc').removeClass('disabled');
            jQuery('#fontsize_dec').removeClass('disabled');
        }

        if (fontsizeClass !== null) {
            jQuery('#fontsize_reset').removeClass('disabled');
        }

        if (fontsizeClassOp === 'inc') {
            if (fontsizeClassSize == 6) {
                jQuery('#fontsize_inc').addClass('disabled');
            }

            if (fontsizeClassSize < 6) {
                jQuery('#fontsize_inc').removeClass('disabled');
            }
        }

        if (fontsizeClassOp === 'dec') {
            if (fontsizeClassSize == 6) {
                jQuery('#fontsize_dec').addClass('disabled');
            }

            if (fontsizeClassSize < 6) {
                jQuery('#fontsize_dec').removeClass('disabled');
            }
        }
    };

    AccessibilityBar.prototype.siteColor = function() {
        var request = Ajax.call([{
            methodname: 'theme_moove_sitecolor',
            args: {
                action: sitecolorCurrentAction
            }
        }]);

        request[0].done(function() {
            this.reloadSitecolorClass();
        }.bind(this));
    };

    AccessibilityBar.prototype.reloadSitecolorClass = function() {
        jQuery('body').removeClass(function(index, className) {
            return (className.match(/(^|\s)sitecolor-color-\S+/g) || []).join(' ');
        });

        if (sitecolorCurrentAction !== 'reset') {
            jQuery('body').addClass(sitecolorCurrentAction);
        }
    };

    return {
        'init': function() {
            return new AccessibilityBar();
        }
    };
});