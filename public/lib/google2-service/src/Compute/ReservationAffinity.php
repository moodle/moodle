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

class ReservationAffinity extends \Google\Collection
{
  /**
   * Consume any allocation available.
   */
  public const CONSUME_RESERVATION_TYPE_ANY_RESERVATION = 'ANY_RESERVATION';
  /**
   * Do not consume from any allocated capacity.
   */
  public const CONSUME_RESERVATION_TYPE_NO_RESERVATION = 'NO_RESERVATION';
  /**
   * Must consume from a specific reservation. Must specify key value fields for
   * specifying the reservations.
   */
  public const CONSUME_RESERVATION_TYPE_SPECIFIC_RESERVATION = 'SPECIFIC_RESERVATION';
  public const CONSUME_RESERVATION_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  protected $collection_key = 'values';
  /**
   * Specifies the type of reservation from which this instance can consume
   * resources: ANY_RESERVATION (default),SPECIFIC_RESERVATION, or
   * NO_RESERVATION. See Consuming reserved instances for examples.
   *
   * @var string
   */
  public $consumeReservationType;
  /**
   * Corresponds to the label key of a reservation resource. To target
   * aSPECIFIC_RESERVATION by name, specifygoogleapis.com/reservation-name as
   * the key and specify the name of your reservation as its value.
   *
   * @var string
   */
  public $key;
  /**
   * Corresponds to the label values of a reservation resource. This can be
   * either a name to a reservation in the same project or "projects/different-
   * project/reservations/some-reservation-name" to target a shared reservation
   * in the same zone but in a different project.
   *
   * @var string[]
   */
  public $values;

  /**
   * Specifies the type of reservation from which this instance can consume
   * resources: ANY_RESERVATION (default),SPECIFIC_RESERVATION, or
   * NO_RESERVATION. See Consuming reserved instances for examples.
   *
   * Accepted values: ANY_RESERVATION, NO_RESERVATION, SPECIFIC_RESERVATION,
   * UNSPECIFIED
   *
   * @param self::CONSUME_RESERVATION_TYPE_* $consumeReservationType
   */
  public function setConsumeReservationType($consumeReservationType)
  {
    $this->consumeReservationType = $consumeReservationType;
  }
  /**
   * @return self::CONSUME_RESERVATION_TYPE_*
   */
  public function getConsumeReservationType()
  {
    return $this->consumeReservationType;
  }
  /**
   * Corresponds to the label key of a reservation resource. To target
   * aSPECIFIC_RESERVATION by name, specifygoogleapis.com/reservation-name as
   * the key and specify the name of your reservation as its value.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Corresponds to the label values of a reservation resource. This can be
   * either a name to a reservation in the same project or "projects/different-
   * project/reservations/some-reservation-name" to target a shared reservation
   * in the same zone but in a different project.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationAffinity::class, 'Google_Service_Compute_ReservationAffinity');
