<?php

/**
 * Function wrapper for HTML Purifier for quick use.
 * @note This function only includes the library when it is called. While
 *       this is efficient for instances when you only use HTML Purifier
 *       on a few of your pages, it murders bytecode caching. You still
 *       need to add HTML Purifier to your path.
 * @note ''HTMLPurifier()'' is NOT the same as ''new HTMLPurifier()''
 */

function HTMLPurifier($html, $config = null) {
    static $purifier = false;
    if (!$purifier) {
        require_once 'HTMLPurifier.php';
        $purifier = new HTMLPurifier();
    }
    return $purifier->purify($html, $config);
}

