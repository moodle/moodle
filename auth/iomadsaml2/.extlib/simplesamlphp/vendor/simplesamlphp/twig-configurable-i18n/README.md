# Configurable Internationalization with Twig

This is a hack to get Twig's native internationalization extension to
work with other translation systems than PHP's native _gettext_
extension. That way, you can use other implementation, such as
[gettext/gettext](https://github.com/oscarotero/Gettext) or even your
own one, provided a function is available to perform translations for
singular strings, and another one for plural strings.

## Installation

If you are using composer, installation is as easy as requiring this
package:

```
composer require jaimeperez/twig-configurable-i18n
```

If you are not using composer, you will need to manually include all
(four) source files in this project from your script, and make sure
you have the `twig/extension` package installed as well.

## Usage

Using this extension is extremely simple, and won't require you to
modify your code or existing templates, but just a couple of
modifications to the PHP scripts where you want internationalization. 

First, **create aliases** for two classes so that the ones defined in
this extension override the default ones:

```php
use SimpleSAML\TwigConfigurableI18n\Twig\Environment as Twig_Environment;
use SimpleSAML\TwigConfigurableI18n\Twig\Extensions\Extension\I18n as Twig_Extensions_Extension_I18n;
```

The first line allows you to redefine the twig environment class, so
that you can keep the configuration options you pass to the constructor.
The second line installs this extension instead of the native one. Your
code will use now this extension without further modifications, and
PHP's native gettext will continue to be used.

Now, you are ready to configure the extension to use different
translation functions. You will do that by passing options to the Twig
environment class you have just imported:

```php
$twig = new Twig_Environment($loader, array(
    'translation_function' => 'translate',
    'translation_function_plural' => 'translate_plural'
));
```

These are the only two supported options. `translation_function` lets
you specify a function that is capable to translate strings in singular,
being equivalent to `gettext()`. On the other hand,
`translate_function_plural` lets you specify a function to handle the
translation of strings in plural. This one is equivalent to
`ngettext()`.

You are all set. Twig will use the `translate()` and
`translate_plural()` functions you have just specified, instead of the
native `gettext()` and `ngettext()` functions. Of course, you will need
to specify some functions that actually **exist**, but that depends on
what implementation of gettext you want to use. If, for example, you
plan to use Oscar Otero's pure-PHP implementation
[gettext/gettext](https://github.com/oscarotero/Gettext), you can do the
following:

```php
use \Gettext\Translator as Translator;

...

$this->translator = new Translator();
$this->translator->register();

...

$twig = new Twig_Environment($loader, array(
    'translation_function' => '__',
    'translation_function_plural' => 'n__'
));
```
