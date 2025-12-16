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

class Mesh extends \Google\Model
{
  /**
   * Defaults to NONE.
   */
  public const ENVOY_HEADERS_ENVOY_HEADERS_UNSPECIFIED = 'ENVOY_HEADERS_UNSPECIFIED';
  /**
   * Suppress envoy debug headers.
   */
  public const ENVOY_HEADERS_NONE = 'NONE';
  /**
   * Envoy will insert default internal debug headers into upstream requests:
   * x-envoy-attempt-count, x-envoy-is-timeout-retry, x-envoy-expected-rq-
   * timeout-ms, x-envoy-original-path, x-envoy-upstream-stream-duration-ms
   */
  public const ENVOY_HEADERS_DEBUG_HEADERS = 'DEBUG_HEADERS';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Determines if envoy will insert internal debug headers into
   * upstream requests. Other Envoy headers may still be injected. By default,
   * envoy will not insert any debug headers.
   *
   * @var string
   */
  public $envoyHeaders;
  /**
   * Optional. If set to a valid TCP port (1-65535), instructs the SIDECAR proxy
   * to listen on the specified port of localhost (127.0.0.1) address. The
   * SIDECAR proxy will expect all traffic to be redirected to this port
   * regardless of its actual ip:port destination. If unset, a port '15001' is
   * used as the interception port. This is applicable only for sidecar proxy
   * deployments.
   *
   * @var int
   */
  public $interceptionPort;
  /**
   * Optional. Set of label tags associated with the Mesh resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the Mesh resource. It matches pattern
   * `projects/locations/meshes/`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Server-defined URL of this resource
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
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
   * Optional. Determines if envoy will insert internal debug headers into
   * upstream requests. Other Envoy headers may still be injected. By default,
   * envoy will not insert any debug headers.
   *
   * Accepted values: ENVOY_HEADERS_UNSPECIFIED, NONE, DEBUG_HEADERS
   *
   * @param self::ENVOY_HEADERS_* $envoyHeaders
   */
  public function setEnvoyHeaders($envoyHeaders)
  {
    $this->envoyHeaders = $envoyHeaders;
  }
  /**
   * @return self::ENVOY_HEADERS_*
   */
  public function getEnvoyHeaders()
  {
    return $this->envoyHeaders;
  }
  /**
   * Optional. If set to a valid TCP port (1-65535), instructs the SIDECAR proxy
   * to listen on the specified port of localhost (127.0.0.1) address. The
   * SIDECAR proxy will expect all traffic to be redirected to this port
   * regardless of its actual ip:port destination. If unset, a port '15001' is
   * used as the interception port. This is applicable only for sidecar proxy
   * deployments.
   *
   * @param int $interceptionPort
   */
  public function setInterceptionPort($interceptionPort)
  {
    $this->interceptionPort = $interceptionPort;
  }
  /**
   * @return int
   */
  public function getInterceptionPort()
  {
    return $this->interceptionPort;
  }
  /**
   * Optional. Set of label tags associated with the Mesh resource.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Name of the Mesh resource. It matches pattern
   * `projects/locations/meshes/`.
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
   * Output only. Server-defined URL of this resource
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
   * Output only. The timestamp when the resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Mesh::class, 'Google_Service_NetworkServices_Mesh');
