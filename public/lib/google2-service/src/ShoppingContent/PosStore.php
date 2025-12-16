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

namespace Google\Service\ShoppingContent;

class PosStore extends \Google\Collection
{
  protected $collection_key = 'gcidCategory';
  /**
   * The business type of the store.
   *
   * @var string[]
   */
  public $gcidCategory;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#posStore`"
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. The matching status of POS store and Google Business Profile
   * store. Possible values are: - "`matched`": The POS store is successfully
   * matched with the Google Business Profile store. - "`failed`": The POS store
   * is not matched with the Google Business Profile store. See
   * matching_status_hint for further details. Note that there is up to 48 hours
   * propagation delay for changes in Merchant Center (e.g. creation of new
   * account, accounts linking) and Google Business Profile (e.g. store address
   * update) which may affect the matching status. In such cases, after a delay
   * call [pos.list](https://developers.google.com/shopping-
   * content/reference/rest/v2.1/pos/list) to retrieve the updated matching
   * status.
   *
   * @var string
   */
  public $matchingStatus;
  /**
   * Output only. The hint of why the matching has failed. This is only set when
   * matching_status=failed. Possible values are: - "`linked-store-not-found`":
   * There aren't any Google Business Profile stores available for matching.
   * Connect your Merchant Center account with the Google Business Profile
   * account. Or add a new Google Business Profile store corresponding to the
   * POS store. - "`store-match-not-found`": The provided POS store couldn't be
   * matched to any of the connected Google Business Profile stores. Merchant
   * Center account is connected correctly and stores are available on Google
   * Business Profile, but POS store location address does not match with Google
   * Business Profile stores' addresses. Update POS store address or Google
   * Business Profile store address to match correctly. - "`store-match-
   * unverified`": The provided POS store couldn't be matched to any of the
   * connected Google Business Profile stores, as the matched Google Business
   * Profile store is unverified. Go through the Google Business Profile
   * verification process to match correctly.
   *
   * @var string
   */
  public $matchingStatusHint;
  /**
   * The store phone number.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * The Google Place Id of the store location.
   *
   * @var string
   */
  public $placeId;
  /**
   * Required. The street address of the store.
   *
   * @var string
   */
  public $storeAddress;
  /**
   * Required. A store identifier that is unique for the given merchant.
   *
   * @var string
   */
  public $storeCode;
  /**
   * The merchant or store name.
   *
   * @var string
   */
  public $storeName;
  /**
   * The website url for the store or merchant.
   *
   * @var string
   */
  public $websiteUrl;

  /**
   * The business type of the store.
   *
   * @param string[] $gcidCategory
   */
  public function setGcidCategory($gcidCategory)
  {
    $this->gcidCategory = $gcidCategory;
  }
  /**
   * @return string[]
   */
  public function getGcidCategory()
  {
    return $this->gcidCategory;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#posStore`"
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. The matching status of POS store and Google Business Profile
   * store. Possible values are: - "`matched`": The POS store is successfully
   * matched with the Google Business Profile store. - "`failed`": The POS store
   * is not matched with the Google Business Profile store. See
   * matching_status_hint for further details. Note that there is up to 48 hours
   * propagation delay for changes in Merchant Center (e.g. creation of new
   * account, accounts linking) and Google Business Profile (e.g. store address
   * update) which may affect the matching status. In such cases, after a delay
   * call [pos.list](https://developers.google.com/shopping-
   * content/reference/rest/v2.1/pos/list) to retrieve the updated matching
   * status.
   *
   * @param string $matchingStatus
   */
  public function setMatchingStatus($matchingStatus)
  {
    $this->matchingStatus = $matchingStatus;
  }
  /**
   * @return string
   */
  public function getMatchingStatus()
  {
    return $this->matchingStatus;
  }
  /**
   * Output only. The hint of why the matching has failed. This is only set when
   * matching_status=failed. Possible values are: - "`linked-store-not-found`":
   * There aren't any Google Business Profile stores available for matching.
   * Connect your Merchant Center account with the Google Business Profile
   * account. Or add a new Google Business Profile store corresponding to the
   * POS store. - "`store-match-not-found`": The provided POS store couldn't be
   * matched to any of the connected Google Business Profile stores. Merchant
   * Center account is connected correctly and stores are available on Google
   * Business Profile, but POS store location address does not match with Google
   * Business Profile stores' addresses. Update POS store address or Google
   * Business Profile store address to match correctly. - "`store-match-
   * unverified`": The provided POS store couldn't be matched to any of the
   * connected Google Business Profile stores, as the matched Google Business
   * Profile store is unverified. Go through the Google Business Profile
   * verification process to match correctly.
   *
   * @param string $matchingStatusHint
   */
  public function setMatchingStatusHint($matchingStatusHint)
  {
    $this->matchingStatusHint = $matchingStatusHint;
  }
  /**
   * @return string
   */
  public function getMatchingStatusHint()
  {
    return $this->matchingStatusHint;
  }
  /**
   * The store phone number.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * The Google Place Id of the store location.
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
  /**
   * Required. The street address of the store.
   *
   * @param string $storeAddress
   */
  public function setStoreAddress($storeAddress)
  {
    $this->storeAddress = $storeAddress;
  }
  /**
   * @return string
   */
  public function getStoreAddress()
  {
    return $this->storeAddress;
  }
  /**
   * Required. A store identifier that is unique for the given merchant.
   *
   * @param string $storeCode
   */
  public function setStoreCode($storeCode)
  {
    $this->storeCode = $storeCode;
  }
  /**
   * @return string
   */
  public function getStoreCode()
  {
    return $this->storeCode;
  }
  /**
   * The merchant or store name.
   *
   * @param string $storeName
   */
  public function setStoreName($storeName)
  {
    $this->storeName = $storeName;
  }
  /**
   * @return string
   */
  public function getStoreName()
  {
    return $this->storeName;
  }
  /**
   * The website url for the store or merchant.
   *
   * @param string $websiteUrl
   */
  public function setWebsiteUrl($websiteUrl)
  {
    $this->websiteUrl = $websiteUrl;
  }
  /**
   * @return string
   */
  public function getWebsiteUrl()
  {
    return $this->websiteUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PosStore::class, 'Google_Service_ShoppingContent_PosStore');
