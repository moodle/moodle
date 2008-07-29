Moodle-specific changes to TCPDF are tracked here.

2008/07/29 
Upgrade to tcpdf_php5 TCPDF 4.0.015 (MDL-15055) 
by David Mudrak <david.mudrak@gmail.com>

* deleted cache/ doc/ examples/
* modified config/tcpdf_config.php
    Default values for K_PATH_MAIN and K_PATH_URL are automatically set for
    the Moodle installation. K_PATH_CACHE set to Moodle datadir cache.
    K_PATH_URL_CACHE can't be mapped in Moodle as datadir should not be
    accessible directly. However, it doesn't seem to be used within the
    library code.

