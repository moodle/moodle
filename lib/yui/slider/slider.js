/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.0
*/
/**
 * The Slider component is a UI control that enables the user to adjust 
 * values in a finite range along one or two axes. Typically, the Slider 
 * control is used in a web application as a rich, visual replacement 
 * for an input box that takes a number as input. The Slider control can 
 * also easily accommodate a second dimension, providing x,y output for 
 * a selection point chosen from a rectangular region.
 *
 * @module    slider
 * @title     Slider Widget
 * @namespace YAHOO.widget
 * @requires  yahoo,dom,dragdrop,event
 * @optional  animation
 */

/**
 * A DragDrop implementation that can be used as a background for a
 * slider.  It takes a reference to the thumb instance 
 * so it can delegate some of the events to it.  The goal is to make the 
 * thumb jump to the location on the background when the background is 
 * clicked.  
 *
 * @class Slider
 * @extends YAHOO.util.DragDrop
 * @uses YAHOO.util.EventProvider
 * @constructor
 * @param {String}      id     The id of the element linked to this instance
 * @param {String}      sGroup The group of related DragDrop items
 * @param {SliderThumb} oThumb The thumb for this slider
 * @param {String}      sType  The type of slider (horiz, vert, region)
 */
YAHOO.widget.Slider = function(sElementId, sGroup, oThumb, sType) {
    if (sElementId) {
        this.init(sElementId, sGroup, true);
        this.initSlider(sType);
        this.initThumb(oThumb);
    }
};

/**
 * Factory method for creating a horizontal slider
 * @method YAHOO.widget.Slider.getHorizSlider
 * @static
 * @param {String} sBGElId the id of the slider's background element
 * @param {String} sHandleElId the id of the thumb element
 * @param {int} iLeft the number of pixels the element can move left
 * @param {int} iRight the number of pixels the element can move right
 * @param {int} iTickSize optional parameter for specifying that the element 
 * should move a certain number pixels at a time.
 * @return {Slider} a horizontal slider control
 */
YAHOO.widget.Slider.getHorizSlider = 
    function (sBGElId, sHandleElId, iLeft, iRight, iTickSize) {
        return new YAHOO.widget.Slider(sBGElId, sBGElId, 
            new YAHOO.widget.SliderThumb(sHandleElId, sBGElId, 
                               iLeft, iRight, 0, 0, iTickSize), "horiz");
};

/**
 * Factory method for creating a vertical slider
 * @method YAHOO.widget.Slider.getVertSlider
 * @static
 * @param {String} sBGElId the id of the slider's background element
 * @param {String} sHandleElId the id of the thumb element
 * @param {int} iUp the number of pixels the element can move up
 * @param {int} iDown the number of pixels the element can move down
 * @param {int} iTickSize optional parameter for specifying that the element 
 * should move a certain number pixels at a time.
 * @return {Slider} a vertical slider control
 */
YAHOO.widget.Slider.getVertSlider = 
    function (sBGElId, sHandleElId, iUp, iDown, iTickSize) {
        return new YAHOO.widget.Slider(sBGElId, sBGElId, 
            new YAHOO.widget.SliderThumb(sHandleElId, sBGElId, 0, 0, 
                               iUp, iDown, iTickSize), "vert");
};

/**
 * Factory method for creating a slider region like the one in the color
 * picker example
 * @method YAHOO.widget.Slider.getSliderRegion
 * @static
 * @param {String} sBGElId the id of the slider's background element
 * @param {String} sHandleElId the id of the thumb element
 * @param {int} iLeft the number of pixels the element can move left
 * @param {int} iRight the number of pixels the element can move right
 * @param {int} iUp the number of pixels the element can move up
 * @param {int} iDown the number of pixels the element can move down
 * @param {int} iTickSize optional parameter for specifying that the element 
 * should move a certain number pixels at a time.
 * @return {Slider} a slider region control
 */
YAHOO.widget.Slider.getSliderRegion = 
    function (sBGElId, sHandleElId, iLeft, iRight, iUp, iDown, iTickSize) {
        return new YAHOO.widget.Slider(sBGElId, sBGElId, 
            new YAHOO.widget.SliderThumb(sHandleElId, sBGElId, iLeft, iRight, 
                               iUp, iDown, iTickSize), "region");
};

/**
 * By default, animation is available if the animation library is detected.
 * @property YAHOO.widget.Slider.ANIM_AVAIL
 * @static
 * @type boolean
 */
YAHOO.widget.Slider.ANIM_AVAIL = true;


