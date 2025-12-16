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

namespace Google\Service\CivicInfo;

class CivicinfoSchemaV2PollingLocation extends \Google\Collection
{
  protected $collection_key = 'sources';
  protected $addressType = CivicinfoSchemaV2SimpleAddressType::class;
  protected $addressDataType = '';
  /**
   * The last date that this early vote site or drop off location may be used.
   * This field is not populated for polling locations.
   *
   * @var string
   */
  public $endDate;
  /**
   * Latitude of the location, in degrees north of the equator. Note this field
   * may not be available for some locations.
   *
   * @var 
   */
  public $latitude;
  /**
   * Longitude of the location, in degrees east of the Prime Meridian. Note this
   * field may not be available for some locations.
   *
   * @var 
   */
  public $longitude;
  /**
   * The name of the early vote site or drop off location. This field is not
   * populated for polling locations.
   *
   * @var string
   */
  public $name;
  /**
   * Notes about this location (e.g. accessibility ramp or entrance to use).
   *
   * @var string
   */
  public $notes;
  /**
   * A description of when this location is open.
   *
   * @var string
   */
  public $pollingHours;
  protected $sourcesType = CivicinfoSchemaV2Source::class;
  protected $sourcesDataType = 'array';
  /**
   * The first date that this early vote site or drop off location may be used.
   * This field is not populated for polling locations.
   *
   * @var string
   */
  public $startDate;
  /**
   * The services provided by this early vote site or drop off location. This
   * field is not populated for polling locations.
   *
   * @var string
   */
  public $voterServices;

  /**
   * The address of the location.
   *
   * @param CivicinfoSchemaV2SimpleAddressType $address
   */
  public function setAddress(CivicinfoSchemaV2SimpleAddressType $address)
  {
    $this->address = $address;
  }
  /**
   * @return CivicinfoSchemaV2SimpleAddressType
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * The last date that this early vote site or drop off location may be used.
   * This field is not populated for polling locations.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }
  public function getLatitude()
  {
    return $this->latitude;
  }
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }
  public function getLongitude()
  {
    return $this->longitude;
  }
  /**
   * The name of the early vote site or drop off location. This field is not
   * populated for polling locations.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Notes about this location (e.g. accessibility ramp or entrance to use).
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * A description of when this location is open.
   *
   * @param string $pollingHours
   */
  public function setPollingHours($pollingHours)
  {
    $this->pollingHours = $pollingHours;
  }
  /**
   * @return string
   */
  public function getPollingHours()
  {
    return $this->pollingHours;
  }
  /**
   * A list of sources for this location. If multiple sources are listed the
   * data has been aggregated from those sources.
   *
   * @param CivicinfoSchemaV2Source[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return CivicinfoSchemaV2Source[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * The first date that this early vote site or drop off location may be used.
   * This field is not populated for polling locations.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * The services provided by this early vote site or drop off location. This
   * field is not populated for polling locations.
   *
   * @param string $voterServices
   */
  public function setVoterServices($voterServices)
  {
    $this->voterServices = $voterServices;
  }
  /**
   * @return string
   */
  public function getVoterServices()
  {
    return $this->voterServices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2PollingLocation::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2PollingLocation');
