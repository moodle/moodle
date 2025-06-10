 Code is being tested against PHP versions 7.0 to 8.0<br />
 PHP version 8.1 brings breaking changes to imagefilledpolygon and count<br />
 for this reason I will create a separate repo for it.


pChart 2.1 library for PHP 7 (7-Compatible branch)
===================

The good old pChart got an overhaul!

 - All examples work fine with zero code modifications to them
 - Code was beautified
 - Made minor improvements and added some speed ups


pChart 2.2
===================
This version will NOT work with your existing code, but supports PHP 7.0 & 7.1<br />
Please check the change log for the complete list of changes.<br />

Major changes:
 - Code cleanup
 - Bootstrapped
 - Exceptions
 - Introduced pColor & pColorGradient
 - Moved functions around
 - Added support for compression and filters in PNG output
 - Cache: added PDO SQLite storage option
 - ImageMapper: JavaScript re-write using jQuery
 - ImageMapper: added PDO SQLite storage option
 - Removed DelayedLoader


 pChart 2.3 (recommended)
===================
Goals:
 - Reduce the use of the hard disk to fonts only
 - Eliminate not-exactly-free 3rd party components
 - Add the first batch of new features since 2011

Major changes:
 - PHP 7.2+ required from now on
 - Introduce pQRCode /* Check my PHP-QRCode-fork repo */
 - Replace all fonts with Open Font licensed ones
 - Performace boost
 - pBarcode to own dir
 - No more config files (palettes & barcode db)


  pChart 2.4 (in progress)
===================
Goals:
 - Introduce pPyramid

Major changes:
 - Removed pImageMap, pCache, pBarcode
 - Introduced the new Barcodes lib /* Check out my PHP-Barcodes-fork repo */
 - Introduced the new PDF417 lib /* Check out my PHP-PDF417-fork repo */
 - Introduced the new Aztec lib /* Check out my PHP-Aztec-fork repo */
 - Introduced the new QRCode lib /* Check out my PHP-QRCode-fork repo */
 - Explicitly declare the visibility for methods and properties
