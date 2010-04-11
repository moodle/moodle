<?php $GLOBALS['yui_current'] = array (
  'base' => 'http://yui.yahooapis.com/3.1.0/build/',
  'skin' => 
  array (
    'after' => 
    array (
      0 => 'cssreset',
      1 => 'cssfonts',
      2 => 'cssreset-context',
      3 => 'cssfonts-context',
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
      'path' => 'datatype/lang/datatype_it-IT.js',
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
      'name' => 'lang/datatype_it-IT',
    ),
    'lang/datatype-date_ja-JP' => 
    array (
      'path' => 'datatype/lang/datatype-date_ja-JP.js',
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
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'console/assets/skins/sam/console-filters.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-console-filters',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_ko-KR' => 
    array (
      'path' => 'datatype/lang/datatype_ko-KR.js',
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
            0 => 'yui-base',
            1 => 'array-extras',
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
        0 => 'datasource-local',
        1 => 'plugin',
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
        0 => 'datasource-local',
        1 => 'plugin',
        2 => 'dataschema-json',
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
      'path' => 'datatype/lang/datatype-date_es-PE.js',
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
      'name' => 'lang/datatype-date_es-PE',
    ),
    'lang/datatype-date_ca' => 
    array (
      'path' => 'datatype/lang/datatype-date_ca.js',
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
      'name' => 'lang/datatype-date_ca',
    ),
    'lang/datatype_pl-PL' => 
    array (
      'path' => 'datatype/lang/datatype_pl-PL.js',
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
      'name' => 'lang/datatype_pl-PL',
    ),
    'skin-sam-slider-base' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'slider/assets/skins/sam/slider-base.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-slider-base',
      'requires' => 
      array (
      ),
    ),
    'swf' => 
    array (
      'path' => 'swf/swf-min.js',
      'requires' => 
      array (
        0 => 'event-custom',
        1 => 'node',
        2 => 'swfdetect',
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
    'console-filters' => 
    array (
      'path' => 'console/console-filters-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'console',
        2 => 'skin-sam-console-filters',
      ),
      'type' => 'js',
      'name' => 'console-filters',
      'ext' => false,
    ),
    'lang/datatype-date_en-US' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-US.js',
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
      'name' => 'lang/datatype-date_en-US',
    ),
    'lang/datatype_ar' => 
    array (
      'path' => 'datatype/lang/datatype_ar.js',
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
      'name' => 'lang/datatype_ar',
    ),
    'lang/datatype-date_vi-VN' => 
    array (
      'path' => 'datatype/lang/datatype-date_vi-VN.js',
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
    'lang/datatype_es-MX' => 
    array (
      'path' => 'datatype/lang/datatype_es-MX.js',
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
      'name' => 'lang/datatype_es-MX',
    ),
    'lang/datatype-date_es-PY' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-PY.js',
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
      'name' => 'lang/datatype-date_es-PY',
    ),
    'event-custom-base' => 
    array (
      'path' => 'event-custom/event-custom-base-min.js',
      'requires' => 
      array (
        0 => 'oop',
        1 => 'yui-later',
      ),
      'type' => 'js',
      'pkg' => 'event-custom',
      'name' => 'event-custom-base',
      'ext' => false,
    ),
    'lang/datatype-date_zh-Hans' => 
    array (
      'path' => 'datatype/lang/datatype-date_zh-Hans.js',
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
      'name' => 'lang/datatype-date_zh-Hans',
    ),
    'lang/datatype-date_zh-Hant' => 
    array (
      'path' => 'datatype/lang/datatype-date_zh-Hant.js',
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
      'name' => 'lang/datatype-date_zh-Hant',
    ),
    'dd-ddm-base' => 
    array (
      'path' => 'dd/dd-ddm-base-min.js',
      'requires' => 
      array (
        0 => 'node',
        1 => 'base',
        2 => 'yui-throttle',
      ),
      'type' => 'js',
      'pkg' => 'dd',
      'name' => 'dd-ddm-base',
      'ext' => false,
    ),
    'lang/datatype_en-SG' => 
    array (
      'path' => 'datatype/lang/datatype_en-SG.js',
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
      'path' => 'datatype/lang/datatype-date_da.js',
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
      'name' => 'lang/datatype-date_da',
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
      'path' => 'datatype/lang/datatype-date_de.js',
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
      'name' => 'lang/datatype-date_de',
    ),
    'lang/datatype-date_pt-BR' => 
    array (
      'path' => 'datatype/lang/datatype-date_pt-BR.js',
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
      'name' => 'lang/datatype-date_pt-BR',
    ),
    'lang/datatype_zh-Hant-TW' => 
    array (
      'path' => 'datatype/lang/datatype_zh-Hant-TW.js',
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
        0 => 'yui-log',
        1 => 'widget',
        2 => 'substitute',
        3 => 'skin-sam-console',
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
            2 => 'skin-sam-console-filters',
          ),
          'type' => 'js',
          'name' => 'console-filters',
          'ext' => false,
        ),
      ),
    ),
    'lang/datatype_th-TH' => 
    array (
      'path' => 'datatype/lang/datatype_th-TH.js',
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
      'name' => 'widget-position-constrain',
      'ext' => false,
    ),
    'lang/datatype_ca' => 
    array (
      'path' => 'datatype/lang/datatype_ca.js',
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
      'name' => 'lang/datatype_ca',
    ),
    'widget-stack' => 
    array (
      'path' => 'widget/widget-stack-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'skin-sam-widget-stack',
      ),
      'type' => 'js',
      'name' => 'widget-stack',
      'ext' => false,
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
    'lang/datatype-date_el' => 
    array (
      'path' => 'datatype/lang/datatype-date_el.js',
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
      'name' => 'lang/datatype-date_el',
    ),
    'lang/datatype-date_en' => 
    array (
      'path' => 'datatype/lang/datatype-date_en.js',
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
      'name' => 'lang/datatype-date_en',
    ),
    'lang/datatype_ja-JP' => 
    array (
      'path' => 'datatype/lang/datatype_ja-JP.js',
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
      'path' => 'datatype/lang/datatype-date_es.js',
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
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-child.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-child',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_da' => 
    array (
      'path' => 'datatype/lang/datatype_da.js',
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
      'path' => 'datatype/lang/datatype_es-PE.js',
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
      'path' => 'datatype/lang/datatype_de.js',
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
      'name' => 'selector-css3',
      'ext' => false,
    ),
    'lang/datatype-date_fi' => 
    array (
      'path' => 'datatype/lang/datatype-date_fi.js',
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
      'name' => 'lang/datatype-date_fi',
    ),
    'json-parse' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'json/json-parse-min.js',
      'provides' => 
      array (
        'json-parse' => true,
      ),
      'type' => 'js',
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
      'path' => 'datatype/lang/datatype_en-US.js',
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
      'name' => 'lang/datatype_en-US',
    ),
    'lang/datatype_vi-VN' => 
    array (
      'path' => 'datatype/lang/datatype_vi-VN.js',
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
      'name' => 'lang/datatype_vi-VN',
    ),
    'node-focusmanager' => 
    array (
      'path' => 'node-focusmanager/node-focusmanager-min.js',
      'requires' => 
      array (
        0 => 'attribute',
        1 => 'node',
        2 => 'plugin',
        3 => 'node-event-simulate',
        4 => 'event-key',
        5 => 'event-focus',
      ),
      'type' => 'js',
      'name' => 'node-focusmanager',
      'ext' => false,
    ),
    'dd' => 
    array (
      'supersedes' => 
      array (
        0 => 'dd-delegate',
        1 => 'dd-drop-plugin',
        2 => 'dd-constrain',
        3 => 'dd-proxy',
        4 => 'dd-scroll',
        5 => 'dd-ddm-drop',
        6 => 'dd-ddm',
        7 => 'dd-ddm-base',
        8 => 'dd-drag',
        9 => 'dd-plugin',
        10 => 'dd-drop',
      ),
      'path' => 'dd/dd-min.js',
      'rollup' => 4,
      'type' => 'js',
      'ext' => false,
      'submodules' => 
      array (
        'dd-delegate' => 
        array (
          'path' => 'dd/dd-delegate-min.js',
          'requires' => 
          array (
            0 => 'dd-drag',
            1 => 'event-mouseenter',
          ),
          'type' => 'js',
          'optional' => 
          array (
            0 => 'dd-drop-plugin',
          ),
          'pkg' => 'dd',
          'name' => 'dd-delegate',
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
            0 => 'node',
            1 => 'base',
            2 => 'yui-throttle',
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
      'name' => 'dd',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_es-PY' => 
    array (
      'path' => 'datatype/lang/datatype_es-PY.js',
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
      'name' => 'lang/datatype_es-PY',
    ),
    'lang/datatype-date_fr' => 
    array (
      'path' => 'datatype/lang/datatype-date_fr.js',
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
      'name' => 'lang/datatype-date_fr',
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
      'path' => 'datatype/lang/datatype-date_nl-BE.js',
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
      'name' => 'lang/datatype-date_nl-BE',
    ),
    'lang/datatype_el' => 
    array (
      'path' => 'datatype/lang/datatype_el.js',
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
      'name' => 'lang/datatype_el',
    ),
    'lang/datatype_pt-BR' => 
    array (
      'path' => 'datatype/lang/datatype_pt-BR.js',
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
      'name' => 'lang/datatype_pt-BR',
    ),
    'lang/datatype_en' => 
    array (
      'path' => 'datatype/lang/datatype_en.js',
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
      'name' => 'lang/datatype_en',
    ),
    'lang/datatype_es' => 
    array (
      'path' => 'datatype/lang/datatype_es.js',
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
      'name' => 'lang/datatype_es',
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
            0 => 'datasource-local',
            1 => 'cache',
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
            0 => 'datasource-local',
            1 => 'plugin',
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
            0 => 'datasource-local',
            1 => 'plugin',
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
            0 => 'datasource-local',
            1 => 'plugin',
            2 => 'dataschema-json',
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
            0 => 'datasource-local',
            1 => 'get',
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
            0 => 'datasource-local',
            1 => 'io-base',
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
            0 => 'datasource-local',
            1 => 'plugin',
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
      'path' => 'datatype/lang/datatype_fi.js',
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
      'name' => 'lang/datatype_fi',
    ),
    'skin-sam-widget-position' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_en-AU' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-AU.js',
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
      'name' => 'lang/datatype-date_en-AU',
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
      'path' => 'datatype/lang/datatype-date_hi.js',
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
      'name' => 'lang/datatype-date_hi',
    ),
    'widget' => 
    array (
      'requires' => 
      array (
        0 => 'skin-sam-widget',
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
            0 => 'attribute',
            1 => 'event-focus',
            2 => 'base',
            3 => 'node',
            4 => 'classnamemanager',
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
            0 => 'arraylist',
          ),
          'type' => 'js',
          'name' => 'widget-parent',
          'ext' => false,
        ),
        'widget-stack' => 
        array (
          'path' => 'widget/widget-stack-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'skin-sam-widget-stack',
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
          ),
          'type' => 'js',
          'name' => 'widget-position',
          'ext' => false,
        ),
        'widget-child' => 
        array (
          'path' => 'widget/widget-child-min.js',
          'requires' => 
          array (
          ),
          'type' => 'js',
          'name' => 'widget-child',
          'ext' => false,
        ),
        'widget-stdmod' => 
        array (
          'path' => 'widget/widget-stdmod-min.js',
          'requires' => 
          array (
          ),
          'type' => 'js',
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
        0 => 'attribute',
        1 => 'event-focus',
        2 => 'base',
        3 => 'node',
        4 => 'classnamemanager',
      ),
      'type' => 'js',
      'pkg' => 'widget',
      'name' => 'widget-base',
      'ext' => false,
    ),
    'lang/datatype_fr' => 
    array (
      'path' => 'datatype/lang/datatype_fr.js',
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
      'name' => 'lang/datatype_fr',
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
      'pkg' => 'datasource',
      'name' => 'datasource-arrayschema',
      'ext' => false,
    ),
    'lang/datatype-date_es-US' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-US.js',
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
      'name' => 'lang/datatype-date_es-US',
    ),
    'event-key' => 
    array (
      'path' => 'event/event-key-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-key',
      'ext' => false,
    ),
    'lang/datatype-date_es-UY' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-UY.js',
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
      'name' => 'lang/datatype-date_es-UY',
    ),
    'widget-position' => 
    array (
      'path' => 'widget/widget-position-min.js',
      'requires' => 
      array (
      ),
      'type' => 'js',
      'name' => 'widget-position',
      'ext' => false,
    ),
    'lang/datatype-date_es-VE' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-VE.js',
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
      'name' => 'lang/datatype-date_es-VE',
    ),
    'lang/datatype-date_id' => 
    array (
      'path' => 'datatype/lang/datatype-date_id.js',
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
      'name' => 'lang/datatype-date_id',
    ),
    'lang/datatype-date_zh-Hans-CN' => 
    array (
      'path' => 'datatype/lang/datatype-date_zh-Hans-CN.js',
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
      'name' => 'lang/datatype-date_zh-Hans-CN',
    ),
    'lang/datatype-date_nb-NO' => 
    array (
      'path' => 'datatype/lang/datatype-date_nb-NO.js',
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
      'name' => 'lang/datatype-date_nb-NO',
    ),
    'get' => 
    array (
      'path' => 'yui/get-min.js',
      'provides' => 
      array (
        'get' => true,
      ),
      'type' => 'js',
      'ext' => false,
      'pkg' => 'yui',
      'name' => 'get',
      'requires' => 
      array (
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
    'lang/datatype-date_fi-FI' => 
    array (
      'path' => 'datatype/lang/datatype-date_fi-FI.js',
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
      'name' => 'lang/datatype-date_fi-FI',
    ),
    'lang/datatype-date_en-CA' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-CA.js',
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
      'name' => 'lang/datatype-date_en-CA',
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
            1 => 'yui-later',
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
    'widget-child' => 
    array (
      'path' => 'widget/widget-child-min.js',
      'requires' => 
      array (
      ),
      'type' => 'js',
      'name' => 'widget-child',
      'ext' => false,
    ),
    'yui-log' => 
    array (
      'path' => 'yui/yui-log-min.js',
      'provides' => 
      array (
        'yui-log' => true,
      ),
      'type' => 'js',
      'ext' => false,
      'pkg' => 'yui',
      'name' => 'yui-log',
      'requires' => 
      array (
      ),
    ),
    'skin-sam-widget-position-constrain' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position-constrain.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position-constrain',
      'requires' => 
      array (
      ),
    ),
    'widget-anim' => 
    array (
      'path' => 'widget-anim/widget-anim-min.js',
      'requires' => 
      array (
        0 => 'plugin',
        1 => 'anim-base',
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
        1 => 'classnamemanager',
        2 => 'plugin',
        3 => 'node-focusmanager',
        4 => 'skin-sam-node-menunav',
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
    'lang/datatype-date_it' => 
    array (
      'path' => 'datatype/lang/datatype-date_it.js',
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
      'name' => 'lang/datatype-date_it',
    ),
    'lang/datatype-date_ja' => 
    array (
      'path' => 'datatype/lang/datatype-date_ja.js',
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
      'name' => 'lang/datatype-date_ja',
    ),
    'lang/datatype_hi' => 
    array (
      'path' => 'datatype/lang/datatype_hi.js',
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
      'name' => 'lang/datatype_hi',
    ),
    'lang/datatype-date_zh-Hant-HK' => 
    array (
      'path' => 'datatype/lang/datatype-date_zh-Hant-HK.js',
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
      'name' => 'lang/datatype-date_zh-Hant-HK',
    ),
    'lang/datatype_nl-BE' => 
    array (
      'path' => 'datatype/lang/datatype_nl-BE.js',
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
    'lang/datatype-date_ca-ES' => 
    array (
      'path' => 'datatype/lang/datatype-date_ca-ES.js',
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
      'name' => 'lang/datatype-date_ca-ES',
    ),
    'slider' => 
    array (
      'requires' => 
      array (
        0 => 'skin-sam-slider',
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
            1 => 'dd-constrain',
            2 => 'substitute',
            3 => 'skin-sam-slider-base',
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
            0 => 'slider-base',
            1 => 'slider-value-range',
            2 => 'clickable-rail',
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
      'path' => 'datatype/lang/datatype-date_da-DK.js',
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
      'name' => 'lang/datatype-date_da-DK',
    ),
    'lang/datatype_id' => 
    array (
      'path' => 'datatype/lang/datatype_id.js',
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
    'loader-rollup' => 
    array (
      'path' => 'loader/loader-rollup-min.js',
      'provides' => 
      array (
        'loader-rollup' => true,
      ),
      'requires' => 
      array (
        0 => 'loader-base',
      ),
      'type' => 'js',
      'pkg' => 'loader',
      'name' => 'loader-rollup',
      'ext' => false,
    ),
    'event' => 
    array (
      'path' => 'event/event-min.js',
      'requires' => 
      array (
      ),
      'supersedes' => 
      array (
        0 => 'event-base',
        1 => 'event-mousewheel',
        2 => 'event-mouseenter',
        3 => 'event-delegate',
        4 => 'event-key',
        5 => 'event-focus',
        6 => 'event-resize',
      ),
      'expound' => 'node-base',
      'submodules' => 
      array (
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
            0 => 'node-base',
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
            0 => 'node-base',
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
            0 => 'node-base',
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
            0 => 'node-base',
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
            0 => 'node-base',
          ),
          'type' => 'js',
          'pkg' => 'event',
          'name' => 'event-resize',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'plugins' => 
      array (
        'event-synthetic' => 
        array (
          'path' => 'event/event-synthetic-min.js',
          'requires' => 
          array (
            0 => 'node-base',
          ),
          'type' => 'js',
          'name' => 'event-synthetic',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'event',
      'rollup' => 4,
    ),
    'lang/datatype_en-AU' => 
    array (
      'path' => 'datatype/lang/datatype_en-AU.js',
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
      'name' => 'lang/datatype_en-AU',
    ),
    'datasource-io' => 
    array (
      'path' => 'datasource/datasource-io-min.js',
      'requires' => 
      array (
        0 => 'datasource-local',
        1 => 'io-base',
      ),
      'type' => 'js',
      'pkg' => 'datasource',
      'name' => 'datasource-io',
      'ext' => false,
    ),
    'lang/datatype_es-US' => 
    array (
      'path' => 'datatype/lang/datatype_es-US.js',
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
      'name' => 'lang/datatype_es-US',
    ),
    'lang/datatype_it' => 
    array (
      'path' => 'datatype/lang/datatype_it.js',
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
      'name' => 'lang/datatype_it',
    ),
    'lang/datatype-date_ko' => 
    array (
      'path' => 'datatype/lang/datatype-date_ko.js',
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
      'name' => 'lang/datatype-date_ko',
    ),
    'lang/datatype_es-UY' => 
    array (
      'path' => 'datatype/lang/datatype_es-UY.js',
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
      'name' => 'lang/datatype_es-UY',
    ),
    'imageloader' => 
    array (
      'path' => 'imageloader/imageloader-min.js',
      'requires' => 
      array (
        0 => 'base-base',
        1 => 'node-style',
        2 => 'node-screen',
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
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-position-align.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-position-align',
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
      'path' => 'datatype/lang/datatype_ja.js',
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
        0 => 'dom-base',
        1 => 'selector-css2',
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
      'path' => 'datatype/lang/datatype_es-VE.js',
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
      'name' => 'lang/datatype_es-VE',
    ),
    'lang/datatype-date_tr-TR' => 
    array (
      'path' => 'datatype/lang/datatype-date_tr-TR.js',
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
      'path' => 'datatype/lang/datatype-date_id-ID.js',
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
      'name' => 'lang/datatype-date_id-ID',
    ),
    'lang/datatype_nb-NO' => 
    array (
      'path' => 'datatype/lang/datatype_nb-NO.js',
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
      'name' => 'lang/datatype_nb-NO',
    ),
    'lang/datatype_fi-FI' => 
    array (
      'path' => 'datatype/lang/datatype_fi-FI.js',
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
      'name' => 'lang/datatype_fi-FI',
    ),
    'lang/datatype_en-CA' => 
    array (
      'path' => 'datatype/lang/datatype_en-CA.js',
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
      'name' => 'lang/datatype_en-CA',
    ),
    'skin-sam-tabview' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'tabview/assets/skins/sam/tabview.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-tabview',
      'requires' => 
      array (
      ),
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
    'skin-sam-overlay' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'overlay/assets/skins/sam/overlay.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-overlay',
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
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-mouseenter',
      'ext' => false,
    ),
    'lang/datatype-date_es-AR' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-AR.js',
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
        0 => 'dd-drag',
        1 => 'event-mouseenter',
      ),
      'type' => 'js',
      'optional' => 
      array (
        0 => 'dd-drop-plugin',
      ),
      'pkg' => 'dd',
      'name' => 'dd-delegate',
      'ext' => false,
    ),
    'lang/datatype_ko' => 
    array (
      'path' => 'datatype/lang/datatype_ko.js',
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
      'name' => 'lang/datatype_ko',
    ),
    'lang/datatype_ca-ES' => 
    array (
      'path' => 'datatype/lang/datatype_ca-ES.js',
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
      'path' => 'datatype/lang/datatype-date_en-GB.js',
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
      'path' => 'datatype/lang/datatype-date_ms.js',
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
      'name' => 'lang/datatype-date_ms',
    ),
    'lang/datatype_da-DK' => 
    array (
      'path' => 'datatype/lang/datatype_da-DK.js',
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
        1 => 'yui-later',
        2 => 'yui-throttle',
        3 => 'get',
        4 => 'intl-base',
        5 => 'yui-log',
      ),
      'provides' => 
      array (
        'yui-later' => true,
        'yui-base' => true,
        'yui' => true,
        'yui-throttle' => true,
        'get' => true,
        'intl-base' => true,
        'yui-log' => true,
      ),
      'submodules' => 
      array (
        'yui-base' => 
        array (
          'requires' => 
          array (
          ),
          'path' => 'yui/yui-base-min.js',
          'provides' => 
          array (
            'yui-base' => true,
          ),
          'type' => 'js',
          'pkg' => 'yui',
          'expanded' => 
          array (
          ),
          'ext' => false,
          '_parsed' => false,
          'name' => 'yui-base',
        ),
        'yui-later' => 
        array (
          'path' => 'yui/yui-later-min.js',
          'provides' => 
          array (
            'yui-later' => true,
          ),
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'yui-later',
          'requires' => 
          array (
          ),
        ),
        'yui-throttle' => 
        array (
          'path' => 'yui/yui-throttle-min.js',
          'provides' => 
          array (
            'yui-throttle' => true,
          ),
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'yui-throttle',
          'requires' => 
          array (
          ),
        ),
        'get' => 
        array (
          'path' => 'yui/get-min.js',
          'provides' => 
          array (
            'get' => true,
          ),
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'get',
          'requires' => 
          array (
          ),
        ),
        'intl-base' => 
        array (
          'path' => 'yui/intl-base-min.js',
          'provides' => 
          array (
            'intl-base' => true,
          ),
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'intl-base',
          'requires' => 
          array (
          ),
        ),
        'yui-log' => 
        array (
          'path' => 'yui/yui-log-min.js',
          'provides' => 
          array (
            'yui-log' => true,
          ),
          'type' => 'js',
          'ext' => false,
          'pkg' => 'yui',
          'name' => 'yui-log',
          'requires' => 
          array (
          ),
        ),
      ),
      'type' => 'js',
      'ext' => false,
      'name' => 'yui',
      'rollup' => 4,
    ),
    'lang/datatype-date_es-BO' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-BO.js',
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
      'name' => 'lang/datatype-date_es-BO',
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
    'lang/datatype-date_nb' => 
    array (
      'path' => 'datatype/lang/datatype-date_nb.js',
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
      'name' => 'lang/datatype-date_nb',
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
      'path' => 'yui/yui-base-min.js',
      'provides' => 
      array (
        'yui-base' => true,
      ),
      'type' => 'js',
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
      'path' => 'datatype/lang/datatype-date_ms-MY.js',
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
      'name' => 'node-event-simulate',
      'ext' => false,
    ),
    'lang/datatype-date_nl' => 
    array (
      'path' => 'datatype/lang/datatype-date_nl.js',
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
      'name' => 'lang/datatype-date_nl',
    ),
    'compat' => 
    array (
      'path' => 'compat/compat-min.js',
      'requires' => 
      array (
        0 => 'event-base',
        1 => 'dom',
        2 => 'dump',
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
      'name' => 'align-plugin',
      'ext' => false,
    ),
    'lang/datatype-date_es-CL' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-CL.js',
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
      'name' => 'lang/datatype-date_es-CL',
    ),
    'lang/datatype-date_es-CO' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-CO.js',
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
      'name' => 'lang/datatype-date_es-CO',
    ),
    'event-focus' => 
    array (
      'path' => 'event/event-focus-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-focus',
      'ext' => false,
    ),
    'intl-base' => 
    array (
      'path' => 'yui/intl-base-min.js',
      'provides' => 
      array (
        'intl-base' => true,
      ),
      'type' => 'js',
      'ext' => false,
      'pkg' => 'yui',
      'name' => 'intl-base',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_tr-TR' => 
    array (
      'path' => 'datatype/lang/datatype_tr-TR.js',
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
      'path' => 'datatype/lang/datatype_id-ID.js',
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
      'name' => 'lang/datatype_id-ID',
    ),
    'range-slider' => 
    array (
      'path' => 'slider/range-slider-min.js',
      'requires' => 
      array (
        0 => 'slider-base',
        1 => 'slider-value-range',
        2 => 'clickable-rail',
      ),
      'type' => 'js',
      'pkg' => 'slider',
      'name' => 'range-slider',
      'ext' => false,
    ),
    'lang/datatype_ms' => 
    array (
      'path' => 'datatype/lang/datatype_ms.js',
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
      'name' => 'lang/datatype_ms',
    ),
    'lang/datatype-date_en-IE' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-IE.js',
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
      'name' => 'lang/datatype-date_en-IE',
    ),
    'lang/datatype_zh-Hans-CN' => 
    array (
      'path' => 'datatype/lang/datatype_zh-Hans-CN.js',
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
      'name' => 'lang/datatype_zh-Hans-CN',
    ),
    'node-event-delegate' => 
    array (
      'path' => 'node/node-event-delegate-min.js',
      'requires' => 
      array (
        0 => 'node-base',
        1 => 'event-delegate',
      ),
      'type' => 'js',
      'pkg' => 'node',
      'name' => 'node-event-delegate',
      'ext' => false,
    ),
    'lang/datatype_nb' => 
    array (
      'path' => 'datatype/lang/datatype_nb.js',
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
      'name' => 'lang/datatype_nb',
    ),
    'lang/datatype-date_en-IN' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-IN.js',
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
      'name' => 'lang/datatype-date_en-IN',
    ),
    'lang/datatype-date_hi-IN' => 
    array (
      'path' => 'datatype/lang/datatype-date_hi-IN.js',
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
      'name' => 'lang/datatype-date_hi-IN',
    ),
    'lang/datatype_es-AR' => 
    array (
      'path' => 'datatype/lang/datatype_es-AR.js',
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
      'name' => 'lang/datatype_es-AR',
    ),
    'lang/datatype_nl' => 
    array (
      'path' => 'datatype/lang/datatype_nl.js',
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
    'lang/datatype-date_pl' => 
    array (
      'path' => 'datatype/lang/datatype-date_pl.js',
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
      'name' => 'lang/datatype-date_pl',
    ),
    'lang/datatype_en-GB' => 
    array (
      'path' => 'datatype/lang/datatype_en-GB.js',
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
      'name' => 'lang/datatype_en-GB',
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
    'lang/datatype-date_es-EC' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-EC.js',
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
      'name' => 'lang/datatype-date_es-EC',
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
      'path' => 'datatype/lang/datatype_zh-Hant-HK.js',
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
        0 => 'datasource-local',
        1 => 'get',
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
    'lang/datatype-date_pt' => 
    array (
      'path' => 'datatype/lang/datatype-date_pt.js',
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
      'name' => 'lang/datatype-date_pt',
    ),
    'lang/datatype-date_en-JO' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-JO.js',
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
      'path' => 'datatype/lang/datatype_es-BO.js',
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
      'name' => 'lang/datatype_es-BO',
    ),
    'lang/datatype-date_de-AT' => 
    array (
      'path' => 'datatype/lang/datatype-date_de-AT.js',
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
      'name' => 'lang/datatype-date_de-AT',
    ),
    'lang/datatype-date_es-ES' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-ES.js',
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
      'path' => 'datatype/lang/datatype_ms-MY.js',
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
      'name' => 'lang/datatype_ms-MY',
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
    'skin-sam-widget' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_es-CL' => 
    array (
      'path' => 'datatype/lang/datatype_es-CL.js',
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
      'name' => 'lang/datatype_es-CL',
    ),
    'lang/datatype_es-CO' => 
    array (
      'path' => 'datatype/lang/datatype_es-CO.js',
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
      'name' => 'lang/datatype_es-CO',
    ),
    'lang/datatype-date_sv-SE' => 
    array (
      'path' => 'datatype/lang/datatype-date_sv-SE.js',
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
      'name' => 'lang/datatype-date_sv-SE',
    ),
    'lang/datatype_pl' => 
    array (
      'path' => 'datatype/lang/datatype_pl.js',
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
      'name' => 'lang/datatype_pl',
    ),
    'event-synthetic' => 
    array (
      'path' => 'event/event-synthetic-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'name' => 'event-synthetic',
      'ext' => false,
    ),
    'lang/datatype_pt' => 
    array (
      'path' => 'datatype/lang/datatype_pt.js',
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
      'name' => 'lang/datatype_pt',
    ),
    'lang/datatype-date_ro' => 
    array (
      'path' => 'datatype/lang/datatype-date_ro.js',
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
      'name' => 'lang/datatype-date_ro',
    ),
    'lang/datatype-date_ro-RO' => 
    array (
      'path' => 'datatype/lang/datatype-date_ro-RO.js',
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
      'name' => 'lang/datatype-date_ro-RO',
    ),
    'lang/datatype_en-IE' => 
    array (
      'path' => 'datatype/lang/datatype_en-IE.js',
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
      'name' => 'lang/datatype_en-IE',
    ),
    'json-stringify' => 
    array (
      'requires' => 
      array (
        0 => 'yui-base',
      ),
      'path' => 'json/json-stringify-min.js',
      'provides' => 
      array (
        'json-stringify' => true,
      ),
      'type' => 'js',
      'pkg' => 'json',
      'expanded' => 
      array (
        0 => 'yui-base',
      ),
      'ext' => false,
      '_parsed' => false,
      'name' => 'json-stringify',
    ),
    'lang/datatype-date_ru' => 
    array (
      'path' => 'datatype/lang/datatype-date_ru.js',
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
      'name' => 'lang/datatype-date_ru',
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
    'shim-plugin' => 
    array (
      'path' => 'node/shim-plugin-min.js',
      'requires' => 
      array (
        0 => 'node-style',
        1 => 'node-pluginhost',
      ),
      'type' => 'js',
      'name' => 'shim-plugin',
      'ext' => false,
    ),
    'lang/datatype_en-IN' => 
    array (
      'path' => 'datatype/lang/datatype_en-IN.js',
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
      'name' => 'lang/datatype_en-IN',
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
    'lang/datatype_hi-IN' => 
    array (
      'path' => 'datatype/lang/datatype_hi-IN.js',
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
      'name' => 'lang/datatype_hi-IN',
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
      'path' => 'datatype/lang/datatype-date_ar-JO.js',
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
      'path' => 'datatype/lang/datatype-date_fr-BE.js',
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
      'name' => 'lang/datatype-date_fr-BE',
    ),
    'lang/datatype-date_nl-NL' => 
    array (
      'path' => 'datatype/lang/datatype-date_nl-NL.js',
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
      'path' => 'datatype/lang/datatype-date_de-DE.js',
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
      'name' => 'lang/datatype-date_de-DE',
    ),
    'lang/datatype_es-EC' => 
    array (
      'path' => 'datatype/lang/datatype_es-EC.js',
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
      'name' => 'lang/datatype_es-EC',
    ),
    'lang/datatype-date_sv' => 
    array (
      'path' => 'datatype/lang/datatype-date_sv.js',
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
    'skin-sam-slider' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'slider/assets/skins/sam/slider.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-slider',
      'requires' => 
      array (
      ),
    ),
    'tabview-base' => 
    array (
      'path' => 'tabview/tabview-base-min.js',
      'requires' => 
      array (
        0 => 'node-event-delegate',
        1 => 'node-focusmanager',
        2 => 'classnamemanager',
      ),
      'type' => 'js',
      'pkg' => 'tabview',
      'name' => 'tabview-base',
      'ext' => false,
    ),
    'lang/datatype_en-JO' => 
    array (
      'path' => 'datatype/lang/datatype_en-JO.js',
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
      'name' => 'lang/datatype_en-JO',
    ),
    'skin-sam-console' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'console/assets/skins/sam/console.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-console',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_ru-RU' => 
    array (
      'path' => 'datatype/lang/datatype-date_ru-RU.js',
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
      'name' => 'lang/datatype-date_ru-RU',
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
      'path' => 'datatype/lang/datatype_de-AT.js',
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
      'name' => 'lang/datatype_de-AT',
    ),
    'skin-sam-test' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'test/assets/skins/sam/test.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-test',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_es-ES' => 
    array (
      'path' => 'datatype/lang/datatype_es-ES.js',
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
      'name' => 'lang/datatype_es-ES',
    ),
    'lang/datatype-date_fr-CA' => 
    array (
      'path' => 'datatype/lang/datatype-date_fr-CA.js',
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
    'lang/datatype-date_en-MY' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-MY.js',
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
      'name' => 'lang/datatype-date_en-MY',
    ),
    'base' => 
    array (
      'supersedes' => 
      array (
        0 => 'base-base',
        1 => 'base-build',
        2 => 'base-pluginhost',
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
    'lang/datatype_ro' => 
    array (
      'path' => 'datatype/lang/datatype_ro.js',
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
      'name' => 'lang/datatype_ro',
    ),
    'yui-throttle' => 
    array (
      'path' => 'yui/yui-throttle-min.js',
      'provides' => 
      array (
        'yui-throttle' => true,
      ),
      'type' => 'js',
      'ext' => false,
      'pkg' => 'yui',
      'name' => 'yui-throttle',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_ru' => 
    array (
      'path' => 'datatype/lang/datatype_ru.js',
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
      'name' => 'lang/datatype_ru',
    ),
    'lang/datatype-date_th' => 
    array (
      'path' => 'datatype/lang/datatype-date_th.js',
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
      'name' => 'lang/datatype-date_th',
    ),
    'lang/datatype-date_tr' => 
    array (
      'path' => 'datatype/lang/datatype-date_tr.js',
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
      'name' => 'lang/datatype-date_tr',
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
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'tabview/assets/skins/sam/tabview-plugin.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-tabview-plugin',
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
            0 => 'dom-screen',
            1 => 'node-base',
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
            0 => 'dom-base',
            1 => 'selector-css2',
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
            0 => 'node-base',
            1 => 'event-delegate',
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
        'shim-plugin' => 
        array (
          'path' => 'node/shim-plugin-min.js',
          'requires' => 
          array (
            0 => 'node-style',
            1 => 'node-pluginhost',
          ),
          'type' => 'js',
          'name' => 'shim-plugin',
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
          'name' => 'align-plugin',
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
          'name' => 'node-event-simulate',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'node',
      'rollup' => 4,
    ),
    'lang/datatype_sv-SE' => 
    array (
      'path' => 'datatype/lang/datatype_sv-SE.js',
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
      'name' => 'lang/datatype_sv-SE',
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
      'path' => 'datatype/lang/datatype-date_en-NZ.js',
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
      'name' => 'lang/datatype-date_en-NZ',
    ),
    'lang/datatype_sv' => 
    array (
      'path' => 'datatype/lang/datatype_sv.js',
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
    'lang/datatype_ro-RO' => 
    array (
      'path' => 'datatype/lang/datatype_ro-RO.js',
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
      'name' => 'lang/datatype_ro-RO',
    ),
    'anim' => 
    array (
      'supersedes' => 
      array (
        0 => 'anim-node-plugin',
        1 => 'anim-color',
        2 => 'anim-scroll',
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
            0 => 'anim-base',
            1 => 'node-screen',
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
      'path' => 'cache/cache-min.js',
      'requires' => 
      array (
        0 => 'plugin',
      ),
      'type' => 'js',
      'name' => 'cache',
      'ext' => false,
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
      'path' => 'datatype/lang/datatype-date_el-GR.js',
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
      'name' => 'lang/datatype-date_el-GR',
    ),
    'tabview' => 
    array (
      'requires' => 
      array (
        0 => 'widget',
        1 => 'widget-parent',
        2 => 'widget-child',
        3 => 'tabview-base',
        4 => 'skin-sam-tabview',
      ),
      'path' => 'tabview/tabview-min.js',
      'supersedes' => 
      array (
        0 => 'tabview-base',
      ),
      'submodules' => 
      array (
        'tabview-base' => 
        array (
          'path' => 'tabview/tabview-base-min.js',
          'requires' => 
          array (
            0 => 'node-event-delegate',
            1 => 'node-focusmanager',
            2 => 'classnamemanager',
          ),
          'type' => 'js',
          'pkg' => 'tabview',
          'name' => 'tabview-base',
          'ext' => false,
        ),
      ),
      'type' => 'js',
      'skinnable' => true,
      'plugins' => 
      array (
        'tabview-plugin' => 
        array (
          'path' => 'tabview/tabview-plugin-min.js',
          'skinnable' => true,
          'requires' => 
          array (
            0 => 'tabview-base',
            1 => 'skin-sam-tabview-plugin',
          ),
          'type' => 'js',
          'name' => 'tabview-plugin',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'tabview',
      'rollup' => 1,
    ),
    'lang/datatype_th' => 
    array (
      'path' => 'datatype/lang/datatype_th.js',
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
      'name' => 'lang/datatype_th',
    ),
    'lang/datatype_ar-JO' => 
    array (
      'path' => 'datatype/lang/datatype_ar-JO.js',
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
      'name' => 'lang/datatype_ar-JO',
    ),
    'overlay' => 
    array (
      'path' => 'overlay/overlay-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'widget-stdmod',
        2 => 'widget-position',
        3 => 'widget-position-align',
        4 => 'widget-stack',
        5 => 'widget-position-constrain',
        6 => 'skin-sam-overlay',
      ),
      'type' => 'js',
      'name' => 'overlay',
      'ext' => false,
    ),
    'lang/datatype_zh-Hans' => 
    array (
      'path' => 'datatype/lang/datatype_zh-Hans.js',
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
      'name' => 'lang/datatype_zh-Hans',
    ),
    'lang/datatype_zh-Hant' => 
    array (
      'path' => 'datatype/lang/datatype_zh-Hant.js',
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
      'name' => 'lang/datatype_zh-Hant',
    ),
    'lang/datatype-date_vi' => 
    array (
      'path' => 'datatype/lang/datatype-date_vi.js',
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
      'name' => 'lang/datatype-date_vi',
    ),
    'lang/datatype_fr-BE' => 
    array (
      'path' => 'datatype/lang/datatype_fr-BE.js',
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
          'provides' => 
          array (
            'loader-rollup' => true,
          ),
          'requires' => 
          array (
            0 => 'loader-base',
          ),
          'type' => 'js',
          'pkg' => 'loader',
          'name' => 'loader-rollup',
          'ext' => false,
        ),
        'loader-yui3' => 
        array (
          'path' => 'loader/loader-yui3-min.js',
          'provides' => 
          array (
            'loader-yui3' => true,
          ),
          'requires' => 
          array (
            0 => 'loader-base',
          ),
          'type' => 'js',
          'pkg' => 'loader',
          'name' => 'loader-yui3',
          'ext' => false,
        ),
        'loader-base' => 
        array (
          'path' => 'loader/loader-base-min.js',
          'provides' => 
          array (
            'loader-base' => true,
          ),
          'type' => 'js',
          'ext' => false,
          'pkg' => 'loader',
          'name' => 'loader-base',
          'requires' => 
          array (
          ),
        ),
      ),
      'type' => 'js',
      'ext' => false,
      'name' => 'loader',
      'rollup' => 3,
    ),
    'lang/datatype_nl-NL' => 
    array (
      'path' => 'datatype/lang/datatype_nl-NL.js',
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
      'name' => 'lang/datatype_nl-NL',
    ),
    'lang/datatype_tr' => 
    array (
      'path' => 'datatype/lang/datatype_tr.js',
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
      'name' => 'lang/datatype_tr',
    ),
    'yui-later' => 
    array (
      'path' => 'yui/yui-later-min.js',
      'provides' => 
      array (
        'yui-later' => true,
      ),
      'type' => 'js',
      'ext' => false,
      'pkg' => 'yui',
      'name' => 'yui-later',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_zh-Hant-TW' => 
    array (
      'path' => 'datatype/lang/datatype-date_zh-Hant-TW.js',
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
      'name' => 'lang/datatype-date_zh-Hant-TW',
    ),
    'lang/datatype_de-DE' => 
    array (
      'path' => 'datatype/lang/datatype_de-DE.js',
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
      'path' => 'console/lang/console_en.js',
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
      'name' => 'lang/console_en',
    ),
    'lang/datatype-date_en-PH' => 
    array (
      'path' => 'datatype/lang/datatype-date_en-PH.js',
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
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-mousewheel',
      'ext' => false,
    ),
    'lang/datatype_ru-RU' => 
    array (
      'path' => 'datatype/lang/datatype_ru-RU.js',
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
      'name' => 'lang/datatype_ru-RU',
    ),
    'lang/console_es' => 
    array (
      'path' => 'console/lang/console_es.js',
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
      'name' => 'lang/console_es',
    ),
    'querystring-parse' => 
    array (
      'path' => 'querystring/querystring-parse-min.js',
      'requires' => 
      array (
        0 => 'yui-base',
        1 => 'array-extras',
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
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-parent.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-parent',
      'requires' => 
      array (
      ),
    ),
    'sortable' => 
    array (
      'path' => 'sortable/sortable-min.js',
      'requires' => 
      array (
        0 => 'dd-delegate',
        1 => 'dd-drop-plugin',
        2 => 'dd-proxy',
      ),
      'type' => 'js',
      'name' => 'sortable',
      'ext' => false,
    ),
    'skin-sam-node-menunav' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'node-menunav/assets/skins/sam/node-menunav.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-node-menunav',
      'requires' => 
      array (
      ),
    ),
    'skin-sam-widget-stdmod' => 
    array (
      'after' => 
      array (
        0 => 'cssreset',
        1 => 'cssfonts',
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-stdmod.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stdmod',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype_fr-CA' => 
    array (
      'path' => 'datatype/lang/datatype_fr-CA.js',
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
      'name' => 'lang/datatype_fr-CA',
    ),
    'lang/datatype_en-MY' => 
    array (
      'path' => 'datatype/lang/datatype_en-MY.js',
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
          'provides' => 
          array (
            'json-parse' => true,
          ),
          'type' => 'js',
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
          'provides' => 
          array (
            'json-stringify' => true,
          ),
          'type' => 'js',
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
      'path' => 'loader/loader-yui3-min.js',
      'provides' => 
      array (
        'loader-yui3' => true,
      ),
      'requires' => 
      array (
        0 => 'loader-base',
      ),
      'type' => 'js',
      'pkg' => 'loader',
      'name' => 'loader-yui3',
      'ext' => false,
    ),
    'lang/datatype-date_fr-FR' => 
    array (
      'path' => 'datatype/lang/datatype-date_fr-FR.js',
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
      'name' => 'lang/datatype-date_fr-FR',
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
      'pkg' => 'anim',
      'name' => 'anim-xy',
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
    'lang/datatype_vi' => 
    array (
      'path' => 'datatype/lang/datatype_vi.js',
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
    'lang/datatype-date_it-IT' => 
    array (
      'path' => 'datatype/lang/datatype-date_it-IT.js',
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
      'name' => 'lang/datatype-date_it-IT',
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
      'path' => 'datatype/lang/datatype_en-NZ.js',
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
      'name' => 'lang/datatype_en-NZ',
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
    'widget-parent' => 
    array (
      'path' => 'widget/widget-parent-min.js',
      'requires' => 
      array (
        0 => 'arraylist',
      ),
      'type' => 'js',
      'name' => 'widget-parent',
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
      'pkg' => 'datasource',
      'name' => 'datasource-cache',
      'ext' => false,
    ),
    'test' => 
    array (
      'path' => 'test/test-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'substitute',
        1 => 'node',
        2 => 'json',
        3 => 'event-simulate',
        4 => 'skin-sam-test',
      ),
      'type' => 'js',
      'name' => 'test',
      'ext' => false,
    ),
    'lang/datatype-date_ko-KR' => 
    array (
      'path' => 'datatype/lang/datatype-date_ko-KR.js',
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
      'name' => 'lang/datatype-date_ko-KR',
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
    'widget-stdmod' => 
    array (
      'path' => 'widget/widget-stdmod-min.js',
      'requires' => 
      array (
      ),
      'type' => 'js',
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
      'path' => 'datatype/lang/datatype-date_pl-PL.js',
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
      'name' => 'lang/datatype-date_pl-PL',
    ),
    'lang/datatype_el-GR' => 
    array (
      'path' => 'datatype/lang/datatype_el-GR.js',
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
          'name' => 'selector-css3',
          'ext' => false,
        ),
      ),
      'ext' => false,
      'name' => 'dom',
      'rollup' => 4,
    ),
    'loader-base' => 
    array (
      'path' => 'loader/loader-base-min.js',
      'provides' => 
      array (
        'loader-base' => true,
      ),
      'type' => 'js',
      'ext' => false,
      'pkg' => 'loader',
      'name' => 'loader-base',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_es-MX' => 
    array (
      'path' => 'datatype/lang/datatype-date_es-MX.js',
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
      'name' => 'lang/datatype-date_es-MX',
    ),
    'slider-base' => 
    array (
      'path' => 'slider/slider-base-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'widget',
        1 => 'dd-constrain',
        2 => 'substitute',
        3 => 'skin-sam-slider-base',
      ),
      'type' => 'js',
      'pkg' => 'slider',
      'name' => 'slider-base',
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
      'path' => 'datatype/lang/datatype-date_en-SG.js',
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
      'name' => 'lang/datatype-date_en-SG',
    ),
    'lang/datatype_en-PH' => 
    array (
      'path' => 'datatype/lang/datatype_en-PH.js',
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
      'name' => 'lang/datatype_en-PH',
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
        2 => 'cssreset-context',
        3 => 'cssfonts-context',
      ),
      'path' => 'widget/assets/skins/sam/widget-stack.css',
      'type' => 'css',
      'ext' => false,
      'name' => 'skin-sam-widget-stack',
      'requires' => 
      array (
      ),
    ),
    'lang/datatype-date_ar' => 
    array (
      'path' => 'datatype/lang/datatype-date_ar.js',
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
      'name' => 'lang/datatype-date_ar',
    ),
    'event-resize' => 
    array (
      'path' => 'event/event-resize-min.js',
      'requires' => 
      array (
        0 => 'node-base',
      ),
      'type' => 'js',
      'pkg' => 'event',
      'name' => 'event-resize',
      'ext' => false,
    ),
    'tabview-plugin' => 
    array (
      'path' => 'tabview/tabview-plugin-min.js',
      'skinnable' => true,
      'requires' => 
      array (
        0 => 'tabview-base',
        1 => 'skin-sam-tabview-plugin',
      ),
      'type' => 'js',
      'name' => 'tabview-plugin',
      'ext' => false,
    ),
    'lang/datatype-date_th-TH' => 
    array (
      'path' => 'datatype/lang/datatype-date_th-TH.js',
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
      'name' => 'lang/datatype-date_th-TH',
    ),
    'lang/datatype_fr-FR' => 
    array (
      'path' => 'datatype/lang/datatype_fr-FR.js',
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
      'name' => 'lang/datatype_fr-FR',
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
      'pkg' => 'node',
      'name' => 'node-screen',
      'ext' => false,
    ),
  ),
); ?>