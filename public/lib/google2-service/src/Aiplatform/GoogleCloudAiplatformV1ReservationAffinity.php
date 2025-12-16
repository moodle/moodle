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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ReservationAffinity extends \Google\Collection
{
  /**
   * Default value. This should not be used.
   */
  public const RESERVATION_AFFINITY_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Do not consume from any reserved capacity, only use on-demand.
   */
  public const RESERVATION_AFFINITY_TYPE_NO_RESERVATION = 'NO_RESERVATION';
  /**
   * Consume any reservation available, falling back to on-demand.
   */
  public const RESERVATION_AFFINITY_TYPE_ANY_RESERVATION = 'ANY_RESERVATION';
  /**
   * Consume from a specific reservation. When chosen, the reservation must be
   * identified via the `key` and `values` fields.
   */
  public const RESERVATION_AFFINITY_TYPE_SPECIFIC_RESERVATION = 'SPECIFIC_RESERVATION';
  protected $collection_key = 'values';
  /**
   * Optional. Corresponds to the label key of a reservation resource. To target
   * a SPECIFIC_RESERVATION by name, use `compute.googleapis.com/reservation-
   * name` as the key and specify the name of your reservation as its value.
   *
   * @var string
   */
  public $key;
  /**
   * Required. Specifies the reservation affinity type.
   *
   * @var string
   */
  public $reservationAffinityType;
  /**
   * Optional. Corresponds to the label values of a reservation resource. This
   * must be the full resource name of the reservation or reservation block.
   *
   * @var string[]
   */
  public $values;

  /**
   * Optional. Corresponds to the label key of a reservation resource. To target
   * a SPECIFIC_RESERVATION by name, use `compute.googleapis.com/reservation-
   * name` as the key and specify the name of your reservation as its value.
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
   * Required. Specifies the reservation affinity type.
   *
   * Accepted values: TYPE_UNSPECIFIED, NO_RESERVATION, ANY_RESERVATION,
   * SPECIFIC_RESERVATION
   *
   * @param self::RESERVATION_AFFINITY_TYPE_* $reservationAffinityType
   */
  public function setReservationAffinityType($reservationAffinityType)
  {
    $this->reservationAffinityType = $reservationAffinityType;
  }
  /**
   * @return self::RESERVATION_AFFINITY_TYPE_*
   */
  public function getReservationAffinityType()
  {
    return $this->reservationAffinityType;
  }
  /**
   * Optional. Corresponds to the label values of a reservation resource. This
   * must be the full resource name of the reservation or reservation block.
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
class_alias(GoogleCloudAiplatformV1ReservationAffinity::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReservationAffinity');
