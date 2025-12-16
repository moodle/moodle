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

namespace Google\Service\ManufacturerCenter;

class DestinationStatus extends \Google\Collection
{
  /**
   * Unspecified status, never used.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The product is used for this destination.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * The decision is still pending.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The product is disapproved. Please look at the issues.
   */
  public const STATUS_DISAPPROVED = 'DISAPPROVED';
  protected $collection_key = 'pendingCountries';
  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where the offer is
   * approved.
   *
   * @var string[]
   */
  public $approvedCountries;
  /**
   * The name of the destination.
   *
   * @var string
   */
  public $destination;
  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where the offer is
   * disapproved.
   *
   * @var string[]
   */
  public $disapprovedCountries;
  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where the offer is
   * pending approval.
   *
   * @var string[]
   */
  public $pendingCountries;
  /**
   * The status of the destination.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where the offer is
   * approved.
   *
   * @param string[] $approvedCountries
   */
  public function setApprovedCountries($approvedCountries)
  {
    $this->approvedCountries = $approvedCountries;
  }
  /**
   * @return string[]
   */
  public function getApprovedCountries()
  {
    return $this->approvedCountries;
  }
  /**
   * The name of the destination.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where the offer is
   * disapproved.
   *
   * @param string[] $disapprovedCountries
   */
  public function setDisapprovedCountries($disapprovedCountries)
  {
    $this->disapprovedCountries = $disapprovedCountries;
  }
  /**
   * @return string[]
   */
  public function getDisapprovedCountries()
  {
    return $this->disapprovedCountries;
  }
  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where the offer is
   * pending approval.
   *
   * @param string[] $pendingCountries
   */
  public function setPendingCountries($pendingCountries)
  {
    $this->pendingCountries = $pendingCountries;
  }
  /**
   * @return string[]
   */
  public function getPendingCountries()
  {
    return $this->pendingCountries;
  }
  /**
   * The status of the destination.
   *
   * Accepted values: UNKNOWN, ACTIVE, PENDING, DISAPPROVED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationStatus::class, 'Google_Service_ManufacturerCenter_DestinationStatus');
