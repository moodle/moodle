Description of Typo3 libraries (v 4.7.19) import into Moodle

Changes:
1/ hacked relative include of class.t3lib_utility_debug.php

Procedure:
1/ download latest version form http://typo3.org/download/
2/ copy csconvtbl/*, unidata/* and all other necessary files we use
3/ run our phpunit tests with and without mbstring PHP extension

Local changes (to verify/apply with new imports):

- MDL-67017: PHP 7.4 compatibility. Curly brackets.
    Remove all the deprecated curly bracket uses {} to access to strings/arrays
    by key. We haven't looked how this is fixed upstream because plans include
    to remove this library from core (see MDL-65809)

- MDL-63967: PHP 7.3 compatibility.
    lib/typo3/class.t3lib_div.php: FILTER_FLAG_SCHEME_REQUIRED is deprecated and
    implied with FILTER_VALIDATE_URL. This is fixed upstream since Typo 6, with
    the file class now under \TYPO3\CMS\Core\Utility\GeneralUtility.

skodak, stronk7, moodler
