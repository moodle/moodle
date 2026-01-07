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

namespace Google\Service\Networkconnectivity;

class RemoteTransportProfile extends \Google\Collection
{
  protected $collection_key = 'supportedBandwidths';
  /**
   * @var string
   */
  public $createTime;
  /**
   * @var string
   */
  public $description;
  /**
   * @var string
   */
  public $flow;
  /**
   * @var string[]
   */
  public $labels;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $orderState;
  /**
   * @var string
   */
  public $provider;
  /**
   * @var string
   */
  public $providerSite;
  /**
   * @var string
   */
  public $region;
  /**
   * @var string
   */
  public $sla;
  /**
   * @var string[]
   */
  public $supportedBandwidths;
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
   * @param string
   */
  public function setFlow($flow)
  {
    $this->flow = $flow;
  }
  /**
   * @return string
   */
  public function getFlow()
  {
    return $this->flow;
  }
  /**
   * @param string[]
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
  public function setOrderState($orderState)
  {
    $this->orderState = $orderState;
  }
  /**
   * @return string
   */
  public function getOrderState()
  {
    return $this->orderState;
  }
  /**
   * @param string
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * @param string
   */
  public function setProviderSite($providerSite)
  {
    $this->providerSite = $providerSite;
  }
  /**
   * @return string
   */
  public function getProviderSite()
  {
    return $this->providerSite;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setSla($sla)
  {
    $this->sla = $sla;
  }
  /**
   * @return string
   */
  public function getSla()
  {
    return $this->sla;
  }
  /**
   * @param string[]
   */
  public function setSupportedBandwidths($supportedBandwidths)
  {
    $this->supportedBandwidths = $supportedBandwidths;
  }
  /**
   * @return string[]
   */
  public function getSupportedBandwidths()
  {
    return $this->supportedBandwidths;
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
class_alias(RemoteTransportProfile::class, 'Google_Service_Networkconnectivity_RemoteTransportProfile');
