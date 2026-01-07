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

namespace Google\Service\AdMob;

class AdUnit extends \Google\Collection
{
  protected $collection_key = 'adTypes';
  /**
   * AdFormat of the ad unit. Possible values are as follows: "APP_OPEN" - App
   * Open ad format. "BANNER" - Banner ad format. "BANNER_INTERSTITIAL" - Legacy
   * format that can be used as either banner or interstitial. This format can
   * no longer be created but can be targeted by mediation groups.
   * "INTERSTITIAL" - A full screen ad. Supported ad types are "RICH_MEDIA" and
   * "VIDEO". "NATIVE" - Native ad format. "REWARDED" - An ad that, once viewed,
   * gets a callback verifying the view so that a reward can be given to the
   * user. Supported ad types are "RICH_MEDIA" (interactive) and video where
   * video can not be excluded. "REWARDED_INTERSTITIAL" - Rewarded Interstitial
   * ad format. Only supports video ad type. See
   * https://support.google.com/admob/answer/9884467.
   *
   * @var string
   */
  public $adFormat;
  /**
   * Ad media type supported by this ad unit. Possible values as follows:
   * "RICH_MEDIA" - Text, image, and other non-video media. "VIDEO" - Video
   * media.
   *
   * @var string[]
   */
  public $adTypes;
  /**
   * The externally visible ID of the ad unit which can be used to integrate
   * with the AdMob SDK. This is a read only property. Example: ca-app-
   * pub-9876543210987654/0123456789
   *
   * @var string
   */
  public $adUnitId;
  /**
   * The externally visible ID of the app this ad unit is associated with.
   * Example: ca-app-pub-9876543210987654~0123456789
   *
   * @var string
   */
  public $appId;
  /**
   * The display name of the ad unit as shown in the AdMob UI, which is provided
   * by the user. The maximum length allowed is 80 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name for this ad unit. Format is
   * accounts/{publisher_id}/adUnits/{ad_unit_id_fragment} Example:
   * accounts/pub-9876543210987654/adUnits/0123456789
   *
   * @var string
   */
  public $name;

  /**
   * AdFormat of the ad unit. Possible values are as follows: "APP_OPEN" - App
   * Open ad format. "BANNER" - Banner ad format. "BANNER_INTERSTITIAL" - Legacy
   * format that can be used as either banner or interstitial. This format can
   * no longer be created but can be targeted by mediation groups.
   * "INTERSTITIAL" - A full screen ad. Supported ad types are "RICH_MEDIA" and
   * "VIDEO". "NATIVE" - Native ad format. "REWARDED" - An ad that, once viewed,
   * gets a callback verifying the view so that a reward can be given to the
   * user. Supported ad types are "RICH_MEDIA" (interactive) and video where
   * video can not be excluded. "REWARDED_INTERSTITIAL" - Rewarded Interstitial
   * ad format. Only supports video ad type. See
   * https://support.google.com/admob/answer/9884467.
   *
   * @param string $adFormat
   */
  public function setAdFormat($adFormat)
  {
    $this->adFormat = $adFormat;
  }
  /**
   * @return string
   */
  public function getAdFormat()
  {
    return $this->adFormat;
  }
  /**
   * Ad media type supported by this ad unit. Possible values as follows:
   * "RICH_MEDIA" - Text, image, and other non-video media. "VIDEO" - Video
   * media.
   *
   * @param string[] $adTypes
   */
  public function setAdTypes($adTypes)
  {
    $this->adTypes = $adTypes;
  }
  /**
   * @return string[]
   */
  public function getAdTypes()
  {
    return $this->adTypes;
  }
  /**
   * The externally visible ID of the ad unit which can be used to integrate
   * with the AdMob SDK. This is a read only property. Example: ca-app-
   * pub-9876543210987654/0123456789
   *
   * @param string $adUnitId
   */
  public function setAdUnitId($adUnitId)
  {
    $this->adUnitId = $adUnitId;
  }
  /**
   * @return string
   */
  public function getAdUnitId()
  {
    return $this->adUnitId;
  }
  /**
   * The externally visible ID of the app this ad unit is associated with.
   * Example: ca-app-pub-9876543210987654~0123456789
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * The display name of the ad unit as shown in the AdMob UI, which is provided
   * by the user. The maximum length allowed is 80 characters.
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
   * Resource name for this ad unit. Format is
   * accounts/{publisher_id}/adUnits/{ad_unit_id_fragment} Example:
   * accounts/pub-9876543210987654/adUnits/0123456789
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdUnit::class, 'Google_Service_AdMob_AdUnit');
