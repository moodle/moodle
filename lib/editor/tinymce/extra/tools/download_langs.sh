#!/bin/bash
mkdir temp
mkdir temp/tinylangs
mkdir temp/langs
for lang in 'sq' 'ar' 'az' 'be' 'bn' 'nb' 'bs' 'br' 'bg' 'ca' 'ch' 'zh' 'hr' 'cs' 'da' 'dv' 'nl' 'en' 'et' 'fi' 'fr' 'gl' 'de' 'el' 'gu' 'he' 'hi' 'hu' 'is' 'id' 'ia' 'it' 'ja' 'ko' 'lv' 'lt' 'mk' 'ms' 'mn' 'se' 'no' 'nn' 'fa' 'pl' 'pt' 'ro' 'ru' 'sc' 'sr' 'ii' 'si' 'sk' 'sl' 'es' 'sv' 'ta' 'tt' 'te' 'th' 'tr' 'tw' 'uk' 'cy' 'vi'
do
 wget "http://services.moxiecode.com/i18n/download.aspx?format=xml&code=$lang&product=tinymce" -O temp/tinylangs/$lang.xml
done