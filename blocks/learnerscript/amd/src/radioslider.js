/**
 * radiosToSlider v0.3.2
 * jquery plugin to create a slider using a list of radio buttons
 * (c)2014 Rubén Torres - rubentdlh@gmail.com
 * Released under the MIT license
 */
define(['jquery','block_learnerscript/report'], function($,report) {
    var defaults = {
                    size: 'medium',
                    animation: true,
                    fitContainer: true,
                    isDisable: false,
                    onSelect: null,
                    reportdashboard: true
                    };
    return RadiosToSlider = {
        init: function(element, options) {
            this.KNOB_WIDTH = 10;
            this.KNOB_MARGIN = 42;
            this.LEVEL_MARGIN = this.KNOB_MARGIN + 10;
            this.LABEL_WIDTH = 34;
            this.LEVEL_WIDTH = 10;
            this.bearer = element;
            this.options = options;
            this.currentLevel = 0; //this means no level selected
            this.value = null;
                var rtn = [],
                    $this = element;

                $this.each(function() {
                    options = $.extend({}, this.defaults, options);
                    RadiosToSlider.activate();

                    rtn.push({
                        bearer: this.bearer,
                        setDisable: RadiosToSlider.setDisable.bind(RadiosToSlider),
                        setEnable: RadiosToSlider.setEnable.bind(RadiosToSlider),
                        getValue: RadiosToSlider.getValue.bind(RadiosToSlider)
                    });
                });
        },
        activate: function() {
            // Get number options
            this.numOptions = this.bearer.find('input[type=radio]').length;
            this.reset(); // helps prevent duplication
            this.fitContainer();
            this.addBaseStyle();
            this.addLevels();
            this.addBar();
            this.setSlider();
            this.addInteraction();
            this.setDisabled();

            var slider = this;

            $(window).on('resize orientationChanged', function() {
                slider.reset();
                slider.fitContainer();
                slider.addBaseStyle();
                slider.addLevels();
                slider.setSlider();
                slider.addInteraction();
                slider.setDisabled();
            });
        },

        reset: function() {
            var $labels = this.bearer.find('label'),
                $levels = this.bearer.find('.slider-level');

            $labels.each(function() {
                var $this = $(this);

                $this.removeClass('slider-label');
                $this.css('left', 0);
            });

            $levels.each(function() {
                $(this).remove();
            });

            this.bearer.css('width', 'auto');
        },

        fitContainer: function() {
            // If fitContainer, calculate KNOB_MARGIN based on container width
            if (this.options.fitContainer) {
                this.KNOB_MARGIN = (this.bearer.width() - this.KNOB_WIDTH) / (this.numOptions - 1) - this.KNOB_WIDTH;
                this.LEVEL_MARGIN = this.KNOB_MARGIN + 10;
            }
        },

        addBaseStyle: function() {
            var label = 0,
                slider = this,
                width = (this.numOptions * this.LEVEL_WIDTH) + (this.numOptions - 1) * this.LEVEL_MARGIN;
            this.bearer.find('input[type=radio]').hide();
            this.bearer.addClass("radios-to-slider " + this.options.size);
            this.bearer.css('width', width + 'px');
            this.bearer.find('label').each(function() {
                var $this = $(this),
                    leftPos = slider.KNOB_WIDTH / 2 - (slider.LABEL_WIDTH / 2) + label * slider.LEVEL_MARGIN + label * slider.LEVEL_WIDTH;
                $this.addClass('slider-label');
                $this.css('left', leftPos + 'px');
                label++;
            });
        },

        //Add level indicators to DOM
        addLevels: function() {
            var $bearer = this.bearer,
                level = 0,
                slider = this;

            $bearer.find('input[type=radio]').each(function() {
                var $this = $(this);

                $bearer.append("<ins class='slider-level' data-radio='" + $this.attr('id') + "' data-value=" + $this.val() + "></ins>");
            });

            $bearer.find('.slider-level').each(function() {
                var $this = $(this),
                    paddingLeft = $bearer.css('padding-left').replace('px', '') - 0,
                    width = paddingLeft + (level * slider.LEVEL_MARGIN) + (level * slider.LEVEL_WIDTH);

                $this.css('left', width + 'px');

                level++;
            });

        },

        //Add slider bar to DOM
        addBar: function() {
            this.bearer.append("<ins class='slider-bar'><span class='slider-knob'></span></ins>");
        },

        //set width of slider bar and current level
        setSlider: function() {
            var $inputs = this.bearer.find('input[type=radio]'),
                $levels = this.bearer.find('.slider-level'),
                $labels = this.bearer.find('.slider-label'),
                radio = 1,
                slider = this,
                label;

            $inputs.each(function() {
                var $this = $(this),
                    $sliderbar = slider.bearer.find('.slider-bar'),
                    radioId = $this.attr('id');

                if ($this.prop('checked')) {
                    var width = (radio * slider.KNOB_WIDTH) + (radio - 1) * slider.KNOB_MARGIN + 10 * (radio-1);

                    $sliderbar.css('display', 'block');
                    $sliderbar.width(width + 'px');

                    slider.currentLevel = radio;
                }

                if (slider.options.animation) {
                    $sliderbar.addClass('transition-enabled');
                }

                radio++;
            });

            //Set style for lower levels
            label = 0;
            $levels.each(function() {
                label++;

                var $this = $(this);

                if (label < slider.currentLevel) {
                    $this.show();
                    $this.addClass('slider-lower-level');
                } else if (label == slider.currentLevel) {
                    $this.hide();
                } else {
                    $this.show();
                    $this.removeClass('slider-lower-level');
                }
            });

            //Add bold style for selected label
            label = 0;
            $labels.each(function() {
                label++;

                var $this = $(this);

                if (label == slider.currentLevel) {
                    $this.addClass('slider-label-active');
                } else {
                    $this.removeClass('slider-label-active');
                }
            });
        },

        addInteraction: function() {
            var slider = this,
                $bearer = slider.bearer,
                $levels = $bearer.find('.slider-level:not(.disabled)'),
                $inputs = $bearer.find('input[type=radio]:not(:disabled)');

            $levels.on('click', function(e) {
                e.stopImmediatePropagation();
                var $this = $(this),
                    val = $this.attr('data-value'),
                    radioId = $this.attr('data-radio'),
                    radioElement = $bearer.find('#' + radioId);
                require(['block_learnerscript/smartfilter'], function(smartfilter) {
                        smartfilter.DurationFilter(val, slider.options.reportdashboard);
                    });
                radioElement.prop('checked', true);

                if (slider.options.onSelect) {
                    slider.options.onSelect(radioElement, [
                        $levels,
                        $inputs
                    ]);
                }
                slider.value = val;
                $bearer.attr('data-value', val);

                slider.setSlider();

                $bearer.trigger('radiochange');
            });

            $inputs.on('change', function(e) {
                e.stopImmediatePropagation();
                var $this = $(this),
                    val = $this.attr('value'),
                    radioId = $this.attr('data-radio'),
                    radioElement = $bearer.find('#' + radioId);
                require(['block_learnerscript/smartfilter'], function(smartfilter) {
                        smartfilter.DurationFilter(val, slider.options.reportdashboard);
                    });

                radioElement.prop('checked', true);

                if (slider.options.onChange) {
                    slider.options.onChange(radioElement, [
                        $levels,
                        $inputs
                    ]);
                }

                slider.value = val;
                $bearer.attr('data-value', val);

                slider.setSlider();
                $bearer.trigger('radiochange');
            });

        },

        setDisabled: function(isDisable, cb) {
            if (!this.options.isDisable) return;

            this.setDisable();
        },

        setDisable: function(cb) {
            this.options.isDisable = true;

            var slider = this,
                $bearer = slider.bearer,
                $levels = this.bearer.find('.slider-level'),
                $inputs = this.bearer.find('input[type=radio]');

            $.merge($levels, $inputs).each(function() {
                var $this = $(this);

                $this.prop('disabled', true).addClass('disabled');
                $this.off('click change');
            });

            if (typeof cb === "function") {
                cb($levels, $inputs);
            }

            $bearer.trigger('radiodisabled');
        },

        setEnable: function(cb) {
            this.options.isDisable = false;

            var slider = this,
                $bearer = slider.bearer,
                $levels = this.bearer.find('.slider-level'),
                $inputs = this.bearer.find('input[type=radio]');

            $.merge($levels, $inputs).each(function() {
                $(this).prop('disabled', false).removeClass('disabled');
                slider.addInteraction();
            });

            if (typeof cb === "function") {
                cb($levels, $inputs);
            }

            $bearer.trigger('radiodenabled');
        },

        getValue: function() {
            return this.value;
        }

    };
});