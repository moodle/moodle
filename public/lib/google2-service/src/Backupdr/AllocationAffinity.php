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

namespace Google\Service\Backupdr;

class AllocationAffinity extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const CONSUME_RESERVATION_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Do not consume from any allocated capacity.
   */
  public const CONSUME_RESERVATION_TYPE_NO_RESERVATION = 'NO_RESERVATION';
  /**
   * Consume any allocation available.
   */
  public const CONSUME_RESERVATION_TYPE_ANY_RESERVATION = 'ANY_RESERVATION';
  /**
   * Must consume from a specific reservation. Must specify key value fields for
   * specifying the reservations.
   */
  public const CONSUME_RESERVATION_TYPE_SPECIFIC_RESERVATION = 'SPECIFIC_RESERVATION';
  protected $collection_key = 'values';
  /**
   * Optional. Specifies the type of reservation from which this instance can
   * consume
   *
   * @var string
   */
  public $consumeReservationType;
  /**
   * Optional. Corresponds to the label key of a reservation resource.
   *
   * @var string
   */
  public $key;
  /**
   * Optional. Corresponds to the label values of a reservation resource.
   *
   * @var string[]
   */
  public $values;

  /**
   * Optional. Specifies the type of reservation from which this instance can
   * consume
   *
   * Accepted values: TYPE_UNSPECIFIED, NO_RESERVATION, ANY_RESERVATION,
   * SPECIFIC_RESERVATION
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
   * Optional. Corresponds to the label key of a reservation resource.
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
   * Optional. Corresponds to the label values of a reservation resource.
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
class_alias(AllocationAffinity::class, 'Google_Service_Backupdr_AllocationAffinity');
