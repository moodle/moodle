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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesAssetGroup extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const AD_STRENGTH_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const AD_STRENGTH_UNKNOWN = 'UNKNOWN';
  /**
   * The ad strength is currently pending.
   */
  public const AD_STRENGTH_PENDING = 'PENDING';
  /**
   * No ads could be generated.
   */
  public const AD_STRENGTH_NO_ADS = 'NO_ADS';
  /**
   * Poor strength.
   */
  public const AD_STRENGTH_POOR = 'POOR';
  /**
   * Average strength.
   */
  public const AD_STRENGTH_AVERAGE = 'AVERAGE';
  /**
   * Good strength.
   */
  public const AD_STRENGTH_GOOD = 'GOOD';
  /**
   * Excellent strength.
   */
  public const AD_STRENGTH_EXCELLENT = 'EXCELLENT';
  /**
   * The status has not been specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The asset group is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The asset group is paused.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * The asset group is removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  protected $collection_key = 'finalUrls';
  /**
   * Output only. Overall ad strength of this asset group.
   *
   * @var string
   */
  public $adStrength;
  /**
   * Immutable. The campaign with which this asset group is associated. The
   * asset which is linked to the asset group.
   *
   * @var string
   */
  public $campaign;
  /**
   * A list of final mobile URLs after all cross domain redirects. In
   * performance max, by default, the urls are eligible for expansion unless
   * opted out.
   *
   * @var string[]
   */
  public $finalMobileUrls;
  /**
   * A list of final URLs after all cross domain redirects. In performance max,
   * by default, the urls are eligible for expansion unless opted out.
   *
   * @var string[]
   */
  public $finalUrls;
  /**
   * Output only. The ID of the asset group.
   *
   * @var string
   */
  public $id;
  /**
   * Required. Name of the asset group. Required. It must have a minimum length
   * of 1 and maximum length of 128. It must be unique under a campaign.
   *
   * @var string
   */
  public $name;
  /**
   * First part of text that may appear appended to the url displayed in the ad.
   *
   * @var string
   */
  public $path1;
  /**
   * Second part of text that may appear appended to the url displayed in the
   * ad. This field can only be set when path1 is set.
   *
   * @var string
   */
  public $path2;
  /**
   * Immutable. The resource name of the asset group. Asset group resource names
   * have the form: `customers/{customer_id}/assetGroups/{asset_group_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * The status of the asset group.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. Overall ad strength of this asset group.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, PENDING, NO_ADS, POOR, AVERAGE,
   * GOOD, EXCELLENT
   *
   * @param self::AD_STRENGTH_* $adStrength
   */
  public function setAdStrength($adStrength)
  {
    $this->adStrength = $adStrength;
  }
  /**
   * @return self::AD_STRENGTH_*
   */
  public function getAdStrength()
  {
    return $this->adStrength;
  }
  /**
   * Immutable. The campaign with which this asset group is associated. The
   * asset which is linked to the asset group.
   *
   * @param string $campaign
   */
  public function setCampaign($campaign)
  {
    $this->campaign = $campaign;
  }
  /**
   * @return string
   */
  public function getCampaign()
  {
    return $this->campaign;
  }
  /**
   * A list of final mobile URLs after all cross domain redirects. In
   * performance max, by default, the urls are eligible for expansion unless
   * opted out.
   *
   * @param string[] $finalMobileUrls
   */
  public function setFinalMobileUrls($finalMobileUrls)
  {
    $this->finalMobileUrls = $finalMobileUrls;
  }
  /**
   * @return string[]
   */
  public function getFinalMobileUrls()
  {
    return $this->finalMobileUrls;
  }
  /**
   * A list of final URLs after all cross domain redirects. In performance max,
   * by default, the urls are eligible for expansion unless opted out.
   *
   * @param string[] $finalUrls
   */
  public function setFinalUrls($finalUrls)
  {
    $this->finalUrls = $finalUrls;
  }
  /**
   * @return string[]
   */
  public function getFinalUrls()
  {
    return $this->finalUrls;
  }
  /**
   * Output only. The ID of the asset group.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. Name of the asset group. Required. It must have a minimum length
   * of 1 and maximum length of 128. It must be unique under a campaign.
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
   * First part of text that may appear appended to the url displayed in the ad.
   *
   * @param string $path1
   */
  public function setPath1($path1)
  {
    $this->path1 = $path1;
  }
  /**
   * @return string
   */
  public function getPath1()
  {
    return $this->path1;
  }
  /**
   * Second part of text that may appear appended to the url displayed in the
   * ad. This field can only be set when path1 is set.
   *
   * @param string $path2
   */
  public function setPath2($path2)
  {
    $this->path2 = $path2;
  }
  /**
   * @return string
   */
  public function getPath2()
  {
    return $this->path2;
  }
  /**
   * Immutable. The resource name of the asset group. Asset group resource names
   * have the form: `customers/{customer_id}/assetGroups/{asset_group_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The status of the asset group.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, PAUSED, REMOVED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAssetGroup::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAssetGroup');
