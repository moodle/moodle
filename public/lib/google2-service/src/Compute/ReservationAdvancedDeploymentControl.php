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

namespace Google\Service\Compute;

class ReservationAdvancedDeploymentControl extends \Google\Model
{
  /**
   * Google Cloud does not manage the failure of machines, but provides
   * additional capacity, which is not guaranteed to be available.
   */
  public const RESERVATION_OPERATIONAL_MODE_ALL_CAPACITY = 'ALL_CAPACITY';
  /**
   * Google Cloud manages the failure of machines to provide high availability.
   */
  public const RESERVATION_OPERATIONAL_MODE_HIGHLY_AVAILABLE_CAPACITY = 'HIGHLY_AVAILABLE_CAPACITY';
  public const RESERVATION_OPERATIONAL_MODE_RESERVATION_OPERATIONAL_MODE_UNSPECIFIED = 'RESERVATION_OPERATIONAL_MODE_UNSPECIFIED';
  /**
   * Indicates chosen reservation operational mode for the reservation.
   *
   * @var string
   */
  public $reservationOperationalMode;

  /**
   * Indicates chosen reservation operational mode for the reservation.
   *
   * Accepted values: ALL_CAPACITY, HIGHLY_AVAILABLE_CAPACITY,
   * RESERVATION_OPERATIONAL_MODE_UNSPECIFIED
   *
   * @param self::RESERVATION_OPERATIONAL_MODE_* $reservationOperationalMode
   */
  public function setReservationOperationalMode($reservationOperationalMode)
  {
    $this->reservationOperationalMode = $reservationOperationalMode;
  }
  /**
   * @return self::RESERVATION_OPERATIONAL_MODE_*
   */
  public function getReservationOperationalMode()
  {
    return $this->reservationOperationalMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationAdvancedDeploymentControl::class, 'Google_Service_Compute_ReservationAdvancedDeploymentControl');