YAHOO.extend(YAHOO.widget.Slider, YAHOO.util.DragDrop, {

    /**
     * Initializes the slider.  Executed in the constructor
     * @method initSlider
     * @param {string} sType the type of slider (horiz, vert, region)
     */
    initSlider: function(sType) {

        /**
         * The type of the slider (horiz, vert, region)
         * @property type
         * @type string
         */
        this.type = sType;

        //this.removeInvalidHandleType("A");


        /**
         * Event the fires when the value of the control changes.  If 
         * the control is animated the event will fire every point
         * along the way.
         * @event change
         * @param {int} newOffset|x the new offset for normal sliders, or the new
         *                          x offset for region sliders
         * @param {int} y the number of pixels the thumb has moved on the y axis
         *                (region sliders only)
         */
        this.createEvent("change", this);

        /**
         * Event that fires at the beginning of a slider thumb move.
         * @event slideStart
         */
        this.createEvent("slideStart", this);

        /**
         * Event that fires at the end of a slider thumb move
         * @event slideEnd
         */
        this.createEvent("slideEnd", this);

        /**
         * Overrides the isTarget property in YAHOO.util.DragDrop
         * @property isTarget
         * @private
         */
        this.isTarget = false;
    
        /**
         * Flag that determines if the thumb will animate when moved
         * @property animate
         * @type boolean
         */
        this.animate = YAHOO.widget.Slider.ANIM_AVAIL;

        /**
         * Set to false to disable a background click thumb move
         * @property backgroundEnabled
         * @type boolean
         */
        this.backgroundEnabled = true;

        /**
         * Adjustment factor for tick animation, the more ticks, the
         * faster the animation (by default)
         * @property tickPause
         * @type int
         */
        this.tickPause = 40;

        /**
         * Enables the arrow, home and end keys, defaults to true.
         * @property enableKeys
         * @type boolean
         */
        this.enableKeys = true;

        /**
         * Specifies the number of pixels the arrow keys will move the slider.
         * Default is 25.
         * @property keyIncrement
         * @type int
         */
        this.keyIncrement = 20;

        /**
         * moveComplete is set to true when the slider has moved to its final
         * destination.  For animated slider, this value can be checked in 
         * the onChange handler to make it possible to execute logic only
         * when the move is complete rather than at all points along the way.
         * Deprecated because this flag is only useful when the background is
         * clicked and the slider is animated.  If the user drags the thumb,
         * the flag is updated when the drag is over ... the final onDrag event
         * fires before the mouseup the ends the drag, so the implementer will
         * never see it.
         *
         * @property moveComplete
         * @type Boolean
         * @deprecated use the slideEnd event instead
         */
        this.moveComplete = true;

        /**
         * If animation is configured, specifies the length of the animation
         * in seconds.
         * @property animationDuration
         * @type int
         * @default 0.2
         */
        this.animationDuration = 0.2;

        /**
         * Constant for valueChangeSource, indicating that the user clicked or
         * dragged the slider to change the value.
         * @property SOURCE_UI_EVENT
         * @final
         * @default 1
         */
        this.SOURCE_UI_EVENT = 1;

        /**
         * Constant for valueChangeSource, indicating that the value was altered
         * by a programmatic call to setValue/setRegionValue.
         * @property SOURCE_SET_VALUE
         * @final
         * @default 2
         */
        this.SOURCE_SET_VALUE = 2;

        /**
         * When the slider value changes, this property is set to identify where
         * the update came from.  This will be either 1, meaning the slider was
         * clicked or dragged, or 2, meaning that it was set via a setValue() call.
         * This can be used within event handlers to apply some of the logic only
         * when dealing with one source or another.
         * @property valueChangeSource
         * @type int
         * @since 2.3.0
         */
        this.valueChangeSource = 0;
    },

    /**
     * Initializes the slider's thumb. Executed in the constructor.
     * @method initThumb
     * @param {YAHOO.widget.SliderThumb} t the slider thumb
     */
    initThumb: function(t) {

        var self = this;

        /**
         * A YAHOO.widget.SliderThumb instance that we will use to 
         * reposition the thumb when the background is clicked
         * @property thumb
         * @type YAHOO.widget.SliderThumb
         */
        this.thumb = t;
        t.cacheBetweenDrags = true;

        // add handler for the handle onchange event
        t.onChange = function() { 
            self.handleThumbChange(); 
        };

        if (t._isHoriz && t.xTicks && t.xTicks.length) {
            this.tickPause = Math.round(360 / t.xTicks.length);
        } else if (t.yTicks && t.yTicks.length) {
            this.tickPause = Math.round(360 / t.yTicks.length);
        }


        // delegate thumb methods
        t.onMouseDown = function () { return self.focus(); };
        t.onMouseUp = function() { self.thumbMouseUp(); };
        t.onDrag = function() { self.fireEvents(true); };
        t.onAvailable = function() { return self.setStartSliderState(); };

    },

    /**
     * Executed when the slider element is available
     * @method onAvailable
     */
    onAvailable: function() {
        var Event = YAHOO.util.Event;
        Event.on(this.id, "keydown",  this.handleKeyDown,  this, true);
        Event.on(this.id, "keypress", this.handleKeyPress, this, true);
    },
 
    /**
     * Executed when a keypress event happens with the control focused.
     * Prevents the default behavior for navigation keys.  The actual
     * logic for moving the slider thumb in response to a key event
     * happens in handleKeyDown.
     * @param {Event} e the keypress event
     */
    handleKeyPress: function(e) {
        if (this.enableKeys) {
            var Event = YAHOO.util.Event;
            var kc = Event.getCharCode(e);
            switch (kc) {
                case 0x25: // left
                case 0x26: // up
                case 0x27: // right
                case 0x28: // down
                case 0x24: // home
                case 0x23: // end
                    Event.preventDefault(e);
                    break;
                default:
            }
        }
    },

    /**
     * Executed when a keydown event happens with the control focused.
     * Updates the slider value and display when the keypress is an
     * arrow key, home, or end as long as enableKeys is set to true.
     * @param {Event} e the keydown event
     */
    handleKeyDown: function(e) {
        if (this.enableKeys) {
            var Event = YAHOO.util.Event;

            var kc = Event.getCharCode(e), t=this.thumb;
            var h=this.getXValue(),v=this.getYValue();

            var horiz = false;
            var changeValue = true;
            switch (kc) {

                // left
                case 0x25: h -= this.keyIncrement; break;

                // up
                case 0x26: v -= this.keyIncrement; break;

                // right
                case 0x27: h += this.keyIncrement; break;

                // down
                case 0x28: v += this.keyIncrement; break;

                // home
                case 0x24: h = t.leftConstraint;    
                           v = t.topConstraint;    
                           break;

                // end
                case 0x23: h = t.rightConstraint; 
                           v = t.bottomConstraint;    
                           break;

                default:   changeValue = false;
            }

            if (changeValue) {
                if (t._isRegion) {
                    this.setRegionValue(h, v, true);
                } else {
                    var newVal = (t._isHoriz) ? h : v;
                    this.setValue(newVal, true);
                }
                Event.stopEvent(e);
            }

        }
    },

    /**
     * Initialization that sets up the value offsets once the elements are ready
     * @method setStartSliderState
     */
    setStartSliderState: function() {


        this.setThumbCenterPoint();

        /**
         * The basline position of the background element, used
         * to determine if the background has moved since the last
         * operation.
         * @property baselinePos
         * @type [int, int]
         */
        this.baselinePos = YAHOO.util.Dom.getXY(this.getEl());

        this.thumb.startOffset = this.thumb.getOffsetFromParent(this.baselinePos);

        if (this.thumb._isRegion) {
            if (this.deferredSetRegionValue) {
                this.setRegionValue.apply(this, this.deferredSetRegionValue, true);
                this.deferredSetRegionValue = null;
            } else {
                this.setRegionValue(0, 0, true, true);
            }
        } else {
            if (this.deferredSetValue) {
                this.setValue.apply(this, this.deferredSetValue, true);
                this.deferredSetValue = null;
            } else {
                this.setValue(0, true, true);
            }
        }
    },

    /**
     * When the thumb is available, we cache the centerpoint of the element so
     * we can position the element correctly when the background is clicked
     * @method setThumbCenterPoint
     */
    setThumbCenterPoint: function() {

        var el = this.thumb.getEl();

        if (el) {
            /**
             * The center of the slider element is stored so we can 
             * place it in the correct position when the background is clicked.
             * @property thumbCenterPoint
             * @type {"x": int, "y": int}
             */
            this.thumbCenterPoint = { 
                    x: parseInt(el.offsetWidth/2, 10), 
                    y: parseInt(el.offsetHeight/2, 10) 
            };
        }

    },

    /**
     * Locks the slider, overrides YAHOO.util.DragDrop
     * @method lock
     */
    lock: function() {
        this.thumb.lock();
        this.locked = true;
    },

    /**
     * Unlocks the slider, overrides YAHOO.util.DragDrop
     * @method unlock
     */
    unlock: function() {
        this.thumb.unlock();
        this.locked = false;
    },

    /**
     * Handles mouseup event on the slider background
     * @method thumbMouseUp
     * @private
     */
    thumbMouseUp: function() {
        if (!this.isLocked() && !this.moveComplete) {
            this.endMove();
        }

    },

    /**
     * Returns a reference to this slider's thumb
     * @method getThumb
     * @return {SliderThumb} this slider's thumb
     */
    getThumb: function() {
        return this.thumb;
    },

    /**
     * Try to focus the element when clicked so we can add
     * accessibility features
     * @method focus
     * @private
     */
    focus: function() {
        this.valueChangeSource = this.SOURCE_UI_EVENT;

        // Focus the background element if possible
        var el = this.getEl();

        if (el.focus) {
            try {
                el.focus();
            } catch(e) {
                // Prevent permission denied unhandled exception in FF that can
                // happen when setting focus while another element is handling
                // the blur.  @TODO this is still writing to the error log 
                // (unhandled error) in FF1.5 with strict error checking on.
            }
        }

        this.verifyOffset();

        if (this.isLocked()) {
            return false;
        } else {
            this.onSlideStart();
            return true;
        }
    },

    /**
     * Event that fires when the value of the slider has changed
     * @method onChange
     * @param {int} firstOffset the number of pixels the thumb has moved
     * from its start position. Normal horizontal and vertical sliders will only
     * have the firstOffset.  Regions will have both, the first is the horizontal
     * offset, the second the vertical.
     * @param {int} secondOffset the y offset for region sliders
     * @deprecated use instance.subscribe("change") instead
     */
    onChange: function (firstOffset, secondOffset) { 
        /* override me */ 
    },

    /**
     * Event that fires when the at the beginning of the slider thumb move
     * @method onSlideStart
     * @deprecated use instance.subscribe("slideStart") instead
     */
    onSlideStart: function () { 
        /* override me */ 
    },

    /**
     * Event that fires at the end of a slider thumb move
     * @method onSliderEnd
     * @deprecated use instance.subscribe("slideEnd") instead
     */
    onSlideEnd: function () { 
        /* override me */ 
    },

    /**
     * Returns the slider's thumb offset from the start position
     * @method getValue
     * @return {int} the current value
     */
    getValue: function () { 
        return this.thumb.getValue();
    },

    /**
     * Returns the slider's thumb X offset from the start position
     * @method getXValue
     * @return {int} the current horizontal offset
     */
    getXValue: function () { 
        return this.thumb.getXValue();
    },

    /**
     * Returns the slider's thumb Y offset from the start position
     * @method getYValue
     * @return {int} the current vertical offset
     */
    getYValue: function () { 
        return this.thumb.getYValue();
    },

    /**
     * Internal handler for the slider thumb's onChange event
     * @method handleThumbChange
     * @private
     */
    handleThumbChange: function () { 
        var t = this.thumb;
        if (t._isRegion) {
            t.onChange(t.getXValue(), t.getYValue());
            this.fireEvent("change", { x: t.getXValue(), y: t.getYValue() } );
        } else {
            t.onChange(t.getValue());
            this.fireEvent("change", t.getValue());
        }

    },

    /**
     * Provides a way to set the value of the slider in code.
     * @method setValue
     * @param {int} newOffset the number of pixels the thumb should be
     * positioned away from the initial start point 
     * @param {boolean} skipAnim set to true to disable the animation
     * for this move action (but not others).
     * @param {boolean} force ignore the locked setting and set value anyway
     * @return {boolean} true if the move was performed, false if it failed
     */
    setValue: function(newOffset, skipAnim, force) {

        this.valueChangeSource = this.SOURCE_SET_VALUE;

        if (!this.thumb.available) {
            this.deferredSetValue = arguments;
            return false;
        }

        if (this.isLocked() && !force) {
            return false;
        }

        if ( isNaN(newOffset) ) {
            return false;
        }

        var t = this.thumb;
        var newX, newY;
        this.verifyOffset(true);
        if (t._isRegion) {
            return false;
        } else if (t._isHoriz) {
            this.onSlideStart();
            // this.fireEvent("slideStart");
            newX = t.initPageX + newOffset + this.thumbCenterPoint.x;
            this.moveThumb(newX, t.initPageY, skipAnim);
        } else {
            this.onSlideStart();
            // this.fireEvent("slideStart");
            newY = t.initPageY + newOffset + this.thumbCenterPoint.y;
            this.moveThumb(t.initPageX, newY, skipAnim);
        }

        return true;
    },

    /**
     * Provides a way to set the value of the region slider in code.
     * @method setRegionValue
     * @param {int} newOffset the number of pixels the thumb should be
     * positioned away from the initial start point (x axis for region)
     * @param {int} newOffset2 the number of pixels the thumb should be
     * positioned away from the initial start point (y axis for region)
     * @param {boolean} skipAnim set to true to disable the animation
     * for this move action (but not others).
     * @param {boolean} force ignore the locked setting and set value anyway
     * @return {boolean} true if the move was performed, false if it failed
     */
    setRegionValue: function(newOffset, newOffset2, skipAnim, force) {

        this.valueChangeSource = this.SOURCE_SET_VALUE;

        if (!this.thumb.available) {
            this.deferredSetRegionValue = arguments;
            return false;
        }

        if (this.isLocked() && !force) {
            return false;
        }

        if ( isNaN(newOffset) ) {
            return false;
        }

        var t = this.thumb;
        if (t._isRegion) {
            this.onSlideStart();
            var newX = t.initPageX + newOffset + this.thumbCenterPoint.x;
            var newY = t.initPageY + newOffset2 + this.thumbCenterPoint.y;
            this.moveThumb(newX, newY, skipAnim);
            return true;
        }

        return false;

    },

    /**
     * Checks the background position element position.  If it has moved from the
     * baseline position, the constraints for the thumb are reset
     * @param checkPos {boolean} check the position instead of using cached value
     * @method verifyOffset
     * @return {boolean} True if the offset is the same as the baseline.
     */
    verifyOffset: function(checkPos) {

        var newPos = YAHOO.util.Dom.getXY(this.getEl());
        //var newPos = [this.initPageX, this.initPageY];


        if (newPos[0] != this.baselinePos[0] || newPos[1] != this.baselinePos[1]) {
            this.thumb.resetConstraints();
            this.baselinePos = newPos;
            return false;
        }

        return true;
    },

    /**
     * Move the associated slider moved to a timeout to try to get around the 
     * mousedown stealing moz does when I move the slider element between the 
     * cursor and the background during the mouseup event
     * @method moveThumb
     * @param {int} x the X coordinate of the click
     * @param {int} y the Y coordinate of the click
     * @param {boolean} skipAnim don't animate if the move happend onDrag
     * @private
     */
    moveThumb: function(x, y, skipAnim) {


        var t = this.thumb;
        var self = this;

        if (!t.available) {
            return;
        }


        // this.verifyOffset();

        t.setDelta(this.thumbCenterPoint.x, this.thumbCenterPoint.y);

        var _p = t.getTargetCoord(x, y);
        var p = [_p.x, _p.y];


        this.fireEvent("slideStart");

        if (this.animate && YAHOO.widget.Slider.ANIM_AVAIL && t._graduated && !skipAnim) {
            // this.thumb._animating = true;
            this.lock();

            // cache the current thumb pos
            this.curCoord = YAHOO.util.Dom.getXY(this.thumb.getEl());

            setTimeout( function() { self.moveOneTick(p); }, this.tickPause );

        } else if (this.animate && YAHOO.widget.Slider.ANIM_AVAIL && !skipAnim) {

            // this.thumb._animating = true;
            this.lock();

            var oAnim = new YAHOO.util.Motion( 
                    t.id, { points: { to: p } }, 
                    this.animationDuration, 
                    YAHOO.util.Easing.easeOut );

            oAnim.onComplete.subscribe( function() { self.endMove(); } );
            oAnim.animate();
        } else {
            t.setDragElPos(x, y);
            // this.fireEvents();
            this.endMove();
        }
    },

    /**
     * Move the slider one tick mark towards its final coordinate.  Used
     * for the animation when tick marks are defined
     * @method moveOneTick
     * @param {int[]} the destination coordinate
     * @private
     */
    moveOneTick: function(finalCoord) {

        var t = this.thumb, tmp;


        // redundant call to getXY since we set the position most of time prior 
        // to getting here.  Moved to this.curCoord
        //var curCoord = YAHOO.util.Dom.getXY(t.getEl());

        // alignElWithMouse caches position in lastPageX, lastPageY .. doesn't work
        //var curCoord = [this.lastPageX, this.lastPageY];

        // var thresh = Math.min(t.tickSize + (Math.floor(t.tickSize/2)), 10);
        // var thresh = 10;
        // var thresh = t.tickSize + (Math.floor(t.tickSize/2));

        var nextCoord = null;

        if (t._isRegion) {
            nextCoord = this._getNextX(this.curCoord, finalCoord);
            var tmpX = (nextCoord) ? nextCoord[0] : this.curCoord[0];
            nextCoord = this._getNextY([tmpX, this.curCoord[1]], finalCoord);

        } else if (t._isHoriz) {
            nextCoord = this._getNextX(this.curCoord, finalCoord);
        } else {
            nextCoord = this._getNextY(this.curCoord, finalCoord);
        }


        if (nextCoord) {

            // cache the position
            this.curCoord = nextCoord;

            // move to the next coord
            // YAHOO.util.Dom.setXY(t.getEl(), nextCoord);

            // var el = t.getEl();
            // YAHOO.util.Dom.setStyle(el, "left", (nextCoord[0] + this.thumb.deltaSetXY[0]) + "px");
            // YAHOO.util.Dom.setStyle(el, "top",  (nextCoord[1] + this.thumb.deltaSetXY[1]) + "px");

            this.thumb.alignElWithMouse(t.getEl(), nextCoord[0], nextCoord[1]);
            
            // check if we are in the final position, if not make a recursive call
            if (!(nextCoord[0] == finalCoord[0] && nextCoord[1] == finalCoord[1])) {
                var self = this;
                setTimeout(function() { self.moveOneTick(finalCoord); }, 
                        this.tickPause);
            } else {
                this.endMove();
            }
        } else {
            this.endMove();
        }

        //this.tickPause = Math.round(this.tickPause/2);
    },

    /**
     * Returns the next X tick value based on the current coord and the target coord.
     * @method _getNextX
     * @private
     */
    _getNextX: function(curCoord, finalCoord) {
        var t = this.thumb;
        var thresh;
        var tmp = [];
        var nextCoord = null;
        if (curCoord[0] > finalCoord[0]) {
            thresh = t.tickSize - this.thumbCenterPoint.x;
            tmp = t.getTargetCoord( curCoord[0] - thresh, curCoord[1] );
            nextCoord = [tmp.x, tmp.y];
        } else if (curCoord[0] < finalCoord[0]) {
            thresh = t.tickSize + this.thumbCenterPoint.x;
            tmp = t.getTargetCoord( curCoord[0] + thresh, curCoord[1] );
            nextCoord = [tmp.x, tmp.y];
        } else {
            // equal, do nothing
        }

        return nextCoord;
    },

    /**
     * Returns the next Y tick value based on the current coord and the target coord.
     * @method _getNextY
     * @private
     */
    _getNextY: function(curCoord, finalCoord) {
        var t = this.thumb;
        var thresh;
        var tmp = [];
        var nextCoord = null;

        if (curCoord[1] > finalCoord[1]) {
            thresh = t.tickSize - this.thumbCenterPoint.y;
            tmp = t.getTargetCoord( curCoord[0], curCoord[1] - thresh );
            nextCoord = [tmp.x, tmp.y];
        } else if (curCoord[1] < finalCoord[1]) {
            thresh = t.tickSize + this.thumbCenterPoint.y;
            tmp = t.getTargetCoord( curCoord[0], curCoord[1] + thresh );
            nextCoord = [tmp.x, tmp.y];
        } else {
            // equal, do nothing
        }

        return nextCoord;
    },

    /**
     * Resets the constraints before moving the thumb.
     * @method b4MouseDown
     * @private
     */
    b4MouseDown: function(e) {
        this.thumb.autoOffset();
        this.thumb.resetConstraints();
    },


    /**
     * Handles the mousedown event for the slider background
     * @method onMouseDown
     * @private
     */
    onMouseDown: function(e) {
        // this.resetConstraints(true);
        // this.thumb.resetConstraints(true);

        if (! this.isLocked() && this.backgroundEnabled) {
            var x = YAHOO.util.Event.getPageX(e);
            var y = YAHOO.util.Event.getPageY(e);

            this.focus();
            this.moveThumb(x, y);
        }
        
    },

    /**
     * Handles the onDrag event for the slider background
     * @method onDrag
     * @private
     */
    onDrag: function(e) {
        if (! this.isLocked()) {
            var x = YAHOO.util.Event.getPageX(e);
            var y = YAHOO.util.Event.getPageY(e);
            this.moveThumb(x, y, true);
        }
    },

    /**
     * Fired when the slider movement ends
     * @method endMove
     * @private
     */
    endMove: function () {
        // this._animating = false;
        this.unlock();
        this.moveComplete = true;
        this.fireEvents();
    },

    /**
     * Fires the change event if the value has been changed.  Ignored if we are in
     * the middle of an animation as the event will fire when the animation is
     * complete
     * @method fireEvents
     * @param {boolean} thumbEvent set to true if this event is fired from an event
     *                  that occurred on the thumb.  If it is, the state of the
     *                  thumb dd object should be correct.  Otherwise, the event
     *                  originated on the background, so the thumb state needs to
     *                  be refreshed before proceeding.
     * @private
     */
    fireEvents: function (thumbEvent) {

        var t = this.thumb;

        if (!thumbEvent) {
            t.cachePosition();
        }

        if (! this.isLocked()) {
            if (t._isRegion) {
                var newX = t.getXValue();
                var newY = t.getYValue();

                if (newX != this.previousX || newY != this.previousY) {
                    this.onChange(newX, newY);
                    this.fireEvent("change", { x: newX, y: newY });
                }

                this.previousX = newX;
                this.previousY = newY;

            } else {
                var newVal = t.getValue();
                if (newVal != this.previousVal) {
                    this.onChange( newVal );
                    this.fireEvent("change", newVal);
                }
                this.previousVal = newVal;
            }

            if (this.moveComplete) {
                this.onSlideEnd();
                this.fireEvent("slideEnd");
                this.moveComplete = false;
            }

        }
    },

    /**
     * Slider toString
     * @method toString
     * @return {string} string representation of the instance
     */
    toString: function () { 
        return ("Slider (" + this.type +") " + this.id);
    }

});

