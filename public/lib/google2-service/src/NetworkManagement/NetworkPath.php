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

class NetworkPath extends \Google\Collection
{
  protected $collection_key = 'providerTags';
  /**
   * @var string
   */
  public $createTime;
  /**
   * @var string
   */
  public $destination;
  protected $destinationGeoLocationType = GeoLocation::class;
  protected $destinationGeoLocationDataType = '';
  /**
   * @var string
   */
  public $displayName;
  /**
   * @var bool
   */
  public $dualEnded;
  /**
   * @var bool
   */
  public $monitoringEnabled;
  /**
   * @var string
   */
  public $monitoringPolicyDisplayName;
  /**
   * @var string
   */
  public $monitoringPolicyId;
  /**
   * @var string
   */
  public $monitoringStatus;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $networkProtocol;
  protected $providerTagsType = ProviderTag::class;
  protected $providerTagsDataType = 'array';
  /**
   * @var string
   */
  public $providerUiUri;
  /**
   * @var string
   */
  public $sourceMonitoringPointId;
  /**
   * @var string
   */
  public $updateTime;

  /**
   * @param string
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
   * @param string
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * @param GeoLocation
   */
  public function setDestinationGeoLocation(GeoLocation $destinationGeoLocation)
  {
    $this->destinationGeoLocation = $destinationGeoLocation;
  }
  /**
   * @return GeoLocation
   */
  public function getDestinationGeoLocation()
  {
    return $this->destinationGeoLocation;
  }
  /**
   * @param string
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
   * @param bool
   */
  public function setDualEnded($dualEnded)
  {
    $this->dualEnded = $dualEnded;
  }
  /**
   * @return bool
   */
  public function getDualEnded()
  {
    return $this->dualEnded;
  }
  /**
   * @param bool
   */
  public function setMonitoringEnabled($monitoringEnabled)
  {
    $this->monitoringEnabled = $monitoringEnabled;
  }
  /**
   * @return bool
   */
  public function getMonitoringEnabled()
  {
    return $this->monitoringEnabled;
  }
  /**
   * @param string
   */
  public function setMonitoringPolicyDisplayName($monitoringPolicyDisplayName)
  {
    $this->monitoringPolicyDisplayName = $monitoringPolicyDisplayName;
  }
  /**
   * @return string
   */
  public function getMonitoringPolicyDisplayName()
  {
    return $this->monitoringPolicyDisplayName;
  }
  /**
   * @param string
   */
  public function setMonitoringPolicyId($monitoringPolicyId)
  {
    $this->monitoringPolicyId = $monitoringPolicyId;
  }
  /**
   * @return string
   */
  public function getMonitoringPolicyId()
  {
    return $this->monitoringPolicyId;
  }
  /**
   * @param string
   */
  public function setMonitoringStatus($monitoringStatus)
  {
    $this->monitoringStatus = $monitoringStatus;
  }
  /**
   * @return string
   */
  public function getMonitoringStatus()
  {
    return $this->monitoringStatus;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setNetworkProtocol($networkProtocol)
  {
    $this->networkProtocol = $networkProtocol;
  }
  /**
   * @return string
   */
  public function getNetworkProtocol()
  {
    return $this->networkProtocol;
  }
  /**
   * @param ProviderTag[]
   */
  public function setProviderTags($providerTags)
  {
    $this->providerTags = $providerTags;
  }
  /**
   * @return ProviderTag[]
   */
  public function getProviderTags()
  {
    return $this->providerTags;
  }
  /**
   * @param string
   */
  public function setProviderUiUri($providerUiUri)
  {
    $this->providerUiUri = $providerUiUri;
  }
  /**
   * @return string
   */
  public function getProviderUiUri()
  {
    return $this->providerUiUri;
  }
  /**
   * @param string
   */
  public function setSourceMonitoringPointId($sourceMonitoringPointId)
  {
    $this->sourceMonitoringPointId = $sourceMonitoringPointId;
  }
  /**
   * @return string
   */
  public function getSourceMonitoringPointId()
  {
    return $this->sourceMonitoringPointId;
  }
  /**
   * @param string
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
class_alias(NetworkPath::class, 'Google_Service_NetworkManagement_NetworkPath');
