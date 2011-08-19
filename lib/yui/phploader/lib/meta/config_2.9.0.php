<?php $GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/2.9.0/build/',
  'skin' => 
  array (
    'after' => 
    array (
      0 => 'reset',
      1 => 'fonts',
      2 => 'grids',
      3 => 'base',
    ),
    'path' => 'skin.css',
    'base' => 'assets/skins/',
    'rollup' => 3,
    'defaultSkin' => 'sam',
  ),
  'moduleInfo' => 
  array (
    'event' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'event/event-min.js',
    ),
    'animation' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'path' => 'animation/animation-min.js',
    ),
    'swfstore' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'element',
        1 => 'cookie',
        2 => 'swf',
      ),
      'path' => 'swfstore/swfstore-min.js',
    ),
    'datatable' => 
    array (
      'path' => 'datatable/datatable-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
        1 => 'datasource',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'calendar',
        1 => 'dragdrop',
        2 => 'paginator',
      ),
    ),
    'swfdetect' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'swfdetect/swfdetect-min.js',
    ),
    'menu' => 
    array (
      'path' => 'menu/menu-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'containercore',
      ),
      'type' => 'js',
    ),
    'treeview' => 
    array (
      'path' => 'treeview/treeview-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'json',
        1 => 'animation',
        2 => 'calendar',
      ),
    ),
    'get' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'get/get-min.js',
    ),
    'progressbar' => 
    array (
      'path' => 'progressbar/progressbar-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'animation',
      ),
    ),
    'uploader' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'path' => 'uploader/uploader-min.js',
    ),
    'datasource' => 
    array (
      'path' => 'datasource/datasource-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'connection',
      ),
    ),
    'fonts' => 
    array (
      'type' => 'css',
      'path' => 'fonts/fonts-min.css',
    ),
    'profiler' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'profiler/profiler-min.js',
    ),
    'connection' => 
    array (
      'supersedes' => 
      array (
        0 => 'connectioncore',
      ),
      'path' => 'connection/connection-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
    ),
    'json' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'json/json-min.js',
    ),
    'datemath' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'datemath/datemath-min.js',
    ),
    'calendar' => 
    array (
      'supersedes' => 
      array (
        0 => 'datemath',
      ),
      'path' => 'calendar/calendar-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'type' => 'js',
    ),
    'simpleeditor' => 
    array (
      'path' => 'editor/simpleeditor-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'containercore',
        1 => 'menu',
        2 => 'button',
        3 => 'animation',
        4 => 'dragdrop',
      ),
      'pkg' => 'editor',
    ),
    'swf' => 
    array (
      'supersedes' => 
      array (
        0 => 'swfdetect',
      ),
      'path' => 'swf/swf-min.js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
    ),
    'reset' => 
    array (
      'type' => 'css',
      'path' => 'reset/reset-min.css',
    ),
    'event-simulate' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'path' => 'event-simulate/event-simulate-min.js',
    ),
    'yuiloader-dom-event' => 
    array (
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'dom',
        2 => 'event',
        3 => 'get',
        4 => 'yuiloader',
        5 => 'yahoo-dom-event',
      ),
      'path' => 'yuiloader-dom-event/yuiloader-dom-event.js',
      'rollup' => 5,
      'type' => 'js',
    ),
    'storage' => 
    array (
      'path' => 'storage/storage-min.js',
      'requires' => 
      array (
        0 => 'yahoo',
        1 => 'event',
        2 => 'cookie',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'swfstore',
      ),
    ),
    'container' => 
    array (
      'supersedes' => 
      array (
        0 => 'containercore',
      ),
      'path' => 'container/container-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'dragdrop',
        1 => 'animation',
        2 => 'connection',
      ),
    ),
    'profilerviewer' => 
    array (
      'path' => 'profilerviewer/profilerviewer-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'profiler',
        1 => 'yuiloader',
        2 => 'element',
      ),
      'type' => 'js',
    ),
    'imagecropper' => 
    array (
      'path' => 'imagecropper/imagecropper-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'dragdrop',
        1 => 'element',
        2 => 'resize',
      ),
      'type' => 'js',
    ),
    'paginator' => 
    array (
      'path' => 'paginator/paginator-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
    ),
    'tabview' => 
    array (
      'path' => 'tabview/tabview-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'connection',
      ),
    ),
    'grids' => 
    array (
      'path' => 'grids/grids-min.css',
      'requires' => 
      array (
        0 => 'fonts',
      ),
      'type' => 'css',
      'optional' => 
      array (
        0 => 'reset',
      ),
    ),
    'layout' => 
    array (
      'path' => 'layout/layout-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'animation',
        1 => 'dragdrop',
        2 => 'resize',
        3 => 'selector',
      ),
    ),
    'imageloader' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'path' => 'imageloader/imageloader-min.js',
    ),
    'containercore' => 
    array (
      'path' => 'container/container_core-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'type' => 'js',
      'pkg' => 'container',
    ),
    'event-mouseenter' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'path' => 'event-mouseenter/event-mouseenter-min.js',
    ),
    'logger' => 
    array (
      'path' => 'logger/logger-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'event',
        1 => 'dom',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'dragdrop',
      ),
    ),
    'cookie' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'cookie/cookie-min.js',
    ),
    'stylesheet' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'stylesheet/stylesheet-min.js',
    ),
    'connectioncore' => 
    array (
      'path' => 'connection/connection_core-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
      'pkg' => 'connection',
    ),
    'utilities' => 
    array (
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
      'path' => 'utilities/utilities.js',
      'rollup' => 8,
      'type' => 'js',
    ),
    'dragdrop' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
      ),
      'path' => 'dragdrop/dragdrop-min.js',
    ),
    'colorpicker' => 
    array (
      'path' => 'colorpicker/colorpicker-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'slider',
        1 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'animation',
      ),
    ),
    'base' => 
    array (
      'type' => 'css',
      'after' => 
      array (
        0 => 'reset',
        1 => 'fonts',
        2 => 'grids',
      ),
      'path' => 'base/base-min.css',
    ),
    'event-delegate' => 
    array (
      'path' => 'event-delegate/event-delegate-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'selector',
      ),
    ),
    'yuiloader' => 
    array (
      'type' => 'js',
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'get',
      ),
      'path' => 'yuiloader/yuiloader-min.js',
    ),
    'button' => 
    array (
      'path' => 'button/button-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'menu',
      ),
    ),
    'resize' => 
    array (
      'path' => 'resize/resize-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'dragdrop',
        1 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'animation',
      ),
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
      'optional' => 
      array (
        0 => 'event-mouseenter',
        1 => 'event-delegate',
      ),
    ),
    'history' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'path' => 'history/history-min.js',
    ),
    'yahoo' => 
    array (
      'type' => 'js',
      'path' => 'yahoo/yahoo-min.js',
    ),
    'element-delegate' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'element',
      ),
      'path' => 'element-delegate/element-delegate-min.js',
    ),
    'charts' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'element',
        1 => 'json',
        2 => 'datasource',
        3 => 'swf',
      ),
      'path' => 'charts/charts-min.js',
    ),
    'slider' => 
    array (
      'path' => 'slider/slider-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'dragdrop',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'animation',
      ),
    ),
    'selector' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
        1 => 'dom',
      ),
      'path' => 'selector/selector-min.js',
    ),
    'reset-fonts-grids' => 
    array (
      'supersedes' => 
      array (
        0 => 'reset',
        1 => 'fonts',
        2 => 'grids',
        3 => 'reset-fonts',
      ),
      'path' => 'reset-fonts-grids/reset-fonts-grids.css',
      'rollup' => 4,
      'type' => 'css',
    ),
    'yuitest' => 
    array (
      'path' => 'yuitest/yuitest-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'logger',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'event-simulate',
      ),
    ),
    'carousel' => 
    array (
      'path' => 'carousel/carousel-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'element',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'animation',
      ),
    ),
    'autocomplete' => 
    array (
      'path' => 'autocomplete/autocomplete-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event',
        2 => 'datasource',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'connection',
        1 => 'animation',
      ),
    ),
    'yahoo-dom-event' => 
    array (
      'supersedes' => 
      array (
        0 => 'yahoo',
        1 => 'event',
        2 => 'dom',
      ),
      'path' => 'yahoo-dom-event/yahoo-dom-event.js',
      'rollup' => 3,
      'type' => 'js',
    ),
    'dom' => 
    array (
      'type' => 'js',
      'requires' => 
      array (
        0 => 'yahoo',
      ),
      'path' => 'dom/dom-min.js',
    ),
    'reset-fonts' => 
    array (
      'supersedes' => 
      array (
        0 => 'reset',
        1 => 'fonts',
      ),
      'path' => 'reset-fonts/reset-fonts.css',
      'rollup' => 2,
      'type' => 'css',
    ),
    'editor' => 
    array (
      'supersedes' => 
      array (
        0 => 'simpleeditor',
      ),
      'path' => 'editor/editor-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'menu',
        1 => 'element',
        2 => 'button',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'animation',
        1 => 'dragdrop',
      ),
    ),
  ),
); ?>