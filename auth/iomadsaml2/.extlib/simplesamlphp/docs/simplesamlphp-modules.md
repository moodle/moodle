SimpleSAMLphp modules
==================================================

<!-- 
	This file is written in Markdown syntax. 
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

This document describes how the module system in SimpleSAMLphp
works. It descibes what types of modules there are, how they are
configured, and how to write new modules.

Overview
--------

There are currently three parts of SimpleSAMLphp which can be stored in 
modules - authentication sources, authentication processing filters and 
themes. There is also support for defining hooks - functions run at 
specific times. More than one thing can be stored in a single module. 
There is also support for storing supporting files, such as templates 
and dictionaries, in modules.

The different functionalities which can be created as modules will be 
described in more detail in the following sections; what follows is a 
short introduction to what you can do with them:

 - Authentication sources implement different methods for 
   authenticating users, for example simple login forms which 
   authenticate against a database backend, or login methods which use 
   client-side certificates. 
 - Authentication processing filters perform various tasks after the 
   user is authenticated and has a set of attributes. They can add, 
   remove and modify attributes, do additional authentication checks, 
   ask questions of the user, +++. 
 - Themes allow you to package custom templates for multiple modules 
   into a single module.


## Module layout

Each SimpleSAMLphp module is stored in a directory under the
`modules`-directory. The module directory contains the following
directories and files:

default-disable
:   The presence of this file indicates that the module is disabled
    by default. It can be enabled using the `module.enable`
    option in `config.php`.

default-enable
:   The presence of this file indicates that the module is enabled
    by default. It can be disabled using the `module.enable`
    option in `config.php`.

dictionaries
:   This directory contains dictionaries which belong to this
    module. To use a dictionary stored in a module, the extended tag
    names can be used:
    `{<module name>:<dictionary name>:<tag name>}` For
    example, `{example:login:hello}` will look up `hello` in
    `modules/example/dictionaries/login.php`.

:   It is also possible to specify
    `<module name>:<dictionary name>` as the default
    dictionary when instantiating the `\SimpleSAML\XHTML\Template`
    class.

hooks
:   This directory contains hook functions for this module. Each
    file in this directory represents a single function. See the
    hook-section in the documentation for more information.

lib
:   This directory contains classes which belong to this module.
    All classes must be named in the following pattern:
    `\SimpleSAML\Module\<module name>\<class name>` When looking up the filename of
    a class, SimpleSAMLphp will search for `<class name>` in the `lib`
    directory. Underscores in the class name will be translated into
    slashes.

:   Thus, if SimpleSAMLphp needs to load a class named
    `\SimpleSAML\Module\example\Auth\Source\Example`, it will load the file named
    `modules/example/lib/Auth/Source/Example.php`.

templates
:   These are module-specific templates. To use one of these
    templates, specify `<module name>:<template file>.php`
    as the template file in the constructor of
    `\SimpleSAML\XHTML\Template`. For example, `example:login-form.php`
    is translated to the file
    `modules/example/templates/default/login-form.php`. Note that
    `default` in the previous example is defined by the `theme.use`
    configuration option.

themes
:   This directory contains themes the module defines. A single
    module can define multiple themes, and these themes may override
    all templates in all modules. Each subdirectory of `themes` defines
    a theme. The theme directory contains a subdirectory for each
    module. The templates stored under `simplesamlphp/templates` can be
    overridden by a directory named `default`.

:   To use a theme provided by a module, the `theme.use`
    configuration option should be set to
    `<module name>:<theme name>`.

:   When using the theme `example:blue`, the template
    `templates/default/login.php` will be overridden by
    `modules/example/themes/blue/default/login.php`, while the template
    `modules/core/templates/default/loginuserpass.php` will be
    overridden by
    `modules/example/themes/blue/core/loginuserpass.php`.

www
:   All files stored in this directory will be available by
    accessing the URL
    `https://.../simplesamlphp/module.php/<module name>/<file name>`.
    For example, if a script named `login.php` is stored in
    `modules/example/www/`, it can be accessed by the URL
    `https://.../simplesamlphp/module.php/example/login.php`.

:   To retrieve this URL, the
    `SimpleSAML\Module::getModuleURL($resource)`-function can be used.
    This function takes in a resource on the form `<module>/<file>`.
    This function will then return a URL to the given file in the
    `www`-directory of `module`.


## Authentication sources

An authentication source is used to authenticate a user and receive a 
set of attributes belonging to this user. In a single-signon setup, the 
authentication source will only be called once, and the attributes 
belonging to the user will be cached until the user logs out.

Authentication sources are defined in `config/authsources.php`. This 
file contains an array of `name => configuration` pairs. The name is 
used to refer to the authentication source in metadata. When 
configuring an IdP to authenticate against an authentication source, 
\the `auth` option should be set to this name. The configuration for an 
authentication source is an array. The first element in the array 
identifies the class which implements the authentication source. The 
remaining elements in the array are configuration entries for the 
authentication source.

A typical configuration entry for an authentication source looks like 
this:

    'example-static' => [
      /* This maps to modules/exampleauth/lib/Auth/Source/Static.php */
      'exampleauth:StaticSource',
    
      /* The following is configuration which is passed on to
       * the exampleauth:StaticSource authentication source. */
      'uid' => 'testuser',
      'eduPersonAffiliation' => ['member', 'employee'],
      'cn' => ['Test User'],
    ],

To use this authentication source in a SAML 2.0 IdP, set the
`auth`-option of the IdP to `'example-static'`:

    '__DYNAMIC:1__' => [
      'host' => '__DEFAULT__',
      'privatekey' => 'example.org.pem',
      'certificate' => 'example.org.crt',
      'auth' => 'example-static',
    ],

### Creating authentication sources

This is described in a separate document:

  * [Creating authentication sources](simplesamlphp-authsource)


Authentication processing filters
---------------------------------

*Authentication processing filters* is explained in a separate document:

  * [Authentication processing filters](simplesamlphp-authproc)



## Themes

This feature allows you to collect all your custom templates in one 
place. The directory structure is like this: 
`modules/<thememodule>/themes/<theme>/<module>/<template>` 
`thememodule` is the module where you store your theme, while `theme` 
is the name of the theme. A theme is activated by setting the 
`theme.use` configuration option to `<thememodule>:<theme>`. `module` 
is the module the template belongs to, and `template` is the template 
in that module.

For example, `modules/example/themes/test/core/loginuserpass.php` 
replaces `modules/core/templates/default/loginuserpass.php`. 
`modules/example/themes/test/default/frontpage.php` replaces 
`templates/default/frontpage.php`. This theme can be activated by 
setting `theme.use` to `example:test`.

## Hook interface

The hook interface allows you to call a hook function in all enabled 
modules which define that hook. Hook functions are stored in a 
directory called 'hooks' in each module directory. Each hook is 
stored in a file named `hook_<hook name>.php`, and each file defines a 
function named `<module name>_hook_<hook name>`.

Each hook function accepts a single argument. This argument will be 
passed by reference, which allows each hook to update that argument.

There is currently a single user of the hook interface - the front 
page. The front page defines a hook named `frontpage`, which allows 
modules to add things to the different sections on the front page. For 
an example of this, see the `modules/modinfo/hooks/hook_frontpage.php` 
file in the
[modinfo module](https://github.com/simplesamlphp/simplesamlphp-module-modinfo).



