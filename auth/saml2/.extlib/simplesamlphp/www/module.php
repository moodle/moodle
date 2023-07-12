<?php

/**
 * This web page receives requests for web-pages hosted by modules, and directs them to
 * the process() handler in the Module class.
 */

require_once('_include.php');

\SimpleSAML\Module::process()->send();
