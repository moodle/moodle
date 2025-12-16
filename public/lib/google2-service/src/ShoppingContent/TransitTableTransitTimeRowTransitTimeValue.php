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

class TransitTableTransitTimeRowTransitTimeValue extends \Google\Model
{
  /**
   * Must be greater than or equal to `minTransitTimeInDays`.
   *
   * @var string
   */
  public $maxTransitTimeInDays;
  /**
   * Transit time range (min-max) in business days. 0 means same day delivery, 1
   * means next day delivery.
   *
   * @var string
   */
  public $minTransitTimeInDays;

  /**
   * Must be greater than or equal to `minTransitTimeInDays`.
   *
   * @param string $maxTransitTimeInDays
   */
  public function setMaxTransitTimeInDays($maxTransitTimeInDays)
  {
    $this->maxTransitTimeInDays = $maxTransitTimeInDays;
  }
  /**
   * @return string
   */
  public function getMaxTransitTimeInDays()
  {
    return $this->maxTransitTimeInDays;
  }
  /**
   * Transit time range (min-max) in business days. 0 means same day delivery, 1
   * means next day delivery.
   *
   * @param string $minTransitTimeInDays
   */
  public function setMinTransitTimeInDays($minTransitTimeInDays)
  {
    $this->minTransitTimeInDays = $minTransitTimeInDays;
  }
  /**
   * @return string
   */
  public function getMinTransitTimeInDays()
  {
    return $this->minTransitTimeInDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransitTableTransitTimeRowTransitTimeValue::class, 'Google_Service_ShoppingContent_TransitTableTransitTimeRowTransitTimeValue');
