<?PHP 
/**
 *  Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 *  Code licensed under the BSD License:
 *  http://developer.yahoo.net/yui/license.html
 *  version: 1.0.0b2
 */
 
$GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/2.7.0/build/',
  'moduleInfo' => 
  array (
    'animation' => 
    array (
      'path' => 'animation/animation-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'type' => 'js',
    ),
    'autocomplete' => 
    array (
      'optional' => 
      array (
        0 => 'connection',
        1 => 'animation',
      ),
      'path' => 'autocomplete/autocomplete-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
        2 => 'datasource',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'base' => 
    array (
      'after' => 
      array (
        0 => 'reset',
        1 => 'fonts',
        2 => 'grids',
        3 => 'reset-fonts',
        4 => 'reset-fonts-grids',
      ),
      'path' => 'base/base-min.css',
      'type' => 'css',
    ),
    'button' => 
    array (
      'optional' => 
      array (
        0 => 'menu',
      ),
      'path' => 'button/button-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'calendar' => 
    array (
      'path' => 'calendar/calendar-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'carousel' => 
    array (
      'optional' => 
      array (
        0 => 'animation',
      ),
      'path' => 'carousel/carousel-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'charts' => 
    array (
      'path' => 'charts/charts-min.js',
      'requires' => 
      array (
        0 => 'element',
        1 => 'json',
        2 => 'datasource',
      ),
      'type' => 'js',
    ),
    'colorpicker' => 
    array (
      'optional' => 
      array (
        0 => 'animation',
      ),
      'path' => 'colorpicker/colorpicker-min.js',
      'requires' => 
      array (
        0 => 'slider',
        1 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'connection' => 
    array (
      'path' => 'connection/connection-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
    ),
    'container' => 
    array (
      'optional' => 
      array (
        0 => 'dragdrop',
        1 => 'animation',
        2 => 'connection',
      ),
      'path' => 'container/container-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'skinnable' => true,
      'supersedes' => 
      array (
        0 => 'containercore',
      ),
      'type' => 'js',
    ),
    'containercore' => 
    array (
      'path' => 'container/container_core-min.js',
      'pkg' => 'container',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'type' => 'js',
    ),
    'cookie' => 
    array (
      'path' => 'cookie/cookie-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'type' => 'js',
    ),
    'datasource' => 
    array (
      'optional' => 
      array (
        0 => 'connection',
      ),
      'path' => 'datasource/datasource-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
    ),
    'datatable' => 
    array (
      'optional' => 
      array (
        0 => 'calendar',
        1 => 'dragdrop',
        2 => 'paginator',
      ),
      'path' => 'datatable/datatable-min.js',
      'requires' => 
      array (
        0 => 'element',
        1 => 'datasource',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'dom' => 
    array (
      'path' => 'dom/dom-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'type' => 'js',
    ),
    'dragdrop' => 
    array (
      'path' => 'dragdrop/dragdrop-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'type' => 'js',
    ),
    'editor' => 
    array (
      'optional' => 
      array (
        0 => 'animation',
        1 => 'dragdrop',
      ),
      'path' => 'editor/editor-min.js',
      'requires' => 
      array (
        0 => 'menu',
        1 => 'element',
        2 => 'button',
      ),
      'skinnable' => true,
      'supersedes' => 
      array (
        0 => 'simpleeditor',
      ),
      'type' => 'js',
    ),
    'element' => 
    array (
      'path' => 'element/element-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'type' => 'js',
    ),
    'event' => 
    array (
      'path' => 'event/event-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'type' => 'js',
    ),
    'fonts' => 
    array (
      'path' => 'fonts/fonts-min.css',
      'type' => 'css',
    ),
    'get' => 
    array (
      'path' => 'get/get-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'type' => 'js',
    ),
    'grids' => 
    array (
      'optional' => 
      array (
        0 => 'reset',
      ),
      'path' => 'grids/grids-min.css',
      'requires' => 
      array (
        0 => 'fonts',
      ),
      'type' => 'css',
    ),
    'history' => 
    array (
      'path' => 'history/history-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
    ),
    'imagecropper' => 
    array (
      'path' => 'imagecropper/imagecropper-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
        2 => 'dragdrop',
        3 => 'element',
        4 => 'resize',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'imageloader' => 
    array (
      'path' => 'imageloader/imageloader-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'type' => 'js',
    ),
    'json' => 
    array (
      'path' => 'json/json-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'type' => 'js',
    ),
    'layout' => 
    array (
      'optional' => 
      array (
        0 => 'animation',
        1 => 'dragdrop',
        2 => 'resize',
        3 => 'selector',
      ),
      'path' => 'layout/layout-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
        2 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'logger' => 
    array (
      'optional' => 
      array (
        0 => 'dragdrop',
      ),
      'path' => 'logger/logger-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'menu' => 
    array (
      'path' => 'menu/menu-min.js',
      'requires' => 
      array (
        0 => 'containercore',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'paginator' => 
    array (
      'path' => 'paginator/paginator-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'profiler' => 
    array (
      'path' => 'profiler/profiler-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'type' => 'js',
    ),
    'profilerviewer' => 
    array (
      'path' => 'profilerviewer/profilerviewer-min.js',
      'requires' => 
      array (
        0 => 'profiler',
        1 => 'yuiloader',
        2 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'reset' => 
    array (
      'path' => 'reset/reset-min.css',
      'type' => 'css',
    ),
    'reset-fonts' => 
    array (
      'path' => 'reset-fonts/reset-fonts.css',
      'rollup' => 2,
      'supersedes' => 
      array (
        0 => 'reset',
        1 => 'fonts',
      ),
      'type' => 'css',
    ),
    'reset-fonts-grids' => 
    array (
      'path' => 'reset-fonts-grids/reset-fonts-grids.css',
      'rollup' => 3,
      'supersedes' => 
      array (
        0 => 'reset',
        1 => 'fonts',
        2 => 'grids',
        3 => 'reset-fonts',
      ),
      'type' => 'css',
    ),
    'resize' => 
    array (
      'optional' => 
      array (
        0 => 'animation',
      ),
      'path' => 'resize/resize-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
        2 => 'dragdrop',
        3 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'selector' => 
    array (
      'path' => 'selector/selector-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
        1 => 'dom',
      ),
      'type' => 'js',
    ),
    'simpleeditor' => 
    array (
      'optional' => 
      array (
        0 => 'containercore',
        1 => 'menu',
        2 => 'button',
        3 => 'animation',
        4 => 'dragdrop',
      ),
      'path' => 'editor/simpleeditor-min.js',
      'pkg' => 'editor',
      'requires' => 
      array (
        0 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'slider' => 
    array (
      'optional' => 
      array (
        0 => 'animation',
      ),
      'path' => 'slider/slider-min.js',
      'requires' => 
      array (
        0 => 'dragdrop',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'stylesheet' => 
    array (
      'path' => 'stylesheet/stylesheet-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'type' => 'js',
    ),
    'tabview' => 
    array (
      'optional' => 
      array (
        0 => 'connection',
      ),
      'path' => 'tabview/tabview-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'treeview' => 
    array (
      'optional' => 
      array (
        0 => 'json',
      ),
      'path' => 'treeview/treeview-min.js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
    'uploader' => 
    array (
      'path' => 'uploader/uploader.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
    ),
    'utilities' => 
    array (
      'path' => 'utilities/utilities.js',
      'rollup' => 8,
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
      'type' => 'js',
    ),
    'yahoo' => 
    array (
      'path' => 'yahoo/yahoo-min.js',
      'type' => 'js',
    ),
    'yahoo-dom-event' => 
    array (
      'path' => 'yahoo-dom-event/yahoo-dom-event.js',
      'rollup' => 3,
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'event',
        2 => 'dom',
      ),
      'type' => 'js',
    ),
    'yuiloader' => 
    array (
      'path' => 'yuiloader/yuiloader-min.js',
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'get',
      ),
      'type' => 'js',
    ),
    'yuiloader-dom-event' => 
    array (
      'path' => 'yuiloader-dom-event/yuiloader-dom-event.js',
      'rollup' => 5,
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'dom',
        2 => 'event',
        3 => 'get',
        4 => 'yuiloader',
        5 => 'yahoo-dom-event',
      ),
      'type' => 'js',
    ),
    'yuitest' => 
    array (
      'path' => 'yuitest/yuitest-min.js',
      'requires' => 
      array (
        0 => 'logger',
      ),
      'skinnable' => true,
      'type' => 'js',
    ),
  ),
  'skin' => 
  array (
    'after' => 
    array (
      0 => 'reset',
      1 => 'fonts',
      2 => 'grids',
      3 => 'base',
    ),
    'base' => 'assets/skins/',
    'defaultSkin' => 'sam',
    'path' => 'skin.css',
    'rollup' => 3,
  ),
); ?>