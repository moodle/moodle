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

namespace Google\Service\MyBusinessLodging;

class PhysicalDistancing extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COMMON_AREAS_PHYSICAL_DISTANCING_ARRANGED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COMMON_AREAS_PHYSICAL_DISTANCING_ARRANGED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COMMON_AREAS_PHYSICAL_DISTANCING_ARRANGED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COMMON_AREAS_PHYSICAL_DISTANCING_ARRANGED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PHYSICAL_DISTANCING_REQUIRED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PHYSICAL_DISTANCING_REQUIRED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PHYSICAL_DISTANCING_REQUIRED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PHYSICAL_DISTANCING_REQUIRED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SAFETY_DIVIDERS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SAFETY_DIVIDERS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SAFETY_DIVIDERS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SAFETY_DIVIDERS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SHARED_AREAS_LIMITED_OCCUPANCY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SHARED_AREAS_LIMITED_OCCUPANCY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SHARED_AREAS_LIMITED_OCCUPANCY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SHARED_AREAS_LIMITED_OCCUPANCY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WELLNESS_AREAS_HAVE_PRIVATE_SPACES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WELLNESS_AREAS_HAVE_PRIVATE_SPACES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WELLNESS_AREAS_HAVE_PRIVATE_SPACES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WELLNESS_AREAS_HAVE_PRIVATE_SPACES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Common areas arranged to maintain physical distancing.
   *
   * @var bool
   */
  public $commonAreasPhysicalDistancingArranged;
  /**
   * Common areas physical distancing arranged exception.
   *
   * @var string
   */
  public $commonAreasPhysicalDistancingArrangedException;
  /**
   * Physical distancing required.
   *
   * @var bool
   */
  public $physicalDistancingRequired;
  /**
   * Physical distancing required exception.
   *
   * @var string
   */
  public $physicalDistancingRequiredException;
  /**
   * Safety dividers at front desk and other locations.
   *
   * @var bool
   */
  public $safetyDividers;
  /**
   * Safety dividers exception.
   *
   * @var string
   */
  public $safetyDividersException;
  /**
   * Guest occupancy limited within shared facilities.
   *
   * @var bool
   */
  public $sharedAreasLimitedOccupancy;
  /**
   * Shared areas limited occupancy exception.
   *
   * @var string
   */
  public $sharedAreasLimitedOccupancyException;
  /**
   * Private spaces designated in spa and wellness areas.
   *
   * @var bool
   */
  public $wellnessAreasHavePrivateSpaces;
  /**
   * Wellness areas have private spaces exception.
   *
   * @var string
   */
  public $wellnessAreasHavePrivateSpacesException;

  /**
   * Common areas arranged to maintain physical distancing.
   *
   * @param bool $commonAreasPhysicalDistancingArranged
   */
  public function setCommonAreasPhysicalDistancingArranged($commonAreasPhysicalDistancingArranged)
  {
    $this->commonAreasPhysicalDistancingArranged = $commonAreasPhysicalDistancingArranged;
  }
  /**
   * @return bool
   */
  public function getCommonAreasPhysicalDistancingArranged()
  {
    return $this->commonAreasPhysicalDistancingArranged;
  }
  /**
   * Common areas physical distancing arranged exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COMMON_AREAS_PHYSICAL_DISTANCING_ARRANGED_EXCEPTION_* $commonAreasPhysicalDistancingArrangedException
   */
  public function setCommonAreasPhysicalDistancingArrangedException($commonAreasPhysicalDistancingArrangedException)
  {
    $this->commonAreasPhysicalDistancingArrangedException = $commonAreasPhysicalDistancingArrangedException;
  }
  /**
   * @return self::COMMON_AREAS_PHYSICAL_DISTANCING_ARRANGED_EXCEPTION_*
   */
  public function getCommonAreasPhysicalDistancingArrangedException()
  {
    return $this->commonAreasPhysicalDistancingArrangedException;
  }
  /**
   * Physical distancing required.
   *
   * @param bool $physicalDistancingRequired
   */
  public function setPhysicalDistancingRequired($physicalDistancingRequired)
  {
    $this->physicalDistancingRequired = $physicalDistancingRequired;
  }
  /**
   * @return bool
   */
  public function getPhysicalDistancingRequired()
  {
    return $this->physicalDistancingRequired;
  }
  /**
   * Physical distancing required exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PHYSICAL_DISTANCING_REQUIRED_EXCEPTION_* $physicalDistancingRequiredException
   */
  public function setPhysicalDistancingRequiredException($physicalDistancingRequiredException)
  {
    $this->physicalDistancingRequiredException = $physicalDistancingRequiredException;
  }
  /**
   * @return self::PHYSICAL_DISTANCING_REQUIRED_EXCEPTION_*
   */
  public function getPhysicalDistancingRequiredException()
  {
    return $this->physicalDistancingRequiredException;
  }
  /**
   * Safety dividers at front desk and other locations.
   *
   * @param bool $safetyDividers
   */
  public function setSafetyDividers($safetyDividers)
  {
    $this->safetyDividers = $safetyDividers;
  }
  /**
   * @return bool
   */
  public function getSafetyDividers()
  {
    return $this->safetyDividers;
  }
  /**
   * Safety dividers exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SAFETY_DIVIDERS_EXCEPTION_* $safetyDividersException
   */
  public function setSafetyDividersException($safetyDividersException)
  {
    $this->safetyDividersException = $safetyDividersException;
  }
  /**
   * @return self::SAFETY_DIVIDERS_EXCEPTION_*
   */
  public function getSafetyDividersException()
  {
    return $this->safetyDividersException;
  }
  /**
   * Guest occupancy limited within shared facilities.
   *
   * @param bool $sharedAreasLimitedOccupancy
   */
  public function setSharedAreasLimitedOccupancy($sharedAreasLimitedOccupancy)
  {
    $this->sharedAreasLimitedOccupancy = $sharedAreasLimitedOccupancy;
  }
  /**
   * @return bool
   */
  public function getSharedAreasLimitedOccupancy()
  {
    return $this->sharedAreasLimitedOccupancy;
  }
  /**
   * Shared areas limited occupancy exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SHARED_AREAS_LIMITED_OCCUPANCY_EXCEPTION_* $sharedAreasLimitedOccupancyException
   */
  public function setSharedAreasLimitedOccupancyException($sharedAreasLimitedOccupancyException)
  {
    $this->sharedAreasLimitedOccupancyException = $sharedAreasLimitedOccupancyException;
  }
  /**
   * @return self::SHARED_AREAS_LIMITED_OCCUPANCY_EXCEPTION_*
   */
  public function getSharedAreasLimitedOccupancyException()
  {
    return $this->sharedAreasLimitedOccupancyException;
  }
  /**
   * Private spaces designated in spa and wellness areas.
   *
   * @param bool $wellnessAreasHavePrivateSpaces
   */
  public function setWellnessAreasHavePrivateSpaces($wellnessAreasHavePrivateSpaces)
  {
    $this->wellnessAreasHavePrivateSpaces = $wellnessAreasHavePrivateSpaces;
  }
  /**
   * @return bool
   */
  public function getWellnessAreasHavePrivateSpaces()
  {
    return $this->wellnessAreasHavePrivateSpaces;
  }
  /**
   * Wellness areas have private spaces exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WELLNESS_AREAS_HAVE_PRIVATE_SPACES_EXCEPTION_* $wellnessAreasHavePrivateSpacesException
   */
  public function setWellnessAreasHavePrivateSpacesException($wellnessAreasHavePrivateSpacesException)
  {
    $this->wellnessAreasHavePrivateSpacesException = $wellnessAreasHavePrivateSpacesException;
  }
  /**
   * @return self::WELLNESS_AREAS_HAVE_PRIVATE_SPACES_EXCEPTION_*
   */
  public function getWellnessAreasHavePrivateSpacesException()
  {
    return $this->wellnessAreasHavePrivateSpacesException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PhysicalDistancing::class, 'Google_Service_MyBusinessLodging_PhysicalDistancing');
