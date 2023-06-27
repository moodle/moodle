// Standard license block omitted.
/*
 * @package    block_slider
 * @copyright  2018 Kamil Łuczak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery"], function ($) {

    /**
     * BxSlider v4.2.1d
     * Copyright 2013-2017 Steven Wanderski
     * Written while drinking Belgian ales and listening to jazz
     * Licensed under MIT (http://opensource.org/licenses/MIT).
     */
    (function () {
        (function ($) {
            var defaults = {
                // GENERAL.
                mode: 'horizontal',
                slideSelector: '',
                infiniteLoop: true,
                hideControlOnEnd: false,
                speed: 500,
                easing: null,
                slideMargin: 0,
                startSlide: 0,
                randomStart: false,
                captions: false,
                ticker: false,
                tickerHover: false,
                adaptiveHeight: false,
                adaptiveHeightSpeed: 500,
                video: false,
                useCSS: true,
                preloadImages: "visible",
                responsive: true,
                slideZIndex: 50,
                wrapperClass: 'bx-wrapper',

                // TOUCH.
                touchEnabled: true,
                swipeThreshold: 50,
                oneToOneTouch: true,
                preventDefaultSwipeX: true,
                preventDefaultSwipeY: false,

                // ACCESSIBILITY.
                ariaLive: true,
                ariaHidden: true,

                // KEYBOARD.
                keyboardEnabled: false,

                // PAGER.
                pager: true,
                pagerType: 'full',
                pagerShortSeparator: ' / ',
                pagerSelector: null,
                buildPager: null,
                pagerCustom: null,

                // CONTROLS.
                controls: true,
                nextText: 'Next',
                prevText: 'Prev',
                nextSelector: null,
                prevSelector: null,
                autoControls: false,
                startText: 'Start',
                stopText: 'Stop',
                autoControlsCombine: false,
                autoControlsSelector: null,

                // AUTO.
                auto: false,
                pause: 4000,
                autoStart: true,
                autoDirection: 'next',
                stopAutoOnClick: false,
                autoHover: false,
                autoDelay: 0,
                autoSlideForOnePage: false,

                // CAROUSEL.
                minSlides: 1,
                maxSlides: 1,
                moveSlides: 0,
                slideWidth: 0,
                shrinkItems: false,

                // CALLBACKS.
                onSliderLoad: function () {
                    return true;
                },
                onSlideBefore: function () {
                    return true;
                },
                onSlideAfter: function () {
                    return true;
                },
                onSlideNext: function () {
                    return true;
                },
                onSlidePrev: function () {
                    return true;
                },
                onSliderResize: function () {
                    return true;
                },
                onAutoChange: function () {
                    return true;
                } // Calls when auto slides starts and stops.
            };

            $.fn.bxSlider = function (options) {

                if (this.length === 0) {
                    return this;
                }

                // Support multiple elements.
                if (this.length > 1) {
                    this.each(function () {
                        $(this).bxSlider(options);
                    });
                    return this;
                }

                // Create a namespace to be used throughout the plugin.
                var slider = {},
                    // Set a reference to our slider element.
                    el = this,
                    // Get the original window dimens (thanks a lot IE).
                    windowWidth = $(window).width(),
                    windowHeight = $(window).height();

                // Return if slider is already initialized.
                if ($(el).data('bxSlider')) {
                    return;
                }

                /**
                 * ===================================================================================
                 * = PRIVATE FUNCTIONS
                 * ===================================================================================
                 */

                /**
                 * Initializes namespace settings to be used throughout plugin
                 */
                var init = function () {
                    // Return if slider is already initialized.
                    if ($(el).data('bxSlider')) {
                        return;
                    }
                    // Merge user-supplied options with the defaults.
                    slider.settings = $.extend({}, defaults, options);
                    // Parse slideWidth setting.
                    slider.settings.slideWidth = parseInt(slider.settings.slideWidth);
                    // Store the original children.
                    slider.children = el.children(slider.settings.slideSelector);
                    // Check if actual number of slides is less than minSlides / maxSlides.
                    if (slider.children.length < slider.settings.minSlides) {
                        slider.settings.minSlides = slider.children.length;
                    }
                    if (slider.children.length < slider.settings.maxSlides) {
                        slider.settings.maxSlides = slider.children.length;
                    }
                    // If random start, set the startSlide setting to random number.
                    if (slider.settings.randomStart) {
                        slider.settings.startSlide = Math.floor(Math.random() * slider.children.length);
                    }
                    // Store active slide information.
                    slider.active = {index: slider.settings.startSlide};
                    // Store if the slider is in carousel mode (displaying / moving multiple slides).
                    slider.carousel = slider.settings.minSlides > 1 || slider.settings.maxSlides > 1;
                    // If carousel, force preloadImages = 'all'.
                    if (slider.carousel) {
                        slider.settings.preloadImages = 'all';
                    }
                    // Calculate the min / max width thresholds based on min / max number of slides.
                    // used to setup and update carousel slides dimensions.
                    slider.minThreshold = (slider.settings.minSlides * slider.settings.slideWidth)
                        + ((slider.settings.minSlides - 1) * slider.settings.slideMargin);
                    slider.maxThreshold = (slider.settings.maxSlides * slider.settings.slideWidth)
                        + ((slider.settings.maxSlides - 1) * slider.settings.slideMargin);
                    // Store the current state of the slider (if currently animating, working is true).
                    slider.working = false;
                    // Initialize the controls object.
                    slider.controls = {};
                    // Initialize an auto interval.
                    slider.interval = null;
                    // Determine which property to use for transitions.
                    slider.animProp = slider.settings.mode === 'vertical' ? 'top' : 'left';
                    // Determine if hardware acceleration can be used.
                    slider.usingCSS = slider.settings.useCSS && slider.settings.mode !== 'fade' && (function () {
                        // Create our test div element.
                        var div = document.createElement('div'),
                            // Css transition properties.
                            props = ['WebkitPerspective', 'MozPerspective', 'OPerspective', 'msPerspective'];
                        // Test for each property.
                        for (var i = 0; i < props.length; i++) {
                            if (div.style[props[i]] !== undefined) {
                                slider.cssPrefix = props[i].replace('Perspective', '').toLowerCase();
                                slider.animProp = '-' + slider.cssPrefix + '-transform';
                                return true;
                            }
                        }
                        return false;
                    }());
                    // If vertical mode always make maxSlides and minSlides equal.
                    if (slider.settings.mode === 'vertical') {
                        slider.settings.maxSlides = slider.settings.minSlides;
                    }
                    // Save original style data.
                    el.data('origStyle', el.attr('style'));
                    el.children(slider.settings.slideSelector).each(function () {
                        $(this).data('origStyle', $(this).attr('style'));
                    });

                    // Perform all DOM / CSS modifications.
                    setup();
                };

                /**
                 * Performs all DOM and CSS modifications
                 */
                var setup = function () {
                    var preloadSelector = slider.children.eq(slider.settings.startSlide); // Set the default preload selector (visible).

                    // Wrap el in a wrapper.
                    el.wrap('<div class="' + slider.settings.wrapperClass + '"><div class="bx-viewport"></div></div>');
                    // Store a namespace reference to .bx-viewport.
                    slider.viewport = el.parent();

                    // Add aria-live if the setting is enabled and ticker mode is disabled.
                    if (slider.settings.ariaLive && !slider.settings.ticker) {
                        slider.viewport.attr('aria-live', 'polite');
                    }
                    // Add a loading div to display while images are loading.
                    slider.loader = $('<div class="bx-loading" />');
                    slider.viewport.prepend(slider.loader);
                    // Set el to a massive width, to hold any needed slides.
                    // also strip any margin and padding from el.
                    el.css({
                        width: slider.settings.mode === 'horizontal' ? (slider.children.length * 1000 + 215) + '%' : 'auto',
                        position: 'relative'
                    });
                    // If using CSS, add the easing property.
                    if (slider.usingCSS && slider.settings.easing) {
                        el.css('-' + slider.cssPrefix + '-transition-timing-function', slider.settings.easing);
                        // If not using CSS and no easing value was supplied, use the default JS animation easing (swing).
                    } else if (!slider.settings.easing) {
                        slider.settings.easing = 'swing';
                    }
                    // Make modifications to the viewport (.bx-viewport).
                    slider.viewport.css({
                        width: '100%',
                        overflow: 'hidden',
                        position: 'relative'
                    });
                    slider.viewport.parent().css({
                        maxWidth: getViewportMaxWidth()
                    });
                    // Apply css to all slider children.
                    slider.children.css({
                        // The float attribute is a reserved word in compressors like YUI compressor and need to be quoted #48.
                        'float': slider.settings.mode === 'horizontal' ? 'left' : 'none',
                        listStyle: 'none',
                        position: 'relative'
                    });
                    // Apply the calculated width after the float is applied to prevent scrollbar interference.
                    slider.children.css('width', getSlideWidth());
                    // If slideMargin is supplied, add the css.
                    if (slider.settings.mode === 'horizontal' && slider.settings.slideMargin > 0) {
                        slider.children.css('marginRight', slider.settings.slideMargin);
                    }
                    if (slider.settings.mode === 'vertical' && slider.settings.slideMargin > 0) {
                        slider.children.css('marginBottom', slider.settings.slideMargin);
                    }
                    // If "fade" mode, add positioning and z-index CSS.
                    if (slider.settings.mode === 'fade') {
                        slider.children.css({
                            position: 'absolute',
                            zIndex: 0,
                            display: 'none'
                        });
                        // Prepare the z-index on the showing element.
                        slider.children.eq(slider.settings.startSlide).css({zIndex: slider.settings.slideZIndex, display: 'block'});
                    }
                    // Create an element to contain all slider controls (pager, start / stop, etc).
                    slider.controls.el = $('<div class="bx-controls" />');
                    // If captions are requested, add them.
                    if (slider.settings.captions) {
                        appendCaptions();
                    }
                    // Check if startSlide is last slide.
                    slider.active.last = slider.settings.startSlide === getPagerQty() - 1;
                    // If video is true, set up the fitVids plugin.
                    if (slider.settings.video) {
                        el.fitVids();
                    }
                    // PreloadImages.
                    if (slider.settings.preloadImages === 'none') {
                        preloadSelector = null;
                    } else if (slider.settings.preloadImages === 'all' || slider.settings.ticker) {
                        preloadSelector = slider.children;
                    }
                    // Only check for control addition if not in "ticker" mode.
                    if (!slider.settings.ticker) {
                        // If controls are requested, add them.
                        if (slider.settings.controls) {
                            appendControls();
                        }
                        // If auto is true, and auto controls are requested, add them.
                        if (slider.settings.auto && slider.settings.autoControls) {
                            appendControlsAuto();
                        }
                        // If pager is requested, add it.
                        if (slider.settings.pager) {
                            appendPager();
                        }
                        // If any control option is requested, add the controls wrapper.
                        if (slider.settings.controls || slider.settings.autoControls || slider.settings.pager) {
                            slider.viewport.after(slider.controls.el);
                        }
                        // If ticker mode, do not allow a pager.
                    } else {
                        slider.settings.pager = false;
                    }
                    if (preloadSelector === null) {
                        start();
                    } else {
                        loadElements(preloadSelector, start);
                    }
                };

                var loadElements = function (selector, callback) {
                    var total = selector.find('img:not([src=""]), iframe').length,
                        count = 0;
                    if (total === 0) {
                        callback();
                        return;
                    }
                    selector.find('img:not([src=""]), iframe').each(function () {
                        $(this).one('load error', function () {
                            if (++count === total) {
                                callback();
                            }
                        }).each(function () {
                            if (this.complete || this.src == '') {
                                $(this).trigger('load');
                            }
                        });
                    });
                };

                /**
                 * Start the slider
                 */
                var start = function () {
                    // If infinite loop, prepare additional slides.
                    if (slider.settings.infiniteLoop && slider.settings.mode !== 'fade' && !slider.settings.ticker) {
                        var slice = slider.settings.mode === 'vertical' ? slider.settings.minSlides : slider.settings.maxSlides,
                            sliceAppend = slider.children.slice(0, slice).clone(true).addClass('bx-clone'),
                            slicePrepend = slider.children.slice(-slice).clone(true).addClass('bx-clone');
                        if (slider.settings.ariaHidden) {
                            sliceAppend.attr('aria-hidden', true);
                            slicePrepend.attr('aria-hidden', true);
                        }
                        el.append(sliceAppend).prepend(slicePrepend);
                    }
                    // Remove the loading DOM element.
                    slider.loader.remove();
                    // Set the left / top position of "el".
                    setSlidePosition();
                    // If "vertical" mode, always use adaptiveHeight to prevent odd behavior.
                    if (slider.settings.mode === 'vertical') {
                        slider.settings.adaptiveHeight = true;
                    }
                    // Set the viewport height.
                    slider.viewport.height(getViewportHeight());
                    // Make sure everything is positioned just right (same as a window resize).
                    el.redrawSlider();
                    // OnSliderLoad callback.
                    slider.settings.onSliderLoad.call(el, slider.active.index);
                    // Slider has been fully initialized.
                    slider.initialized = true;
                    // Add the resize call to the window.
                    if (slider.settings.responsive) {
                        $(window).on('resize', resizeWindow);
                    }
                    // If auto is true and has more than 1 page, start the show.
                    if (slider.settings.auto && slider.settings.autoStart && (getPagerQty() > 1
                        || slider.settings.autoSlideForOnePage)) {
                        initAuto();
                    }
                    // If ticker is true, start the ticker.
                    if (slider.settings.ticker) {
                        initTicker();
                    }
                    // If pager is requested, make the appropriate pager link active.
                    if (slider.settings.pager) {
                        updatePagerActive(slider.settings.startSlide);
                    }
                    // Check for any updates to the controls (like hideControlOnEnd updates).
                    if (slider.settings.controls) {
                        updateDirectionControls();
                    }
                    // If touchEnabled is true, setup the touch events.
                    if (slider.settings.touchEnabled && !slider.settings.ticker) {
                        initTouch();
                    }
                    // If keyboardEnabled is true, setup the keyboard events.
                    if (slider.settings.keyboardEnabled && !slider.settings.ticker) {
                        $(document).keydown(keyPress);
                    }
                };

                /**
                 * Returns the calculated height of the viewport, used to determine either adaptiveHeight or the maxHeight value
                 */
                var getViewportHeight = function () {
                    var height = 0;
                    // First determine which children (slides) should be used in our height calculation.
                    var children = $();
                    // If mode is not "vertical" and adaptiveHeight is false, include all children.
                    if (slider.settings.mode !== 'vertical' && !slider.settings.adaptiveHeight) {
                        children = slider.children;
                    } else {
                        // If not carousel, return the single active child.
                        if (!slider.carousel) {
                            children = slider.children.eq(slider.active.index);
                            // If carousel, return a slice of children.
                        } else {
                            // Get the individual slide index.
                            var currentIndex = slider.settings.moveSlides === 1 ? slider.active.index
                                : slider.active.index * getMoveBy();
                            // Add the current slide to the children.
                            children = slider.children.eq(currentIndex);
                            // Cycle through the remaining "showing" slides.
                            for (i = 1; i <= slider.settings.maxSlides - 1; i++) {
                                // If looped back to the start.
                                if (currentIndex + i >= slider.children.length) {
                                    children = children.add(slider.children.eq(i - 1));
                                } else {
                                    children = children.add(slider.children.eq(currentIndex + i));
                                }
                            }
                        }
                    }
                    // If "vertical" mode, calculate the sum of the heights of the children.
                    if (slider.settings.mode === 'vertical') {
                        children.each(function (index) {
                            height += $(this).outerHeight();
                        });
                        // Add user-supplied margins.
                        if (slider.settings.slideMargin > 0) {
                            height += slider.settings.slideMargin * (slider.settings.minSlides - 1);
                        }
                        // If not "vertical" mode, calculate the max height of the children.
                    } else {
                        height = Math.max.apply(Math, children.map(function () {
                            return $(this).outerHeight(false);
                        }).get());
                    }

                    if (slider.viewport.css('box-sizing') === 'border-box') {
                        height += parseFloat(slider.viewport.css('padding-top'))
                            + parseFloat(slider.viewport.css('padding-bottom'))
                            + parseFloat(slider.viewport.css('border-top-width'))
                            + parseFloat(slider.viewport.css('border-bottom-width'));
                    } else if (slider.viewport.css('box-sizing') === 'padding-box') {
                        height += parseFloat(slider.viewport.css('padding-top'))
                            + parseFloat(slider.viewport.css('padding-bottom'));
                    }

                    return height;
                };

                /**
                 * Returns the calculated width to be used for the outer wrapper / viewport
                 */
                var getViewportMaxWidth = function () {
                    var width = '100%';
                    if (slider.settings.slideWidth > 0) {
                        if (slider.settings.mode === 'horizontal') {
                            width = (slider.settings.maxSlides * slider.settings.slideWidth)
                                + ((slider.settings.maxSlides - 1) * slider.settings.slideMargin);
                        } else {
                            width = slider.settings.slideWidth;
                        }
                    }
                    return width;
                };

                /**
                 * Returns the calculated width to be applied to each slide
                 */
                var getSlideWidth = function () {
                    var newElWidth = slider.settings.slideWidth, // Start with any user-supplied slide width.
                        wrapWidth = slider.viewport.width(); // Get the current viewport width.
                    // if slide width was not supplied, or is larger than the viewport use the viewport width.
                    if (slider.settings.slideWidth === 0 ||
                        (slider.settings.slideWidth > wrapWidth && !slider.carousel) ||
                        slider.settings.mode === 'vertical') {
                        newElWidth = wrapWidth;
                        // If carousel, use the thresholds to determine the width.
                    } else if (slider.settings.maxSlides > 1 && slider.settings.mode === 'horizontal') {
                        if (wrapWidth > slider.maxThreshold) {
                            return newElWidth;
                        } else if (wrapWidth < slider.minThreshold) {
                            newElWidth = (wrapWidth - (slider.settings.slideMargin * (slider.settings.minSlides - 1)))
                                / slider.settings.minSlides;
                        } else if (slider.settings.shrinkItems) {
                            newElWidth = Math.floor((wrapWidth + slider.settings.slideMargin)
                                / (Math.ceil((wrapWidth + slider.settings.slideMargin)
                                    / (newElWidth + slider.settings.slideMargin)))
                                - slider.settings.slideMargin);
                        }
                    }
                    return newElWidth;
                };

                /**
                 * Returns the number of slides currently visible in the viewport (includes partially visible slides)
                 */
                var getNumberSlidesShowing = function () {
                    var slidesShowing = 1,
                        childWidth = null;
                    if (slider.settings.mode === 'horizontal' && slider.settings.slideWidth > 0) {
                        // If viewport is smaller than minThreshold, return minSlides.
                        if (slider.viewport.width() < slider.minThreshold) {
                            slidesShowing = slider.settings.minSlides;
                            // If viewport is larger than maxThreshold, return maxSlides.
                        } else if (slider.viewport.width() > slider.maxThreshold) {
                            slidesShowing = slider.settings.maxSlides;
                            // If viewport is between min / max thresholds, divide viewport width by first child width.
                        } else {
                            childWidth = slider.children.first().width() + slider.settings.slideMargin;
                            slidesShowing = Math.floor((slider.viewport.width() +
                                slider.settings.slideMargin) / childWidth) || 1;
                        }
                        // If "vertical" mode, slides showing will always be minSlides.
                    } else if (slider.settings.mode === 'vertical') {
                        slidesShowing = slider.settings.minSlides;
                    }
                    return slidesShowing;
                };

                /**
                 * Returns the number of pages (one full viewport of slides is one "page")
                 */
                var getPagerQty = function () {
                    var pagerQty = 0,
                        breakPoint = 0,
                        counter = 0;
                    // If moveSlides is specified by the user.
                    if (slider.settings.moveSlides > 0) {
                        if (slider.settings.infiniteLoop) {
                            pagerQty = Math.ceil(slider.children.length / getMoveBy());
                        } else {
                            // When breakpoint goes above children length, counter is the number of pages.
                            while (breakPoint < slider.children.length) {
                                ++pagerQty;
                                breakPoint = counter + getNumberSlidesShowing();
                                counter += slider.settings.moveSlides <= getNumberSlidesShowing()
                                    ? slider.settings.moveSlides : getNumberSlidesShowing();
                            }
                            return counter;
                        }
                        // If moveSlides is 0 (auto) divide children length by sides showing, then round up.
                    } else {
                        pagerQty = Math.ceil(slider.children.length / getNumberSlidesShowing());
                    }
                    return pagerQty;
                };

                /**
                 * Returns the number of individual slides by which to shift the slider
                 */
                var getMoveBy = function () {
                    // If moveSlides was set by the user and moveSlides is less than number of slides showing.
                    if (slider.settings.moveSlides > 0 && slider.settings.moveSlides <= getNumberSlidesShowing()) {
                        return slider.settings.moveSlides;
                    }
                    // If moveSlides is 0 (auto).
                    return getNumberSlidesShowing();
                };

                /**
                 * Sets the slider's (el) left or top position
                 */
                var setSlidePosition = function () {
                    var position, lastChild, lastShowingIndex;
                    // If last slide, not infinite loop, and number of children is larger than specified maxSlides.
                    if (slider.children.length > slider.settings.maxSlides && slider.active.last && !slider.settings.infiniteLoop) {
                        if (slider.settings.mode === 'horizontal') {
                            // Get the last child's position.
                            lastChild = slider.children.last();
                            position = lastChild.position();
                            // Set the left position.
                            setPositionProperty(-(position.left - (slider.viewport.width() - lastChild.outerWidth())), 'reset', 0);
                        } else if (slider.settings.mode === 'vertical') {
                            // Get the last showing index's position.
                            lastShowingIndex = slider.children.length - slider.settings.minSlides;
                            position = slider.children.eq(lastShowingIndex).position();
                            // Set the top position.
                            setPositionProperty(-position.top, 'reset', 0);
                        }
                        // If not last slide.
                    } else {
                        // Get the position of the first showing slide.
                        position = slider.children.eq(slider.active.index * getMoveBy()).position();
                        // Check for last slide.
                        if (slider.active.index === getPagerQty() - 1) {
                            slider.active.last = true;
                        }
                        // Set the respective position.
                        if (position !== undefined) {
                            if (slider.settings.mode === 'horizontal') {
                                setPositionProperty(-position.left, 'reset', 0);
                            } else if (slider.settings.mode === 'vertical') {
                                setPositionProperty(-position.top, 'reset', 0);
                            }
                        }
                    }
                };

                /**
                 * Sets the el's animating property position (which in turn will sometimes animate el).
                 * If using CSS, sets the transform property. If not using CSS, sets the top / left property.
                 *
                 * @param value (int)
                 *  - the animating property's value
                 *
                 * @param type (string) 'slide', 'reset', 'ticker'
                 *  - the type of instance for which the function is being
                 *
                 * @param duration (int)
                 *  - the amount of time (in ms) the transition should occupy
                 *
                 * @param params (array) optional
                 *  - an optional parameter containing any variables that need to be passed in
                 */
                var setPositionProperty = function (value, type, duration, params) {
                    var animateObj, propValue;
                    // Use CSS transform.
                    if (slider.usingCSS) {
                        // Determine the translate3d value.
                        propValue = slider.settings.mode === 'vertical' ? 'translate3d(0, ' + value + 'px, 0)'
                            : 'translate3d(' + value + 'px, 0, 0)';
                        // Add the CSS transition-duration.
                        el.css('-' + slider.cssPrefix + '-transition-duration', duration / 1000 + 's');
                        if (type === 'slide') {
                            // Set the property value.
                            el.css(slider.animProp, propValue);
                            if (duration !== 0) {
                                // Add a callback method - executes when CSS transition completes.
                                el.on('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function (e) {
                                    // Make sure it's the correct one.
                                    if (!$(e.target).is(el)) {
                                        return;
                                    }
                                    // Remove the callback.
                                    el.off('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd');
                                    updateAfterSlideTransition();
                                });
                            } else { // Duration = 0.
                                updateAfterSlideTransition();
                            }
                        } else if (type === 'reset') {
                            el.css(slider.animProp, propValue);
                        } else if (type === 'ticker') {
                            // Make the transition use 'linear'.
                            el.css('-' + slider.cssPrefix + '-transition-timing-function', 'linear');
                            el.css(slider.animProp, propValue);
                            if (duration !== 0) {
                                el.on('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function (e) {
                                    // Make sure it's the correct one.
                                    if (!$(e.target).is(el)) {
                                        return;
                                    }
                                    // Remove the callback.
                                    el.off('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd');
                                    // Reset the position.
                                    setPositionProperty(params.resetValue, 'reset', 0);
                                    // Start the loop again.
                                    tickerLoop();
                                });
                            } else { // Duration = 0.
                                setPositionProperty(params.resetValue, 'reset', 0);
                                tickerLoop();
                            }
                        }
                        // Use JS animate.
                    } else {
                        animateObj = {};
                        animateObj[slider.animProp] = value;
                        if (type === 'slide') {
                            el.animate(animateObj, duration, slider.settings.easing, function () {
                                updateAfterSlideTransition();
                            });
                        } else if (type === 'reset') {
                            el.css(slider.animProp, value);
                        } else if (type === 'ticker') {
                            el.animate(animateObj, duration, 'linear', function () {
                                setPositionProperty(params.resetValue, 'reset', 0);
                                // Run the recursive loop after animation.
                                tickerLoop();
                            });
                        }
                    }
                };

                /**
                 * Populates the pager with proper amount of pages
                 */
                var populatePager = function () {
                    var pagerHtml = '',
                        linkContent = '',
                        pagerQty = getPagerQty();
                    // Loop through each pager item.
                    for (var i = 0; i < pagerQty; i++) {
                        linkContent = '';
                        // If a buildPager function is supplied, use it to get pager link value, else use index + 1.
                        if (slider.settings.buildPager && $.isFunction(slider.settings.buildPager) || slider.settings.pagerCustom) {
                            linkContent = slider.settings.buildPager(i);
                            slider.pagerEl.addClass('bx-custom-pager');
                        } else {
                            linkContent = i + 1;
                            slider.pagerEl.addClass('bx-default-pager');
                        }
                        // Var linkContent = slider.settings.buildPager && $.isFunction(slider.settings.buildPager)
                        // ? slider.settings.buildPager(i) : i + 1;.
                        // add the markup to the string.
                        pagerHtml += '<div class="bx-pager-item"><a href="" data-slide-index="'
                            + i + '" class="bx-pager-link">' + linkContent + '</a></div>';
                    }
                    // Populate the pager element with pager links.
                    slider.pagerEl.html(pagerHtml);
                };

                /**
                 * Appends the pager to the controls element
                 */
                var appendPager = function () {
                    if (!slider.settings.pagerCustom) {
                        // Create the pager DOM element.
                        slider.pagerEl = $('<div class="bx-pager" />');
                        // If a pager selector was supplied, populate it with the pager.
                        if (slider.settings.pagerSelector) {
                            $(slider.settings.pagerSelector).html(slider.pagerEl);
                            // If no pager selector was supplied, add it after the wrapper.
                        } else {
                            slider.controls.el.addClass('bx-has-pager').append(slider.pagerEl);
                        }
                        // Populate the pager.
                        populatePager();
                    } else {
                        slider.pagerEl = $(slider.settings.pagerCustom);
                    }
                    // Assign the pager click binding.
                    slider.pagerEl.on('click touchend', 'a', clickPagerBind);
                };

                /**
                 * Appends prev / next controls to the controls element
                 */
                var appendControls = function () {
                    slider.controls.next = $('<a class="bx-next" href="">' + slider.settings.nextText + '</a>');
                    slider.controls.prev = $('<a class="bx-prev" href="">' + slider.settings.prevText + '</a>');
                    // Add click actions to the controls.
                    slider.controls.next.on('click touchend', clickNextBind);
                    slider.controls.prev.on('click touchend', clickPrevBind);
                    // If nextSelector was supplied, populate it.
                    if (slider.settings.nextSelector) {
                        $(slider.settings.nextSelector).append(slider.controls.next);
                    }
                    // If prevSelector was supplied, populate it.
                    if (slider.settings.prevSelector) {
                        $(slider.settings.prevSelector).append(slider.controls.prev);
                    }
                    // If no custom selectors were supplied.
                    if (!slider.settings.nextSelector && !slider.settings.prevSelector) {
                        // Add the controls to the DOM.
                        slider.controls.directionEl = $('<div class="bx-controls-direction" />');
                        // Add the control elements to the directionEl.
                        slider.controls.directionEl.append(slider.controls.prev).append(slider.controls.next);
                        // Slider.viewport.append(slider.controls.directionEl);.
                        slider.controls.el.addClass('bx-has-controls-direction').append(slider.controls.directionEl);
                    }
                };

                /**
                 * Appends start / stop auto controls to the controls element
                 */
                var appendControlsAuto = function () {
                    slider.controls.start = $('<div class="bx-controls-auto-item"><a class="bx-start" href="">'
                        + slider.settings.startText + '</a></div>');
                    slider.controls.stop = $('<div class="bx-controls-auto-item"><a class="bx-stop" href="">'
                        + slider.settings.stopText + '</a></div>');
                    // Add the controls to the DOM.
                    slider.controls.autoEl = $('<div class="bx-controls-auto" />');
                    // On click actions to the controls.
                    slider.controls.autoEl.on('click', '.bx-start', clickStartBind);
                    slider.controls.autoEl.on('click', '.bx-stop', clickStopBind);
                    // If autoControlsCombine, insert only the "start" control.
                    if (slider.settings.autoControlsCombine) {
                        slider.controls.autoEl.append(slider.controls.start);
                        // If autoControlsCombine is false, insert both controls.
                    } else {
                        slider.controls.autoEl.append(slider.controls.start).append(slider.controls.stop);
                    }
                    // If auto controls selector was supplied, populate it with the controls.
                    if (slider.settings.autoControlsSelector) {
                        $(slider.settings.autoControlsSelector).html(slider.controls.autoEl);
                        // If auto controls selector was not supplied, add it after the wrapper.
                    } else {
                        slider.controls.el.addClass('bx-has-controls-auto').append(slider.controls.autoEl);
                    }
                    // Update the auto controls.
                    updateAutoControls(slider.settings.autoStart ? 'stop' : 'start');
                };

                /**
                 * Appends image captions to the DOM
                 */
                var appendCaptions = function () {
                    // Cycle through each child.
                    slider.children.each(function (index) {
                        // Get the image title attribute.
                        var title = $(this).find('img:first').attr('title');
                        // Append the caption.
                        if (title !== undefined && ('' + title).length) {
                            $(this).append('<div class="bx-caption"><span>' + title + '</span></div>');
                        }
                    });
                };

                /**
                 * Click next binding
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var clickNextBind = function (e) {
                    e.preventDefault();
                    if (slider.controls.el.hasClass('disabled')) {
                        return;
                    }
                    // If auto show is running, stop it.
                    if (slider.settings.auto && slider.settings.stopAutoOnClick) {
                        el.stopAuto();
                    }
                    el.goToNextSlide();
                };

                /**
                 * Click prev binding
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var clickPrevBind = function (e) {
                    e.preventDefault();
                    if (slider.controls.el.hasClass('disabled')) {
                        return;
                    }
                    // If auto show is running, stop it.
                    if (slider.settings.auto && slider.settings.stopAutoOnClick) {
                        el.stopAuto();
                    }
                    el.goToPrevSlide();
                };

                /**
                 * Click start binding
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var clickStartBind = function (e) {
                    el.startAuto();
                    e.preventDefault();
                };

                /**
                 * Click stop binding
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var clickStopBind = function (e) {
                    el.stopAuto();
                    e.preventDefault();
                };

                /**
                 * Click pager binding
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var clickPagerBind = function (e) {
                    var pagerLink, pagerIndex;
                    e.preventDefault();
                    if (slider.controls.el.hasClass('disabled')) {
                        return;
                    }
                    // If auto show is running, stop it.
                    if (slider.settings.auto && slider.settings.stopAutoOnClick) {
                        el.stopAuto();
                    }
                    pagerLink = $(e.currentTarget);
                    if (pagerLink.attr('data-slide-index') !== undefined) {
                        pagerIndex = parseInt(pagerLink.attr('data-slide-index'));
                        // If clicked pager link is not active, continue with the goToSlide call.
                        if (pagerIndex !== slider.active.index) {
                            el.goToSlide(pagerIndex);
                        }
                    }
                };

                /**
                 * Updates the pager links with an active class
                 *
                 * @param slideIndex (int)
                 *  - index of slide to make active
                 */
                var updatePagerActive = function (slideIndex) {
                    // If "short" pager type.
                    var len = slider.children.length; // Nb of children.
                    if (slider.settings.pagerType === 'short') {
                        if (slider.settings.maxSlides > 1) {
                            len = Math.ceil(slider.children.length / slider.settings.maxSlides);
                        }
                        slider.pagerEl.html((slideIndex + 1) + slider.settings.pagerShortSeparator + len);
                        return;
                    }
                    // Remove all pager active classes.
                    slider.pagerEl.find('a').removeClass('active');
                    // Apply the active class for all pagers.
                    slider.pagerEl.each(function (i, el) {
                        $(el).find('a').eq(slideIndex).addClass('active');
                    });
                };

                /**
                 * Performs needed actions after a slide transition
                 */
                var updateAfterSlideTransition = function () {
                    // If infinite loop is true.
                    if (slider.settings.infiniteLoop) {
                        var position = '';
                        // First slide.
                        if (slider.active.index === 0) {
                            // Set the new position.
                            position = slider.children.eq(0).position();
                            // Carousel, last slide.
                        } else if (slider.active.index === getPagerQty() - 1 && slider.carousel) {
                            position = slider.children.eq((getPagerQty() - 1) * getMoveBy()).position();
                            // Last slide.
                        } else if (slider.active.index === slider.children.length - 1) {
                            position = slider.children.eq(slider.children.length - 1).position();
                        }
                        if (position) {
                            if (slider.settings.mode === 'horizontal') {
                                setPositionProperty(-position.left, 'reset', 0);
                            } else if (slider.settings.mode === 'vertical') {
                                setPositionProperty(-position.top, 'reset', 0);
                            }
                        }
                    }
                    // Declare that the transition is complete.
                    slider.working = false;
                    // OnSlideAfter callback.
                    slider.settings.onSlideAfter.call(el, slider.children.eq(slider.active.index),
                        slider.oldIndex, slider.active.index);
                };

                /**
                 * Updates the auto controls state (either active, or combined switch)
                 *
                 * @param state (string) "start", "stop"
                 *  - the new state of the auto show
                 */
                var updateAutoControls = function (state) {
                    // If autoControlsCombine is true, replace the current control with the new state.
                    if (slider.settings.autoControlsCombine) {
                        slider.controls.autoEl.html(slider.controls[state]);
                        // If autoControlsCombine is false, apply the "active" class to the appropriate control.
                    } else {
                        slider.controls.autoEl.find('a').removeClass('active');
                        slider.controls.autoEl.find('a:not(.bx-' + state + ')').addClass('active');
                    }
                };

                /**
                 * Updates the direction controls (checks if either should be hidden)
                 */
                var updateDirectionControls = function () {
                    if (getPagerQty() === 1) {
                        slider.controls.prev.addClass('disabled');
                        slider.controls.next.addClass('disabled');
                    } else if (!slider.settings.infiniteLoop && slider.settings.hideControlOnEnd) {
                        // If first slide.
                        if (slider.active.index === 0) {
                            slider.controls.prev.addClass('disabled');
                            slider.controls.next.removeClass('disabled');
                            // If last slide.
                        } else if (slider.active.index === getPagerQty() - 1) {
                            slider.controls.next.addClass('disabled');
                            slider.controls.prev.removeClass('disabled');
                            // If any slide in the middle.
                        } else {
                            slider.controls.prev.removeClass('disabled');
                            slider.controls.next.removeClass('disabled');
                        }
                    }
                };
                /* Auto start and stop functions */
                var windowFocusHandler = function () {
                    el.startAuto();
                };
                var windowBlurHandler = function () {
                    el.stopAuto();
                };
                /**
                 * Initializes the auto process
                 */
                var initAuto = function () {
                    // If autoDelay was supplied, launch the auto show using a setTimeout() call.
                    if (slider.settings.autoDelay > 0) {
                        setTimeout(el.startAuto, slider.settings.autoDelay);
                        // If autoDelay was not supplied, start the auto show normally.
                    } else {
                        el.startAuto();

                        // Add focus and blur events to ensure its running if timeout gets paused.
                        $(window).focus(windowFocusHandler).blur(windowBlurHandler);
                    }
                    // If autoHover is requested.
                    if (slider.settings.autoHover) {
                        // On el hover.
                        el.hover(function () {
                            // If the auto show is currently playing (has an active interval).
                            if (slider.interval) {
                                // Stop the auto show and pass true argument which will prevent control update.
                                el.stopAuto(true);
                                // Create a new autoPaused value which will be used by the relative "mouseout" event.
                                slider.autoPaused = true;
                            }
                        }, function () {
                            // If the autoPaused value was created be the prior "mouseover" event.
                            if (slider.autoPaused) {
                                // Start the auto show and pass true argument which will prevent control update.
                                el.startAuto(true);
                                // Reset the autoPaused value.
                                slider.autoPaused = null;
                            }
                        });
                    }
                };

                /**
                 * Initializes the ticker process
                 */
                var initTicker = function () {
                    var startPosition = 0,
                        position, transform, value, idx, ratio, property, newSpeed, totalDimens;
                    // If autoDirection is "next", append a clone of the entire slider.
                    if (slider.settings.autoDirection === 'next') {
                        el.append(slider.children.clone().addClass('bx-clone'));
                        // If autoDirection is "prev", prepend a clone of the entire slider, and set the left position.
                    } else {
                        el.prepend(slider.children.clone().addClass('bx-clone'));
                        position = slider.children.first().position();
                        startPosition = slider.settings.mode === 'horizontal' ? -position.left : -position.top;
                    }
                    setPositionProperty(startPosition, 'reset', 0);
                    // Do not allow controls in ticker mode.
                    slider.settings.pager = false;
                    slider.settings.controls = false;
                    slider.settings.autoControls = false;
                    // If autoHover is requested.
                    if (slider.settings.tickerHover) {
                        if (slider.usingCSS) {
                            idx = slider.settings.mode === 'horizontal' ? 4 : 5;
                            slider.viewport.hover(function () {
                                transform = el.css('-' + slider.cssPrefix + '-transform');
                                value = parseFloat(transform.split(',')[idx]);
                                setPositionProperty(value, 'reset', 0);
                            }, function () {
                                totalDimens = 0;
                                slider.children.each(function (index) {
                                    totalDimens += slider.settings.mode === 'horizontal' ? $(this).outerWidth(true)
                                        : $(this).outerHeight(true);
                                });
                                // Calculate the speed ratio (used to determine the new speed to finish the paused animation).
                                ratio = slider.settings.speed / totalDimens;
                                // Determine which property to use.
                                property = slider.settings.mode === 'horizontal' ? 'left' : 'top';
                                // Calculate the new speed.
                                newSpeed = ratio * (totalDimens - (Math.abs(parseInt(value))));
                                tickerLoop(newSpeed);
                            });
                        } else {
                            // On el hover.
                            slider.viewport.hover(function () {
                                el.stop();
                            }, function () {
                                // Calculate the total width of children (used to calculate the speed ratio).
                                totalDimens = 0;
                                slider.children.each(function (index) {
                                    totalDimens += slider.settings.mode === 'horizontal' ? $(this).outerWidth(true) : $(this).outerHeight(true);
                                });
                                // Calculate the speed ratio (used to determine the new speed to finish the paused animation).
                                ratio = slider.settings.speed / totalDimens;
                                // Determine which property to use.
                                property = slider.settings.mode === 'horizontal' ? 'left' : 'top';
                                // Calculate the new speed.
                                newSpeed = ratio * (totalDimens - (Math.abs(parseInt(el.css(property)))));
                                tickerLoop(newSpeed);
                            });
                        }
                    }
                    // Start the ticker loop.
                    tickerLoop();
                };

                /**
                 * Runs a continuous loop, news ticker-style
                 */
                var tickerLoop = function (resumeSpeed) {
                    var speed = resumeSpeed ? resumeSpeed : slider.settings.speed,
                        position = {left: 0, top: 0},
                        reset = {left: 0, top: 0},
                        animateProperty, resetValue, params;

                    // If "next" animate left position to last child, then reset left to 0.
                    if (slider.settings.autoDirection === 'next') {
                        position = el.find('.bx-clone').first().position();
                        // If "prev" animate left position to 0, then reset left to first non-clone child.
                    } else {
                        reset = slider.children.first().position();
                    }
                    animateProperty = slider.settings.mode === 'horizontal' ? -position.left : -position.top;
                    resetValue = slider.settings.mode === 'horizontal' ? -reset.left : -reset.top;
                    params = {resetValue: resetValue};
                    setPositionProperty(animateProperty, 'ticker', speed, params);
                };

                /**
                 * Check if el is on screen
                 */
                var isOnScreen = function (el) {
                    var win = $(window),
                        viewport = {top: win.scrollTop(), left: win.scrollLeft()},
                        bounds = el.offset();
                    viewport.right = viewport.left + win.width();
                    viewport.bottom = viewport.top + win.height();
                    bounds.right = bounds.left + el.outerWidth();
                    bounds.bottom = bounds.top + el.outerHeight();

                    return (!(viewport.right < bounds.left || viewport.left > bounds.right
                        || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
                };

                /**
                 * Initializes keyboard events
                 */
                var keyPress = function (e) {
                    var activeElementTag = document.activeElement.tagName.toLowerCase(),
                        tagFilters = 'input|textarea',
                        p = new RegExp(activeElementTag, ['i']),
                        result = p.exec(tagFilters);

                    if (result == null && isOnScreen(el)) {
                        if (e.keyCode === 39) {
                            clickNextBind(e);
                            return false;
                        } else if (e.keyCode === 37) {
                            clickPrevBind(e);
                            return false;
                        }
                    }
                };

                /**
                 * Initializes touch events
                 */
                var initTouch = function () {
                    // Initialize object to contain all touch values.
                    slider.touch = {
                        start: {x: 0, y: 0},
                        end: {x: 0, y: 0}
                    };
                    slider.viewport.on('touchstart MSPointerDown pointerdown', onTouchStart);

                    // For browsers that have implemented pointer events and fire a click after.
                    // every pointerup regardless of whether pointerup is on same screen location as pointerdown or not.
                    slider.viewport.on('click', '.bxslider a', function (e) {
                        if (slider.viewport.hasClass('click-disabled')) {
                            e.preventDefault();
                            slider.viewport.removeClass('click-disabled');
                        }
                    });
                };

                /**
                 * Event handler for "touchstart"
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var onTouchStart = function (e) {
                    // Watch only for left mouse, touch contact and pen contact.
                    // touchstart event object doesn`t have button property.
                    if (e.type !== 'touchstart' && e.button !== 0) {
                        return;
                    }
                    e.preventDefault();
                    // Disable slider controls while user is interacting with slides to avoid slider
                    // freeze that happens on touch devices when a slide swipe happens immediately
                    // after interacting with slider controls.
                    slider.controls.el.addClass('disabled');

                    if (slider.working) {
                        slider.controls.el.removeClass('disabled');
                    } else {
                        // Record the original position when touch starts.
                        slider.touch.originalPos = el.position();
                        var orig = e.originalEvent,
                            touchPoints = (typeof orig.changedTouches !== 'undefined') ? orig.changedTouches : [orig];
                        var chromePointerEvents = typeof PointerEvent === 'function';
                        if (chromePointerEvents) {
                            if (orig.pointerId === undefined) {
                                return;
                            }
                        }
                        // Record the starting touch x, y coordinates.
                        slider.touch.start.x = touchPoints[0].pageX;
                        slider.touch.start.y = touchPoints[0].pageY;

                        if (slider.viewport.get(0).setPointerCapture) {
                            slider.pointerId = orig.pointerId;
                            slider.viewport.get(0).setPointerCapture(slider.pointerId);
                        }
                        // Store original event data for click fixation.
                        slider.originalClickTarget = orig.originalTarget || orig.target;
                        slider.originalClickButton = orig.button;
                        slider.originalClickButtons = orig.buttons;
                        slider.originalEventType = orig.type;
                        // At this moment we don`t know what it is click or swipe.
                        slider.hasMove = false;
                        // On a "touchmove" event to the viewport.
                        slider.viewport.on('touchmove MSPointerMove pointermove', onTouchMove);
                        // On a "touchend" event to the viewport.
                        slider.viewport.on('touchend MSPointerUp pointerup', onTouchEnd);
                        slider.viewport.on('MSPointerCancel pointercancel', onPointerCancel);
                    }
                };

                /**
                 * Cancel Pointer for Windows Phone
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var onPointerCancel = function (e) {
                    e.preventDefault();
                    /* OnPointerCancel handler is needed to deal with situations when a touchend
                    doesn't fire after a touchstart (this happens on windows phones only) */
                    setPositionProperty(slider.touch.originalPos.left, 'reset', 0);

                    // Remove handlers.
                    slider.controls.el.removeClass('disabled');
                    slider.viewport.off('MSPointerCancel pointercancel', onPointerCancel);
                    slider.viewport.off('touchmove MSPointerMove pointermove', onTouchMove);
                    slider.viewport.off('touchend MSPointerUp pointerup', onTouchEnd);
                    if (slider.viewport.get(0).releasePointerCapture) {
                        slider.viewport.get(0).releasePointerCapture(slider.pointerId);
                    }
                };

                /**
                 * Event handler for "touchmove"
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var onTouchMove = function (e) {
                    var orig = e.originalEvent,
                        touchPoints = (typeof orig.changedTouches !== 'undefined') ? orig.changedTouches : [orig],
                        // If scrolling on y axis, do not prevent default.
                        xMovement = Math.abs(touchPoints[0].pageX - slider.touch.start.x),
                        yMovement = Math.abs(touchPoints[0].pageY - slider.touch.start.y),
                        value = 0,
                        change = 0;
                    // This is swipe.
                    slider.hasMove = true;

                    // X axis swipe.
                    if ((xMovement * 3) > yMovement && slider.settings.preventDefaultSwipeX) {
                        e.preventDefault();
                        // Y axis swipe.
                    } else if ((yMovement * 3) > xMovement && slider.settings.preventDefaultSwipeY) {
                        e.preventDefault();
                    }
                    if (e.type !== 'touchmove') {
                        e.preventDefault();
                    }

                    if (slider.settings.mode !== 'fade' && slider.settings.oneToOneTouch) {
                        // If horizontal, drag along x axis.
                        if (slider.settings.mode === 'horizontal') {
                            change = touchPoints[0].pageX - slider.touch.start.x;
                            value = slider.touch.originalPos.left + change;
                            // If vertical, drag along y axis.
                        } else {
                            change = touchPoints[0].pageY - slider.touch.start.y;
                            value = slider.touch.originalPos.top + change;
                        }
                        setPositionProperty(value, 'reset', 0);
                    }
                };

                /**
                 * Event handler for "touchend"
                 *
                 * @param e (event)
                 *  - DOM event object
                 */
                var onTouchEnd = function (e) {
                    e.preventDefault();
                    slider.viewport.off('touchmove MSPointerMove pointermove', onTouchMove);
                    // Enable slider controls as soon as user stops interacing with slides.
                    slider.controls.el.removeClass('disabled');
                    var orig = e.originalEvent,
                        touchPoints = (typeof orig.changedTouches !== 'undefined') ? orig.changedTouches : [orig],
                        value = 0,
                        distance = 0;
                    // Record end x, y positions.
                    slider.touch.end.x = touchPoints[0].pageX;
                    slider.touch.end.y = touchPoints[0].pageY;
                    // If fade mode, check if absolute x distance clears the threshold.
                    if (slider.settings.mode === 'fade') {
                        distance = Math.abs(slider.touch.start.x - slider.touch.end.x);
                        if (distance >= slider.settings.swipeThreshold) {
                            if (slider.touch.start.x > slider.touch.end.x) {
                                el.goToNextSlide();
                            } else {
                                el.goToPrevSlide();
                            }
                            el.stopAuto();
                        }
                        // Not fade mode.
                    } else {
                        // Calculate distance and el's animate property.
                        if (slider.settings.mode === 'horizontal') {
                            distance = slider.touch.end.x - slider.touch.start.x;
                            value = slider.touch.originalPos.left;
                        } else {
                            distance = slider.touch.end.y - slider.touch.start.y;
                            value = slider.touch.originalPos.top;
                        }
                        // If not infinite loop and first / last slide, do not attempt a slide transition.
                        if (!slider.settings.infiniteLoop && ((slider.active.index === 0 && distance > 0)
                            || (slider.active.last && distance < 0))) {
                            setPositionProperty(value, 'reset', 200);
                        } else {
                            // Check if distance clears threshold.
                            if (Math.abs(distance) >= slider.settings.swipeThreshold) {
                                if (distance < 0) {
                                    el.goToNextSlide();
                                } else {
                                    el.goToPrevSlide();
                                }
                                el.stopAuto();
                            } else {
                                // El.animate(property, 200);.
                                setPositionProperty(value, 'reset', 200);
                            }
                        }
                    }
                    slider.viewport.off('touchend MSPointerUp pointerup', onTouchEnd);

                    if (slider.viewport.get(0).releasePointerCapture) {
                        slider.viewport.get(0).releasePointerCapture(slider.pointerId);
                    }
                    // If slider had swipe with left mouse, touch contact and pen contact.
                    if (slider.hasMove === false && (slider.originalClickButton === 0 || slider.originalEventType === 'touchstart')) {
                        // Trigger click event (fix for Firefox59 and PointerEvent standard compatibility).
                        $(slider.originalClickTarget).trigger({
                            type: 'click',
                            button: slider.originalClickButton,
                            buttons: slider.originalClickButtons
                        });
                    }
                };

                /**
                 * Window resize event callback
                 */
                var resizeWindow = function (e) {
                    // Don't do anything if slider isn't initialized.
                    if (!slider.initialized) {
                        return;
                    }
                    // Delay if slider working.
                    if (slider.working) {
                        window.setTimeout(resizeWindow, 10);
                    } else {
                        // Get the new window dimens (again, thank you IE).
                        var windowWidthNew = $(window).width(),
                            windowHeightNew = $(window).height();
                        // Make sure that it is a true window resize.
                        // *we must check this because our dinosaur friend IE fires a window resize event when certain DOM elements.
                        // are resized. Can you just die already?*.
                        if (windowWidth !== windowWidthNew || windowHeight !== windowHeightNew) {
                            // Set the new window dimens.
                            windowWidth = windowWidthNew;
                            windowHeight = windowHeightNew;
                            // Update all dynamic elements.
                            el.redrawSlider();
                            // Call user resize handler.
                            slider.settings.onSliderResize.call(el, slider.active.index);
                        }
                    }
                };

                /**
                 * Adds an aria-hidden=true attribute to each element
                 *
                 * @param startVisibleIndex (int)
                 *  - the first visible element's index
                 */
                var applyAriaHiddenAttributes = function (startVisibleIndex) {
                    var numberOfSlidesShowing = getNumberSlidesShowing();
                    // Only apply attributes if the setting is enabled and not in ticker mode.
                    if (slider.settings.ariaHidden && !slider.settings.ticker) {
                        // Add aria-hidden=true to all elements.
                        slider.children.attr('aria-hidden', 'true');
                        // Get the visible elements and change to aria-hidden=false.
                        slider.children.slice(startVisibleIndex, startVisibleIndex
                            + numberOfSlidesShowing).attr('aria-hidden', 'false');
                    }
                };

                /**
                 * Returns index according to present page range
                 *
                 * @param slideOndex (int)
                 *  - the desired slide index
                 */
                var setSlideIndex = function (slideIndex) {
                    if (slideIndex < 0) {
                        if (slider.settings.infiniteLoop) {
                            return getPagerQty() - 1;
                        } else {
                            // We don't go to undefined slides.
                            return slider.active.index;
                        }
                        // If slideIndex is greater than children length, set active index to 0 (this happens during infinite loop).
                    } else if (slideIndex >= getPagerQty()) {
                        if (slider.settings.infiniteLoop) {
                            return 0;
                        } else {
                            // We don't move to undefined pages.
                            return slider.active.index;
                        }
                        // Set active index to requested slide.
                    } else {
                        return slideIndex;
                    }
                };

                /**
                 * Performs slide transition to the specified slide
                 *
                 * @param slideIndex (int)
                 *  - the destination slide's index (zero-based)
                 *
                 * @param direction (string)
                 *  - INTERNAL USE ONLY - the direction of travel ("prev" / "next")
                 */
                el.goToSlide = function (slideIndex, direction) {
                    // OnSlideBefore, onSlideNext, onSlidePrev callbacks.
                    // Allow transition canceling based on returned value.
                    var performTransition = true,
                        moveBy = 0,
                        position = {left: 0, top: 0},
                        lastChild = null,
                        lastShowingIndex, eq, value, requestEl;
                    // Store the old index.
                    slider.oldIndex = slider.active.index;
                    // Set new index.
                    slider.active.index = setSlideIndex(slideIndex);

                    // If plugin is currently in motion, ignore request.
                    if (slider.working || slider.active.index === slider.oldIndex) {
                        return;
                    }
                    // Declare that plugin is in motion.
                    slider.working = true;

                    performTransition = slider.settings.onSlideBefore.call(el,
                        slider.children.eq(slider.active.index), slider.oldIndex, slider.active.index);

                    // If transitions canceled, reset and return.
                    if (typeof (performTransition) !== 'undefined' && !performTransition) {
                        slider.active.index = slider.oldIndex; // Restore old index.
                        slider.working = false; // Is not in motion.
                        return;
                    }

                    if (direction === 'next') {
                        // Prevent canceling in future functions or lack there-of from negating previous commands to cancel.
                        if (!slider.settings.onSlideNext.call(el, slider.children.eq(slider.active.index),
                            slider.oldIndex, slider.active.index)) {
                            performTransition = false;
                        }
                    } else if (direction === 'prev') {
                        // Prevent canceling in future functions or lack there-of from negating previous commands to cancel.
                        if (!slider.settings.onSlidePrev.call(el, slider.children.eq(slider.active.index),
                            slider.oldIndex, slider.active.index)) {
                            performTransition = false;
                        }
                    }

                    // Check if last slide.
                    slider.active.last = slider.active.index >= getPagerQty() - 1;
                    // Update the pager with active class.
                    if (slider.settings.pager || slider.settings.pagerCustom) {
                        updatePagerActive(slider.active.index);
                    }
                    // Check for direction control update.
                    if (slider.settings.controls) {
                        updateDirectionControls();
                    }
                    // If slider is set to mode: "fade".
                    if (slider.settings.mode === 'fade') {
                        // If adaptiveHeight is true and next height is different from current height, animate to the new height.
                        if (slider.settings.adaptiveHeight && slider.viewport.height() !== getViewportHeight()) {
                            slider.viewport.animate({height: getViewportHeight()}, slider.settings.adaptiveHeightSpeed);
                        }
                        // Fade out the visible child and reset its z-index value.
                        slider.children.filter(':visible').fadeOut(slider.settings.speed).css({zIndex: 0});
                        // Fade in the newly requested slide.
                        slider.children.eq(slider.active.index).css('zIndex',
                            slider.settings.slideZIndex + 1).fadeIn(slider.settings.speed, function () {
                                $(this).css('zIndex', slider.settings.slideZIndex);
                                updateAfterSlideTransition();
                            });
                        // Slider mode is not "fade".
                    } else {
                        // If adaptiveHeight is true and next height is different from current height, animate to the new height.
                        if (slider.settings.adaptiveHeight && slider.viewport.height() !== getViewportHeight()) {
                            slider.viewport.animate({height: getViewportHeight()}, slider.settings.adaptiveHeightSpeed);
                        }
                        // If carousel and not infinite loop.
                        if (!slider.settings.infiniteLoop && slider.carousel && slider.active.last) {
                            if (slider.settings.mode === 'horizontal') {
                                // Get the last child position.
                                lastChild = slider.children.eq(slider.children.length - 1);
                                position = lastChild.position();
                                // Calculate the position of the last slide.
                                moveBy = slider.viewport.width() - lastChild.outerWidth();
                            } else {
                                // Get last showing index position.
                                lastShowingIndex = slider.children.length - slider.settings.minSlides;
                                position = slider.children.eq(lastShowingIndex).position();
                            }
                            // Horizontal carousel, going previous while on first slide (infiniteLoop mode).
                        } else if (slider.carousel && slider.active.last && direction === 'prev') {
                            // Get the last child position.
                            eq = slider.settings.moveSlides === 1 ? slider.settings.maxSlides - getMoveBy() :
                                ((getPagerQty() - 1) * getMoveBy()) - (slider.children.length - slider.settings.maxSlides);
                            lastChild = el.children('.bx-clone').eq(eq);
                            position = lastChild.position();
                            // If infinite loop and "Next" is clicked on the last slide.
                        } else if (direction === 'next' && slider.active.index === 0) {
                            // Get the last clone position.
                            position = el.find('> .bx-clone').eq(slider.settings.maxSlides).position();
                            slider.active.last = false;
                            // Normal non-zero requests.
                        } else if (slideIndex >= 0) {
                            // ParseInt is applied to allow floats for slides/page.
                            requestEl = slideIndex * parseInt(getMoveBy());
                            position = slider.children.eq(requestEl).position();
                        }

                        /* If the position doesn't exist
                         * (e.g. if you destroy the slider on a next click),
                         * it doesn't throw an error.
                         */
                        if (typeof (position) !== 'undefined') {
                            value = slider.settings.mode === 'horizontal' ? -(position.left - moveBy) : -position.top;
                            // Plugin values to be animated.
                            setPositionProperty(value, 'slide', slider.settings.speed);
                        }
                        slider.working = false;
                    }
                    if (slider.settings.ariaHidden) {
                        applyAriaHiddenAttributes(slider.active.index * getMoveBy());
                    }
                };

                /**
                 * Transitions to the next slide in the show
                 */
                el.goToNextSlide = function () {
                    // If infiniteLoop is false and last page is showing, disregard call.
                    if (!slider.settings.infiniteLoop && slider.active.last) {
                        return;
                    }
                    if (slider.working === true) {
                        return;
                    }
                    var pagerIndex = parseInt(slider.active.index) + 1;
                    el.goToSlide(pagerIndex, 'next');
                };

                /**
                 * Transitions to the prev slide in the show
                 */
                el.goToPrevSlide = function () {
                    // If infiniteLoop is false and last page is showing, disregard call.
                    if (!slider.settings.infiniteLoop && slider.active.index === 0) {
                        return;
                    }
                    if (slider.working === true) {
                        return;
                    }
                    var pagerIndex = parseInt(slider.active.index) - 1;
                    el.goToSlide(pagerIndex, 'prev');
                };

                /**
                 * Starts the auto show
                 *
                 * @param preventControlUpdate (boolean)
                 *  - if true, auto controls state will not be updated
                 */
                el.startAuto = function (preventControlUpdate) {
                    // If an interval already exists, disregard call.
                    if (slider.interval) {
                        return;
                    }
                    // Create an interval.
                    slider.interval = setInterval(function () {
                        if (slider.settings.autoDirection === 'next') {
                            el.goToNextSlide();
                        } else {
                            el.goToPrevSlide();
                        }
                    }, slider.settings.pause);
                    // Allback for when the auto rotate status changes.
                    slider.settings.onAutoChange.call(el, true);
                    // If auto controls are displayed and preventControlUpdate is not true.
                    if (slider.settings.autoControls && preventControlUpdate !== true) {
                        updateAutoControls('stop');
                    }
                };

                /**
                 * Stops the auto show
                 *
                 * @param preventControlUpdate (boolean)
                 *  - if true, auto controls state will not be updated
                 */
                el.stopAuto = function (preventControlUpdate) {
                    // If slider is auto paused, just clear that state.
                    if (slider.autoPaused) {
                        slider.autoPaused = false;
                    }
                    // If no interval exists, disregard call.
                    if (!slider.interval) {
                        return;
                    }
                    // Clear the interval.
                    clearInterval(slider.interval);
                    slider.interval = null;
                    // Allback for when the auto rotate status changes.
                    slider.settings.onAutoChange.call(el, false);
                    // If auto controls are displayed and preventControlUpdate is not true.
                    if (slider.settings.autoControls && preventControlUpdate !== true) {
                        updateAutoControls('start');
                    }
                };

                /**
                 * Returns current slide index (zero-based)
                 */
                el.getCurrentSlide = function () {
                    return slider.active.index;
                };

                /**
                 * Returns current slide element
                 */
                el.getCurrentSlideElement = function () {
                    return slider.children.eq(slider.active.index);
                };

                /**
                 * Returns a slide element
                 * @param index (int)
                 *  - The index (zero-based) of the element you want returned.
                 */
                el.getSlideElement = function (index) {
                    return slider.children.eq(index);
                };

                /**
                 * Returns number of slides in show
                 */
                el.getSlideCount = function () {
                    return slider.children.length;
                };

                /**
                 * Return slider.working variable
                 */
                el.isWorking = function () {
                    return slider.working;
                };

                /**
                 * Update all dynamic slider elements
                 */
                el.redrawSlider = function () {
                    // Resize all children in ratio to new screen size.
                    slider.children.add(el.find('.bx-clone')).outerWidth(getSlideWidth());
                    // Adjust the height.
                    slider.viewport.css('height', getViewportHeight());
                    // Update the slide position.
                    if (!slider.settings.ticker) {
                        setSlidePosition();
                    }
                    // If active.last was true before the screen resize, we want.
                    // to keep it last no matter what screen size we end on.
                    if (slider.active.last) {
                        slider.active.index = getPagerQty() - 1;
                    }
                    // If the active index (page) no longer exists due to the resize, simply set the index as last.
                    if (slider.active.index >= getPagerQty()) {
                        slider.active.last = true;
                    }
                    // If a pager is being displayed and a custom pager is not being used, update it.
                    if (slider.settings.pager && !slider.settings.pagerCustom) {
                        populatePager();
                        updatePagerActive(slider.active.index);
                    }
                    if (slider.settings.ariaHidden) {
                        applyAriaHiddenAttributes(slider.active.index * getMoveBy());
                    }
                };

                /**
                 * Destroy the current instance of the slider (revert everything back to original state)
                 */
                el.destroySlider = function () {
                    // Don't do anything if slider has already been destroyed.
                    if (!slider.initialized) {
                        return;
                    }
                    slider.initialized = false;
                    $('.bx-clone', this).remove();
                    slider.children.each(function () {
                        if ($(this).data('origStyle') !== undefined) {
                            $(this).attr('style', $(this).data('origStyle'));
                        } else {
                            $(this).removeAttr('style');
                        }
                    });
                    if ($(this).data('origStyle') !== undefined) {
                        this.attr('style', $(this).data('origStyle'));
                    } else {
                        $(this).removeAttr('style');
                    }
                    $(this).unwrap().unwrap();
                    if (slider.controls.el) {
                        slider.controls.el.remove();
                    }
                    if (slider.controls.next) {
                        slider.controls.next.remove();
                    }
                    if (slider.controls.prev) {
                        slider.controls.prev.remove();
                    }
                    if (slider.pagerEl && slider.settings.controls && !slider.settings.pagerCustom) {
                        slider.pagerEl.remove();
                    }
                    $('.bx-caption', this).remove();
                    if (slider.controls.autoEl) {
                        slider.controls.autoEl.remove();
                    }
                    clearInterval(slider.interval);
                    if (slider.settings.responsive) {
                        $(window).off('resize', resizeWindow);
                    }
                    if (slider.settings.keyboardEnabled) {
                        $(document).off('keydown', keyPress);
                    }
                    // Remove self reference in data.
                    $(this).removeData('bxSlider');
                    // Remove global window handlers.
                    $(window).off('blur', windowBlurHandler).off('focus', windowFocusHandler);
                };

                /**
                 * Reload the slider (revert all DOM changes, and re-initialize)
                 */
                el.reloadSlider = function (settings) {
                    if (settings !== undefined) {
                        options = settings;
                    }
                    el.destroySlider();
                    init();
                    // Store reference to self in order to access public functions later.
                    $(el).data('bxSlider', this);
                };

                init();

                $(el).data('bxSlider', this);

                // Returns the current jQuery object.
                return this;
            };

        })($, window, document);

    }).call(this);

    return {
        init: function (sliderid, bx_pause, bx_effect, bx_speed, bx_captions, bx_responsive, bx_pager, bx_controls, bx_auto,
                        bx_stopAutoOnClick, bx_useCSS) {
            $(function () {
                $("#slides" + sliderid).bxSlider({
                    pause: bx_pause,
                    mode: bx_effect,
                    speed: bx_speed,
                    captions: bx_captions,
                    responsive: bx_responsive,
                    pager: bx_pager,
                    controls: bx_controls,
                    auto: bx_auto,
                    stopAutoOnClick: bx_stopAutoOnClick,
                    stopAuto: false, // This is fix for not stopping auto?
                    useCSS: bx_useCSS,
                    onSliderLoad: function () {
                        $("#slides" + sliderid).css("visibility", "visible");
                    }
                });
            });
        }
    };
});