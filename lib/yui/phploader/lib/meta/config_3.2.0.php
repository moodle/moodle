<?php $GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/3.2.0/build/',
  'skin' => 
  array (
    'after' => 
    array (
      0 => 'cssreset',
      1 => 'cssfonts',
      2 => 'cssgrids',
      3 => 'cssbase',
      4 => 'cssreset-context',
      5 => 'cssfonts-context',
    ),
    'path' => 'skin.css',
    'base' => 'assets/skins/',
    'defaultSkin' => 'sam',
  ),
  'moduleInfo' => 
  array (
    'arraylist-add' => 
    array (
      'path' => 'collection/arraylist-add-min.js',
      'requires' => 
      array (
        0 => 'arraylist',
      ),
      'type' => 'js',
      'pkg' => 'collection',
      'name' => 'arraylist-add',
      'ext' => false,
    ),
    'lang/datatype_it-IT' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_it-IT.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_it-IT',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_it-IT',
    ),
    'lang/datatype-date_ja-JP' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ja-JP.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ja-JP',
    ),
    'datasource-local' => 
    array (
      'path' => 'datasource/datasource-local-min.js',
      'requires' => 
      array (
        0 => 'base',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-local',
      'ext' => false,
    ),
    'skin-sam-console-filters' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'console/assets/skins/sam/console-filters.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-console-filters',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_ko-KR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ko-KR.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ko-KR',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ko-KR',
    ),
    'querystring' => 
    array (
      'supersedes' => 
      array (
        0 => 'querystring-parse',
        1 => 'querystring-stringify',
      ),
      'path' => 'querystring/querystring-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'querystring-parse' => 
        array (
          'path' => 'querystring/querystring-parse-min.js',
          'requires' => 
          array (
            0 => 'array-extras',
            1 => 'yui-base',
          ),
          'type' => 'js',
          'pkg' => 'querystring',
          'name' => 'querystring-parse',
          'ext' => false,
        ),
        'querystring-stringify' => 
        array (
          'path' => 'querystring/querystring-stringify-min.js',
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'pkg' => 'querystring',
          'name' => 'querystring-stringify',
          'ext' => false,
        ),
      ),
      'name' => 'querystring',
      'requires' => 
      array (
      ),
    ),
    'datasource-textschema' => 
    array (
      'path' => 'datasource/datasource-textschema-min.js',
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'datasource-local',
        2 => 'dataschema-text',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-textschema',
      'ext' => false,
    ),
    'datasource-jsonschema' => 
    array (
      'path' => 'datasource/datasource-jsonschema-min.js',
      'requires' => 
      array (
        0 => 'dataschema-json',
        1 => 'plugin',
        2 => 'datasource-local',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-jsonschema',
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
    'datasource-polling' => 
    array (
      'path' => 'datasource/datasource-polling-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-polling',
      'ext' => false,
    ),
    'lang/datatype-date_es-PE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-PE.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-PE',
    ),
    'lang/datatype-date_ca' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ca.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ca',
    ),
    'lang/datatype_pl-PL' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_pl-PL.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_pl-PL',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_pl-PL',
    ),
    'skin-sam-slider-base' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'slider/assets/skins/sam/slider-base.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-slider-base',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'swf' => 
    array (
      'path' => 'swf/swf-min.js',
      'requires' => 
      array (
        0 => 'swfdetect',
        1 => 'event-custom',
        2 => 'node',
      ),
      'type' => 'js',
      'name' => 'swf',
      'ext' => false,
    ),
    'anim-node-plugin' => 
    array (
      'path' => 'anim/anim-node-plugin-min.js',
      'requires' => 
      array (
        0 => 'node-pluginhost',
        1 => 'anim-base',
      ),
      'type' => 'js',
      'pkg' => 'anim',
      'name' => 'anim-node-plugin',
      'ext' => false,
    ),
    'cache-offline' => 
    array (
      'path' => 'cache/cache-offline-min.js',
      'requires' => 
      array (
        0 => 'cache-base',
        1 => 'json',
      ),
      'type' => 'js',
      'pkg' => 'cache',
      'name' => 'cache-offline',
      'ext' => false,
    ),
    'console-filters' => 
    array (
      'path' => 'console/console-filters-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'console',
      ),
      'type' => 'js',
      'pkg' => 'console',
      'name' => 'console-filters',
      'ext' => false,
    ),
    'lang/datatype-date_en-US' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-US.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-US',
    ),
    'lang/datatype_ar' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ar.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ar',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ar',
    ),
    'lang/datatype-date_vi-VN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_vi-VN.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_vi-VN',
    ),
    'anim-color' => 
    array (
      'path' => 'anim/anim-color-min.js',
      'requires' => 
      array (
        0 => 'anim-base',
      ),
      'type' => 'js',
      'pkg' => 'anim',
      'name' => 'anim-color',
      'ext' => false,
    ),
    'lang/datatype-date_es-PY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-PY.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-PY',
    ),
    'lang/datatype_es-MX' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-MX.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-MX',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-MX',
    ),
    'event-custom-base' => 
    array (
      'path' => 'event-custom/event-custom-base-min.js',
      'requires' => 
      array (
        0 => 'yui-later',
        1 => 'oop',
      ),
      'type' => 'js',
      'pkg' => 'event-custom',
      'name' => 'event-custom-base',
      'ext' => false,
    ),
    'lang/datatype-date_zh-Hant' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_zh-Hant.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_zh-Hant',
    ),
    'lang/datatype-date_zh-Hans' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_zh-Hans.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_zh-Hans',
    ),
    'event-move' => 
    array (
      'path' => 'event-gestures/event-move-min.js',
      'requires' => 
      array (
        0 => 'event-touch',
        1 => 'event-synthetic',
        2 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event-gestures',
      'name' => 'event-move',
      'ext' => false,
    ),
    'dd-ddm-base' => 
    array (
      'path' => 'dd/dd-ddm-base-min.js',
      'requires' => 
      array (
        0 => 'base',
        1 => 'node',
        2 => 'yui-throttle',
        3 => 'classnamemanager',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-ddm-base',
      'ext' => false,
    ),
    'lang/datatype_en-SG' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-SG.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-SG',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-SG',
    ),
    'cssfonts' => 
    array (
      'path' => 'cssfonts/fonts-min.css',
      'ext' => false,
      'type' => 'css',
      'name' => 'cssfonts',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_da' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_da.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_da',
    ),
    'event-gestures' => 
    array (
      'supersedes' => 
      array (
        0 => 'event-move',
        1 => 'event-flick',
      ),
      'path' => 'event-gestures/event-gestures-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'event-move' => 
        array (
          'path' => 'event-gestures/event-move-min.js',
          'requires' => 
          array (
            0 => 'event-touch',
            1 => 'event-synthetic',
            2 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'event-gestures',
          'name' => 'event-move',
          'ext' => false,
        ),
        'event-flick' => 
        array (
          'path' => 'event-gestures/event-flick-min.js',
          'requires' => 
          array (
            0 => 'event-touch',
            1 => 'event-synthetic',
            2 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'event-gestures',
          'name' => 'event-flick',
          'ext' => false,
        ),
      ),
      'name' => 'event-gestures',
      'requires' => 
      array (
      ),
    ),
    'attribute-base' => 
    array (
      'path' => 'attribute/attribute-base-min.js',
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'type' => 'js',
      'pkg' => 'attribute',
      'name' => 'attribute-base',
      'ext' => false,
    ),
    'lang/datatype-date_de' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_de.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_de',
    ),
    'selection' => 
    array (
      'path' => 'editor/selection-min.js',
      'requires' => 
      array (
        0 => 'node',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'selection',
      'ext' => false,
    ),
    'lang/datatype-date_pt-BR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_pt-BR.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_pt-BR',
    ),
    'lang/datatype_zh-Hant-TW' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_zh-Hant-TW.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_zh-Hant-TW',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_zh-Hant-TW',
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
      'pkg' => 'io',
      'name' => 'io-queue',
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
      'pkg' => 'dataschema',
      'name' => 'dataschema-array',
      'ext' => false,
    ),
    'console' => 
    array (
      'lang' => 
      array (
        0 => 'en',
        1 => 'es',
      ),
      'path' => 'console/console-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'yui-log',
        2 => 'substitute',
      ),
      'type' => 'js',
      'name' => 'console',
      'ext' => false,
      'plugins' => 
      array (
        'console-filters' => 
        array (
          'path' => 'console/console-filters-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'console',
          ),
          'type' => 'js',
          'pkg' => 'console',
          'name' => 'console-filters',
          'ext' => false,
        ),
      ),
    ),
    'transition' => 
    array (
      'supersedes' => 
      array (
        0 => 'transition-native',
        1 => 'transition-timer',
      ),
      'path' => 'transition/transition-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'transition-native' => 
        array (
          'path' => 'transition/transition-native-min.js',
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'transition',
          'name' => 'transition-native',
          'ext' => false,
        ),
        'transition-timer' => 
        array (
          'path' => 'transition/transition-timer-min.js',
          'requires' => 
          array (
            0 => 'transition-native',
            1 => 'node-style',
          ),
          'type' => 'js',
          'pkg' => 'transition',
          'name' => 'transition-timer',
          'ext' => false,
        ),
      ),
      'name' => 'transition',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_th-TH' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_th-TH.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_th-TH',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_th-TH',
    ),
    'async-queue' => 
    array (
      'path' => 'async-queue/async-queue-min.js',
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'type' => 'js',
      'name' => 'async-queue',
      'ext' => false,
    ),
    'widget-position-constrain' => 
    array (
      'path' => 'widget/widget-position-constrain-min.js',
      'requires' => 
      array (
        0 => 'widget-position',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-position-constrain',
      'ext' => false,
    ),
    'cache-base' => 
    array (
      'path' => 'cache/cache-base-min.js',
      'requires' => 
      array (
        0 => 'base',
      ),
      'type' => 'js',
      'pkg' => 'cache',
      'name' => 'cache-base',
      'ext' => false,
    ),
    'lang/datatype_ca' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ca.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ca',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ca',
    ),
    'widget-stack' => 
    array (
      'path' => 'widget/widget-stack-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'base-build',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-stack',
      'ext' => false,
    ),
    'history' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'history/history-min.js',
      'supersedes' => 
      array (
        0 => 'history-html5',
        1 => 'history-hash',
        2 => 'history-base',
      ),
      'submodules' => 
      array (
        'history-html5' => 
        array (
          'path' => 'history/history-html5-min.js',
          'requires' => 
          array (
            0 => 'history-base',
            1 => 'event-base',
            2 => 'node-base',
          ),
          'type' => 'js',
          'optional' => 
          array (
            0 => 'json',
          ),
          'pkg' => 'history',
          'name' => 'history-html5',
          'ext' => false,
        ),
        'history-hash' => 
        array (
          'after' => 
          array (
            0 => 'history-html5',
          ),
          'path' => 'history/history-hash-min.js',
          'requires' => 
          array (
            0 => 'event-synthetic',
            1 => 'history-base',
            2 => 'yui-later',
          ),
          'type' => 'js',
          'pkg' => 'history',
          'name' => 'history-hash',
          'ext' => false,
        ),
        'history-base' => 
        array (
          'after' => 
          array (
            0 => 'history-deprecated',
          ),
          'path' => 'history/history-base-min.js',
          'requires' => 
          array (
            0 => 'event-custom-complex',
          ),
          'type' => 'js',
          'pkg' => 'history',
          'name' => 'history-base',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'plugins' => 
      array (
        'history-hash-ie' => 
        array (
          'condition' => 
          array (
            'trigger' => 'history-hash',
          ),
          'path' => 'history/history-hash-ie-min.js',
          'requires' => 
          array (
            0 => 'history-hash',
            1 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'history',
          'name' => 'history-hash-ie',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'history',
      'rollup' => 3,
    ),
    'lang/datatype-date_el' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_el.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_el',
    ),
    'lang/datatype-date_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en',
    ),
    'lang/datatype_ja-JP' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ja-JP.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ja-JP',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ja-JP',
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
    'lang/datatype-date_es' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es',
    ),
    'dd-ddm' => 
    array (
      'path' => 'dd/dd-ddm-min.js',
      'requires' => 
      array (
        0 => 'dd-ddm-base',
        1 => 'event-resize',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-ddm',
      'ext' => false,
    ),
    'skin-sam-widget-child' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-child.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-child',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_da' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_da.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_da',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_da',
    ),
    'event-custom-complex' => 
    array (
      'path' => 'event-custom/event-custom-complex-min.js',
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'type' => 'js',
      'pkg' => 'event-custom',
      'name' => 'event-custom-complex',
      'ext' => false,
    ),
    'lang/datatype_es-PE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-PE.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-PE',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-PE',
    ),
    'dd-drag' => 
    array (
      'path' => 'dd/dd-drag-min.js',
      'requires' => 
      array (
        0 => 'dd-ddm-base',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-drag',
      'ext' => false,
    ),
    'cssreset' => 
    array (
      'path' => 'cssreset/reset-min.css',
      'ext' => false,
      'type' => 'css',
      'name' => 'cssreset',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_de' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_de.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_de',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_de',
    ),
    'event-base' => 
    array (
      'path' => 'event/event-base-min.js',
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'expound' => 'node-base',
      'pkg' => 'event',
      'name' => 'event-base',
      'type' => 'js',
      'ext' => false,
    ),
    'selector-css2' => 
    array (
      'path' => 'dom/selector-css2-min.js',
      'requires' => 
      array (
        0 => 'selector-native',
      ),
      'type' => 'js',
      'pkg' => 'dom',
      'name' => 'selector-css2',
      'ext' => false,
    ),
    'selector-css3' => 
    array (
      'path' => 'dom/selector-css3-min.js',
      'requires' => 
      array (
        0 => 'selector-css2',
      ),
      'type' => 'js',
      'pkg' => 'dom',
      'name' => 'selector-css3',
      'ext' => false,
    ),
    'lang/datatype-date_fi' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_fi.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_fi',
    ),
    'json-parse' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'expanded_map' => 
      array (
        'yui-base' => true,
      ),
      'path' => 'json/json-parse-min.js',
      'provides' => 
      array (
        'json-parse' => true,
      ),
      'type' => 'js',
      'pkg' => 'json',
      '_inspected' => true,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
      'ext' => false,
      '_parsed' => false,
      'name' => 'json-parse',
    ),
    'lang/datatype_en-US' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-US.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-US',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-US',
    ),
    'node-flick' => 
    array (
      'path' => 'node-flick/node-flick-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'transition',
        1 => 'plugin',
        2 => 'classnamemanager',
        3 => 'event-flick',
      ),
      'type' => 'js',
      'name' => 'node-flick',
      'ext' => false,
    ),
    'editor-base' => 
    array (
      'path' => 'editor/editor-base-min.js',
      'requires' => 
      array (
        0 => 'frame',
        1 => 'exec-command',
        2 => 'base',
        3 => 'node',
        4 => 'selection',
        5 => 'editor-para',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'editor-base',
      'ext' => false,
    ),
    'node-focusmanager' => 
    array (
      'path' => 'node-focusmanager/node-focusmanager-min.js',
      'requires' => 
      array (
        0 => 'node-event-simulate',
        1 => 'node',
        2 => 'plugin',
        3 => 'attribute',
        4 => 'event-key',
        5 => 'event-focus',
      ),
      'type' => 'js',
      'name' => 'node-focusmanager',
      'ext' => false,
    ),
    'lang/datatype_vi-VN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_vi-VN.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_vi-VN',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_vi-VN',
    ),
    'lang/datatype-date_fr' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_fr.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_fr',
    ),
    'lang/datatype_es-PY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-PY.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-PY',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-PY',
    ),
    'dd' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'dd/dd-min.js',
      'supersedes' => 
      array (
        0 => 'dd-delegate',
        1 => 'dd-constrain',
        2 => 'dd-proxy',
        3 => 'dd-scroll',
        4 => 'dd-ddm-drop',
        5 => 'dd-drag',
        6 => 'dd-ddm-base',
        7 => 'dd-ddm',
        8 => 'dd-drop',
      ),
      'submodules' => 
      array (
        'dd-delegate' => 
        array (
          'path' => 'dd/dd-delegate-min.js',
          'requires' => 
          array (
            0 => 'dd-drop-plugin',
            1 => 'event-mouseenter',
            2 => 'dd-drag',
          ),
          'type' => 'js',
          'pkg' => 'dd',
          'name' => 'dd-delegate',
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
          'pkg' => 'dd',
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
          'pkg' => 'dd',
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
          'pkg' => 'dd',
          'name' => 'dd-scroll',
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
          'pkg' => 'dd',
          'name' => 'dd-ddm-drop',
          'ext' => false,
        ),
        'dd-ddm' => 
        array (
          'path' => 'dd/dd-ddm-min.js',
          'requires' => 
          array (
            0 => 'dd-ddm-base',
            1 => 'event-resize',
          ),
          'type' => 'js',
          'pkg' => 'dd',
          'name' => 'dd-ddm',
          'ext' => false,
        ),
        'dd-ddm-base' => 
        array (
          'path' => 'dd/dd-ddm-base-min.js',
          'requires' => 
          array (
            0 => 'base',
            1 => 'node',
            2 => 'yui-throttle',
            3 => 'classnamemanager',
          ),
          'type' => 'js',
          'pkg' => 'dd',
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
          'pkg' => 'dd',
          'name' => 'dd-drag',
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
          'pkg' => 'dd',
          'name' => 'dd-drop',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'plugins' => 
      array (
        'dd-drop-plugin' => 
        array (
          'path' => 'dd/dd-drop-plugin-min.js',
          'requires' => 
          array (
            0 => 'dd-drop',
          ),
          'type' => 'js',
          'pkg' => 'dd',
          'name' => 'dd-drop-plugin',
          'ext' => false,
        ),
        'dd-gestures' => 
        array (
          'condition' => 
          array (
            'trigger' => 'dd-drag',
          ),
          'path' => 'dd/dd-gestures-min.js',
          'requires' => 
          array (
            0 => 'event-move',
            1 => 'dd-drag',
          ),
          'type' => 'js',
          'pkg' => 'dd',
          'name' => 'dd-gestures',
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
          'pkg' => 'dd',
          'name' => 'dd-plugin',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'dd',
      'rollup' => 4,
    ),
    'history-html5' => 
    array (
      'path' => 'history/history-html5-min.js',
      'requires' => 
      array (
        0 => 'history-base',
        1 => 'event-base',
        2 => 'node-base',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'json',
      ),
      'pkg' => 'history',
      'name' => 'history-html5',
      'ext' => false,
    ),
    'clickable-rail' => 
    array (
      'path' => 'slider/clickable-rail-min.js',
      'requires' => 
      array (
        0 => 'slider-base',
      ),
      'type' => 'js',
      'pkg' => 'slider',
      'name' => 'clickable-rail',
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
      'pkg' => 'node',
      'name' => 'node-style',
      'ext' => false,
    ),
    'lang/datatype-date_nl-BE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_nl-BE.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_nl-BE',
    ),
    'lang/datatype_el' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_el.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_el',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_el',
    ),
    'lang/datatype_pt-BR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_pt-BR.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_pt-BR',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_pt-BR',
    ),
    'lang/datatype_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en',
    ),
    'lang/datatype_es' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es',
    ),
    'transition-timer' => 
    array (
      'path' => 'transition/transition-timer-min.js',
      'requires' => 
      array (
        0 => 'transition-native',
        1 => 'node-style',
      ),
      'type' => 'js',
      'pkg' => 'transition',
      'name' => 'transition-timer',
      'ext' => false,
    ),
    'queue-promote' => 
    array (
      'path' => 'queue-promote/queue-promote-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'queue-promote',
      'ext' => false,
    ),
    'querystring-parse-simple' => 
    array (
      'path' => 'querystring/querystring-parse-simple-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'querystring-parse-simple',
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
        6 => 'datasource-get',
        7 => 'datasource-polling',
        8 => 'datasource-io',
        9 => 'datasource-textschema',
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
            0 => 'cache-base',
            1 => 'datasource-local',
          ),
          'type' => 'js',
          'pkg' => 'datasource',
          'name' => 'datasource-cache',
          'ext' => false,
        ),
        'datasource-xmlschema' => 
        array (
          'path' => 'datasource/datasource-xmlschema-min.js',
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'datasource-local',
            2 => 'dataschema-xml',
          ),
          'type' => 'js',
          'pkg' => 'datasource',
          'name' => 'datasource-xmlschema',
          'ext' => false,
        ),
        'datasource-arrayschema' => 
        array (
          'path' => 'datasource/datasource-arrayschema-min.js',
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'datasource-local',
            2 => 'dataschema-array',
          ),
          'type' => 'js',
          'pkg' => 'datasource',
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
          'pkg' => 'datasource',
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
          'pkg' => 'datasource',
          'name' => 'datasource-local',
          'ext' => false,
        ),
        'datasource-jsonschema' => 
        array (
          'path' => 'datasource/datasource-jsonschema-min.js',
          'requires' => 
          array (
            0 => 'dataschema-json',
            1 => 'plugin',
            2 => 'datasource-local',
          ),
          'type' => 'js',
          'pkg' => 'datasource',
          'name' => 'datasource-jsonschema',
          'ext' => false,
        ),
        'datasource-get' => 
        array (
          'path' => 'datasource/datasource-get-min.js',
          'requires' => 
          array (
            0 => 'get',
            1 => 'datasource-local',
          ),
          'type' => 'js',
          'pkg' => 'datasource',
          'name' => 'datasource-get',
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
          'pkg' => 'datasource',
          'name' => 'datasource-polling',
          'ext' => false,
        ),
        'datasource-io' => 
        array (
          'path' => 'datasource/datasource-io-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'datasource-local',
          ),
          'type' => 'js',
          'pkg' => 'datasource',
          'name' => 'datasource-io',
          'ext' => false,
        ),
        'datasource-textschema' => 
        array (
          'path' => 'datasource/datasource-textschema-min.js',
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'datasource-local',
            2 => 'dataschema-text',
          ),
          'type' => 'js',
          'pkg' => 'datasource',
          'name' => 'datasource-textschema',
          'ext' => false,
        ),
      ),
      'name' => 'datasource',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_fi' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_fi.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_fi',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_fi',
    ),
    'editor' => 
    array (
      'supersedes' => 
      array (
        0 => 'createlink-base',
        1 => 'editor-base',
        2 => 'exec-command',
        3 => 'frame',
        4 => 'editor-bidi',
        5 => 'selection',
        6 => 'editor-para',
        7 => 'editor-lists',
      ),
      'path' => 'editor/editor-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'editor-base' => 
        array (
          'path' => 'editor/editor-base-min.js',
          'requires' => 
          array (
            0 => 'frame',
            1 => 'exec-command',
            2 => 'base',
            3 => 'node',
            4 => 'selection',
            5 => 'editor-para',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'editor-base',
          'ext' => false,
        ),
        'createlink-base' => 
        array (
          'path' => 'editor/createlink-base-min.js',
          'requires' => 
          array (
            0 => 'editor-base',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'createlink-base',
          'ext' => false,
        ),
        'editor-lists' => 
        array (
          'path' => 'editor/editor-lists-min.js',
          'requires' => 
          array (
            0 => 'editor-base',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'editor-lists',
          'ext' => false,
        ),
        'exec-command' => 
        array (
          'path' => 'editor/exec-command-min.js',
          'requires' => 
          array (
            0 => 'frame',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'exec-command',
          'ext' => false,
        ),
        'frame' => 
        array (
          'path' => 'editor/frame-min.js',
          'requires' => 
          array (
            0 => 'base',
            1 => 'node',
            2 => 'selector-css3',
            3 => 'substitute',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'frame',
          'ext' => false,
        ),
        'selection' => 
        array (
          'path' => 'editor/selection-min.js',
          'requires' => 
          array (
            0 => 'node',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'selection',
          'ext' => false,
        ),
        'editor-para' => 
        array (
          'path' => 'editor/editor-para-min.js',
          'requires' => 
          array (
            0 => 'editor-base',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'editor-para',
          'ext' => false,
        ),
        'editor-bidi' => 
        array (
          'path' => 'editor/editor-bidi-min.js',
          'requires' => 
          array (
            0 => 'editor-base',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'editor-bidi',
          'ext' => false,
        ),
      ),
      'name' => 'editor',
      'requires' => 
      array (
      ),
    ),
    'skin-sam-widget-position' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_en-AU' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-AU.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-AU',
    ),
    'scrollview' => 
    array (
      'path' => 'scrollview/scrollview-min.js',
      'requires' => 
      array (
        0 => 'scrollview-base',
        1 => 'scrollview-scrollbars',
      ),
      'type' => 'js',
      'name' => 'scrollview',
      'ext' => false,
      'plugins' => 
      array (
        'scrollview-base' => 
        array (
          'path' => 'scrollview/scrollview-base-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'widget',
            1 => 'event-gestures',
            2 => 'transition',
          ),
          'type' => 'js',
          'pkg' => 'scrollview',
          'name' => 'scrollview-base',
          'ext' => false,
        ),
        'scrollview-paginator' => 
        array (
          'path' => 'scrollview/scrollview-paginator-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'scrollview',
          'name' => 'scrollview-paginator',
          'ext' => false,
        ),
        'scrollview-scrollbars' => 
        array (
          'path' => 'scrollview/scrollview-scrollbars-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'scrollview',
          'name' => 'scrollview-scrollbars',
          'ext' => false,
        ),
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
    'lang/datatype-date_hi' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_hi.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_hi',
    ),
    'widget' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'widget/widget-min.js',
      'supersedes' => 
      array (
        0 => 'widget-base',
        1 => 'widget-htmlparser',
      ),
      'submodules' => 
      array (
        'widget-base' => 
        array (
          'path' => 'widget/widget-base-min.js',
          'requires' => 
          array (
            0 => 'classnamemanager',
            1 => 'base-pluginhost',
            2 => 'attribute',
            3 => 'base-base',
            4 => 'node-base',
            5 => 'node-style',
            6 => 'event-focus',
            7 => 'node-event-delegate',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-base',
          'ext' => false,
        ),
        'widget-htmlparser' => 
        array (
          'path' => 'widget/widget-htmlparser-min.js',
          'requires' => 
          array (
            0 => 'widget-base',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-htmlparser',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'skinnable' => true,
      'plugins' => 
      array (
        'widget-parent' => 
        array (
          'path' => 'widget/widget-parent-min.js',
          'requires' => 
          array (
            0 => 'widget',
            1 => 'base-build',
            2 => 'arraylist',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-parent',
          'ext' => false,
        ),
        'widget-stack' => 
        array (
          'path' => 'widget/widget-stack-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'widget',
            1 => 'base-build',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-stack',
          'ext' => false,
        ),
        'widget-position' => 
        array (
          'path' => 'widget/widget-position-min.js',
          'requires' => 
          array (
            0 => 'node-screen',
            1 => 'widget',
            2 => 'base-build',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-position',
          'ext' => false,
        ),
        'widget-child' => 
        array (
          'path' => 'widget/widget-child-min.js',
          'requires' => 
          array (
            0 => 'widget',
            1 => 'base-build',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-child',
          'ext' => false,
        ),
        'widget-stdmod' => 
        array (
          'path' => 'widget/widget-stdmod-min.js',
          'requires' => 
          array (
            0 => 'widget',
            1 => 'base-build',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-stdmod',
          'ext' => false,
        ),
        'widget-position-constrain' => 
        array (
          'path' => 'widget/widget-position-constrain-min.js',
          'requires' => 
          array (
            0 => 'widget-position',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-position-constrain',
          'ext' => false,
        ),
        'widget-position-align' => 
        array (
          'path' => 'widget/widget-position-align-min.js',
          'requires' => 
          array (
            0 => 'widget-position',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-position-align',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'widget',
      'rollup' => 2,
    ),
    'widget-base' => 
    array (
      'path' => 'widget/widget-base-min.js',
      'requires' => 
      array (
        0 => 'node-event-delegate',
        1 => 'base-pluginhost',
        2 => 'attribute',
        3 => 'base-base',
        4 => 'classnamemanager',
        5 => 'node-base',
        6 => 'node-style',
        7 => 'event-focus',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-base',
      'ext' => false,
    ),
    'lang/datatype_fr' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_fr.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_fr',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_fr',
    ),
    'lang/datatype-date_es-US' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-US.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-US',
    ),
    'datasource-arrayschema' => 
    array (
      'path' => 'datasource/datasource-arrayschema-min.js',
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'datasource-local',
        2 => 'dataschema-array',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-arrayschema',
      'ext' => false,
    ),
    'event-key' => 
    array (
      'path' => 'event/event-key-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-key',
      'ext' => false,
    ),
    'lang/datatype-date_es-UY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-UY.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-UY',
    ),
    'widget-position' => 
    array (
      'path' => 'widget/widget-position-min.js',
      'requires' => 
      array (
        0 => 'node-screen',
        1 => 'widget',
        2 => 'base-build',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-position',
      'ext' => false,
    ),
    'lang/datatype-date_es-VE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-VE.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-VE',
    ),
    'lang/datatype-date_id' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_id.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_id',
    ),
    'lang/datatype-date_zh-Hans-CN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_zh-Hans-CN.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_zh-Hans-CN',
    ),
    'features' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'yui/features-min.js',
      'provides' => 
      array (
        'features' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'yui',
      'ext' => false,
      'name' => 'features',
    ),
    'lang/datatype-date_nb-NO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_nb-NO.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_nb-NO',
    ),
    'get' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'yui/get-min.js',
      'provides' => 
      array (
        'get' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'yui',
      'ext' => false,
      'name' => 'get',
    ),
    'base-build' => 
    array (
      'path' => 'base/base-build-min.js',
      'requires' => 
      array (
        0 => 'base-base',
      ),
      'type' => 'js',
      'pkg' => 'base',
      'name' => 'base-build',
      'ext' => false,
    ),
    'lang/datatype-date_fi-FI' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_fi-FI.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_fi-FI',
    ),
    'lang/datatype-date_en-CA' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-CA.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-CA',
    ),
    'event-touch' => 
    array (
      'path' => 'event/event-touch-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-touch',
      'ext' => false,
    ),
    'event-custom' => 
    array (
      'supersedes' => 
      array (
        0 => 'event-custom-complex',
        1 => 'event-custom-base',
      ),
      'path' => 'event-custom/event-custom-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'event-custom-complex' => 
        array (
          'path' => 'event-custom/event-custom-complex-min.js',
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'type' => 'js',
          'pkg' => 'event-custom',
          'name' => 'event-custom-complex',
          'ext' => false,
        ),
        'event-custom-base' => 
        array (
          'path' => 'event-custom/event-custom-base-min.js',
          'requires' => 
          array (
            0 => 'yui-later',
            1 => 'oop',
          ),
          'type' => 'js',
          'pkg' => 'event-custom',
          'name' => 'event-custom-base',
          'ext' => false,
        ),
      ),
      'name' => 'event-custom',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_it' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_it.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_it',
    ),
    'yui-log' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'yui/yui-log-min.js',
      'provides' => 
      array (
        'yui-log' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'yui',
      'ext' => false,
      'name' => 'yui-log',
    ),
    'skin-sam-widget-position-constrain' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position-constrain.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position-constrain',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'widget-child' => 
    array (
      'path' => 'widget/widget-child-min.js',
      'requires' => 
      array (
        0 => 'widget',
        1 => 'base-build',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-child',
      'ext' => false,
    ),
    'widget-anim' => 
    array (
      'path' => 'widget-anim/widget-anim-min.js',
      'requires' => 
      array (
        0 => 'widget',
        1 => 'plugin',
        2 => 'anim-base',
      ),
      'type' => 'js',
      'name' => 'widget-anim',
      'ext' => false,
    ),
    'node-menunav' => 
    array (
      'path' => 'node-menunav/node-menunav-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'node',
        1 => 'node-focusmanager',
        2 => 'plugin',
        3 => 'classnamemanager',
      ),
      'type' => 'js',
      'name' => 'node-menunav',
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
      'pkg' => 'anim',
      'name' => 'anim-easing',
      'ext' => false,
    ),
    'lang/datatype-date_ja' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ja.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ja',
    ),
    'yql' => 
    array (
      'path' => 'yql/yql-min.js',
      'requires' => 
      array (
        0 => 'jsonp-url',
        1 => 'jsonp',
      ),
      'type' => 'js',
      'name' => 'yql',
      'ext' => false,
    ),
    'lang/datatype_hi' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_hi.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_hi',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_hi',
    ),
    'lang/datatype-date_zh-Hant-HK' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_zh-Hant-HK.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_zh-Hant-HK',
    ),
    'lang/datatype_nl-BE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_nl-BE.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_nl-BE',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_nl-BE',
    ),
    'queue-run' => 
    array (
      'path' => 'async-queue/async-queue-min.js',
      'requires' => 
      array (
        0 => 'event-custom',
      ),
      'type' => 'js',
      'name' => 'queue-run',
      'ext' => false,
    ),
    'createlink-base' => 
    array (
      'path' => 'editor/createlink-base-min.js',
      'requires' => 
      array (
        0 => 'editor-base',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'createlink-base',
      'ext' => false,
    ),
    'lang/datatype-date_ca-ES' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ca-ES.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ca-ES',
    ),
    'slider' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'slider/slider-min.js',
      'supersedes' => 
      array (
        0 => 'slider-value-range',
        1 => 'slider-base',
        2 => 'clickable-rail',
        3 => 'skin-sam-slider-base',
        4 => 'range-slider',
      ),
      'submodules' => 
      array (
        'slider-value-range' => 
        array (
          'path' => 'slider/slider-value-range-min.js',
          'requires' => 
          array (
            0 => 'slider-base',
          ),
          'type' => 'js',
          'pkg' => 'slider',
          'name' => 'slider-value-range',
          'ext' => false,
        ),
        'clickable-rail' => 
        array (
          'path' => 'slider/clickable-rail-min.js',
          'requires' => 
          array (
            0 => 'slider-base',
          ),
          'type' => 'js',
          'pkg' => 'slider',
          'name' => 'clickable-rail',
          'ext' => false,
        ),
        'slider-base' => 
        array (
          'path' => 'slider/slider-base-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'widget',
            1 => 'substitute',
            2 => 'dd-constrain',
          ),
          'type' => 'js',
          'pkg' => 'slider',
          'name' => 'slider-base',
          'ext' => false,
        ),
        'range-slider' => 
        array (
          'path' => 'slider/range-slider-min.js',
          'requires' => 
          array (
            0 => 'slider-value-range',
            1 => 'clickable-rail',
            2 => 'slider-base',
          ),
          'type' => 'js',
          'pkg' => 'slider',
          'name' => 'range-slider',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'skinnable' => true,
      'ext' => false,
      'name' => 'slider',
      'rollup' => 3,
    ),
    'scrollview-paginator' => 
    array (
      'path' => 'scrollview/scrollview-paginator-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'plugin',
      ),
      'type' => 'js',
      'pkg' => 'scrollview',
      'name' => 'scrollview-paginator',
      'ext' => false,
    ),
    'event-delegate' => 
    array (
      'path' => 'event/event-delegate-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-delegate',
      'ext' => false,
    ),
    'lang/datatype-date_da-DK' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_da-DK.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_da-DK',
    ),
    'sortable-scroll' => 
    array (
      'path' => 'sortable/sortable-scroll-min.js',
      'requires' => 
      array (
        0 => 'dd-scroll',
      ),
      'type' => 'js',
      'pkg' => 'sortable',
      'name' => 'sortable-scroll',
      'ext' => false,
    ),
    'lang/datatype_id' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_id.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_id',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_id',
    ),
    'dataschema-xml' => 
    array (
      'path' => 'dataschema/dataschema-xml-min.js',
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'type' => 'js',
      'pkg' => 'dataschema',
      'name' => 'dataschema-xml',
      'ext' => false,
    ),
    'rls' => 
    array (
      'requires' => 
      array (
        0 => 'features',
      ),
      'path' => 'yui/rls-min.js',
      'provides' => 
      array (
        'rls' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'yui',
      'ext' => false,
      'name' => 'rls',
    ),
    'loader-rollup' => 
    array (
      'requires' => 
      array (
        0 => 'loader-base',
      ),
      'path' => 'loader/loader-rollup-min.js',
      'provides' => 
      array (
        'loader-rollup' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'loader',
      'ext' => false,
      'name' => 'loader-rollup',
    ),
    'event' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'event/event-min.js',
      'supersedes' => 
      array (
        0 => 'event-resize',
        1 => 'event-mousewheel',
        2 => 'event-base',
        3 => 'event-mouseenter',
        4 => 'event-delegate',
        5 => 'event-key',
        6 => 'event-focus',
        7 => 'event-synthetic',
      ),
      'expound' => 'node-base',
      'type' => 'js',
      'submodules' => 
      array (
        'event-synthetic' => 
        array (
          'path' => 'event/event-synthetic-min.js',
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'event-custom',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-synthetic',
          'ext' => false,
        ),
        'event-base' => 
        array (
          'path' => 'event/event-base-min.js',
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'expound' => 'node-base',
          'pkg' => 'event',
          'name' => 'event-base',
          'type' => 'js',
          'ext' => false,
        ),
        'event-mousewheel' => 
        array (
          'path' => 'event/event-mousewheel-min.js',
          'requires' => 
          array (
            0 => 'event-synthetic',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-mousewheel',
          'ext' => false,
        ),
        'event-mouseenter' => 
        array (
          'path' => 'event/event-mouseenter-min.js',
          'requires' => 
          array (
            0 => 'event-synthetic',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-mouseenter',
          'ext' => false,
        ),
        'event-delegate' => 
        array (
          'path' => 'event/event-delegate-min.js',
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-delegate',
          'ext' => false,
        ),
        'event-key' => 
        array (
          'path' => 'event/event-key-min.js',
          'requires' => 
          array (
            0 => 'event-synthetic',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-key',
          'ext' => false,
        ),
        'event-focus' => 
        array (
          'path' => 'event/event-focus-min.js',
          'requires' => 
          array (
            0 => 'event-synthetic',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-focus',
          'ext' => false,
        ),
        'event-resize' => 
        array (
          'path' => 'event/event-resize-min.js',
          'requires' => 
          array (
            0 => 'event-synthetic',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-resize',
          'ext' => false,
        ),
      ),
      'plugins' => 
      array (
        'event-touch' => 
        array (
          'path' => 'event/event-touch-min.js',
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-touch',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'event',
      'rollup' => 4,
    ),
    'cssgrids-deprecated' => 
    array (
      'path' => 'cssgrids-deprecated/grids-min.css',
      'requires' => 
      array (
        0 => 'cssfonts',
      ),
      'type' => 'css',
      'optional' => 
      array (
        0 => 'cssreset',
      ),
      'name' => 'cssgrids-deprecated',
      'ext' => false,
    ),
    'lang/datatype_en-AU' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-AU.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-AU',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-AU',
    ),
    'datasource-io' => 
    array (
      'path' => 'datasource/datasource-io-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'datasource-local',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-io',
      'ext' => false,
    ),
    'lang/datatype_es-US' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-US.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-US',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-US',
    ),
    'lang/datatype_it' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_it.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_it',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_it',
    ),
    'lang/datatype-date_ko' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ko.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ko',
    ),
    'lang/datatype_es-UY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-UY.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-UY',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-UY',
    ),
    'imageloader' => 
    array (
      'path' => 'imageloader/imageloader-min.js',
      'requires' => 
      array (
        0 => 'node-screen',
        1 => 'base-base',
        2 => 'node-style',
      ),
      'type' => 'js',
      'name' => 'imageloader',
      'ext' => false,
    ),
    'skin-sam-widget-position-align' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position-align.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position-align',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'dd-scroll' => 
    array (
      'path' => 'dd/dd-scroll-min.js',
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-scroll',
      'ext' => false,
    ),
    'lang/datatype_ja' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ja.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ja',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ja',
    ),
    'anim-base' => 
    array (
      'path' => 'anim/anim-base-min.js',
      'requires' => 
      array (
        0 => 'base-base',
        1 => 'node-style',
      ),
      'type' => 'js',
      'pkg' => 'anim',
      'name' => 'anim-base',
      'ext' => false,
    ),
    'node-base' => 
    array (
      'path' => 'node/node-base-min.js',
      'requires' => 
      array (
        0 => 'selector-css2',
        1 => 'dom-base',
        2 => 'event-base',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-base',
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
          'pkg' => 'io',
          'name' => 'io-queue',
          'ext' => false,
        ),
        'io-upload-iframe' => 
        array (
          'path' => 'io/io-upload-iframe-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'io',
          'name' => 'io-upload-iframe',
          'ext' => false,
        ),
        'io-base' => 
        array (
          'path' => 'io/io-base-min.js',
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'type' => 'js',
          'optional' => 
          array (
            0 => 'querystring-stringify-simple',
          ),
          'pkg' => 'io',
          'name' => 'io-base',
          'ext' => false,
        ),
        'io-form' => 
        array (
          'path' => 'io/io-form-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'node-base',
            2 => 'node-style',
          ),
          'type' => 'js',
          'pkg' => 'io',
          'name' => 'io-form',
          'ext' => false,
        ),
        'io-xdr' => 
        array (
          'path' => 'io/io-xdr-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'datatype-xml',
          ),
          'type' => 'js',
          'pkg' => 'io',
          'name' => 'io-xdr',
          'ext' => false,
        ),
      ),
      'name' => 'io',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_es-VE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-VE.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-VE',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-VE',
    ),
    'lang/datatype-date_tr-TR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_tr-TR.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_tr-TR',
    ),
    'collection' => 
    array (
      'supersedes' => 
      array (
        0 => 'array-invoke',
        1 => 'arraylist-filter',
        2 => 'arraylist',
        3 => 'array-extras',
        4 => 'arraylist-add',
      ),
      'path' => 'collection/collection-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'array-invoke' => 
        array (
          'path' => 'collection/array-invoke-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'collection',
          'name' => 'array-invoke',
          'requires' => 
          array (
          ),
        ),
        'arraylist-filter' => 
        array (
          'path' => 'collection/arraylist-filter-min.js',
          'requires' => 
          array (
            0 => 'arraylist',
          ),
          'type' => 'js',
          'pkg' => 'collection',
          'name' => 'arraylist-filter',
          'ext' => false,
        ),
        'arraylist' => 
        array (
          'path' => 'collection/arraylist-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'collection',
          'name' => 'arraylist',
          'requires' => 
          array (
          ),
        ),
        'array-extras' => 
        array (
          'path' => 'collection/array-extras-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'collection',
          'name' => 'array-extras',
          'requires' => 
          array (
          ),
        ),
        'arraylist-add' => 
        array (
          'path' => 'collection/arraylist-add-min.js',
          'requires' => 
          array (
            0 => 'arraylist',
          ),
          'type' => 'js',
          'pkg' => 'collection',
          'name' => 'arraylist-add',
          'ext' => false,
        ),
      ),
      'name' => 'collection',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_id-ID' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_id-ID.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_id-ID',
    ),
    'lang/datatype_nb-NO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_nb-NO.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_nb-NO',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_nb-NO',
    ),
    'lang/datatype_fi-FI' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_fi-FI.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_fi-FI',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_fi-FI',
    ),
    'lang/datatype_en-CA' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-CA.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-CA',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-CA',
    ),
    'attribute' => 
    array (
      'supersedes' => 
      array (
        0 => 'attribute-base',
        1 => 'attribute-complex',
      ),
      'path' => 'attribute/attribute-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'attribute-base' => 
        array (
          'path' => 'attribute/attribute-base-min.js',
          'requires' => 
          array (
            0 => 'event-custom',
          ),
          'type' => 'js',
          'pkg' => 'attribute',
          'name' => 'attribute-base',
          'ext' => false,
        ),
        'attribute-complex' => 
        array (
          'path' => 'attribute/attribute-complex-min.js',
          'requires' => 
          array (
            0 => 'attribute-base',
          ),
          'type' => 'js',
          'pkg' => 'attribute',
          'name' => 'attribute-complex',
          'ext' => false,
        ),
      ),
      'name' => 'attribute',
      'requires' => 
      array (
      ),
    ),
    'dd-proxy' => 
    array (
      'path' => 'dd/dd-proxy-min.js',
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-proxy',
      'ext' => false,
    ),
    'datatype' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/datatype-min.js',
      'supersedes' => 
      array (
        0 => 'datatype-date-format',
        1 => 'datatype-xml',
        2 => 'datatype-number',
        3 => 'datatype-date',
      ),
      'submodules' => 
      array (
        'datatype-date' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'datatype/datatype-date-min.js',
          'supersedes' => 
          array (
            0 => 'datatype-date-format',
          ),
          'type' => 'js',
          'pkg' => 'datatype',
          'lang' => 
          array (
            0 => 'ar',
            1 => 'ar-JO',
            2 => 'ca',
            3 => 'ca-ES',
            4 => 'da',
            5 => 'da-DK',
            6 => 'de',
            7 => 'de-AT',
            8 => 'de-DE',
            9 => 'el',
            10 => 'el-GR',
            11 => 'en',
            12 => 'en-AU',
            13 => 'en-CA',
            14 => 'en-GB',
            15 => 'en-IE',
            16 => 'en-IN',
            17 => 'en-JO',
            18 => 'en-MY',
            19 => 'en-NZ',
            20 => 'en-PH',
            21 => 'en-SG',
            22 => 'en-US',
            23 => 'es',
            24 => 'es-AR',
            25 => 'es-BO',
            26 => 'es-CL',
            27 => 'es-CO',
            28 => 'es-EC',
            29 => 'es-ES',
            30 => 'es-MX',
            31 => 'es-PE',
            32 => 'es-PY',
            33 => 'es-US',
            34 => 'es-UY',
            35 => 'es-VE',
            36 => 'fi',
            37 => 'fi-FI',
            38 => 'fr',
            39 => 'fr-BE',
            40 => 'fr-CA',
            41 => 'fr-FR',
            42 => 'hi',
            43 => 'hi-IN',
            44 => 'id',
            45 => 'id-ID',
            46 => 'it',
            47 => 'it-IT',
            48 => 'ja',
            49 => 'ja-JP',
            50 => 'ko',
            51 => 'ko-KR',
            52 => 'ms',
            53 => 'ms-MY',
            54 => 'nb',
            55 => 'nb-NO',
            56 => 'nl',
            57 => 'nl-BE',
            58 => 'nl-NL',
            59 => 'pl',
            60 => 'pl-PL',
            61 => 'pt',
            62 => 'pt-BR',
            63 => 'ro',
            64 => 'ro-RO',
            65 => 'ru',
            66 => 'ru-RU',
            67 => 'sv',
            68 => 'sv-SE',
            69 => 'th',
            70 => 'th-TH',
            71 => 'tr',
            72 => 'tr-TR',
            73 => 'vi',
            74 => 'vi-VN',
            75 => 'zh-Hans',
            76 => 'zh-Hans-CN',
            77 => 'zh-Hant',
            78 => 'zh-Hant-HK',
            79 => 'zh-Hant-TW',
          ),
          'ext' => false,
          'name' => 'datatype-date',
        ),
        'datatype-xml' => 
        array (
          'path' => 'datatype/datatype-xml-min.js',
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'pkg' => 'datatype',
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
          'pkg' => 'datatype',
          'name' => 'datatype-number',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'ext' => false,
      'lang' => 
      array (
        0 => 'ar',
        1 => 'ar-JO',
        2 => 'ca',
        3 => 'ca-ES',
        4 => 'da',
        5 => 'da-DK',
        6 => 'de',
        7 => 'de-AT',
        8 => 'de-DE',
        9 => 'el',
        10 => 'el-GR',
        11 => 'en',
        12 => 'en-AU',
        13 => 'en-CA',
        14 => 'en-GB',
        15 => 'en-IE',
        16 => 'en-IN',
        17 => 'en-JO',
        18 => 'en-MY',
        19 => 'en-NZ',
        20 => 'en-PH',
        21 => 'en-SG',
        22 => 'en-US',
        23 => 'es',
        24 => 'es-AR',
        25 => 'es-BO',
        26 => 'es-CL',
        27 => 'es-CO',
        28 => 'es-EC',
        29 => 'es-ES',
        30 => 'es-MX',
        31 => 'es-PE',
        32 => 'es-PY',
        33 => 'es-US',
        34 => 'es-UY',
        35 => 'es-VE',
        36 => 'fi',
        37 => 'fi-FI',
        38 => 'fr',
        39 => 'fr-BE',
        40 => 'fr-CA',
        41 => 'fr-FR',
        42 => 'hi',
        43 => 'hi-IN',
        44 => 'id',
        45 => 'id-ID',
        46 => 'it',
        47 => 'it-IT',
        48 => 'ja',
        49 => 'ja-JP',
        50 => 'ko',
        51 => 'ko-KR',
        52 => 'ms',
        53 => 'ms-MY',
        54 => 'nb',
        55 => 'nb-NO',
        56 => 'nl',
        57 => 'nl-BE',
        58 => 'nl-NL',
        59 => 'pl',
        60 => 'pl-PL',
        61 => 'pt',
        62 => 'pt-BR',
        63 => 'ro',
        64 => 'ro-RO',
        65 => 'ru',
        66 => 'ru-RU',
        67 => 'sv',
        68 => 'sv-SE',
        69 => 'th',
        70 => 'th-TH',
        71 => 'tr',
        72 => 'tr-TR',
        73 => 'vi',
        74 => 'vi-VN',
        75 => 'zh-Hans',
        76 => 'zh-Hans-CN',
        77 => 'zh-Hant',
        78 => 'zh-Hant-HK',
        79 => 'zh-Hant-TW',
      ),
      'name' => 'datatype',
      'rollup' => 3,
    ),
    'event-mouseenter' => 
    array (
      'path' => 'event/event-mouseenter-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-mouseenter',
      'ext' => false,
    ),
    'lang/datatype-date_es-AR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-AR.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-AR',
    ),
    'swfdetect' => 
    array (
      'path' => 'swfdetect/swfdetect-min.js',
      'ext' => false,
      'type' => 'js',
      'name' => 'swfdetect',
      'requires' => 
      array (
      ),
    ),
    'dd-delegate' => 
    array (
      'path' => 'dd/dd-delegate-min.js',
      'requires' => 
      array (
        0 => 'dd-drop-plugin',
        1 => 'event-mouseenter',
        2 => 'dd-drag',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-delegate',
      'ext' => false,
    ),
    'lang/datatype_ko' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ko.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ko',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ko',
    ),
    'lang/datatype_ca-ES' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ca-ES.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ca-ES',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ca-ES',
    ),
    'dataschema-base' => 
    array (
      'path' => 'dataschema/dataschema-base-min.js',
      'requires' => 
      array (
        0 => 'base',
      ),
      'type' => 'js',
      'pkg' => 'dataschema',
      'name' => 'dataschema-base',
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
    'lang/datatype-date_en-GB' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-GB.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-GB',
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
    'lang/datatype-date_ms' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ms.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ms',
    ),
    'lang/datatype_da-DK' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_da-DK.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_da-DK',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_da-DK',
    ),
    'yui' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'yui/yui-min.js',
      'supersedes' => 
      array (
        0 => 'yui-base',
        1 => 'rls',
        2 => 'features',
        3 => 'yui-later',
        4 => 'get',
        5 => 'yui-throttle',
        6 => 'intl-base',
        7 => 'yui-log',
      ),
      'provides' => 
      array (
        'intl-base' => true,
        'yui-throttle' => true,
        'yui-base' => true,
        'yui' => true,
        'yui-later' => true,
        'get' => true,
        'features' => true,
        'rls' => true,
        'yui-log' => true,
      ),
      'submodules' => 
      array (
        'rls' => 
        array (
          'path' => 'yui/rls-min.js',
          'requires' => 
          array (
            0 => 'features',
          ),
          'type' => 'js',
          'pkg' => 'yui',
          'name' => 'rls',
          'ext' => false,
          '_inspected' => true,
        ),
        'yui-base' => 
        array (
          'requires' => 
          array (
          ),
          'path' => 'yui/yui-base-min.js',
          'expanded_map' => 
          array (
          ),
          'type' => 'js',
          '_inspected' => true,
          'pkg' => 'yui',
          'expanded' => 
          array (
          ),
          'ext' => false,
          '_parsed' => false,
          'name' => 'yui-base',
        ),
        'features' => 
        array (
          'path' => 'yui/features-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'features',
          'requires' => 
          array (
          ),
          '_inspected' => true,
        ),
        'yui-later' => 
        array (
          'path' => 'yui/yui-later-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'yui-later',
          'requires' => 
          array (
          ),
          '_inspected' => true,
        ),
        'get' => 
        array (
          'path' => 'yui/get-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'get',
          'requires' => 
          array (
          ),
          '_inspected' => true,
        ),
        'yui-throttle' => 
        array (
          'path' => 'yui/yui-throttle-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'yui-throttle',
          'requires' => 
          array (
          ),
          '_inspected' => true,
        ),
        'intl-base' => 
        array (
          'path' => 'yui/intl-base-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'intl-base',
          'requires' => 
          array (
          ),
          '_inspected' => true,
        ),
        'yui-log' => 
        array (
          'path' => 'yui/yui-log-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'yui-log',
          'requires' => 
          array (
          ),
          '_inspected' => true,
        ),
      ),
      '_inspected' => true,
      'type' => 'js',
      'ext' => false,
      'name' => 'yui',
      'rollup' => 4,
    ),
    'lang/datatype-date_es-BO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-BO.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-BO',
    ),
    'lang/datatype-date_nb' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_nb.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_nb',
    ),
    'selector' => 
    array (
      'path' => 'dom/selector-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'type' => 'js',
      'pkg' => 'dom',
      'name' => 'selector',
      'ext' => false,
    ),
    'base-base' => 
    array (
      'path' => 'base/base-base-min.js',
      'requires' => 
      array (
        0 => 'attribute-base',
      ),
      'type' => 'js',
      'pkg' => 'base',
      'name' => 'base-base',
      'ext' => false,
    ),
    'yui-base' => 
    array (
      'requires' => 
      array (
      ),
      'expanded_map' => 
      array (
      ),
      'path' => 'yui/yui-base-min.js',
      'provides' => 
      array (
        'yui-base' => true,
      ),
      'type' => 'js',
      'pkg' => 'yui',
      '_inspected' => true,
      'expanded' => 
      array (
      ),
      'ext' => false,
      '_parsed' => false,
      'name' => 'yui-base',
    ),
    'lang/datatype-date_ms-MY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ms-MY.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ms-MY',
    ),
    'node-event-simulate' => 
    array (
      'path' => 'node/node-event-simulate-min.js',
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'event-simulate',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-event-simulate',
      'ext' => false,
    ),
    'dom-style-ie' => 
    array (
      'condition' => 
      array (
        'ua' => 'ie',
        'trigger' => 'dom-style',
      ),
      'path' => 'dom/dom-style-ie-min.js',
      'requires' => 
      array (
        0 => 'dom-style',
      ),
      'type' => 'js',
      'pkg' => 'dom',
      'name' => 'dom-style-ie',
      'ext' => false,
    ),
    'lang/datatype-date_nl' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_nl.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_nl',
    ),
    'compat' => 
    array (
      'path' => 'compat/compat-min.js',
      'requires' => 
      array (
        0 => 'event-base',
        1 => 'dump',
        2 => 'dom',
        3 => 'substitute',
      ),
      'type' => 'js',
      'name' => 'compat',
      'ext' => false,
    ),
    'align-plugin' => 
    array (
      'path' => 'node/align-plugin-min.js',
      'requires' => 
      array (
        0 => 'node-screen',
        1 => 'node-pluginhost',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'align-plugin',
      'ext' => false,
    ),
    'transition-native' => 
    array (
      'path' => 'transition/transition-native-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'transition',
      'name' => 'transition-native',
      'ext' => false,
    ),
    'lang/datatype-date_es-CL' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-CL.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-CL',
    ),
    'lang/datatype-date_es-CO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-CO.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-CO',
    ),
    'event-focus' => 
    array (
      'path' => 'event/event-focus-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-focus',
      'ext' => false,
    ),
    'intl-base' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'yui/intl-base-min.js',
      'provides' => 
      array (
        'intl-base' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'yui',
      'ext' => false,
      'name' => 'intl-base',
    ),
    'lang/datatype_tr-TR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_tr-TR.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_tr-TR',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_tr-TR',
    ),
    'datasource-function' => 
    array (
      'path' => 'datasource/datasource-function-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-function',
      'ext' => false,
    ),
    'lang/datatype_id-ID' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_id-ID.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_id-ID',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_id-ID',
    ),
    'range-slider' => 
    array (
      'path' => 'slider/range-slider-min.js',
      'requires' => 
      array (
        0 => 'slider-value-range',
        1 => 'clickable-rail',
        2 => 'slider-base',
      ),
      'type' => 'js',
      'pkg' => 'slider',
      'name' => 'range-slider',
      'ext' => false,
    ),
    'lang/datatype_ms' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ms.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ms',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ms',
    ),
    'jsonp-url' => 
    array (
      'path' => 'jsonp/jsonp-url-min.js',
      'requires' => 
      array (
        0 => 'jsonp',
      ),
      'type' => 'js',
      'pkg' => 'jsonp',
      'name' => 'jsonp-url',
      'ext' => false,
    ),
    'lang/datatype-date_en-IE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-IE.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-IE',
    ),
    'lang/datatype_zh-Hans-CN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_zh-Hans-CN.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_zh-Hans-CN',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_zh-Hans-CN',
    ),
    'node-event-delegate' => 
    array (
      'path' => 'node/node-event-delegate-min.js',
      'requires' => 
      array (
        0 => 'event-delegate',
        1 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-event-delegate',
      'ext' => false,
    ),
    'lang/datatype_nb' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_nb.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_nb',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_nb',
    ),
    'lang/datatype-date_en-IN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-IN.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-IN',
    ),
    'lang/datatype-date_hi-IN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_hi-IN.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_hi-IN',
    ),
    'skin-sam-tabview-base' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'tabview/assets/skins/sam/tabview-base.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-tabview-base',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_es-AR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-AR.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-AR',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-AR',
    ),
    'uploader' => 
    array (
      'path' => 'uploader/uploader-min.js',
      'requires' => 
      array (
        0 => 'base',
        1 => 'node',
        2 => 'swf',
        3 => 'event-custom',
      ),
      'type' => 'js',
      'name' => 'uploader',
      'ext' => false,
    ),
    'lang/datatype_nl' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_nl.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_nl',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_nl',
    ),
    'widget-locale' => 
    array (
      'path' => 'widget/widget-locale-min.js',
      'requires' => 
      array (
        0 => 'widget-base',
      ),
      'type' => 'js',
      'name' => 'widget-locale',
      'ext' => false,
    ),
    'history-deprecated' => 
    array (
      'path' => 'history-deprecated/history-deprecated-min.js',
      'requires' => 
      array (
        0 => 'node',
      ),
      'type' => 'js',
      'name' => 'history-deprecated',
      'ext' => false,
    ),
    'lang/datatype-date_pl' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_pl.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_pl',
    ),
    'lang/datatype_en-GB' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-GB.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-GB',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-GB',
    ),
    'lang/datatype-date_es-EC' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-EC.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-EC',
    ),
    'lang/datatype-date_pt' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_pt.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_pt',
    ),
    'dataschema-json' => 
    array (
      'path' => 'dataschema/dataschema-json-min.js',
      'requires' => 
      array (
        0 => 'dataschema-base',
        1 => 'json',
      ),
      'type' => 'js',
      'pkg' => 'dataschema',
      'name' => 'dataschema-json',
      'ext' => false,
    ),
    'lang/datatype_zh-Hant-HK' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_zh-Hant-HK.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_zh-Hant-HK',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_zh-Hant-HK',
    ),
    'dd-drop-plugin' => 
    array (
      'path' => 'dd/dd-drop-plugin-min.js',
      'requires' => 
      array (
        0 => 'dd-drop',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-drop-plugin',
      'ext' => false,
    ),
    'datasource-get' => 
    array (
      'path' => 'datasource/datasource-get-min.js',
      'requires' => 
      array (
        0 => 'get',
        1 => 'datasource-local',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-get',
      'ext' => false,
    ),
    'event-simulate' => 
    array (
      'path' => 'event-simulate/event-simulate-min.js',
      'requires' => 
      array (
        0 => 'event-base',
      ),
      'type' => 'js',
      'name' => 'event-simulate',
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
            1 => 'json',
          ),
          'type' => 'js',
          'pkg' => 'dataschema',
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
          'pkg' => 'dataschema',
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
          'pkg' => 'dataschema',
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
          'pkg' => 'dataschema',
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
          'pkg' => 'dataschema',
          'name' => 'dataschema-base',
          'ext' => false,
        ),
      ),
      'name' => 'dataschema',
      'requires' => 
      array (
      ),
    ),
    'editor-bidi' => 
    array (
      'path' => 'editor/editor-bidi-min.js',
      'requires' => 
      array (
        0 => 'editor-base',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'editor-bidi',
      'ext' => false,
    ),
    'lang/datatype-date_en-JO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-JO.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-JO',
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
    'lang/datatype_es-BO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-BO.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-BO',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-BO',
    ),
    'lang/datatype-date_de-AT' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_de-AT.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_de-AT',
    ),
    'lang/datatype-date_es-ES' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-ES.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-ES',
    ),
    'selector-native' => 
    array (
      'path' => 'dom/selector-native-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'type' => 'js',
      'pkg' => 'dom',
      'name' => 'selector-native',
      'ext' => false,
    ),
    'lang/datatype_ms-MY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ms-MY.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ms-MY',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ms-MY',
    ),
    'io-upload-iframe' => 
    array (
      'path' => 'io/io-upload-iframe-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'io',
      'name' => 'io-upload-iframe',
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
      'pkg' => 'dom',
      'name' => 'dom-base',
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
      'pkg' => 'datatype',
      'name' => 'datatype-number',
      'ext' => false,
    ),
    'lang/datatype_es-CL' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-CL.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-CL',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-CL',
    ),
    'lang/datatype_es-CO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-CO.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-CO',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-CO',
    ),
    'lang/datatype-date_sv-SE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_sv-SE.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_sv-SE',
    ),
    'lang/datatype_pl' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_pl.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_pl',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_pl',
    ),
    'event-synthetic' => 
    array (
      'path' => 'event/event-synthetic-min.js',
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'event-custom',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-synthetic',
      'ext' => false,
    ),
    'lang/datatype_pt' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_pt.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_pt',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_pt',
    ),
    'lang/datatype-date_ro' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ro.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ro',
    ),
    'lang/datatype-date_ro-RO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ro-RO.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ro-RO',
    ),
    'lang/datatype_en-IE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-IE.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-IE',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-IE',
    ),
    'lang/datatype-date_ru' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ru.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ru',
    ),
    'history-hash-ie' => 
    array (
      'condition' => 
      array (
        'trigger' => 'history-hash',
      ),
      'path' => 'history/history-hash-ie-min.js',
      'requires' => 
      array (
        0 => 'history-hash',
        1 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'history',
      'name' => 'history-hash-ie',
      'ext' => false,
    ),
    'json-stringify' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'expanded_map' => 
      array (
        'yui-base' => true,
      ),
      'path' => 'json/json-stringify-min.js',
      'provides' => 
      array (
        'json-stringify' => true,
      ),
      'type' => 'js',
      'pkg' => 'json',
      '_inspected' => true,
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
      'ext' => false,
      '_parsed' => false,
      'name' => 'json-stringify',
    ),
    'datatype-date' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'datatype/datatype-date-min.js',
      'supersedes' => 
      array (
        0 => 'datatype-date-format',
      ),
      'type' => 'js',
      'pkg' => 'datatype',
      'ext' => false,
      'lang' => 
      array (
        0 => 'ar',
        1 => 'ar-JO',
        2 => 'ca',
        3 => 'ca-ES',
        4 => 'da',
        5 => 'da-DK',
        6 => 'de',
        7 => 'de-AT',
        8 => 'de-DE',
        9 => 'el',
        10 => 'el-GR',
        11 => 'en',
        12 => 'en-AU',
        13 => 'en-CA',
        14 => 'en-GB',
        15 => 'en-IE',
        16 => 'en-IN',
        17 => 'en-JO',
        18 => 'en-MY',
        19 => 'en-NZ',
        20 => 'en-PH',
        21 => 'en-SG',
        22 => 'en-US',
        23 => 'es',
        24 => 'es-AR',
        25 => 'es-BO',
        26 => 'es-CL',
        27 => 'es-CO',
        28 => 'es-EC',
        29 => 'es-ES',
        30 => 'es-MX',
        31 => 'es-PE',
        32 => 'es-PY',
        33 => 'es-US',
        34 => 'es-UY',
        35 => 'es-VE',
        36 => 'fi',
        37 => 'fi-FI',
        38 => 'fr',
        39 => 'fr-BE',
        40 => 'fr-CA',
        41 => 'fr-FR',
        42 => 'hi',
        43 => 'hi-IN',
        44 => 'id',
        45 => 'id-ID',
        46 => 'it',
        47 => 'it-IT',
        48 => 'ja',
        49 => 'ja-JP',
        50 => 'ko',
        51 => 'ko-KR',
        52 => 'ms',
        53 => 'ms-MY',
        54 => 'nb',
        55 => 'nb-NO',
        56 => 'nl',
        57 => 'nl-BE',
        58 => 'nl-NL',
        59 => 'pl',
        60 => 'pl-PL',
        61 => 'pt',
        62 => 'pt-BR',
        63 => 'ro',
        64 => 'ro-RO',
        65 => 'ru',
        66 => 'ru-RU',
        67 => 'sv',
        68 => 'sv-SE',
        69 => 'th',
        70 => 'th-TH',
        71 => 'tr',
        72 => 'tr-TR',
        73 => 'vi',
        74 => 'vi-VN',
        75 => 'zh-Hans',
        76 => 'zh-Hans-CN',
        77 => 'zh-Hant',
        78 => 'zh-Hant-HK',
        79 => 'zh-Hant-TW',
      ),
      'name' => 'datatype-date',
    ),
    'shim-plugin' => 
    array (
      'path' => 'node/shim-plugin-min.js',
      'requires' => 
      array (
        0 => 'node-pluginhost',
        1 => 'node-style',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'shim-plugin',
      'ext' => false,
    ),
    'lang/datatype_en-IN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-IN.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-IN',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-IN',
    ),
    'scrollview-scrollbars' => 
    array (
      'path' => 'scrollview/scrollview-scrollbars-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'plugin',
      ),
      'type' => 'js',
      'pkg' => 'scrollview',
      'name' => 'scrollview-scrollbars',
      'ext' => false,
    ),
    'lang/datatype_hi-IN' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_hi-IN.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_hi-IN',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_hi-IN',
    ),
    'io-xdr' => 
    array (
      'path' => 'io/io-xdr-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'datatype-xml',
      ),
      'type' => 'js',
      'pkg' => 'io',
      'name' => 'io-xdr',
      'ext' => false,
    ),
    'arraylist' => 
    array (
      'path' => 'collection/arraylist-min.js',
      'type' => 'js',
      'ext' => false,
      'pkg' => 'collection',
      'name' => 'arraylist',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_ar-JO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ar-JO.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ar-JO',
    ),
    'cssreset-context' => 
    array (
      'path' => 'cssreset/reset-context-min.css',
      'ext' => false,
      'type' => 'css',
      'name' => 'cssreset-context',
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
      'pkg' => 'dom',
      'name' => 'dom-screen',
      'ext' => false,
    ),
    'lang/datatype-date_fr-BE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_fr-BE.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_fr-BE',
    ),
    'lang/datatype-date_nl-NL' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_nl-NL.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_nl-NL',
    ),
    'array-extras' => 
    array (
      'path' => 'collection/array-extras-min.js',
      'type' => 'js',
      'ext' => false,
      'pkg' => 'collection',
      'name' => 'array-extras',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_de-DE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_de-DE.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_de-DE',
    ),
    'lang/datatype_es-EC' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-EC.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-EC',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-EC',
    ),
    'jsonp' => 
    array (
      'path' => 'jsonp/jsonp-min.js',
      'requires' => 
      array (
        0 => 'get',
        1 => 'oop',
      ),
      'type' => 'js',
      'name' => 'jsonp',
      'ext' => false,
      'plugins' => 
      array (
        'jsonp-url' => 
        array (
          'path' => 'jsonp/jsonp-url-min.js',
          'requires' => 
          array (
            0 => 'jsonp',
          ),
          'type' => 'js',
          'pkg' => 'jsonp',
          'name' => 'jsonp-url',
          'ext' => false,
        ),
      ),
    ),
    'lang/datatype-date_sv' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_sv.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_sv',
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
    'lang/datatype-date_ru-RU' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ru-RU.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ru-RU',
    ),
    'tabview-base' => 
    array (
      'path' => 'tabview/tabview-base-min.js',
      'requires' => 
      array (
        0 => 'node-event-delegate',
        1 => 'classnamemanager',
        2 => 'skin-sam-tabview',
      ),
      'type' => 'js',
      'pkg' => 'tabview',
      'name' => 'tabview-base',
      'ext' => false,
    ),
    'cssgrids-context-deprecated' => 
    array (
      'path' => 'cssgrids-deprecated/grids-context-min.css',
      'requires' => 
      array (
        0 => 'cssfonts-context',
      ),
      'type' => 'css',
      'optional' => 
      array (
        0 => 'cssreset-context',
      ),
      'name' => 'cssgrids-context-deprecated',
      'ext' => false,
    ),
    'lang/datatype_en-JO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-JO.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-JO',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-JO',
    ),
    'widget-htmlparser' => 
    array (
      'path' => 'widget/widget-htmlparser-min.js',
      'requires' => 
      array (
        0 => 'widget-base',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-htmlparser',
      'ext' => false,
    ),
    'querystring-stringify' => 
    array (
      'path' => 'querystring/querystring-stringify-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'pkg' => 'querystring',
      'name' => 'querystring-stringify',
      'ext' => false,
    ),
    'lang/datatype_de-AT' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_de-AT.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_de-AT',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_de-AT',
    ),
    'lang/datatype_es-ES' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_es-ES.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_es-ES',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_es-ES',
    ),
    'lang/datatype-date_fr-CA' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_fr-CA.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_fr-CA',
    ),
    'dd-drop' => 
    array (
      'path' => 'dd/dd-drop-min.js',
      'requires' => 
      array (
        0 => 'dd-ddm-drop',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-drop',
      'ext' => false,
    ),
    'lang/datatype-date_th' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_th.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_th',
    ),
    'lang/datatype-date_en-MY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-MY.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-MY',
    ),
    'base' => 
    array (
      'supersedes' => 
      array (
        0 => 'base-build',
        1 => 'base-pluginhost',
        2 => 'base-base',
      ),
      'path' => 'base/base-min.js',
      'rollup' => 3,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'base-pluginhost' => 
        array (
          'path' => 'base/base-pluginhost-min.js',
          'requires' => 
          array (
            0 => 'base-base',
            1 => 'pluginhost',
          ),
          'type' => 'js',
          'pkg' => 'base',
          'name' => 'base-pluginhost',
          'ext' => false,
        ),
        'base-base' => 
        array (
          'path' => 'base/base-base-min.js',
          'requires' => 
          array (
            0 => 'attribute-base',
          ),
          'type' => 'js',
          'pkg' => 'base',
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
          'pkg' => 'base',
          'name' => 'base-build',
          'ext' => false,
        ),
      ),
      'name' => 'base',
      'requires' => 
      array (
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
      'pkg' => 'anim',
      'name' => 'anim-scroll',
      'ext' => false,
    ),
    'attribute-complex' => 
    array (
      'path' => 'attribute/attribute-complex-min.js',
      'requires' => 
      array (
        0 => 'attribute-base',
      ),
      'type' => 'js',
      'pkg' => 'attribute',
      'name' => 'attribute-complex',
      'ext' => false,
    ),
    'lang/datatype_ro' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ro.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ro',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ro',
    ),
    'lang/datatype_ru' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ru.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ru',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ru',
    ),
    'yui-throttle' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'yui/yui-throttle-min.js',
      'provides' => 
      array (
        'yui-throttle' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'yui',
      'ext' => false,
      'name' => 'yui-throttle',
    ),
    'lang/datatype-date_tr' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_tr.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_tr',
    ),
    'event-valuechange' => 
    array (
      'path' => 'event-valuechange/event-valuechange-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
        1 => 'event-focus',
      ),
      'type' => 'js',
      'name' => 'event-valuechange',
      'ext' => false,
    ),
    'pluginhost' => 
    array (
      'path' => 'pluginhost/pluginhost-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'pluginhost',
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
      'pkg' => 'dom',
      'name' => 'dom-style',
      'ext' => false,
    ),
    'cssgrids' => 
    array (
      'path' => 'cssgrids/grids-min.css',
      'ext' => false,
      'type' => 'css',
      'optional' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
      ),
      'name' => 'cssgrids',
      'requires' => 
      array (
      ),
    ),
    'io-form' => 
    array (
      'path' => 'io/io-form-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'node-base',
        2 => 'node-style',
      ),
      'type' => 'js',
      'pkg' => 'io',
      'name' => 'io-form',
      'ext' => false,
    ),
    'skin-sam-tabview-plugin' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'tabview/assets/skins/sam/tabview-plugin.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-tabview-plugin',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'node' => 
    array (
      'requires' => 
      array (
        0 => 'dom',
        1 => 'event-base',
      ),
      'path' => 'node/node-min.js',
      'supersedes' => 
      array (
        0 => 'node-screen',
        1 => 'node-style',
        2 => 'node-base',
        3 => 'node-pluginhost',
        4 => 'node-event-delegate',
      ),
      'submodules' => 
      array (
        'node-screen' => 
        array (
          'path' => 'node/node-screen-min.js',
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'dom-screen',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'node-screen',
          'ext' => false,
        ),
        'node-base' => 
        array (
          'path' => 'node/node-base-min.js',
          'requires' => 
          array (
            0 => 'selector-css2',
            1 => 'dom-base',
            2 => 'event-base',
          ),
          'type' => 'js',
          'pkg' => 'node',
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
          'pkg' => 'node',
          'name' => 'node-style',
          'ext' => false,
        ),
        'node-pluginhost' => 
        array (
          'path' => 'node/node-pluginhost-min.js',
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'pluginhost',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'node-pluginhost',
          'ext' => false,
        ),
        'node-event-delegate' => 
        array (
          'path' => 'node/node-event-delegate-min.js',
          'requires' => 
          array (
            0 => 'event-delegate',
            1 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'node-event-delegate',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'plugins' => 
      array (
        'transition-native' => 
        array (
          'path' => 'node/transition-native-min.js',
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'transition-native',
          'ext' => false,
        ),
        'node-event-simulate' => 
        array (
          'path' => 'node/node-event-simulate-min.js',
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'event-simulate',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'node-event-simulate',
          'ext' => false,
        ),
        'align-plugin' => 
        array (
          'path' => 'node/align-plugin-min.js',
          'requires' => 
          array (
            0 => 'node-screen',
            1 => 'node-pluginhost',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'align-plugin',
          'ext' => false,
        ),
        'transition' => 
        array (
          'path' => 'node/transition-min.js',
          'requires' => 
          array (
            0 => 'transition-native',
            1 => 'node-style',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'transition',
          'ext' => false,
        ),
        'shim-plugin' => 
        array (
          'path' => 'node/shim-plugin-min.js',
          'requires' => 
          array (
            0 => 'node-pluginhost',
            1 => 'node-style',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'shim-plugin',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'node',
      'rollup' => 4,
    ),
    'datasource-xmlschema' => 
    array (
      'path' => 'datasource/datasource-xmlschema-min.js',
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'datasource-local',
        2 => 'dataschema-xml',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-xmlschema',
      'ext' => false,
    ),
    'lang/datatype_sv-SE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_sv-SE.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_sv-SE',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_sv-SE',
    ),
    'querystring-stringify-simple' => 
    array (
      'path' => 'querystring/querystring-stringify-simple-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'querystring-stringify-simple',
      'ext' => false,
    ),
    'lang/datatype-date_en-NZ' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-NZ.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-NZ',
    ),
    'event-flick' => 
    array (
      'path' => 'event-gestures/event-flick-min.js',
      'requires' => 
      array (
        0 => 'event-touch',
        1 => 'event-synthetic',
        2 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event-gestures',
      'name' => 'event-flick',
      'ext' => false,
    ),
    'lang/datatype_ro-RO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ro-RO.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ro-RO',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ro-RO',
    ),
    'lang/datatype_sv' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_sv.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_sv',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_sv',
    ),
    'lang/datatype' => 
    array (
      'path' => 'datatype/lang/datatype.js',
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype',
    ),
    'anim' => 
    array (
      'supersedes' => 
      array (
        0 => 'anim-node-plugin',
        1 => 'anim-scroll',
        2 => 'anim-color',
        3 => 'anim-base',
        4 => 'anim-curve',
        5 => 'anim-easing',
        6 => 'anim-xy',
      ),
      'path' => 'anim/anim-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
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
          'pkg' => 'anim',
          'name' => 'anim-color',
          'ext' => false,
        ),
        'anim-node-plugin' => 
        array (
          'path' => 'anim/anim-node-plugin-min.js',
          'requires' => 
          array (
            0 => 'node-pluginhost',
            1 => 'anim-base',
          ),
          'type' => 'js',
          'pkg' => 'anim',
          'name' => 'anim-node-plugin',
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
          'pkg' => 'anim',
          'name' => 'anim-scroll',
          'ext' => false,
        ),
        'anim-base' => 
        array (
          'path' => 'anim/anim-base-min.js',
          'requires' => 
          array (
            0 => 'base-base',
            1 => 'node-style',
          ),
          'type' => 'js',
          'pkg' => 'anim',
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
          'pkg' => 'anim',
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
          'pkg' => 'anim',
          'name' => 'anim-easing',
          'ext' => false,
        ),
        'anim-xy' => 
        array (
          'path' => 'anim/anim-xy-min.js',
          'requires' => 
          array (
            0 => 'node-screen',
            1 => 'anim-base',
          ),
          'type' => 'js',
          'pkg' => 'anim',
          'name' => 'anim-xy',
          'ext' => false,
        ),
      ),
      'name' => 'anim',
      'requires' => 
      array (
      ),
    ),
    'slider-value-range' => 
    array (
      'path' => 'slider/slider-value-range-min.js',
      'requires' => 
      array (
        0 => 'slider-base',
      ),
      'type' => 'js',
      'pkg' => 'slider',
      'name' => 'slider-value-range',
      'ext' => false,
    ),
    'cache' => 
    array (
      'supersedes' => 
      array (
        0 => 'cache-base',
        1 => 'cache-offline',
      ),
      'path' => 'cache/cache-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'cache-base' => 
        array (
          'path' => 'cache/cache-base-min.js',
          'requires' => 
          array (
            0 => 'base',
          ),
          'type' => 'js',
          'pkg' => 'cache',
          'name' => 'cache-base',
          'ext' => false,
        ),
        'cache-offline' => 
        array (
          'path' => 'cache/cache-offline-min.js',
          'requires' => 
          array (
            0 => 'cache-base',
            1 => 'json',
          ),
          'type' => 'js',
          'pkg' => 'cache',
          'name' => 'cache-offline',
          'ext' => false,
        ),
      ),
      'name' => 'cache',
      'requires' => 
      array (
      ),
    ),
    'lang/console' => 
    array (
      'path' => 'console/lang/console.js',
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/console',
    ),
    'lang/datatype-date_el-GR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_el-GR.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_el-GR',
    ),
    'tabview' => 
    array (
      'path' => 'tabview/tabview-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget-parent',
        1 => 'tabview-base',
        2 => 'widget-child',
        3 => 'widget',
        4 => 'node-focusmanager',
        5 => 'node-pluginhost',
      ),
      'type' => 'js',
      'name' => 'tabview',
      'ext' => false,
      'plugins' => 
      array (
        'tabview-base' => 
        array (
          'path' => 'tabview/tabview-base-min.js',
          'requires' => 
          array (
            0 => 'node-event-delegate',
            1 => 'classnamemanager',
            2 => 'skin-sam-tabview',
          ),
          'type' => 'js',
          'pkg' => 'tabview',
          'name' => 'tabview-base',
          'ext' => false,
        ),
        'tabview-plugin' => 
        array (
          'path' => 'tabview/tabview-plugin-min.js',
          'requires' => 
          array (
            0 => 'tabview-base',
          ),
          'type' => 'js',
          'pkg' => 'tabview',
          'name' => 'tabview-plugin',
          'ext' => false,
        ),
      ),
    ),
    'lang/datatype_th' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_th.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_th',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_th',
    ),
    'lang/datatype_ar-JO' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ar-JO.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ar-JO',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ar-JO',
    ),
    'overlay' => 
    array (
      'path' => 'overlay/overlay-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget-stack',
        1 => 'widget-position',
        2 => 'widget',
        3 => 'widget-stdmod',
        4 => 'widget-position-constrain',
        5 => 'widget-position-align',
      ),
      'type' => 'js',
      'name' => 'overlay',
      'ext' => false,
    ),
    'lang/datatype_zh-Hans' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_zh-Hans.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_zh-Hans',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_zh-Hans',
    ),
    'lang/datatype_zh-Hant' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_zh-Hant.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_zh-Hant',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_zh-Hant',
    ),
    'lang/datatype-date_vi' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_vi.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_vi',
    ),
    'lang/datatype_fr-BE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_fr-BE.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_fr-BE',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_fr-BE',
    ),
    'loader' => 
    array (
      'requires' => 
      array (
        0 => 'get',
      ),
      'path' => 'loader/loader-min.js',
      'supersedes' => 
      array (
        0 => 'loader-rollup',
        1 => 'loader-yui3',
        2 => 'loader-base',
      ),
      'provides' => 
      array (
        'loader-yui3' => true,
        'loader-base' => true,
        'loader-rollup' => true,
        'loader' => true,
      ),
      'submodules' => 
      array (
        'loader-rollup' => 
        array (
          'path' => 'loader/loader-rollup-min.js',
          'requires' => 
          array (
            0 => 'loader-base',
          ),
          'type' => 'js',
          'pkg' => 'loader',
          'name' => 'loader-rollup',
          'ext' => false,
          '_inspected' => true,
        ),
        'loader-yui3' => 
        array (
          'path' => 'loader/loader-yui3-min.js',
          'requires' => 
          array (
            0 => 'loader-base',
          ),
          'type' => 'js',
          'pkg' => 'loader',
          'name' => 'loader-yui3',
          'ext' => false,
          '_inspected' => true,
        ),
        'loader-base' => 
        array (
          'path' => 'loader/loader-base-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'loader',
          'name' => 'loader-base',
          'requires' => 
          array (
          ),
          '_inspected' => true,
        ),
      ),
      '_inspected' => true,
      'type' => 'js',
      'ext' => false,
      'name' => 'loader',
      'rollup' => 3,
    ),
    'lang/datatype_nl-NL' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_nl-NL.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_nl-NL',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_nl-NL',
    ),
    'yui-later' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'yui/yui-later-min.js',
      'provides' => 
      array (
        'yui-later' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'yui',
      'ext' => false,
      'name' => 'yui-later',
    ),
    'lang/datatype_tr' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_tr.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_tr',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_tr',
    ),
    'lang/datatype-date_zh-Hant-TW' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_zh-Hant-TW.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_zh-Hant-TW',
    ),
    'editor-lists' => 
    array (
      'path' => 'editor/editor-lists-min.js',
      'requires' => 
      array (
        0 => 'editor-base',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'editor-lists',
      'ext' => false,
    ),
    'lang/datatype_de-DE' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_de-DE.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_de-DE',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_de-DE',
    ),
    'lang/console_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'console/lang/console_en.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/console_en',
    ),
    'dataschema-text' => 
    array (
      'path' => 'dataschema/dataschema-text-min.js',
      'requires' => 
      array (
        0 => 'dataschema-base',
      ),
      'type' => 'js',
      'pkg' => 'dataschema',
      'name' => 'dataschema-text',
      'ext' => false,
    ),
    'lang/datatype-date_en-PH' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-PH.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-PH',
    ),
    'substitute' => 
    array (
      'path' => 'substitute/substitute-min.js',
      'ext' => false,
      'type' => 'js',
      'optional' => 
      array (
        0 => 'dump',
      ),
      'name' => 'substitute',
      'requires' => 
      array (
      ),
    ),
    'event-mousewheel' => 
    array (
      'path' => 'event/event-mousewheel-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-mousewheel',
      'ext' => false,
    ),
    'lang/console_es' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'console/lang/console_es.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/console_es',
    ),
    'lang/datatype_ru-RU' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_ru-RU.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_ru-RU',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_ru-RU',
    ),
    'querystring-parse' => 
    array (
      'path' => 'querystring/querystring-parse-min.js',
      'requires' => 
      array (
        0 => 'array-extras',
        1 => 'yui-base',
      ),
      'type' => 'js',
      'pkg' => 'querystring',
      'name' => 'querystring-parse',
      'ext' => false,
    ),
    'skin-sam-widget-parent' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-parent.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-parent',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'sortable' => 
    array (
      'path' => 'sortable/sortable-min.js',
      'requires' => 
      array (
        0 => 'dd-drop-plugin',
        1 => 'dd-delegate',
        2 => 'dd-proxy',
      ),
      'type' => 'js',
      'name' => 'sortable',
      'ext' => false,
      'plugins' => 
      array (
        'sortable-scroll' => 
        array (
          'path' => 'sortable/sortable-scroll-min.js',
          'requires' => 
          array (
            0 => 'dd-scroll',
          ),
          'type' => 'js',
          'pkg' => 'sortable',
          'name' => 'sortable-scroll',
          'ext' => false,
        ),
      ),
    ),
    'scrollview-base' => 
    array (
      'path' => 'scrollview/scrollview-base-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'event-gestures',
        2 => 'transition',
      ),
      'type' => 'js',
      'pkg' => 'scrollview',
      'name' => 'scrollview-base',
      'ext' => false,
    ),
    'skin-sam-widget-stdmod' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-stdmod.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stdmod',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_fr-CA' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_fr-CA.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_fr-CA',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_fr-CA',
    ),
    'lang/datatype_en-MY' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-MY.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-MY',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-MY',
    ),
    'lang/datatype-date' => 
    array (
      'path' => 'datatype/lang/datatype-date.js',
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date',
    ),
    'json' => 
    array (
      'requires' => 
      array (
      ),
      'expanded_map' => 
      array (
        'json-parse' => true,
        'yui-base' => true,
        'json-stringify' => true,
      ),
      'path' => 'json/json-min.js',
      'supersedes' => 
      array (
        0 => 'json-parse',
        1 => 'json-stringify',
      ),
      'provides' => 
      array (
        'json-parse' => true,
        'json' => true,
        'json-stringify' => true,
      ),
      'submodules' => 
      array (
        'json-parse' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'json/json-parse-min.js',
          'expanded_map' => 
          array (
            'yui-base' => true,
          ),
          'type' => 'js',
          '_inspected' => true,
          'pkg' => 'json',
          'expanded' => 
          array (
            0 => 'yui-base',
          ),
          'ext' => false,
          '_parsed' => false,
          'name' => 'json-parse',
        ),
        'json-stringify' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'json/json-stringify-min.js',
          'expanded_map' => 
          array (
            'yui-base' => true,
          ),
          'type' => 'js',
          '_inspected' => true,
          'pkg' => 'json',
          'expanded' => 
          array (
            0 => 'yui-base',
          ),
          'ext' => false,
          '_parsed' => false,
          'name' => 'json-stringify',
        ),
      ),
      '_inspected' => true,
      'type' => 'js',
      'expanded' => 
      array (
        0 => 'json-parse',
        1 => 'yui-base',
        2 => 'json-stringify',
      ),
      'ext' => false,
      '_parsed' => false,
      'name' => 'json',
      'rollup' => 2,
    ),
    'loader-yui3' => 
    array (
      'requires' => 
      array (
        0 => 'loader-base',
      ),
      'path' => 'loader/loader-yui3-min.js',
      'provides' => 
      array (
        'loader-yui3' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'loader',
      'ext' => false,
      'name' => 'loader-yui3',
    ),
    'lang/datatype-date_fr-FR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_fr-FR.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_fr-FR',
    ),
    'anim-xy' => 
    array (
      'path' => 'anim/anim-xy-min.js',
      'requires' => 
      array (
        0 => 'node-screen',
        1 => 'anim-base',
      ),
      'type' => 'js',
      'pkg' => 'anim',
      'name' => 'anim-xy',
      'ext' => false,
    ),
    'dd-gestures' => 
    array (
      'condition' => 
      array (
        'trigger' => 'dd-drag',
      ),
      'path' => 'dd/dd-gestures-min.js',
      'requires' => 
      array (
        0 => 'event-move',
        1 => 'dd-drag',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-gestures',
      'ext' => false,
    ),
    'exec-command' => 
    array (
      'path' => 'editor/exec-command-min.js',
      'requires' => 
      array (
        0 => 'frame',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'exec-command',
      'ext' => false,
    ),
    'widget-position-align' => 
    array (
      'path' => 'widget/widget-position-align-min.js',
      'requires' => 
      array (
        0 => 'widget-position',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-position-align',
      'ext' => false,
    ),
    'io-base' => 
    array (
      'path' => 'io/io-base-min.js',
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'querystring-stringify-simple',
      ),
      'pkg' => 'io',
      'name' => 'io-base',
      'ext' => false,
    ),
    'lang/datatype-date_it-IT' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_it-IT.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_it-IT',
    ),
    'anim-curve' => 
    array (
      'path' => 'anim/anim-curve-min.js',
      'requires' => 
      array (
        0 => 'anim-xy',
      ),
      'type' => 'js',
      'pkg' => 'anim',
      'name' => 'anim-curve',
      'ext' => false,
    ),
    'array-invoke' => 
    array (
      'path' => 'collection/array-invoke-min.js',
      'type' => 'js',
      'ext' => false,
      'pkg' => 'collection',
      'name' => 'array-invoke',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_vi' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_vi.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_vi',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_vi',
    ),
    'datatype-xml' => 
    array (
      'path' => 'datatype/datatype-xml-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'pkg' => 'datatype',
      'name' => 'datatype-xml',
      'ext' => false,
    ),
    'lang/datatype_en-NZ' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-NZ.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-NZ',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-NZ',
    ),
    'datasource-cache' => 
    array (
      'path' => 'datasource/datasource-cache-min.js',
      'requires' => 
      array (
        0 => 'cache-base',
        1 => 'datasource-local',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-cache',
      'ext' => false,
    ),
    'widget-parent' => 
    array (
      'path' => 'widget/widget-parent-min.js',
      'requires' => 
      array (
        0 => 'widget',
        1 => 'base-build',
        2 => 'arraylist',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-parent',
      'ext' => false,
    ),
    'lang/datatype-date_ko-KR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ko-KR.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ko-KR',
    ),
    'history-base' => 
    array (
      'after' => 
      array (
        0 => 'history-deprecated',
      ),
      'path' => 'history/history-base-min.js',
      'requires' => 
      array (
        0 => 'event-custom-complex',
      ),
      'type' => 'js',
      'pkg' => 'history',
      'name' => 'history-base',
      'ext' => false,
    ),
    'test' => 
    array (
      'path' => 'test/test-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'event-simulate',
        1 => 'node',
        2 => 'json',
        3 => 'substitute',
      ),
      'type' => 'js',
      'name' => 'test',
      'ext' => false,
    ),
    'datatype-date-format' => 
    array (
      'path' => 'datatype/datatype-date-format-min.js',
      'ext' => false,
      'type' => 'js',
      'name' => 'datatype-date-format',
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
      'pkg' => 'dd',
      'name' => 'dd-ddm-drop',
      'ext' => false,
    ),
    'editor-para' => 
    array (
      'path' => 'editor/editor-para-min.js',
      'requires' => 
      array (
        0 => 'editor-base',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'editor-para',
      'ext' => false,
    ),
    'widget-stdmod' => 
    array (
      'path' => 'widget/widget-stdmod-min.js',
      'requires' => 
      array (
        0 => 'widget',
        1 => 'base-build',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-stdmod',
      'ext' => false,
    ),
    'arraylist-filter' => 
    array (
      'path' => 'collection/arraylist-filter-min.js',
      'requires' => 
      array (
        0 => 'arraylist',
      ),
      'type' => 'js',
      'pkg' => 'collection',
      'name' => 'arraylist-filter',
      'ext' => false,
    ),
    'plugin' => 
    array (
      'path' => 'plugin/plugin-min.js',
      'requires' => 
      array (
        0 => 'base-base',
      ),
      'type' => 'js',
      'name' => 'plugin',
      'ext' => false,
    ),
    'lang/datatype-date_pl-PL' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_pl-PL.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_pl-PL',
    ),
    'lang/datatype_el-GR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_el-GR.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_el-GR',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_el-GR',
    ),
    'node-pluginhost' => 
    array (
      'path' => 'node/node-pluginhost-min.js',
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'pluginhost',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-pluginhost',
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
        3 => 'selector-css2',
        4 => 'dom-style',
        5 => 'selector',
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
          'pkg' => 'dom',
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
          'pkg' => 'dom',
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
          'pkg' => 'dom',
          'name' => 'dom-base',
          'ext' => false,
        ),
        'selector-css2' => 
        array (
          'path' => 'dom/selector-css2-min.js',
          'requires' => 
          array (
            0 => 'selector-native',
          ),
          'type' => 'js',
          'pkg' => 'dom',
          'name' => 'selector-css2',
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
          'pkg' => 'dom',
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
          'pkg' => 'dom',
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
            0 => 'selector-css2',
          ),
          'type' => 'js',
          'pkg' => 'dom',
          'name' => 'selector-css3',
          'ext' => false,
        ),
        'dom-style-ie' => 
        array (
          'condition' => 
          array (
            'ua' => 'ie',
            'trigger' => 'dom-style',
          ),
          'path' => 'dom/dom-style-ie-min.js',
          'requires' => 
          array (
            0 => 'dom-style',
          ),
          'type' => 'js',
          'pkg' => 'dom',
          'name' => 'dom-style-ie',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'dom',
      'rollup' => 4,
    ),
    'loader-base' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'loader/loader-base-min.js',
      'provides' => 
      array (
        'loader-base' => true,
      ),
      'type' => 'js',
      '_inspected' => true,
      'pkg' => 'loader',
      'ext' => false,
      'name' => 'loader-base',
    ),
    'lang/datatype-date_es-MX' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_es-MX.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_es-MX',
    ),
    'slider-base' => 
    array (
      'path' => 'slider/slider-base-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'substitute',
        2 => 'dd-constrain',
      ),
      'type' => 'js',
      'pkg' => 'slider',
      'name' => 'slider-base',
      'ext' => false,
    ),
    'history-hash' => 
    array (
      'after' => 
      array (
        0 => 'history-html5',
      ),
      'path' => 'history/history-hash-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
        1 => 'history-base',
        2 => 'yui-later',
      ),
      'type' => 'js',
      'pkg' => 'history',
      'name' => 'history-hash',
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
      'pkg' => 'dd',
      'name' => 'dd-constrain',
      'ext' => false,
    ),
    'base-pluginhost' => 
    array (
      'path' => 'base/base-pluginhost-min.js',
      'requires' => 
      array (
        0 => 'base-base',
        1 => 'pluginhost',
      ),
      'type' => 'js',
      'pkg' => 'base',
      'name' => 'base-pluginhost',
      'ext' => false,
    ),
    'lang/datatype-date_en-SG' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_en-SG.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_en-SG',
    ),
    'lang/datatype_en-PH' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_en-PH.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_en-PH',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_en-PH',
    ),
    'cssfonts-context' => 
    array (
      'path' => 'cssfonts/fonts-context-min.css',
      'ext' => false,
      'type' => 'css',
      'name' => 'cssfonts-context',
      'requires' => 
      array (
      ),
    ),
    'intl' => 
    array (
      'path' => 'intl/intl-min.js',
      'requires' => 
      array (
        0 => 'intl-base',
        1 => 'event-custom',
      ),
      'type' => 'js',
      'name' => 'intl',
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
      'pkg' => 'dd',
      'name' => 'dd-plugin',
      'ext' => false,
    ),
    'skin-sam-widget-stack' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-stack.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stack',
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_ar' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_ar.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_ar',
    ),
    'event-resize' => 
    array (
      'path' => 'event/event-resize-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-resize',
      'ext' => false,
    ),
    'tabview-plugin' => 
    array (
      'path' => 'tabview/tabview-plugin-min.js',
      'requires' => 
      array (
        0 => 'tabview-base',
      ),
      'type' => 'js',
      'pkg' => 'tabview',
      'name' => 'tabview-plugin',
      'ext' => false,
    ),
    'lang/datatype-date_th-TH' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype-date_th-TH.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype-date_th-TH',
    ),
    'frame' => 
    array (
      'path' => 'editor/frame-min.js',
      'requires' => 
      array (
        0 => 'base',
        1 => 'node',
        2 => 'selector-css3',
        3 => 'substitute',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'frame',
      'ext' => false,
    ),
    'lang/datatype_fr-FR' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype_fr-FR.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date_fr-FR',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype_fr-FR',
    ),
    'node-screen' => 
    array (
      'path' => 'node/node-screen-min.js',
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'dom-screen',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-screen',
      'ext' => false,
    ),
  ),
); ?>