Description of MathJAX library integration in Moodle
====================================================

* Default MathJax version: 2.7.2
* License: Apache 2.0
* Source: https://www.mathjax.org/

This library is not shipped with Moodle, but this filter is provided, which can be used to
correctly load MathJax into a page from the CDN. Alternatively you can download the entire
library and install it locally, then use this filter to load that local version.

Upgrading the default MathJax version
-------------------------------------

1. Update the default CDN URL in settings.php
2. Perform an upgrade step to change the configured URL if it matches the
   previous default.
3. Check and eventually update the list of language mappings in filter.php.
   Also see the unit test for the language mappings.

