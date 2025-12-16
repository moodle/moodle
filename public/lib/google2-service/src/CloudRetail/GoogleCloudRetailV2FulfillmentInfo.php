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

class GoogleCloudRetailV2FulfillmentInfo extends \Google\Collection
{
  protected $collection_key = 'placeIds';
  /**
   * The IDs for this type, such as the store IDs for
   * FulfillmentInfo.type.pickup-in-store or the region IDs for
   * FulfillmentInfo.type.same-day-delivery. A maximum of 3000 values are
   * allowed. Each value must be a string with a length limit of 30 characters,
   * matching the pattern `[a-zA-Z0-9_-]+`, such as "store1" or "REGION-2".
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string[]
   */
  public $placeIds;
  /**
   * The fulfillment type, including commonly used types (such as pickup in
   * store and same day delivery), and custom types. Customers have to map
   * custom types to their display names before rendering UI. Supported values:
   * * "pickup-in-store" * "ship-to-store" * "same-day-delivery" * "next-day-
   * delivery" * "custom-type-1" * "custom-type-2" * "custom-type-3" * "custom-
   * type-4" * "custom-type-5" If this field is set to an invalid value other
   * than these, an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $type;

  /**
   * The IDs for this type, such as the store IDs for
   * FulfillmentInfo.type.pickup-in-store or the region IDs for
   * FulfillmentInfo.type.same-day-delivery. A maximum of 3000 values are
   * allowed. Each value must be a string with a length limit of 30 characters,
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
   * The fulfillment type, including commonly used types (such as pickup in
   * store and same day delivery), and custom types. Customers have to map
   * custom types to their display names before rendering UI. Supported values:
   * * "pickup-in-store" * "ship-to-store" * "same-day-delivery" * "next-day-
   * delivery" * "custom-type-1" * "custom-type-2" * "custom-type-3" * "custom-
   * type-4" * "custom-type-5" If this field is set to an invalid value other
   * than these, an INVALID_ARGUMENT error is returned.
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
class_alias(GoogleCloudRetailV2FulfillmentInfo::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2FulfillmentInfo');