YAHOO.augment(YAHOO.widget.Slider, YAHOO.util.EventProvider);

/**
 * A drag and drop implementation to be used as the thumb of a slider.
 * @class SliderThumb
 * @extends YAHOO.util.DD
 * @constructor
 * @param {String} id the id of the slider html element
 * @param {String} sGroup the group of related DragDrop items
 * @param {int} iLeft the number of pixels the element can move left
 * @param {int} iRight the number of pixels the element can move right
 * @param {int} iUp the number of pixels the element can move up
 * @param {int} iDown the number of pixels the element can move down
 * @param {int} iTickSize optional parameter for specifying that the element 
 * should move a certain number pixels at a time.
 */
YAHOO.widget.SliderThumb = function(id, sGroup, iLeft, iRight, iUp, iDown, iTickSize) {

    if (id) {
        //this.init(id, sGroup);
        YAHOO.widget.SliderThumb.superclass.constructor.call(this, id, sGroup);

        /**
         * The id of the thumbs parent HTML element (the slider background 
         * element).
         * @property parentElId
         * @type string
         */
        this.parentElId = sGroup;
    }


    //this.removeInvalidHandleType("A");


    /**
     * Overrides the isTarget property in YAHOO.util.DragDrop
     * @property isTarget
     * @private
     */
    this.isTarget = false;

    /**
     * The tick size for this slider
     * @property tickSize
     * @type int
     * @private
     */
    this.tickSize = iTickSize;

    /**
     * Informs the drag and drop util that the offsets should remain when
     * resetting the constraints.  This preserves the slider value when
     * the constraints are reset
     * @property maintainOffset
     * @type boolean
     * @private
     */
    this.maintainOffset = true;

    this.initSlider(iLeft, iRight, iUp, iDown, iTickSize);

    /**
     * Turns off the autoscroll feature in drag and drop
     * @property scroll
     * @private
     */
    this.scroll = false;

}; 

