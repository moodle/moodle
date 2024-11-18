Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/Gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/Gettext/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gettext/gettext/v/stable.svg)](https://packagist.org/packages/gettext/gettext)
[![Total Downloads](https://poser.pugx.org/gettext/gettext/downloads.svg)](https://packagist.org/packages/gettext/gettext)
[![Monthly Downloads](https://poser.pugx.org/gettext/gettext/d/monthly.png)](https://packagist.org/packages/gettext/gettext)
[![License](https://poser.pugx.org/gettext/gettext/license.svg)](https://packagist.org/packages/gettext/gettext)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/496dc2a6-43be-4046-a283-f8370239dd47/big.png)](https://insight.sensiolabs.com/projects/496dc2a6-43be-4046-a283-f8370239dd47)

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com> (MIT License)

Gettext is a PHP (>=5.4) library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

## Installation

With composer (recomended):

```
composer require gettext/gettext
```

If you don't use composer in your project, you have to download and place this package in a directory of your project. You need to install also [gettext/languages](https://github.com/mlocati/cldr-to-gettext-plural-rules). Then, include the autoloaders of both projects in any place of your php code:

```php
include_once "libs/gettext/src/autoloader.php";
include_once "libs/cldr-to-gettext-plural-rules/src/autoloader.php";
```

## Classes and functions

This package contains the following classes:

* `Gettext\Translation` - A translation definition
* `Gettext\Translations` - A collection of translations
* `Gettext\Extractors\*` - Import translations from various sources (po, mo, php, js, etc)
* `Gettext\Generators\*` - Export translations to various formats (po, mo, php, json, etc)
* `Gettext\Translator` - To use the translations in your php templates instead the [gettext extension](http://php.net/gettext)
* `Gettext\GettextTranslator` - To use the [gettext extension](http://php.net/gettext)

## Usage example

```php
use Gettext\Translations;

//import from a .po file:
$translations = Translations::fromPoFile('locales/gl.po');

//edit some translations:
$translation = $translations->find(null, 'apple');

if ($translation) {
	$translation->setTranslation('Mazá');
}

//export to a php array:
$translations->toPhpArrayFile('locales/gl.php');

//and to a .mo file
$translations->toMoFile('Locale/gl/LC_MESSAGES/messages.mo');
```

If you want use this translations in your php templates without using the gettext extension:

```php
use Gettext\Translator;

//Create the translator instance
$t = new Translator();

//Load your translations (exported as PhpArray):
$t->loadTranslations('locales/gl.php');

//Use it:
echo $t->gettext('apple'); // "Mazá"

//If you want use global functions:
$t->register();

echo __('apple'); // "Mazá"
```

To use this translations with the gettext extension:

```php
use Gettext\GettextTranslator;

//Create the translator instance
$t = new GettextTranslator();

//Set the language and load the domain
$t->setLanguage('gl');
$t->loadDomain('messages', 'Locale');

//Use it:
echo $t->gettext('apple'); // "Mazá"

//Or use the gettext functions
echo gettext('apple'); // "Mazá"

//If you want use the global functions
$t->register();

echo __('apple'); // "Mazá"

//And use sprintf/strtr placeholders
echo __('Hello %s', 'world'); //Hello world
echo __('Hello {name}', ['{name}' => 'world']); //Hello world
```

The benefits of using the functions provided by this library (`__()` instead `_()` or `gettext()`) are:

* You are using the same functions, no matter whether the translations are provided by gettext extension or any other method.
* You can use variables easier because `sprintf` functionality is included. For example: `__('Hello %s', 'world')` instead `sprintf(_('Hello %s'), 'world')`.
* You can also use named placeholders if the second argument is an array. For example: `__('Hello %name%', ['%name%' => 'world'])` instead of `strtr(_('Hello %name%'), ['%name%' => 'world'])`.

## Translation

The `Gettext\Translation` class stores all information about a translation: the original text, the translated text, source references, comments, etc.

```php
// __construct($context, $original, $plural)
$translation = new Gettext\Translation('comments', 'One comment', '%s comments');

$translation->setTranslation('Un comentario');
$translation->setPluralTranslation('%s comentarios');

$translation->addReference('templates/comments/comment.php', 34);
$translation->addComment('To display the amount of comments in a post');

echo $translation->getContext(); // comments
echo $translation->getOriginal(); // One comment
echo $translation->getTranslation(); // Un comentario

// etc...
```

## Translations

The `Gettext\Translations` class stores a collection of translations:

```php
$translations = new Gettext\Translations();

//You can add new translations using the array syntax
$translations[] = new Gettext\Translation('comments', 'One comment', '%s comments');

//Or using the "insert" method
$insertedTranslation = $translations->insert('comments', 'One comment', '%s comments');

//Find a specific translation
$translation = $translations->find('comments', 'One comment');

//Edit headers, domain, etc
$translations->setHeader('Last-Translator', 'Oscar Otero');
$translations->setDomain('my-blog');
```

## Extractors

The extrators allows to fetch gettext values from any source. For example, to scan a .po file:

```php
$translations = new Gettext\Translations();

//From a file
Gettext\Extractors\Po::fromFile('locales/en.po', $translations);

//From a string
$string = file_get_contents('locales2/en.po');
Gettext\Extractors\Po::fromString($string, $translations);
```

The better way to use extractors is using the magic methods of `Gettext\Translations`:

```php
//Create a Translations instance using a po file
$translations = Gettext\Translations::fromPoFile('locales/en.po');

//Add more messages from other files
$translations->addFromPoFile('locales2/en.po');
```

The available extractors are the following:

Name | Description | Example
---- | ----------- | --------
**Blade**          | Scans a Blade template (For laravel users). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/blade/input.php)
**Csv**            | Gets the messages from csv. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Csv.csv)
**CsvDictionary**  | Gets the messages from csv (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/CsvDictionary.csv)
**Jed**            | Gets the messages from a json compatible with [Jed](http://slexaxton.github.com/Jed/). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Jed.json)
**JsCode**         | Scans javascript code looking for all gettext functions (the same than PhpCode but for javascript). You can use [the javascript gettext-translator library](https://github.com/oscarotero/gettext-translator) | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/jscode/input.js)
**Json**           | Gets the messages from json compatible with [gettext-translator](https://github.com/oscarotero/gettext-translator). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Json.json)
**JsonDictionary** | Gets the messages from a json (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/JsonDictionary.json)
**Mo**             | Gets the messages from MO. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Mo.mo)
**PhpArray**       | Gets the messages from a php file that returns an array. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/PhpArray.php)
**PhpCode**        | Scans php code looking for all gettext functions (see `translator_functions.php`). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/phpcode/input.php)
**Po**             | Gets the messages from PO. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Po.po)
**Twig**           | To scan a Twig template. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/twig/input.php)
**Xliff**          | Gets the messages from [xliff (2.0)](http://docs.oasis-open.org/xliff/xliff-core/v2.0/os/xliff-core-v2.0-os.html). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Xliff.xlf)
**Yaml**           | Gets the messages from yaml. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Yaml.yml)
**YamlDictionary** | Gets the messages from a yaml (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/YamlDictionary.yml)
**VueJs**          | Gets the messages from a VueJs template. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/vuejs/input.vue)

## Generators

The generators export a `Gettext\Translations` instance to any format (po, mo, array, etc).

```php
//Save to a file
Gettext\Generators\Po::toFile($translations, 'locales/en.po');

//Return as a string
$content = Gettext\Generators\Po::toString($translations);
file_put_contents('locales/en.po', $content);
```

Like extractors, the better way to use generators is using the magic methods of `Gettext\Translations`:

```php
//Extract messages from a php code file
$translations = Gettext\Translations::fromPhpCodeFile('templates/index.php');

//Export to a po file
$translations->toPoFile('locales/en.po');

//Export to a po string
$content = $translations->toPoString();
file_put_contents('locales/en.po', $content);
```

The available generators are the following:

Name | Description | Example
---- | ----------- | --------
**Csv**            | Exports to csv. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Csv.csv)
**CsvDictionary**  | Exports to csv (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/CsvDictionary.csv)
**Json**           | Exports to json, compatible with [gettext-translator](https://github.com/oscarotero/gettext-translator). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Json.json)
**JsonDictionary** | Exports to json (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/JsonDictionary.json)
**Mo**             | Exports to Mo. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Mo.mo)
**PhpArray**       | Exports to php code that returns an array. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/PhpArray.php)
**Po**             | Exports to Po. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Po.po)
**Jed**            | Exports to json format compatible with [Jed](http://slexaxton.github.com/Jed/). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Jed.json)
**Xliff**          | Exports to [xliff (2.0)](http://docs.oasis-open.org/xliff/xliff-core/v2.0/os/xliff-core-v2.0-os.html). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Xliff.xlf)
**Yaml**           | Exports to yaml. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/Yaml.yml)
**YamlDictionary** | Exports to yaml (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/po/YamlDictionary.yml)

## Translator

The class `Gettext\Translator` implements the gettext functions in php. Useful if you don't have the native gettext extension for php or want to avoid problems with it. You can load the translations from a php array file or using a `Gettext\Translations` instance:

```php
use Gettext\Translator;

//Create a new instance of the translator
$t = new Translator();

//Load the translations using any of the following ways:

// 1. from php files (generated by Gettext\Extractors\PhpArray)
$t->loadTranslations('locales/gl.php');

// 2. using the array directly
$array = include 'locales/gl.php';
$t->loadTranslations($array);

// 3. using a Gettext\Translations instance (slower)
$translations = Gettext\Translations::fromPoFile('locales/gl.po');
$t->loadTranslations($translations);

//Now you can use it in your templates
echo $t->gettext('apple');
```

## GettextTranslator

The class `Gettext\GettextTranslator` uses the gettext extension. It's useful because combines the performance of using real gettext functions but with the same API than `Translator` class, so you can switch to one or other translator deppending of the environment without change code of your app.

```php
use Gettext\GettextTranslator;

//Create a new instance
$t = new GettextTranslator();

//It detects the environment variables to set the locale, but you can change it:
$t->setLanguage('gl');

//Load the domains:
$t->loadDomain('messages', 'project/Locale');
//this means you have the file "project/Locale/gl/LC_MESSAGES/messages.po"

//Now you can use it in your templates
echo $t->gettext('apple');
```

## Global functions

To ease the use of translations in your php templates, you can use the provided functions:

```php
//Register the translator to use the global functions
$t->register();

echo __('apple'); // it's the same than $t->gettext('apple');
```

You can scan the php files containing these functions and extract the values with the PhpCode extractor:

```html
<!-- index.php -->
<html>
	<body>
		<?= __('Hello world'); ?>
	</body>
</html>
```


## Merge translations

To work with different translations you may want merge them in an unique file. There are two ways to do this:

The simplest way is adding new translations:

```php
use Gettext\Translations;

$translations = Translations::fromPoFile('my-file1.po');
$translations->addFromPoFile('my-file2.po');
```

A more advanced way is merge two `Translations` instances:

```php
use Gettext\Translations;

//Create a new Translations instances with our translations.

$translations1 = Translations::fromPoFile('my-file1.po');
$translations2 = Translations::fromPoFile('my-file2.po');

//Merge one inside other:
$translations1->mergeWith($translations2);

//Now translations1 has all values
```

The second argument of `mergeWith` defines how the merge will be done. Use the `Gettext\Merge` constants to configure the merging:

Constant | Description
--------- | -----------
`Merge::ADD` | Adds the translations from `$translations2` that are missing
`Merge::REMOVE` | Removes the translations missing in `$translations2`
`Merge::HEADERS_ADD` | Adds the headers from `$translations2` that are missing
`Merge::HEADERS_REMOVE` | Removes the headers missing in `$translations2`
`Merge::HEADERS_OVERRIDE` | Overrides the headers with the values of `$translations2`
`Merge::LANGUAGE_OVERRIDE` | Set the language defined in `$translations2`
`Merge::DOMAIN_OVERRIDE` | Set the domain defined in `$translations2`
`Merge::TRANSLATION_OVERRIDE` | Override the translation and plural translations with the value of `$translation2`
`Merge::COMMENTS_OURS` | Use only the comments of `$translation1`
`Merge::COMMENTS_THEIRS` | Use only the comments of `$translation2`
`Merge::EXTRACTED_COMMENTS_OURS` | Use only the extracted comments of `$translation1`
`Merge::EXTRACTED_COMMENTS_THEIRS` | Use only the extracted comments of `$translation2`
`Merge::FLAGS_OURS` | Use only the flags of `$translation1`
`Merge::FLAGS_THEIRS` | Use only the flags of `$translation2`
`Merge::REFERENCES_OURS` | Use only the references of `$translation1`
`Merge::REFERENCES_THEIRS` | Use only the references of `$translation2`

Example:

```php
use Gettext\Translations;
use Gettext\Merge;

//Scan the php code to find the latest gettext translations
$phpTranslations = Translations::fromPhpCodeFile('my-templates.php');

//Get the translations of the code that are stored in a po file
$poTranslations = Translations::fromPoFile('locale.po');

//Merge the translations from the po file using the references from `$phpTranslations`:
$translations->mergeWith($poTranslations, Merge::REFERENCES_OURS);

//Now save a po file with the result
$translations->toPoFile('locale.po');
```

Note, if the second argument is not defined, the default value is `Merge::DEFAULTS` that's equivalent to `Merge::ADD | Merge::HEADERS_ADD`.

## Use from CLI

There's a Robo task to use this library from the command line interface: https://github.com/oscarotero/GettextRobo

## Use in the browser

If you want to use your translations in the browser, there's a javascript translator: https://github.com/oscarotero/gettext-translator

## Third party packages

Twig integration:

* [jaimeperez/twig-configurable-i18n](https://packagist.org/packages/jaimeperez/twig-configurable-i18n)
* [cemerson/translator-twig-extension](https://packagist.org/packages/cemerson/translator-twig-extension)

Framework integration:

* [Laravel 5](https://packagist.org/packages/eusonlito/laravel-gettext)
* [CakePHP 3](https://packagist.org/packages/k1low/po)
* [Symfony 2](https://packagist.org/packages/mablae/gettext-bundle)

[add your package](https://github.com/oscarotero/Gettext/issues/new)

## Contributors

Thanks to all [contributors](https://github.com/oscarotero/Gettext/graphs/contributors) specially to [@mlocati](https://github.com/mlocati).

## Donations

If this library is useful for you, consider to donate to the author.

[Buy me a beer :beer:](https://www.paypal.me/oscarotero)

Thanks in advance!
