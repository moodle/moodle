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

class CollectionStatusDestinationStatus extends \Google\Collection
{
  protected $collection_key = 'pendingCountries';
  /**
   * Country codes (ISO 3166-1 alpha-2) where the collection is approved.
   *
   * @var string[]
   */
  public $approvedCountries;
  /**
   * The name of the destination
   *
   * @var string
   */
  public $destination;
  /**
   * Country codes (ISO 3166-1 alpha-2) where the collection is disapproved.
   *
   * @var string[]
   */
  public $disapprovedCountries;
  /**
   * Country codes (ISO 3166-1 alpha-2) where the collection is pending
   * approval.
   *
   * @var string[]
   */
  public $pendingCountries;
  /**
   * The status for the specified destination in the collections target country.
   *
   * @var string
   */
  public $status;

  /**
   * Country codes (ISO 3166-1 alpha-2) where the collection is approved.
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
   * The name of the destination
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
   * Country codes (ISO 3166-1 alpha-2) where the collection is disapproved.
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
   * Country codes (ISO 3166-1 alpha-2) where the collection is pending
   * approval.
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
   * The status for the specified destination in the collections target country.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CollectionStatusDestinationStatus::class, 'Google_Service_ShoppingContent_CollectionStatusDestinationStatus');
