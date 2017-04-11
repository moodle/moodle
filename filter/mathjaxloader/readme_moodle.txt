Description of MathJAX library integration in Moodle
=========================================================================================

License: Apache 2.0
Source: http://www.mathjax.org

Moodle maintainer: Damyon Wiese

=========================================================================================
This library is not shipped with Moodle, but this filter is provided, which can be used to
correctly load MathJax into a page from the CDN. Alternatively you can download the entire
library and install it locally, then use this filter to load that local version.

The only changes required to this filter to handle different MathJax versions is to update
the default CDN urls in settings.php - and update the list of language mappings - in filter.php.

This filter does however include the MathJax Accessibility extensions from
https://github.com/mathjax/MathJax-a11y

To update the extensions download the latest release from https://github.com/mathjax/MathJax-a11y/releases
. Delete all the files from "contrib/ally" and extract the files from the zip to the same folder. Then
update the version numbers in the lib/thirdpartylibs.xml.
