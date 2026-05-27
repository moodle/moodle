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
    - License: PHP 2.02 or PHP v4 (BSD-3-Clause)
    - http://pear.php.net/package/HTML_Quickform
- PEAR HTML_Quickform_Renderer_Tableless:
    - Current version: 0.3.4
    - by Mark Wiesemann
    - License: PHP 3.01 or PHP v4 (BSD-3-Clause)
    - http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless
- PEAR HTML_QuickForm_DHTMLRulesTableless:
    - Current version: 0.1.2
    - by Alexey Borzov, Adam Daniel, Bertrand Mansion, Justin Patrin, Mark Wiesemann
    - License: PHP 3.01 or PHP v4 (BSD-3-Clause)
    - http://pear.php.net/package/HTML_QuickForm_DHTMLRulesTableless
- PEAR HTML_Common:
    - Current version: 1.2.2
    - by Adam Daniel, Bertrand Mansion, Klaus Guenther, Alexey Borzov,
    - License: PHP 2.02 or PHP v4 (BSD-3-Clause)
    - http://pear.php.net/package/HTML_Common
- PEAR main class:
    - Current version: 1.4.5
    - by Stig Bakken, Thomas V.V.Cox, Pierre-Alain Joye,
      Greg Beaver and Martin Jansen
    - License: BSD-3-Clause
    - http://pear.php.net/package/PEAR
- PEAR HTML_AJAX:
    - Current version: 0.5.6
    - by Elizabeth Smith, Arpad Ray, Joshua Eichorn, David Coallier and Laurent Yaish
    - License: LGPL
    - http://pear.php.net/package/HTML_AJAX/

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

Some bundled PEAR libraries use older PHP License versions. The PHP
Group's license page states that PHP software distributed under PHP
License versions 2.01, 2.02, 3.0, and 3.01 may, at the user's option,
be used under the PHP License version 4:

    https://www.php.net/license/

PHP License version 4 is the Modified BSD License, has SPDX identifier
BSD-3-Clause, and is compatible with the GNU General Public License
(GPL). Moodle distributes these PHP-licensed PEAR libraries on that
compatibility path while retaining the original upstream headers as
provenance.

Earlier versions of this file described author-specific permissions
that were sought in 2006. The PHP License version 4 compatibility path
is the current basis documented here.

If you are at all worried about this situation you can simply delete
this directory from Moodle and it will use your installed PEAR
libraries instead.
