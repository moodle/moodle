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

namespace Google\Service\MyBusinessBusinessInformation;

class Metadata extends \Google\Model
{
  /**
   * Output only. Indicates whether the location can be deleted using the API.
   *
   * @var bool
   */
  public $canDelete;
  /**
   * Output only. Indicates if the listing is eligible for business calls.
   *
   * @var bool
   */
  public $canHaveBusinessCalls;
  /**
   * Output only. Indicates if the listing is eligible for food menu.
   *
   * @var bool
   */
  public $canHaveFoodMenus;
  /**
   * Output only. Indicates if the listing can modify the service list.
   *
   * @var bool
   */
  public $canModifyServiceList;
  /**
   * Output only. Indicates whether the location can operate on Health data.
   *
   * @var bool
   */
  public $canOperateHealthData;
  /**
   * Output only. Indicates if the listing can manage local posts. Deprecated:
   * This field is no longer populated and will be removed in a future version.
   *
   * @deprecated
   * @var bool
   */
  public $canOperateLocalPost;
  /**
   * Output only. Indicates whether the location can operate on Lodging data.
   *
   * @var bool
   */
  public $canOperateLodgingData;
  /**
   * Output only. The location resource that this location duplicates.
   *
   * @var string
   */
  public $duplicateLocation;
  /**
   * Output only. Indicates whether the place ID associated with this location
   * has updates that need to be updated or rejected by the client. If this
   * boolean is set, you should call the `getGoogleUpdated` method to lookup
   * information that's needs to be verified.
   *
   * @var bool
   */
  public $hasGoogleUpdated;
  /**
   * Output only. Indicates whether any of this Location's properties are in the
   * edit pending state.
   *
   * @var bool
   */
  public $hasPendingEdits;
  /**
   * Output only. Indicates if the listing has Voice of Merchant. If this
   * boolean is false, you should call the locations.getVoiceOfMerchantState API
   * to get details as to why they do not have Voice of Merchant.
   *
   * @var bool
   */
  public $hasVoiceOfMerchant;
  /**
   * Output only.
   *
   * @var bool
   */
  public $isParticularlyPersonalPlace;
  /**
   * Output only. A link to the location on Maps.
   *
   * @var string
   */
  public $mapsUri;
  /**
   * Output only. A link to the page on Google Search where a customer can leave
   * a review for the location.
   *
   * @var string
   */
  public $newReviewUri;
  /**
   * Output only. If this locationappears on Google Maps, this field is
   * populated with the place ID for the location. This ID can be used in
   * various Places APIs. This field can be set during Create calls, but not for
   * Update.
   *
   * @var string
   */
  public $placeId;

