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

class Pools extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ADULT_POOL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ADULT_POOL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ADULT_POOL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ADULT_POOL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HOT_TUB_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HOT_TUB_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HOT_TUB_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HOT_TUB_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INDOOR_POOL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INDOOR_POOL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INDOOR_POOL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INDOOR_POOL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INDOOR_POOLS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INDOOR_POOLS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INDOOR_POOLS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INDOOR_POOLS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LAZY_RIVER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LAZY_RIVER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LAZY_RIVER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LAZY_RIVER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LIFEGUARD_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LIFEGUARD_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LIFEGUARD_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LIFEGUARD_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const OUTDOOR_POOL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const OUTDOOR_POOL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const OUTDOOR_POOL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const OUTDOOR_POOL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const OUTDOOR_POOLS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const OUTDOOR_POOLS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const OUTDOOR_POOLS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const OUTDOOR_POOLS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const POOL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const POOL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const POOL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const POOL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const POOLS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const POOLS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const POOLS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const POOLS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WADING_POOL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WADING_POOL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WADING_POOL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WADING_POOL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATER_PARK_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATER_PARK_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATER_PARK_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATER_PARK_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATERSLIDE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATERSLIDE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATERSLIDE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATERSLIDE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WAVE_POOL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WAVE_POOL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WAVE_POOL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WAVE_POOL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Adult pool. A pool restricted for use by adults only. Can be indoors or
   * outdoors.
   *
   * @var bool
   */
  public $adultPool;
  /**
   * Adult pool exception.
   *
   * @var string
   */
  public $adultPoolException;
  /**
   * Hot tub. A man-made pool containing bubbling water maintained at a higher
   * temperature and circulated by aerating jets for the purpose of soaking,
   * relaxation and hydrotherapy. Can be indoors or outdoors. Not used for
   * active swimming. Also known as Jacuzzi. Hot tub must be in a common area
   * where all guests can access it. Does not apply to room-specific hot tubs
   * that are only accessible to guest occupying that room.
   *
   * @var bool
   */
  public $hotTub;
  /**
   * Hot tub exception.
   *
   * @var string
   */
  public $hotTubException;
  /**
   * Indoor pool. A pool located inside the hotel and available for guests to
   * use for swimming and/or soaking. Use may or may not be restricted to adults
   * and/or children.
   *
   * @var bool
   */
  public $indoorPool;
  /**
   * Indoor pool exception.
   *
   * @var string
   */
  public $indoorPoolException;
  /**
   * Indoor pools count. The sum of all indoor pools at the hotel.
   *
   * @var int
   */
  public $indoorPoolsCount;
  /**
   * Indoor pools count exception.
   *
   * @var string
   */
  public $indoorPoolsCountException;
  /**
   * Lazy river. A man-made pool or several interconnected recreational pools
   * built to mimic the shape and current of a winding river where guests float
   * in the water on inflated rubber tubes. Can be indoors or outdoors.
   *
   * @var bool
   */
  public $lazyRiver;
  /**
   * Lazy river exception.
   *
   * @var string
   */
  public $lazyRiverException;
  /**
   * Lifeguard. A trained member of the hotel staff stationed by the hotel's
   * indoor or outdoor swimming area and responsible for the safety of swimming
   * guests.
   *
   * @var bool
   */
  public $lifeguard;
  /**
   * Lifeguard exception.
   *
   * @var string
   */
  public $lifeguardException;
  /**
   * Outdoor pool. A pool located outside on the grounds of the hotel and
   * available for guests to use for swimming, soaking or recreation. Use may or
   * may not be restricted to adults and/or children.
   *
   * @var bool
   */
  public $outdoorPool;
  /**
   * Outdoor pool exception.
   *
   * @var string
   */
  public $outdoorPoolException;
  /**
   * Outdoor pools count. The sum of all outdoor pools at the hotel.
   *
   * @var int
   */
  public $outdoorPoolsCount;
  /**
   * Outdoor pools count exception.
   *
   * @var string
   */
  public $outdoorPoolsCountException;
  /**
   * Pool. The presence of a pool, either indoors or outdoors, for guests to use
   * for swimming and/or soaking. Use may or may not be restricted to adults
   * and/or children.
   *
   * @var bool
   */
  public $pool;
  /**
   * Pool exception.
   *
   * @var string
   */
  public $poolException;
  /**
   * Pools count. The sum of all pools at the hotel.
   *
   * @var int
   */
  public $poolsCount;
  /**
   * Pools count exception.
   *
   * @var string
   */
  public $poolsCountException;
  /**
   * Wading pool. A shallow pool designed for small children to play in. Can be
   * indoors or outdoors. Also known as kiddie pool.
   *
   * @var bool
   */
  public $wadingPool;
  /**
   * Wading pool exception.
   *
   * @var string
   */
  public $wadingPoolException;
  /**
   * Water park. An aquatic recreation area with a large pool or series of pools
   * that has features such as a water slide or tube, wavepool, fountains, rope
   * swings, and/or obstacle course. Can be indoors or outdoors. Also known as
   * adventure pool.
   *
   * @var bool
   */
  public $waterPark;
  /**
   * Water park exception.
   *
   * @var string
   */
  public $waterParkException;
  /**
   * Waterslide. A continuously wetted chute positioned by an indoor or outdoor
   * pool which people slide down into the water.
   *
   * @var bool
   */
  public $waterslide;
  /**
   * Waterslide exception.
   *
   * @var string
   */
  public $waterslideException;
  /**
   * Wave pool. A large indoor or outdoor pool with a machine that produces
   * water currents to mimic the ocean's crests.
   *
   * @var bool
   */
  public $wavePool;
  /**
   * Wave pool exception.
   *
   * @var string
   */
  public $wavePoolException;

  /**
   * Adult pool. A pool restricted for use by adults only. Can be indoors or
   * outdoors.
   *
   * @param bool $adultPool
   */
  public function setAdultPool($adultPool)
  {
    $this->adultPool = $adultPool;
  }
  /**
   * @return bool
   */
  public function getAdultPool()
  {
    return $this->adultPool;
  }
  /**
   * Adult pool exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ADULT_POOL_EXCEPTION_* $adultPoolException
   */
  public function setAdultPoolException($adultPoolException)
  {
    $this->adultPoolException = $adultPoolException;
  }
  /**
   * @return self::ADULT_POOL_EXCEPTION_*
   */
  public function getAdultPoolException()
  {
    return $this->adultPoolException;
  }
  /**
   * Hot tub. A man-made pool containing bubbling water maintained at a higher
   * temperature and circulated by aerating jets for the purpose of soaking,
   * relaxation and hydrotherapy. Can be indoors or outdoors. Not used for
   * active swimming. Also known as Jacuzzi. Hot tub must be in a common area
   * where all guests can access it. Does not apply to room-specific hot tubs
   * that are only accessible to guest occupying that room.
   *
   * @param bool $hotTub
   */
  public function setHotTub($hotTub)
  {
    $this->hotTub = $hotTub;
  }
  /**
   * @return bool
   */
  public function getHotTub()
  {
    return $this->hotTub;
  }
  /**
   * Hot tub exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HOT_TUB_EXCEPTION_* $hotTubException
   */
  public function setHotTubException($hotTubException)
  {
    $this->hotTubException = $hotTubException;
  }
  /**
   * @return self::HOT_TUB_EXCEPTION_*
   */
  public function getHotTubException()
  {
    return $this->hotTubException;
  }
  /**
   * Indoor pool. A pool located inside the hotel and available for guests to
   * use for swimming and/or soaking. Use may or may not be restricted to adults
   * and/or children.
   *
   * @param bool $indoorPool
   */
  public function setIndoorPool($indoorPool)
  {
    $this->indoorPool = $indoorPool;
  }
  /**
   * @return bool
   */
  public function getIndoorPool()
  {
    return $this->indoorPool;
  }
  /**
   * Indoor pool exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INDOOR_POOL_EXCEPTION_* $indoorPoolException
   */
  public function setIndoorPoolException($indoorPoolException)
  {
    $this->indoorPoolException = $indoorPoolException;
  }
  /**
   * @return self::INDOOR_POOL_EXCEPTION_*
   */
  public function getIndoorPoolException()
  {
    return $this->indoorPoolException;
  }
  /**
   * Indoor pools count. The sum of all indoor pools at the hotel.
   *
   * @param int $indoorPoolsCount
   */
  public function setIndoorPoolsCount($indoorPoolsCount)
  {
    $this->indoorPoolsCount = $indoorPoolsCount;
  }
  /**
   * @return int
   */
  public function getIndoorPoolsCount()
  {
    return $this->indoorPoolsCount;
  }
  /**
   * Indoor pools count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INDOOR_POOLS_COUNT_EXCEPTION_* $indoorPoolsCountException
   */
  public function setIndoorPoolsCountException($indoorPoolsCountException)
  {
    $this->indoorPoolsCountException = $indoorPoolsCountException;
  }
  /**
   * @return self::INDOOR_POOLS_COUNT_EXCEPTION_*
   */
  public function getIndoorPoolsCountException()
  {
    return $this->indoorPoolsCountException;
  }
  /**
   * Lazy river. A man-made pool or several interconnected recreational pools
   * built to mimic the shape and current of a winding river where guests float
   * in the water on inflated rubber tubes. Can be indoors or outdoors.
   *
   * @param bool $lazyRiver
   */
  public function setLazyRiver($lazyRiver)
  {
    $this->lazyRiver = $lazyRiver;
  }
  /**
   * @return bool
   */
  public function getLazyRiver()
  {
    return $this->lazyRiver;
  }
  /**
   * Lazy river exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LAZY_RIVER_EXCEPTION_* $lazyRiverException
   */
  public function setLazyRiverException($lazyRiverException)
  {
    $this->lazyRiverException = $lazyRiverException;
  }
  /**
   * @return self::LAZY_RIVER_EXCEPTION_*
   */
  public function getLazyRiverException()
  {
    return $this->lazyRiverException;
  }
  /**
   * Lifeguard. A trained member of the hotel staff stationed by the hotel's
   * indoor or outdoor swimming area and responsible for the safety of swimming
   * guests.
   *
   * @param bool $lifeguard
   */
  public function setLifeguard($lifeguard)
  {
    $this->lifeguard = $lifeguard;
  }
  /**
   * @return bool
   */
  public function getLifeguard()
  {
    return $this->lifeguard;
  }
  /**
   * Lifeguard exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LIFEGUARD_EXCEPTION_* $lifeguardException
   */
  public function setLifeguardException($lifeguardException)
  {
    $this->lifeguardException = $lifeguardException;
  }
  /**
   * @return self::LIFEGUARD_EXCEPTION_*
   */
  public function getLifeguardException()
  {
    return $this->lifeguardException;
  }
  /**
   * Outdoor pool. A pool located outside on the grounds of the hotel and
   * available for guests to use for swimming, soaking or recreation. Use may or
   * may not be restricted to adults and/or children.
   *
   * @param bool $outdoorPool
   */
  public function setOutdoorPool($outdoorPool)
  {
    $this->outdoorPool = $outdoorPool;
  }
  /**
   * @return bool
   */
  public function getOutdoorPool()
  {
    return $this->outdoorPool;
  }
  /**
   * Outdoor pool exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::OUTDOOR_POOL_EXCEPTION_* $outdoorPoolException
   */
  public function setOutdoorPoolException($outdoorPoolException)
  {
    $this->outdoorPoolException = $outdoorPoolException;
  }
  /**
   * @return self::OUTDOOR_POOL_EXCEPTION_*
   */
  public function getOutdoorPoolException()
  {
    return $this->outdoorPoolException;
  }
  /**
   * Outdoor pools count. The sum of all outdoor pools at the hotel.
   *
   * @param int $outdoorPoolsCount
   */
  public function setOutdoorPoolsCount($outdoorPoolsCount)
  {
    $this->outdoorPoolsCount = $outdoorPoolsCount;
  }
  /**
   * @return int
   */
  public function getOutdoorPoolsCount()
  {
    return $this->outdoorPoolsCount;
  }
  /**
   * Outdoor pools count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::OUTDOOR_POOLS_COUNT_EXCEPTION_* $outdoorPoolsCountException
   */
  public function setOutdoorPoolsCountException($outdoorPoolsCountException)
  {
    $this->outdoorPoolsCountException = $outdoorPoolsCountException;
  }
  /**
   * @return self::OUTDOOR_POOLS_COUNT_EXCEPTION_*
   */
  public function getOutdoorPoolsCountException()
  {
    return $this->outdoorPoolsCountException;
  }
  /**
   * Pool. The presence of a pool, either indoors or outdoors, for guests to use
   * for swimming and/or soaking. Use may or may not be restricted to adults
   * and/or children.
   *
   * @param bool $pool
   */
  public function setPool($pool)
  {
    $this->pool = $pool;
  }
  /**
   * @return bool
   */
  public function getPool()
  {
    return $this->pool;
  }
  /**
   * Pool exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::POOL_EXCEPTION_* $poolException
   */
  public function setPoolException($poolException)
  {
    $this->poolException = $poolException;
  }
  /**
   * @return self::POOL_EXCEPTION_*
   */
  public function getPoolException()
  {
    return $this->poolException;
  }
  /**
   * Pools count. The sum of all pools at the hotel.
   *
   * @param int $poolsCount
   */
  public function setPoolsCount($poolsCount)
  {
    $this->poolsCount = $poolsCount;
  }
  /**
   * @return int
   */
  public function getPoolsCount()
  {
    return $this->poolsCount;
  }
  /**
   * Pools count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::POOLS_COUNT_EXCEPTION_* $poolsCountException
   */
  public function setPoolsCountException($poolsCountException)
  {
    $this->poolsCountException = $poolsCountException;
  }
  /**
   * @return self::POOLS_COUNT_EXCEPTION_*
   */
  public function getPoolsCountException()
  {
    return $this->poolsCountException;
  }
  /**
   * Wading pool. A shallow pool designed for small children to play in. Can be
   * indoors or outdoors. Also known as kiddie pool.
   *
   * @param bool $wadingPool
   */
  public function setWadingPool($wadingPool)
  {
    $this->wadingPool = $wadingPool;
  }
  /**
   * @return bool
   */
  public function getWadingPool()
  {
    return $this->wadingPool;
  }
  /**
   * Wading pool exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WADING_POOL_EXCEPTION_* $wadingPoolException
   */
  public function setWadingPoolException($wadingPoolException)
  {
    $this->wadingPoolException = $wadingPoolException;
  }
  /**
   * @return self::WADING_POOL_EXCEPTION_*
   */
  public function getWadingPoolException()
  {
    return $this->wadingPoolException;
  }
  /**
   * Water park. An aquatic recreation area with a large pool or series of pools
   * that has features such as a water slide or tube, wavepool, fountains, rope
   * swings, and/or obstacle course. Can be indoors or outdoors. Also known as
   * adventure pool.
   *
   * @param bool $waterPark
   */
  public function setWaterPark($waterPark)
  {
    $this->waterPark = $waterPark;
  }
  /**
   * @return bool
   */
  public function getWaterPark()
  {
    return $this->waterPark;
  }
  /**
   * Water park exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATER_PARK_EXCEPTION_* $waterParkException
   */
  public function setWaterParkException($waterParkException)
  {
    $this->waterParkException = $waterParkException;
  }
  /**
   * @return self::WATER_PARK_EXCEPTION_*
   */
  public function getWaterParkException()
  {
    return $this->waterParkException;
  }
  /**
   * Waterslide. A continuously wetted chute positioned by an indoor or outdoor
   * pool which people slide down into the water.
   *
   * @param bool $waterslide
   */
  public function setWaterslide($waterslide)
  {
    $this->waterslide = $waterslide;
  }
  /**
   * @return bool
   */
  public function getWaterslide()
  {
    return $this->waterslide;
  }
  /**
   * Waterslide exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATERSLIDE_EXCEPTION_* $waterslideException
   */
  public function setWaterslideException($waterslideException)
  {
    $this->waterslideException = $waterslideException;
  }
  /**
   * @return self::WATERSLIDE_EXCEPTION_*
   */
  public function getWaterslideException()
  {
    return $this->waterslideException;
  }
  /**
   * Wave pool. A large indoor or outdoor pool with a machine that produces
   * water currents to mimic the ocean's crests.
   *
   * @param bool $wavePool
   */
  public function setWavePool($wavePool)
  {
    $this->wavePool = $wavePool;
  }
  /**
   * @return bool
   */
  public function getWavePool()
  {
    return $this->wavePool;
  }
  /**
   * Wave pool exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WAVE_POOL_EXCEPTION_* $wavePoolException
   */
  public function setWavePoolException($wavePoolException)
  {
    $this->wavePoolException = $wavePoolException;
  }
  /**
   * @return self::WAVE_POOL_EXCEPTION_*
   */
  public function getWavePoolException()
  {
    return $this->wavePoolException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pools::class, 'Google_Service_MyBusinessLodging_Pools');
