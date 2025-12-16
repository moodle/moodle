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

class ProductDeliveryTimeAreaDeliveryTimeDeliveryTime extends \Google\Model
{
  /**
   * Required. The maximum number of business days (inclusive) between when an
   * order is placed and when the product ships. If a product ships in the same
   * day, set this value to 0.
   *
   * @var int
   */
  public $maxHandlingTimeDays;
  /**
   * Required. The maximum number of business days (inclusive) between when the
   * product ships and when the product is delivered.
   *
   * @var int
   */
  public $maxTransitTimeDays;
  /**
   * Required. The minimum number of business days (inclusive) between when an
   * order is placed and when the product ships. If a product ships in the same
   * day, set this value to 0.
   *
   * @var int
   */
  public $minHandlingTimeDays;
  /**
   * Required. The minimum number of business days (inclusive) between when the
   * product ships and when the product is delivered.
   *
   * @var int
   */
  public $minTransitTimeDays;

  /**
   * Required. The maximum number of business days (inclusive) between when an
   * order is placed and when the product ships. If a product ships in the same
   * day, set this value to 0.
   *
   * @param int $maxHandlingTimeDays
   */
  public function setMaxHandlingTimeDays($maxHandlingTimeDays)
  {
    $this->maxHandlingTimeDays = $maxHandlingTimeDays;
  }
  /**
   * @return int
   */
  public function getMaxHandlingTimeDays()
  {
    return $this->maxHandlingTimeDays;
  }
  /**
   * Required. The maximum number of business days (inclusive) between when the
   * product ships and when the product is delivered.
   *
   * @param int $maxTransitTimeDays
   */
  public function setMaxTransitTimeDays($maxTransitTimeDays)
  {
    $this->maxTransitTimeDays = $maxTransitTimeDays;
  }
  /**
   * @return int
   */
  public function getMaxTransitTimeDays()
  {
    return $this->maxTransitTimeDays;
  }
  /**
   * Required. The minimum number of business days (inclusive) between when an
   * order is placed and when the product ships. If a product ships in the same
   * day, set this value to 0.
   *
   * @param int $minHandlingTimeDays
   */
  public function setMinHandlingTimeDays($minHandlingTimeDays)
  {
    $this->minHandlingTimeDays = $minHandlingTimeDays;
  }
  /**
   * @return int
   */
  public function getMinHandlingTimeDays()
  {
    return $this->minHandlingTimeDays;
  }
  /**
   * Required. The minimum number of business days (inclusive) between when the
   * product ships and when the product is delivered.
   *
   * @param int $minTransitTimeDays
   */
  public function setMinTransitTimeDays($minTransitTimeDays)
  {
    $this->minTransitTimeDays = $minTransitTimeDays;
  }
  /**
   * @return int
   */
  public function getMinTransitTimeDays()
  {
    return $this->minTransitTimeDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductDeliveryTimeAreaDeliveryTimeDeliveryTime::class, 'Google_Service_ShoppingContent_ProductDeliveryTimeAreaDeliveryTimeDeliveryTime');
