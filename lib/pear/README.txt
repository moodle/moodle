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



----------------------------------------------------------------
A NOTE ON THE PHP LICENSE AND MOODLE
================================================================

There is some question about how PHP-licensed software can be 
included within a GPL-licensed distribution like Moodle, specifically
the clause that says no derivative of the software can include the 
name PHP.  We don't intend to rename Moodle to anything of the 
sort, obviously, but to help people downstream who could possibly 
want to do so, we have sought special permission from the authors
of these classes to allow us an exemption on this point so that 
we don't need to change our nice clean GPL license.

Xavier Noguer has given Moodle explicit permission to distribute 
his OLE PEAR class in the Moodle distribution, and allows any
body using this class ONLY as part of the Moodle distribution
exemption from clauses of the PHP license that could cause
conflict with the main GNU Public License that Moodle uses.

We are still waiting to hear back from Stig, Thomas or Pierre-Alain,
but we assume for now that it will likewise be OK.

Cheers,
Martin Dougiamas, 2 April 2006
