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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ProbeHttpGetAction extends \Google\Collection
{
  protected $collection_key = 'httpHeaders';
  /**
   * Host name to connect to, defaults to the model serving container's IP. You
   * probably want to set "Host" in httpHeaders instead.
   *
   * @var string
   */
  public $host;
  protected $httpHeadersType = GoogleCloudAiplatformV1ProbeHttpHeader::class;
  protected $httpHeadersDataType = 'array';
  /**
   * Path to access on the HTTP server.
   *
   * @var string
   */
  public $path;
  /**
   * Number of the port to access on the container. Number must be in the range
   * 1 to 65535.
   *
   * @var int
   */
  public $port;
  /**
   * Scheme to use for connecting to the host. Defaults to HTTP. Acceptable
   * values are "HTTP" or "HTTPS".
   *
   * @var string
   */
  public $scheme;

  /**
   * Host name to connect to, defaults to the model serving container's IP. You
   * probably want to set "Host" in httpHeaders instead.
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
   * Custom headers to set in the request. HTTP allows repeated headers.
   *
   * @param GoogleCloudAiplatformV1ProbeHttpHeader[] $httpHeaders
   */
  public function setHttpHeaders($httpHeaders)
  {
    $this->httpHeaders = $httpHeaders;
  }
  /**
   * @return GoogleCloudAiplatformV1ProbeHttpHeader[]
   */
  public function getHttpHeaders()
  {
    return $this->httpHeaders;
  }
  /**
   * Path to access on the HTTP server.
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
   * Number of the port to access on the container. Number must be in the range
   * 1 to 65535.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Scheme to use for connecting to the host. Defaults to HTTP. Acceptable
   * values are "HTTP" or "HTTPS".
   *
   * @param string $scheme
   */
  public function setScheme($scheme)
  {
    $this->scheme = $scheme;
  }
  /**
   * @return string
   */
  public function getScheme()
  {
    return $this->scheme;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ProbeHttpGetAction::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ProbeHttpGetAction');
