QUIZ FILE FORMATS FOR IMPORT/EXPORT
------------------------------------

This directory contains plug-in sub-modules to add 
import-export formats to Moodle quizzes.

Each sub-module must contain at least a format.php file 
containing a class that contains functions for reading, 
writing, importing and exporting quiz questions.

For correct operation the class name must be based on the
name of the containing directory, e.g.,

directory: webct
class:  class quiz_format_webct extends quiz_default_format { 

Most of them are based on the class found in mod/quiz/format.php. 
See the comments therein for more information.

