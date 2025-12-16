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

class RedisInstanceInfo extends \Google\Model
{
  /**
   * Name of a Cloud Redis Instance.
   *
   * @var string
   */
  public $displayName;
  /**
   * URI of a Cloud Redis Instance network.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Primary endpoint IP address of a Cloud Redis Instance.
   *
   * @var string
   */
  public $primaryEndpointIp;
  /**
   * Read endpoint IP address of a Cloud Redis Instance (if applicable).
   *
   * @var string
   */
  public $readEndpointIp;
  /**
   * Region in which the Cloud Redis Instance is defined.
   *
   * @var string
   */
  public $region;
  /**
   * URI of a Cloud Redis Instance.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of a Cloud Redis Instance.
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
   * URI of a Cloud Redis Instance network.
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
   * Primary endpoint IP address of a Cloud Redis Instance.
   *
   * @param string $primaryEndpointIp
   */
  public function setPrimaryEndpointIp($primaryEndpointIp)
  {
    $this->primaryEndpointIp = $primaryEndpointIp;
  }
  /**
   * @return string
   */
  public function getPrimaryEndpointIp()
  {
    return $this->primaryEndpointIp;
  }
  /**
   * Read endpoint IP address of a Cloud Redis Instance (if applicable).
   *
   * @param string $readEndpointIp
   */
  public function setReadEndpointIp($readEndpointIp)
  {
    $this->readEndpointIp = $readEndpointIp;
  }
  /**
   * @return string
   */
  public function getReadEndpointIp()
  {
    return $this->readEndpointIp;
  }
  /**
   * Region in which the Cloud Redis Instance is defined.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * URI of a Cloud Redis Instance.
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
class_alias(RedisInstanceInfo::class, 'Google_Service_NetworkManagement_RedisInstanceInfo');
