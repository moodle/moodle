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

namespace Google\Service\Adsense;

class AdUnit extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Ad unit has been activated by the user.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Ad unit has been archived by the user. Note that archived ad units are only
   * removed from the default view in the UI. Archived ad units can still serve
   * ads.
   */
  public const STATE_ARCHIVED = 'ARCHIVED';
  protected $contentAdsSettingsType = ContentAdsSettings::class;
  protected $contentAdsSettingsDataType = '';
  /**
   * Required. Display name of the ad unit, as provided when the ad unit was
   * created.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Resource name of the ad unit. Format:
   * accounts/{account}/adclients/{adclient}/adunits/{adunit}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Unique ID of the ad unit as used in the `AD_UNIT_ID` reporting
   * dimension.
   *
   * @var string
   */
  public $reportingDimensionId;
  /**
   * Required. State of the ad unit.
   *
   * @var string
   */
  public $state;

  /**
   * Required. Settings specific to content ads (AFC).
   *
   * @param ContentAdsSettings $contentAdsSettings
   */
  public function setContentAdsSettings(ContentAdsSettings $contentAdsSettings)
  {
    $this->contentAdsSettings = $contentAdsSettings;
  }
  /**
   * @return ContentAdsSettings
   */
  public function getContentAdsSettings()
  {
    return $this->contentAdsSettings;
  }
  /**
   * Required. Display name of the ad unit, as provided when the ad unit was
   * created.
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
   * Output only. Resource name of the ad unit. Format:
   * accounts/{account}/adclients/{adclient}/adunits/{adunit}
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
   * Output only. Unique ID of the ad unit as used in the `AD_UNIT_ID` reporting
   * dimension.
   *
   * @param string $reportingDimensionId
   */
  public function setReportingDimensionId($reportingDimensionId)
  {
    $this->reportingDimensionId = $reportingDimensionId;
  }
  /**
   * @return string
   */
  public function getReportingDimensionId()
  {
    return $this->reportingDimensionId;
  }
  /**
   * Required. State of the ad unit.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, ARCHIVED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdUnit::class, 'Google_Service_Adsense_AdUnit');
