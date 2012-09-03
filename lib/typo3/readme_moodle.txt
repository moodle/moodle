Description of Typo3 libraries (v 4.7.4) import into Moodle

Changes: none

skodak, stronk7


Previous changes:

25 June 2010 - Martin D (4.3.0RC1)
  I renamed getURL to getUrl since it was being called that way everywhere.
  I added a check to avoid notices on lib/typo3/class.t3lib_cs.php line 976

22 October 2011 - Petr Skoda (4.5.0)
  reapplied getURL --> getUrl in class.t3lib_div.php line 2992
  reintroduced check to avoid notices on class.t3lib_cs.php line 1031
