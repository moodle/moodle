<?PHP
/**
 *  Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 *  Code licensed under the BSD License:
 *  http://developer.yahoo.net/yui/license.html
 *  version: 1.0.0b2
 */
 
$GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/3.0.0b1/build/',
  'skin' => 
  array (
    'after' => 
    array (
      0 => 'cssreset',
      1 => 'cssfonts',
      2 => 'cssgrids',
      3 => 'cssreset-context',
      4 => 'cssfonts-context',
      5 => 'cssgrids-context',
    ),
    'path' => 'skin.css',
    'base' => 'assets/skins/',
    'defaultSkin' => 'sam',
  ),
  'moduleInfo' => 
  array (
    'datatype-date' => 
    array (
      'path' => 'datatype/datatype-date-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'datatype-date',
      'ext' => false,
    ),
    'cssfonts-context' => 
    array (
      'path' => 'cssfonts/fonts-context-min.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'cssfonts-context',
      'requires' => 
      array (
      ),
    ),
    'dd-ddm' => 
    array (
      'path' => 'dd/dd-ddm-min.js',
      'requires' => 
      array (
        0 => 'dd-ddm-base',
      ),
      'type' => 'js',
      'name' => 'dd-ddm',
      'ext' => false,
    ),
    'stylesheet' => 
    array (
      'path' => 'stylesheet/stylesheet-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'stylesheet',
      'ext' => false,
    ),
    'imageloader' => 
    array (
      'path' => 'imageloader/imageloader-min.js',
      'requires' => 
      array (
        0 => 'node',
      ),
      'type' => 'js',
      'name' => 'imageloader',
      'ext' => false,
    ),
    'cssbase' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'cssbase/base-min.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'cssbase',
      'requires' => 
      array (
      ),
    ),
    'anim-base' => 
    array (
      'path' => 'anim/anim-base-min.js',
      'requires' => 
      array (
        0 => 'base',
        1 => 'node-style',
      ),
      'type' => 'js',
      'name' => 'anim-base',
      'ext' => false,
    ),
    'oop' => 
    array (
      'path' => 'oop/oop-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'oop',
      'ext' => false,
    ),
    'datasource-xhr' => 
    array (
      'path' => 'datasource/datasource-xhr-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'io-base',
      ),
      'type' => 'js',
      'name' => 'datasource-xhr',
      'ext' => false,
    ),
    'selector-css3' => 
    array (
      'path' => 'dom/selector-css3-min.js',
      'requires' => 
      array (
        0 => 'selector',
        1 => 'dom',
        2 => 'dom',
      ),
      'type' => 'js',
      'name' => 'selector-css3',
      'ext' => false,
    ),
    'yui-base' => 
    array (
      '_provides' => 
      array (
        'yui-base' => true,
      ),
      'path' => 'yui-base/yui-base-min.js',
      '_supersedes' => 
      array (
      ),
      'type' => 'js',
      'ext' => false,
      'name' => 'yui-base',
      'requires' => 
      array (
      ),
      'expanded' => 
      array (
      ),
    ),
    'json-parse' => 
    array (
      '_provides' => 
      array (
        'json-parse' => true,
      ),
      'path' => 'json/json-parse-min.js',
      '_supersedes' => 
      array (
      ),
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'json-parse',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
    ),
    'skin-sam-widget-stack' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-stack.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stack',
      'requires' => 
      array (
      ),
    ),
    'dom-screen' => 
    array (
      'path' => 'dom/dom-screen-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
        1 => 'dom-style',
      ),
      'type' => 'js',
      'name' => 'dom-screen',
      'ext' => false,
    ),
    'selector' => 
    array (
      'path' => 'dom/selector-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'type' => 'js',
      'name' => 'selector',
      'ext' => false,
    ),
    'anim-node-plugin' => 
    array (
      'path' => 'anim/anim-node-plugin-min.js',
      'requires' => 
      array (
        0 => 'node',
        1 => 'anim-base',
      ),
      'type' => 'js',
      'name' => 'anim-node-plugin',
      'ext' => false,
    ),
    'classnamemanager' => 
    array (
      'path' => 'classnamemanager/classnamemanager-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'classnamemanager',
      'ext' => false,
    ),
    'skin-sam-widget-position-ext' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position-ext.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position-ext',
      'requires' => 
      array (
      ),
    ),
    'dd-plugin' => 
    array (
      'path' => 'dd/dd-plugin-min.js',
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'dd-constrain',
        1 => 'dd-proxy',
      ),
      'name' => 'dd-plugin',
      'ext' => false,
    ),
    'skin-sam-widget-stdmod' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-stdmod.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stdmod',
      'requires' => 
      array (
      ),
    ),
    'overlay' => 
    array (
      'path' => 'overlay/overlay-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'widget-position',
        2 => 'widget-position-ext',
        3 => 'widget-stack',
        4 => 'widget-stdmod',
        5 => 'skin-sam-overlay',
        6 => 'skin-sam-overlay',
      ),
      'type' => 'js',
      'name' => 'overlay',
      'ext' => false,
    ),
    'datasource-polling' => 
    array (
      'path' => 'datasource/datasource-polling-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
      ),
      'type' => 'js',
      'name' => 'datasource-polling',
      'ext' => false,
    ),
    'datasource-cache' => 
    array (
      'path' => 'datasource/datasource-cache-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'cache',
      ),
      'type' => 'js',
      'name' => 'datasource-cache',
      'ext' => false,
    ),
    'get' => 
    array (
      '_provides' => 
      array (
        'get' => true,
      ),
      'path' => 'get/get-min.js',
      '_supersedes' => 
      array (
      ),
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'get',
      'ext' => false,
    ),
    'queue-run' => 
    array (
      'path' => 'queue/queue-run-min.js',
      'requires' => 
      array (
        0 => 'queue-base',
        1 => 'event-custom',
      ),
      'type' => 'js',
      'name' => 'queue-run',
      'ext' => false,
    ),
    'widget-position-ext' => 
    array (
      'path' => 'widget/widget-position-ext-min.js',
      'requires' => 
      array (
        0 => 'widget-position',
        1 => 'widget',
        2 => 'widget',
      ),
      'type' => 'js',
      'name' => 'widget-position-ext',
      'ext' => false,
    ),
    'datatype' => 
    array (
      'supersedes' => 
      array (
        0 => 'datatype-date',
        1 => 'datatype-xml',
        2 => 'datatype-number',
      ),
      'path' => 'datatype/datatype-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'datatype-date' => 
        array (
          'path' => 'datatype/datatype-date-min.js',
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'name' => 'datatype-date',
          'ext' => false,
        ),
        'datatype-xml' => 
        array (
          'path' => 'datatype/datatype-xml-min.js',
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'name' => 'datatype-xml',
          'ext' => false,
        ),
        'datatype-number' => 
        array (
          'path' => 'datatype/datatype-number-min.js',
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'name' => 'datatype-number',
          'ext' => false,
        ),
      ),
      'name' => 'datatype',
      'requires' => 
      array (
      ),
    ),
    'dd-drop' => 
    array (
      'path' => 'dd/dd-drop-min.js',
      'requires' => 
      array (
        0 => 'dd-ddm-drop',
      ),
      'type' => 'js',
      'name' => 'dd-drop',
      'ext' => false,
    ),
    'dataschema' => 
    array (
      'supersedes' => 
      array (
        0 => 'dataschema-json',
        1 => 'dataschema-array',
        2 => 'dataschema-xml',
        3 => 'dataschema-text',
        4 => 'dataschema-base',
      ),
      'path' => 'dataschema/dataschema-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'dataschema-json' => 
        array (
          'path' => 'dataschema/dataschema-json-min.js',
          'requires' => 
          array (
            0 => 'dataschema-base',
          ),
          'type' => 'js',
          'name' => 'dataschema-json',
          'ext' => false,
        ),
        'dataschema-array' => 
        array (
          'path' => 'dataschema/dataschema-array-min.js',
          'requires' => 
          array (
            0 => 'dataschema-base',
          ),
          'type' => 'js',
          'name' => 'dataschema-array',
          'ext' => false,
        ),
        'dataschema-xml' => 
        array (
          'path' => 'dataschema/dataschema-xml-min.js',
          'requires' => 
          array (
            0 => 'dataschema-base',
          ),
          'type' => 'js',
          'name' => 'dataschema-xml',
          'ext' => false,
        ),
        'dataschema-text' => 
        array (
          'path' => 'dataschema/dataschema-text-min.js',
          'requires' => 
          array (
            0 => 'dataschema-base',
          ),
          'type' => 'js',
          'name' => 'dataschema-text',
          'ext' => false,
        ),
        'dataschema-base' => 
        array (
          'path' => 'dataschema/dataschema-base-min.js',
          'requires' => 
          array (
            0 => 'base',
          ),
          'type' => 'js',
          'name' => 'dataschema-base',
          'ext' => false,
        ),
      ),
      'name' => 'dataschema',
      'requires' => 
      array (
      ),
    ),
    'io-upload-iframe' => 
    array (
      'path' => 'io/io-upload-iframe-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'node',
      ),
      'type' => 'js',
      'name' => 'io-upload-iframe',
      'ext' => false,
    ),
    'collection' => 
    array (
      'path' => 'collection/collection-min.js',
      'requires' => 
      array (
        0 => 'oop',
      ),
      'type' => 'js',
      'name' => 'collection',
      'ext' => false,
    ),
    'dd-drop-plugin' => 
    array (
      'path' => 'dd/dd-drop-plugin-min.js',
      'requires' => 
      array (
        0 => 'dd-drop',
      ),
      'type' => 'js',
      'name' => 'dd-drop-plugin',
      'ext' => false,
    ),
    'queue-promote' => 
    array (
      'path' => 'queue/queue-promote-min.js',
      'requires' => 
      array (
        0 => 'queue',
        1 => 'queue',
      ),
      'type' => 'js',
      'name' => 'queue-promote',
      'ext' => false,
    ),
    'dataschema-array' => 
    array (
      'path' => 'dataschema/dataschema-array-min.js',
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'type' => 'js',
      'name' => 'dataschema-array',
      'ext' => false,
    ),
    'dataschema-text' => 
    array (
      'path' => 'dataschema/dataschema-text-min.js',
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'type' => 'js',
      'name' => 'dataschema-text',
      'ext' => false,
    ),
    'anim-color' => 
    array (
      'path' => 'anim/anim-color-min.js',
      'requires' => 
      array (
        0 => 'anim-base',
      ),
      'type' => 'js',
      'name' => 'anim-color',
      'ext' => false,
    ),
    'dom-base' => 
    array (
      'path' => 'dom/dom-base-min.js',
      'requires' => 
      array (
        0 => 'oop',
      ),
      'type' => 'js',
      'name' => 'dom-base',
      'ext' => false,
    ),
    'dom-style' => 
    array (
      'path' => 'dom/dom-style-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'type' => 'js',
      'name' => 'dom-style',
      'ext' => false,
    ),
    'skin-sam-overlay' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'overlay/assets/skins/sam/overlay.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-overlay',
      'requires' => 
      array (
      ),
    ),
    'skin-sam-node-menunav' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'node-menunav/assets/skins/sam/node-menunav.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-node-menunav',
      'requires' => 
      array (
      ),
    ),
    'dump' => 
    array (
      'path' => 'dump/dump-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'dump',
      'ext' => false,
    ),
    'yui' => 
    array (
      'supersedes' => 
      array (
        0 => 'yui-base',
        1 => 'get',
        2 => 'loader',
        3 => 'queue-base',
      ),
      '_provides' => 
      array (
        'queue-base' => true,
        'yui-base' => true,
        'yui' => true,
        'get' => true,
        'loader' => true,
      ),
      'path' => 'yui/yui-min.js',
      '_supersedes' => 
      array (
        'queue-base' => true,
        'yui-base' => true,
        'get' => true,
        'loader' => true,
      ),
      'type' => 'js',
      'ext' => false,
      'name' => 'yui',
      'requires' => 
      array (
      ),
    ),
    'cssgrids-context' => 
    array (
      'path' => 'cssgrids/grids-context-min.css',
      'requires' => 
      array (
        0 => 'cssfonts-context',
      ),
      'type' => 'css',
      'optional' => 
      array (
        0 => 'cssreset-context',
      ),
      'name' => 'cssgrids-context',
      'ext' => false,
    ),
    'node' => 
    array (
      'requires' => 
      array (
        0 => 'dom',
        1 => 'base',
      ),
      'path' => 'node/node-min.js',
      'supersedes' => 
      array (
        0 => 'node-screen',
        1 => 'node-base',
        2 => 'node-style',
      ),
      'expound' => 'event',
      'type' => 'js',
      'submodules' => 
      array (
        'node-screen' => 
        array (
          'path' => 'node/node-screen-min.js',
          'requires' => 
          array (
            0 => 'dom-screen',
            1 => 'node-base',
          ),
          'type' => 'js',
          'name' => 'node-screen',
          'ext' => false,
        ),
        'node-base' => 
        array (
          'path' => 'node/node-base-min.js',
          'requires' => 
          array (
            0 => 'dom-base',
            1 => 'base',
            2 => 'selector',
          ),
          'type' => 'js',
          'name' => 'node-base',
          'ext' => false,
        ),
        'node-style' => 
        array (
          'path' => 'node/node-style-min.js',
          'requires' => 
          array (
            0 => 'dom-style',
            1 => 'node-base',
          ),
          'type' => 'js',
          'name' => 'node-style',
          'ext' => false,
        ),
      ),
      'plugins' => 
      array (
        'node-event-simulate' => 
        array (
          'path' => 'node/node-event-simulate-min.js',
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'event-simulate',
            2 => 'node',
            3 => 'node',
          ),
          'type' => 'js',
          'name' => 'node-event-simulate',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'node',
      'rollup' => 2,
    ),
    'skin-sam-slider' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'slider/assets/skins/sam/slider.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-slider',
      'requires' => 
      array (
      ),
    ),
    'anim-curve' => 
    array (
      'path' => 'anim/anim-curve-min.js',
      'requires' => 
      array (
        0 => 'anim-xy',
      ),
      'type' => 'js',
      'name' => 'anim-curve',
      'ext' => false,
    ),
    'test' => 
    array (
      'path' => 'test/test-min.js',
      'requires' => 
      array (
        0 => 'substitute',
        1 => 'node',
        2 => 'json',
        3 => 'event-simulate',
      ),
      'type' => 'js',
      'name' => 'test',
      'ext' => false,
    ),
    'skin-sam-widget' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'widget/assets/skins/sam/widget.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget',
      'requires' => 
      array (
      ),
    ),
    'json' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'json/json-min.js',
      'supersedes' => 
      array (
        0 => 'json-parse',
        1 => 'json-stringify',
      ),
      '_supersedes' => 
      array (
        'json-parse' => true,
        'json-stringify' => true,
      ),
      '_provides' => 
      array (
        'json-parse' => true,
        'json' => true,
        'json-stringify' => true,
      ),
      'submodules' => 
      array (
        'json-parse' => 
        array (
          '_provides' => 
          array (
            'json-parse' => true,
          ),
          'path' => 'json/json-parse-min.js',
          '_supersedes' => 
          array (
          ),
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'name' => 'json-parse',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'yui-base',
          ),
        ),
        'json-stringify' => 
        array (
          '_provides' => 
          array (
            'json-stringify' => true,
          ),
          'path' => 'json/json-stringify-min.js',
          '_supersedes' => 
          array (
          ),
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'name' => 'json-stringify',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'yui-base',
          ),
        ),
      ),
      'type' => 'js',
      'expanded' => 
      array (
        0 => 'json-parse',
        1 => 'yui-base',
        2 => 'json-stringify',
      ),
      'ext' => false,
      'name' => 'json',
      'rollup' => 1,
    ),
    'history' => 
    array (
      'path' => 'history/history-min.js',
      'requires' => 
      array (
        0 => 'node',
      ),
      'type' => 'js',
      'name' => 'history',
      'ext' => false,
    ),
    'dataschema-json' => 
    array (
      'path' => 'dataschema/dataschema-json-min.js',
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'type' => 'js',
      'name' => 'dataschema-json',
      'ext' => false,
    ),
    'datasource-local' => 
    array (
      'path' => 'datasource/datasource-local-min.js',
      'requires' => 
      array (
        0 => 'base',
      ),
      'type' => 'js',
      'name' => 'datasource-local',
      'ext' => false,
    ),
    'node-focusmanager' => 
    array (
      'path' => 'node-focusmanager/node-focusmanager-min.js',
      'requires' => 
      array (
        0 => 'node',
        1 => 'plugin',
      ),
      'type' => 'js',
      'name' => 'node-focusmanager',
      'ext' => false,
    ),
    'queue-base' => 
    array (
      '_provides' => 
      array (
        'queue-base' => true,
      ),
      'path' => 'queue/queue-base-min.js',
      '_supersedes' => 
      array (
      ),
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'queue-base',
      'ext' => false,
    ),
    'compat' => 
    array (
      'path' => 'compat/compat-min.js',
      'requires' => 
      array (
        0 => 'node',
        1 => 'dump',
        2 => 'substitute',
      ),
      'type' => 'js',
      'name' => 'compat',
      'ext' => false,
    ),
    'cookie' => 
    array (
      'path' => 'cookie/cookie-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'cookie',
      'ext' => false,
    ),
    'datasource-scriptnode' => 
    array (
      'path' => 'datasource/datasource-scriptnode-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'get',
      ),
      'type' => 'js',
      'name' => 'datasource-scriptnode',
      'ext' => false,
    ),
    'anim' => 
    array (
      'supersedes' => 
      array (
        0 => 'anim-color',
        1 => 'anim-scroll',
        2 => 'anim-node-plugin',
        3 => 'anim-base',
        4 => 'anim-curve',
        5 => 'anim-easing',
        6 => 'anim-xy',
      ),
      'path' => 'anim/anim-min.js',
      'rollup' => 4,
      'requires' => 
      array (
        0 => 'base',
        1 => 'node',
      ),
      'type' => 'js',
      'submodules' => 
      array (
        'anim-color' => 
        array (
          'path' => 'anim/anim-color-min.js',
          'requires' => 
          array (
            0 => 'anim-base',
          ),
          'type' => 'js',
          'name' => 'anim-color',
          'ext' => false,
        ),
        'anim-scroll' => 
        array (
          'path' => 'anim/anim-scroll-min.js',
          'requires' => 
          array (
            0 => 'anim-base',
          ),
          'type' => 'js',
          'name' => 'anim-scroll',
          'ext' => false,
        ),
        'anim-node-plugin' => 
        array (
          'path' => 'anim/anim-node-plugin-min.js',
          'requires' => 
          array (
            0 => 'node',
            1 => 'anim-base',
          ),
          'type' => 'js',
          'name' => 'anim-node-plugin',
          'ext' => false,
        ),
        'anim-base' => 
        array (
          'path' => 'anim/anim-base-min.js',
          'requires' => 
          array (
            0 => 'base',
            1 => 'node-style',
          ),
          'type' => 'js',
          'name' => 'anim-base',
          'ext' => false,
        ),
        'anim-curve' => 
        array (
          'path' => 'anim/anim-curve-min.js',
          'requires' => 
          array (
            0 => 'anim-xy',
          ),
          'type' => 'js',
          'name' => 'anim-curve',
          'ext' => false,
        ),
        'anim-easing' => 
        array (
          'path' => 'anim/anim-easing-min.js',
          'requires' => 
          array (
            0 => 'anim-base',
          ),
          'type' => 'js',
          'name' => 'anim-easing',
          'ext' => false,
        ),
        'anim-xy' => 
        array (
          'path' => 'anim/anim-xy-min.js',
          'requires' => 
          array (
            0 => 'anim-base',
            1 => 'node-screen',
          ),
          'type' => 'js',
          'name' => 'anim-xy',
          'ext' => false,
        ),
      ),
      'name' => 'anim',
      'ext' => false,
    ),
    'io-base' => 
    array (
      'path' => 'io/io-base-min.js',
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'type' => 'js',
      'name' => 'io-base',
      'ext' => false,
    ),
    'datasource-function' => 
    array (
      'path' => 'datasource/datasource-function-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
      ),
      'type' => 'js',
      'name' => 'datasource-function',
      'ext' => false,
    ),
    'widget-stdmod' => 
    array (
      'path' => 'widget/widget-stdmod-min.js',
      'requires' => 
      array (
        0 => 'widget',
        1 => 'widget',
      ),
      'type' => 'js',
      'name' => 'widget-stdmod',
      'ext' => false,
    ),
    'event' => 
    array (
      'path' => 'event/event-min.js',
      'requires' => 
      array (
        0 => 'event-custom',
        1 => 'node',
      ),
      'type' => 'js',
      'name' => 'event',
      'ext' => false,
    ),
    'cssfonts' => 
    array (
      'path' => 'cssfonts/fonts-min.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'cssfonts',
      'requires' => 
      array (
      ),
    ),
    'skin-sam-console' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'console/assets/skins/sam/console.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-console',
      'requires' => 
      array (
      ),
    ),
    'anim-xy' => 
    array (
      'path' => 'anim/anim-xy-min.js',
      'requires' => 
      array (
        0 => 'anim-base',
        1 => 'node-screen',
      ),
      'type' => 'js',
      'name' => 'anim-xy',
      'ext' => false,
    ),
    'datasource-xmlschema' => 
    array (
      'path' => 'datasource/datasource-xmlschema-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-xml',
      ),
      'type' => 'js',
      'name' => 'datasource-xmlschema',
      'ext' => false,
    ),
    'cssbase-context' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'cssbase/base-context-min.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'cssbase-context',
      'requires' => 
      array (
      ),
    ),
    'node-event-simulate' => 
    array (
      'path' => 'node/node-event-simulate-min.js',
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'event-simulate',
        2 => 'node',
        3 => 'node',
      ),
      'type' => 'js',
      'name' => 'node-event-simulate',
      'ext' => false,
    ),
    'io' => 
    array (
      'supersedes' => 
      array (
        0 => 'io-queue',
        1 => 'io-upload-iframe',
        2 => 'io-base',
        3 => 'io-form',
        4 => 'io-xdr',
      ),
      'path' => 'io/io-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'io-queue' => 
        array (
          'path' => 'io/io-queue-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'queue-promote',
          ),
          'type' => 'js',
          'name' => 'io-queue',
          'ext' => false,
        ),
        'io-upload-iframe' => 
        array (
          'path' => 'io/io-upload-iframe-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'node',
          ),
          'type' => 'js',
          'name' => 'io-upload-iframe',
          'ext' => false,
        ),
        'io-base' => 
        array (
          'path' => 'io/io-base-min.js',
          'requires' => 
          array (
            0 => 'event-custom',
          ),
          'type' => 'js',
          'name' => 'io-base',
          'ext' => false,
        ),
        'io-form' => 
        array (
          'path' => 'io/io-form-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'node',
          ),
          'type' => 'js',
          'name' => 'io-form',
          'ext' => false,
        ),
        'io-xdr' => 
        array (
          'path' => 'io/io-xdr-min.js',
          'requires' => 
          array (
            0 => 'io-base',
          ),
          'type' => 'js',
          'name' => 'io-xdr',
          'ext' => false,
        ),
      ),
      'name' => 'io',
      'requires' => 
      array (
      ),
    ),
    'console' => 
    array (
      'path' => 'console/console-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'substitute',
        2 => 'skin-sam-console',
        3 => 'skin-sam-console',
      ),
      'type' => 'js',
      'name' => 'console',
      'ext' => false,
    ),
    'attribute' => 
    array (
      'path' => 'attribute/attribute-min.js',
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'type' => 'js',
      'name' => 'attribute',
      'ext' => false,
    ),
    'anim-easing' => 
    array (
      'path' => 'anim/anim-easing-min.js',
      'requires' => 
      array (
        0 => 'anim-base',
      ),
      'type' => 'js',
      'name' => 'anim-easing',
      'ext' => false,
    ),
    'profiler' => 
    array (
      'path' => 'profiler/profiler-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'profiler',
      'ext' => false,
    ),
    'cache' => 
    array (
      'path' => 'cache/cache-min.js',
      'requires' => 
      array (
        0 => 'plugin',
      ),
      'type' => 'js',
      'name' => 'cache',
      'ext' => false,
    ),
    'node-style' => 
    array (
      'path' => 'node/node-style-min.js',
      'requires' => 
      array (
        0 => 'dom-style',
        1 => 'node-base',
      ),
      'type' => 'js',
      'name' => 'node-style',
      'ext' => false,
    ),
    'io-queue' => 
    array (
      'path' => 'io/io-queue-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'queue-promote',
      ),
      'type' => 'js',
      'name' => 'io-queue',
      'ext' => false,
    ),
    'loader' => 
    array (
      '_provides' => 
      array (
        'loader' => true,
      ),
      'path' => 'loader/loader-min.js',
      '_supersedes' => 
      array (
      ),
      'requires' => 
      array (
        0 => 'get',
      ),
      'type' => 'js',
      'name' => 'loader',
      'ext' => false,
    ),
    'plugin' => 
    array (
      'path' => 'plugin/plugin-min.js',
      'requires' => 
      array (
        0 => 'base',
      ),
      'type' => 'js',
      'name' => 'plugin',
      'ext' => false,
    ),
    'base' => 
    array (
      'supersedes' => 
      array (
        0 => 'base-base',
        1 => 'base-build',
      ),
      'path' => 'base/base-min.js',
      'rollup' => 1,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'base-base' => 
        array (
          'path' => 'base/base-base-min.js',
          'requires' => 
          array (
            0 => 'attribute',
          ),
          'type' => 'js',
          'name' => 'base-base',
          'ext' => false,
        ),
        'base-build' => 
        array (
          'path' => 'base/base-build-min.js',
          'requires' => 
          array (
            0 => 'base-base',
          ),
          'type' => 'js',
          'name' => 'base-build',
          'ext' => false,
        ),
      ),
      'name' => 'base',
      'requires' => 
      array (
      ),
    ),
    'datasource-jsonschema' => 
    array (
      'path' => 'datasource/datasource-jsonschema-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-json',
      ),
      'type' => 'js',
      'name' => 'datasource-jsonschema',
      'ext' => false,
    ),
    'datatype-number' => 
    array (
      'path' => 'datatype/datatype-number-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'datatype-number',
      'ext' => false,
    ),
    'event-simulate' => 
    array (
      'path' => 'event-simulate/event-simulate-min.js',
      'requires' => 
      array (
        0 => 'event',
      ),
      'type' => 'js',
      'name' => 'event-simulate',
      'ext' => false,
    ),
    'dataschema-base' => 
    array (
      'path' => 'dataschema/dataschema-base-min.js',
      'requires' => 
      array (
        0 => 'base',
      ),
      'type' => 'js',
      'name' => 'dataschema-base',
      'ext' => false,
    ),
    'widget-position' => 
    array (
      'path' => 'widget/widget-position-min.js',
      'requires' => 
      array (
        0 => 'widget',
        1 => 'widget',
      ),
      'type' => 'js',
      'name' => 'widget-position',
      'ext' => false,
    ),
    'dom' => 
    array (
      'requires' => 
      array (
        0 => 'oop',
      ),
      'path' => 'dom/dom-min.js',
      'supersedes' => 
      array (
        0 => 'selector-native',
        1 => 'dom-screen',
        2 => 'dom-base',
        3 => 'dom-style',
        4 => 'selector',
      ),
      'submodules' => 
      array (
        'selector-native' => 
        array (
          'path' => 'dom/selector-native-min.js',
          'requires' => 
          array (
            0 => 'dom-base',
          ),
          'type' => 'js',
          'name' => 'selector-native',
          'ext' => false,
        ),
        'dom-screen' => 
        array (
          'path' => 'dom/dom-screen-min.js',
          'requires' => 
          array (
            0 => 'dom-base',
            1 => 'dom-style',
          ),
          'type' => 'js',
          'name' => 'dom-screen',
          'ext' => false,
        ),
        'dom-base' => 
        array (
          'path' => 'dom/dom-base-min.js',
          'requires' => 
          array (
            0 => 'oop',
          ),
          'type' => 'js',
          'name' => 'dom-base',
          'ext' => false,
        ),
        'dom-style' => 
        array (
          'path' => 'dom/dom-style-min.js',
          'requires' => 
          array (
            0 => 'dom-base',
          ),
          'type' => 'js',
          'name' => 'dom-style',
          'ext' => false,
        ),
        'selector' => 
        array (
          'path' => 'dom/selector-min.js',
          'requires' => 
          array (
            0 => 'dom-base',
          ),
          'type' => 'js',
          'name' => 'selector',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'plugins' => 
      array (
        'selector-css3' => 
        array (
          'path' => 'dom/selector-css3-min.js',
          'requires' => 
          array (
            0 => 'selector',
            1 => 'dom',
            2 => 'dom',
          ),
          'type' => 'js',
          'name' => 'selector-css3',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'dom',
      'rollup' => 4,
    ),
    'node-screen' => 
    array (
      'path' => 'node/node-screen-min.js',
      'requires' => 
      array (
        0 => 'dom-screen',
        1 => 'node-base',
      ),
      'type' => 'js',
      'name' => 'node-screen',
      'ext' => false,
    ),
    'io-form' => 
    array (
      'path' => 'io/io-form-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'node',
      ),
      'type' => 'js',
      'name' => 'io-form',
      'ext' => false,
    ),
    'dd' => 
    array (
      'supersedes' => 
      array (
        0 => 'dd-drop-plugin',
        1 => 'dd-constrain',
        2 => 'dd-proxy',
        3 => 'dd-scroll',
        4 => 'dd-ddm',
        5 => 'dd-ddm-drop',
        6 => 'dd-ddm-base',
        7 => 'dd-drag',
        8 => 'dd-plugin',
        9 => 'dd-drop',
      ),
      'path' => 'dd/dd-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'dd-drop-plugin' => 
        array (
          'path' => 'dd/dd-drop-plugin-min.js',
          'requires' => 
          array (
            0 => 'dd-drop',
          ),
          'type' => 'js',
          'name' => 'dd-drop-plugin',
          'ext' => false,
        ),
        'dd-constrain' => 
        array (
          'path' => 'dd/dd-constrain-min.js',
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'type' => 'js',
          'name' => 'dd-constrain',
          'ext' => false,
        ),
        'dd-proxy' => 
        array (
          'path' => 'dd/dd-proxy-min.js',
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'type' => 'js',
          'name' => 'dd-proxy',
          'ext' => false,
        ),
        'dd-scroll' => 
        array (
          'path' => 'dd/dd-scroll-min.js',
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'type' => 'js',
          'name' => 'dd-scroll',
          'ext' => false,
        ),
        'dd-ddm' => 
        array (
          'path' => 'dd/dd-ddm-min.js',
          'requires' => 
          array (
            0 => 'dd-ddm-base',
          ),
          'type' => 'js',
          'name' => 'dd-ddm',
          'ext' => false,
        ),
        'dd-ddm-drop' => 
        array (
          'path' => 'dd/dd-ddm-drop-min.js',
          'requires' => 
          array (
            0 => 'dd-ddm',
          ),
          'type' => 'js',
          'name' => 'dd-ddm-drop',
          'ext' => false,
        ),
        'dd-ddm-base' => 
        array (
          'path' => 'dd/dd-ddm-base-min.js',
          'requires' => 
          array (
            0 => 'node',
            1 => 'base',
          ),
          'type' => 'js',
          'name' => 'dd-ddm-base',
          'ext' => false,
        ),
        'dd-drag' => 
        array (
          'path' => 'dd/dd-drag-min.js',
          'requires' => 
          array (
            0 => 'dd-ddm-base',
          ),
          'type' => 'js',
          'name' => 'dd-drag',
          'ext' => false,
        ),
        'dd-plugin' => 
        array (
          'path' => 'dd/dd-plugin-min.js',
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'type' => 'js',
          'optional' => 
          array (
            0 => 'dd-constrain',
            1 => 'dd-proxy',
          ),
          'name' => 'dd-plugin',
          'ext' => false,
        ),
        'dd-drop' => 
        array (
          'path' => 'dd/dd-drop-min.js',
          'requires' => 
          array (
            0 => 'dd-ddm-drop',
          ),
          'type' => 'js',
          'name' => 'dd-drop',
          'ext' => false,
        ),
      ),
      'name' => 'dd',
      'requires' => 
      array (
      ),
    ),
    'base-base' => 
    array (
      'path' => 'base/base-base-min.js',
      'requires' => 
      array (
        0 => 'attribute',
      ),
      'type' => 'js',
      'name' => 'base-base',
      'ext' => false,
    ),
    'skin-sam-widget-position' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position',
      'requires' => 
      array (
      ),
    ),
    'dd-ddm-drop' => 
    array (
      'path' => 'dd/dd-ddm-drop-min.js',
      'requires' => 
      array (
        0 => 'dd-ddm',
      ),
      'type' => 'js',
      'name' => 'dd-ddm-drop',
      'ext' => false,
    ),
    'slider' => 
    array (
      'path' => 'slider/slider-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'dd-constrain',
        2 => 'skin-sam-slider',
        3 => 'skin-sam-slider',
      ),
      'type' => 'js',
      'name' => 'slider',
      'ext' => false,
    ),
    'substitute' => 
    array (
      'path' => 'substitute/substitute-min.js',
      'type' => 'js',
      'ext' => false,
      'optional' => 
      array (
        0 => 'dump',
      ),
      'name' => 'substitute',
      'requires' => 
      array (
      ),
    ),
    'widget-stack' => 
    array (
      'path' => 'widget/widget-stack-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'skin-sam-widget-stack',
        2 => 'widget',
        3 => 'skin-sam-widget-stack',
      ),
      'type' => 'js',
      'name' => 'widget-stack',
      'ext' => false,
    ),
    'base-build' => 
    array (
      'path' => 'base/base-build-min.js',
      'requires' => 
      array (
        0 => 'base-base',
      ),
      'type' => 'js',
      'name' => 'base-build',
      'ext' => false,
    ),
    'node-menunav' => 
    array (
      'path' => 'node-menunav/node-menunav-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'node',
        1 => 'classnamemanager',
        2 => 'plugin',
        3 => 'node-focusmanager',
        4 => 'skin-sam-node-menunav',
        5 => 'skin-sam-node-menunav',
      ),
      'type' => 'js',
      'name' => 'node-menunav',
      'ext' => false,
    ),
    'selector-native' => 
    array (
      'path' => 'dom/selector-native-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'type' => 'js',
      'name' => 'selector-native',
      'ext' => false,
    ),
    'widget' => 
    array (
      'path' => 'widget/widget-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'base',
        1 => 'node',
        2 => 'classnamemanager',
        3 => 'skin-sam-widget',
        4 => 'skin-sam-widget',
      ),
      'type' => 'js',
      'name' => 'widget',
      'ext' => false,
      'plugins' => 
      array (
        'widget-stack' => 
        array (
          'path' => 'widget/widget-stack-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'widget',
            1 => 'skin-sam-widget-stack',
            2 => 'widget',
            3 => 'skin-sam-widget-stack',
          ),
          'type' => 'js',
          'name' => 'widget-stack',
          'ext' => false,
        ),
        'widget-position' => 
        array (
          'path' => 'widget/widget-position-min.js',
          'requires' => 
          array (
            0 => 'widget',
            1 => 'widget',
          ),
          'type' => 'js',
          'name' => 'widget-position',
          'ext' => false,
        ),
        'widget-stdmod' => 
        array (
          'path' => 'widget/widget-stdmod-min.js',
          'requires' => 
          array (
            0 => 'widget',
            1 => 'widget',
          ),
          'type' => 'js',
          'name' => 'widget-stdmod',
          'ext' => false,
        ),
        'widget-position-ext' => 
        array (
          'path' => 'widget/widget-position-ext-min.js',
          'requires' => 
          array (
            0 => 'widget-position',
            1 => 'widget',
            2 => 'widget',
          ),
          'type' => 'js',
          'name' => 'widget-position-ext',
          'ext' => false,
        ),
      ),
    ),
    'cssreset' => 
    array (
      'path' => 'cssreset/reset-min.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'cssreset',
      'requires' => 
      array (
      ),
    ),
    'json-stringify' => 
    array (
      '_provides' => 
      array (
        'json-stringify' => true,
      ),
      'path' => 'json/json-stringify-min.js',
      '_supersedes' => 
      array (
      ),
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'json-stringify',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
    ),
    'anim-scroll' => 
    array (
      'path' => 'anim/anim-scroll-min.js',
      'requires' => 
      array (
        0 => 'anim-base',
      ),
      'type' => 'js',
      'name' => 'anim-scroll',
      'ext' => false,
    ),
    'dd-scroll' => 
    array (
      'path' => 'dd/dd-scroll-min.js',
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'type' => 'js',
      'name' => 'dd-scroll',
      'ext' => false,
    ),
    'datasource-textschema' => 
    array (
      'path' => 'datasource/datasource-textschema-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-text',
      ),
      'type' => 'js',
      'name' => 'datasource-textschema',
      'ext' => false,
    ),
    'datatype-xml' => 
    array (
      'path' => 'datatype/datatype-xml-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'datatype-xml',
      'ext' => false,
    ),
    'dd-proxy' => 
    array (
      'path' => 'dd/dd-proxy-min.js',
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'type' => 'js',
      'name' => 'dd-proxy',
      'ext' => false,
    ),
    'dd-constrain' => 
    array (
      'path' => 'dd/dd-constrain-min.js',
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'type' => 'js',
      'name' => 'dd-constrain',
      'ext' => false,
    ),
    'dd-ddm-base' => 
    array (
      'path' => 'dd/dd-ddm-base-min.js',
      'requires' => 
      array (
        0 => 'node',
        1 => 'base',
      ),
      'type' => 'js',
      'name' => 'dd-ddm-base',
      'ext' => false,
    ),
    'cssreset-context' => 
    array (
      'path' => 'cssreset/reset-context-min.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'cssreset-context',
      'requires' => 
      array (
      ),
    ),
    'dataschema-xml' => 
    array (
      'path' => 'dataschema/dataschema-xml-min.js',
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'type' => 'js',
      'name' => 'dataschema-xml',
      'ext' => false,
    ),
    'node-base' => 
    array (
      'path' => 'node/node-base-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
        1 => 'base',
        2 => 'selector',
      ),
      'type' => 'js',
      'name' => 'node-base',
      'ext' => false,
    ),
    'queue' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'queue/queue-min.js',
      'supersedes' => 
      array (
        0 => 'queue-base',
        1 => 'queue-run',
      ),
      'submodules' => 
      array (
        'queue-base' => 
        array (
          '_provides' => 
          array (
            'queue-base' => true,
          ),
          'path' => 'queue/queue-base-min.js',
          '_supersedes' => 
          array (
          ),
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'name' => 'queue-base',
          'ext' => false,
        ),
        'queue-run' => 
        array (
          'path' => 'queue/queue-run-min.js',
          'requires' => 
          array (
            0 => 'queue-base',
            1 => 'event-custom',
          ),
          'type' => 'js',
          'name' => 'queue-run',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'plugins' => 
      array (
        'queue-promote' => 
        array (
          'path' => 'queue/queue-promote-min.js',
          'requires' => 
          array (
            0 => 'queue',
            1 => 'queue',
          ),
          'type' => 'js',
          'name' => 'queue-promote',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'queue',
      'rollup' => 1,
    ),
    'datasource-arrayschema' => 
    array (
      'path' => 'datasource/datasource-arrayschema-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-array',
      ),
      'type' => 'js',
      'name' => 'datasource-arrayschema',
      'ext' => false,
    ),
    'cssgrids' => 
    array (
      'path' => 'cssgrids/grids-min.css',
      'requires' => 
      array (
        0 => 'cssfonts',
      ),
      'type' => 'css',
      'optional' => 
      array (
        0 => 'cssreset',
      ),
      'name' => 'cssgrids',
      'ext' => false,
    ),
    'dd-drag' => 
    array (
      'path' => 'dd/dd-drag-min.js',
      'requires' => 
      array (
        0 => 'dd-ddm-base',
      ),
      'type' => 'js',
      'name' => 'dd-drag',
      'ext' => false,
    ),
    'io-xdr' => 
    array (
      'path' => 'io/io-xdr-min.js',
      'requires' => 
      array (
        0 => 'io-base',
      ),
      'type' => 'js',
      'name' => 'io-xdr',
      'ext' => false,
    ),
    'datasource' => 
    array (
      'supersedes' => 
      array (
        0 => 'datasource-cache',
        1 => 'datasource-xmlschema',
        2 => 'datasource-arrayschema',
        3 => 'datasource-function',
        4 => 'datasource-local',
        5 => 'datasource-jsonschema',
        6 => 'datasource-polling',
        7 => 'datasource-textschema',
        8 => 'datasource-scriptnode',
        9 => 'datasource-xhr',
      ),
      'path' => 'datasource/datasource-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'datasource-cache' => 
        array (
          'path' => 'datasource/datasource-cache-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'cache',
          ),
          'type' => 'js',
          'name' => 'datasource-cache',
          'ext' => false,
        ),
        'datasource-xmlschema' => 
        array (
          'path' => 'datasource/datasource-xmlschema-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-xml',
          ),
          'type' => 'js',
          'name' => 'datasource-xmlschema',
          'ext' => false,
        ),
        'datasource-arrayschema' => 
        array (
          'path' => 'datasource/datasource-arrayschema-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-array',
          ),
          'type' => 'js',
          'name' => 'datasource-arrayschema',
          'ext' => false,
        ),
        'datasource-function' => 
        array (
          'path' => 'datasource/datasource-function-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
          ),
          'type' => 'js',
          'name' => 'datasource-function',
          'ext' => false,
        ),
        'datasource-local' => 
        array (
          'path' => 'datasource/datasource-local-min.js',
          'requires' => 
          array (
            0 => 'base',
          ),
          'type' => 'js',
          'name' => 'datasource-local',
          'ext' => false,
        ),
        'datasource-jsonschema' => 
        array (
          'path' => 'datasource/datasource-jsonschema-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-json',
          ),
          'type' => 'js',
          'name' => 'datasource-jsonschema',
          'ext' => false,
        ),
        'datasource-polling' => 
        array (
          'path' => 'datasource/datasource-polling-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
          ),
          'type' => 'js',
          'name' => 'datasource-polling',
          'ext' => false,
        ),
        'datasource-textschema' => 
        array (
          'path' => 'datasource/datasource-textschema-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-text',
          ),
          'type' => 'js',
          'name' => 'datasource-textschema',
          'ext' => false,
        ),
        'datasource-scriptnode' => 
        array (
          'path' => 'datasource/datasource-scriptnode-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'get',
          ),
          'type' => 'js',
          'name' => 'datasource-scriptnode',
          'ext' => false,
        ),
        'datasource-xhr' => 
        array (
          'path' => 'datasource/datasource-xhr-min.js',
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'io-base',
          ),
          'type' => 'js',
          'name' => 'datasource-xhr',
          'ext' => false,
        ),
      ),
      'name' => 'datasource',
      'requires' => 
      array (
      ),
    ),
    'event-custom' => 
    array (
      'path' => 'event-custom/event-custom-min.js',
      'requires' => 
      array (
        0 => 'oop',
      ),
      'type' => 'js',
      'name' => 'event-custom',
      'ext' => false,
    ),
  ),
); ?>