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

namespace Google\Service\NetworkSecurity;

class DnsThreatDetector extends \Google\Collection
{
  /**
   * An unspecified provider.
   */
  public const PROVIDER_PROVIDER_UNSPECIFIED = 'PROVIDER_UNSPECIFIED';
  /**
   * The Infoblox DNS threat detector provider.
   */
  public const PROVIDER_INFOBLOX = 'INFOBLOX';
  protected $collection_key = 'excludedNetworks';
  /**
   * Output only. Create time stamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A list of network resource names which aren't monitored by this
   * DnsThreatDetector. Example:
   * `projects/PROJECT_ID/global/networks/NETWORK_NAME`.
   *
   * @var string[]
   */
  public $excludedNetworks;
  /**
   * Optional. Any labels associated with the DnsThreatDetector, listed as key
   * value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. Name of the DnsThreatDetector resource.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The provider used for DNS threat analysis.
   *
   * @var string
   */
  public $provider;
  /**
   * Output only. Update time stamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Create time stamp.
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
   * Optional. A list of network resource names which aren't monitored by this
   * DnsThreatDetector. Example:
   * `projects/PROJECT_ID/global/networks/NETWORK_NAME`.
   *
   * @param string[] $excludedNetworks
   */
  public function setExcludedNetworks($excludedNetworks)
  {
    $this->excludedNetworks = $excludedNetworks;
  }
  /**
   * @return string[]
   */
  public function getExcludedNetworks()
  {
    return $this->excludedNetworks;
  }
  /**
   * Optional. Any labels associated with the DnsThreatDetector, listed as key
   * value pairs.
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
   * Immutable. Identifier. Name of the DnsThreatDetector resource.
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
   * Required. The provider used for DNS threat analysis.
   *
   * Accepted values: PROVIDER_UNSPECIFIED, INFOBLOX
   *
   * @param self::PROVIDER_* $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return self::PROVIDER_*
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Output only. Update time stamp.
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
class_alias(DnsThreatDetector::class, 'Google_Service_NetworkSecurity_DnsThreatDetector');
