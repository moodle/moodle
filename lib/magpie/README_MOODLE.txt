This folder is the MagpieRSS news feed client library
http://magpierss.sourceforge.net/
Moodle's rss_client block uses these libraries to download, parse and cache remote new feeds.

Magpie version 0.72 added to Moodle 1.6dev on 20051213

=============================================================
Changes - see MDL-7045:
* ETag and Last-Modified http field names are not case sensitive anymore - should improve caching
* Fixed some minor undefined warnings
* Tralining newlines are stripped from ETag and Last-Modified headers (discovered by Matthew Bockol),
  we should be sending valid headers when fetching feed updates now, yay!


Fixes not reported upstream yet.

skodak
16 October 2006
=============================================================
Bug fix - see MDL-18644

Bug in function array_change_key_case(), using assignment (=) instead of
equality (==).

Reported upstream as bug #2705796

David Mudrak (mudrd8mz)
=============================================================

