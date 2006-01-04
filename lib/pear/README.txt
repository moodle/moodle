This files are part of the PEAR package (http://pear.php.net).

In detail, the libraries added here are:

- PEAR Spreadsheet_Excel_Writer classes:
    - Current version: 0.9.0
    - by Xavier Noguer and Mika Tuupola
    - License: LGPL
    - http://pear.php.net/package/Spreadsheet_Excel_Writer
- PEAR OLE classes:
    - Current version: 0.5
    - by Xavier Noguer
    - License: PHP
    - http://pear.php.net/package/OLE
- PEAR main class:
    - Current version: 1.4.5
    - by Stig Bakken, Thomas V.V.Cox, Pierre-Alain Joye, 
      Greg Beaver and Martin Jansen
    - License: PHP
    - http://pear.php.net/package/PEAR

We must not use these classes directly ever. Instead we must build 
some wrapper classes to isolate Moodle code from internal PEAR
implementations, allowing us to migrate if needed to other 
libraries in the future. For an example of wrapped classes, 
see the excel.class.lib file, that includes code to build 
Excel files using the cool library inside PEAR, but using
the old calls used before Moodle 1.6 to maintain compatibility.

Please, don't forget it! Always use wrapper classes/functions!

Ciao, Eloy Lafuente, 2005-12-17 :-)
