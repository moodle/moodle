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

class LivingAreaSleeping extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BUNK_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BUNK_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BUNK_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BUNK_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CRIBS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CRIBS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CRIBS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CRIBS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DOUBLE_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DOUBLE_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DOUBLE_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DOUBLE_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FEATHER_PILLOWS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FEATHER_PILLOWS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FEATHER_PILLOWS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FEATHER_PILLOWS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HYPOALLERGENIC_BEDDING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HYPOALLERGENIC_BEDDING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HYPOALLERGENIC_BEDDING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HYPOALLERGENIC_BEDDING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const KING_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const KING_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const KING_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const KING_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MEMORY_FOAM_PILLOWS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MEMORY_FOAM_PILLOWS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MEMORY_FOAM_PILLOWS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MEMORY_FOAM_PILLOWS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const OTHER_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const OTHER_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const OTHER_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const OTHER_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const QUEEN_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const QUEEN_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const QUEEN_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const QUEEN_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ROLL_AWAY_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ROLL_AWAY_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ROLL_AWAY_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ROLL_AWAY_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SINGLE_OR_TWIN_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SINGLE_OR_TWIN_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SINGLE_OR_TWIN_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SINGLE_OR_TWIN_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SOFA_BEDS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SOFA_BEDS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SOFA_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SOFA_BEDS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SYNTHETIC_PILLOWS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SYNTHETIC_PILLOWS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SYNTHETIC_PILLOWS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SYNTHETIC_PILLOWS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Beds count. The number of permanent beds present in a guestroom. Does not
   * include rollaway beds, cribs or sofabeds.
   *
   * @var int
   */
  public $bedsCount;
  /**
   * Beds count exception.
   *
   * @var string
   */
  public $bedsCountException;
  /**
   * Bunk beds count. The number of furniture pieces in which one framed
   * mattress is fixed directly above another by means of a physical frame. This
   * allows one person(s) to sleep in the bottom bunk and one person(s) to sleep
   * in the top bunk. Also known as double decker bed.
   *
   * @var int
   */
  public $bunkBedsCount;
  /**
   * Bunk beds count exception.
   *
   * @var string
   */
  public $bunkBedsCountException;
  /**
   * Cribs count. The number of small beds for an infant or toddler that the
   * guestroom can obtain. The bed is surrounded by a high railing to prevent
   * the child from falling or climbing out of the bed
   *
   * @var int
   */
  public $cribsCount;
  /**
   * Cribs count exception.
   *
   * @var string
   */
  public $cribsCountException;
  /**
   * Double beds count. The number of medium beds measuring 53"W x 75"L (135cm x
   * 191cm). Also known as full size bed.
   *
   * @var int
   */
  public $doubleBedsCount;
  /**
   * Double beds count exception.
   *
   * @var string
   */
  public $doubleBedsCountException;
  /**
   * Feather pillows. The option for guests to obtain bed pillows that are
   * stuffed with the feathers and down of ducks or geese.
   *
   * @var bool
   */
  public $featherPillows;
  /**
   * Feather pillows exception.
   *
   * @var string
   */
  public $featherPillowsException;
  /**
   * Hypoallergenic bedding. Bedding such as linens, pillows, mattress covers
   * and/or mattresses that are made of materials known to be resistant to
   * allergens such as mold, dust and dander.
   *
   * @var bool
   */
  public $hypoallergenicBedding;
  /**
   * Hypoallergenic bedding exception.
   *
   * @var string
   */
  public $hypoallergenicBeddingException;
  /**
   * King beds count. The number of large beds measuring 76"W x 80"L (193cm x
   * 102cm). Most often meant to accompany two people. Includes California king
   * and super king.
   *
   * @var int
   */
  public $kingBedsCount;
  /**
   * King beds count exception.
   *
   * @var string
   */
  public $kingBedsCountException;
  /**
   * Memory foam pillows. The option for guests to obtain bed pillows that are
   * stuffed with a man-made foam that responds to body heat by conforming to
   * the body closely, and then recovers its shape when the pillow cools down.
   *
   * @var bool
   */
  public $memoryFoamPillows;
  /**
   * Memory foam pillows exception.
   *
   * @var string
   */
  public $memoryFoamPillowsException;
  /**
   * Other beds count. The number of beds that are not standard mattress and
   * boxspring setups such as Japanese tatami mats, trundle beds, air mattresses
   * and cots.
   *
   * @var int
   */
  public $otherBedsCount;
  /**
   * Other beds count exception.
   *
   * @var string
   */
  public $otherBedsCountException;
  /**
   * Queen beds count. The number of medium-large beds measuring 60"W x 80"L
   * (152cm x 102cm).
   *
   * @var int
   */
  public $queenBedsCount;
  /**
   * Queen beds count exception.
   *
   * @var string
   */
  public $queenBedsCountException;
  /**
   * Roll away beds count. The number of mattresses on wheeled frames that can
   * be folded in half and rolled away for easy storage that the guestroom can
   * obtain upon request.
   *
   * @var int
   */
  public $rollAwayBedsCount;
  /**
   * Roll away beds count exception.
   *
   * @var string
   */
  public $rollAwayBedsCountException;
  /**
   * Single or twin count beds. The number of smaller beds measuring 38"W x 75"L
   * (97cm x 191cm) that can accommodate one adult.
   *
   * @var int
   */
  public $singleOrTwinBedsCount;
  /**
   * Single or twin beds count exception.
   *
   * @var string
   */
  public $singleOrTwinBedsCountException;
  /**
   * Sofa beds count. The number of specially designed sofas that can be made to
   * serve as a bed by lowering its hinged upholstered back to horizontal
   * position or by pulling out a concealed mattress.
   *
   * @var int
   */
  public $sofaBedsCount;
  /**
   * Sofa beds count exception.
   *
   * @var string
   */
  public $sofaBedsCountException;
  /**
   * Synthetic pillows. The option for guests to obtain bed pillows stuffed with
   * polyester material crafted to reproduce the feel of a pillow stuffed with
   * down and feathers.
   *
   * @var bool
   */
  public $syntheticPillows;
  /**
   * Synthetic pillows exception.
   *
   * @var string
   */
  public $syntheticPillowsException;

  /**
   * Beds count. The number of permanent beds present in a guestroom. Does not
   * include rollaway beds, cribs or sofabeds.
   *
   * @param int $bedsCount
   */
  public function setBedsCount($bedsCount)
  {
    $this->bedsCount = $bedsCount;
  }
  /**
   * @return int
   */
  public function getBedsCount()
  {
    return $this->bedsCount;
  }
  /**
   * Beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BEDS_COUNT_EXCEPTION_* $bedsCountException
   */
  public function setBedsCountException($bedsCountException)
  {
    $this->bedsCountException = $bedsCountException;
  }
  /**
   * @return self::BEDS_COUNT_EXCEPTION_*
   */
  public function getBedsCountException()
  {
    return $this->bedsCountException;
  }
  /**
   * Bunk beds count. The number of furniture pieces in which one framed
   * mattress is fixed directly above another by means of a physical frame. This
   * allows one person(s) to sleep in the bottom bunk and one person(s) to sleep
   * in the top bunk. Also known as double decker bed.
   *
   * @param int $bunkBedsCount
   */
  public function setBunkBedsCount($bunkBedsCount)
  {
    $this->bunkBedsCount = $bunkBedsCount;
  }
  /**
   * @return int
   */
  public function getBunkBedsCount()
  {
    return $this->bunkBedsCount;
  }
  /**
   * Bunk beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BUNK_BEDS_COUNT_EXCEPTION_* $bunkBedsCountException
   */
  public function setBunkBedsCountException($bunkBedsCountException)
  {
    $this->bunkBedsCountException = $bunkBedsCountException;
  }
  /**
   * @return self::BUNK_BEDS_COUNT_EXCEPTION_*
   */
  public function getBunkBedsCountException()
  {
    return $this->bunkBedsCountException;
  }
  /**
   * Cribs count. The number of small beds for an infant or toddler that the
   * guestroom can obtain. The bed is surrounded by a high railing to prevent
   * the child from falling or climbing out of the bed
   *
   * @param int $cribsCount
   */
  public function setCribsCount($cribsCount)
  {
    $this->cribsCount = $cribsCount;
  }
  /**
   * @return int
   */
  public function getCribsCount()
  {
    return $this->cribsCount;
  }
  /**
   * Cribs count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CRIBS_COUNT_EXCEPTION_* $cribsCountException
   */
  public function setCribsCountException($cribsCountException)
  {
    $this->cribsCountException = $cribsCountException;
  }
  /**
   * @return self::CRIBS_COUNT_EXCEPTION_*
   */
  public function getCribsCountException()
  {
    return $this->cribsCountException;
  }
  /**
   * Double beds count. The number of medium beds measuring 53"W x 75"L (135cm x
   * 191cm). Also known as full size bed.
   *
   * @param int $doubleBedsCount
   */
  public function setDoubleBedsCount($doubleBedsCount)
  {
    $this->doubleBedsCount = $doubleBedsCount;
  }
  /**
   * @return int
   */
  public function getDoubleBedsCount()
  {
    return $this->doubleBedsCount;
  }
  /**
   * Double beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DOUBLE_BEDS_COUNT_EXCEPTION_* $doubleBedsCountException
   */
  public function setDoubleBedsCountException($doubleBedsCountException)
  {
    $this->doubleBedsCountException = $doubleBedsCountException;
  }
  /**
   * @return self::DOUBLE_BEDS_COUNT_EXCEPTION_*
   */
  public function getDoubleBedsCountException()
  {
    return $this->doubleBedsCountException;
  }
  /**
   * Feather pillows. The option for guests to obtain bed pillows that are
   * stuffed with the feathers and down of ducks or geese.
   *
   * @param bool $featherPillows
   */
  public function setFeatherPillows($featherPillows)
  {
    $this->featherPillows = $featherPillows;
  }
  /**
   * @return bool
   */
  public function getFeatherPillows()
  {
    return $this->featherPillows;
  }
  /**
   * Feather pillows exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FEATHER_PILLOWS_EXCEPTION_* $featherPillowsException
   */
  public function setFeatherPillowsException($featherPillowsException)
  {
    $this->featherPillowsException = $featherPillowsException;
  }
  /**
   * @return self::FEATHER_PILLOWS_EXCEPTION_*
   */
  public function getFeatherPillowsException()
  {
    return $this->featherPillowsException;
  }
  /**
   * Hypoallergenic bedding. Bedding such as linens, pillows, mattress covers
   * and/or mattresses that are made of materials known to be resistant to
   * allergens such as mold, dust and dander.
   *
   * @param bool $hypoallergenicBedding
   */
  public function setHypoallergenicBedding($hypoallergenicBedding)
  {
    $this->hypoallergenicBedding = $hypoallergenicBedding;
  }
  /**
   * @return bool
   */
  public function getHypoallergenicBedding()
  {
    return $this->hypoallergenicBedding;
  }
  /**
   * Hypoallergenic bedding exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HYPOALLERGENIC_BEDDING_EXCEPTION_* $hypoallergenicBeddingException
   */
  public function setHypoallergenicBeddingException($hypoallergenicBeddingException)
  {
    $this->hypoallergenicBeddingException = $hypoallergenicBeddingException;
  }
  /**
   * @return self::HYPOALLERGENIC_BEDDING_EXCEPTION_*
   */
  public function getHypoallergenicBeddingException()
  {
    return $this->hypoallergenicBeddingException;
  }
  /**
   * King beds count. The number of large beds measuring 76"W x 80"L (193cm x
   * 102cm). Most often meant to accompany two people. Includes California king
   * and super king.
   *
   * @param int $kingBedsCount
   */
  public function setKingBedsCount($kingBedsCount)
  {
    $this->kingBedsCount = $kingBedsCount;
  }
  /**
   * @return int
   */
  public function getKingBedsCount()
  {
    return $this->kingBedsCount;
  }
  /**
   * King beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::KING_BEDS_COUNT_EXCEPTION_* $kingBedsCountException
   */
  public function setKingBedsCountException($kingBedsCountException)
  {
    $this->kingBedsCountException = $kingBedsCountException;
  }
  /**
   * @return self::KING_BEDS_COUNT_EXCEPTION_*
   */
  public function getKingBedsCountException()
  {
    return $this->kingBedsCountException;
  }
  /**
   * Memory foam pillows. The option for guests to obtain bed pillows that are
   * stuffed with a man-made foam that responds to body heat by conforming to
   * the body closely, and then recovers its shape when the pillow cools down.
   *
   * @param bool $memoryFoamPillows
   */
  public function setMemoryFoamPillows($memoryFoamPillows)
  {
    $this->memoryFoamPillows = $memoryFoamPillows;
  }
  /**
   * @return bool
   */
  public function getMemoryFoamPillows()
  {
    return $this->memoryFoamPillows;
  }
  /**
   * Memory foam pillows exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MEMORY_FOAM_PILLOWS_EXCEPTION_* $memoryFoamPillowsException
   */
  public function setMemoryFoamPillowsException($memoryFoamPillowsException)
  {
    $this->memoryFoamPillowsException = $memoryFoamPillowsException;
  }
  /**
   * @return self::MEMORY_FOAM_PILLOWS_EXCEPTION_*
   */
  public function getMemoryFoamPillowsException()
  {
    return $this->memoryFoamPillowsException;
  }
  /**
   * Other beds count. The number of beds that are not standard mattress and
   * boxspring setups such as Japanese tatami mats, trundle beds, air mattresses
   * and cots.
   *
   * @param int $otherBedsCount
   */
  public function setOtherBedsCount($otherBedsCount)
  {
    $this->otherBedsCount = $otherBedsCount;
  }
  /**
   * @return int
   */
  public function getOtherBedsCount()
  {
    return $this->otherBedsCount;
  }
  /**
   * Other beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::OTHER_BEDS_COUNT_EXCEPTION_* $otherBedsCountException
   */
  public function setOtherBedsCountException($otherBedsCountException)
  {
    $this->otherBedsCountException = $otherBedsCountException;
  }
  /**
   * @return self::OTHER_BEDS_COUNT_EXCEPTION_*
   */
  public function getOtherBedsCountException()
  {
    return $this->otherBedsCountException;
  }
  /**
   * Queen beds count. The number of medium-large beds measuring 60"W x 80"L
   * (152cm x 102cm).
   *
   * @param int $queenBedsCount
   */
  public function setQueenBedsCount($queenBedsCount)
  {
    $this->queenBedsCount = $queenBedsCount;
  }
  /**
   * @return int
   */
  public function getQueenBedsCount()
  {
    return $this->queenBedsCount;
  }
  /**
   * Queen beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::QUEEN_BEDS_COUNT_EXCEPTION_* $queenBedsCountException
   */
  public function setQueenBedsCountException($queenBedsCountException)
  {
    $this->queenBedsCountException = $queenBedsCountException;
  }
  /**
   * @return self::QUEEN_BEDS_COUNT_EXCEPTION_*
   */
  public function getQueenBedsCountException()
  {
    return $this->queenBedsCountException;
  }
  /**
   * Roll away beds count. The number of mattresses on wheeled frames that can
   * be folded in half and rolled away for easy storage that the guestroom can
   * obtain upon request.
   *
   * @param int $rollAwayBedsCount
   */
  public function setRollAwayBedsCount($rollAwayBedsCount)
  {
    $this->rollAwayBedsCount = $rollAwayBedsCount;
  }
  /**
   * @return int
   */
  public function getRollAwayBedsCount()
  {
    return $this->rollAwayBedsCount;
  }
  /**
   * Roll away beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ROLL_AWAY_BEDS_COUNT_EXCEPTION_* $rollAwayBedsCountException
   */
  public function setRollAwayBedsCountException($rollAwayBedsCountException)
  {
    $this->rollAwayBedsCountException = $rollAwayBedsCountException;
  }
  /**
   * @return self::ROLL_AWAY_BEDS_COUNT_EXCEPTION_*
   */
  public function getRollAwayBedsCountException()
  {
    return $this->rollAwayBedsCountException;
  }
  /**
   * Single or twin count beds. The number of smaller beds measuring 38"W x 75"L
   * (97cm x 191cm) that can accommodate one adult.
   *
   * @param int $singleOrTwinBedsCount
   */
  public function setSingleOrTwinBedsCount($singleOrTwinBedsCount)
  {
    $this->singleOrTwinBedsCount = $singleOrTwinBedsCount;
  }
  /**
   * @return int
   */
  public function getSingleOrTwinBedsCount()
  {
    return $this->singleOrTwinBedsCount;
  }
  /**
   * Single or twin beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SINGLE_OR_TWIN_BEDS_COUNT_EXCEPTION_* $singleOrTwinBedsCountException
   */
  public function setSingleOrTwinBedsCountException($singleOrTwinBedsCountException)
  {
    $this->singleOrTwinBedsCountException = $singleOrTwinBedsCountException;
  }
  /**
   * @return self::SINGLE_OR_TWIN_BEDS_COUNT_EXCEPTION_*
   */
  public function getSingleOrTwinBedsCountException()
  {
    return $this->singleOrTwinBedsCountException;
  }
  /**
   * Sofa beds count. The number of specially designed sofas that can be made to
   * serve as a bed by lowering its hinged upholstered back to horizontal
   * position or by pulling out a concealed mattress.
   *
   * @param int $sofaBedsCount
   */
  public function setSofaBedsCount($sofaBedsCount)
  {
    $this->sofaBedsCount = $sofaBedsCount;
  }
  /**
   * @return int
   */
  public function getSofaBedsCount()
  {
    return $this->sofaBedsCount;
  }
  /**
   * Sofa beds count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SOFA_BEDS_COUNT_EXCEPTION_* $sofaBedsCountException
   */
  public function setSofaBedsCountException($sofaBedsCountException)
  {
    $this->sofaBedsCountException = $sofaBedsCountException;
  }
  /**
   * @return self::SOFA_BEDS_COUNT_EXCEPTION_*
   */
  public function getSofaBedsCountException()
  {
    return $this->sofaBedsCountException;
  }
  /**
   * Synthetic pillows. The option for guests to obtain bed pillows stuffed with
   * polyester material crafted to reproduce the feel of a pillow stuffed with
   * down and feathers.
   *
   * @param bool $syntheticPillows
   */
  public function setSyntheticPillows($syntheticPillows)
  {
    $this->syntheticPillows = $syntheticPillows;
  }
  /**
   * @return bool
   */
  public function getSyntheticPillows()
  {
    return $this->syntheticPillows;
  }
  /**
   * Synthetic pillows exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SYNTHETIC_PILLOWS_EXCEPTION_* $syntheticPillowsException
   */
  public function setSyntheticPillowsException($syntheticPillowsException)
  {
    $this->syntheticPillowsException = $syntheticPillowsException;
  }
  /**
   * @return self::SYNTHETIC_PILLOWS_EXCEPTION_*
   */
  public function getSyntheticPillowsException()
  {
    return $this->syntheticPillowsException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LivingAreaSleeping::class, 'Google_Service_MyBusinessLodging_LivingAreaSleeping');
