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

class ViewsFromUnit extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BEACH_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BEACH_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BEACH_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BEACH_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CITY_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CITY_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CITY_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CITY_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const GARDEN_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const GARDEN_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const GARDEN_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const GARDEN_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LAKE_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LAKE_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LAKE_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LAKE_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LANDMARK_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LANDMARK_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LANDMARK_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LANDMARK_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const OCEAN_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const OCEAN_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const OCEAN_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const OCEAN_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const POOL_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const POOL_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const POOL_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const POOL_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const VALLEY_VIEW_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const VALLEY_VIEW_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const VALLEY_VIEW_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const VALLEY_VIEW_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Beach view. A guestroom that features a window through which guests can see
   * the beach.
   *
   * @var bool
   */
  public $beachView;
  /**
   * Beach view exception.
   *
   * @var string
   */
  public $beachViewException;
  /**
   * City view. A guestroom that features a window through which guests can see
   * the buildings, parks and/or streets of the city.
   *
   * @var bool
   */
  public $cityView;
  /**
   * City view exception.
   *
   * @var string
   */
  public $cityViewException;
  /**
   * Garden view. A guestroom that features a window through which guests can
   * see a garden.
   *
   * @var bool
   */
  public $gardenView;
  /**
   * Garden view exception.
   *
   * @var string
   */
  public $gardenViewException;
  /**
   * Lake view.
   *
   * @var bool
   */
  public $lakeView;
  /**
   * Lake view exception.
   *
   * @var string
   */
  public $lakeViewException;
  /**
   * Landmark view. A guestroom that features a window through which guests can
   * see a landmark such as the countryside, a golf course, the forest, a park,
   * a rain forst, a mountain or a slope.
   *
   * @var bool
   */
  public $landmarkView;
  /**
   * Landmark view exception.
   *
   * @var string
   */
  public $landmarkViewException;
  /**
   * Ocean view. A guestroom that features a window through which guests can see
   * the ocean.
   *
   * @var bool
   */
  public $oceanView;
  /**
   * Ocean view exception.
   *
   * @var string
   */
  public $oceanViewException;
  /**
   * Pool view. A guestroom that features a window through which guests can see
   * the hotel's swimming pool.
   *
   * @var bool
   */
  public $poolView;
  /**
   * Pool view exception.
   *
   * @var string
   */
  public $poolViewException;
  /**
   * Valley view. A guestroom that features a window through which guests can
   * see over a valley.
   *
   * @var bool
   */
  public $valleyView;
  /**
   * Valley view exception.
   *
   * @var string
   */
  public $valleyViewException;

  /**
   * Beach view. A guestroom that features a window through which guests can see
   * the beach.
   *
   * @param bool $beachView
   */
  public function setBeachView($beachView)
  {
    $this->beachView = $beachView;
  }
  /**
   * @return bool
   */
  public function getBeachView()
  {
    return $this->beachView;
  }
  /**
   * Beach view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BEACH_VIEW_EXCEPTION_* $beachViewException
   */
  public function setBeachViewException($beachViewException)
  {
    $this->beachViewException = $beachViewException;
  }
  /**
   * @return self::BEACH_VIEW_EXCEPTION_*
   */
  public function getBeachViewException()
  {
    return $this->beachViewException;
  }
  /**
   * City view. A guestroom that features a window through which guests can see
   * the buildings, parks and/or streets of the city.
   *
   * @param bool $cityView
   */
  public function setCityView($cityView)
  {
    $this->cityView = $cityView;
  }
  /**
   * @return bool
   */
  public function getCityView()
  {
    return $this->cityView;
  }
  /**
   * City view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CITY_VIEW_EXCEPTION_* $cityViewException
   */
  public function setCityViewException($cityViewException)
  {
    $this->cityViewException = $cityViewException;
  }
  /**
   * @return self::CITY_VIEW_EXCEPTION_*
   */
  public function getCityViewException()
  {
    return $this->cityViewException;
  }
  /**
   * Garden view. A guestroom that features a window through which guests can
   * see a garden.
   *
   * @param bool $gardenView
   */
  public function setGardenView($gardenView)
  {
    $this->gardenView = $gardenView;
  }
  /**
   * @return bool
   */
  public function getGardenView()
  {
    return $this->gardenView;
  }
  /**
   * Garden view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::GARDEN_VIEW_EXCEPTION_* $gardenViewException
   */
  public function setGardenViewException($gardenViewException)
  {
    $this->gardenViewException = $gardenViewException;
  }
  /**
   * @return self::GARDEN_VIEW_EXCEPTION_*
   */
  public function getGardenViewException()
  {
    return $this->gardenViewException;
  }
  /**
   * Lake view.
   *
   * @param bool $lakeView
   */
  public function setLakeView($lakeView)
  {
    $this->lakeView = $lakeView;
  }
  /**
   * @return bool
   */
  public function getLakeView()
  {
    return $this->lakeView;
  }
  /**
   * Lake view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LAKE_VIEW_EXCEPTION_* $lakeViewException
   */
  public function setLakeViewException($lakeViewException)
  {
    $this->lakeViewException = $lakeViewException;
  }
  /**
   * @return self::LAKE_VIEW_EXCEPTION_*
   */
  public function getLakeViewException()
  {
    return $this->lakeViewException;
  }
  /**
   * Landmark view. A guestroom that features a window through which guests can
   * see a landmark such as the countryside, a golf course, the forest, a park,
   * a rain forst, a mountain or a slope.
   *
   * @param bool $landmarkView
   */
  public function setLandmarkView($landmarkView)
  {
    $this->landmarkView = $landmarkView;
  }
  /**
   * @return bool
   */
  public function getLandmarkView()
  {
    return $this->landmarkView;
  }
  /**
   * Landmark view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LANDMARK_VIEW_EXCEPTION_* $landmarkViewException
   */
  public function setLandmarkViewException($landmarkViewException)
  {
    $this->landmarkViewException = $landmarkViewException;
  }
  /**
   * @return self::LANDMARK_VIEW_EXCEPTION_*
   */
  public function getLandmarkViewException()
  {
    return $this->landmarkViewException;
  }
  /**
   * Ocean view. A guestroom that features a window through which guests can see
   * the ocean.
   *
   * @param bool $oceanView
   */
  public function setOceanView($oceanView)
  {
    $this->oceanView = $oceanView;
  }
  /**
   * @return bool
   */
  public function getOceanView()
  {
    return $this->oceanView;
  }
  /**
   * Ocean view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::OCEAN_VIEW_EXCEPTION_* $oceanViewException
   */
  public function setOceanViewException($oceanViewException)
  {
    $this->oceanViewException = $oceanViewException;
  }
  /**
   * @return self::OCEAN_VIEW_EXCEPTION_*
   */
  public function getOceanViewException()
  {
    return $this->oceanViewException;
  }
  /**
   * Pool view. A guestroom that features a window through which guests can see
   * the hotel's swimming pool.
   *
   * @param bool $poolView
   */
  public function setPoolView($poolView)
  {
    $this->poolView = $poolView;
  }
  /**
   * @return bool
   */
  public function getPoolView()
  {
    return $this->poolView;
  }
  /**
   * Pool view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::POOL_VIEW_EXCEPTION_* $poolViewException
   */
  public function setPoolViewException($poolViewException)
  {
    $this->poolViewException = $poolViewException;
  }
  /**
   * @return self::POOL_VIEW_EXCEPTION_*
   */
  public function getPoolViewException()
  {
    return $this->poolViewException;
  }
  /**
   * Valley view. A guestroom that features a window through which guests can
   * see over a valley.
   *
   * @param bool $valleyView
   */
  public function setValleyView($valleyView)
  {
    $this->valleyView = $valleyView;
  }
  /**
   * @return bool
   */
  public function getValleyView()
  {
    return $this->valleyView;
  }
  /**
   * Valley view exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::VALLEY_VIEW_EXCEPTION_* $valleyViewException
   */
  public function setValleyViewException($valleyViewException)
  {
    $this->valleyViewException = $valleyViewException;
  }
  /**
   * @return self::VALLEY_VIEW_EXCEPTION_*
   */
  public function getValleyViewException()
  {
    return $this->valleyViewException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ViewsFromUnit::class, 'Google_Service_MyBusinessLodging_ViewsFromUnit');
