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

class CreateAdAssetRequest extends \Google\Model
{
  protected $adAssetType = AdAsset::class;
  protected $adAssetDataType = '';

  /**
   * Required. The ad asset to create. Only supports assets of AdAssetType
   * `AD_ASSET_TYPE_YOUTUBE_VIDEO`.
   *
   * @param AdAsset $adAsset
   */
  public function setAdAsset(AdAsset $adAsset)
  {
    $this->adAsset = $adAsset;
  }
  /**
   * @return AdAsset
   */
  public function getAdAsset()
  {
    return $this->adAsset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateAdAssetRequest::class, 'Google_Service_DisplayVideo_CreateAdAssetRequest');
