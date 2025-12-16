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

class LivingAreaLayout extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BALCONY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BALCONY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BALCONY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BALCONY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LIVING_AREA_SQ_METERS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LIVING_AREA_SQ_METERS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LIVING_AREA_SQ_METERS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LIVING_AREA_SQ_METERS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LOFT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LOFT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LOFT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LOFT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const NON_SMOKING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const NON_SMOKING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const NON_SMOKING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const NON_SMOKING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PATIO_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PATIO_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PATIO_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PATIO_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const STAIRS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const STAIRS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const STAIRS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const STAIRS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Balcony. An outdoor platform attached to a building and surrounded by a
   * short wall, fence or other safety railing. The balcony is accessed through
   * a door in a guestroom or suite and is for use by the guest staying in that
   * room. May or may not include seating or outdoor furniture. Is not located
   * on the ground floor. Also lanai.
   *
   * @var bool
   */
  public $balcony;
  /**
   * Balcony exception.
   *
   * @var string
   */
  public $balconyException;
  /**
   * Living area sq meters. The measurement in meters of the area of a
   * guestroom's living space.
   *
   * @var float
   */
  public $livingAreaSqMeters;
  /**
   * Living area sq meters exception.
   *
   * @var string
   */
  public $livingAreaSqMetersException;
  /**
   * Loft. A three-walled upper area accessed by stairs or a ladder that
   * overlooks the lower area of a room.
   *
   * @var bool
   */
  public $loft;
  /**
   * Loft exception.
   *
   * @var string
   */
  public $loftException;
  /**
   * Non smoking. A guestroom in which the smoking of cigarettes, cigars and
   * pipes is prohibited.
   *
   * @var bool
   */
  public $nonSmoking;
  /**
   * Non smoking exception.
   *
   * @var string
   */
  public $nonSmokingException;
  /**
   * Patio. A paved, outdoor area with seating attached to and accessed through
   * a ground-floor guestroom for use by the occupants of the guestroom.
   *
   * @var bool
   */
  public $patio;
  /**
   * Patio exception.
   *
   * @var string
   */
  public $patioException;
  /**
   * Stairs. There are steps leading from one level or story to another in the
   * unit.
   *
   * @var bool
   */
  public $stairs;
  /**
   * Stairs exception.
   *
   * @var string
   */
  public $stairsException;

  /**
   * Balcony. An outdoor platform attached to a building and surrounded by a
   * short wall, fence or other safety railing. The balcony is accessed through
   * a door in a guestroom or suite and is for use by the guest staying in that
   * room. May or may not include seating or outdoor furniture. Is not located
   * on the ground floor. Also lanai.
   *
   * @param bool $balcony
   */
  public function setBalcony($balcony)
  {
    $this->balcony = $balcony;
  }
  /**
   * @return bool
   */
  public function getBalcony()
  {
    return $this->balcony;
  }
  /**
   * Balcony exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BALCONY_EXCEPTION_* $balconyException
   */
  public function setBalconyException($balconyException)
  {
    $this->balconyException = $balconyException;
  }
  /**
   * @return self::BALCONY_EXCEPTION_*
   */
  public function getBalconyException()
  {
    return $this->balconyException;
  }
  /**
   * Living area sq meters. The measurement in meters of the area of a
   * guestroom's living space.
   *
   * @param float $livingAreaSqMeters
   */
  public function setLivingAreaSqMeters($livingAreaSqMeters)
  {
    $this->livingAreaSqMeters = $livingAreaSqMeters;
  }
  /**
   * @return float
   */
  public function getLivingAreaSqMeters()
  {
    return $this->livingAreaSqMeters;
  }
  /**
   * Living area sq meters exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LIVING_AREA_SQ_METERS_EXCEPTION_* $livingAreaSqMetersException
   */
  public function setLivingAreaSqMetersException($livingAreaSqMetersException)
  {
    $this->livingAreaSqMetersException = $livingAreaSqMetersException;
  }
  /**
   * @return self::LIVING_AREA_SQ_METERS_EXCEPTION_*
   */
  public function getLivingAreaSqMetersException()
  {
    return $this->livingAreaSqMetersException;
  }
  /**
   * Loft. A three-walled upper area accessed by stairs or a ladder that
   * overlooks the lower area of a room.
   *
   * @param bool $loft
   */
  public function setLoft($loft)
  {
    $this->loft = $loft;
  }
  /**
   * @return bool
   */
  public function getLoft()
  {
    return $this->loft;
  }
  /**
   * Loft exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LOFT_EXCEPTION_* $loftException
   */
  public function setLoftException($loftException)
  {
    $this->loftException = $loftException;
  }
  /**
   * @return self::LOFT_EXCEPTION_*
   */
  public function getLoftException()
  {
    return $this->loftException;
  }
  /**
   * Non smoking. A guestroom in which the smoking of cigarettes, cigars and
   * pipes is prohibited.
   *
   * @param bool $nonSmoking
   */
  public function setNonSmoking($nonSmoking)
  {
    $this->nonSmoking = $nonSmoking;
  }
  /**
   * @return bool
   */
  public function getNonSmoking()
  {
    return $this->nonSmoking;
  }
  /**
   * Non smoking exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::NON_SMOKING_EXCEPTION_* $nonSmokingException
   */
  public function setNonSmokingException($nonSmokingException)
  {
    $this->nonSmokingException = $nonSmokingException;
  }
  /**
   * @return self::NON_SMOKING_EXCEPTION_*
   */
  public function getNonSmokingException()
  {
    return $this->nonSmokingException;
  }
  /**
   * Patio. A paved, outdoor area with seating attached to and accessed through
   * a ground-floor guestroom for use by the occupants of the guestroom.
   *
   * @param bool $patio
   */
  public function setPatio($patio)
  {
    $this->patio = $patio;
  }
  /**
   * @return bool
   */
  public function getPatio()
  {
    return $this->patio;
  }
  /**
   * Patio exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PATIO_EXCEPTION_* $patioException
   */
  public function setPatioException($patioException)
  {
    $this->patioException = $patioException;
  }
  /**
   * @return self::PATIO_EXCEPTION_*
   */
  public function getPatioException()
  {
    return $this->patioException;
  }
  /**
   * Stairs. There are steps leading from one level or story to another in the
   * unit.
   *
   * @param bool $stairs
   */
  public function setStairs($stairs)
  {
    $this->stairs = $stairs;
  }
  /**
   * @return bool
   */
  public function getStairs()
  {
    return $this->stairs;
  }
  /**
   * Stairs exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::STAIRS_EXCEPTION_* $stairsException
   */
  public function setStairsException($stairsException)
  {
    $this->stairsException = $stairsException;
  }
  /**
   * @return self::STAIRS_EXCEPTION_*
   */
  public function getStairsException()
  {
    return $this->stairsException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LivingAreaLayout::class, 'Google_Service_MyBusinessLodging_LivingAreaLayout');
