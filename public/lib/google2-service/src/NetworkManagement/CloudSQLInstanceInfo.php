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

class CloudSQLInstanceInfo extends \Google\Model
{
  /**
   * Name of a Cloud SQL instance.
   *
   * @var string
   */
  public $displayName;
  /**
   * External IP address of a Cloud SQL instance.
   *
   * @var string
   */
  public $externalIp;
  /**
   * Internal IP address of a Cloud SQL instance.
   *
   * @var string
   */
  public $internalIp;
  /**
   * URI of a Cloud SQL instance network or empty string if the instance does
   * not have one.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Region in which the Cloud SQL instance is running.
   *
   * @var string
   */
  public $region;
  /**
   * URI of a Cloud SQL instance.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of a Cloud SQL instance.
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
   * External IP address of a Cloud SQL instance.
   *
   * @param string $externalIp
   */
  public function setExternalIp($externalIp)
  {
    $this->externalIp = $externalIp;
  }
  /**
   * @return string
   */
  public function getExternalIp()
  {
    return $this->externalIp;
  }
  /**
   * Internal IP address of a Cloud SQL instance.
   *
   * @param string $internalIp
   */
  public function setInternalIp($internalIp)
  {
    $this->internalIp = $internalIp;
  }
  /**
   * @return string
   */
  public function getInternalIp()
  {
    return $this->internalIp;
  }
  /**
   * URI of a Cloud SQL instance network or empty string if the instance does
   * not have one.
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
   * Region in which the Cloud SQL instance is running.
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
   * URI of a Cloud SQL instance.
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
class_alias(CloudSQLInstanceInfo::class, 'Google_Service_NetworkManagement_CloudSQLInstanceInfo');
