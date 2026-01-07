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

class BulkEditAssignedTargetingOptionsRequest extends \Google\Collection
{
  protected $collection_key = 'lineItemIds';
  protected $createRequestsType = CreateAssignedTargetingOptionsRequest::class;
  protected $createRequestsDataType = 'array';
  protected $deleteRequestsType = DeleteAssignedTargetingOptionsRequest::class;
  protected $deleteRequestsDataType = 'array';
  /**
   * Required. The ID of the line items whose targeting is being updated.
   *
   * @var string[]
   */
  public $lineItemIds;

  /**
   * The assigned targeting options to create in batch, specified as a list of
   * CreateAssignedTargetingOptionsRequest. Supported targeting types include: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_APP` *
   * `TARGETING_TYPE_APP_CATEGORY` * `TARGETING_TYPE_AUDIENCE_GROUP` *
   * `TARGETING_TYPE_AUDIO_CONTENT_TYPE` *
   * `TARGETING_TYPE_AUTHORIZED_SELLER_STATUS` * `TARGETING_TYPE_BROWSER` *
   * `TARGETING_TYPE_BUSINESS_CHAIN` * `TARGETING_TYPE_CARRIER_AND_ISP` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_CHANNEL` *
   * `TARGETING_TYPE_CONTENT_DURATION` * `TARGETING_TYPE_CONTENT_GENRE` *
   * `TARGETING_TYPE_CONTENT_INSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_STREAM_TYPE` * `TARGETING_TYPE_DAY_AND_TIME` *
   * `TARGETING_TYPE_DEVICE_MAKE_MODEL` * `TARGETING_TYPE_DEVICE_TYPE` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_EXCHANGE` *
   * `TARGETING_TYPE_GENDER` * `TARGETING_TYPE_GEO_REGION` *
   * `TARGETING_TYPE_HOUSEHOLD_INCOME` * `TARGETING_TYPE_INVENTORY_SOURCE` *
   * `TARGETING_TYPE_INVENTORY_SOURCE_GROUP` * `TARGETING_TYPE_KEYWORD` *
   * `TARGETING_TYPE_LANGUAGE` * `TARGETING_TYPE_NATIVE_CONTENT_POSITION` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OMID` *
   * `TARGETING_TYPE_ON_SCREEN_POSITION` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` * `TARGETING_TYPE_POI` *
   * `TARGETING_TYPE_PROXIMITY_LOCATION_LIST` *
   * `TARGETING_TYPE_REGIONAL_LOCATION_LIST` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` *
   * `TARGETING_TYPE_SUB_EXCHANGE` * `TARGETING_TYPE_THIRD_PARTY_VERIFIER` *
   * `TARGETING_TYPE_URL` * `TARGETING_TYPE_USER_REWARDED_CONTENT` *
   * `TARGETING_TYPE_VIDEO_PLAYER_SIZE` * `TARGETING_TYPE_VIEWABILITY`
   *
   * @param CreateAssignedTargetingOptionsRequest[] $createRequests
   */
  public function setCreateRequests($createRequests)
  {
    $this->createRequests = $createRequests;
  }
  /**
   * @return CreateAssignedTargetingOptionsRequest[]
   */
  public function getCreateRequests()
  {
    return $this->createRequests;
  }
  /**
   * The assigned targeting options to delete in batch, specified as a list of
   * DeleteAssignedTargetingOptionsRequest. Supported targeting types include: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_APP` *
   * `TARGETING_TYPE_APP_CATEGORY` * `TARGETING_TYPE_AUDIENCE_GROUP` *
   * `TARGETING_TYPE_AUDIO_CONTENT_TYPE` *
   * `TARGETING_TYPE_AUTHORIZED_SELLER_STATUS` * `TARGETING_TYPE_BROWSER` *
   * `TARGETING_TYPE_BUSINESS_CHAIN` * `TARGETING_TYPE_CARRIER_AND_ISP` *
   * `TARGETING_TYPE_CATEGORY` * `TARGETING_TYPE_CHANNEL` *
   * `TARGETING_TYPE_CONTENT_DURATION` * `TARGETING_TYPE_CONTENT_GENRE` *
   * `TARGETING_TYPE_CONTENT_INSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_STREAM_TYPE` * `TARGETING_TYPE_DAY_AND_TIME` *
   * `TARGETING_TYPE_DEVICE_MAKE_MODEL` * `TARGETING_TYPE_DEVICE_TYPE` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_EXCHANGE` *
   * `TARGETING_TYPE_GENDER` * `TARGETING_TYPE_GEO_REGION` *
   * `TARGETING_TYPE_HOUSEHOLD_INCOME` * `TARGETING_TYPE_INVENTORY_SOURCE` *
   * `TARGETING_TYPE_INVENTORY_SOURCE_GROUP` * `TARGETING_TYPE_KEYWORD` *
   * `TARGETING_TYPE_LANGUAGE` * `TARGETING_TYPE_NATIVE_CONTENT_POSITION` *
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST` * `TARGETING_TYPE_OMID` *
   * `TARGETING_TYPE_ON_SCREEN_POSITION` * `TARGETING_TYPE_OPERATING_SYSTEM` *
   * `TARGETING_TYPE_PARENTAL_STATUS` * `TARGETING_TYPE_POI` *
   * `TARGETING_TYPE_PROXIMITY_LOCATION_LIST` *
   * `TARGETING_TYPE_REGIONAL_LOCATION_LIST` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` *
   * `TARGETING_TYPE_SUB_EXCHANGE` * `TARGETING_TYPE_THIRD_PARTY_VERIFIER` *
   * `TARGETING_TYPE_URL` * `TARGETING_TYPE_USER_REWARDED_CONTENT` *
   * `TARGETING_TYPE_VIDEO_PLAYER_SIZE` * `TARGETING_TYPE_VIEWABILITY`
   *
   * @param DeleteAssignedTargetingOptionsRequest[] $deleteRequests
   */
  public function setDeleteRequests($deleteRequests)
  {
    $this->deleteRequests = $deleteRequests;
  }
  /**
   * @return DeleteAssignedTargetingOptionsRequest[]
   */
  public function getDeleteRequests()
  {
    return $this->deleteRequests;
  }
  /**
   * Required. The ID of the line items whose targeting is being updated.
   *
   * @param string[] $lineItemIds
   */
  public function setLineItemIds($lineItemIds)
  {
    $this->lineItemIds = $lineItemIds;
  }
  /**
   * @return string[]
   */
  public function getLineItemIds()
  {
    return $this->lineItemIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkEditAssignedTargetingOptionsRequest::class, 'Google_Service_DisplayVideo_BulkEditAssignedTargetingOptionsRequest');
