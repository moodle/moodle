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

namespace Google\Service\AndroidEnterprise;

class ProductAvailabilityChangeEvent extends \Google\Model
{
  /**
   * Conveys no information.
   */
  public const AVAILABILITY_STATUS_unknown = 'unknown';
  /**
   * The previously unavailable product is again available on Google Play.
   */
  public const AVAILABILITY_STATUS_available = 'available';
  /**
   * The product was removed from Google Play.
   */
  public const AVAILABILITY_STATUS_removed = 'removed';
  /**
   * The product was unpublished by the developer.
   */
  public const AVAILABILITY_STATUS_unpublished = 'unpublished';
  /**
   * The new state of the product. This field will always be present.
   *
   * @var string
   */
  public $availabilityStatus;
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which the
   * product availability changed. This field will always be present.
   *
   * @var string
   */
  public $productId;

  /**
   * The new state of the product. This field will always be present.
   *
   * Accepted values: unknown, available, removed, unpublished
   *
   * @param self::AVAILABILITY_STATUS_* $availabilityStatus
   */
  public function setAvailabilityStatus($availabilityStatus)
  {
    $this->availabilityStatus = $availabilityStatus;
  }
  /**
   * @return self::AVAILABILITY_STATUS_*
   */
  public function getAvailabilityStatus()
  {
    return $this->availabilityStatus;
  }
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which the
   * product availability changed. This field will always be present.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductAvailabilityChangeEvent::class, 'Google_Service_AndroidEnterprise_ProductAvailabilityChangeEvent');
