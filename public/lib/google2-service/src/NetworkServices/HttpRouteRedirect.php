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

namespace Google\Service\NetworkServices;

class HttpRouteRedirect extends \Google\Model
{
  /**
   * Default value
   */
  public const RESPONSE_CODE_RESPONSE_CODE_UNSPECIFIED = 'RESPONSE_CODE_UNSPECIFIED';
  /**
   * Corresponds to 301.
   */
  public const RESPONSE_CODE_MOVED_PERMANENTLY_DEFAULT = 'MOVED_PERMANENTLY_DEFAULT';
  /**
   * Corresponds to 302.
   */
  public const RESPONSE_CODE_FOUND = 'FOUND';
  /**
   * Corresponds to 303.
   */
  public const RESPONSE_CODE_SEE_OTHER = 'SEE_OTHER';
  /**
   * Corresponds to 307. In this case, the request method will be retained.
   */
  public const RESPONSE_CODE_TEMPORARY_REDIRECT = 'TEMPORARY_REDIRECT';
  /**
   * Corresponds to 308. In this case, the request method will be retained.
   */
  public const RESPONSE_CODE_PERMANENT_REDIRECT = 'PERMANENT_REDIRECT';
  /**
   * The host that will be used in the redirect response instead of the one that
   * was supplied in the request.
   *
   * @var string
   */
  public $hostRedirect;
  /**
   * If set to true, the URL scheme in the redirected request is set to https.
   * If set to false, the URL scheme of the redirected request will remain the
   * same as that of the request. The default is set to false.
   *
   * @var bool
   */
  public $httpsRedirect;
  /**
   * The path that will be used in the redirect response instead of the one that
   * was supplied in the request. path_redirect can not be supplied together
   * with prefix_redirect. Supply one alone or neither. If neither is supplied,
   * the path of the original request will be used for the redirect.
   *
   * @var string
   */
  public $pathRedirect;
  /**
   * The port that will be used in the redirected request instead of the one
   * that was supplied in the request.
   *
   * @var int
   */
  public $portRedirect;
  /**
   * Indicates that during redirection, the matched prefix (or path) should be
   * swapped with this value. This option allows URLs be dynamically created
   * based on the request.
   *
   * @var string
   */
  public $prefixRewrite;
  /**
   * The HTTP Status code to use for the redirect.
   *
   * @var string
   */
  public $responseCode;
  /**
   * if set to true, any accompanying query portion of the original URL is
   * removed prior to redirecting the request. If set to false, the query
   * portion of the original URL is retained. The default is set to false.
   *
   * @var bool
   */
  public $stripQuery;

  /**
   * The host that will be used in the redirect response instead of the one that
   * was supplied in the request.
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
   * If set to true, the URL scheme in the redirected request is set to https.
   * If set to false, the URL scheme of the redirected request will remain the
   * same as that of the request. The default is set to false.
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
   * The path that will be used in the redirect response instead of the one that
   * was supplied in the request. path_redirect can not be supplied together
   * with prefix_redirect. Supply one alone or neither. If neither is supplied,
   * the path of the original request will be used for the redirect.
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
   * The port that will be used in the redirected request instead of the one
   * that was supplied in the request.
   *
   * @param int $portRedirect
   */
  public function setPortRedirect($portRedirect)
  {
    $this->portRedirect = $portRedirect;
  }
  /**
   * @return int
   */
  public function getPortRedirect()
  {
    return $this->portRedirect;
  }
  /**
   * Indicates that during redirection, the matched prefix (or path) should be
   * swapped with this value. This option allows URLs be dynamically created
   * based on the request.
   *
   * @param string $prefixRewrite
   */
  public function setPrefixRewrite($prefixRewrite)
  {
    $this->prefixRewrite = $prefixRewrite;
  }
  /**
   * @return string
   */
  public function getPrefixRewrite()
  {
    return $this->prefixRewrite;
  }
  /**
   * The HTTP Status code to use for the redirect.
   *
   * Accepted values: RESPONSE_CODE_UNSPECIFIED, MOVED_PERMANENTLY_DEFAULT,
   * FOUND, SEE_OTHER, TEMPORARY_REDIRECT, PERMANENT_REDIRECT
   *
   * @param self::RESPONSE_CODE_* $responseCode
   */
  public function setResponseCode($responseCode)
  {
    $this->responseCode = $responseCode;
  }
  /**
   * @return self::RESPONSE_CODE_*
   */
  public function getResponseCode()
  {
    return $this->responseCode;
  }
  /**
   * if set to true, any accompanying query portion of the original URL is
   * removed prior to redirecting the request. If set to false, the query
   * portion of the original URL is retained. The default is set to false.
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
class_alias(HttpRouteRedirect::class, 'Google_Service_NetworkServices_HttpRouteRedirect');
