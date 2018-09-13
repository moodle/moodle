/**
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict"; // jshint ;_;

    log.debug('Essential Colour Switcher AMD');

    (function($) {
        // Constructor.
        var ColoursSwitcher = function(element, data) {
            this.$element = $(element);
            this.SCHEMES = ['default', 'alternative1', 'alternative2', 'alternative3', 'alternative4'];
            this.scheme = 'default';
            this.init(data);
        };

        ColoursSwitcher.prototype = {
            constructor: ColoursSwitcher,
            init: function(data) {
                var index, scheme;
                /* Attach events to the links to change colours scheme so we can do it with
                   JavaScript without refreshing the page. */
                log.debug('Colour switcher on element: ' + data.div);
                var body = $('body');
                for (index in this.SCHEMES) {
                    scheme = this.SCHEMES[index];
                    // Check if this is the current colour.
                    if (body.hasClass('essential-colours-' + scheme)) {
                        this.scheme = scheme;
                        log.debug('Colour switcher current scheme: ' + scheme);
                    }
                    var us = this;
                    $(data.div + ' .' + scheme).each(function() {
                        log.debug('Colour switcher \'init\' each: ' + scheme);
                        $(this).click({scheme: scheme, us: us}, us.setScheme);
                    });
                }
            },
            setScheme: function(event) {
                event.preventDefault();
                log.debug('Colour switcher \'setScheme\' scheme: ' + event.data.scheme);
                log.debug('Colour switcher \'setScheme\' our scheme: ' + event.data.us.scheme);
                if (event.data.scheme != event.data.us.scheme) {
                    // Switch over the CSS classes on the body.
                    var prefix = 'essential-colours-';
                    // The $element is the 'body', see module 'init' below.
                    event.data.us.$element.removeClass(prefix + event.data.us.scheme).addClass(prefix + event.data.scheme);
                    // Update the current colour.
                    event.data.us.scheme = event.data.scheme;
                    // Store the users selection (uses AJAX to save to the database).
                    // Core YUI function, so only need to replace if core changes.
                    M.util.set_user_preference('theme_essential_colours', event.data.us.scheme);
                    log.debug('Colour switcher \'setScheme\' our scheme now: ' + event.data.us.scheme);
                }
            }
        };

        // Plugin definition.
        var old = $.fn.ColoursSwitcher;

        $.fn.ColoursSwitcher = function(data) {
            new ColoursSwitcher(this, data);
            return this;
        };

        // No conflict.
        $.fn.ColoursSwitcher.noConflict = function() {
            $.fn.ColoursSwitcher = old;
            return this;
        };
    })($);

    return {
        init: function(data) {
            $(document).ready(function($) {
                log.debug('Essential Colour Switcher AMD init');
                return $(document.body).ColoursSwitcher(data);
            });
        }
    };
});
/* jshint ignore:end */