  /**
   * Output only. Indicates whether the location can be deleted using the API.
   *
   * @param bool $canDelete
   */
  public function setCanDelete($canDelete)
  {
    $this->canDelete = $canDelete;
  }
  /**
   * @return bool
   */
  public function getCanDelete()
  {
    return $this->canDelete;
  }
  /**
   * Output only. Indicates if the listing is eligible for business calls.
   *
   * @param bool $canHaveBusinessCalls
   */
  public function setCanHaveBusinessCalls($canHaveBusinessCalls)
  {
    $this->canHaveBusinessCalls = $canHaveBusinessCalls;
  }
  /**
   * @return bool
   */
  public function getCanHaveBusinessCalls()
  {
    return $this->canHaveBusinessCalls;
  }
  /**
   * Output only. Indicates if the listing is eligible for food menu.
   *
   * @param bool $canHaveFoodMenus
   */
  public function setCanHaveFoodMenus($canHaveFoodMenus)
  {
    $this->canHaveFoodMenus = $canHaveFoodMenus;
  }
  /**
   * @return bool
   */
  public function getCanHaveFoodMenus()
  {
    return $this->canHaveFoodMenus;
  }
  /**
   * Output only. Indicates if the listing can modify the service list.
   *
   * @param bool $canModifyServiceList
   */
  public function setCanModifyServiceList($canModifyServiceList)
  {
    $this->canModifyServiceList = $canModifyServiceList;
  }
  /**
   * @return bool
   */
  public function getCanModifyServiceList()
  {
    return $this->canModifyServiceList;
  }
  /**
   * Output only. Indicates whether the location can operate on Health data.
   *
   * @param bool $canOperateHealthData
   */
  public function setCanOperateHealthData($canOperateHealthData)
  {
    $this->canOperateHealthData = $canOperateHealthData;
  }
  /**
   * @return bool
   */
  public function getCanOperateHealthData()
  {
    return $this->canOperateHealthData;
  }
  /**
   * Output only. Indicates if the listing can manage local posts. Deprecated:
   * This field is no longer populated and will be removed in a future version.
   *
   * @deprecated
   * @param bool $canOperateLocalPost
   */
  public function setCanOperateLocalPost($canOperateLocalPost)
  {
    $this->canOperateLocalPost = $canOperateLocalPost;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanOperateLocalPost()
  {
    return $this->canOperateLocalPost;
  }
  /**
   * Output only. Indicates whether the location can operate on Lodging data.
   *
   * @param bool $canOperateLodgingData
   */
  public function setCanOperateLodgingData($canOperateLodgingData)
  {
    $this->canOperateLodgingData = $canOperateLodgingData;
  }
  /**
   * @return bool
   */
  public function getCanOperateLodgingData()
  {
    return $this->canOperateLodgingData;
  }
  /**
   * Output only. The location resource that this location duplicates.
   *
   * @param string $duplicateLocation
   */
  public function setDuplicateLocation($duplicateLocation)
  {
    $this->duplicateLocation = $duplicateLocation;
  }
  /**
   * @return string
   */
  public function getDuplicateLocation()
  {
    return $this->duplicateLocation;
  }
  /**
   * Output only. Indicates whether the place ID associated with this location
   * has updates that need to be updated or rejected by the client. If this
   * boolean is set, you should call the `getGoogleUpdated` method to lookup
   * information that's needs to be verified.
   *
   * @param bool $hasGoogleUpdated
   */
  public function setHasGoogleUpdated($hasGoogleUpdated)
  {
    $this->hasGoogleUpdated = $hasGoogleUpdated;
  }
  /**
   * @return bool
   */
  public function getHasGoogleUpdated()
  {
    return $this->hasGoogleUpdated;
  }
  /**
   * Output only. Indicates whether any of this Location's properties are in the
   * edit pending state.
   *
   * @param bool $hasPendingEdits
   */
  public function setHasPendingEdits($hasPendingEdits)
  {
    $this->hasPendingEdits = $hasPendingEdits;
  }
  /**
   * @return bool
   */
  public function getHasPendingEdits()
  {
    return $this->hasPendingEdits;
  }
  /**
   * Output only. Indicates if the listing has Voice of Merchant. If this
   * boolean is false, you should call the locations.getVoiceOfMerchantState API
   * to get details as to why they do not have Voice of Merchant.
   *
   * @param bool $hasVoiceOfMerchant
   */
  public function setHasVoiceOfMerchant($hasVoiceOfMerchant)
  {
    $this->hasVoiceOfMerchant = $hasVoiceOfMerchant;
  }
  /**
   * @return bool
   */
  public function getHasVoiceOfMerchant()
  {
    return $this->hasVoiceOfMerchant;
  }
  /**
   * Output only.
   *
   * @param bool $isParticularlyPersonalPlace
   */
  public function setIsParticularlyPersonalPlace($isParticularlyPersonalPlace)
  {
    $this->isParticularlyPersonalPlace = $isParticularlyPersonalPlace;
  }
  /**
   * @return bool
   */
  public function getIsParticularlyPersonalPlace()
  {
    return $this->isParticularlyPersonalPlace;
  }
  /**
   * Output only. A link to the location on Maps.
   *
   * @param string $mapsUri
   */
  public function setMapsUri($mapsUri)
  {
    $this->mapsUri = $mapsUri;
  }
  /**
   * @return string
   */
  public function getMapsUri()
  {
    return $this->mapsUri;
  }
  /**
   * Output only. A link to the page on Google Search where a customer can leave
   * a review for the location.
   *
   * @param string $newReviewUri
   */
  public function setNewReviewUri($newReviewUri)
  {
    $this->newReviewUri = $newReviewUri;
  }
  /**
   * @return string
   */
  public function getNewReviewUri()
  {
    return $this->newReviewUri;
  }
  /**
   * Output only. If this locationappears on Google Maps, this field is
   * populated with the place ID for the location. This ID can be used in
   * various Places APIs. This field can be set during Create calls, but not for
   * Update.
   *
   * @param string $placeId
   */
  public function setPlaceId($placeId)
  {
    $this->placeId = $placeId;
  }
  /**
   * @return string
   */
  public function getPlaceId()
  {
    return $this->placeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metadata::class, 'Google_Service_MyBusinessBusinessInformation_Metadata');
