MOODLE-SPECIFIC PEAR MODIFICATIONS
==================================

XML/Parser
=================
1/ changed ereg_ to preg_
* http://cvs.moodle.org/moodle/lib/pear/XML/Parser.php.diff?r1=1.1&r2=1.2


Quickforms
==========
Full of our custom hacks, no way to upgrade to latest upstream.
Most probably we will stop using this library in the future.

Just dropping a couple of links here, for whenever we update/switch or whatever:
- QF2: https://github.com/pear/HTML_QuickForm2 (https://moodle.org/mod/forum/discuss.php?d=200124)
- Quickform (fork): https://github.com/openpsa/quickform

MDL-20876 - replaced split() with explode() or preg_split() where appropriate
MDL-40267 - Moodle core_text strlen functions used for range rule rule to be utf8 safe.
MDL-46467 - $mform->hardfreeze causes labels to loose their for HTML attribute
MDL-52081 - made all constructors PHP7 compatible
MDL-52826 - Remove onsubmit events pointing to the global validation functions and script
            tag moved after the HTML
MDL-50484 - _getPersistantData() returns id with _persistant prefixed to element id.
MDL-55123 - corrected call to non-static functions in HTML_QuickForm to be PHP7.1-compliant
MDL-60281 - replaced deprecated create_function() with lambda functions for PHP7.2 compatibility
MDL-70711 - removed unnecessary if-else conditional block in HTML_QuickForm as the given
            condition always evaluates to false due to the deprecated get_magic_quotes_gpc()
            which always returns false
MDL-70457 - PHP 7.4 curly brackets string access fix.
MDL-71126 - Quiz: Manual grading page size preference can get stuck at 0
            Including in this change:
             - New positiveint regex rule to check if the value is a positive integer
MDL-76102 / MDL-77081
            PHP 8.1 passing null to a non-nullable argument of a built-in function is deprecated
MDL-77164 - PHPdocs corrections
MDL-78145 - PHP 8.2 compliance. Added a missing class property that still need to be declared
            to avoid dynamic properties deprecated error warning.
            And also remove the $_elementIdx because it is not needed in Moodle code.
MDL-78527 - Adding a sixth parameter to allow groups to use attributes.

Pear
====
It was decided that we will not upgrade this library from upstream any more, see MDL-52465

Changed constructors in classes PEAR and PEAR_ERROR to be __construct().
MDL-60281 - replaced deprecated function each() with foreach loop for PHP7.2 compatibility
