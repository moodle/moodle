Theming the user interface in SimpleSAMLphp
===========================================

<!--
	This file is written in Markdown syntax.
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

In SimpleSAMLphp every part that needs to interact with the user by using a web page, uses templates to present the XHTML. SimpleSAMLphp comes with a default set of templates that presents a anonymous look.

You may create your own theme, where you add one or more template files that will override the default ones. This document explains how to achieve that.


How themes work
--------------------

If you want to customize the UI, the right way to do that is to create a new **theme**. A theme is a set of templates that can be configured to override the default templates.

### Configuring which theme to use

In `config.php` there is a configuration option that controls theming. Here is an example:

	'theme.use' 		=> 'fancymodule:fancytheme',

The `theme.use` parameter points to which theme that will be used. If some functionality in SimpleSAMLphp needs to present UI in example with the `logout.php` template, it will first look for `logout.php` in the `theme.use` theme, and if not found it will all fallback to look for the base templates.

All required templates SHOULD be available as a base in the `templates` folder, and you SHOULD never change the base templates. To customize UI, add a new theme within a module that overrides the base templates, instead of modifying it.

### Templates that include other files

A template file may *include* other files. For example all the default templates will include a header and footer: the `login.php` template will first include `includes/header.php` then present the login page, and then include `includes/footer.php`.

SimpleSAMLphp allows themes to override the included templates files only, if needed. That means you can create a new theme `fancytheme` that includes only a header and footer. The header file refers to the CSS files, which means that a simple way of making a new look on SimpleSAMLphp is to create a new theme, and copy the existing header, but point to your own CSS instead of the default CSS.


Creating your first theme
-------------------------

The first thing you need to do is having a SimpleSAMLphp module to place your theme in. If you do not have a module already, create a new one:

	cd modules
	mkdir mymodule
	cd mymodule
	touch default-enable

Then within this module, you can create a new theme named `fancytheme`.

	cd modules/mymodule
	mkdir -p themes/fancytheme/default/includes

Now, configure SimpleSAMLphp to use your new theme in `config.php`:

	'theme.use' 		=> 'mymodule:fancytheme',

Next, we create `themes/fancytheme/default/includes`, and copy the header file from the base theme:

	cp templates/includes/header.php modules/mymodule/themes/fancytheme/default/includes/

In the `modules/mymodule/themes/fancytheme/default/includes/header.php` type in something and go to the SimpleSAMLphp front page to see that your new theme is in use.

A good start is to modify the reference to the default CSS:

	<link rel="stylesheet" type="text/css" href="/<?php echo $this->data['baseurlpath']; ?>resources/default.css" />

to in example:

	<link rel="stylesheet" type="text/css" href="/<?php echo $this->data['baseurlpath']; ?>resources/fancytheme/default.css" />


Examples
---------------------

To override the frontpage body, add the file:

	modules/mymodule/themes/fancytheme/default/frontpage.php

In the path above `default` means that the frontpage template is not part of any modules. If you are replacing a template that is part of a module, then use the module name instead of `default`.

For example, to override the `preprodwarning` template, (the file is located in `modules/preprodwarning/templates/warning.php`), you need to add a new file:

	modules/mymodule/themes/fancytheme/preprodwarning/warning.php


Say in a module `foomodule`, some code requests to present the `bar.php` template, SimpleSAMLphp will:

 1. first look in your theme for a replacement: `modules/mymodule/themes/fancytheme/foomodule/bar.php`.
 2. If not found, it will use the base template of that module: `modules/foomodule/templates/bar.php`


Adding resource files
---------------------

You can put resource files within the www folder of your module, to make your module completely independent with included css, icons etc.

```
modules
└───mymodule
    └───lib
    └───themes
    └───www
        └───logo.png
        └───style.css
```

Reference these resources in your custom PHP templates under `themes/fancytheme` by using a generator for the URL:
```
<?php echo SimpleSAML\Module::getModuleURL('mymodule/logo.png'); ?>
```

Example for a custom CSS stylesheet file:
```
<link rel="stylesheet" href="<?php echo SimpleSAML\Module::getModuleURL('mymodule/style.css'); ?>">
```

Migrating to Twig templates
---------------------------

In version 1.15, a new templating system based on [Twig](https://twig.symfony.com/) was introduced. As modules migrate, it will become necessary for themes to include both the old templating style described above and new Twig-based templates.

Twig works by extending a base template, which can itself include other partial templates. Some of the content of the old `includes/header.php` template is now located in a separate `_header.twig` file. This can be customized by copying it from the base template:

	cp templates/_header.twig modules/mymodule/themes/fancytheme/default/

If you need to make more extensive customizations to the base template, you should copy it from the base theme:

	cp templates/base.twig modules/mymodule/themes/fancytheme/default/

Any references to `$this->data['baseurlpath']` in old-style templates can be replaced with `{{baseurlpath}}` in Twig templates. Likewise, references to `\SimpleSAML\Module::getModuleURL()` can be replaced with `{{baseurlpath}}module.php/mymodule/...`

Within templates each module is defined as a separate namespace matching the module name. This allows one template to reference templates from other modules using Twig's `@namespace_name/template_path` notation. For instance, a template in `mymodule` can include the widget template from the `yourmodule` module using the notation `@yourmodule/widget.twig`. A special namespace, `__parent__`, exists to allow theme developers to more easily extend a module's stock template.

Even more advanced changes can be made by defining a theme controller in `config.php`:

    'theme.controller' => '\SimpleSAML\Module\mymodule\FancyThemeController',

This requires you to implement `\SimpleSAML\XHTML\TemplateControllerInterface.php` in your module's `lib`-directory.
The class can then modify the Twig Environment and the variables passed to the theme's templates. In short, this allows you to set additional global variables and to write your own Twig filters and functions.

See the [Twig documentation](https://twig.symfony.com/doc/1.x/templates.html) for more information on using variables and expressions in Twig templates, and the SimpleSAMLphp wiki for [our conventions](https://github.com/simplesamlphp/simplesamlphp/wiki/Twig-conventions).

The wiki also includes some information on [migrating translations](https://github.com/simplesamlphp/simplesamlphp/wiki/Migrating-translation-in-Twig) and [migrating templates](https://github.com/simplesamlphp/simplesamlphp/wiki/Twig:-Migrating-templates).
