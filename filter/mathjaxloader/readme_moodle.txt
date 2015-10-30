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
