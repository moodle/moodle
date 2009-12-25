<?PHP 
/**
 *  Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 *  Code licensed under the BSD License:
 *  http://developer.yahoo.net/yui/license.html
 *  version: 1.0.0b2
 */
 
$GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/3.0.0/build/',
  'skin' => 
  array (
    'defaultSkin' => 'sam',
    'base' => 'assets/skins/',
    'path' => 'skin.css',
    'after' => 
    array (
      0 => 'cssreset',
      1 => 'cssfonts',
      2 => 'cssgrids',
      3 => 'cssreset-context',
      4 => 'cssfonts-context',
      5 => 'cssgrids-context',
    ),
  ),
  'moduleInfo' => 
  array (
    'dom' => 
    array (
      'requires' => 
      array (
        0 => 'oop',
      ),
      'submodules' => 
      array (
        'dom-base' => 
        array (
          'requires' => 
          array (
            0 => 'oop',
          ),
          'path' => 'dom/dom-base-min.js',
          'name' => 'dom-base',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'oop',
            1 => 'yui-base',
          ),
          'provides' => 
          array (
            'dom-base' => true,
          ),
        ),
        'dom-style' => 
        array (
          'requires' => 
          array (
            0 => 'dom-base',
          ),
          'path' => 'dom/dom-style-min.js',
          'name' => 'dom-style',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'dom-base',
            1 => 'oop',
            2 => 'yui-base',
          ),
          'provides' => 
          array (
            'dom-style' => true,
          ),
        ),
        'dom-screen' => 
        array (
          'requires' => 
          array (
            0 => 'dom-base',
            1 => 'dom-style',
          ),
          'path' => 'dom/dom-screen-min.js',
          'name' => 'dom-screen',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'dom-base',
            1 => 'oop',
            2 => 'yui-base',
            3 => 'dom-style',
          ),
          'provides' => 
          array (
            'dom-screen' => true,
          ),
        ),
        'selector-native' => 
        array (
          'requires' => 
          array (
            0 => 'dom-base',
          ),
          'path' => 'dom/selector-native-min.js',
          'name' => 'selector-native',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'dom-base',
            1 => 'oop',
            2 => 'yui-base',
          ),
          'provides' => 
          array (
            'selector-native' => true,
          ),
        ),
        'selector-css2' => 
        array (
          'requires' => 
          array (
            0 => 'selector-native',
          ),
          'path' => 'dom/selector-css2-min.js',
          'name' => 'selector-css2',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'selector-native',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
          ),
          'provides' => 
          array (
            'selector-css2' => true,
          ),
        ),
        'selector' => 
        array (
          'requires' => 
          array (
            0 => 'dom-base',
          ),
          'path' => 'dom/selector-min.js',
          'name' => 'selector',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'dom-base',
            1 => 'oop',
            2 => 'yui-base',
          ),
          'provides' => 
          array (
            'selector' => true,
          ),
        ),
      ),
      'plugins' => 
      array (
        'selector-css3' => 
        array (
          'requires' => 
          array (
            0 => 'selector-css2',
          ),
          'path' => 'dom/selector-css3-min.js',
          'name' => 'selector-css3',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'dom',
      'type' => 'js',
      'path' => 'dom/dom-min.js',
      'ext' => false,
      'supersedes' => 
      array (
        0 => 'dom-base',
        1 => 'dom-style',
        2 => 'dom-screen',
        3 => 'selector-native',
        4 => 'selector-css2',
        5 => 'selector',
      ),
      'rollup' => 4,
      'expanded' => 
      array (
        0 => 'oop',
        1 => 'yui-base',
        2 => 'dom-base',
        3 => 'dom-style',
        4 => 'dom-screen',
        5 => 'selector-native',
        6 => 'selector-css2',
        7 => 'selector',
      ),
      'provides' => 
      array (
        'dom-base' => true,
        'dom-style' => true,
        'dom-screen' => true,
        'selector-native' => true,
        'selector-css2' => true,
        'selector' => true,
        'dom' => true,
      ),
    ),
    'dom-base' => 
    array (
      'requires' => 
      array (
        0 => 'oop',
      ),
      'path' => 'dom/dom-base-min.js',
      'name' => 'dom-base',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'oop',
        1 => 'yui-base',
      ),
      'provides' => 
      array (
        'dom-base' => true,
      ),
    ),
    'dom-style' => 
    array (
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'path' => 'dom/dom-style-min.js',
      'name' => 'dom-style',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'dom-base',
        1 => 'oop',
        2 => 'yui-base',
      ),
      'provides' => 
      array (
        'dom-style' => true,
      ),
    ),
    'dom-screen' => 
    array (
      'requires' => 
      array (
        0 => 'dom-base',
        1 => 'dom-style',
      ),
      'path' => 'dom/dom-screen-min.js',
      'name' => 'dom-screen',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'dom-base',
        1 => 'oop',
        2 => 'yui-base',
        3 => 'dom-style',
      ),
      'provides' => 
      array (
        'dom-screen' => true,
      ),
    ),
    'selector-native' => 
    array (
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'path' => 'dom/selector-native-min.js',
      'name' => 'selector-native',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'dom-base',
        1 => 'oop',
        2 => 'yui-base',
      ),
      'provides' => 
      array (
        'selector-native' => true,
      ),
    ),
    'selector-css2' => 
    array (
      'requires' => 
      array (
        0 => 'selector-native',
      ),
      'path' => 'dom/selector-css2-min.js',
      'name' => 'selector-css2',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'selector-native',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
      ),
      'provides' => 
      array (
        'selector-css2' => true,
      ),
    ),
    'selector' => 
    array (
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'path' => 'dom/selector-min.js',
      'name' => 'selector',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'dom-base',
        1 => 'oop',
        2 => 'yui-base',
      ),
      'provides' => 
      array (
        'selector' => true,
      ),
    ),
    'selector-css3' => 
    array (
      'requires' => 
      array (
        0 => 'selector-css2',
      ),
      'path' => 'dom/selector-css3-min.js',
      'name' => 'selector-css3',
      'type' => 'js',
      'ext' => false,
    ),
    'node' => 
    array (
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event-base',
      ),
      'submodules' => 
      array (
        'node-base' => 
        array (
          'requires' => 
          array (
            0 => 'dom-base',
            1 => 'selector-css2',
            2 => 'event-base',
          ),
          'path' => 'node/node-base-min.js',
          'name' => 'node-base',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'dom-base',
            1 => 'oop',
            2 => 'yui-base',
            3 => 'selector-css2',
            4 => 'selector-native',
            5 => 'event-base',
            6 => 'event-custom-base',
            7 => 'yui-later',
          ),
          'provides' => 
          array (
            'node-base' => true,
          ),
        ),
        'node-style' => 
        array (
          'requires' => 
          array (
            0 => 'dom-style',
            1 => 'node-base',
          ),
          'path' => 'node/node-style-min.js',
          'name' => 'node-style',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'dom-style',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'node-base',
            5 => 'selector-css2',
            6 => 'selector-native',
            7 => 'event-base',
            8 => 'event-custom-base',
            9 => 'yui-later',
          ),
          'provides' => 
          array (
            'node-style' => true,
          ),
        ),
        'node-screen' => 
        array (
          'requires' => 
          array (
            0 => 'dom-screen',
            1 => 'node-base',
          ),
          'path' => 'node/node-screen-min.js',
          'name' => 'node-screen',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'dom-screen',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'dom-style',
            5 => 'node-base',
            6 => 'selector-css2',
            7 => 'selector-native',
            8 => 'event-base',
            9 => 'event-custom-base',
            10 => 'yui-later',
          ),
          'provides' => 
          array (
            'node-screen' => true,
          ),
        ),
        'node-pluginhost' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'pluginhost',
          ),
          'path' => 'node/node-pluginhost-min.js',
          'name' => 'node-pluginhost',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
            9 => 'pluginhost',
          ),
          'provides' => 
          array (
            'node-pluginhost' => true,
          ),
        ),
        'node-event-delegate' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'event-delegate',
          ),
          'path' => 'node/node-event-delegate-min.js',
          'name' => 'node-event-delegate',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
            9 => 'event-delegate',
          ),
          'provides' => 
          array (
            'node-event-delegate' => true,
          ),
        ),
      ),
      'plugins' => 
      array (
        'node-event-simulate' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'event-simulate',
          ),
          'path' => 'node/node-event-simulate-min.js',
          'name' => 'node-event-simulate',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'node',
      'type' => 'js',
      'path' => 'node/node-min.js',
      'ext' => false,
      'supersedes' => 
      array (
        0 => 'node-base',
        1 => 'node-style',
        2 => 'node-screen',
        3 => 'node-pluginhost',
        4 => 'node-event-delegate',
      ),
      'rollup' => 4,
      'expanded' => 
      array (
        0 => 'dom',
        1 => 'oop',
        2 => 'yui-base',
        3 => 'dom-base',
        4 => 'dom-style',
        5 => 'dom-screen',
        6 => 'selector-native',
        7 => 'selector-css2',
        8 => 'selector',
        9 => 'event-base',
        10 => 'event-custom-base',
        11 => 'yui-later',
        12 => 'node-base',
        13 => 'node-style',
        14 => 'node-screen',
        15 => 'node-pluginhost',
        16 => 'pluginhost',
        17 => 'node-event-delegate',
        18 => 'event-delegate',
      ),
      'provides' => 
      array (
        'node-base' => true,
        'node-style' => true,
        'node-screen' => true,
        'node-pluginhost' => true,
        'node-event-delegate' => true,
        'node' => true,
      ),
    ),
    'node-base' => 
    array (
      'requires' => 
      array (
        0 => 'dom-base',
        1 => 'selector-css2',
        2 => 'event-base',
      ),
      'path' => 'node/node-base-min.js',
      'name' => 'node-base',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'dom-base',
        1 => 'oop',
        2 => 'yui-base',
        3 => 'selector-css2',
        4 => 'selector-native',
        5 => 'event-base',
        6 => 'event-custom-base',
        7 => 'yui-later',
      ),
      'provides' => 
      array (
        'node-base' => true,
      ),
    ),
    'node-style' => 
    array (
      'requires' => 
      array (
        0 => 'dom-style',
        1 => 'node-base',
      ),
      'path' => 'node/node-style-min.js',
      'name' => 'node-style',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'dom-style',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'node-base',
        5 => 'selector-css2',
        6 => 'selector-native',
        7 => 'event-base',
        8 => 'event-custom-base',
        9 => 'yui-later',
      ),
      'provides' => 
      array (
        'node-style' => true,
      ),
    ),
    'node-screen' => 
    array (
      'requires' => 
      array (
        0 => 'dom-screen',
        1 => 'node-base',
      ),
      'path' => 'node/node-screen-min.js',
      'name' => 'node-screen',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'dom-screen',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'dom-style',
        5 => 'node-base',
        6 => 'selector-css2',
        7 => 'selector-native',
        8 => 'event-base',
        9 => 'event-custom-base',
        10 => 'yui-later',
      ),
      'provides' => 
      array (
        'node-screen' => true,
      ),
    ),
    'node-pluginhost' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'pluginhost',
      ),
      'path' => 'node/node-pluginhost-min.js',
      'name' => 'node-pluginhost',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
        9 => 'pluginhost',
      ),
      'provides' => 
      array (
        'node-pluginhost' => true,
      ),
    ),
    'node-event-delegate' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'event-delegate',
      ),
      'path' => 'node/node-event-delegate-min.js',
      'name' => 'node-event-delegate',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
        9 => 'event-delegate',
      ),
      'provides' => 
      array (
        'node-event-delegate' => true,
      ),
    ),
    'node-event-simulate' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'event-simulate',
      ),
      'path' => 'node/node-event-simulate-min.js',
      'name' => 'node-event-simulate',
      'type' => 'js',
      'ext' => false,
    ),
    'anim' => 
    array (
      'submodules' => 
      array (
        'anim-base' => 
        array (
          'requires' => 
          array (
            0 => 'base-base',
            1 => 'node-style',
          ),
          'path' => 'anim/anim-base-min.js',
          'name' => 'anim-base',
          'type' => 'js',
          'ext' => false,
        ),
        'anim-color' => 
        array (
          'requires' => 
          array (
            0 => 'anim-base',
          ),
          'path' => 'anim/anim-color-min.js',
          'name' => 'anim-color',
          'type' => 'js',
          'ext' => false,
        ),
        'anim-easing' => 
        array (
          'requires' => 
          array (
            0 => 'anim-base',
          ),
          'path' => 'anim/anim-easing-min.js',
          'name' => 'anim-easing',
          'type' => 'js',
          'ext' => false,
        ),
        'anim-scroll' => 
        array (
          'requires' => 
          array (
            0 => 'anim-base',
          ),
          'path' => 'anim/anim-scroll-min.js',
          'name' => 'anim-scroll',
          'type' => 'js',
          'ext' => false,
        ),
        'anim-xy' => 
        array (
          'requires' => 
          array (
            0 => 'anim-base',
            1 => 'node-screen',
          ),
          'path' => 'anim/anim-xy-min.js',
          'name' => 'anim-xy',
          'type' => 'js',
          'ext' => false,
        ),
        'anim-curve' => 
        array (
          'requires' => 
          array (
            0 => 'anim-xy',
          ),
          'path' => 'anim/anim-curve-min.js',
          'name' => 'anim-curve',
          'type' => 'js',
          'ext' => false,
        ),
        'anim-node-plugin' => 
        array (
          'requires' => 
          array (
            0 => 'node-pluginhost',
            1 => 'anim-base',
          ),
          'path' => 'anim/anim-node-plugin-min.js',
          'name' => 'anim-node-plugin',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'anim',
      'type' => 'js',
      'path' => 'anim/anim-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'anim-base',
        1 => 'anim-color',
        2 => 'anim-easing',
        3 => 'anim-scroll',
        4 => 'anim-xy',
        5 => 'anim-curve',
        6 => 'anim-node-plugin',
      ),
      'rollup' => 4,
    ),
    'anim-base' => 
    array (
      'requires' => 
      array (
        0 => 'base-base',
        1 => 'node-style',
      ),
      'path' => 'anim/anim-base-min.js',
      'name' => 'anim-base',
      'type' => 'js',
      'ext' => false,
    ),
    'anim-color' => 
    array (
      'requires' => 
      array (
        0 => 'anim-base',
      ),
      'path' => 'anim/anim-color-min.js',
      'name' => 'anim-color',
      'type' => 'js',
      'ext' => false,
    ),
    'anim-easing' => 
    array (
      'requires' => 
      array (
        0 => 'anim-base',
      ),
      'path' => 'anim/anim-easing-min.js',
      'name' => 'anim-easing',
      'type' => 'js',
      'ext' => false,
    ),
    'anim-scroll' => 
    array (
      'requires' => 
      array (
        0 => 'anim-base',
      ),
      'path' => 'anim/anim-scroll-min.js',
      'name' => 'anim-scroll',
      'type' => 'js',
      'ext' => false,
    ),
    'anim-xy' => 
    array (
      'requires' => 
      array (
        0 => 'anim-base',
        1 => 'node-screen',
      ),
      'path' => 'anim/anim-xy-min.js',
      'name' => 'anim-xy',
      'type' => 'js',
      'ext' => false,
    ),
    'anim-curve' => 
    array (
      'requires' => 
      array (
        0 => 'anim-xy',
      ),
      'path' => 'anim/anim-curve-min.js',
      'name' => 'anim-curve',
      'type' => 'js',
      'ext' => false,
    ),
    'anim-node-plugin' => 
    array (
      'requires' => 
      array (
        0 => 'node-pluginhost',
        1 => 'anim-base',
      ),
      'path' => 'anim/anim-node-plugin-min.js',
      'name' => 'anim-node-plugin',
      'type' => 'js',
      'ext' => false,
    ),
    'attribute' => 
    array (
      'submodules' => 
      array (
        'attribute-base' => 
        array (
          'requires' => 
          array (
            0 => 'event-custom',
          ),
          'path' => 'attribute/attribute-base-min.js',
          'name' => 'attribute-base',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'event-custom',
            1 => 'event-custom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'yui-later',
            5 => 'event-custom-complex',
          ),
          'provides' => 
          array (
            'attribute-base' => true,
          ),
        ),
        'attribute-complex' => 
        array (
          'requires' => 
          array (
            0 => 'attribute-base',
          ),
          'path' => 'attribute/attribute-complex-min.js',
          'name' => 'attribute-complex',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'attribute',
      'type' => 'js',
      'path' => 'attribute/attribute-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'attribute-base',
        1 => 'attribute-complex',
      ),
      'rollup' => 2,
    ),
    'attribute-base' => 
    array (
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'path' => 'attribute/attribute-base-min.js',
      'name' => 'attribute-base',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'event-custom',
        1 => 'event-custom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'yui-later',
        5 => 'event-custom-complex',
      ),
      'provides' => 
      array (
        'attribute-base' => true,
      ),
    ),
    'attribute-complex' => 
    array (
      'requires' => 
      array (
        0 => 'attribute-base',
      ),
      'path' => 'attribute/attribute-complex-min.js',
      'name' => 'attribute-complex',
      'type' => 'js',
      'ext' => false,
    ),
    'base' => 
    array (
      'submodules' => 
      array (
        'base-base' => 
        array (
          'requires' => 
          array (
            0 => 'attribute-base',
          ),
          'path' => 'base/base-base-min.js',
          'name' => 'base-base',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'attribute-base',
            1 => 'event-custom',
            2 => 'event-custom-base',
            3 => 'oop',
            4 => 'yui-base',
            5 => 'yui-later',
            6 => 'event-custom-complex',
          ),
          'provides' => 
          array (
            'base-base' => true,
          ),
        ),
        'base-build' => 
        array (
          'requires' => 
          array (
            0 => 'base-base',
          ),
          'path' => 'base/base-build-min.js',
          'name' => 'base-build',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'base-base',
            1 => 'attribute-base',
            2 => 'event-custom',
            3 => 'event-custom-base',
            4 => 'oop',
            5 => 'yui-base',
            6 => 'yui-later',
            7 => 'event-custom-complex',
          ),
          'provides' => 
          array (
            'base-build' => true,
          ),
        ),
        'base-pluginhost' => 
        array (
          'requires' => 
          array (
            0 => 'base-base',
            1 => 'pluginhost',
          ),
          'path' => 'base/base-pluginhost-min.js',
          'name' => 'base-pluginhost',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'base-base',
            1 => 'attribute-base',
            2 => 'event-custom',
            3 => 'event-custom-base',
            4 => 'oop',
            5 => 'yui-base',
            6 => 'yui-later',
            7 => 'event-custom-complex',
            8 => 'pluginhost',
          ),
          'provides' => 
          array (
            'base-pluginhost' => true,
          ),
        ),
      ),
      'name' => 'base',
      'type' => 'js',
      'path' => 'base/base-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'base-base',
        1 => 'base-build',
        2 => 'base-pluginhost',
      ),
      'rollup' => 3,
      'expanded' => 
      array (
        0 => 'base-base',
        1 => 'attribute-base',
        2 => 'event-custom',
        3 => 'event-custom-base',
        4 => 'oop',
        5 => 'yui-base',
        6 => 'yui-later',
        7 => 'event-custom-complex',
        8 => 'base-build',
        9 => 'base-pluginhost',
        10 => 'pluginhost',
      ),
      'provides' => 
      array (
        'base-base' => true,
        'base-build' => true,
        'base-pluginhost' => true,
        'base' => true,
      ),
    ),
    'base-base' => 
    array (
      'requires' => 
      array (
        0 => 'attribute-base',
      ),
      'path' => 'base/base-base-min.js',
      'name' => 'base-base',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'attribute-base',
        1 => 'event-custom',
        2 => 'event-custom-base',
        3 => 'oop',
        4 => 'yui-base',
        5 => 'yui-later',
        6 => 'event-custom-complex',
      ),
      'provides' => 
      array (
        'base-base' => true,
      ),
    ),
    'base-build' => 
    array (
      'requires' => 
      array (
        0 => 'base-base',
      ),
      'path' => 'base/base-build-min.js',
      'name' => 'base-build',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'base-base',
        1 => 'attribute-base',
        2 => 'event-custom',
        3 => 'event-custom-base',
        4 => 'oop',
        5 => 'yui-base',
        6 => 'yui-later',
        7 => 'event-custom-complex',
      ),
      'provides' => 
      array (
        'base-build' => true,
      ),
    ),
    'base-pluginhost' => 
    array (
      'requires' => 
      array (
        0 => 'base-base',
        1 => 'pluginhost',
      ),
      'path' => 'base/base-pluginhost-min.js',
      'name' => 'base-pluginhost',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'base-base',
        1 => 'attribute-base',
        2 => 'event-custom',
        3 => 'event-custom-base',
        4 => 'oop',
        5 => 'yui-base',
        6 => 'yui-later',
        7 => 'event-custom-complex',
        8 => 'pluginhost',
      ),
      'provides' => 
      array (
        'base-pluginhost' => true,
      ),
    ),
    'cache' => 
    array (
      'requires' => 
      array (
        0 => 'plugin',
      ),
      'name' => 'cache',
      'type' => 'js',
      'path' => 'cache/cache-min.js',
      'ext' => false,
    ),
    'compat' => 
    array (
      'requires' => 
      array (
        0 => 'node',
        1 => 'dump',
        2 => 'substitute',
      ),
      'name' => 'compat',
      'type' => 'js',
      'path' => 'compat/compat-min.js',
      'ext' => false,
    ),
    'classnamemanager' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'classnamemanager',
      'type' => 'js',
      'path' => 'classnamemanager/classnamemanager-min.js',
      'ext' => false,
    ),
    'collection' => 
    array (
      'requires' => 
      array (
        0 => 'oop',
      ),
      'name' => 'collection',
      'type' => 'js',
      'path' => 'collection/collection-min.js',
      'ext' => false,
    ),
    'console' => 
    array (
      'requires' => 
      array (
        0 => 'yui-log',
        1 => 'widget',
        2 => 'substitute',
        3 => 'skin-sam-console',
        4 => 'skin-sam-console',
        5 => 'skin-sam-console',
      ),
      'skinnable' => true,
      'plugins' => 
      array (
        'console-filters' => 
        array (
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'console',
            2 => 'skin-sam-console-filters',
            3 => 'skin-sam-console-filters',
            4 => 'skin-sam-console-filters',
          ),
          'skinnable' => true,
          'path' => 'console/console-filters-min.js',
          'name' => 'console-filters',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'console',
      'type' => 'js',
      'path' => 'console/console-min.js',
      'ext' => false,
    ),
    'console-filters' => 
    array (
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'console',
        2 => 'skin-sam-console-filters',
        3 => 'skin-sam-console-filters',
        4 => 'skin-sam-console-filters',
      ),
      'skinnable' => true,
      'path' => 'console/console-filters-min.js',
      'name' => 'console-filters',
      'type' => 'js',
      'ext' => false,
    ),
    'skin-sam-console-filters' => 
    array (
      'name' => 'skin-sam-console-filters',
      'type' => 'css',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'path' => 'console/assets/skins/sam/console-filters.css',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'cookie' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'cookie',
      'type' => 'js',
      'path' => 'cookie/cookie-min.js',
      'ext' => false,
    ),
    'dataschema' => 
    array (
      'submodules' => 
      array (
        'dataschema-base' => 
        array (
          'requires' => 
          array (
            0 => 'base',
          ),
          'path' => 'dataschema/dataschema-base-min.js',
          'name' => 'dataschema-base',
          'type' => 'js',
          'ext' => false,
        ),
        'dataschema-array' => 
        array (
          'requires' => 
          array (
            0 => 'dataschema-base',
          ),
          'path' => 'dataschema/dataschema-array-min.js',
          'name' => 'dataschema-array',
          'type' => 'js',
          'ext' => false,
        ),
        'dataschema-json' => 
        array (
          'requires' => 
          array (
            0 => 'dataschema-base',
            1 => 'json',
          ),
          'path' => 'dataschema/dataschema-json-min.js',
          'name' => 'dataschema-json',
          'type' => 'js',
          'ext' => false,
        ),
        'dataschema-text' => 
        array (
          'requires' => 
          array (
            0 => 'dataschema-base',
          ),
          'path' => 'dataschema/dataschema-text-min.js',
          'name' => 'dataschema-text',
          'type' => 'js',
          'ext' => false,
        ),
        'dataschema-xml' => 
        array (
          'requires' => 
          array (
            0 => 'dataschema-base',
          ),
          'path' => 'dataschema/dataschema-xml-min.js',
          'name' => 'dataschema-xml',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'dataschema',
      'type' => 'js',
      'path' => 'dataschema/dataschema-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'dataschema-base',
        1 => 'dataschema-array',
        2 => 'dataschema-json',
        3 => 'dataschema-text',
        4 => 'dataschema-xml',
      ),
      'rollup' => 4,
    ),
    'dataschema-base' => 
    array (
      'requires' => 
      array (
        0 => 'base',
      ),
      'path' => 'dataschema/dataschema-base-min.js',
      'name' => 'dataschema-base',
      'type' => 'js',
      'ext' => false,
    ),
    'dataschema-array' => 
    array (
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'path' => 'dataschema/dataschema-array-min.js',
      'name' => 'dataschema-array',
      'type' => 'js',
      'ext' => false,
    ),
    'dataschema-json' => 
    array (
      'requires' => 
      array (
        0 => 'dataschema-base',
        1 => 'json',
      ),
      'path' => 'dataschema/dataschema-json-min.js',
      'name' => 'dataschema-json',
      'type' => 'js',
      'ext' => false,
    ),
    'dataschema-text' => 
    array (
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'path' => 'dataschema/dataschema-text-min.js',
      'name' => 'dataschema-text',
      'type' => 'js',
      'ext' => false,
    ),
    'dataschema-xml' => 
    array (
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'path' => 'dataschema/dataschema-xml-min.js',
      'name' => 'dataschema-xml',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource' => 
    array (
      'submodules' => 
      array (
        'datasource-local' => 
        array (
          'requires' => 
          array (
            0 => 'base',
          ),
          'path' => 'datasource/datasource-local-min.js',
          'name' => 'datasource-local',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-arrayschema' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-array',
          ),
          'path' => 'datasource/datasource-arrayschema-min.js',
          'name' => 'datasource-arrayschema',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-cache' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'cache',
          ),
          'path' => 'datasource/datasource-cache-min.js',
          'name' => 'datasource-cache',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-function' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
          ),
          'path' => 'datasource/datasource-function-min.js',
          'name' => 'datasource-function',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-jsonschema' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-json',
          ),
          'path' => 'datasource/datasource-jsonschema-min.js',
          'name' => 'datasource-jsonschema',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-polling' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
          ),
          'path' => 'datasource/datasource-polling-min.js',
          'name' => 'datasource-polling',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-get' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'get',
          ),
          'path' => 'datasource/datasource-get-min.js',
          'name' => 'datasource-get',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-textschema' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-text',
          ),
          'path' => 'datasource/datasource-textschema-min.js',
          'name' => 'datasource-textschema',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-io' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'io-base',
          ),
          'path' => 'datasource/datasource-io-min.js',
          'name' => 'datasource-io',
          'type' => 'js',
          'ext' => false,
        ),
        'datasource-xmlschema' => 
        array (
          'requires' => 
          array (
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-xml',
          ),
          'path' => 'datasource/datasource-xmlschema-min.js',
          'name' => 'datasource-xmlschema',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'datasource',
      'type' => 'js',
      'path' => 'datasource/datasource-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'datasource-local',
        1 => 'datasource-arrayschema',
        2 => 'datasource-cache',
        3 => 'datasource-function',
        4 => 'datasource-jsonschema',
        5 => 'datasource-polling',
        6 => 'datasource-get',
        7 => 'datasource-textschema',
        8 => 'datasource-io',
        9 => 'datasource-xmlschema',
      ),
      'rollup' => 4,
    ),
    'datasource-local' => 
    array (
      'requires' => 
      array (
        0 => 'base',
      ),
      'path' => 'datasource/datasource-local-min.js',
      'name' => 'datasource-local',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-arrayschema' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-array',
      ),
      'path' => 'datasource/datasource-arrayschema-min.js',
      'name' => 'datasource-arrayschema',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-cache' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'cache',
      ),
      'path' => 'datasource/datasource-cache-min.js',
      'name' => 'datasource-cache',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-function' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
      ),
      'path' => 'datasource/datasource-function-min.js',
      'name' => 'datasource-function',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-jsonschema' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-json',
      ),
      'path' => 'datasource/datasource-jsonschema-min.js',
      'name' => 'datasource-jsonschema',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-polling' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
      ),
      'path' => 'datasource/datasource-polling-min.js',
      'name' => 'datasource-polling',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-get' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'get',
      ),
      'path' => 'datasource/datasource-get-min.js',
      'name' => 'datasource-get',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-textschema' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-text',
      ),
      'path' => 'datasource/datasource-textschema-min.js',
      'name' => 'datasource-textschema',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-io' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'io-base',
      ),
      'path' => 'datasource/datasource-io-min.js',
      'name' => 'datasource-io',
      'type' => 'js',
      'ext' => false,
    ),
    'datasource-xmlschema' => 
    array (
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-xml',
      ),
      'path' => 'datasource/datasource-xmlschema-min.js',
      'name' => 'datasource-xmlschema',
      'type' => 'js',
      'ext' => false,
    ),
    'datatype' => 
    array (
      'submodules' => 
      array (
        'datatype-date' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'datatype/datatype-date-min.js',
          'name' => 'datatype-date',
          'type' => 'js',
          'ext' => false,
        ),
        'datatype-number' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'datatype/datatype-number-min.js',
          'name' => 'datatype-number',
          'type' => 'js',
          'ext' => false,
        ),
        'datatype-xml' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'datatype/datatype-xml-min.js',
          'name' => 'datatype-xml',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'datatype',
      'type' => 'js',
      'path' => 'datatype/datatype-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'datatype-date',
        1 => 'datatype-number',
        2 => 'datatype-xml',
      ),
      'rollup' => 3,
    ),
    'datatype-date' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'datatype/datatype-date-min.js',
      'name' => 'datatype-date',
      'type' => 'js',
      'ext' => false,
    ),
    'datatype-number' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'datatype/datatype-number-min.js',
      'name' => 'datatype-number',
      'type' => 'js',
      'ext' => false,
    ),
    'datatype-xml' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'datatype/datatype-xml-min.js',
      'name' => 'datatype-xml',
      'type' => 'js',
      'ext' => false,
    ),
    'dd' => 
    array (
      'submodules' => 
      array (
        'dd-ddm-base' => 
        array (
          'requires' => 
          array (
            0 => 'node',
            1 => 'base',
          ),
          'path' => 'dd/dd-ddm-base-min.js',
          'name' => 'dd-ddm-base',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-ddm' => 
        array (
          'requires' => 
          array (
            0 => 'dd-ddm-base',
            1 => 'event-resize',
          ),
          'path' => 'dd/dd-ddm-min.js',
          'name' => 'dd-ddm',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-ddm-drop' => 
        array (
          'requires' => 
          array (
            0 => 'dd-ddm',
          ),
          'path' => 'dd/dd-ddm-drop-min.js',
          'name' => 'dd-ddm-drop',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-drag' => 
        array (
          'requires' => 
          array (
            0 => 'dd-ddm-base',
          ),
          'path' => 'dd/dd-drag-min.js',
          'name' => 'dd-drag',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-drop' => 
        array (
          'requires' => 
          array (
            0 => 'dd-ddm-drop',
          ),
          'path' => 'dd/dd-drop-min.js',
          'name' => 'dd-drop',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-proxy' => 
        array (
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'path' => 'dd/dd-proxy-min.js',
          'name' => 'dd-proxy',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-constrain' => 
        array (
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'path' => 'dd/dd-constrain-min.js',
          'name' => 'dd-constrain',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-scroll' => 
        array (
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'path' => 'dd/dd-scroll-min.js',
          'name' => 'dd-scroll',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-plugin' => 
        array (
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'optional' => 
          array (
            0 => 'dd-constrain',
            1 => 'dd-proxy',
          ),
          'path' => 'dd/dd-plugin-min.js',
          'name' => 'dd-plugin',
          'type' => 'js',
          'ext' => false,
        ),
        'dd-drop-plugin' => 
        array (
          'requires' => 
          array (
            0 => 'dd-drop',
          ),
          'path' => 'dd/dd-drop-plugin-min.js',
          'name' => 'dd-drop-plugin',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'dd',
      'type' => 'js',
      'path' => 'dd/dd-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'dd-ddm-base',
        1 => 'dd-ddm',
        2 => 'dd-ddm-drop',
        3 => 'dd-drag',
        4 => 'dd-drop',
        5 => 'dd-proxy',
        6 => 'dd-constrain',
        7 => 'dd-scroll',
        8 => 'dd-plugin',
        9 => 'dd-drop-plugin',
      ),
      'rollup' => 4,
    ),
    'dd-ddm-base' => 
    array (
      'requires' => 
      array (
        0 => 'node',
        1 => 'base',
      ),
      'path' => 'dd/dd-ddm-base-min.js',
      'name' => 'dd-ddm-base',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-ddm' => 
    array (
      'requires' => 
      array (
        0 => 'dd-ddm-base',
        1 => 'event-resize',
      ),
      'path' => 'dd/dd-ddm-min.js',
      'name' => 'dd-ddm',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-ddm-drop' => 
    array (
      'requires' => 
      array (
        0 => 'dd-ddm',
      ),
      'path' => 'dd/dd-ddm-drop-min.js',
      'name' => 'dd-ddm-drop',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-drag' => 
    array (
      'requires' => 
      array (
        0 => 'dd-ddm-base',
      ),
      'path' => 'dd/dd-drag-min.js',
      'name' => 'dd-drag',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-drop' => 
    array (
      'requires' => 
      array (
        0 => 'dd-ddm-drop',
      ),
      'path' => 'dd/dd-drop-min.js',
      'name' => 'dd-drop',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-proxy' => 
    array (
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'path' => 'dd/dd-proxy-min.js',
      'name' => 'dd-proxy',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-constrain' => 
    array (
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'path' => 'dd/dd-constrain-min.js',
      'name' => 'dd-constrain',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-scroll' => 
    array (
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'path' => 'dd/dd-scroll-min.js',
      'name' => 'dd-scroll',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-plugin' => 
    array (
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'optional' => 
      array (
        0 => 'dd-constrain',
        1 => 'dd-proxy',
      ),
      'path' => 'dd/dd-plugin-min.js',
      'name' => 'dd-plugin',
      'type' => 'js',
      'ext' => false,
    ),
    'dd-drop-plugin' => 
    array (
      'requires' => 
      array (
        0 => 'dd-drop',
      ),
      'path' => 'dd/dd-drop-plugin-min.js',
      'name' => 'dd-drop-plugin',
      'type' => 'js',
      'ext' => false,
    ),
    'dump' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'dump',
      'type' => 'js',
      'path' => 'dump/dump-min.js',
      'ext' => false,
    ),
    'event' => 
    array (
      'expound' => 'node-base',
      'submodules' => 
      array (
        'event-base' => 
        array (
          'expound' => 'node-base',
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'path' => 'event/event-base-min.js',
          'name' => 'event-base',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'event-custom-base',
            1 => 'oop',
            2 => 'yui-base',
            3 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-base' => true,
          ),
        ),
        'event-delegate' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'path' => 'event/event-delegate-min.js',
          'name' => 'event-delegate',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-delegate' => true,
          ),
        ),
        'event-focus' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'path' => 'event/event-focus-min.js',
          'name' => 'event-focus',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-focus' => true,
          ),
        ),
        'event-key' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'path' => 'event/event-key-min.js',
          'name' => 'event-key',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-key' => true,
          ),
        ),
        'event-mouseenter' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'path' => 'event/event-mouseenter-min.js',
          'name' => 'event-mouseenter',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-mouseenter' => true,
          ),
        ),
        'event-mousewheel' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'path' => 'event/event-mousewheel-min.js',
          'name' => 'event-mousewheel',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-mousewheel' => true,
          ),
        ),
        'event-resize' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'path' => 'event/event-resize-min.js',
          'name' => 'event-resize',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'node-base',
            1 => 'dom-base',
            2 => 'oop',
            3 => 'yui-base',
            4 => 'selector-css2',
            5 => 'selector-native',
            6 => 'event-base',
            7 => 'event-custom-base',
            8 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-resize' => true,
          ),
        ),
      ),
      'name' => 'event',
      'type' => 'js',
      'path' => 'event/event-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'event-base',
        1 => 'event-delegate',
        2 => 'event-focus',
        3 => 'event-key',
        4 => 'event-mouseenter',
        5 => 'event-mousewheel',
        6 => 'event-resize',
      ),
      'rollup' => 4,
      'expanded' => 
      array (
        0 => 'event-base',
        1 => 'event-custom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'yui-later',
        5 => 'event-delegate',
        6 => 'node-base',
        7 => 'dom-base',
        8 => 'selector-css2',
        9 => 'selector-native',
        10 => 'event-focus',
        11 => 'event-key',
        12 => 'event-mouseenter',
        13 => 'event-mousewheel',
        14 => 'event-resize',
      ),
      'provides' => 
      array (
        'event-base' => true,
        'event-delegate' => true,
        'event-focus' => true,
        'event-key' => true,
        'event-mouseenter' => true,
        'event-mousewheel' => true,
        'event-resize' => true,
        'event' => true,
      ),
    ),
    'event-base' => 
    array (
      'expound' => 'node-base',
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'path' => 'event/event-base-min.js',
      'name' => 'event-base',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'event-custom-base',
        1 => 'oop',
        2 => 'yui-base',
        3 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-base' => true,
      ),
    ),
    'event-delegate' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'path' => 'event/event-delegate-min.js',
      'name' => 'event-delegate',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-delegate' => true,
      ),
    ),
    'event-focus' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'path' => 'event/event-focus-min.js',
      'name' => 'event-focus',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-focus' => true,
      ),
    ),
    'event-key' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'path' => 'event/event-key-min.js',
      'name' => 'event-key',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-key' => true,
      ),
    ),
    'event-mouseenter' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'path' => 'event/event-mouseenter-min.js',
      'name' => 'event-mouseenter',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-mouseenter' => true,
      ),
    ),
    'event-mousewheel' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'path' => 'event/event-mousewheel-min.js',
      'name' => 'event-mousewheel',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-mousewheel' => true,
      ),
    ),
    'event-resize' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'path' => 'event/event-resize-min.js',
      'name' => 'event-resize',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'node-base',
        1 => 'dom-base',
        2 => 'oop',
        3 => 'yui-base',
        4 => 'selector-css2',
        5 => 'selector-native',
        6 => 'event-base',
        7 => 'event-custom-base',
        8 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-resize' => true,
      ),
    ),
    'event-custom' => 
    array (
      'submodules' => 
      array (
        'event-custom-base' => 
        array (
          'requires' => 
          array (
            0 => 'oop',
            1 => 'yui-later',
          ),
          'path' => 'event-custom/event-custom-base-min.js',
          'name' => 'event-custom-base',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'oop',
            1 => 'yui-base',
            2 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-custom-base' => true,
          ),
        ),
        'event-custom-complex' => 
        array (
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'path' => 'event-custom/event-custom-complex-min.js',
          'name' => 'event-custom-complex',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'event-custom-base',
            1 => 'oop',
            2 => 'yui-base',
            3 => 'yui-later',
          ),
          'provides' => 
          array (
            'event-custom-complex' => true,
          ),
        ),
      ),
      'name' => 'event-custom',
      'type' => 'js',
      'path' => 'event-custom/event-custom-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'event-custom-base',
        1 => 'event-custom-complex',
      ),
      'rollup' => 2,
      'expanded' => 
      array (
        0 => 'event-custom-base',
        1 => 'oop',
        2 => 'yui-base',
        3 => 'yui-later',
        4 => 'event-custom-complex',
      ),
      'provides' => 
      array (
        'event-custom-base' => true,
        'event-custom-complex' => true,
        'event-custom' => true,
      ),
    ),
    'event-custom-base' => 
    array (
      'requires' => 
      array (
        0 => 'oop',
        1 => 'yui-later',
      ),
      'path' => 'event-custom/event-custom-base-min.js',
      'name' => 'event-custom-base',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'oop',
        1 => 'yui-base',
        2 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-custom-base' => true,
      ),
    ),
    'event-custom-complex' => 
    array (
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'path' => 'event-custom/event-custom-complex-min.js',
      'name' => 'event-custom-complex',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'event-custom-base',
        1 => 'oop',
        2 => 'yui-base',
        3 => 'yui-later',
      ),
      'provides' => 
      array (
        'event-custom-complex' => true,
      ),
    ),
    'event-simulate' => 
    array (
      'requires' => 
      array (
        0 => 'event-base',
      ),
      'name' => 'event-simulate',
      'type' => 'js',
      'path' => 'event-simulate/event-simulate-min.js',
      'ext' => false,
    ),
    'node-focusmanager' => 
    array (
      'requires' => 
      array (
        0 => 'attribute',
        1 => 'node',
        2 => 'plugin',
        3 => 'node-event-simulate',
        4 => 'event-key',
        5 => 'event-focus',
      ),
      'name' => 'node-focusmanager',
      'type' => 'js',
      'path' => 'node-focusmanager/node-focusmanager-min.js',
      'ext' => false,
    ),
    'history' => 
    array (
      'requires' => 
      array (
        0 => 'node',
      ),
      'name' => 'history',
      'type' => 'js',
      'path' => 'history/history-min.js',
      'ext' => false,
    ),
    'imageloader' => 
    array (
      'requires' => 
      array (
        0 => 'base-base',
        1 => 'node-style',
        2 => 'node-screen',
      ),
      'name' => 'imageloader',
      'type' => 'js',
      'path' => 'imageloader/imageloader-min.js',
      'ext' => false,
    ),
    'io' => 
    array (
      'submodules' => 
      array (
        'io-base' => 
        array (
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'path' => 'io/io-base-min.js',
          'name' => 'io-base',
          'type' => 'js',
          'ext' => false,
        ),
        'io-xdr' => 
        array (
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'datatype-xml',
          ),
          'path' => 'io/io-xdr-min.js',
          'name' => 'io-xdr',
          'type' => 'js',
          'ext' => false,
        ),
        'io-form' => 
        array (
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'node-base',
            2 => 'node-style',
          ),
          'path' => 'io/io-form-min.js',
          'name' => 'io-form',
          'type' => 'js',
          'ext' => false,
        ),
        'io-upload-iframe' => 
        array (
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'node-base',
          ),
          'path' => 'io/io-upload-iframe-min.js',
          'name' => 'io-upload-iframe',
          'type' => 'js',
          'ext' => false,
        ),
        'io-queue' => 
        array (
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'queue-promote',
          ),
          'path' => 'io/io-queue-min.js',
          'name' => 'io-queue',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'name' => 'io',
      'type' => 'js',
      'path' => 'io/io-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'io-base',
        1 => 'io-xdr',
        2 => 'io-form',
        3 => 'io-upload-iframe',
        4 => 'io-queue',
      ),
      'rollup' => 4,
    ),
    'io-base' => 
    array (
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'path' => 'io/io-base-min.js',
      'name' => 'io-base',
      'type' => 'js',
      'ext' => false,
    ),
    'io-xdr' => 
    array (
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'datatype-xml',
      ),
      'path' => 'io/io-xdr-min.js',
      'name' => 'io-xdr',
      'type' => 'js',
      'ext' => false,
    ),
    'io-form' => 
    array (
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'node-base',
        2 => 'node-style',
      ),
      'path' => 'io/io-form-min.js',
      'name' => 'io-form',
      'type' => 'js',
      'ext' => false,
    ),
    'io-upload-iframe' => 
    array (
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'node-base',
      ),
      'path' => 'io/io-upload-iframe-min.js',
      'name' => 'io-upload-iframe',
      'type' => 'js',
      'ext' => false,
    ),
    'io-queue' => 
    array (
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'queue-promote',
      ),
      'path' => 'io/io-queue-min.js',
      'name' => 'io-queue',
      'type' => 'js',
      'ext' => false,
    ),
    'json' => 
    array (
      'submodules' => 
      array (
        'json-parse' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'json/json-parse-min.js',
          'name' => 'json-parse',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'yui-base',
          ),
          'provides' => 
          array (
            'json-parse' => true,
          ),
        ),
        'json-stringify' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'json/json-stringify-min.js',
          'name' => 'json-stringify',
          'type' => 'js',
          'ext' => false,
          'expanded' => 
          array (
            0 => 'yui-base',
          ),
          'provides' => 
          array (
            'json-stringify' => true,
          ),
        ),
      ),
      'name' => 'json',
      'type' => 'js',
      'path' => 'json/json-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'json-parse',
        1 => 'json-stringify',
      ),
      'rollup' => 2,
      'expanded' => 
      array (
        0 => 'json-parse',
        1 => 'yui-base',
        2 => 'json-stringify',
      ),
      'provides' => 
      array (
        'json-parse' => true,
        'json-stringify' => true,
        'json' => true,
      ),
    ),
    'json-parse' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'json/json-parse-min.js',
      'name' => 'json-parse',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
      'provides' => 
      array (
        'json-parse' => true,
      ),
    ),
    'json-stringify' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'json/json-stringify-min.js',
      'name' => 'json-stringify',
      'type' => 'js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
      'provides' => 
      array (
        'json-stringify' => true,
      ),
    ),
    'loader' => 
    array (
      'requires' => 
      array (
        0 => 'get',
      ),
      'name' => 'loader',
      'type' => 'js',
      'path' => 'loader/loader-min.js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'get',
      ),
      'provides' => 
      array (
        'loader' => true,
      ),
    ),
    'node-menunav' => 
    array (
      'requires' => 
      array (
        0 => 'node',
        1 => 'classnamemanager',
        2 => 'plugin',
        3 => 'node-focusmanager',
        4 => 'skin-sam-node-menunav',
        5 => 'skin-sam-node-menunav',
        6 => 'skin-sam-node-menunav',
      ),
      'skinnable' => true,
      'name' => 'node-menunav',
      'type' => 'js',
      'path' => 'node-menunav/node-menunav-min.js',
      'ext' => false,
    ),
    'oop' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'oop',
      'type' => 'js',
      'path' => 'oop/oop-min.js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
      'provides' => 
      array (
        'oop' => true,
      ),
    ),
    'overlay' => 
    array (
      'requires' => 
      array (
        0 => 'widget',
        1 => 'widget-position',
        2 => 'widget-position-ext',
        3 => 'widget-stack',
        4 => 'widget-stdmod',
        5 => 'skin-sam-overlay',
        6 => 'skin-sam-overlay',
        7 => 'skin-sam-overlay',
      ),
      'skinnable' => true,
      'name' => 'overlay',
      'type' => 'js',
      'path' => 'overlay/overlay-min.js',
      'ext' => false,
    ),
    'plugin' => 
    array (
      'requires' => 
      array (
        0 => 'base-base',
      ),
      'name' => 'plugin',
      'type' => 'js',
      'path' => 'plugin/plugin-min.js',
      'ext' => false,
    ),
    'pluginhost' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'pluginhost',
      'type' => 'js',
      'path' => 'pluginhost/pluginhost-min.js',
      'ext' => false,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
      'provides' => 
      array (
        'pluginhost' => true,
      ),
    ),
    'profiler' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'profiler',
      'type' => 'js',
      'path' => 'profiler/profiler-min.js',
      'ext' => false,
    ),
    'queue-promote' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'queue-promote',
      'type' => 'js',
      'path' => 'queue-promote/queue-promote-min.js',
      'ext' => false,
    ),
    'queue-run' => 
    array (
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'path' => 'async-queue/async-queue-min.js',
      'name' => 'queue-run',
      'type' => 'js',
      'ext' => false,
    ),
    'async-queue' => 
    array (
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'supersedes' => 
      array (
        0 => 'queue-run',
      ),
      'name' => 'async-queue',
      'type' => 'js',
      'path' => 'async-queue/async-queue-min.js',
      'ext' => false,
    ),
    'slider' => 
    array (
      'requires' => 
      array (
        0 => 'widget',
        1 => 'dd-constrain',
        2 => 'skin-sam-slider',
        3 => 'skin-sam-slider',
        4 => 'skin-sam-slider',
      ),
      'skinnable' => true,
      'name' => 'slider',
      'type' => 'js',
      'path' => 'slider/slider-min.js',
      'ext' => false,
    ),
    'stylesheet' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'name' => 'stylesheet',
      'type' => 'js',
      'path' => 'stylesheet/stylesheet-min.js',
      'ext' => false,
    ),
    'substitute' => 
    array (
      'optional' => 
      array (
        0 => 'dump',
      ),
      'name' => 'substitute',
      'type' => 'js',
      'path' => 'substitute/substitute-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'widget' => 
    array (
      'requires' => 
      array (
        0 => 'attribute',
        1 => 'event-focus',
        2 => 'base',
        3 => 'node',
        4 => 'classnamemanager',
        5 => 'skin-sam-widget',
        6 => 'skin-sam-widget',
        7 => 'skin-sam-widget',
      ),
      'plugins' => 
      array (
        'widget-position' => 
        array (
          'path' => 'widget/widget-position-min.js',
          'requires' => 
          array (
          ),
          'name' => 'widget-position',
          'type' => 'js',
          'ext' => false,
        ),
        'widget-position-ext' => 
        array (
          'requires' => 
          array (
            0 => 'widget-position',
          ),
          'path' => 'widget/widget-position-ext-min.js',
          'name' => 'widget-position-ext',
          'type' => 'js',
          'ext' => false,
        ),
        'widget-stack' => 
        array (
          'skinnable' => true,
          'path' => 'widget/widget-stack-min.js',
          'requires' => 
          array (
            0 => 'skin-sam-widget-stack',
            1 => 'skin-sam-widget-stack',
            2 => 'skin-sam-widget-stack',
          ),
          'name' => 'widget-stack',
          'type' => 'js',
          'ext' => false,
        ),
        'widget-stdmod' => 
        array (
          'path' => 'widget/widget-stdmod-min.js',
          'requires' => 
          array (
          ),
          'name' => 'widget-stdmod',
          'type' => 'js',
          'ext' => false,
        ),
      ),
      'skinnable' => true,
      'name' => 'widget',
      'type' => 'js',
      'path' => 'widget/widget-min.js',
      'ext' => false,
    ),
    'widget-position' => 
    array (
      'path' => 'widget/widget-position-min.js',
      'requires' => 
      array (
      ),
      'name' => 'widget-position',
      'type' => 'js',
      'ext' => false,
    ),
    'skin-sam-widget-position' => 
    array (
      'name' => 'skin-sam-widget-position',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'widget-position-ext' => 
    array (
      'requires' => 
      array (
        0 => 'widget-position',
      ),
      'path' => 'widget/widget-position-ext-min.js',
      'name' => 'widget-position-ext',
      'type' => 'js',
      'ext' => false,
    ),
    'skin-sam-widget-position-ext' => 
    array (
      'name' => 'skin-sam-widget-position-ext',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'widget-stack' => 
    array (
      'skinnable' => true,
      'path' => 'widget/widget-stack-min.js',
      'requires' => 
      array (
        0 => 'skin-sam-widget-stack',
        1 => 'skin-sam-widget-stack',
        2 => 'skin-sam-widget-stack',
      ),
      'name' => 'widget-stack',
      'type' => 'js',
      'ext' => false,
    ),
    'skin-sam-widget-stack' => 
    array (
      'name' => 'skin-sam-widget-stack',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'widget-stdmod' => 
    array (
      'path' => 'widget/widget-stdmod-min.js',
      'requires' => 
      array (
      ),
      'name' => 'widget-stdmod',
      'type' => 'js',
      'ext' => false,
    ),
    'skin-sam-widget-stdmod' => 
    array (
      'name' => 'skin-sam-widget-stdmod',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'yui' => 
    array (
      'submodules' => 
      array (
        'yui-base' => 
        array (
          'path' => 'yui/yui-base-min.js',
          'name' => 'yui-base',
          'type' => 'js',
          'ext' => false,
          'requires' => 
          array (
          ),
          'expanded' => 
          array (
          ),
          'provides' => 
          array (
            'yui-base' => true,
          ),
        ),
        'get' => 
        array (
          'path' => 'yui/get-min.js',
          'name' => 'get',
          'type' => 'js',
          'ext' => false,
          'requires' => 
          array (
          ),
          'expanded' => 
          array (
          ),
          'provides' => 
          array (
            'get' => true,
          ),
        ),
        'yui-log' => 
        array (
          'path' => 'yui/yui-log-min.js',
          'name' => 'yui-log',
          'type' => 'js',
          'ext' => false,
          'requires' => 
          array (
          ),
          'provides' => 
          array (
            'yui-log' => true,
          ),
        ),
        'yui-later' => 
        array (
          'path' => 'yui/yui-later-min.js',
          'name' => 'yui-later',
          'type' => 'js',
          'ext' => false,
          'requires' => 
          array (
          ),
          'expanded' => 
          array (
          ),
          'provides' => 
          array (
            'yui-later' => true,
          ),
        ),
      ),
      'name' => 'yui',
      'type' => 'js',
      'path' => 'yui/yui-min.js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'yui-base',
        1 => 'get',
        2 => 'yui-log',
        3 => 'yui-later',
      ),
      'rollup' => 3,
      'provides' => 
      array (
        'yui-base' => true,
        'get' => true,
        'yui-log' => true,
        'yui-later' => true,
        'yui' => true,
      ),
    ),
    'yui-base' => 
    array (
      'path' => 'yui/yui-base-min.js',
      'name' => 'yui-base',
      'type' => 'js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'expanded' => 
      array (
      ),
      'provides' => 
      array (
        'yui-base' => true,
      ),
    ),
    'get' => 
    array (
      'path' => 'yui/get-min.js',
      'name' => 'get',
      'type' => 'js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'expanded' => 
      array (
      ),
      'provides' => 
      array (
        'get' => true,
      ),
    ),
    'yui-log' => 
    array (
      'path' => 'yui/yui-log-min.js',
      'name' => 'yui-log',
      'type' => 'js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'provides' => 
      array (
        'yui-log' => true,
      ),
    ),
    'yui-later' => 
    array (
      'path' => 'yui/yui-later-min.js',
      'name' => 'yui-later',
      'type' => 'js',
      'ext' => false,
      'requires' => 
      array (
      ),
      'expanded' => 
      array (
      ),
      'provides' => 
      array (
        'yui-later' => true,
      ),
    ),
    'test' => 
    array (
      'requires' => 
      array (
        0 => 'substitute',
        1 => 'node',
        2 => 'json',
        3 => 'event-simulate',
      ),
      'name' => 'test',
      'type' => 'js',
      'path' => 'test/test-min.js',
      'ext' => false,
    ),
    'cssreset' => 
    array (
      'type' => 'css',
      'path' => 'cssreset/reset-min.css',
      'name' => 'cssreset',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'cssreset-context' => 
    array (
      'type' => 'css',
      'path' => 'cssreset/reset-context-min.css',
      'name' => 'cssreset-context',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'cssfonts' => 
    array (
      'type' => 'css',
      'path' => 'cssfonts/fonts-min.css',
      'name' => 'cssfonts',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'cssfonts-context' => 
    array (
      'type' => 'css',
      'path' => 'cssfonts/fonts-context-min.css',
      'name' => 'cssfonts-context',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'cssgrids' => 
    array (
      'type' => 'css',
      'path' => 'cssgrids/grids-min.css',
      'requires' => 
      array (
        0 => 'cssfonts',
      ),
      'optional' => 
      array (
        0 => 'cssreset',
      ),
      'name' => 'cssgrids',
      'ext' => false,
    ),
    'cssgrids-context' => 
    array (
      'type' => 'css',
      'path' => 'cssgrids/grids-context-min.css',
      'requires' => 
      array (
        0 => 'cssfonts-context',
      ),
      'optional' => 
      array (
        0 => 'cssreset-context',
      ),
      'name' => 'cssgrids-context',
      'ext' => false,
    ),
    'cssbase' => 
    array (
      'type' => 'css',
      'path' => 'cssbase/base-min.css',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'name' => 'cssbase',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'cssbase-context' => 
    array (
      'type' => 'css',
      'path' => 'cssbase/base-context-min.css',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssreset-context',
        4 => 'cssfonts-context',
        5 => 'cssgrids-context',
      ),
      'name' => 'cssbase-context',
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'skin-sam-console' => 
    array (
      'name' => 'skin-sam-console',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'skin-sam-node-menunav' => 
    array (
      'name' => 'skin-sam-node-menunav',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'skin-sam-overlay' => 
    array (
      'name' => 'skin-sam-overlay',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'skin-sam-slider' => 
    array (
      'name' => 'skin-sam-slider',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
    'skin-sam-widget' => 
    array (
      'name' => 'skin-sam-widget',
      'type' => 'css',
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
      'ext' => false,
      'requires' => 
      array (
      ),
    ),
  ),
); ?>