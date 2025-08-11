Description of TCPDF library import
===================================
* download library from https://github.com/tecnickcom/TCPDF/tags
* delete tools/ and VERSION
* remove all fonts that were not already present
* visit http://127.0.0.1/lib/tests/other/pdflibtestpage.php and view the pdf
* modify getTCPDFProducer lib/tcpdf/include/tcpdf_static.php to remove the version number

Important
---------
A new version of the libray is being developed @ https://github.com/tecnickcom/tc-lib-pdf . Check periodically when it's ready
and if it's a drop-in replacement for the legacy tcpdf one.

2025/02/04
----------
Upgrade to tcpdf TCPDF 6.8.2 (MDL-84354)
by Stevani Andolo <stevani@hotmail.com.au>

2024/06/16
----------
Upgrade to tcpdf TCPDF 6.7.5 (MDL-81658)
by Daniel Ziegenbergg <daniel.ziegenberg@tuwien.ac.at>

2023/10/23
----------
Upgrade to tcpdf TCPDF 6.6.5 (MDL-79665)
by Paul Holden <paulh@moodle.com>

2023/03/02
----------
Upgrade to tcpdf TCPDF 6.6.2 (MDL-76910)
by Paul Holden <paulh@moodle.com>

2022/09/27
----------
Upgrade to tcpdf TCPDF 6.5.0 (MDL-75468)
by Paul Holden <paulh@moodle.com>

2021/04/20
----------
Upgrade to tcpdf TCPDF 6.4.1 (MDL-70902)
by David Matamoros <davidmc@moodle.com>

2021/02/11
----------
Reduce PDF metadata disclosure (MDL-70735)
by Brendan Heywood <brendan@catalyst-au.net>

2020/12/15
----------
Upgrade to tcpdf TCPDF 6.3.5 (MDL-70294)
by Paul Holden <paulh@moodle.com>

2019/10/20
----------
Upgrade to tcpdf TCPDF 6.3.2 (MDL-66966)
by Eloy Lafuente <stronk7@moodle.org>

2019/05/06
----------
Upgrade to tcpdf TCPDF 6.2.26 (MDL-64794)
by Eloy Lafuente <stronk7@moodle.org>

- https://github.com/tecnickcom/TCPDF/pull/74 has been already merged upstream (6.2.16 and up), so we don't need to apply it.
- https://github.com/tecnickcom/TCPDF/pull/91 has been already merged upstream (6.2.19 and up), so we don't need to apply it.

2017/10/02
----------
Upgrade to tcpdf_php5 TCPDF 6.2.13 (MDL-60237)
by Marina Glancy <marina@moodle.com>

* replaced the calls to function each() deprecated in PHP7.2

2015/09/29
----------
Upgrade to tcpdf_php5 TCPDF 6.2.12 (MDL-51534)
by Jun Pataleta <jun@moodle.com>

2015/03/23
----------
Upgrade to tcpdf_php5 TCPDF 6.2.6 (MDL-49522)
by Adrian Greeve <adrian@moodle.com>

2011/10/29
----------
Upgrade to tcpdf_php5 TCPDF 5.9.133 (MDL-29283)
by Petr Skoda

2009/11/19
----------
Upgrade to tcpdf_php5 TCPDF 4.8.014 (MDL-20888)
by David Mudrak <david.mudrak@gmail.com>

2009/07/20
----------
Upgrade to tcpdf_php5 TCPDF 4.6.020 (MDL-19876)
by David Mudrak <david.mudrak@gmail.com>

* deleted cache/ doc/ examples/ config/tcpdf_config_alt.php config/lang/ images/
* removed all fonts but the core ones (courier.php, helveticabi.php,
    helveticab.php, helveticai.php, helvetica.php, symbol.php, timesbi.php,
    timesb.php, timesi.php, times.php, zapfdingbats.php) and FreeFont
* FreeFont chosen as a default utf8 font distributed by default, all others will
    be downloadable from moodle.org via new UI
* removed font/utils/
* moving configuration to the lib/pdflib.php wrapper so we do not need to modify
    TCPDF at all. Credit to Chardelle Busch for this solution in MDL-17179

2008/07/29
----------
Upgrade to tcpdf_php5 TCPDF 4.0.015 (MDL-15055)
by David Mudrak <david.mudrak@gmail.com>

* deleted cache/ doc/ examples/
* modified config/tcpdf_config.php
    Default values for K_PATH_MAIN and K_PATH_URL are automatically set for
    the Moodle installation. K_PATH_CACHE set to Moodle datadir cache.
    K_PATH_URL_CACHE can't be mapped in Moodle as datadir should not be
    accessible directly. However, it doesn't seem to be used within the
    library code.
