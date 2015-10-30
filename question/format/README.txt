Question import/export formats
==============================

This directory contains plug-ins to supprt importing and exporting questions in
a variety of formats.

Each sub-module must contain at least a format.php file containing a class that
contains functions for reading, writing, importing and exporting questions.

For correct operation the class name must be based on the name of the plugin.
For example:

plugin: webct
class:  class qformat_webct extends qformat_default {

Most of them are based on the class found in question/format.php.
See the comments therein for more information.
