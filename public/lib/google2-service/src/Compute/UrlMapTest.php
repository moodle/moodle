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

class UrlMapTest extends \Google\Collection
{
  protected $collection_key = 'headers';
  /**
   * Description of this test case.
   *
   * @var string
   */
  public $description;
  /**
   * The expected output URL evaluated by the load balancer containing the
   * scheme, host, path and query parameters.
   *
   * For rules that forward requests to backends, the test passes only
   * whenexpectedOutputUrl matches the request forwarded by the load balancer to
   * backends. For rules with urlRewrite, the test verifies that the forwarded
   * request matcheshostRewrite and pathPrefixRewrite in theurlRewrite action.
   * When service is specified,expectedOutputUrl`s scheme is ignored.
   *
   * For rules with urlRedirect, the test passes only ifexpectedOutputUrl
   * matches the URL in the load balancer's redirect response. If urlRedirect
   * specifieshttps_redirect, the test passes only if the scheme
   * inexpectedOutputUrl is also set to HTTPS. If urlRedirect specifies
   * strip_query, the test passes only if expectedOutputUrl does not contain any
   * query parameters.
   *
   * expectedOutputUrl is optional whenservice is specified.
   *
   * @var string
   */
  public $expectedOutputUrl;
  /**
   * For rules with urlRedirect, the test passes only
   * ifexpectedRedirectResponseCode matches the HTTP status code in load
   * balancer's redirect response.
   *
   * expectedRedirectResponseCode cannot be set whenservice is set.
   *
   * @var int
   */
  public $expectedRedirectResponseCode;
  protected $headersType = UrlMapTestHeader::class;
  protected $headersDataType = 'array';
  /**
   * Host portion of the URL. If headers contains a host header, then host must
   * also match the header value.
   *
   * @var string
   */
  public $host;
  /**
   * Path portion of the URL.
   *
   * @var string
   */
  public $path;
  /**
   * Expected BackendService or BackendBucket resource the given URL should be
   * mapped to.
   *
   * The service field cannot be set if expectedRedirectResponseCode is set.
   *
   * @var string
   */
  public $service;

  /**
   * Description of this test case.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The expected output URL evaluated by the load balancer containing the
   * scheme, host, path and query parameters.
   *
   * For rules that forward requests to backends, the test passes only
   * whenexpectedOutputUrl matches the request forwarded by the load balancer to
   * backends. For rules with urlRewrite, the test verifies that the forwarded
   * request matcheshostRewrite and pathPrefixRewrite in theurlRewrite action.
   * When service is specified,expectedOutputUrl`s scheme is ignored.
   *
   * For rules with urlRedirect, the test passes only ifexpectedOutputUrl
   * matches the URL in the load balancer's redirect response. If urlRedirect
   * specifieshttps_redirect, the test passes only if the scheme
   * inexpectedOutputUrl is also set to HTTPS. If urlRedirect specifies
   * strip_query, the test passes only if expectedOutputUrl does not contain any
   * query parameters.
   *
   * expectedOutputUrl is optional whenservice is specified.
   *
   * @param string $expectedOutputUrl
   */
  public function setExpectedOutputUrl($expectedOutputUrl)
  {
    $this->expectedOutputUrl = $expectedOutputUrl;
  }
  /**
   * @return string
   */
  public function getExpectedOutputUrl()
  {
    return $this->expectedOutputUrl;
  }
  /**
   * For rules with urlRedirect, the test passes only
   * ifexpectedRedirectResponseCode matches the HTTP status code in load
   * balancer's redirect response.
   *
   * expectedRedirectResponseCode cannot be set whenservice is set.
   *
   * @param int $expectedRedirectResponseCode
   */
  public function setExpectedRedirectResponseCode($expectedRedirectResponseCode)
  {
    $this->expectedRedirectResponseCode = $expectedRedirectResponseCode;
  }
  /**
   * @return int
   */
  public function getExpectedRedirectResponseCode()
  {
    return $this->expectedRedirectResponseCode;
  }
  /**
   * HTTP headers for this request. If headers contains a host header, then host
   * must also match the header value.
   *
   * @param UrlMapTestHeader[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return UrlMapTestHeader[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Host portion of the URL. If headers contains a host header, then host must
   * also match the header value.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Path portion of the URL.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Expected BackendService or BackendBucket resource the given URL should be
   * mapped to.
   *
   * The service field cannot be set if expectedRedirectResponseCode is set.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlMapTest::class, 'Google_Service_Compute_UrlMapTest');
