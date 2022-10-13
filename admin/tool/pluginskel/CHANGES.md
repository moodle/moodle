## 1.5.3 ##

* No actual change. I am just using this as a final test for the moodle-plugin-release
  workflow functionality. Sorry for the noise.

## 1.5.2 ##

* Single class (instance, trait) files do not need file-level phpDocs block even if
  they have side-effect and load other libraries manually.
* Monolog logger update to the latest version 2.2 as we now require Moodle 3.9 so PHP
  is always 7.2 or higher.
* Plugin types in the wizard form selector are now alphabetically ordered - credit
  goes to Daniel Neis Araujo and Jonathan Champ.
* Added tests for Moodle 3.11 and ability to set 3.11 as required Moodle version.
* Fixed coding style issues detected by phpcs in the plugin as well as in the
  generated code.

## 1.5.1 ##

* New recipe section `templates` allowing to generate mustache templates.
* Fixed standard boilerplate of all custom skeleton templates and re-enabled CI
  prechecks for them.

## 1.5.0 ##

* New recipe section `external` can be used to describe external functions provided by
  the plugin.
* New recipe section `services` can be used to describe pre-built web services
  provided by the plugin.
* The `--recipe` argument has been deprecated. The path to the recipe file should be
  newly provided via positional argument of the generate.php script.
* CLI generator script now also supports relative paths to files as arguments.
* New `--list-files` argument allows to display list of to-be generated files.
* New `--file` argument allows to display the contents of given generated file.
* New `--decode` argument allows to display decoded structure of given YAML file.
* Added ability to generate single declaration files that have no side effects.
* Travis-CI prechecks were replaced with Github Actions.
* Github Actions are also used to precheck generated skeletons, not only the tool
  itself. Various detected coding style issues were fixed in both places.
* Generated README files now contain information about how and where the generated
  plugin should be installed to. Credit goes to Jan Dageförde.
* Generated install.xml files fixed to contain EOL at EOF. This reflects the upstream
  fix MDL-70931.
* Spyc library replaced with Symfony Yaml component for recipe files parsing.

## 1.4.0 ##

* Fixed deprecation warnings in unit tests under Moodle 3.10. Credit goes to @ewallah.
* Fixed tests syntax to be compatible with PHPUnit 8.5.
* Added support for generating version.php code requiring Moodle 3.10.

## 1.3.0 ##

* Added support for generating contenttype plugin templates. Credit goes to Ferran
  Recio (@ferranrecio).
* Moodle versions 3.8 and 3.9 can be selected as required versions.
* URLs in boilerplates use the HTTPS protocol explicitly.
* Fixed typos and errors. Credit goes to @kritisingh1.

## 1.2.3 ##

* Fixed generating skeletons for activity modules. Credit goes to Leo Auri (@leoauri)
  for the fix.
* Fixed travis-ci configuration. Credit goes to Jonathan Champ (@jrchamp).
* Added support for generating minimal db/install.xml files for activity modules.

## 1.2.2 ##

* Added support for generating version.php code requiring Moodle 3.7

## 1.2.1 ##

* Fixed bug - auth plugins not setting the authtype property. Credit goes to
  Geoffrey Van Wyk (@systemovich) for the report and the fix suggestion.
* Fixed bug #90 - the name of the XMLDB upgrade function for activity modules.
* Added support for generating version.php code requiring Moodle 3.6.

## 1.2.0 ##

* Privacy API implemented. The plugin does not store any personal data.
* Fixed bug #87 - invalid language file name for activity modules.

## 1.1.1 ##

* Fixed a bug leading to generating the provider.php file with a syntax error in some
  cases.

## 1.1.0 ##

* Added support to generate privacy API related code (refer to cli/example.yaml).
  Special thanks to Michael Hughes for the initial implementation.
* Improved the component type and name fields usability - autodisplay the plugin type
  prefix so that it is more intuitive what the name field should hold.
* Added support to generate plugins requiring Moodle 3.4 and 3.5
* Make mustache loader path configurable, allowing better integration with moosh.
  Credit goes to Tomasz Muras.

## 1.0.0 ##

* Added support to generate plugins requiring Moodle 3.3 and 3.2.
* Added support for setting default values of some recipe file form fields
* Fixed the risk of having the generated ZIP file corrupted with debugging data
* Fixed some formal coding style violations


## 0.9.0 ##

* Initial version submitted to the Moodle plugins directory as a result of
  GSOC 2016
