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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2RemoveFulfillmentPlacesRequest extends \Google\Collection
{
  protected $collection_key = 'placeIds';
  /**
   * If set to true, and the Product is not found, the fulfillment information
   * will still be processed and retained for at most 1 day and processed once
   * the Product is created. If set to false, a NOT_FOUND error is returned if
   * the Product is not found.
   *
   * @var bool
   */
  public $allowMissing;
  /**
   * Required. The IDs for this type, such as the store IDs for "pickup-in-
   * store" or the region IDs for "same-day-delivery", to be removed for this
   * type. At least 1 value is required, and a maximum of 2000 values are
   * allowed. Each value must be a string with a length limit of 10 characters,
   * matching the pattern `[a-zA-Z0-9_-]+`, such as "store1" or "REGION-2".
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string[]
   */
  public $placeIds;
  /**
   * The time when the fulfillment updates are issued, used to prevent out-of-
   * order updates on fulfillment information. If not provided, the internal
   * system time will be used.
   *
   * @var string
   */
  public $removeTime;
  /**
   * Required. The fulfillment type, including commonly used types (such as
   * pickup in store and same day delivery), and custom types. Supported values:
   * * "pickup-in-store" * "ship-to-store" * "same-day-delivery" * "next-day-
   * delivery" * "custom-type-1" * "custom-type-2" * "custom-type-3" * "custom-
   * type-4" * "custom-type-5" If this field is set to an invalid value other
   * than these, an INVALID_ARGUMENT error is returned. This field directly
   * corresponds to Product.fulfillment_info.type.
   *
   * @var string
   */
  public $type;

  /**
   * If set to true, and the Product is not found, the fulfillment information
   * will still be processed and retained for at most 1 day and processed once
   * the Product is created. If set to false, a NOT_FOUND error is returned if
   * the Product is not found.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * Required. The IDs for this type, such as the store IDs for "pickup-in-
   * store" or the region IDs for "same-day-delivery", to be removed for this
   * type. At least 1 value is required, and a maximum of 2000 values are
   * allowed. Each value must be a string with a length limit of 10 characters,
   * matching the pattern `[a-zA-Z0-9_-]+`, such as "store1" or "REGION-2".
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @param string[] $placeIds
   */
  public function setPlaceIds($placeIds)
  {
    $this->placeIds = $placeIds;
  }
  /**
   * @return string[]
   */
  public function getPlaceIds()
  {
    return $this->placeIds;
  }
  /**
   * The time when the fulfillment updates are issued, used to prevent out-of-
   * order updates on fulfillment information. If not provided, the internal
   * system time will be used.
   *
   * @param string $removeTime
   */
  public function setRemoveTime($removeTime)
  {
    $this->removeTime = $removeTime;
  }
  /**
   * @return string
   */
  public function getRemoveTime()
  {
    return $this->removeTime;
  }
  /**
   * Required. The fulfillment type, including commonly used types (such as
   * pickup in store and same day delivery), and custom types. Supported values:
   * * "pickup-in-store" * "ship-to-store" * "same-day-delivery" * "next-day-
   * delivery" * "custom-type-1" * "custom-type-2" * "custom-type-3" * "custom-
   * type-4" * "custom-type-5" If this field is set to an invalid value other
   * than these, an INVALID_ARGUMENT error is returned. This field directly
   * corresponds to Product.fulfillment_info.type.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RemoveFulfillmentPlacesRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RemoveFulfillmentPlacesRequest');
