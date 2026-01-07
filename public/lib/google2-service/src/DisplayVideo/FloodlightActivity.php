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

namespace Google\Service\DisplayVideo;

class FloodlightActivity extends \Google\Collection
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const SERVING_STATUS_FLOODLIGHT_ACTIVITY_SERVING_STATUS_UNSPECIFIED = 'FLOODLIGHT_ACTIVITY_SERVING_STATUS_UNSPECIFIED';
  /**
   * Enabled.
   */
  public const SERVING_STATUS_FLOODLIGHT_ACTIVITY_SERVING_STATUS_ENABLED = 'FLOODLIGHT_ACTIVITY_SERVING_STATUS_ENABLED';
  /**
   * Disabled.
   */
  public const SERVING_STATUS_FLOODLIGHT_ACTIVITY_SERVING_STATUS_DISABLED = 'FLOODLIGHT_ACTIVITY_SERVING_STATUS_DISABLED';
  protected $collection_key = 'remarketingConfigs';
  /**
   * Output only. IDs of the advertisers that have access to the parent
   * Floodlight group. Only advertisers under the provided partner ID will be
   * listed in this field.
   *
   * @var string[]
   */
  public $advertiserIds;
  /**
   * Required. The display name of the Floodlight activity.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The unique ID of the Floodlight activity. Assigned by the
   * system.
   *
   * @var string
   */
  public $floodlightActivityId;
  /**
   * Required. Immutable. The ID of the parent Floodlight group.
   *
   * @var string
   */
  public $floodlightGroupId;
  /**
   * Output only. The resource name of the Floodlight activity.
   *
   * @var string
   */
  public $name;
  protected $remarketingConfigsType = RemarketingConfig::class;
  protected $remarketingConfigsDataType = 'array';
  /**
   * Optional. Whether the Floodlight activity is served.
   *
   * @var string
   */
  public $servingStatus;
  /**
   * Output only. Whether tags are required to be compliant.
   *
   * @var bool
   */
  public $sslRequired;

  /**
   * Output only. IDs of the advertisers that have access to the parent
   * Floodlight group. Only advertisers under the provided partner ID will be
   * listed in this field.
   *
   * @param string[] $advertiserIds
   */
  public function setAdvertiserIds($advertiserIds)
  {
    $this->advertiserIds = $advertiserIds;
  }
  /**
   * @return string[]
   */
  public function getAdvertiserIds()
  {
    return $this->advertiserIds;
  }
  /**
   * Required. The display name of the Floodlight activity.
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
   * Output only. The unique ID of the Floodlight activity. Assigned by the
   * system.
   *
   * @param string $floodlightActivityId
   */
  public function setFloodlightActivityId($floodlightActivityId)
  {
    $this->floodlightActivityId = $floodlightActivityId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityId()
  {
    return $this->floodlightActivityId;
  }
  /**
   * Required. Immutable. The ID of the parent Floodlight group.
   *
   * @param string $floodlightGroupId
   */
  public function setFloodlightGroupId($floodlightGroupId)
  {
    $this->floodlightGroupId = $floodlightGroupId;
  }
  /**
   * @return string
   */
  public function getFloodlightGroupId()
  {
    return $this->floodlightGroupId;
  }
  /**
   * Output only. The resource name of the Floodlight activity.
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
   * Output only. A list of configuration objects designating whether
   * remarketing for this Floodlight Activity is enabled and available for a
   * specifc advertiser. If enabled, this Floodlight Activity generates a
   * remarketing user list that is able to be used in targeting under the
   * advertiser.
   *
   * @param RemarketingConfig[] $remarketingConfigs
   */
  public function setRemarketingConfigs($remarketingConfigs)
  {
    $this->remarketingConfigs = $remarketingConfigs;
  }
  /**
   * @return RemarketingConfig[]
   */
  public function getRemarketingConfigs()
  {
    return $this->remarketingConfigs;
  }
  /**
   * Optional. Whether the Floodlight activity is served.
   *
   * Accepted values: FLOODLIGHT_ACTIVITY_SERVING_STATUS_UNSPECIFIED,
   * FLOODLIGHT_ACTIVITY_SERVING_STATUS_ENABLED,
   * FLOODLIGHT_ACTIVITY_SERVING_STATUS_DISABLED
   *
   * @param self::SERVING_STATUS_* $servingStatus
   */
  public function setServingStatus($servingStatus)
  {
    $this->servingStatus = $servingStatus;
  }
  /**
   * @return self::SERVING_STATUS_*
   */
  public function getServingStatus()
  {
    return $this->servingStatus;
  }
  /**
   * Output only. Whether tags are required to be compliant.
   *
   * @param bool $sslRequired
   */
  public function setSslRequired($sslRequired)
  {
    $this->sslRequired = $sslRequired;
  }
  /**
   * @return bool
   */
  public function getSslRequired()
  {
    return $this->sslRequired;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightActivity::class, 'Google_Service_DisplayVideo_FloodlightActivity');
