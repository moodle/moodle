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

class WaterConservation extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_WATER_USE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_WATER_USE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_WATER_USE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INDEPENDENT_ORGANIZATION_AUDITS_WATER_USE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LINEN_REUSE_PROGRAM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LINEN_REUSE_PROGRAM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LINEN_REUSE_PROGRAM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LINEN_REUSE_PROGRAM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TOWEL_REUSE_PROGRAM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TOWEL_REUSE_PROGRAM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TOWEL_REUSE_PROGRAM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TOWEL_REUSE_PROGRAM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATER_SAVING_SHOWERS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATER_SAVING_SHOWERS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATER_SAVING_SHOWERS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATER_SAVING_SHOWERS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATER_SAVING_SINKS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATER_SAVING_SINKS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATER_SAVING_SINKS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATER_SAVING_SINKS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATER_SAVING_TOILETS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATER_SAVING_TOILETS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATER_SAVING_TOILETS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATER_SAVING_TOILETS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Independent organization audits water use. The property conducts a water
   * conservation audit every 5 years, the results of which are either verified
   * by a third-party and/or published in external communications. A water
   * conservation audit is a detailed assessment of the facility, providing
   * recommendations to existing operations and procedures to improve water
   * efficiency, available incentives or rebates, and opportunities for
   * improvements through renovations or upgrades. Examples of organizations who
   * conduct credible third party audits include: Engie Impact, and local
   * utility providers (they often provide energy and water audits).
   *
   * @var bool
   */
  public $independentOrganizationAuditsWaterUse;
  /**
   * Independent organization audits water use exception.
   *
   * @var string
   */
  public $independentOrganizationAuditsWaterUseException;
  /**
   * Linen reuse program. The property offers a linen reuse program.
   *
   * @var bool
   */
  public $linenReuseProgram;
  /**
   * Linen reuse program exception.
   *
   * @var string
   */
  public $linenReuseProgramException;
  /**
   * Towel reuse program. The property offers a towel reuse program.
   *
   * @var bool
   */
  public $towelReuseProgram;
  /**
   * Towel reuse program exception.
   *
   * @var string
   */
  public $towelReuseProgramException;
  /**
   * Water saving showers. All of the property's guest rooms have shower heads
   * that use no more than 2.0 gallons per minute (gpm).
   *
   * @var bool
   */
  public $waterSavingShowers;
  /**
   * Water saving showers exception.
   *
   * @var string
   */
  public $waterSavingShowersException;
  /**
   * Water saving sinks. All of the property's guest rooms have bathroom faucets
   * that use a maximum of 1.5 gallons per minute (gpm), public restroom faucets
   * do not exceed 0.5 gpm, and kitchen faucets (excluding faucets used
   * exclusively for filling operations) do not exceed 2.2 gpm.
   *
   * @var bool
   */
  public $waterSavingSinks;
  /**
   * Water saving sinks exception.
   *
   * @var string
   */
  public $waterSavingSinksException;
  /**
   * Water saving toilets. All of the property's toilets use 1.6 gallons per
   * flush, or less.
   *
   * @var bool
   */
  public $waterSavingToilets;
  /**
   * Water saving toilets exception.
   *
   * @var string
   */
  public $waterSavingToiletsException;

  /**
   * Independent organization audits water use. The property conducts a water
   * conservation audit every 5 years, the results of which are either verified
   * by a third-party and/or published in external communications. A water
   * conservation audit is a detailed assessment of the facility, providing
   * recommendations to existing operations and procedures to improve water
   * efficiency, available incentives or rebates, and opportunities for
   * improvements through renovations or upgrades. Examples of organizations who
   * conduct credible third party audits include: Engie Impact, and local
   * utility providers (they often provide energy and water audits).
   *
   * @param bool $independentOrganizationAuditsWaterUse
   */
  public function setIndependentOrganizationAuditsWaterUse($independentOrganizationAuditsWaterUse)
  {
    $this->independentOrganizationAuditsWaterUse = $independentOrganizationAuditsWaterUse;
  }
  /**
   * @return bool
   */
  public function getIndependentOrganizationAuditsWaterUse()
  {
    return $this->independentOrganizationAuditsWaterUse;
  }
  /**
   * Independent organization audits water use exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INDEPENDENT_ORGANIZATION_AUDITS_WATER_USE_EXCEPTION_* $independentOrganizationAuditsWaterUseException
   */
  public function setIndependentOrganizationAuditsWaterUseException($independentOrganizationAuditsWaterUseException)
  {
    $this->independentOrganizationAuditsWaterUseException = $independentOrganizationAuditsWaterUseException;
  }
  /**
   * @return self::INDEPENDENT_ORGANIZATION_AUDITS_WATER_USE_EXCEPTION_*
   */
  public function getIndependentOrganizationAuditsWaterUseException()
  {
    return $this->independentOrganizationAuditsWaterUseException;
  }
  /**
   * Linen reuse program. The property offers a linen reuse program.
   *
   * @param bool $linenReuseProgram
   */
  public function setLinenReuseProgram($linenReuseProgram)
  {
    $this->linenReuseProgram = $linenReuseProgram;
  }
  /**
   * @return bool
   */
  public function getLinenReuseProgram()
  {
    return $this->linenReuseProgram;
  }
  /**
   * Linen reuse program exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LINEN_REUSE_PROGRAM_EXCEPTION_* $linenReuseProgramException
   */
  public function setLinenReuseProgramException($linenReuseProgramException)
  {
    $this->linenReuseProgramException = $linenReuseProgramException;
  }
  /**
   * @return self::LINEN_REUSE_PROGRAM_EXCEPTION_*
   */
  public function getLinenReuseProgramException()
  {
    return $this->linenReuseProgramException;
  }
  /**
   * Towel reuse program. The property offers a towel reuse program.
   *
   * @param bool $towelReuseProgram
   */
  public function setTowelReuseProgram($towelReuseProgram)
  {
    $this->towelReuseProgram = $towelReuseProgram;
  }
  /**
   * @return bool
   */
  public function getTowelReuseProgram()
  {
    return $this->towelReuseProgram;
  }
  /**
   * Towel reuse program exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TOWEL_REUSE_PROGRAM_EXCEPTION_* $towelReuseProgramException
   */
  public function setTowelReuseProgramException($towelReuseProgramException)
  {
    $this->towelReuseProgramException = $towelReuseProgramException;
  }
  /**
   * @return self::TOWEL_REUSE_PROGRAM_EXCEPTION_*
   */
  public function getTowelReuseProgramException()
  {
    return $this->towelReuseProgramException;
  }
  /**
   * Water saving showers. All of the property's guest rooms have shower heads
   * that use no more than 2.0 gallons per minute (gpm).
   *
   * @param bool $waterSavingShowers
   */
  public function setWaterSavingShowers($waterSavingShowers)
  {
    $this->waterSavingShowers = $waterSavingShowers;
  }
  /**
   * @return bool
   */
  public function getWaterSavingShowers()
  {
    return $this->waterSavingShowers;
  }
  /**
   * Water saving showers exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATER_SAVING_SHOWERS_EXCEPTION_* $waterSavingShowersException
   */
  public function setWaterSavingShowersException($waterSavingShowersException)
  {
    $this->waterSavingShowersException = $waterSavingShowersException;
  }
  /**
   * @return self::WATER_SAVING_SHOWERS_EXCEPTION_*
   */
  public function getWaterSavingShowersException()
  {
    return $this->waterSavingShowersException;
  }
  /**
   * Water saving sinks. All of the property's guest rooms have bathroom faucets
   * that use a maximum of 1.5 gallons per minute (gpm), public restroom faucets
   * do not exceed 0.5 gpm, and kitchen faucets (excluding faucets used
   * exclusively for filling operations) do not exceed 2.2 gpm.
   *
   * @param bool $waterSavingSinks
   */
  public function setWaterSavingSinks($waterSavingSinks)
  {
    $this->waterSavingSinks = $waterSavingSinks;
  }
  /**
   * @return bool
   */
  public function getWaterSavingSinks()
  {
    return $this->waterSavingSinks;
  }
  /**
   * Water saving sinks exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATER_SAVING_SINKS_EXCEPTION_* $waterSavingSinksException
   */
  public function setWaterSavingSinksException($waterSavingSinksException)
  {
    $this->waterSavingSinksException = $waterSavingSinksException;
  }
  /**
   * @return self::WATER_SAVING_SINKS_EXCEPTION_*
   */
  public function getWaterSavingSinksException()
  {
    return $this->waterSavingSinksException;
  }
  /**
   * Water saving toilets. All of the property's toilets use 1.6 gallons per
   * flush, or less.
   *
   * @param bool $waterSavingToilets
   */
  public function setWaterSavingToilets($waterSavingToilets)
  {
    $this->waterSavingToilets = $waterSavingToilets;
  }
  /**
   * @return bool
   */
  public function getWaterSavingToilets()
  {
    return $this->waterSavingToilets;
  }
  /**
   * Water saving toilets exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATER_SAVING_TOILETS_EXCEPTION_* $waterSavingToiletsException
   */
  public function setWaterSavingToiletsException($waterSavingToiletsException)
  {
    $this->waterSavingToiletsException = $waterSavingToiletsException;
  }
  /**
   * @return self::WATER_SAVING_TOILETS_EXCEPTION_*
   */
  public function getWaterSavingToiletsException()
  {
    return $this->waterSavingToiletsException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WaterConservation::class, 'Google_Service_MyBusinessLodging_WaterConservation');
