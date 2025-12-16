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

class FutureReservationsScopedList extends \Google\Collection
{
  protected $collection_key = 'futureReservations';
  protected $futureReservationsType = FutureReservation::class;
  protected $futureReservationsDataType = 'array';
  protected $warningType = FutureReservationsScopedListWarning::class;
  protected $warningDataType = '';

  /**
   * A list of future reservations contained in this scope.
   *
   * @param FutureReservation[] $futureReservations
   */
  public function setFutureReservations($futureReservations)
  {
    $this->futureReservations = $futureReservations;
  }
  /**
   * @return FutureReservation[]
   */
  public function getFutureReservations()
  {
    return $this->futureReservations;
  }
  /**
   * Informational warning which replaces the list of future reservations when
   * the list is empty.
   *
   * @param FutureReservationsScopedListWarning $warning
   */
  public function setWarning(FutureReservationsScopedListWarning $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return FutureReservationsScopedListWarning
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureReservationsScopedList::class, 'Google_Service_Compute_FutureReservationsScopedList');
