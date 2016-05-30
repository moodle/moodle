FPDI
==================================

No changes from the upstream version have been made. Both FPDI and FPDF_TPL have
been downloaded and unzipped to this directory.

Information
-----------

URL: http://www.setasign.de/products/pdf-php-solutions/fpdi/
Download from: http://www.setasign.de/products/pdf-php-solutions/fpdi/downloads
Documentation: http://www.setasign.de/products/pdf-php-solutions/fpdi/manuals/
License: The MIT License (MIT)

Installation
------------
1) Download the latest version of fpdi from the url above.
2) Unzip the files into this directory.
3) Update mod/assign/feedback/editpdf/fpdi/fpdi_bridge.php (or the replacement file) to extend 'pdf' instead of 'TCPDF'.
4) Make a note below of any changes made.

2015/12/04
----------
Updated to FPDI: 1.6.1

1) Changed 'TCPDF' to 'pdf' (as stated above).
2) License changed from Apache Software License 2.0 to The MIT License (MIT).

2015/10/01
----------
Updated to FPDI: 1.5.4

fpdi no longer uses fpdi2tcpdf_bridge.php this has been replaced with fpdi_bridge.php.
fpdi_bridge.php has been modified to extend pdf (lib/pdflib.php) as was done with
fpdi2tcpdf_bridge.php.

Updated by Adrian Greeve (MDL-49515)

------------------
Previous versions:

FPDI: 1.4.4
FPDF_TPL: 1.2.3
