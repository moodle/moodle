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

class GoogleAdsSearchads360V0ResourcesCampaignAssetSet extends \Google\Model
{
  /**
   * The status has not been specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The linkage between asset set and its container is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The linkage between asset set and its container is removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * Immutable. The asset set which is linked to the campaign.
   *
   * @var string
   */
  public $assetSet;
  /**
   * Immutable. The campaign to which this asset set is linked.
   *
   * @var string
   */
  public $campaign;
  /**
   * Immutable. The resource name of the campaign asset set. Asset set asset
   * resource names have the form:
   * `customers/{customer_id}/campaignAssetSets/{campaign_id}~{asset_set_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The status of the campaign asset set asset. Read-only.
   *
   * @var string
   */
  public $status;

  /**
   * Immutable. The asset set which is linked to the campaign.
   *
   * @param string $assetSet
   */
  public function setAssetSet($assetSet)
  {
    $this->assetSet = $assetSet;
  }
  /**
   * @return string
   */
  public function getAssetSet()
  {
    return $this->assetSet;
  }
  /**
   * Immutable. The campaign to which this asset set is linked.
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
   * Immutable. The resource name of the campaign asset set. Asset set asset
   * resource names have the form:
   * `customers/{customer_id}/campaignAssetSets/{campaign_id}~{asset_set_id}`
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
   * Output only. The status of the campaign asset set asset. Read-only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVED
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
class_alias(GoogleAdsSearchads360V0ResourcesCampaignAssetSet::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCampaignAssetSet');
