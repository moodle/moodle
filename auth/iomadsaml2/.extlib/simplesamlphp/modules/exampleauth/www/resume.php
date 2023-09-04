<?php

/**
 * This page serves as the point where the user's authentication
 * process is resumed after the login page.
 *
 * It simply passes control back to the class.
 *
 * @package SimpleSAMLphp
 */

namespace SimpleSAML\Module\exampleauth\Auth\Source;

External::resume();
