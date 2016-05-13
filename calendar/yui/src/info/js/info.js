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
 * Overlay manager for the Moodle Calendar.
 *
 * @module     moodle-core_calendar-info
 * @package    core_calendar
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @main       moodle-core_calendar-info
 */

var ARIACONTROLS = 'aria-controls',
    BOUNDINGBOX = 'boundingBox',
    CALENDAREVENT = '[data-core_calendar-title]',
    CALENDARTABLE = 'calendartable',
    DATAPREFIX = 'core_calendar-',
    DOT = '.',
    EVENTCONTENT = 'eventcontent',
    EVENTDELAY = 'delay',
    EVENTTITLE = 'eventtitle',
    INNERHTML = 'innerHTML',

    /**
     * Overlay manager for the Moodle calendar.
     *
     * @namespace M.core_calendar
     * @class Info
     * @constructor
     */

    Info = function() {
        Info.superclass.constructor.apply(this, arguments);
    };


Y.extend(Info, Y.Base, {
    /**
     * A pointer to the timer used for showing the panel.
     *
     * @property _showTimer
     * @type object
     * @private
     */
    _showTimer: null,

    /**
     * A pointer to the timer used for hiding the panel.
     *
     * @property _hideTimer
     * @type object
     * @private
     */
    _hideTimer: null,

    /**
     * A pointer for the Calendar Overlay.
     *
     * @property _panel
     * @type object
     * @private
     */
    _panel: null,

    /**
     * A pointer to the cell containing the currently open calendar day.
     *
     * @property _currentDay
     * @type object
     * @private
     */
    _currentDay: null,

    initializer: function() {
        var body = Y.one(Y.config.doc.body);
        body.delegate(['mouseenter', 'focus'], this._startShow, CALENDAREVENT, this);
        body.delegate(['mouseleave', 'blur'], this._startHide, CALENDAREVENT, this);
    },

    /**
     * Initialise the Overlay in which information is displayed.
     *
     * @method __initOverlay
     * @chainable
     */
    _initOverlay: function() {
        if (!this._panel) {
            this._panel = new Y.Overlay({
                headerContent: Y.Node.create('<h2 class="' + EVENTTITLE + '"/>'),
                bodyContent: Y.Node.create('<div class="' + EVENTCONTENT + '"/>'),
                visible: false,
                render: true
            });

            this._panel.get(BOUNDINGBOX)
                .addClass('calendar-event-panel');
        }

        return this;
    },

    /**
     * Prepare to show the Overlay, and kick off the jobs that cause it to be shown.
     *
     * @method _startShow
     * @param {EventFacade} e
     * @private
     */
    _startShow: function(e) {
        if (this._isCurrentDayVisible(e.currentTarget)) {
            // Only start the show if the current day isn't already visible.
            return;
        }

        this._cancelHide()
            ._cancelShow()

        // Initialise the panel now - this will only happen once. This way
        // it's ready for when the timer times out.
            ._initOverlay();


        this._showTimer = setTimeout(Y.bind(function() {
                var calendarCell = e.target.ancestor(CALENDAREVENT, true);
                this._show(calendarCell);
            }, this), this.get(EVENTDELAY));
    },

    /**
     * Display the Overlay immediately.
     *
     * @method _show
     * @param {Node} dayCell The location that the Overlay should be displayed.
     */
    _show: function(dayCell) {
        var bb = this._panel.get(BOUNDINGBOX),
            widgetPositionAlign = Y.WidgetPositionAlign,
            calendarParent = dayCell.ancestor(DOT + CALENDARTABLE);

        bb.one(DOT + EVENTTITLE).set(INNERHTML, dayCell.getData(DATAPREFIX + 'title'));
        bb.one(DOT + EVENTCONTENT).set(INNERHTML, dayCell.getData(DATAPREFIX + 'popupcontent'));

        // Set the ARIA attributes for the owning cell.
        if (this._currentDay) {
            this._currentDay.setAttribute(ARIACONTROLS, null);
        }
        dayCell.setAttribute(ARIACONTROLS, dayCell.get('id'));

        // Move the panel to the current target.
        dayCell.appendChild(bb);

        // Keep track of the new day being shown.
        this._currentDay = dayCell;

        this._panel.constrain(calendarParent);
        this._panel
            .set('width', calendarParent.get('offsetWidth') + 'px')
            // Align it with the area clicked.
            .align(calendarParent, [
                    widgetPositionAlign.TC,
                    widgetPositionAlign.TC
                ])
            // Show it.
            .show();

        bb.setAttribute('tabindex', '0')
          .focus();
    },

    /**
     * Cancel the timers which would cause the overlay to be shown.
     *
     * @method _cancelShow
     * @chainable
     * @private
     */
    _cancelShow: function() {
        if (this._showTimer) {
            clearTimeout(this._showTimer);
        }

        return this;
    },

    /**
     * Prepare to hide the Overlay, and kick off the jobs that cause it to be hidden.
     *
     * @method _startHide
     * @param {EventFacade} e
     * @private
     */
    _startHide: function(e) {
        if (e.type === 'blur' && e.currentTarget.contains(e.target)) {
            return;
        }
        this._cancelShow()
            ._cancelHide();
        this._hideTimer = setTimeout(Y.bind(function() {
                this._hide();
            }, this), this.get(EVENTDELAY));
    },

    /**
     * Hide the Overlay immediately.
     *
     * @method _hide
     */
    _hide: function() {
        if (this._panel) {
            this._panel.hide();
        }
    },

    /**
     * Cancel the timers which would cause the overlay to be hidden.
     *
     * @method _cancelHide
     * @chainable
     * @private
     */
    _cancelHide: function() {
        if (this._hideTimer) {
            clearTimeout(this._hideTimer);
        }

        return this;
    },

    /**
     * Determine whether the specified day is currently visible.
     *
     * @method _isCurrentDayVisible
     * @param specifiedDay {Node} The Node to check visibility for.
     * @private
     */
    _isCurrentDayVisible: function(specifiedDay) {
        if (!this._panel || !this._panel.get('visible')) {
            return false;
        }

        if (specifiedDay !== this._currentDay) {
            return false;
        }

        return true;
    }
}, {
    NAME: 'calendarInfo',
    ATTRS: {
        /**
         * The delay to use before showing or hiding the calendar.
         *
         * @attribute delay
         * @type Number
         * @default 300
         */
        delay: {
            value: 300
        }
    }
});

Y.namespace('M.core_calendar.info').init = function(config) {
    return new Info(config);
};
