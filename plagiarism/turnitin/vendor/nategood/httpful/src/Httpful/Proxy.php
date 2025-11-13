<?php
namespace Httpful;

if (!defined('CURLPROXY_SOCKS4')) {
    define('CURLPROXY_SOCKS4', 4);
}

/**
 * Class to organize the Proxy stuff a bit more
 */
class Proxy
{
    public const HTTP = CURLPROXY_HTTP;
    public const SOCKS4 = CURLPROXY_SOCKS4;
    public const SOCKS5 = CURLPROXY_SOCKS5;
}
