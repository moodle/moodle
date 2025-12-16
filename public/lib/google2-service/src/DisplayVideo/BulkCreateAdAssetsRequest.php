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

class BulkCreateAdAssetsRequest extends \Google\Collection
{
  protected $collection_key = 'adAssets';
  protected $adAssetsType = AdAsset::class;
  protected $adAssetsDataType = 'array';

  /**
   * Required. Ad assets to create. Only supports assets of AdAssetType
   * `AD_ASSET_TYPE_YOUTUBE_VIDEO`.
   *
   * @param AdAsset[] $adAssets
   */
  public function setAdAssets($adAssets)
  {
    $this->adAssets = $adAssets;
  }
  /**
   * @return AdAsset[]
   */
  public function getAdAssets()
  {
    return $this->adAssets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkCreateAdAssetsRequest::class, 'Google_Service_DisplayVideo_BulkCreateAdAssetsRequest');