YAHOO.extend(YAHOO.widget.SliderThumb, YAHOO.util.DD, {

    /**
     * The (X and Y) difference between the thumb location and its parent 
     * (the slider background) when the control is instantiated.
     * @property startOffset
     * @type [int, int]
     */
    startOffset: null,

    /**
     * Flag used to figure out if this is a horizontal or vertical slider
     * @property _isHoriz
     * @type boolean
     * @private
     */
    _isHoriz: false,

    /**
     * Cache the last value so we can check for change
     * @property _prevVal
     * @type int
     * @private
     */
    _prevVal: 0,

    /**
     * The slider is _graduated if there is a tick interval defined
     * @property _graduated
     * @type boolean
     * @private
     */
    _graduated: false,


    /**
     * Returns the difference between the location of the thumb and its parent.
     * @method getOffsetFromParent
     * @param {[int, int]} parentPos Optionally accepts the position of the parent
     * @type [int, int]
     */
    getOffsetFromParent0: function(parentPos) {
        var myPos = YAHOO.util.Dom.getXY(this.getEl());
        var ppos  = parentPos || YAHOO.util.Dom.getXY(this.parentElId);

        return [ (myPos[0] - ppos[0]), (myPos[1] - ppos[1]) ];
    },

    getOffsetFromParent: function(parentPos) {

        var el = this.getEl();

        if (!this.deltaOffset) {

            var myPos = YAHOO.util.Dom.getXY(el);
            var ppos  = parentPos || YAHOO.util.Dom.getXY(this.parentElId);

            var newOffset = [ (myPos[0] - ppos[0]), (myPos[1] - ppos[1]) ];

            var l = parseInt( YAHOO.util.Dom.getStyle(el, "left"), 10 );
            var t = parseInt( YAHOO.util.Dom.getStyle(el, "top" ), 10 );

            var deltaX = l - newOffset[0];
            var deltaY = t - newOffset[1];

            if (isNaN(deltaX) || isNaN(deltaY)) {
            } else {
                this.deltaOffset = [deltaX, deltaY];
            }

        } else {
            var newLeft = parseInt( YAHOO.util.Dom.getStyle(el, "left"), 10 );
            var newTop  = parseInt( YAHOO.util.Dom.getStyle(el, "top" ), 10 );

            newOffset  = [newLeft + this.deltaOffset[0], newTop + this.deltaOffset[1]];
        }

        return newOffset;

        //return [ (myPos[0] - ppos[0]), (myPos[1] - ppos[1]) ];
    },

    /**
     * Set up the slider, must be called in the constructor of all subclasses
     * @method initSlider
     * @param {int} iLeft the number of pixels the element can move left
     * @param {int} iRight the number of pixels the element can move right
     * @param {int} iUp the number of pixels the element can move up
     * @param {int} iDown the number of pixels the element can move down
     * @param {int} iTickSize the width of the tick interval.
     */
    initSlider: function (iLeft, iRight, iUp, iDown, iTickSize) {


        //document these.  new for 0.12.1
        this.initLeft = iLeft;
        this.initRight = iRight;
        this.initUp = iUp;
        this.initDown = iDown;

        this.setXConstraint(iLeft, iRight, iTickSize);
        this.setYConstraint(iUp, iDown, iTickSize);

        if (iTickSize && iTickSize > 1) {
            this._graduated = true;
        }

        this._isHoriz  = (iLeft || iRight); 
        this._isVert   = (iUp   || iDown);
        this._isRegion = (this._isHoriz && this._isVert); 

    },

    /**
     * Clear's the slider's ticks
     * @method clearTicks
     */
    clearTicks: function () {
        YAHOO.widget.SliderThumb.superclass.clearTicks.call(this);
        this.tickSize = 0;
        this._graduated = false;
    },


    /**
     * Gets the current offset from the element's start position in
     * pixels.
     * @method getValue
     * @return {int} the number of pixels (positive or negative) the
     * slider has moved from the start position.
     */
    getValue: function () {
        if (!this.available) { return 0; }
        var val = (this._isHoriz) ? this.getXValue() : this.getYValue();
        return val;
    },

    /**
     * Gets the current X offset from the element's start position in
     * pixels.
     * @method getXValue
     * @return {int} the number of pixels (positive or negative) the
     * slider has moved horizontally from the start position.
     */
    getXValue: function () {
        if (!this.available) { return 0; }
        var newOffset = this.getOffsetFromParent();
        return (newOffset[0] - this.startOffset[0]);
    },

    /**
     * Gets the current Y offset from the element's start position in
     * pixels.
     * @method getYValue
     * @return {int} the number of pixels (positive or negative) the
     * slider has moved vertically from the start position.
     */
    getYValue: function () {
        if (!this.available) { return 0; }
        var newOffset = this.getOffsetFromParent();
        return (newOffset[1] - this.startOffset[1]);
    },

    /**
     * Thumb toString
     * @method toString
     * @return {string} string representation of the instance
     */
    toString: function () { 
        return "SliderThumb " + this.id;
    },

    /**
     * The onchange event for the handle/thumb is delegated to the YAHOO.widget.Slider
     * instance it belongs to.
     * @method onChange
     * @private
     */
    onChange: function (x, y) { 
    }

});

if ("undefined" == typeof YAHOO.util.Anim) {
    YAHOO.widget.Slider.ANIM_AVAIL = false;
}

YAHOO.register("slider", YAHOO.widget.Slider, {version: "2.3.0", build: "442"});
