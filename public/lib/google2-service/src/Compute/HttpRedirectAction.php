<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Compute;

class HttpRedirectAction extends \Google\Model
{
  /**
   * Http Status Code 302 - Found.
   */
  public const REDIRECT_RESPONSE_CODE_FOUND = 'FOUND';
  /**
   * Http Status Code 301 - Moved Permanently.
   */
  public const REDIRECT_RESPONSE_CODE_MOVED_PERMANENTLY_DEFAULT = 'MOVED_PERMANENTLY_DEFAULT';
  /**
   * Http Status Code 308 - Permanent Redirect maintaining HTTP method.
   */
  public const REDIRECT_RESPONSE_CODE_PERMANENT_REDIRECT = 'PERMANENT_REDIRECT';
  /**
   * Http Status Code 303 - See Other.
   */
  public const REDIRECT_RESPONSE_CODE_SEE_OTHER = 'SEE_OTHER';
  /**
   * Http Status Code 307 - Temporary Redirect maintaining HTTP method.
   */
  public const REDIRECT_RESPONSE_CODE_TEMPORARY_REDIRECT = 'TEMPORARY_REDIRECT';
  /**
   * The host that is used in the redirect response instead of the one that was
   * supplied in the request.
   *
   * The value must be from 1 to 255 characters.
   *
   * @var string
   */
  public $hostRedirect;
  /**
   * If set to true, the URL scheme in the redirected request is set to HTTPS.
   * If set to false, the URL scheme of the redirected request remains the same
   * as that of the request.
   *
   * This must only be set for URL maps used inTargetHttpProxys. Setting this
   * true forTargetHttpsProxy is not permitted.
   *
   * The default is set to false.
   *
   * @var bool
   */
  public $httpsRedirect;
  /**
   * The path that is used in the redirect response instead of the one that was
   * supplied in the request.
   *
   * pathRedirect cannot be supplied together withprefixRedirect. Supply one
   * alone or neither. If neither is supplied, the path of the original request
   * is used for the redirect.
   *
   * The value must be from 1 to 1024 characters.
   *
   * @var string
   */
  public $pathRedirect;
  /**
   * The prefix that replaces the prefixMatch specified in the
   * HttpRouteRuleMatch, retaining the remaining portion of the URL before
   * redirecting the request.
   *
   * prefixRedirect cannot be supplied together withpathRedirect. Supply one
   * alone or neither. If neither is supplied, the path of the original request
   * is used for the redirect.
   *
   * The value must be from 1 to 1024 characters.
   *
   * @var string
   */
  public $prefixRedirect;
  /**
   * The HTTP Status code to use for this RedirectAction.
   *
   * Supported values are:        - MOVED_PERMANENTLY_DEFAULT, which is the
   * default value and corresponds    to 301.    - FOUND, which corresponds to
   * 302.    - SEE_OTHER which corresponds to 303.    - TEMPORARY_REDIRECT,
   * which corresponds to 307. In this case, the request    method is retained.
   * - PERMANENT_REDIRECT, which corresponds to 308. In this case, the request
   * method is retained.
   *
   * @var string
   */
  public $redirectResponseCode;
  /**
   * If set to true, any accompanying query portion of the original URL is
   * removed before redirecting the request. If set to false, the query portion
   * of the original URL is retained.
   *
   * The default is set to false.
   *
   * @var bool
   */
  public $stripQuery;

  /**
   * The host that is used in the redirect response instead of the one that was
   * supplied in the request.
   *
   * The value must be from 1 to 255 characters.
   *
   * @param string $hostRedirect
   */
  public function setHostRedirect($hostRedirect)
  {
    $this->hostRedirect = $hostRedirect;
  }
  /**
   * @return string
   */
  public function getHostRedirect()
  {
    return $this->hostRedirect;
  }
  /**
   * If set to true, the URL scheme in the redirected request is set to HTTPS.
   * If set to false, the URL scheme of the redirected request remains the same
   * as that of the request.
   *
   * This must only be set for URL maps used inTargetHttpProxys. Setting this
   * true forTargetHttpsProxy is not permitted.
   *
   * The default is set to false.
   *
   * @param bool $httpsRedirect
   */
  public function setHttpsRedirect($httpsRedirect)
  {
    $this->httpsRedirect = $httpsRedirect;
  }
  /**
   * @return bool
   */
  public function getHttpsRedirect()
  {
    return $this->httpsRedirect;
  }
  /**
   * The path that is used in the redirect response instead of the one that was
   * supplied in the request.
   *
   * pathRedirect cannot be supplied together withprefixRedirect. Supply one
   * alone or neither. If neither is supplied, the path of the original request
   * is used for the redirect.
   *
   * The value must be from 1 to 1024 characters.
   *
   * @param string $pathRedirect
   */
  public function setPathRedirect($pathRedirect)
  {
    $this->pathRedirect = $pathRedirect;
  }
  /**
   * @return string
   */
  public function getPathRedirect()
  {
    return $this->pathRedirect;
  }
  /**
   * The prefix that replaces the prefixMatch specified in the
   * HttpRouteRuleMatch, retaining the remaining portion of the URL before
   * redirecting the request.
   *
   * prefixRedirect cannot be supplied together withpathRedirect. Supply one
   * alone or neither. If neither is supplied, the path of the original request
   * is used for the redirect.
   *
   * The value must be from 1 to 1024 characters.
   *
   * @param string $prefixRedirect
   */
  public function setPrefixRedirect($prefixRedirect)
  {
    $this->prefixRedirect = $prefixRedirect;
  }
  /**
   * @return string
   */
  public function getPrefixRedirect()
  {
    return $this->prefixRedirect;
  }
  /**
   * The HTTP Status code to use for this RedirectAction.
   *
   * Supported values are:        - MOVED_PERMANENTLY_DEFAULT, which is the
   * default value and corresponds    to 301.    - FOUND, which corresponds to
   * 302.    - SEE_OTHER which corresponds to 303.    - TEMPORARY_REDIRECT,
   * which corresponds to 307. In this case, the request    method is retained.
   * - PERMANENT_REDIRECT, which corresponds to 308. In this case, the request
   * method is retained.
   *
   * Accepted values: FOUND, MOVED_PERMANENTLY_DEFAULT, PERMANENT_REDIRECT,
   * SEE_OTHER, TEMPORARY_REDIRECT
   *
   * @param self::REDIRECT_RESPONSE_CODE_* $redirectResponseCode
   */
  public function setRedirectResponseCode($redirectResponseCode)
  {
    $this->redirectResponseCode = $redirectResponseCode;
  }
  /**
   * @return self::REDIRECT_RESPONSE_CODE_*
   */
  public function getRedirectResponseCode()
  {
    return $this->redirectResponseCode;
  }
  /**
   * If set to true, any accompanying query portion of the original URL is
   * removed before redirecting the request. If set to false, the query portion
   * of the original URL is retained.
   *
   * The default is set to false.
   *
   * @param bool $stripQuery
   */
  public function setStripQuery($stripQuery)
  {
    $this->stripQuery = $stripQuery;
  }
  /**
   * @return bool
   */
  public function getStripQuery()
  {
    return $this->stripQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRedirectAction::class, 'Google_Service_Compute_HttpRedirectAction');
