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

class MonitoringPoint extends \Google\Collection
{
  protected $collection_key = 'providerTags';
  /**
   * @var bool
   */
  public $autoGeoLocationEnabled;
  /**
   * @var string
   */
  public $connectionStatus;
  /**
   * @var string
   */
  public $createTime;
  /**
   * @var string
   */
  public $displayName;
  /**
   * @var string[]
   */
  public $errors;
  protected $geoLocationType = GeoLocation::class;
  protected $geoLocationDataType = '';
  protected $hostType = Host::class;
  protected $hostDataType = '';
  /**
   * @var string
   */
  public $hostname;
  /**
   * @var string
   */
  public $name;
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  /**
   * @var string
   */
  public $originatingIp;
  protected $providerTagsType = ProviderTag::class;
  protected $providerTagsDataType = 'array';
  /**
   * @var string
   */
  public $type;
  /**
   * @var string
   */
  public $updateTime;
  /**
   * @var bool
   */
  public $upgradeAvailable;
  /**
   * @var string
   */
  public $upgradeType;
  /**
   * @var string
   */
  public $version;

  /**
   * @param bool
   */
  public function setAutoGeoLocationEnabled($autoGeoLocationEnabled)
  {
    $this->autoGeoLocationEnabled = $autoGeoLocationEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoGeoLocationEnabled()
  {
    return $this->autoGeoLocationEnabled;
  }
  /**
   * @param string
   */
  public function setConnectionStatus($connectionStatus)
  {
    $this->connectionStatus = $connectionStatus;
  }
  /**
   * @return string
   */
  public function getConnectionStatus()
  {
    return $this->connectionStatus;
  }
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
   * @param string[]
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return string[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * @param GeoLocation
   */
  public function setGeoLocation(GeoLocation $geoLocation)
  {
    $this->geoLocation = $geoLocation;
  }
  /**
   * @return GeoLocation
   */
  public function getGeoLocation()
  {
    return $this->geoLocation;
  }
  /**
   * @param Host
   */
  public function setHost(Host $host)
  {
    $this->host = $host;
  }
  /**
   * @return Host
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * @param string
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
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
   * @param NetworkInterface[]
   */
  public function setNetworkInterfaces($networkInterfaces)
  {
    $this->networkInterfaces = $networkInterfaces;
  }
  /**
   * @return NetworkInterface[]
   */
  public function getNetworkInterfaces()
  {
    return $this->networkInterfaces;
  }
  /**
   * @param string
   */
  public function setOriginatingIp($originatingIp)
  {
    $this->originatingIp = $originatingIp;
  }
  /**
   * @return string
   */
  public function getOriginatingIp()
  {
    return $this->originatingIp;
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
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
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
  /**
   * @param bool
   */
  public function setUpgradeAvailable($upgradeAvailable)
  {
    $this->upgradeAvailable = $upgradeAvailable;
  }
  /**
   * @return bool
   */
  public function getUpgradeAvailable()
  {
    return $this->upgradeAvailable;
  }
  /**
   * @param string
   */
  public function setUpgradeType($upgradeType)
  {
    $this->upgradeType = $upgradeType;
  }
  /**
   * @return string
   */
  public function getUpgradeType()
  {
    return $this->upgradeType;
  }
  /**
   * @param string
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonitoringPoint::class, 'Google_Service_NetworkManagement_MonitoringPoint');
