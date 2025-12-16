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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1DefineNetworkResponse extends \Google\Collection
{
  protected $collection_key = 'settings';
  /**
   * Network ID of the new created network.
   *
   * @var string
   */
  public $networkId;
  protected $settingsType = GoogleChromePolicyVersionsV1NetworkSetting::class;
  protected $settingsDataType = 'array';
  /**
   * The target resource on which this new network will be defined. The
   * following resources are supported: * Organizational Unit
   * ("orgunits/{orgunit_id}")
   *
   * @var string
   */
  public $targetResource;

  /**
   * Network ID of the new created network.
   *
   * @param string $networkId
   */
  public function setNetworkId($networkId)
  {
    $this->networkId = $networkId;
  }
  /**
   * @return string
   */
  public function getNetworkId()
  {
    return $this->networkId;
  }
  /**
   * Detailed network settings of the new created network
   *
   * @param GoogleChromePolicyVersionsV1NetworkSetting[] $settings
   */
  public function setSettings($settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return GoogleChromePolicyVersionsV1NetworkSetting[]
   */
  public function getSettings()
  {
    return $this->settings;
  }
  /**
   * The target resource on which this new network will be defined. The
   * following resources are supported: * Organizational Unit
   * ("orgunits/{orgunit_id}")
   *
   * @param string $targetResource
   */
  public function setTargetResource($targetResource)
  {
    $this->targetResource = $targetResource;
  }
  /**
   * @return string
   */
  public function getTargetResource()
  {
    return $this->targetResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1DefineNetworkResponse::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1DefineNetworkResponse');
