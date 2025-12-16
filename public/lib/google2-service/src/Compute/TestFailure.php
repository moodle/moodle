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

class TestFailure extends \Google\Collection
{
  protected $collection_key = 'headers';
  /**
   * The actual output URL evaluated by a load balancer containing the scheme,
   * host, path and query parameters.
   *
   * @var string
   */
  public $actualOutputUrl;
  /**
   * Actual HTTP status code for rule with `urlRedirect` calculated by load
   * balancer
   *
   * @var int
   */
  public $actualRedirectResponseCode;
  /**
   * BackendService or BackendBucket returned by load balancer.
   *
   * @var string
   */
  public $actualService;
  /**
   * The expected output URL evaluated by a load balancer containing the scheme,
   * host, path and query parameters.
   *
   * @var string
   */
  public $expectedOutputUrl;
  /**
   * Expected HTTP status code for rule with `urlRedirect` calculated by load
   * balancer
   *
   * @var int
   */
  public $expectedRedirectResponseCode;
  /**
   * Expected BackendService or BackendBucket resource the given URL should be
   * mapped to.
   *
   * @var string
   */
  public $expectedService;
  protected $headersType = UrlMapTestHeader::class;
  protected $headersDataType = 'array';
  /**
   * Host portion of the URL.
   *
   * @var string
   */
  public $host;
  /**
   * Path portion including query parameters in the URL.
   *
   * @var string
   */
  public $path;

  /**
   * The actual output URL evaluated by a load balancer containing the scheme,
   * host, path and query parameters.
   *
   * @param string $actualOutputUrl
   */
  public function setActualOutputUrl($actualOutputUrl)
  {
    $this->actualOutputUrl = $actualOutputUrl;
  }
  /**
   * @return string
   */
  public function getActualOutputUrl()
  {
    return $this->actualOutputUrl;
  }
  /**
   * Actual HTTP status code for rule with `urlRedirect` calculated by load
   * balancer
   *
   * @param int $actualRedirectResponseCode
   */
  public function setActualRedirectResponseCode($actualRedirectResponseCode)
  {
    $this->actualRedirectResponseCode = $actualRedirectResponseCode;
  }
  /**
   * @return int
   */
  public function getActualRedirectResponseCode()
  {
    return $this->actualRedirectResponseCode;
  }
  /**
   * BackendService or BackendBucket returned by load balancer.
   *
   * @param string $actualService
   */
  public function setActualService($actualService)
  {
    $this->actualService = $actualService;
  }
  /**
   * @return string
   */
  public function getActualService()
  {
    return $this->actualService;
  }
  /**
   * The expected output URL evaluated by a load balancer containing the scheme,
   * host, path and query parameters.
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
   * Expected HTTP status code for rule with `urlRedirect` calculated by load
   * balancer
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
   * Expected BackendService or BackendBucket resource the given URL should be
   * mapped to.
   *
   * @param string $expectedService
   */
  public function setExpectedService($expectedService)
  {
    $this->expectedService = $expectedService;
  }
  /**
   * @return string
   */
  public function getExpectedService()
  {
    return $this->expectedService;
  }
  /**
   * HTTP headers of the request.
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
   * Host portion of the URL.
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
   * Path portion including query parameters in the URL.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestFailure::class, 'Google_Service_Compute_TestFailure');
