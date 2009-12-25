<?PHP 
/**
 *  Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 *  Code licensed under the BSD License:
 *  http://developer.yahoo.net/yui/license.html
 *  version: 1.0.0b2
 */
 
$GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/2.8.0/build/',
  'skin' => 
  array (
    'defaultSkin' => 'sam',
    'base' => 'assets/skins/',
    'path' => 'skin.css',
    'after' => 
    array (
      0 => 'reset',
      1 => 'fonts',
      2 => 'grids',
      3 => 'base',
    ),
    'rollup' => 3,
  ),
  'moduleInfo' => 
  array (
    'animation' => 
    array (
      'type' => 'js',
      'path' => 'animation/animation-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
    ),
    'autocomplete' => 
    array (
      'type' => 'js',
      'path' => 'autocomplete/autocomplete-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
        2 => 'datasource',
      ),
      'optional' => 
      array (
        0 => 'connection',
        1 => 'animation',
      ),
      'skinnable' => true,
    ),
    'base' => 
    array (
      'type' => 'css',
      'path' => 'base/base-min.css',
      'after' => 
      array (
        0 => 'reset',
        1 => 'fonts',
        2 => 'grids',
        3 => 'reset-fonts',
        4 => 'reset-fonts-grids',
      ),
    ),
    'button' => 
    array (
      'type' => 'js',
      'path' => 'button/button-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'optional' => 
      array (
        0 => 'menu',
      ),
      'skinnable' => true,
    ),
    'calendar' => 
    array (
      'type' => 'js',
      'path' => 'calendar/calendar-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'supersedes' => 
      array (
        0 => 'datemeth',
      ),
      'skinnable' => true,
    ),
    'carousel' => 
    array (
      'type' => 'js',
      'path' => 'carousel/carousel-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'optional' => 
      array (
        0 => 'animation',
      ),
      'skinnable' => true,
    ),
    'charts' => 
    array (
      'type' => 'js',
      'path' => 'charts/charts-min.js',
      'requires' => 
      array (
        0 => 'element',
        1 => 'json',
        2 => 'datasource',
        3 => 'swf',
      ),
    ),
    'colorpicker' => 
    array (
      'type' => 'js',
      'path' => 'colorpicker/colorpicker-min.js',
      'requires' => 
      array (
        0 => 'slider',
        1 => 'element',
      ),
      'optional' => 
      array (
        0 => 'animation',
      ),
      'skinnable' => true,
    ),
    'connection' => 
    array (
      'type' => 'js',
      'path' => 'connection/connection-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'supersedes' => 
      array (
        0 => 'connectioncore',
      ),
    ),
    'connectioncore' => 
    array (
      'type' => 'js',
      'path' => 'connection/connection_core-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'pkg' => 'connection',
    ),
    'container' => 
    array (
      'type' => 'js',
      'path' => 'container/container-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'optional' => 
      array (
        0 => 'dragdrop',
        1 => 'animation',
        2 => 'connection',
      ),
      'supersedes' => 
      array (
        0 => 'containercore',
      ),
      'skinnable' => true,
    ),
    'containercore' => 
    array (
      'type' => 'js',
      'path' => 'container/container_core-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'pkg' => 'container',
    ),
    'cookie' => 
    array (
      'type' => 'js',
      'path' => 'cookie/cookie-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'datasource' => 
    array (
      'type' => 'js',
      'path' => 'datasource/datasource-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'optional' => 
      array (
        0 => 'connection',
      ),
    ),
    'datatable' => 
    array (
      'type' => 'js',
      'path' => 'datatable/datatable-min.js',
      'requires' => 
      array (
        0 => 'element',
        1 => 'datasource',
      ),
      'optional' => 
      array (
        0 => 'calendar',
        1 => 'dragdrop',
        2 => 'paginator',
      ),
      'skinnable' => true,
    ),
    'datemath' => 
    array (
      'type' => 'js',
      'path' => 'datemath/datemath-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'dom' => 
    array (
      'type' => 'js',
      'path' => 'dom/dom-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'dragdrop' => 
    array (
      'type' => 'js',
      'path' => 'dragdrop/dragdrop-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
    ),
    'editor' => 
    array (
      'type' => 'js',
      'path' => 'editor/editor-min.js',
      'requires' => 
      array (
        0 => 'menu',
        1 => 'element',
        2 => 'button',
      ),
      'optional' => 
      array (
        0 => 'animation',
        1 => 'dragdrop',
      ),
      'supersedes' => 
      array (
        0 => 'simpleeditor',
      ),
      'skinnable' => true,
    ),
    'element' => 
    array (
      'type' => 'js',
      'path' => 'element/element-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'optional' => 
      array (
        0 => 'event-mouseenter',
        1 => 'event-delegate',
      ),
    ),
    'element-delegate' => 
    array (
      'type' => 'js',
      'path' => 'element-delegate/element-delegate-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
    ),
    'event' => 
    array (
      'type' => 'js',
      'path' => 'event/event-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'event-simulate' => 
    array (
      'type' => 'js',
      'path' => 'event-simulate/event-simulate-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
    ),
    'event-delegate' => 
    array (
      'type' => 'js',
      'path' => 'event-delegate/event-delegate-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'optional' => 
      array (
        0 => 'selector',
      ),
    ),
    'event-mouseenter' => 
    array (
      'type' => 'js',
      'path' => 'event-mouseenter/event-mouseenter-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
    ),
    'fonts' => 
    array (
      'type' => 'css',
      'path' => 'fonts/fonts-min.css',
    ),
    'get' => 
    array (
      'type' => 'js',
      'path' => 'get/get-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'grids' => 
    array (
      'type' => 'css',
      'path' => 'grids/grids-min.css',
      'requires' => 
      array (
        0 => 'fonts',
      ),
      'optional' => 
      array (
        0 => 'reset',
      ),
    ),
    'history' => 
    array (
      'type' => 'js',
      'path' => 'history/history-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
    ),
    'imagecropper' => 
    array (
      'type' => 'js',
      'path' => 'imagecropper/imagecropper-min.js',
      'requires' => 
      array (
        0 => 'dragdrop',
        1 => 'element',
        2 => 'resize',
      ),
      'skinnable' => true,
    ),
    'imageloader' => 
    array (
      'type' => 'js',
      'path' => 'imageloader/imageloader-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
    ),
    'json' => 
    array (
      'type' => 'js',
      'path' => 'json/json-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'layout' => 
    array (
      'type' => 'js',
      'path' => 'layout/layout-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'optional' => 
      array (
        0 => 'animation',
        1 => 'dragdrop',
        2 => 'resize',
        3 => 'selector',
      ),
      'skinnable' => true,
    ),
    'logger' => 
    array (
      'type' => 'js',
      'path' => 'logger/logger-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'optional' => 
      array (
        0 => 'dragdrop',
      ),
      'skinnable' => true,
    ),
    'menu' => 
    array (
      'type' => 'js',
      'path' => 'menu/menu-min.js',
      'requires' => 
      array (
        0 => 'containercore',
      ),
      'skinnable' => true,
    ),
    'paginator' => 
    array (
      'type' => 'js',
      'path' => 'paginator/paginator-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'skinnable' => true,
    ),
    'profiler' => 
    array (
      'type' => 'js',
      'path' => 'profiler/profiler-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'profilerviewer' => 
    array (
      'type' => 'js',
      'path' => 'profilerviewer/profilerviewer-min.js',
      'requires' => 
      array (
        0 => 'profiler',
        1 => 'yuiloader',
        2 => 'element',
      ),
      'skinnable' => true,
    ),
    'progressbar' => 
    array (
      'type' => 'js',
      'path' => 'progressbar/progressbar-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'optional' => 
      array (
        0 => 'animation',
      ),
      'skinnable' => true,
    ),
    'reset' => 
    array (
      'type' => 'css',
      'path' => 'reset/reset-min.css',
    ),
    'reset-fonts-grids' => 
    array (
      'type' => 'css',
      'path' => 'reset-fonts-grids/reset-fonts-grids.css',
      'supersedes' => 
      array (
        0 => 'reset',
        1 => 'fonts',
        2 => 'grids',
        3 => 'reset-fonts',
      ),
      'rollup' => 3,
    ),
    'reset-fonts' => 
    array (
      'type' => 'css',
      'path' => 'reset-fonts/reset-fonts.css',
      'supersedes' => 
      array (
        0 => 'reset',
        1 => 'fonts',
      ),
      'rollup' => 2,
    ),
    'resize' => 
    array (
      'type' => 'js',
      'path' => 'resize/resize-min.js',
      'requires' => 
      array (
        0 => 'dragdrop',
        1 => 'element',
      ),
      'optional' => 
      array (
        0 => 'animation',
      ),
      'skinnable' => true,
    ),
    'selector' => 
    array (
      'type' => 'js',
      'path' => 'selector/selector-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
        1 => 'dom',
      ),
    ),
    'simpleeditor' => 
    array (
      'type' => 'js',
      'path' => 'editor/simpleeditor-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'optional' => 
      array (
        0 => 'containercore',
        1 => 'menu',
        2 => 'button',
        3 => 'animation',
        4 => 'dragdrop',
      ),
      'skinnable' => true,
      'pkg' => 'editor',
    ),
    'slider' => 
    array (
      'type' => 'js',
      'path' => 'slider/slider-min.js',
      'requires' => 
      array (
        0 => 'dragdrop',
      ),
      'optional' => 
      array (
        0 => 'animation',
      ),
      'skinnable' => true,
    ),
    'storage' => 
    array (
      'type' => 'js',
      'path' => 'storage/storage-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
        1 => 'event',
        2 => 'cookie',
      ),
      'optional' => 
      array (
        0 => 'swfstore',
      ),
    ),
    'stylesheet' => 
    array (
      'type' => 'js',
      'path' => 'stylesheet/stylesheet-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'swf' => 
    array (
      'type' => 'js',
      'path' => 'swf/swf-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'supersedes' => 
      array (
        0 => 'swfdetect',
      ),
    ),
    'swfdetect' => 
    array (
      'type' => 'js',
      'path' => 'swfdetect/swfdetect-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
    ),
    'swfstore' => 
    array (
      'type' => 'js',
      'path' => 'swfstore/swfstore-min.js',
      'requires' => 
      array (
        0 => 'element',
        1 => 'cookie',
        2 => 'swf',
      ),
    ),
    'tabview' => 
    array (
      'type' => 'js',
      'path' => 'tabview/tabview-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'optional' => 
      array (
        0 => 'connection',
      ),
      'skinnable' => true,
    ),
    'treeview' => 
    array (
      'type' => 'js',
      'path' => 'treeview/treeview-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'optional' => 
      array (
        0 => 'json',
        1 => 'animation',
        2 => 'calendar',
      ),
      'skinnable' => true,
    ),
    'uploader' => 
    array (
      'type' => 'js',
      'path' => 'uploader/uploader-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
    ),
    'utilities' => 
    array (
      'type' => 'js',
      'path' => 'utilities/utilities.js',
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'event',
        2 => 'dragdrop',
        3 => 'animation',
        4 => 'dom',
        5 => 'connection',
        6 => 'element',
        7 => 'yahoo-dom-event',
        8 => 'get',
        9 => 'yuiloader',
        10 => 'yuiloader-dom-event',
      ),
      'rollup' => 8,
    ),
    'yahoo' => 
    array (
      'type' => 'js',
      'path' => 'yahoo/yahoo-min.js',
    ),
    'yahoo-dom-event' => 
    array (
      'type' => 'js',
      'path' => 'yahoo-dom-event/yahoo-dom-event.js',
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'event',
        2 => 'dom',
      ),
      'rollup' => 3,
    ),
    'yuiloader' => 
    array (
      'type' => 'js',
      'path' => 'yuiloader/yuiloader-min.js',
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'get',
      ),
    ),
    'yuiloader-dom-event' => 
    array (
      'type' => 'js',
      'path' => 'yuiloader-dom-event/yuiloader-dom-event.js',
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'dom',
        2 => 'event',
        3 => 'get',
        4 => 'yuiloader',
        5 => 'yahoo-dom-event',
      ),
      'rollup' => 5,
    ),
    'yuitest' => 
    array (
      'type' => 'js',
      'path' => 'yuitest/yuitest-min.js',
      'requires' => 
      array (
        0 => 'logger',
      ),
      'optional' => 
      array (
        0 => 'event-simulate',
      ),
      'skinnable' => true,
    ),
  ),
); ?>