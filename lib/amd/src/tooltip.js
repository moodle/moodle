define(['jquery', 'core/aria'], function($, Aria) {

    /**
     * Tooltip class.
     *
     * @param {String} selector The css selector for the node(s) to enhance with tooltips.
     */
    var Tooltip = function(selector) {
        // Tooltip code matches: http://www.w3.org/WAI/PF/aria-practices/#tooltip
        this._regionSelector = selector;

        // For each node matching the selector - find an aria-describedby attribute pointing to an role="tooltip" element.

        $(this._regionSelector).each(function(index, element) {
            var tooltipId = $(element).attr('aria-describedby');
            if (tooltipId) {
                var tooltipele = document.getElementById(tooltipId);
                if (tooltipele) {
                    var correctRole = $(tooltipele).attr('role') == 'tooltip';

                    if (correctRole) {
                        $(tooltipele).hide();
                        // Ensure the trigger for the tooltip is keyboard focusable.
                        $(element).attr('tabindex', '0');
                    }

                    // Attach listeners.
                    $(element).on('focus', this._handleFocus.bind(this));
                    $(element).on('mouseover', this._handleMouseOver.bind(this));
                    $(element).on('mouseout', this._handleMouseOut.bind(this));
                    $(element).on('blur', this._handleBlur.bind(this));
                    $(element).on('keydown', this._handleKeyDown.bind(this));
                }
            }
        }.bind(this));
    };

    /** @type {String} Selector for the page region containing the user navigation. */
    Tooltip.prototype._regionSelector = null;

    /**
     * Find the tooltip referred to by this element and show it.
     *
     * @param {Event} e
     */
    Tooltip.prototype._showTooltip = function(e) {
        var triggerElement = $(e.target);
        var tooltipId = triggerElement.attr('aria-describedby');
        if (tooltipId) {
            var tooltipele = $(document.getElementById(tooltipId));

            tooltipele.show();
            Aria.unhide(tooltipele);

            if (!tooltipele.is('.tooltip')) {
                // Change the markup to a bootstrap tooltip.
                var inner = $('<div class="tooltip-inner"></div>');
                inner.append(tooltipele.contents());
                tooltipele.append(inner);
                tooltipele.addClass('tooltip');
                tooltipele.addClass('bottom');
                tooltipele.append('<div class="tooltip-arrow"></div>');
            }
            var pos = triggerElement.offset();
            pos.top += triggerElement.height() + 10;
            $(tooltipele).offset(pos);
        }
    };

    /**
     * Find the tooltip referred to by this element and hide it.
     *
     * @param {Event} e
     */
    Tooltip.prototype._hideTooltip = function(e) {
        var triggerElement = $(e.target);
        var tooltipId = triggerElement.attr('aria-describedby');
        if (tooltipId) {
            var tooltipele = document.getElementById(tooltipId);

            $(tooltipele).hide();
            Aria.hide(tooltipele);
        }
    };

    /**
     * Listener for focus events.
     * @param {Event} e
     */
    Tooltip.prototype._handleFocus = function(e) {
        this._showTooltip(e);
    };

    /**
     * Listener for keydown events.
     * @param {Event} e
     */
    Tooltip.prototype._handleKeyDown = function(e) {
        if (e.which == 27) {
            this._hideTooltip(e);
        }
    };

    /**
     * Listener for mouseover events.
     * @param {Event} e
     */
    Tooltip.prototype._handleMouseOver = function(e) {
        this._showTooltip(e);
    };

    /**
     * Listener for mouseout events.
     * @param {Event} e
     */
    Tooltip.prototype._handleMouseOut = function(e) {
        var triggerElement = $(e.target);

        if (!triggerElement.is(":focus")) {
            this._hideTooltip(e);
        }
    };

    /**
     * Listener for blur events.
     * @param {Event} e
     */
    Tooltip.prototype._handleBlur = function(e) {
        this._hideTooltip(e);
    };

    return Tooltip;
});
