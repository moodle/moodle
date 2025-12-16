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

class GoogleAdsSearchads360V0ResourcesAssetSet extends \Google\Model
{
  /**
   * Output only. The ID of the asset set.
   *
   * @var string
   */
  public $id;
  /**
   * Immutable. The resource name of the asset set. Asset set resource names
   * have the form: `customers/{customer_id}/assetSets/{asset_set_id}`
   *
   * @var string
   */
  public $resourceName;

  /**
   * Output only. The ID of the asset set.
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
   * Immutable. The resource name of the asset set. Asset set resource names
   * have the form: `customers/{customer_id}/assetSets/{asset_set_id}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAssetSet::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAssetSet');
