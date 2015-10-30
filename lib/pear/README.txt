PEAR Libraries
====================================================================


This directory (lib/pear) contains unmodified copies of some
libraries from the standard PEAR distribution (http://pear.php.net).

We include these in Moodle solely for the convenience of sites that
may not have PEAR installed.

If this directory is DELETED from Moodle then Moodle will search
the standard PHP directories and use the PEAR libraries there instead.


In detail, the libraries added here are:

- PEAR HTML_Quickform:
    - Current version: 3.2.6
    - by Bertrand Mansion, Adam Daniel, Alexey Borzov
    - License: PHP (Permission given to Moodle to redistribute under GPL)
    - http://pear.php.net/package/HTML_Quickform
- PEAR HTML_Quickform_Renderer_Tableless:
    - Current version: 0.3.4
    - by Mark Wiesemann
    - License: PHP (Permission given to Moodle to redistribute under GPL)
    - http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless
- PEAR HTML_QuickForm_DHTMLRulesTableless:
    - Current version: 0.1.2
    - by Alexey Borzov, Adam Daniel, Bertrand Mansion, Justin Patrin, Mark Wiesemann
    - License: PHP (Permission given to Moodle to redistribute under GPL)
    - http://pear.php.net/package/HTML_QuickForm_DHTMLRulesTableless
- PEAR HTML_Common:
    - Current version: 1.2.2
    - by Adam Daniel, Bertrand Mansion, Klaus Guenther, Alexey Borzov,
    - License: PHP  (Permission given to Moodle to redistribute under GPL)
    - http://pear.php.net/package/HTML
- PEAR main class:
    - Current version: 1.4.5
    - by Stig Bakken, Thomas V.V.Cox, Pierre-Alain Joye,
      Greg Beaver and Martin Jansen
    - License: PHP
    - http://pear.php.net/package/PEAR
- PEAR HTTP_WebDAV_Server
    - Current version: HEAD @ 28-01-2008
    - by Hartmut Holzgraefe and Christian Stocker
    - License: BSD
    - http://pear.php.net/package/HTTP_WebDAV_Server
- PEAR HTML_AJAX:
    - Current version: 0.5.6
    - by Elizabeth Smith, Arpad Ray, Joshua Eichorn, David Coallier and Laurent Yaish
    - License: LGPL
    - http://pear.php.net/package/HTML_AJAX/
- PEAR Auth_RADIUS:
    - Current version: 1.0.6 (2008-04-13)
    - by Michael Bretterklieber
    - License: BSD
    - http://pear.php.net/package/Auth_RADIUS
- PEAR Crypt_CHAP:
    - Current Version: 1.0.1 (2007-03-14)
    - by Michael Bretterklieber
    - License: BSD
    - http://pear.php.net/package/Crypt_CHAP
- PEAR XML_Parser:
    - Current Version: 1.3.2 (2009-01-21)
    - by Stephan Schmidt, Stig Bakken, Tomas V.V.Cox
    - License: BSD
    - http://pear.php.net/package/XML_Parser



----------------------------------------------------------------
A NOTE TO DEVELOPERS
================================================================

We must not use these classes directly ever. Instead we must build
and use wrapper classes to isolate Moodle code from internal PEAR
implementations, allowing us to migrate if needed to other
libraries in the future. For an example of wrapped classes,
see the excel.class.lib file, that includes code to build
Excel files using the cool library inside PEAR, but using
the old calls used before Moodle 1.6 to maintain compatibility.

Please, don't forget it! Always use wrapper classes/functions!

Ciao,
Eloy Lafuente, 2005-12-17 :-)



----------------------------------------------------------------
A NOTE ON THE PHP LICENSE AND MOODLE
================================================================

Everything in Moodle in pure GPL.  This pear directory is the only
part of the distribution that is not.

There is some question about how PHP-licensed software can be
included within a GPL-licensed distribution like Moodle, specifically
the clause that annoyingly says no derivative of the software can
include the name PHP.

We don't intend to rename Moodle to anything of the sort, obviously,
but to help people downstream who could possibly want to do so,
we have sought special permission from the authors of these classes
to allow us an exemption on this point so that we don't need to
change our nice clean GPL license.

Several authors have given Moodle explicit permission to distribute
their PHP-licensed PEAR classes in the Moodle distribution, allowing
anybody using these classes ONLY as part of the Moodle distribution
exemption from clauses of the PHP license that could cause
conflict with the main GNU Public License that Moodle uses.

We are still waiting to hear back from the others but we assume
for now that it will likewise be OK.

If you are at all worried about this situation you can simply delete
this directory from Moodle and it will use your installed PEAR
libraries instead.

Cheers,
Martin Dougiamas, 2 April 2006
