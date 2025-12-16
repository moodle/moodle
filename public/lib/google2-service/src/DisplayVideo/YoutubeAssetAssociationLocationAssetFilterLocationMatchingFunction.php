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

class YoutubeAssetAssociationLocationAssetFilterLocationMatchingFunction extends \Google\Collection
{
  protected $collection_key = 'locationAssetIds';
  /**
   * Optional. The business name to match with. This field is optional and can
   * only be set if location_matching_type is `FILTER`.
   *
   * @var string
   */
  public $business;
  /**
   * Optional. The labels to match with. Labels are logically OR'ed together.
   * This field is optional and can only be set if location_matching_type is
   * `FILTER`.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The selected location asset IDs. This field is required if
   * location_matching_type is `SELECTED_ASSETS`.
   *
   * @var string[]
   */
  public $locationAssetIds;

  /**
   * Optional. The business name to match with. This field is optional and can
   * only be set if location_matching_type is `FILTER`.
   *
   * @param string $business
   */
  public function setBusiness($business)
  {
    $this->business = $business;
  }
  /**
   * @return string
   */
  public function getBusiness()
  {
    return $this->business;
  }
  /**
   * Optional. The labels to match with. Labels are logically OR'ed together.
   * This field is optional and can only be set if location_matching_type is
   * `FILTER`.
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
   * Optional. The selected location asset IDs. This field is required if
   * location_matching_type is `SELECTED_ASSETS`.
   *
   * @param string[] $locationAssetIds
   */
  public function setLocationAssetIds($locationAssetIds)
  {
    $this->locationAssetIds = $locationAssetIds;
  }
  /**
   * @return string[]
   */
  public function getLocationAssetIds()
  {
    return $this->locationAssetIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAssetAssociationLocationAssetFilterLocationMatchingFunction::class, 'Google_Service_DisplayVideo_YoutubeAssetAssociationLocationAssetFilterLocationMatchingFunction');
