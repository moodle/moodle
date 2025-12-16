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

namespace Google\Service\Clouderrorreporting;

class HttpRequestContext extends \Google\Model
{
  /**
   * The type of HTTP request, such as `GET`, `POST`, etc.
   *
   * @var string
   */
  public $method;
  /**
   * The referrer information that is provided with the request.
   *
   * @var string
   */
  public $referrer;
  /**
   * The IP address from which the request originated. This can be IPv4, IPv6,
   * or a token which is derived from the IP address, depending on the data that
   * has been provided in the error report.
   *
   * @var string
   */
  public $remoteIp;
  /**
   * The HTTP response status code for the request.
   *
   * @var int
   */
  public $responseStatusCode;
  /**
   * The URL of the request.
   *
   * @var string
   */
  public $url;
  /**
   * The user agent information that is provided with the request.
   *
   * @var string
   */
  public $userAgent;

  /**
   * The type of HTTP request, such as `GET`, `POST`, etc.
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The referrer information that is provided with the request.
   *
   * @param string $referrer
   */
  public function setReferrer($referrer)
  {
    $this->referrer = $referrer;
  }
  /**
   * @return string
   */
  public function getReferrer()
  {
    return $this->referrer;
  }
  /**
   * The IP address from which the request originated. This can be IPv4, IPv6,
   * or a token which is derived from the IP address, depending on the data that
   * has been provided in the error report.
   *
   * @param string $remoteIp
   */
  public function setRemoteIp($remoteIp)
  {
    $this->remoteIp = $remoteIp;
  }
  /**
   * @return string
   */
  public function getRemoteIp()
  {
    return $this->remoteIp;
  }
  /**
   * The HTTP response status code for the request.
   *
   * @param int $responseStatusCode
   */
  public function setResponseStatusCode($responseStatusCode)
  {
    $this->responseStatusCode = $responseStatusCode;
  }
  /**
   * @return int
   */
  public function getResponseStatusCode()
  {
    return $this->responseStatusCode;
  }
  /**
   * The URL of the request.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * The user agent information that is provided with the request.
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRequestContext::class, 'Google_Service_Clouderrorreporting_HttpRequestContext');
