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

class HttpHealthCheck extends \Google\Model
{
  /**
   * How often (in seconds) to send a health check. The default value is5
   * seconds.
   *
   * @var int
   */
  public $checkIntervalSec;
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * A so-far unhealthy instance will be marked healthy after this many
   * consecutive successes. The default value is 2.
   *
   * @var int
   */
  public $healthyThreshold;
  /**
   * The value of the host header in the HTTP health check request. If left
   * empty (default value), the public IP on behalf of which this health check
   * is performed will be used.
   *
   * @var string
   */
  public $host;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#httpHealthCheck for HTTP health checks.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * The TCP port number for the HTTP health check request. The default value
   * is80.
   *
   * @var int
   */
  public $port;
  /**
   * The request path of the HTTP health check request. The default value is/.
   * This field does not support query parameters. Must comply withRFC3986.
   *
   * @var string
   */
  public $requestPath;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * How long (in seconds) to wait before claiming failure. The default value
   * is5 seconds. It is invalid for timeoutSec to have greater value than
   * checkIntervalSec.
   *
   * @var int
   */
  public $timeoutSec;
  /**
   * A so-far healthy instance will be marked unhealthy after this many
   * consecutive failures. The default value is 2.
   *
   * @var int
   */
  public $unhealthyThreshold;

  /**
   * How often (in seconds) to send a health check. The default value is5
   * seconds.
   *
   * @param int $checkIntervalSec
   */
  public function setCheckIntervalSec($checkIntervalSec)
  {
    $this->checkIntervalSec = $checkIntervalSec;
  }
  /**
   * @return int
   */
  public function getCheckIntervalSec()
  {
    return $this->checkIntervalSec;
  }
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * A so-far unhealthy instance will be marked healthy after this many
   * consecutive successes. The default value is 2.
   *
   * @param int $healthyThreshold
   */
  public function setHealthyThreshold($healthyThreshold)
  {
    $this->healthyThreshold = $healthyThreshold;
  }
  /**
   * @return int
   */
  public function getHealthyThreshold()
  {
    return $this->healthyThreshold;
  }
  /**
   * The value of the host header in the HTTP health check request. If left
   * empty (default value), the public IP on behalf of which this health check
   * is performed will be used.
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
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#httpHealthCheck for HTTP health checks.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The TCP port number for the HTTP health check request. The default value
   * is80.
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
   * The request path of the HTTP health check request. The default value is/.
   * This field does not support query parameters. Must comply withRFC3986.
   *
   * @param string $requestPath
   */
  public function setRequestPath($requestPath)
  {
    $this->requestPath = $requestPath;
  }
  /**
   * @return string
   */
  public function getRequestPath()
  {
    return $this->requestPath;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * How long (in seconds) to wait before claiming failure. The default value
   * is5 seconds. It is invalid for timeoutSec to have greater value than
   * checkIntervalSec.
   *
   * @param int $timeoutSec
   */
  public function setTimeoutSec($timeoutSec)
  {
    $this->timeoutSec = $timeoutSec;
  }
  /**
   * @return int
   */
  public function getTimeoutSec()
  {
    return $this->timeoutSec;
  }
  /**
   * A so-far healthy instance will be marked unhealthy after this many
   * consecutive failures. The default value is 2.
   *
   * @param int $unhealthyThreshold
   */
  public function setUnhealthyThreshold($unhealthyThreshold)
  {
    $this->unhealthyThreshold = $unhealthyThreshold;
  }
  /**
   * @return int
   */
  public function getUnhealthyThreshold()
  {
    return $this->unhealthyThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpHealthCheck::class, 'Google_Service_Compute_HttpHealthCheck');
