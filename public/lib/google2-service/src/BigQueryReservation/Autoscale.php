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

namespace Google\Service\BigQueryReservation;

class Autoscale extends \Google\Model
{
  /**
   * Output only. The slot capacity added to this reservation when autoscale
   * happens. Will be between [0, max_slots]. Note: after users reduce
   * max_slots, it may take a while before it can be propagated, so
   * current_slots may stay in the original value and could be larger than
   * max_slots for that brief period (less than one minute)
   *
   * @var string
   */
  public $currentSlots;
  /**
   * Optional. Number of slots to be scaled when needed.
   *
   * @var string
   */
  public $maxSlots;

  /**
   * Output only. The slot capacity added to this reservation when autoscale
   * happens. Will be between [0, max_slots]. Note: after users reduce
   * max_slots, it may take a while before it can be propagated, so
   * current_slots may stay in the original value and could be larger than
   * max_slots for that brief period (less than one minute)
   *
   * @param string $currentSlots
   */
  public function setCurrentSlots($currentSlots)
  {
    $this->currentSlots = $currentSlots;
  }
  /**
   * @return string
   */
  public function getCurrentSlots()
  {
    return $this->currentSlots;
  }
  /**
   * Optional. Number of slots to be scaled when needed.
   *
   * @param string $maxSlots
   */
  public function setMaxSlots($maxSlots)
  {
    $this->maxSlots = $maxSlots;
  }
  /**
   * @return string
   */
  public function getMaxSlots()
  {
    return $this->maxSlots;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Autoscale::class, 'Google_Service_BigQueryReservation_Autoscale');
