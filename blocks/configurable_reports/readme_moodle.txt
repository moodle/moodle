=======================
CodeMirror
=======================
https://github.com/codemirror/codemirror

Instructions to import 'CodeMirror' into Moodle:

1. Download the latest release from https://github.com/codemirror/codemirror

2. Execute 'npm install'

3. Copy 'lib/codemirror.js' into 'amd/src/codemirror.js':

   3.a. Add /*eslint-ignore*/ to the beginning of the file.


4. Copy 'mode/sql/sql.js' into 'amd/src/sql.js':

   4.a. Add /*eslint-ignore*/ to the beginning of the file.

   4.b. Replace lib reference reference
   ------------------
    define(["../../lib/codemirror"], mod);
   ------------------

   to
   ------------------
    define(["block_configurable_reports/codemirror"], mod);
   ------------------

5. Copy all the styles from 'lib/codemirror.css' into the
"CodeMirror styles" section in 'styles.css'.


6. Execute grunt to compile js
   grunt js


=======================
DataTables
=======================
https://cdn.datatables.net/releases.html

Instructions to import 'DataTables' into Moodle:

1. Download the latest release for 'jquery.dataTables.js' from
https://cdn.datatables.net/releases.html

2. Copy 'jquery.dataTables.js' into 'amd/src/jquery.dataTables.js':

   2.a. Add /*eslint-ignore*/ to the beginning of the file.

3. Download 'css/jquery.dataTables.css' and replace all the references from:
   ------------------
    url('../images/xxxxx.png')
   ------------------

   to
   ------------------
    url([[pix:block_configurable_reports|datatable/xxxxx]])
   ------------------

4. Copy the new 'css/jquery.dataTables.css' after replacing the references to
the image folder into the "DataTables styles" section in 'styles.css'.

5. Execute grunt to compile js
   grunt js



=======================
TableSorter
=======================
https://github.com/Mottie/tablesorter

Instructions to import 'TableSorter' into Moodle:

1. Download the latest release from https://github.com/Mottie/tablesorter

2. Copy 'js/jquery.tablesorter.js' into 'amd/src/jquery.tablesorter.js':

   2.a. Add the following lines to the beginning of the file:
   ------------------
   /*eslint-ignore*/
   (function(factory){
     if (typeof define === 'function' && define.amd){
        define(['jquery'], factory);
     } else if (typeof module === 'object' && typeof module.exports === 'object'){
        module.exports = factory(require('jquery'));
     } else {
        factory(jQuery);
     }
   }(function(jQuery){
   ------------------

   2.b. Add the following lines at the end of the file:
   ------------------
    return jQuery.tablesorter;}));
   ------------------


3. Execute grunt to compile js
   grunt js


=======================
pChart
=======================
http://www.pchart.net/

Instructions to import 'pChart' into Moodle:

1. Access to the 'lib/' folder

2. Remove the 'pChart2' folder.

3. Download the latest release for this library compatible with PHP 7 from
https://github.com/bozhinov/pChart2.0-for-PHP7 using the following command:

git clone -b 7.x-compatible https://github.com/bozhinov/pChart2.0-for-PHP7.git pChart2

   3.a. Remove the folders 'examples' and 'cache' and the file 'index.php'.

