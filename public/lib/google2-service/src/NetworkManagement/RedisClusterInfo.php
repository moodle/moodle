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

namespace Google\Service\NetworkManagement;

class RedisClusterInfo extends \Google\Model
{
  /**
   * Discovery endpoint IP address of a Redis Cluster.
   *
   * @var string
   */
  public $discoveryEndpointIpAddress;
  /**
   * Name of a Redis Cluster.
   *
   * @var string
   */
  public $displayName;
  /**
   * Name of the region in which the Redis Cluster is defined. For example, "us-
   * central1".
   *
   * @var string
   */
  public $location;
  /**
   * URI of the network containing the Redis Cluster endpoints in format
   * "projects/{project_id}/global/networks/{network_id}".
   *
   * @var string
   */
  public $networkUri;
  /**
   * Secondary endpoint IP address of a Redis Cluster.
   *
   * @var string
   */
  public $secondaryEndpointIpAddress;
  /**
   * URI of a Redis Cluster in format
   * "projects/{project_id}/locations/{location}/clusters/{cluster_id}"
   *
   * @var string
   */
  public $uri;

  /**
   * Discovery endpoint IP address of a Redis Cluster.
   *
   * @param string $discoveryEndpointIpAddress
   */
  public function setDiscoveryEndpointIpAddress($discoveryEndpointIpAddress)
  {
    $this->discoveryEndpointIpAddress = $discoveryEndpointIpAddress;
  }
  /**
   * @return string
   */
  public function getDiscoveryEndpointIpAddress()
  {
    return $this->discoveryEndpointIpAddress;
  }
  /**
   * Name of a Redis Cluster.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Name of the region in which the Redis Cluster is defined. For example, "us-
   * central1".
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * URI of the network containing the Redis Cluster endpoints in format
   * "projects/{project_id}/global/networks/{network_id}".
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * Secondary endpoint IP address of a Redis Cluster.
   *
   * @param string $secondaryEndpointIpAddress
   */
  public function setSecondaryEndpointIpAddress($secondaryEndpointIpAddress)
  {
    $this->secondaryEndpointIpAddress = $secondaryEndpointIpAddress;
  }
  /**
   * @return string
   */
  public function getSecondaryEndpointIpAddress()
  {
    return $this->secondaryEndpointIpAddress;
  }
  /**
   * URI of a Redis Cluster in format
   * "projects/{project_id}/locations/{location}/clusters/{cluster_id}"
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RedisClusterInfo::class, 'Google_Service_NetworkManagement_RedisClusterInfo');
