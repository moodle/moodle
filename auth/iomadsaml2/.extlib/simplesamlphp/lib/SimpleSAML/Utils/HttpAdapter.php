<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

/**
 * Provides a non-static wrapper for the HTTP utility class.
 *
 * @package SimpleSAML\Utils
 */
class HttpAdapter
{
    /**
     * @see HTTP::getServerHTTPS()
     * @return bool
     */
    public function getServerHTTPS()
    {
        return HTTP::getServerHTTPS();
    }

    /**
     * @see HTTP::getServerPort()
     * @return string
     */
    public function getServerPort()
    {
        return HTTP::getServerPort();
    }

    /**
     * @see HTTP::addURLParameters()
     *
     * @param string $url
     * @param array $parameters
     * @return string
     */
    public function addURLParameters($url, $parameters)
    {
        return HTTP::addURLParameters($url, $parameters);
    }

    /**
     * @see HTTP::checkSessionCookie()
     *
     * @param string|null $retryURL
     * @return void
     */
    public function checkSessionCookie($retryURL = null)
    {
        HTTP::checkSessionCookie($retryURL);
    }

    /**
     * @see HTTP::checkURLAllowed()
     *
     * @param string $url
     * @param array|null $trustedSites
     * @return string
     */
    public function checkURLAllowed($url, array $trustedSites = null)
    {
        return HTTP::checkURLAllowed($url, $trustedSites);
    }

    /**
     * @see HTTP::fetch()
     *
     * @param string $url
     * @param array $context
     * @param bool $getHeaders
     * @return array|string
     */
    public function fetch($url, $context = [], $getHeaders = false)
    {
        return HTTP::fetch($url, $context, $getHeaders);
    }

    /**
     * @see HTTP::getAcceptLanguage()
     * @return array
     */
    public function getAcceptLanguage()
    {
        return HTTP::getAcceptLanguage();
    }

    /**
     * @see HTTP::guessBasePath()
     * @return string
     */
    public function guessBasePath()
    {
        return HTTP::guessBasePath();
    }

    /**
     * @see HTTP::getBaseURL()
     * @return string
     */
    public function getBaseURL()
    {
        return HTTP::getBaseURL();
    }

    /**
     * @see HTTP::getFirstPathElement()
     *
     * @param bool $trailingslash
     * @return string
     */
    public function getFirstPathElement($trailingslash = true)
    {
        return HTTP::getFirstPathElement($trailingslash);
    }

    /**
     * @see HTTP::getPOSTRedirectURL()
     *
     * @param string $destination
     * @param array $data
     * @return string
     */
    public function getPOSTRedirectURL($destination, $data)
    {
        return HTTP::getPOSTRedirectURL($destination, $data);
    }

    /**
     * @see HTTP::getSelfHost()
     * @return string
     */
    public function getSelfHost()
    {
        return HTTP::getSelfHost();
    }

    /**
     * @see HTTP::getSelfHostWithNonStandardPort()
     * @return string
     */
    public function getSelfHostWithNonStandardPort()
    {
        return HTTP::getSelfHostWithNonStandardPort();
    }

    /**
     * @see HTTP::getSelfHostWithPath()
     * @return string
     */
    public function getSelfHostWithPath()
    {
        return HTTP::getSelfHostWithPath();
    }

    /**
     * @see HTTP::getSelfURL()
     * @return string
     */
    public function getSelfURL()
    {
        return HTTP::getSelfURL();
    }

    /**
     * @see HTTP::getSelfURLHost()
     * @return string
     */
    public function getSelfURLHost()
    {
        return HTTP::getSelfURLHost();
    }

    /**
     * @see HTTP::getSelfURLNoQuery()
     * @return string
     */
    public function getSelfURLNoQuery()
    {
        return HTTP::getSelfURLNoQuery();
    }

    /**
     * @see HTTP::isHTTPS()
     * @return bool
     */
    public function isHTTPS()
    {
        return HTTP::isHTTPS();
    }

    /**
     * @see HTTP::normalizeURL()
     * @param string $url
     * @return string
     */
    public function normalizeURL($url)
    {
        return HTTP::normalizeURL($url);
    }

    /**
     * @see HTTP::parseQueryString()
     *
     * @param string $query_string
     * @return array
     */
    public function parseQueryString($query_string)
    {
        return HTTP::parseQueryString($query_string);
    }

    /**
     * @see HTTP::redirectTrustedURL()
     *
     * @param string $url
     * @param array $parameters
     * @return void
     */
    public function redirectTrustedURL($url, $parameters = [])
    {
        HTTP::redirectTrustedURL($url, $parameters);
    }

    /**
     * @see HTTP::redirectUntrustedURL()
     *
     * @param string $url
     * @param array $parameters
     * @return void
     */
    public function redirectUntrustedURL($url, $parameters = [])
    {
        HTTP::redirectUntrustedURL($url, $parameters);
    }

    /**
     * @see HTTP::resolveURL()
     *
     * @param string $url
     * @param string|null $base
     * @return string
     */
    public function resolveURL($url, $base = null)
    {
        return HTTP::resolveURL($url, $base);
    }

    /**
     * @see HTTP::setCookie()
     *
     * @param string $name
     * @param string $value
     * @param array|null $params
     * @param bool $throw
     * @return void
     */
    public function setCookie($name, $value, $params = null, $throw = true)
    {
        HTTP::setCookie($name, $value, $params, $throw);
    }

    /**
     * @see HTTP::submitPOSTData()
     *
     * @param string $destination
     * @param array $data
     * @return void
     */
    public function submitPOSTData($destination, $data)
    {
        HTTP::submitPOSTData($destination, $data);
    }
}
