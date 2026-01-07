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

namespace Google\Service\Solar;

class BuildingInsights extends \Google\Model
{
  /**
   * No quality is known.
   */
  public const IMAGERY_QUALITY_IMAGERY_QUALITY_UNSPECIFIED = 'IMAGERY_QUALITY_UNSPECIFIED';
  /**
   * Solar data is derived from aerial imagery captured at low-altitude and
   * processed at 0.1 m/pixel.
   */
  public const IMAGERY_QUALITY_HIGH = 'HIGH';
  /**
   * Solar data is derived from enhanced aerial imagery captured at high-
   * altitude and processed at 0.25 m/pixel.
   */
  public const IMAGERY_QUALITY_MEDIUM = 'MEDIUM';
  /**
   * Solar data is derived from enhanced satellite imagery processed at 0.25
   * m/pixel.
   */
  public const IMAGERY_QUALITY_LOW = 'LOW';
  /**
   * Solar data is derived from enhanced satellite imagery processed at 0.25
   * m/pixel.
   */
  public const IMAGERY_QUALITY_BASE = 'BASE';
  /**
   * Administrative area 1 (e.g., in the US, the state) that contains this
   * building. For example, in the US, the abbreviation might be "MA" or "CA."
   *
   * @var string
   */
  public $administrativeArea;
  protected $boundingBoxType = LatLngBox::class;
  protected $boundingBoxDataType = '';
  protected $centerType = LatLng::class;
  protected $centerDataType = '';
  protected $imageryDateType = Date::class;
  protected $imageryDateDataType = '';
  protected $imageryProcessedDateType = Date::class;
  protected $imageryProcessedDateDataType = '';
  /**
   * The quality of the imagery used to compute the data for this building.
   *
   * @var string
   */
  public $imageryQuality;
  /**
   * The resource name for the building, of the format `buildings/{place_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Postal code (e.g., US zip code) this building is contained by.
   *
   * @var string
   */
  public $postalCode;
  /**
   * Region code for the country (or region) this building is in.
   *
   * @var string
   */
  public $regionCode;
  protected $solarPotentialType = SolarPotential::class;
  protected $solarPotentialDataType = '';
  /**
   * Statistical area (e.g., US census tract) this building is in.
   *
   * @var string
   */
  public $statisticalArea;

  /**
   * Administrative area 1 (e.g., in the US, the state) that contains this
   * building. For example, in the US, the abbreviation might be "MA" or "CA."
   *
   * @param string $administrativeArea
   */
  public function setAdministrativeArea($administrativeArea)
  {
    $this->administrativeArea = $administrativeArea;
  }
  /**
   * @return string
   */
  public function getAdministrativeArea()
  {
    return $this->administrativeArea;
  }
  /**
   * The bounding box of the building.
   *
   * @param LatLngBox $boundingBox
   */
  public function setBoundingBox(LatLngBox $boundingBox)
  {
    $this->boundingBox = $boundingBox;
  }
  /**
   * @return LatLngBox
   */
  public function getBoundingBox()
  {
    return $this->boundingBox;
  }
  /**
   * A point near the center of the building.
   *
   * @param LatLng $center
   */
  public function setCenter(LatLng $center)
  {
    $this->center = $center;
  }
  /**
   * @return LatLng
   */
  public function getCenter()
  {
    return $this->center;
  }
  /**
   * Date that the underlying imagery was acquired. This is approximate.
   *
   * @param Date $imageryDate
   */
  public function setImageryDate(Date $imageryDate)
  {
    $this->imageryDate = $imageryDate;
  }
  /**
   * @return Date
   */
  public function getImageryDate()
  {
    return $this->imageryDate;
  }
  /**
   * When processing was completed on this imagery.
   *
   * @param Date $imageryProcessedDate
   */
  public function setImageryProcessedDate(Date $imageryProcessedDate)
  {
    $this->imageryProcessedDate = $imageryProcessedDate;
  }
  /**
   * @return Date
   */
  public function getImageryProcessedDate()
  {
    return $this->imageryProcessedDate;
  }
  /**
   * The quality of the imagery used to compute the data for this building.
   *
   * Accepted values: IMAGERY_QUALITY_UNSPECIFIED, HIGH, MEDIUM, LOW, BASE
   *
   * @param self::IMAGERY_QUALITY_* $imageryQuality
   */
  public function setImageryQuality($imageryQuality)
  {
    $this->imageryQuality = $imageryQuality;
  }
  /**
   * @return self::IMAGERY_QUALITY_*
   */
  public function getImageryQuality()
  {
    return $this->imageryQuality;
  }
  /**
   * The resource name for the building, of the format `buildings/{place_id}`.
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
   * Postal code (e.g., US zip code) this building is contained by.
   *
   * @param string $postalCode
   */
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  /**
   * @return string
   */
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  /**
   * Region code for the country (or region) this building is in.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * Solar potential of the building.
   *
   * @param SolarPotential $solarPotential
   */
  public function setSolarPotential(SolarPotential $solarPotential)
  {
    $this->solarPotential = $solarPotential;
  }
  /**
   * @return SolarPotential
   */
  public function getSolarPotential()
  {
    return $this->solarPotential;
  }
  /**
   * Statistical area (e.g., US census tract) this building is in.
   *
   * @param string $statisticalArea
   */
  public function setStatisticalArea($statisticalArea)
  {
    $this->statisticalArea = $statisticalArea;
  }
  /**
   * @return string
   */
  public function getStatisticalArea()
  {
    return $this->statisticalArea;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildingInsights::class, 'Google_Service_Solar_BuildingInsights');
