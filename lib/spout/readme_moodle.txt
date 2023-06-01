Description of Spout library import
=========================================
* Download / Clone from https://github.com/box/spout/
* Only include the src/Spout directory.
* Update lib/thirdpartylibs.xml with the latest version.

2023/05/26
----------
MDL-78262 lib_spout: Update box/spout to address PHP 8.1 deprecation
This change is a direct pull from an upstream fix:
https://github.com/openspout/openspout/commit/e75f6f73012b81fd5fee6107d0af9e86c458448e
This addresses the deprecation of str_replace() in PHP 8.1.

2022/11/25
----------
Imported PHP 8.1 patch from OpenSpout/OpenSpout 4.8.1
https://github.com/openspout/openspout/commit/64a09a748d04992d63b38712599a9d8742bd77f7

2022/10/27
----------
Changes:
Box/Spout has been archived and is no longer maintained,
MDL-73624 needs to fix with a couple of minor changes to
Writer/WriterAbstract.php. The changes replace rawurldecode() with
rawurlencode() in lines 143 and 144.
by Meirza <meirza.arson@moodle.com>
MDL-76494 compatibility for PHP 8.1

2021/09/01
----------
Update to v3.3.0 (MDL-71707)
by Paul Holden <paulh@moodle.com>

2020/12/07
----------
Update to v3.1.0 (MDL-70302)
by Peter Dias <peter@moodle.com>

2019/06/17
----------
Update to v3.0.1 (MDL-65762)
by Adrian Greeve <adrian@moodle.com>

2017/10/10
----------
Updated to v2.7.3 (MDL-60288)
by Ankit Agarwal <ankit.agrr@gmail.com>

2016/09/20
----------
Updated to v2.6.0 (MDL-56012)
by Adrian Greeve <adrian@moodle.com>
