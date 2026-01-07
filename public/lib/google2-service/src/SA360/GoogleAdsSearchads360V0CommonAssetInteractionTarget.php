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

class GoogleAdsSearchads360V0CommonAssetInteractionTarget extends \Google\Model
{
  /**
   * The asset resource name.
   *
   * @var string
   */
  public $asset;
  /**
   * Only used with CustomerAsset, CampaignAsset and AdGroupAsset metrics.
   * Indicates whether the interaction metrics occurred on the asset itself or a
   * different asset or ad unit.
   *
   * @var bool
   */
  public $interactionOnThisAsset;

  /**
   * The asset resource name.
   *
   * @param string $asset
   */
  public function setAsset($asset)
  {
    $this->asset = $asset;
  }
  /**
   * @return string
   */
  public function getAsset()
  {
    return $this->asset;
  }
  /**
   * Only used with CustomerAsset, CampaignAsset and AdGroupAsset metrics.
   * Indicates whether the interaction metrics occurred on the asset itself or a
   * different asset or ad unit.
   *
   * @param bool $interactionOnThisAsset
   */
  public function setInteractionOnThisAsset($interactionOnThisAsset)
  {
    $this->interactionOnThisAsset = $interactionOnThisAsset;
  }
  /**
   * @return bool
   */
  public function getInteractionOnThisAsset()
  {
    return $this->interactionOnThisAsset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonAssetInteractionTarget::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonAssetInteractionTarget');
