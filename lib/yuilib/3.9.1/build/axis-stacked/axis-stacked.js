/* YUI 3.9.1 (build 5852) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add('axis-stacked', function (Y, NAME) {

/**
 * Provides functionality for drawing a stacked numeric axis for use with a chart.
 *
 * @module charts
 * @submodule axis-stacked
 */
/**
 * StackedAxis draws a stacked numeric axis for a chart.
 *
 * @class StackedAxis
 * @constructor
 * @param {Object} config (optional) Configuration parameters.
 * @extends NumericAxis
 * @uses StackedImpl
 * @submodule axis-stacked
 */
Y.StackedAxis = Y.Base.create("stackedAxis", Y.NumericAxis, [Y.StackedImpl]);



}, '3.9.1', {"requires": ["axis-numeric", "axis-stacked-base"]});
