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

class SchedulingPolicy extends \Google\Model
{
  /**
   * Optional. If present and > 0, the reservation will attempt to limit the
   * concurrency of jobs running for any particular project within it to the
   * given value. This feature is not yet generally available.
   *
   * @var string
   */
  public $concurrency;
  /**
   * Optional. If present and > 0, the reservation will attempt to limit the
   * slot consumption of queries running for any particular project within it to
   * the given value. This feature is not yet generally available.
   *
   * @var string
   */
  public $maxSlots;

  /**
   * Optional. If present and > 0, the reservation will attempt to limit the
   * concurrency of jobs running for any particular project within it to the
   * given value. This feature is not yet generally available.
   *
   * @param string $concurrency
   */
  public function setConcurrency($concurrency)
  {
    $this->concurrency = $concurrency;
  }
  /**
   * @return string
   */
  public function getConcurrency()
  {
    return $this->concurrency;
  }
  /**
   * Optional. If present and > 0, the reservation will attempt to limit the
   * slot consumption of queries running for any particular project within it to
   * the given value. This feature is not yet generally available.
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
class_alias(SchedulingPolicy::class, 'Google_Service_BigQueryReservation_SchedulingPolicy');
