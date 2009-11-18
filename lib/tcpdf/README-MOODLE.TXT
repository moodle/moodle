Moodle-specific changes to TCPDF are tracked here.
==================================================

2008/07/20
==========
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
==========
Upgrade to tcpdf_php5 TCPDF 4.0.015 (MDL-15055)
by David Mudrak <david.mudrak@gmail.com>

* deleted cache/ doc/ examples/
* modified config/tcpdf_config.php
    Default values for K_PATH_MAIN and K_PATH_URL are automatically set for
    the Moodle installation. K_PATH_CACHE set to Moodle datadir cache.
    K_PATH_URL_CACHE can't be mapped in Moodle as datadir should not be
    accessible directly. However, it doesn't seem to be used within the
    library code.



18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: tcpdf.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/tcpdf/tcpdf.php,v
retrieving revision 1.3
diff -u -r1.3 tcpdf.php
--- tcpdf.php	20 Jul 2009 17:31:48 -0000	1.3
+++ tcpdf.php	18 Nov 2009 05:48:56 -0000
@@ -7595,7 +7595,7 @@
 			if (isset($dash)) {
 				$dash_string = '';
 				if ($dash) {
-					if (ereg('^.+,', $dash)) {
+					if (preg_match('/^.+,/', $dash)) {
 						$tab = explode(',', $dash);
 					} else {
 						$tab = array($dash);
