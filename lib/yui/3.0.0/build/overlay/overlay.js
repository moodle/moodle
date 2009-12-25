/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add('overlay', function(Y) {

/**
 * Provides a basic Overlay widget, with Standard Module content support. The Overlay widget
 * provides Page XY positioning support, alignment and centering support along with basic 
 * stackable support (z-index and shimming).
 *
 * @module overlay
 */

/**
 * A basic Overlay Widget, which can be positioned based on Page XY co-ordinates and is stackable (z-index support).
 * It also provides alignment and centering support and uses a standard module format for it's content, with header,
 * body and footer section support.
 * 
 * @class Overlay
 * @constructor
 * @extends Widget
 * @uses WidgetPosition
 * @uses WidgetStack
 * @uses WidgetPositionExt
 * @uses WidgetStdMod
 * @param {Object} object The user configuration for the instance.
 */
Y.Overlay = Y.Base.build("overlay", Y.Widget, [Y.WidgetPosition, Y.WidgetStack, Y.WidgetPositionExt, Y.WidgetStdMod]);



}, '3.0.0' ,{requires:['widget', 'widget-position', 'widget-stack', 'widget-position-ext', 'widget-stdmod']});
