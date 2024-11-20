# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

Previous releases are documented in [github releases](https://github.com/oscarotero/Gettext/releases)

## [4.8.7] - 2022-08-02
### Fixed
- Suppress deprecation error on PHP 8.1 [#280]

## [4.8.6] - 2021-10-19
### Fixed
- Parse PO files with multiline disabled entries [#274]

## [4.8.5] - 2021-07-13
### Fixed
- Prevent adding the same translator comment to multiple functions [#271]

## [4.8.4] - 2021-03-10
### Fixed
- PHP 8 compatibilty [#266]

## [4.8.3] - 2020-11-18
### Fixed
- Blade extractor for Laravel8/Jetstream [#261]

## [4.8.2] - 2019-12-02
### Fixed
- UTF-8 handling for VueJs extractor [#242]

## [4.8.1] - 2019-11-15
### Fixed
- Php error when scanning for a single domain but other string found [#238]

## [4.8.0] - 2019-11-04
### Changed
- Many `private` properties and methods were changed to `protected` in order to improve the extensibility [#231]

### Fixed
- PHP 7.4 support [#230]

## [4.7.0] - 2019-10-07
### Added
- Support for UnitID in Xliff [#221] [#224] [#225]
- Support for scan multiple domains at the same time [#223]

### Fixed
- New lines in windows [#218] [#226]

## [4.6.3] - 2019-07-15
### Added
- Some VueJs extraction improvements and additions [#205], [#213]

### Fixed
- Multiline extractions in jsCode [#200]
- Support for js template literals [#214]
- Fixed tabs in PHP comments [#215]

## [4.6.2] - 2019-01-12
### Added
- New option `facade` in blade extractor to use a facade instead create a blade compiler [#197], [#198]

### Fixed
- Added php-7.3 to travis
- Added VueJS extractor method docblocks for IDEs [#191]

## [4.6.1] - 2018-08-27
### Fixed
- VueJS DOM parsing [#188]
- Javascript parser was unable to extract some functions [#187]

## [4.6.0] - 2018-06-26
### Added
- New extractor for VueJs [#178]

### Fixed
- Do not include empty translations containing the headers in the translator [#182]
- Test enhancement [#177]

## [4.5.0] - 2018-04-23
### Added
- Support for disabled translations

### Fixed
- Added php-7.2 to travis
- Fixed po tests on bigendian [#159]
- Improved comment estraction [#166]
- Fixed incorrect docs to dn__ function [#170]
- Ignored phpcs.xml file on export [#168]
- Improved `@method` docs in `Translations` [#175]

## [4.4.4] - 2018-02-21
### Fixed
- Changed the comment extraction to be compatible with gettext behaviour: the comment must be placed in the line preceding the function [#161]

### Security
- Validate eval input from plural forms [#156]

## [4.4.3] - 2017-08-09
### Fixed
- Handle `NULL` arguments on extract entries in php. For example `dn__(null, 'singular', 'plural')`.
- Fixed the `PhpCode` and `JsCode` extractors that didn't extract `dn__` and `dngettext` entries [#155].
- Fixed the `PhpCode` and `JsCode` extractors that didn't extract `dnpgettext` correctly.

## [4.4.2] - 2017-07-27
### Fixed
- Clone the translations in `Translations::mergeWith` to prevent that the translation is referenced in both places. [#152]
- Fixed escaped quotes in the javascript extractor [#154]

## [4.4.1] - 2017-05-20
### Fixed
- Fixed a bug where the options was not passed correctly to the merging Translations object [#147]
- Unified the plural behaviours between PHP gettext and Translator when the plural translation is unknown [#148]
- Removed the deprecated function `create_function()` and use `eval()` instead

## [4.4.0] - 2017-05-10
### Added
- New option `noLocation` to po generator, to omit the references [#143]
- New options `delimiter`, `enclosure` and `escape_char` to Csv and CsvDictionary extractors and generators [#145]
- Added the missing `dn__()` function [#146]

### Fixed
- Improved the code style including php_codesniffer in development

## [4.3.0] - 2017-03-04
### Added
- Added support for named placeholders (using `strtr`). For example:
  ```php
  __('Hello :name', [':name' => 'World']);
  ```
- Added support for Twig v2
- New function `BaseTranslator::includeFunctions()` to include the functions file without register any translator

### Fixed
- Fixed a bug related with the javascript source extraction with single quotes

[#143]: https://github.com/oscarotero/Gettext/issues/143
[#145]: https://github.com/oscarotero/Gettext/issues/145
[#146]: https://github.com/oscarotero/Gettext/issues/146
[#147]: https://github.com/oscarotero/Gettext/issues/147
[#148]: https://github.com/oscarotero/Gettext/issues/148
[#152]: https://github.com/oscarotero/Gettext/issues/152
[#154]: https://github.com/oscarotero/Gettext/issues/154
[#155]: https://github.com/oscarotero/Gettext/issues/155
[#156]: https://github.com/oscarotero/Gettext/issues/156
[#159]: https://github.com/oscarotero/Gettext/issues/159
[#161]: https://github.com/oscarotero/Gettext/issues/161
[#166]: https://github.com/oscarotero/Gettext/issues/166
[#168]: https://github.com/oscarotero/Gettext/issues/168
[#170]: https://github.com/oscarotero/Gettext/issues/170
[#175]: https://github.com/oscarotero/Gettext/issues/175
[#177]: https://github.com/oscarotero/Gettext/issues/177
[#178]: https://github.com/oscarotero/Gettext/issues/178
[#182]: https://github.com/oscarotero/Gettext/issues/182
[#187]: https://github.com/oscarotero/Gettext/issues/187
[#188]: https://github.com/oscarotero/Gettext/issues/188
[#191]: https://github.com/oscarotero/Gettext/issues/191
[#197]: https://github.com/oscarotero/Gettext/issues/197
[#198]: https://github.com/oscarotero/Gettext/issues/198
[#200]: https://github.com/oscarotero/Gettext/issues/200
[#205]: https://github.com/oscarotero/Gettext/issues/205
[#213]: https://github.com/oscarotero/Gettext/issues/213
[#214]: https://github.com/oscarotero/Gettext/issues/214
[#215]: https://github.com/oscarotero/Gettext/issues/215
[#218]: https://github.com/oscarotero/Gettext/issues/218
[#221]: https://github.com/oscarotero/Gettext/issues/221
[#223]: https://github.com/oscarotero/Gettext/issues/223
[#224]: https://github.com/oscarotero/Gettext/issues/224
[#225]: https://github.com/oscarotero/Gettext/issues/225
[#226]: https://github.com/oscarotero/Gettext/issues/226
[#230]: https://github.com/oscarotero/Gettext/issues/230
[#231]: https://github.com/oscarotero/Gettext/issues/231
[#238]: https://github.com/oscarotero/Gettext/issues/238
[#242]: https://github.com/oscarotero/Gettext/issues/242
[#261]: https://github.com/oscarotero/Gettext/issues/261
[#266]: https://github.com/oscarotero/Gettext/issues/266
[#271]: https://github.com/oscarotero/Gettext/issues/271
[#274]: https://github.com/oscarotero/Gettext/issues/274
[#280]: https://github.com/oscarotero/Gettext/issues/280

[4.8.7]: https://github.com/oscarotero/Gettext/compare/v4.8.6...v4.8.7
[4.8.6]: https://github.com/oscarotero/Gettext/compare/v4.8.5...v4.8.6
[4.8.5]: https://github.com/oscarotero/Gettext/compare/v4.8.4...v4.8.5
[4.8.4]: https://github.com/oscarotero/Gettext/compare/v4.8.3...v4.8.4
[4.8.3]: https://github.com/oscarotero/Gettext/compare/v4.8.2...v4.8.3
[4.8.2]: https://github.com/oscarotero/Gettext/compare/v4.8.1...v4.8.2
[4.8.1]: https://github.com/oscarotero/Gettext/compare/v4.8.0...v4.8.1
[4.8.0]: https://github.com/oscarotero/Gettext/compare/v4.7.0...v4.8.0
[4.7.0]: https://github.com/oscarotero/Gettext/compare/v4.6.3...v4.7.0
[4.6.3]: https://github.com/oscarotero/Gettext/compare/v4.6.2...v4.6.3
[4.6.2]: https://github.com/oscarotero/Gettext/compare/v4.6.1...v4.6.2
[4.6.1]: https://github.com/oscarotero/Gettext/compare/v4.6.0...v4.6.1
[4.6.0]: https://github.com/oscarotero/Gettext/compare/v4.5.0...v4.6.0
[4.5.0]: https://github.com/oscarotero/Gettext/compare/v4.4.4...v4.5.0
[4.4.4]: https://github.com/oscarotero/Gettext/compare/v4.4.3...v4.4.4
[4.4.3]: https://github.com/oscarotero/Gettext/compare/v4.4.2...v4.4.3
[4.4.2]: https://github.com/oscarotero/Gettext/compare/v4.4.1...v4.4.2
[4.4.1]: https://github.com/oscarotero/Gettext/compare/v4.4.0...v4.4.1
[4.4.0]: https://github.com/oscarotero/Gettext/compare/v4.3.0...v4.4.0
[4.3.0]: https://github.com/oscarotero/Gettext/releases/tag/v4.3.0
