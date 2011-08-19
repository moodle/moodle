<?php $GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/3.3.0/build/',
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
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'console/assets/skins/sam/console-filters.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-console-filters',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
    'autocomplete-plugin' => 
    array (
      'path' => 'autocomplete/autocomplete-plugin-min.js',
      'requires' => 
      array (
        0 => 'node-pluginhost',
        1 => 'autocomplete-list',
      ),
      'type' => 'js',
      'pkg' => 'autocomplete-list',
      'name' => 'autocomplete-plugin',
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
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'slider/assets/skins/sam/slider-base.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-slider-base',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
    'arraysort' => 
    array (
      'path' => 'arraysort/arraysort-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'name' => 'arraysort',
      'ext' => false,
    ),
    'console-filters' => 
    array (
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'console',
      ),
      'path' => 'console/console-filters-min.js',
      'type' => 'js',
      'pkg' => 'console',
      'skinnable' => true,
      'ext' => false,
      'name' => 'console-filters',
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
    'cache-plugin' => 
    array (
      'path' => 'cache/cache-plugin-min.js',
      'requires' => 
      array (
        0 => 'cache-base',
        1 => 'plugin',
      ),
      'type' => 'js',
      'pkg' => 'cache',
      'name' => 'cache-plugin',
      'ext' => false,
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
        0 => 'oop',
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
      'type' => 'css',
      'ext' => false,
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
    'lang/datatable-sort_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatable/lang/datatable-sort_en.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatable-sort_en',
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
      'requires' => 
      array (
        0 => 'widget',
        1 => 'yui-log',
        2 => 'substitute',
      ),
      'path' => 'console/console-min.js',
      'type' => 'js',
      'skinnable' => true,
      'plugins' => 
      array (
        'console-filters' => 
        array (
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'console',
          ),
          'path' => 'console/console-filters-min.js',
          'type' => 'js',
          'pkg' => 'console',
          'skinnable' => true,
          'ext' => false,
          'name' => 'console-filters',
        ),
      ),
      'ext' => false,
      'lang' => 
      array (
        0 => 'en',
        1 => 'es',
      ),
      'name' => 'console',
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
    'text' => 
    array (
      'supersedes' => 
      array (
        0 => 'text-wordbreak',
        1 => 'text-data-wordbreak',
        2 => 'text-accentfold',
        3 => 'text-data-accentfold',
      ),
      'path' => 'text/text-min.js',
      'rollup' => 3,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'text-wordbreak' => 
        array (
          'path' => 'text/text-wordbreak-min.js',
          'requires' => 
          array (
            0 => 'text-data-wordbreak',
            1 => 'array-extras',
          ),
          'type' => 'js',
          'pkg' => 'text',
          'name' => 'text-wordbreak',
          'ext' => false,
        ),
        'text-data-wordbreak' => 
        array (
          'path' => 'text/text-data-wordbreak-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'text',
          'name' => 'text-data-wordbreak',
          'requires' => 
          array (
          ),
        ),
        'text-accentfold' => 
        array (
          'path' => 'text/text-accentfold-min.js',
          'requires' => 
          array (
            0 => 'array-extras',
            1 => 'text-data-accentfold',
          ),
          'type' => 'js',
          'pkg' => 'text',
          'name' => 'text-accentfold',
          'ext' => false,
        ),
        'text-data-accentfold' => 
        array (
          'path' => 'text/text-data-accentfold-min.js',
          'type' => 'js',
          'ext' => false,
          'pkg' => 'text',
          'name' => 'text-data-accentfold',
          'requires' => 
          array (
          ),
        ),
      ),
      'name' => 'text',
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
    'text-wordbreak' => 
    array (
      'path' => 'text/text-wordbreak-min.js',
      'requires' => 
      array (
        0 => 'text-data-wordbreak',
        1 => 'array-extras',
      ),
      'type' => 'js',
      'pkg' => 'text',
      'name' => 'text-wordbreak',
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
      'requires' => 
      array (
        0 => 'widget',
        1 => 'base-build',
      ),
      'path' => 'widget/widget-stack-min.js',
      'type' => 'js',
      'pkg' => 'widget',
      'skinnable' => true,
      'ext' => false,
      'name' => 'widget-stack',
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
          'requires' => 
          array (
            0 => 'history-base',
            1 => 'event-base',
            2 => 'node-base',
          ),
          'path' => 'history/history-html5-min.js',
          'optional' => 
          array (
            0 => 'json',
          ),
          'pkg' => 'history',
          'type' => 'js',
          'ext' => false,
          'name' => 'history-html5',
        ),
        'history-hash' => 
        array (
          'requires' => 
          array (
            0 => 'event-synthetic',
            1 => 'history-base',
            2 => 'yui-later',
          ),
          'path' => 'history/history-hash-min.js',
          'after_map' => 
          array (
            'history-html5' => true,
          ),
          'type' => 'js',
          'pkg' => 'history',
          'ext' => false,
          'name' => 'history-hash',
          'after' => 
          array (
            0 => 'history-html5',
          ),
        ),
        'history-base' => 
        array (
          'requires' => 
          array (
            0 => 'event-custom-complex',
          ),
          'path' => 'history/history-base-min.js',
          'after_map' => 
          array (
            'history-deprecated' => true,
          ),
          'type' => 'js',
          'pkg' => 'history',
          'ext' => false,
          'name' => 'history-base',
          'after' => 
          array (
            0 => 'history-deprecated',
          ),
        ),
      ),
      'type' => 'js',
      'plugins' => 
      array (
        'history-hash-ie' => 
        array (
          'requires' => 
          array (
            0 => 'history-hash',
            1 => 'node-base',
          ),
          'path' => 'history/history-hash-ie-min.js',
          'after_map' => 
          array (
            'history-hash' => true,
          ),
          'type' => 'js',
          'pkg' => 'history',
          'condition' => 
          array (
            'trigger' => 'history-hash',
          ),
          'ext' => false,
          'name' => 'history-hash-ie',
          'after' => 
          array (
            0 => 'history-hash',
          ),
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
    'autocomplete-list-keys' => 
    array (
      'requires' => 
      array (
        0 => 'autocomplete-list',
        1 => 'base-build',
      ),
      'after_map' => 
      array (
        'autocomplete-list' => true,
      ),
      'path' => 'autocomplete/autocomplete-list-keys-min.js',
      'type' => 'js',
      'pkg' => 'autocomplete-list',
      'condition' => 
      array (
        'trigger' => 'autocomplete-list',
      ),
      'ext' => false,
      'name' => 'autocomplete-list-keys',
      'after' => 
      array (
        0 => 'autocomplete-list',
      ),
    ),
    'recordset-filter' => 
    array (
      'path' => 'recordset/recordset-filter-min.js',
      'requires' => 
      array (
        0 => 'recordset-base',
        1 => 'array-extras',
        2 => 'plugin',
      ),
      'type' => 'js',
      'pkg' => 'recordset',
      'name' => 'recordset-filter',
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
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-child.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-child',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
    'event-base-ie' => 
    array (
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'after_map' => 
      array (
        'node-base' => true,
        'event-base' => true,
      ),
      'path' => 'event/event-base-ie-min.js',
      'type' => 'js',
      'pkg' => 'event',
      'condition' => 
      array (
        'trigger' => 'node-base',
      ),
      'ext' => false,
      'name' => 'event-base-ie',
      'after' => 
      array (
        0 => 'event-base',
        1 => 'node-base',
      ),
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
      'type' => 'css',
      'ext' => false,
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
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'after_map' => 
      array (
        'n' => true,
        'd' => true,
        'o' => true,
        'e' => true,
        '-' => true,
        's' => true,
        'a' => true,
        'b' => true,
      ),
      'path' => 'event/event-base-min.js',
      'type' => 'js',
      'pkg' => 'event',
      'ext' => false,
      'name' => 'event-base',
      'after' => 'node-base',
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
          'requires' => 
          array (
            0 => 'event-move',
            1 => 'dd-drag',
          ),
          'path' => 'dd/dd-gestures-min.js',
          'after_map' => 
          array (
            'dd-drag' => true,
          ),
          'type' => 'js',
          'pkg' => 'dd',
          'condition' => 
          array (
            'trigger' => 'dd-drag',
          ),
          'ext' => false,
          'name' => 'dd-gestures',
          'after' => 
          array (
            0 => 'dd-drag',
          ),
        ),
        'dd-plugin' => 
        array (
          'requires' => 
          array (
            0 => 'dd-drag',
          ),
          'path' => 'dd/dd-plugin-min.js',
          'optional' => 
          array (
            0 => 'dd-constrain',
            1 => 'dd-proxy',
          ),
          'pkg' => 'dd',
          'type' => 'js',
          'ext' => false,
          'name' => 'dd-plugin',
        ),
      ),
      'ext' => false,
      'name' => 'dd',
      'rollup' => 4,
    ),
    'history-html5' => 
    array (
      'requires' => 
      array (
        0 => 'history-base',
        1 => 'event-base',
        2 => 'node-base',
      ),
      'path' => 'history/history-html5-min.js',
      'optional' => 
      array (
        0 => 'json',
      ),
      'pkg' => 'history',
      'type' => 'js',
      'ext' => false,
      'name' => 'history-html5',
    ),
    'recordset' => 
    array (
      'supersedes' => 
      array (
        0 => 'recordset-filter',
        1 => 'recordset-base',
        2 => 'recordset-indexer',
        3 => 'recordset-sort',
      ),
      'path' => 'recordset/recordset-min.js',
      'rollup' => 3,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'recordset-filter' => 
        array (
          'path' => 'recordset/recordset-filter-min.js',
          'requires' => 
          array (
            0 => 'recordset-base',
            1 => 'array-extras',
            2 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'recordset',
          'name' => 'recordset-filter',
          'ext' => false,
        ),
        'recordset-base' => 
        array (
          'path' => 'recordset/recordset-base-min.js',
          'requires' => 
          array (
            0 => 'base',
            1 => 'arraylist',
          ),
          'type' => 'js',
          'pkg' => 'recordset',
          'name' => 'recordset-base',
          'ext' => false,
        ),
        'recordset-indexer' => 
        array (
          'path' => 'recordset/recordset-indexer-min.js',
          'requires' => 
          array (
            0 => 'recordset-base',
            1 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'recordset',
          'name' => 'recordset-indexer',
          'ext' => false,
        ),
        'recordset-sort' => 
        array (
          'path' => 'recordset/recordset-sort-min.js',
          'requires' => 
          array (
            0 => 'arraysort',
            1 => 'recordset-base',
            2 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'recordset',
          'name' => 'recordset-sort',
          'ext' => false,
        ),
      ),
      'name' => 'recordset',
      'requires' => 
      array (
      ),
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
    'autocomplete-filters-accentfold' => 
    array (
      'path' => 'autocomplete/autocomplete-filters-accentfold-min.js',
      'requires' => 
      array (
        0 => 'array-extras',
        1 => 'text-accentfold',
        2 => 'text-wordbreak',
      ),
      'type' => 'js',
      'pkg' => 'autocomplete-base',
      'name' => 'autocomplete-filters-accentfold',
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
    'charts' => 
    array (
      'path' => 'charts/charts-min.js',
      'requires' => 
      array (
        0 => 'dom',
        1 => 'widget-stack',
        2 => 'widget',
        3 => 'widget-position',
        4 => 'datatype',
        5 => 'event-custom',
        6 => 'event-mouseenter',
      ),
      'type' => 'js',
      'name' => 'charts',
      'ext' => false,
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
    'resize-base' => 
    array (
      'requires' => 
      array (
        0 => 'dd-drag',
        1 => 'widget',
        2 => 'event',
        3 => 'oop',
        4 => 'dd-drop',
        5 => 'dd-delegate',
        6 => 'substitute',
      ),
      'path' => 'resize/resize-base-min.js',
      'type' => 'js',
      'pkg' => 'resize',
      'skinnable' => true,
      'ext' => false,
      'name' => 'resize-base',
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
    'resize' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'resize/resize-min.js',
      'supersedes' => 
      array (
        0 => 'resize-base',
        1 => 'skin-sam-resize-base',
        2 => 'resize-constrain',
        3 => 'resize-proxy',
      ),
      'submodules' => 
      array (
        'resize-proxy' => 
        array (
          'path' => 'resize/resize-proxy-min.js',
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'resize-base',
          ),
          'type' => 'js',
          'pkg' => 'resize',
          'name' => 'resize-proxy',
          'ext' => false,
        ),
        'resize-base' => 
        array (
          'requires' => 
          array (
            0 => 'dd-drag',
            1 => 'widget',
            2 => 'event',
            3 => 'oop',
            4 => 'dd-drop',
            5 => 'dd-delegate',
            6 => 'substitute',
          ),
          'path' => 'resize/resize-base-min.js',
          'type' => 'js',
          'pkg' => 'resize',
          'skinnable' => true,
          'ext' => false,
          'name' => 'resize-base',
        ),
        'resize-constrain' => 
        array (
          'path' => 'resize/resize-constrain-min.js',
          'requires' => 
          array (
            0 => 'plugin',
            1 => 'resize-base',
          ),
          'type' => 'js',
          'pkg' => 'resize',
          'name' => 'resize-constrain',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'skinnable' => true,
      'ext' => false,
      'name' => 'resize',
      'rollup' => 3,
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
    'skin-sam-autocomplete-plugin' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'autocomplete-list/assets/skins/sam/autocomplete-plugin.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-autocomplete-plugin',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
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
        0 => 'editor-lists',
        1 => 'selection',
        2 => 'createlink-base',
        3 => 'editor-br',
        4 => 'exec-command',
        5 => 'editor-base',
        6 => 'editor-para',
        7 => 'editor-bidi',
        8 => 'frame',
      ),
      'path' => 'editor/editor-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
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
        'editor-br' => 
        array (
          'path' => 'editor/editor-br-min.js',
          'requires' => 
          array (
            0 => 'node',
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'editor-br',
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
          ),
          'type' => 'js',
          'pkg' => 'editor',
          'name' => 'editor-base',
          'ext' => false,
        ),
        'editor-para' => 
        array (
          'path' => 'editor/editor-para-min.js',
          'requires' => 
          array (
            0 => 'node',
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
      ),
      'name' => 'editor',
      'requires' => 
      array (
      ),
    ),
    'skin-sam-widget-position' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-position.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
          'requires' => 
          array (
            0 => 'widget',
            1 => 'event-gestures',
            2 => 'transition',
          ),
          'path' => 'scrollview/scrollview-base-min.js',
          'type' => 'js',
          'pkg' => 'scrollview',
          'skinnable' => true,
          'ext' => false,
          'name' => 'scrollview-base',
        ),
        'scrollview-scrollbars' => 
        array (
          'requires' => 
          array (
            0 => 'plugin',
          ),
          'path' => 'scrollview/scrollview-scrollbars-min.js',
          'type' => 'js',
          'pkg' => 'scrollview',
          'skinnable' => true,
          'ext' => false,
          'name' => 'scrollview-scrollbars',
        ),
        'scrollview-paginator' => 
        array (
          'path' => 'scrollview/scrollview-paginator-min.js',
          'requires' => 
          array (
            0 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'scrollview',
          'name' => 'scrollview-paginator',
          'ext' => false,
        ),
        'scrollview-base-ie' => 
        array (
          'requires' => 
          array (
            0 => 'scrollview-base',
          ),
          'path' => 'scrollview/scrollview-base-ie-min.js',
          'after_map' => 
          array (
            'scrollview-base' => true,
          ),
          'type' => 'js',
          'pkg' => 'scrollview',
          'condition' => 
          array (
            'ua' => 'ie',
            'trigger' => 'scrollview-base',
          ),
          'ext' => false,
          'name' => 'scrollview-base-ie',
          'after' => 
          array (
            0 => 'scrollview-base',
          ),
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
    'lang/dial_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'dial/lang/dial_en.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/dial_en',
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
        0 => 'widget-skin',
        1 => 'widget-base',
        2 => 'widget-htmlparser',
        3 => 'widget-uievents',
      ),
      'submodules' => 
      array (
        'widget-skin' => 
        array (
          'path' => 'widget/widget-skin-min.js',
          'requires' => 
          array (
            0 => 'widget-base',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-skin',
          'ext' => false,
        ),
        'widget-base' => 
        array (
          'path' => 'widget/widget-base-min.js',
          'requires' => 
          array (
            0 => 'base-pluginhost',
            1 => 'attribute',
            2 => 'base-base',
            3 => 'node-base',
            4 => 'node-style',
            5 => 'event-focus',
            6 => 'classnamemanager',
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
        'widget-uievents' => 
        array (
          'path' => 'widget/widget-uievents-min.js',
          'requires' => 
          array (
            0 => 'node-event-delegate',
            1 => 'widget-base',
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'name' => 'widget-uievents',
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
          'requires' => 
          array (
            0 => 'widget',
            1 => 'base-build',
          ),
          'path' => 'widget/widget-stack-min.js',
          'type' => 'js',
          'pkg' => 'widget',
          'skinnable' => true,
          'ext' => false,
          'name' => 'widget-stack',
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
        'widget-base-ie' => 
        array (
          'requires' => 
          array (
            0 => 'widget-base',
          ),
          'path' => 'widget/widget-base-ie-min.js',
          'after_map' => 
          array (
            'widget-base' => true,
          ),
          'type' => 'js',
          'pkg' => 'widget',
          'condition' => 
          array (
            'ua' => 'ie',
            'trigger' => 'widget-base',
          ),
          'ext' => false,
          'name' => 'widget-base-ie',
          'after' => 
          array (
            0 => 'widget-base',
          ),
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
      'rollup' => 3,
    ),
    'widget-base' => 
    array (
      'path' => 'widget/widget-base-min.js',
      'requires' => 
      array (
        0 => 'base-pluginhost',
        1 => 'attribute',
        2 => 'base-base',
        3 => 'node-base',
        4 => 'node-style',
        5 => 'event-focus',
        6 => 'classnamemanager',
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
    'lang/dial_es' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'dial/lang/dial_es.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/dial_es',
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
    'resize-proxy' => 
    array (
      'path' => 'resize/resize-proxy-min.js',
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'resize-base',
      ),
      'type' => 'js',
      'pkg' => 'resize',
      'name' => 'resize-proxy',
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
        0 => 'yui-base',
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
        0 => 'yui-base',
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
    'widget-base-ie' => 
    array (
      'requires' => 
      array (
        0 => 'widget-base',
      ),
      'after_map' => 
      array (
        'widget-base' => true,
      ),
      'path' => 'widget/widget-base-ie-min.js',
      'type' => 'js',
      'pkg' => 'widget',
      'condition' => 
      array (
        'ua' => 'ie',
        'trigger' => 'widget-base',
      ),
      'ext' => false,
      'name' => 'widget-base-ie',
      'after' => 
      array (
        0 => 'widget-base',
      ),
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
    'skin-sam-resize-base' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'resize/assets/skins/sam/resize-base.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-resize-base',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
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
            0 => 'oop',
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
        0 => 'yui-base',
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
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-position-constrain.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position-constrain',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
          'requires' => 
          array (
            0 => 'widget',
            1 => 'substitute',
            2 => 'dd-constrain',
          ),
          'path' => 'slider/slider-base-min.js',
          'type' => 'js',
          'pkg' => 'slider',
          'skinnable' => true,
          'ext' => false,
          'name' => 'slider-base',
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
        0 => 'get',
        1 => 'features',
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
      'after_map' => 
      array (
        'n' => true,
        'd' => true,
        'o' => true,
        'e' => true,
        '-' => true,
        's' => true,
        'a' => true,
        'b' => true,
      ),
      'path' => 'event/event-min.js',
      'supersedes' => 
      array (
        0 => 'event-base',
        1 => 'event-synthetic',
        2 => 'event-focus',
        3 => 'event-delegate',
        4 => 'event-resize',
        5 => 'event-key',
        6 => 'event-mouseenter',
        7 => 'event-hover',
        8 => 'event-mousewheel',
      ),
      'after' => 'node-base',
      'submodules' => 
      array (
        'event-base' => 
        array (
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'path' => 'event/event-base-min.js',
          'after_map' => 
          array (
            'n' => true,
            'd' => true,
            'o' => true,
            'e' => true,
            '-' => true,
            's' => true,
            'a' => true,
            'b' => true,
          ),
          'type' => 'js',
          'pkg' => 'event',
          'ext' => false,
          'name' => 'event-base',
          'after' => 'node-base',
        ),
        'event-synthetic' => 
        array (
          'path' => 'event/event-synthetic-min.js',
          'requires' => 
          array (
            0 => 'node-base',
            1 => 'event-custom-complex',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-synthetic',
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
        'event-hover' => 
        array (
          'path' => 'event/event-hover-min.js',
          'requires' => 
          array (
            0 => 'event-synthetic',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-hover',
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
      ),
      'type' => 'js',
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
        'event-base-ie' => 
        array (
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'path' => 'event/event-base-ie-min.js',
          'after_map' => 
          array (
            'node-base' => true,
            'event-base' => true,
          ),
          'type' => 'js',
          'pkg' => 'event',
          'condition' => 
          array (
            'trigger' => 'node-base',
          ),
          'ext' => false,
          'name' => 'event-base-ie',
          'after' => 
          array (
            0 => 'event-base',
            1 => 'node-base',
          ),
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
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-position-align.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position-align',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
          'requires' => 
          array (
            0 => 'event-custom-base',
          ),
          'path' => 'io/io-base-min.js',
          'optional' => 
          array (
            0 => 'querystring-stringify-simple',
          ),
          'pkg' => 'io',
          'type' => 'js',
          'ext' => false,
          'name' => 'io-base',
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
    'autocomplete-filters' => 
    array (
      'path' => 'autocomplete/autocomplete-filters-min.js',
      'requires' => 
      array (
        0 => 'array-extras',
        1 => 'text-wordbreak',
      ),
      'type' => 'js',
      'pkg' => 'autocomplete-base',
      'name' => 'autocomplete-filters',
      'ext' => false,
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
    'autocomplete-base' => 
    array (
      'requires' => 
      array (
        0 => 'escape',
        1 => 'event-valuechange',
        2 => 'node-base',
        3 => 'base-build',
        4 => 'array-extras',
      ),
      'path' => 'autocomplete/autocomplete-base-min.js',
      'optional' => 
      array (
        0 => 'autocomplete-sources',
      ),
      'pkg' => 'autocomplete',
      'type' => 'js',
      'plugins' => 
      array (
        'autocomplete-filters-accentfold' => 
        array (
          'path' => 'autocomplete/autocomplete-filters-accentfold-min.js',
          'requires' => 
          array (
            0 => 'array-extras',
            1 => 'text-accentfold',
            2 => 'text-wordbreak',
          ),
          'type' => 'js',
          'pkg' => 'autocomplete-base',
          'name' => 'autocomplete-filters-accentfold',
          'ext' => false,
        ),
        'autocomplete-highlighters' => 
        array (
          'path' => 'autocomplete/autocomplete-highlighters-min.js',
          'requires' => 
          array (
            0 => 'array-extras',
            1 => 'highlight-base',
          ),
          'type' => 'js',
          'pkg' => 'autocomplete-base',
          'name' => 'autocomplete-highlighters',
          'ext' => false,
        ),
        'autocomplete-highlighters-accentfold' => 
        array (
          'path' => 'autocomplete/autocomplete-highlighters-accentfold-min.js',
          'requires' => 
          array (
            0 => 'array-extras',
            1 => 'highlight-accentfold',
          ),
          'type' => 'js',
          'pkg' => 'autocomplete-base',
          'name' => 'autocomplete-highlighters-accentfold',
          'ext' => false,
        ),
        'autocomplete-filters' => 
        array (
          'path' => 'autocomplete/autocomplete-filters-min.js',
          'requires' => 
          array (
            0 => 'array-extras',
            1 => 'text-wordbreak',
          ),
          'type' => 'js',
          'pkg' => 'autocomplete-base',
          'name' => 'autocomplete-filters',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'autocomplete-base',
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
    'autocomplete' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'autocomplete/autocomplete-min.js',
      'supersedes' => 
      array (
        0 => 'autocomplete-sources',
        1 => 'autocomplete-base',
        2 => 'skin-sam-autocomplete-list',
        3 => 'autocomplete-list',
      ),
      'submodules' => 
      array (
        'autocomplete-list' => 
        array (
          'requires' => 
          array (
            0 => 'widget-stack',
            1 => 'widget',
            2 => 'widget-position',
            3 => 'autocomplete-base',
            4 => 'selector-css3',
            5 => 'widget-position-align',
          ),
          'path' => 'autocomplete/autocomplete-list-min.js',
          'after_map' => 
          array (
            's' => true,
            't' => true,
            'u' => true,
            'r' => true,
            'a' => true,
            'c' => true,
            'e' => true,
            'l' => true,
            'm' => true,
            'o' => true,
            'p' => true,
            '-' => true,
          ),
          'type' => 'js',
          'pkg' => 'autocomplete',
          'skinnable' => true,
          'plugins' => 
          array (
            'autocomplete-list-keys' => 
            array (
              'requires' => 
              array (
                0 => 'autocomplete-list',
                1 => 'base-build',
              ),
              'path' => 'autocomplete/autocomplete-list-keys-min.js',
              'after_map' => 
              array (
                'autocomplete-list' => true,
              ),
              'type' => 'js',
              'pkg' => 'autocomplete-list',
              'condition' => 
              array (
                'trigger' => 'autocomplete-list',
              ),
              'ext' => false,
              'name' => 'autocomplete-list-keys',
              'after' => 
              array (
                0 => 'autocomplete-list',
              ),
            ),
            'autocomplete-plugin' => 
            array (
              'path' => 'autocomplete/autocomplete-plugin-min.js',
              'requires' => 
              array (
                0 => 'node-pluginhost',
                1 => 'autocomplete-list',
              ),
              'type' => 'js',
              'pkg' => 'autocomplete-list',
              'name' => 'autocomplete-plugin',
              'ext' => false,
            ),
          ),
          'lang' => 
          array (
            0 => 'en',
          ),
          'ext' => false,
          'after' => 'autocomplete-sources',
          'name' => 'autocomplete-list',
        ),
        'autocomplete-base' => 
        array (
          'requires' => 
          array (
            0 => 'escape',
            1 => 'event-valuechange',
            2 => 'node-base',
            3 => 'base-build',
            4 => 'array-extras',
          ),
          'path' => 'autocomplete/autocomplete-base-min.js',
          'optional' => 
          array (
            0 => 'autocomplete-sources',
          ),
          'pkg' => 'autocomplete',
          'type' => 'js',
          'plugins' => 
          array (
            'autocomplete-filters-accentfold' => 
            array (
              'path' => 'autocomplete/autocomplete-filters-accentfold-min.js',
              'requires' => 
              array (
                0 => 'array-extras',
                1 => 'text-accentfold',
                2 => 'text-wordbreak',
              ),
              'type' => 'js',
              'pkg' => 'autocomplete-base',
              'name' => 'autocomplete-filters-accentfold',
              'ext' => false,
            ),
            'autocomplete-highlighters' => 
            array (
              'path' => 'autocomplete/autocomplete-highlighters-min.js',
              'requires' => 
              array (
                0 => 'array-extras',
                1 => 'highlight-base',
              ),
              'type' => 'js',
              'pkg' => 'autocomplete-base',
              'name' => 'autocomplete-highlighters',
              'ext' => false,
            ),
            'autocomplete-highlighters-accentfold' => 
            array (
              'path' => 'autocomplete/autocomplete-highlighters-accentfold-min.js',
              'requires' => 
              array (
                0 => 'array-extras',
                1 => 'highlight-accentfold',
              ),
              'type' => 'js',
              'pkg' => 'autocomplete-base',
              'name' => 'autocomplete-highlighters-accentfold',
              'ext' => false,
            ),
            'autocomplete-filters' => 
            array (
              'path' => 'autocomplete/autocomplete-filters-min.js',
              'requires' => 
              array (
                0 => 'array-extras',
                1 => 'text-wordbreak',
              ),
              'type' => 'js',
              'pkg' => 'autocomplete-base',
              'name' => 'autocomplete-filters',
              'ext' => false,
            ),
          ),
          'ext' => false,
          'name' => 'autocomplete-base',
        ),
        'autocomplete-sources' => 
        array (
          'requires' => 
          array (
            0 => 'autocomplete-base',
          ),
          'path' => 'autocomplete/autocomplete-sources-min.js',
          'optional' => 
          array (
            0 => 'io-base',
            1 => 'json-parse',
            2 => 'jsonp',
            3 => 'yql',
          ),
          'pkg' => 'autocomplete',
          'type' => 'js',
          'ext' => false,
          'name' => 'autocomplete-sources',
        ),
      ),
      'type' => 'js',
      'skinnable' => true,
      'ext' => false,
      'lang' => 
      array (
        0 => 'en',
      ),
      'name' => 'autocomplete',
      'rollup' => 3,
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
    'recordset-sort' => 
    array (
      'path' => 'recordset/recordset-sort-min.js',
      'requires' => 
      array (
        0 => 'arraysort',
        1 => 'recordset-base',
        2 => 'plugin',
      ),
      'type' => 'js',
      'pkg' => 'recordset',
      'name' => 'recordset-sort',
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
      'type' => 'js',
      'ext' => false,
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
    'text-data-accentfold' => 
    array (
      'path' => 'text/text-data-accentfold-min.js',
      'type' => 'js',
      'ext' => false,
      'pkg' => 'text',
      'name' => 'text-data-accentfold',
      'requires' => 
      array (
      ),
    ),
    'escape' => 
    array (
      'path' => 'escape/escape-min.js',
      'type' => 'js',
      'ext' => false,
      'name' => 'escape',
      'requires' => 
      array (
      ),
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
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssgrids-context' => true,
        'cssgrids' => true,
        'cssreset' => true,
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
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssgrids-context' => true,
        'cssgrids' => true,
        'cssreset' => true,
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
    'skin-sam-datatable-base' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'datatable/assets/skins/sam/datatable-base.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-datatable-base',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
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
          'requires' => 
          array (
            0 => 'get',
            1 => 'features',
          ),
          'path' => 'yui/rls-min.js',
          'type' => 'js',
          'pkg' => 'yui',
          '_inspected' => true,
          'ext' => false,
          'name' => 'rls',
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
          'pkg' => 'yui',
          '_inspected' => true,
          'expanded' => 
          array (
          ),
          'ext' => false,
          '_parsed' => false,
          'name' => 'yui-base',
        ),
        'features' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'yui/features-min.js',
          'type' => 'js',
          'pkg' => 'yui',
          '_inspected' => true,
          'ext' => false,
          'name' => 'features',
        ),
        'yui-later' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'yui/yui-later-min.js',
          'type' => 'js',
          'pkg' => 'yui',
          '_inspected' => true,
          'ext' => false,
          'name' => 'yui-later',
        ),
        'get' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'yui/get-min.js',
          'type' => 'js',
          'pkg' => 'yui',
          '_inspected' => true,
          'ext' => false,
          'name' => 'get',
        ),
        'yui-throttle' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'yui/yui-throttle-min.js',
          'type' => 'js',
          'pkg' => 'yui',
          '_inspected' => true,
          'ext' => false,
          'name' => 'yui-throttle',
        ),
        'intl-base' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'yui/intl-base-min.js',
          'type' => 'js',
          'pkg' => 'yui',
          '_inspected' => true,
          'ext' => false,
          'name' => 'intl-base',
        ),
        'yui-log' => 
        array (
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'path' => 'yui/yui-log-min.js',
          'type' => 'js',
          'pkg' => 'yui',
          '_inspected' => true,
          'ext' => false,
          'name' => 'yui-log',
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
      'requires' => 
      array (
        0 => 'attribute-base',
      ),
      'after_map' => 
      array (
        'attribute-complex' => true,
      ),
      'path' => 'base/base-base-min.js',
      'type' => 'js',
      'pkg' => 'base',
      'ext' => false,
      'name' => 'base-base',
      'after' => 
      array (
        0 => 'attribute-complex',
      ),
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
      '_inspected' => true,
      'pkg' => 'yui',
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
      'requires' => 
      array (
        0 => 'dom-style',
      ),
      'after_map' => 
      array (
        'dom-style' => true,
      ),
      'path' => 'dom/dom-style-ie-min.js',
      'type' => 'js',
      'pkg' => 'dom',
      'condition' => 
      array (
        'trigger' => 'dom-style',
      ),
      'ext' => false,
      'name' => 'dom-style-ie',
      'after' => 
      array (
        0 => 'dom-style',
      ),
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
    'datatable-sort' => 
    array (
      'requires' => 
      array (
        0 => 'datatable-base',
        1 => 'recordset-sort',
        2 => 'plugin',
      ),
      'path' => 'datatable/datatable-sort-min.js',
      'type' => 'js',
      'pkg' => 'datatable',
      'ext' => false,
      'lang' => 
      array (
        0 => 'en',
      ),
      'name' => 'datatable-sort',
    ),
    'datatable-scroll' => 
    array (
      'path' => 'datatable/datatable-scroll-min.js',
      'requires' => 
      array (
        0 => 'datatable-base',
        1 => 'stylesheet',
        2 => 'plugin',
      ),
      'type' => 'js',
      'pkg' => 'datatable',
      'name' => 'datatable-scroll',
      'ext' => false,
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
    'datatable' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatable/datatable-min.js',
      'supersedes' => 
      array (
        0 => 'datatable-sort',
        1 => 'datatable-scroll',
        2 => 'skin-sam-datatable-base',
        3 => 'datatable-datasource',
        4 => 'datatable-base',
      ),
      'submodules' => 
      array (
        'datatable-sort' => 
        array (
          'requires' => 
          array (
            0 => 'datatable-base',
            1 => 'recordset-sort',
            2 => 'plugin',
          ),
          'path' => 'datatable/datatable-sort-min.js',
          'type' => 'js',
          'pkg' => 'datatable',
          'lang' => 
          array (
            0 => 'en',
          ),
          'ext' => false,
          'name' => 'datatable-sort',
        ),
        'datatable-scroll' => 
        array (
          'path' => 'datatable/datatable-scroll-min.js',
          'requires' => 
          array (
            0 => 'datatable-base',
            1 => 'stylesheet',
            2 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'datatable',
          'name' => 'datatable-scroll',
          'ext' => false,
        ),
        'datatable-datasource' => 
        array (
          'path' => 'datatable/datatable-datasource-min.js',
          'requires' => 
          array (
            0 => 'datatable-base',
            1 => 'plugin',
            2 => 'datasource-local',
          ),
          'type' => 'js',
          'pkg' => 'datatable',
          'name' => 'datatable-datasource',
          'ext' => false,
        ),
        'datatable-base' => 
        array (
          'requires' => 
          array (
            0 => 'widget',
            1 => 'recordset-base',
            2 => 'event-mouseenter',
            3 => 'substitute',
          ),
          'path' => 'datatable/datatable-base-min.js',
          'type' => 'js',
          'pkg' => 'datatable',
          'skinnable' => true,
          'ext' => false,
          'name' => 'datatable-base',
        ),
      ),
      'type' => 'js',
      'skinnable' => true,
      'ext' => false,
      'lang' => 
      array (
        0 => 'en',
      ),
      'name' => 'datatable',
      'rollup' => 3,
    ),
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
        0 => 'yui-base',
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
    'skin-sam-autocomplete-list-keys' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'autocomplete-list/assets/skins/sam/autocomplete-list-keys.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-autocomplete-list-keys',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
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
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'tabview/assets/skins/sam/tabview-base.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-tabview-base',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
    'autocomplete-highlighters-accentfold' => 
    array (
      'path' => 'autocomplete/autocomplete-highlighters-accentfold-min.js',
      'requires' => 
      array (
        0 => 'array-extras',
        1 => 'highlight-accentfold',
      ),
      'type' => 'js',
      'pkg' => 'autocomplete-base',
      'name' => 'autocomplete-highlighters-accentfold',
      'ext' => false,
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
    'lang/autocomplete_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'autocomplete/lang/autocomplete_en.js',
      'supersedes' => 
      array (
        0 => 'lang/autocomplete-list_en',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/autocomplete_en',
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
    'lang/autocomplete-list_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'autocomplete/lang/autocomplete-list_en.js',
      'supersedes' => 
      array (
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/autocomplete-list_en',
    ),
    'node-deprecated' => 
    array (
      'path' => 'node/node-deprecated-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-deprecated',
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
    'scrollview-base-ie' => 
    array (
      'requires' => 
      array (
        0 => 'scrollview-base',
      ),
      'after_map' => 
      array (
        'scrollview-base' => true,
      ),
      'path' => 'scrollview/scrollview-base-ie-min.js',
      'type' => 'js',
      'pkg' => 'scrollview',
      'condition' => 
      array (
        'ua' => 'ie',
        'trigger' => 'scrollview-base',
      ),
      'ext' => false,
      'name' => 'scrollview-base-ie',
      'after' => 
      array (
        0 => 'scrollview-base',
      ),
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
    'node-load' => 
    array (
      'path' => 'node/node-load-min.js',
      'requires' => 
      array (
        0 => 'io-base',
        1 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-load',
      'ext' => false,
    ),
    'editor-br' => 
    array (
      'path' => 'editor/editor-br-min.js',
      'requires' => 
      array (
        0 => 'node',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'editor-br',
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
    'dom-deprecated' => 
    array (
      'path' => 'dom/dom-deprecated-min.js',
      'requires' => 
      array (
        0 => 'dom-base',
      ),
      'type' => 'js',
      'pkg' => 'dom',
      'name' => 'dom-deprecated',
      'ext' => false,
    ),
    'lang/datatable' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatable/lang/datatable.js',
      'supersedes' => 
      array (
        0 => 'lang/datatable-sort',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatable',
    ),
    'recordset-base' => 
    array (
      'path' => 'recordset/recordset-base-min.js',
      'requires' => 
      array (
        0 => 'base',
        1 => 'arraylist',
      ),
      'type' => 'js',
      'pkg' => 'recordset',
      'name' => 'recordset-base',
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
    'skin-sam-autocomplete-list' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'autocomplete/assets/skins/sam/autocomplete-list.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-autocomplete-list',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
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
    'lang/autocomplete' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'autocomplete/lang/autocomplete.js',
      'supersedes' => 
      array (
        0 => 'lang/autocomplete-list',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/autocomplete',
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
    'pluginhost-base' => 
    array (
      'path' => 'pluginhost/pluginhost-base-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'type' => 'js',
      'pkg' => 'pluginhost',
      'name' => 'pluginhost-base',
      'ext' => false,
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
    'lang/datatable_en' => 
    array (
      'requires' => 
      array (
      ),
      'path' => 'datatable/lang/datatable_en.js',
      'supersedes' => 
      array (
        0 => 'lang/datatable-sort_en',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatable_en',
    ),
    'event-synthetic' => 
    array (
      'path' => 'event/event-synthetic-min.js',
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'event-custom-complex',
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
      'requires' => 
      array (
        0 => 'history-hash',
        1 => 'node-base',
      ),
      'after_map' => 
      array (
        'history-hash' => true,
      ),
      'path' => 'history/history-hash-ie-min.js',
      'type' => 'js',
      'pkg' => 'history',
      'condition' => 
      array (
        'trigger' => 'history-hash',
      ),
      'ext' => false,
      'name' => 'history-hash-ie',
      'after' => 
      array (
        0 => 'history-hash',
      ),
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
    'resize-constrain' => 
    array (
      'path' => 'resize/resize-constrain-min.js',
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'resize-base',
      ),
      'type' => 'js',
      'pkg' => 'resize',
      'name' => 'resize-constrain',
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
      'requires' => 
      array (
        0 => 'plugin',
      ),
      'path' => 'scrollview/scrollview-scrollbars-min.js',
      'type' => 'js',
      'pkg' => 'scrollview',
      'skinnable' => true,
      'ext' => false,
      'name' => 'scrollview-scrollbars',
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
    'widget-skin' => 
    array (
      'path' => 'widget/widget-skin-min.js',
      'requires' => 
      array (
        0 => 'widget-base',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-skin',
      'ext' => false,
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
      'type' => 'css',
      'ext' => false,
      'name' => 'cssreset-context',
      'requires' => 
      array (
      ),
    ),
    'lang/datatable-sort' => 
    array (
      'path' => 'datatable/lang/datatable-sort.js',
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
      'name' => 'lang/datatable-sort',
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
    'datatable-base' => 
    array (
      'requires' => 
      array (
        0 => 'recordset-base',
        1 => 'widget',
        2 => 'event-mouseenter',
        3 => 'substitute',
      ),
      'path' => 'datatable/datatable-base-min.js',
      'type' => 'js',
      'pkg' => 'datatable',
      'skinnable' => true,
      'ext' => false,
      'name' => 'datatable-base',
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
    'datatable-datasource' => 
    array (
      'path' => 'datatable/datatable-datasource-min.js',
      'requires' => 
      array (
        0 => 'datatable-base',
        1 => 'plugin',
        2 => 'datasource-local',
      ),
      'type' => 'js',
      'pkg' => 'datatable',
      'name' => 'datatable-datasource',
      'ext' => false,
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
    'widget-uievents' => 
    array (
      'path' => 'widget/widget-uievents-min.js',
      'requires' => 
      array (
        0 => 'node-event-delegate',
        1 => 'widget-base',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-uievents',
      'ext' => false,
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
    'dial' => 
    array (
      'lang' => 
      array (
        0 => 'en',
        1 => 'es',
      ),
      'path' => 'dial/dial-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'transition',
        1 => 'dd-drag',
        2 => 'widget',
        3 => 'intl',
        4 => 'event-mouseenter',
        5 => 'substitute',
      ),
      'type' => 'js',
      'name' => 'dial',
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
    'yui-throttle' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
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
          'requires' => 
          array (
            0 => 'attribute-base',
          ),
          'path' => 'base/base-base-min.js',
          'after_map' => 
          array (
            'attribute-complex' => true,
          ),
          'type' => 'js',
          'pkg' => 'base',
          'ext' => false,
          'name' => 'base-base',
          'after' => 
          array (
            0 => 'attribute-complex',
          ),
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
      'supersedes' => 
      array (
        0 => 'pluginhost-base',
        1 => 'pluginhost-config',
      ),
      'path' => 'pluginhost/pluginhost-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'pluginhost-base' => 
        array (
          'path' => 'pluginhost/pluginhost-base-min.js',
          'requires' => 
          array (
            0 => 'yui-base',
          ),
          'type' => 'js',
          'pkg' => 'pluginhost',
          'name' => 'pluginhost-base',
          'ext' => false,
        ),
        'pluginhost-config' => 
        array (
          'path' => 'pluginhost/pluginhost-config-min.js',
          'requires' => 
          array (
            0 => 'pluginhost-base',
          ),
          'type' => 'js',
          'pkg' => 'pluginhost',
          'name' => 'pluginhost-config',
          'ext' => false,
        ),
      ),
      'name' => 'pluginhost',
      'requires' => 
      array (
      ),
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
      'type' => 'css',
      'ext' => false,
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
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'tabview/assets/skins/sam/tabview-plugin.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-tabview-plugin',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
    ),
    'node' => 
    array (
      'requires' => 
      array (
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
        'node-load' => 
        array (
          'path' => 'node/node-load-min.js',
          'requires' => 
          array (
            0 => 'io-base',
            1 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'node-load',
          'ext' => false,
        ),
        'node-deprecated' => 
        array (
          'path' => 'node/node-deprecated-min.js',
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'node',
          'name' => 'node-deprecated',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'node',
      'rollup' => 4,
    ),
    'autocomplete-highlighters' => 
    array (
      'path' => 'autocomplete/autocomplete-highlighters-min.js',
      'requires' => 
      array (
        0 => 'array-extras',
        1 => 'highlight-base',
      ),
      'type' => 'js',
      'pkg' => 'autocomplete-base',
      'name' => 'autocomplete-highlighters',
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
    'highlight' => 
    array (
      'supersedes' => 
      array (
        0 => 'highlight-accentfold',
        1 => 'highlight-base',
      ),
      'path' => 'highlight/highlight-min.js',
      'rollup' => 2,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'highlight-accentfold' => 
        array (
          'path' => 'highlight/highlight-accentfold-min.js',
          'requires' => 
          array (
            0 => 'text-accentfold',
            1 => 'highlight-base',
          ),
          'type' => 'js',
          'pkg' => 'highlight',
          'name' => 'highlight-accentfold',
          'ext' => false,
        ),
        'highlight-base' => 
        array (
          'path' => 'highlight/highlight-base-min.js',
          'requires' => 
          array (
            0 => 'array-extras',
            1 => 'escape',
            2 => 'text-wordbreak',
          ),
          'type' => 'js',
          'pkg' => 'highlight',
          'name' => 'highlight-base',
          'ext' => false,
        ),
      ),
      'name' => 'highlight',
      'requires' => 
      array (
      ),
    ),
    'skin-sam-widget-base-ie' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-base-ie.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-base-ie',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
      ),
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
      'requires' => 
      array (
      ),
      'path' => 'datatype/lang/datatype.js',
      'supersedes' => 
      array (
        0 => 'lang/datatype-date',
        1 => 'lang/datatype-date',
        2 => 'lang/datatype-date',
        3 => 'lang/datatype-date',
        4 => 'lang/datatype-date',
        5 => 'lang/datatype-date',
        6 => 'lang/datatype-date',
        7 => 'lang/datatype-date',
        8 => 'lang/datatype-date',
        9 => 'lang/datatype-date',
        10 => 'lang/datatype-date',
        11 => 'lang/datatype-date',
        12 => 'lang/datatype-date',
        13 => 'lang/datatype-date',
        14 => 'lang/datatype-date',
        15 => 'lang/datatype-date',
        16 => 'lang/datatype-date',
        17 => 'lang/datatype-date',
        18 => 'lang/datatype-date',
        19 => 'lang/datatype-date',
        20 => 'lang/datatype-date',
        21 => 'lang/datatype-date',
        22 => 'lang/datatype-date',
        23 => 'lang/datatype-date',
        24 => 'lang/datatype-date',
        25 => 'lang/datatype-date',
        26 => 'lang/datatype-date',
        27 => 'lang/datatype-date',
        28 => 'lang/datatype-date',
        29 => 'lang/datatype-date',
        30 => 'lang/datatype-date',
        31 => 'lang/datatype-date',
        32 => 'lang/datatype-date',
        33 => 'lang/datatype-date',
        34 => 'lang/datatype-date',
        35 => 'lang/datatype-date',
        36 => 'lang/datatype-date',
        37 => 'lang/datatype-date',
        38 => 'lang/datatype-date',
        39 => 'lang/datatype-date',
        40 => 'lang/datatype-date',
        41 => 'lang/datatype-date',
        42 => 'lang/datatype-date',
        43 => 'lang/datatype-date',
        44 => 'lang/datatype-date',
        45 => 'lang/datatype-date',
        46 => 'lang/datatype-date',
        47 => 'lang/datatype-date',
        48 => 'lang/datatype-date',
        49 => 'lang/datatype-date',
        50 => 'lang/datatype-date',
        51 => 'lang/datatype-date',
        52 => 'lang/datatype-date',
        53 => 'lang/datatype-date',
        54 => 'lang/datatype-date',
        55 => 'lang/datatype-date',
        56 => 'lang/datatype-date',
        57 => 'lang/datatype-date',
        58 => 'lang/datatype-date',
        59 => 'lang/datatype-date',
        60 => 'lang/datatype-date',
        61 => 'lang/datatype-date',
        62 => 'lang/datatype-date',
        63 => 'lang/datatype-date',
        64 => 'lang/datatype-date',
        65 => 'lang/datatype-date',
        66 => 'lang/datatype-date',
        67 => 'lang/datatype-date',
        68 => 'lang/datatype-date',
        69 => 'lang/datatype-date',
        70 => 'lang/datatype-date',
        71 => 'lang/datatype-date',
        72 => 'lang/datatype-date',
        73 => 'lang/datatype-date',
        74 => 'lang/datatype-date',
        75 => 'lang/datatype-date',
        76 => 'lang/datatype-date',
        77 => 'lang/datatype-date',
        78 => 'lang/datatype-date',
        79 => 'lang/datatype-date',
      ),
      'type' => 'js',
      'intl' => true,
      'langPack' => true,
      'ext' => false,
      'name' => 'lang/datatype',
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
    'pluginhost-config' => 
    array (
      'path' => 'pluginhost/pluginhost-config-min.js',
      'requires' => 
      array (
        0 => 'pluginhost-base',
      ),
      'type' => 'js',
      'pkg' => 'pluginhost',
      'name' => 'pluginhost-config',
      'ext' => false,
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
        2 => 'cache-plugin',
      ),
      'path' => 'cache/cache-min.js',
      'rollup' => 3,
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
        'cache-plugin' => 
        array (
          'path' => 'cache/cache-plugin-min.js',
          'requires' => 
          array (
            0 => 'cache-base',
            1 => 'plugin',
          ),
          'type' => 'js',
          'pkg' => 'cache',
          'name' => 'cache-plugin',
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
    'event-hover' => 
    array (
      'path' => 'event/event-hover-min.js',
      'requires' => 
      array (
        0 => 'event-synthetic',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-hover',
      'ext' => false,
    ),
    'yui-later' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
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
    'loader' => 
    array (
      'requires' => 
      array (
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
          'requires' => 
          array (
            0 => 'loader-base',
          ),
          'path' => 'loader/loader-rollup-min.js',
          'type' => 'js',
          'pkg' => 'loader',
          '_inspected' => true,
          'ext' => false,
          'name' => 'loader-rollup',
        ),
        'loader-yui3' => 
        array (
          'requires' => 
          array (
            0 => 'loader-base',
          ),
          'path' => 'loader/loader-yui3-min.js',
          'type' => 'js',
          'pkg' => 'loader',
          '_inspected' => true,
          'ext' => false,
          'name' => 'loader-yui3',
        ),
        'loader-base' => 
        array (
          'requires' => 
          array (
            0 => 'get',
          ),
          'path' => 'loader/loader-base-min.js',
          'type' => 'js',
          'pkg' => 'loader',
          '_inspected' => true,
          'ext' => false,
          'name' => 'loader-base',
        ),
      ),
      '_inspected' => true,
      'type' => 'js',
      'ext' => false,
      'name' => 'loader',
      'rollup' => 3,
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
    'skin-sam-widget-parent' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-parent.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-parent',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
      'requires' => 
      array (
        0 => 'widget',
        1 => 'event-gestures',
        2 => 'transition',
      ),
      'path' => 'scrollview/scrollview-base-min.js',
      'type' => 'js',
      'pkg' => 'scrollview',
      'skinnable' => true,
      'ext' => false,
      'name' => 'scrollview-base',
    ),
    'lang/autocomplete-list' => 
    array (
      'path' => 'autocomplete/lang/autocomplete-list.js',
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
      'name' => 'lang/autocomplete-list',
    ),
    'skin-sam-widget-stdmod' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-stdmod.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stdmod',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
    'highlight-base' => 
    array (
      'path' => 'highlight/highlight-base-min.js',
      'requires' => 
      array (
        0 => 'array-extras',
        1 => 'escape',
        2 => 'text-wordbreak',
      ),
      'type' => 'js',
      'pkg' => 'highlight',
      'name' => 'highlight-base',
      'ext' => false,
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
    'autocomplete-list' => 
    array (
      'requires' => 
      array (
        0 => 'widget-stack',
        1 => 'widget-position',
        2 => 'widget',
        3 => 'autocomplete-base',
        4 => 'selector-css3',
        5 => 'widget-position-align',
      ),
      'after_map' => 
      array (
        's' => true,
        't' => true,
        'u' => true,
        'r' => true,
        'a' => true,
        'c' => true,
        'e' => true,
        'l' => true,
        'm' => true,
        'o' => true,
        'p' => true,
        '-' => true,
      ),
      'path' => 'autocomplete/autocomplete-list-min.js',
      'type' => 'js',
      'pkg' => 'autocomplete',
      'skinnable' => true,
      'plugins' => 
      array (
        'autocomplete-list-keys' => 
        array (
          'requires' => 
          array (
            0 => 'autocomplete-list',
            1 => 'base-build',
          ),
          'path' => 'autocomplete/autocomplete-list-keys-min.js',
          'after_map' => 
          array (
            'autocomplete-list' => true,
          ),
          'type' => 'js',
          'pkg' => 'autocomplete-list',
          'condition' => 
          array (
            'trigger' => 'autocomplete-list',
          ),
          'ext' => false,
          'name' => 'autocomplete-list-keys',
          'after' => 
          array (
            0 => 'autocomplete-list',
          ),
        ),
        'autocomplete-plugin' => 
        array (
          'path' => 'autocomplete/autocomplete-plugin-min.js',
          'requires' => 
          array (
            0 => 'node-pluginhost',
            1 => 'autocomplete-list',
          ),
          'type' => 'js',
          'pkg' => 'autocomplete-list',
          'name' => 'autocomplete-plugin',
          'ext' => false,
        ),
      ),
      'lang' => 
      array (
        0 => 'en',
      ),
      'ext' => false,
      'after' => 'autocomplete-sources',
      'name' => 'autocomplete-list',
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
      'requires' => 
      array (
        0 => 'event-move',
        1 => 'dd-drag',
      ),
      'after_map' => 
      array (
        'dd-drag' => true,
      ),
      'path' => 'dd/dd-gestures-min.js',
      'type' => 'js',
      'pkg' => 'dd',
      'condition' => 
      array (
        'trigger' => 'dd-drag',
      ),
      'ext' => false,
      'name' => 'dd-gestures',
      'after' => 
      array (
        0 => 'dd-drag',
      ),
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
    'io-base' => 
    array (
      'requires' => 
      array (
        0 => 'event-custom-base',
      ),
      'path' => 'io/io-base-min.js',
      'optional' => 
      array (
        0 => 'querystring-stringify-simple',
      ),
      'pkg' => 'io',
      'type' => 'js',
      'ext' => false,
      'name' => 'io-base',
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
    'history-base' => 
    array (
      'requires' => 
      array (
        0 => 'event-custom-complex',
      ),
      'after_map' => 
      array (
        'history-deprecated' => true,
      ),
      'path' => 'history/history-base-min.js',
      'type' => 'js',
      'pkg' => 'history',
      'ext' => false,
      'name' => 'history-base',
      'after' => 
      array (
        0 => 'history-deprecated',
      ),
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
    'datatype-date-format' => 
    array (
      'path' => 'datatype/datatype-date-format-min.js',
      'type' => 'js',
      'ext' => false,
      'name' => 'datatype-date-format',
      'requires' => 
      array (
      ),
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
    'editor-para' => 
    array (
      'path' => 'editor/editor-para-min.js',
      'requires' => 
      array (
        0 => 'node',
      ),
      'type' => 'js',
      'pkg' => 'editor',
      'name' => 'editor-para',
      'ext' => false,
    ),
    'highlight-accentfold' => 
    array (
      'path' => 'highlight/highlight-accentfold-min.js',
      'requires' => 
      array (
        0 => 'text-accentfold',
        1 => 'highlight-base',
      ),
      'type' => 'js',
      'pkg' => 'highlight',
      'name' => 'highlight-accentfold',
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
    'autocomplete-sources' => 
    array (
      'requires' => 
      array (
        0 => 'autocomplete-base',
      ),
      'path' => 'autocomplete/autocomplete-sources-min.js',
      'optional' => 
      array (
        0 => 'io-base',
        1 => 'json-parse',
        2 => 'jsonp',
        3 => 'yql',
      ),
      'pkg' => 'autocomplete',
      'type' => 'js',
      'ext' => false,
      'name' => 'autocomplete-sources',
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
        'dom-style-ie' => 
        array (
          'requires' => 
          array (
            0 => 'dom-style',
          ),
          'path' => 'dom/dom-style-ie-min.js',
          'after_map' => 
          array (
            'dom-style' => true,
          ),
          'type' => 'js',
          'pkg' => 'dom',
          'condition' => 
          array (
            'trigger' => 'dom-style',
          ),
          'ext' => false,
          'name' => 'dom-style-ie',
          'after' => 
          array (
            0 => 'dom-style',
          ),
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
        'dom-deprecated' => 
        array (
          'path' => 'dom/dom-deprecated-min.js',
          'requires' => 
          array (
            0 => 'dom-base',
          ),
          'type' => 'js',
          'pkg' => 'dom',
          'name' => 'dom-deprecated',
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
        0 => 'get',
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
      'requires' => 
      array (
        0 => 'widget',
        1 => 'substitute',
        2 => 'dd-constrain',
      ),
      'path' => 'slider/slider-base-min.js',
      'type' => 'js',
      'pkg' => 'slider',
      'skinnable' => true,
      'ext' => false,
      'name' => 'slider-base',
    ),
    'history-hash' => 
    array (
      'requires' => 
      array (
        0 => 'event-synthetic',
        1 => 'history-base',
        2 => 'yui-later',
      ),
      'after_map' => 
      array (
        'history-html5' => true,
      ),
      'path' => 'history/history-hash-min.js',
      'type' => 'js',
      'pkg' => 'history',
      'ext' => false,
      'name' => 'history-hash',
      'after' => 
      array (
        0 => 'history-html5',
      ),
    ),
    'recordset-indexer' => 
    array (
      'path' => 'recordset/recordset-indexer-min.js',
      'requires' => 
      array (
        0 => 'recordset-base',
        1 => 'plugin',
      ),
      'type' => 'js',
      'pkg' => 'recordset',
      'name' => 'recordset-indexer',
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
    'text-accentfold' => 
    array (
      'path' => 'text/text-accentfold-min.js',
      'requires' => 
      array (
        0 => 'array-extras',
        1 => 'text-data-accentfold',
      ),
      'type' => 'js',
      'pkg' => 'text',
      'name' => 'text-accentfold',
      'ext' => false,
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
    'lang/dial' => 
    array (
      'path' => 'dial/lang/dial.js',
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
      'name' => 'lang/dial',
    ),
    'dd-plugin' => 
    array (
      'requires' => 
      array (
        0 => 'dd-drag',
      ),
      'path' => 'dd/dd-plugin-min.js',
      'optional' => 
      array (
        0 => 'dd-constrain',
        1 => 'dd-proxy',
      ),
      'pkg' => 'dd',
      'type' => 'js',
      'ext' => false,
      'name' => 'dd-plugin',
    ),
    'skin-sam-widget-stack' => 
    array (
      'requires' => 
      array (
      ),
      'after_map' => 
      array (
        'cssfonts' => true,
        'cssfonts-context' => true,
        'cssreset-context' => true,
        'cssbase' => true,
        'cssgrids' => true,
        'cssreset' => true,
      ),
      'path' => 'widget/assets/skins/sam/widget-stack.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stack',
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssgrids',
        3 => 'cssbase',
        4 => 'cssreset-context',
        5 => 'cssfonts-context',
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
    'text-data-wordbreak' => 
    array (
      'path' => 'text/text-data-wordbreak-min.js',
      'type' => 'js',
      'ext' => false,
      'pkg' => 'text',
      'name' => 'text-data-wordbreak',
      'requires' => 
      array (
      ),
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