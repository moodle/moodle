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

namespace Google\Service\Css;

class DestinationStatus extends \Google\Collection
{
  protected $collection_key = 'pendingCountries';
  /**
   * List of country codes (ISO 3166-1 alpha-2) where the CSS Product is
   * approved.
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
   * List of country codes (ISO 3166-1 alpha-2) where the CSS Product is
   * disapproved.
   *
   * @var string[]
   */
  public $disapprovedCountries;
  /**
   * List of country codes (ISO 3166-1 alpha-2) where the CSS Product is pending
   * approval.
   *
   * @var string[]
   */
  public $pendingCountries;

  /**
   * List of country codes (ISO 3166-1 alpha-2) where the CSS Product is
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
   * List of country codes (ISO 3166-1 alpha-2) where the CSS Product is
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
   * List of country codes (ISO 3166-1 alpha-2) where the CSS Product is pending
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationStatus::class, 'Google_Service_Css_DestinationStatus');
