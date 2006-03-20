FILE FORMATS FOR QUESTION IMPORT/EXPORT
------------------------------------

This directory contains plug-in sub-modules to add 
import-export formats for Moodle questions

Each sub-module must contain at least a format.php file 
containing a class that contains functions for reading, 
writing, importing and exporting questions.

For correct operation the class name must be based on the
name of the containing directory, e.g.,

directory: webct
class:  class qformat_webct extends qformat_default { 

Most of them are based on the class found in question/format.php. 
See the comments therein for more information.

