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

namespace Google\Service\Doubleclicksearch;

class Availability extends \Google\Model
{
  /**
   * DS advertiser ID.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * DS agency ID.
   *
   * @var string
   */
  public $agencyId;
  /**
   * The time by which all conversions have been uploaded, in epoch millis UTC.
   *
   * @var string
   */
  public $availabilityTimestamp;
  /**
   * Customer ID of a client account in the new Search Ads 360 experience.
   *
   * @var string
   */
  public $customerId;
  /**
   * The numeric segmentation identifier (for example, DoubleClick Search
   * Floodlight activity ID).
   *
   * @var string
   */
  public $segmentationId;
  /**
   * The friendly segmentation identifier (for example, DoubleClick Search
   * Floodlight activity name).
   *
   * @var string
   */
  public $segmentationName;
  /**
   * The segmentation type that this availability is for (its default value is
   * `FLOODLIGHT`).
   *
   * @var string
   */
  public $segmentationType;

  /**
   * DS advertiser ID.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * DS agency ID.
   *
   * @param string $agencyId
   */
  public function setAgencyId($agencyId)
  {
    $this->agencyId = $agencyId;
  }
  /**
   * @return string
   */
  public function getAgencyId()
  {
    return $this->agencyId;
  }
  /**
   * The time by which all conversions have been uploaded, in epoch millis UTC.
   *
   * @param string $availabilityTimestamp
   */
  public function setAvailabilityTimestamp($availabilityTimestamp)
  {
    $this->availabilityTimestamp = $availabilityTimestamp;
  }
  /**
   * @return string
   */
  public function getAvailabilityTimestamp()
  {
    return $this->availabilityTimestamp;
  }
  /**
   * Customer ID of a client account in the new Search Ads 360 experience.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * The numeric segmentation identifier (for example, DoubleClick Search
   * Floodlight activity ID).
   *
   * @param string $segmentationId
   */
  public function setSegmentationId($segmentationId)
  {
    $this->segmentationId = $segmentationId;
  }
  /**
   * @return string
   */
  public function getSegmentationId()
  {
    return $this->segmentationId;
  }
  /**
   * The friendly segmentation identifier (for example, DoubleClick Search
   * Floodlight activity name).
   *
   * @param string $segmentationName
   */
  public function setSegmentationName($segmentationName)
  {
    $this->segmentationName = $segmentationName;
  }
  /**
   * @return string
   */
  public function getSegmentationName()
  {
    return $this->segmentationName;
  }
  /**
   * The segmentation type that this availability is for (its default value is
   * `FLOODLIGHT`).
   *
   * @param string $segmentationType
   */
  public function setSegmentationType($segmentationType)
  {
    $this->segmentationType = $segmentationType;
  }
  /**
   * @return string
   */
  public function getSegmentationType()
  {
    return $this->segmentationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Availability::class, 'Google_Service_Doubleclicksearch_Availability');
