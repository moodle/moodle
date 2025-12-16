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

namespace Google\Service\AuthorizedBuyersMarketplace;

class MarketplaceTargeting extends \Google\Collection
{
  protected $collection_key = 'excludedSensitiveCategoryIds';
  protected $daypartTargetingType = DayPartTargeting::class;
  protected $daypartTargetingDataType = '';
  /**
   * Output only. The sensitive content category label IDs excluded. Refer to
   * this file https://storage.googleapis.com/adx-rtb-dictionaries/content-
   * labels.txt for category IDs.
   *
   * @var string[]
   */
  public $excludedSensitiveCategoryIds;
  protected $geoTargetingType = CriteriaTargeting::class;
  protected $geoTargetingDataType = '';
  protected $inventorySizeTargetingType = InventorySizeTargeting::class;
  protected $inventorySizeTargetingDataType = '';
  protected $inventoryTypeTargetingType = InventoryTypeTargeting::class;
  protected $inventoryTypeTargetingDataType = '';
  protected $placementTargetingType = PlacementTargeting::class;
  protected $placementTargetingDataType = '';
  protected $technologyTargetingType = TechnologyTargeting::class;
  protected $technologyTargetingDataType = '';
  protected $userListTargetingType = CriteriaTargeting::class;
  protected $userListTargetingDataType = '';
  protected $verticalTargetingType = CriteriaTargeting::class;
  protected $verticalTargetingDataType = '';
  protected $videoTargetingType = VideoTargeting::class;
  protected $videoTargetingDataType = '';

  /**
   * Daypart targeting information.
   *
   * @param DayPartTargeting $daypartTargeting
   */
  public function setDaypartTargeting(DayPartTargeting $daypartTargeting)
  {
    $this->daypartTargeting = $daypartTargeting;
  }
  /**
   * @return DayPartTargeting
   */
  public function getDaypartTargeting()
  {
    return $this->daypartTargeting;
  }
  /**
   * Output only. The sensitive content category label IDs excluded. Refer to
   * this file https://storage.googleapis.com/adx-rtb-dictionaries/content-
   * labels.txt for category IDs.
   *
   * @param string[] $excludedSensitiveCategoryIds
   */
  public function setExcludedSensitiveCategoryIds($excludedSensitiveCategoryIds)
  {
    $this->excludedSensitiveCategoryIds = $excludedSensitiveCategoryIds;
  }
  /**
   * @return string[]
   */
  public function getExcludedSensitiveCategoryIds()
  {
    return $this->excludedSensitiveCategoryIds;
  }
  /**
   * Output only. Geo criteria IDs to be included/excluded.
   *
   * @param CriteriaTargeting $geoTargeting
   */
  public function setGeoTargeting(CriteriaTargeting $geoTargeting)
  {
    $this->geoTargeting = $geoTargeting;
  }
  /**
   * @return CriteriaTargeting
   */
  public function getGeoTargeting()
  {
    return $this->geoTargeting;
  }
  /**
   * Output only. Inventory sizes to be included/excluded.
   *
   * @param InventorySizeTargeting $inventorySizeTargeting
   */
  public function setInventorySizeTargeting(InventorySizeTargeting $inventorySizeTargeting)
  {
    $this->inventorySizeTargeting = $inventorySizeTargeting;
  }
  /**
   * @return InventorySizeTargeting
   */
  public function getInventorySizeTargeting()
  {
    return $this->inventorySizeTargeting;
  }
  /**
   * Output only. Inventory type targeting information.
   *
   * @param InventoryTypeTargeting $inventoryTypeTargeting
   */
  public function setInventoryTypeTargeting(InventoryTypeTargeting $inventoryTypeTargeting)
  {
    $this->inventoryTypeTargeting = $inventoryTypeTargeting;
  }
  /**
   * @return InventoryTypeTargeting
   */
  public function getInventoryTypeTargeting()
  {
    return $this->inventoryTypeTargeting;
  }
  /**
   * Output only. Placement targeting information, for example, URL, mobile
   * applications.
   *
   * @param PlacementTargeting $placementTargeting
   */
  public function setPlacementTargeting(PlacementTargeting $placementTargeting)
  {
    $this->placementTargeting = $placementTargeting;
  }
  /**
   * @return PlacementTargeting
   */
  public function getPlacementTargeting()
  {
    return $this->placementTargeting;
  }
  /**
   * Output only. Technology targeting information, for example, operating
   * system, device category.
   *
   * @param TechnologyTargeting $technologyTargeting
   */
  public function setTechnologyTargeting(TechnologyTargeting $technologyTargeting)
  {
    $this->technologyTargeting = $technologyTargeting;
  }
  /**
   * @return TechnologyTargeting
   */
  public function getTechnologyTargeting()
  {
    return $this->technologyTargeting;
  }
  /**
   * Buyer user list targeting information. User lists can be uploaded using
   * https://developers.google.com/authorized-buyers/rtb/bulk-uploader.
   *
   * @param CriteriaTargeting $userListTargeting
   */
  public function setUserListTargeting(CriteriaTargeting $userListTargeting)
  {
    $this->userListTargeting = $userListTargeting;
  }
  /**
   * @return CriteriaTargeting
   */
  public function getUserListTargeting()
  {
    return $this->userListTargeting;
  }
  /**
   * Output only. The verticals included or excluded as defined in
   * https://developers.google.com/authorized-buyers/rtb/downloads/publisher-
   * verticals
   *
   * @param CriteriaTargeting $verticalTargeting
   */
  public function setVerticalTargeting(CriteriaTargeting $verticalTargeting)
  {
    $this->verticalTargeting = $verticalTargeting;
  }
  /**
   * @return CriteriaTargeting
   */
  public function getVerticalTargeting()
  {
    return $this->verticalTargeting;
  }
  /**
   * Output only. Video targeting information.
   *
   * @param VideoTargeting $videoTargeting
   */
  public function setVideoTargeting(VideoTargeting $videoTargeting)
  {
    $this->videoTargeting = $videoTargeting;
  }
  /**
   * @return VideoTargeting
   */
  public function getVideoTargeting()
  {
    return $this->videoTargeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MarketplaceTargeting::class, 'Google_Service_AuthorizedBuyersMarketplace_MarketplaceTargeting');
