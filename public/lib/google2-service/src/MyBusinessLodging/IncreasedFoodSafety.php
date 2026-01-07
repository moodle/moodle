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

class IncreasedFoodSafety extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DINING_AREAS_ADDITIONAL_SANITATION_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DINING_AREAS_ADDITIONAL_SANITATION_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DINING_AREAS_ADDITIONAL_SANITATION_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DINING_AREAS_ADDITIONAL_SANITATION_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DISPOSABLE_FLATWARE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DISPOSABLE_FLATWARE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DISPOSABLE_FLATWARE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DISPOSABLE_FLATWARE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FOOD_PREPARATION_AND_SERVING_ADDITIONAL_SAFETY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FOOD_PREPARATION_AND_SERVING_ADDITIONAL_SAFETY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FOOD_PREPARATION_AND_SERVING_ADDITIONAL_SAFETY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FOOD_PREPARATION_AND_SERVING_ADDITIONAL_SAFETY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INDIVIDUAL_PACKAGED_MEALS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INDIVIDUAL_PACKAGED_MEALS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INDIVIDUAL_PACKAGED_MEALS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INDIVIDUAL_PACKAGED_MEALS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SINGLE_USE_FOOD_MENUS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SINGLE_USE_FOOD_MENUS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SINGLE_USE_FOOD_MENUS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SINGLE_USE_FOOD_MENUS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Additional sanitation in dining areas.
   *
   * @var bool
   */
  public $diningAreasAdditionalSanitation;
  /**
   * Dining areas additional sanitation exception.
   *
   * @var string
   */
  public $diningAreasAdditionalSanitationException;
  /**
   * Disposable flatware.
   *
   * @var bool
   */
  public $disposableFlatware;
  /**
   * Disposable flatware exception.
   *
   * @var string
   */
  public $disposableFlatwareException;
  /**
   * Additional safety measures during food prep and serving.
   *
   * @var bool
   */
  public $foodPreparationAndServingAdditionalSafety;
  /**
   * Food preparation and serving additional safety exception.
   *
   * @var string
   */
  public $foodPreparationAndServingAdditionalSafetyException;
  /**
   * Individually-packaged meals.
   *
   * @var bool
   */
  public $individualPackagedMeals;
  /**
   * Individual packaged meals exception.
   *
   * @var string
   */
  public $individualPackagedMealsException;
  /**
   * Single-use menus.
   *
   * @var bool
   */
  public $singleUseFoodMenus;
  /**
   * Single use food menus exception.
   *
   * @var string
   */
  public $singleUseFoodMenusException;

  /**
   * Additional sanitation in dining areas.
   *
   * @param bool $diningAreasAdditionalSanitation
   */
  public function setDiningAreasAdditionalSanitation($diningAreasAdditionalSanitation)
  {
    $this->diningAreasAdditionalSanitation = $diningAreasAdditionalSanitation;
  }
  /**
   * @return bool
   */
  public function getDiningAreasAdditionalSanitation()
  {
    return $this->diningAreasAdditionalSanitation;
  }
  /**
   * Dining areas additional sanitation exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DINING_AREAS_ADDITIONAL_SANITATION_EXCEPTION_* $diningAreasAdditionalSanitationException
   */
  public function setDiningAreasAdditionalSanitationException($diningAreasAdditionalSanitationException)
  {
    $this->diningAreasAdditionalSanitationException = $diningAreasAdditionalSanitationException;
  }
  /**
   * @return self::DINING_AREAS_ADDITIONAL_SANITATION_EXCEPTION_*
   */
  public function getDiningAreasAdditionalSanitationException()
  {
    return $this->diningAreasAdditionalSanitationException;
  }
  /**
   * Disposable flatware.
   *
   * @param bool $disposableFlatware
   */
  public function setDisposableFlatware($disposableFlatware)
  {
    $this->disposableFlatware = $disposableFlatware;
  }
  /**
   * @return bool
   */
  public function getDisposableFlatware()
  {
    return $this->disposableFlatware;
  }
  /**
   * Disposable flatware exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DISPOSABLE_FLATWARE_EXCEPTION_* $disposableFlatwareException
   */
  public function setDisposableFlatwareException($disposableFlatwareException)
  {
    $this->disposableFlatwareException = $disposableFlatwareException;
  }
  /**
   * @return self::DISPOSABLE_FLATWARE_EXCEPTION_*
   */
  public function getDisposableFlatwareException()
  {
    return $this->disposableFlatwareException;
  }
  /**
   * Additional safety measures during food prep and serving.
   *
   * @param bool $foodPreparationAndServingAdditionalSafety
   */
  public function setFoodPreparationAndServingAdditionalSafety($foodPreparationAndServingAdditionalSafety)
  {
    $this->foodPreparationAndServingAdditionalSafety = $foodPreparationAndServingAdditionalSafety;
  }
  /**
   * @return bool
   */
  public function getFoodPreparationAndServingAdditionalSafety()
  {
    return $this->foodPreparationAndServingAdditionalSafety;
  }
  /**
   * Food preparation and serving additional safety exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FOOD_PREPARATION_AND_SERVING_ADDITIONAL_SAFETY_EXCEPTION_* $foodPreparationAndServingAdditionalSafetyException
   */
  public function setFoodPreparationAndServingAdditionalSafetyException($foodPreparationAndServingAdditionalSafetyException)
  {
    $this->foodPreparationAndServingAdditionalSafetyException = $foodPreparationAndServingAdditionalSafetyException;
  }
  /**
   * @return self::FOOD_PREPARATION_AND_SERVING_ADDITIONAL_SAFETY_EXCEPTION_*
   */
  public function getFoodPreparationAndServingAdditionalSafetyException()
  {
    return $this->foodPreparationAndServingAdditionalSafetyException;
  }
  /**
   * Individually-packaged meals.
   *
   * @param bool $individualPackagedMeals
   */
  public function setIndividualPackagedMeals($individualPackagedMeals)
  {
    $this->individualPackagedMeals = $individualPackagedMeals;
  }
  /**
   * @return bool
   */
  public function getIndividualPackagedMeals()
  {
    return $this->individualPackagedMeals;
  }
  /**
   * Individual packaged meals exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INDIVIDUAL_PACKAGED_MEALS_EXCEPTION_* $individualPackagedMealsException
   */
  public function setIndividualPackagedMealsException($individualPackagedMealsException)
  {
    $this->individualPackagedMealsException = $individualPackagedMealsException;
  }
  /**
   * @return self::INDIVIDUAL_PACKAGED_MEALS_EXCEPTION_*
   */
  public function getIndividualPackagedMealsException()
  {
    return $this->individualPackagedMealsException;
  }
  /**
   * Single-use menus.
   *
   * @param bool $singleUseFoodMenus
   */
  public function setSingleUseFoodMenus($singleUseFoodMenus)
  {
    $this->singleUseFoodMenus = $singleUseFoodMenus;
  }
  /**
   * @return bool
   */
  public function getSingleUseFoodMenus()
  {
    return $this->singleUseFoodMenus;
  }
  /**
   * Single use food menus exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SINGLE_USE_FOOD_MENUS_EXCEPTION_* $singleUseFoodMenusException
   */
  public function setSingleUseFoodMenusException($singleUseFoodMenusException)
  {
    $this->singleUseFoodMenusException = $singleUseFoodMenusException;
  }
  /**
   * @return self::SINGLE_USE_FOOD_MENUS_EXCEPTION_*
   */
  public function getSingleUseFoodMenusException()
  {
    return $this->singleUseFoodMenusException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IncreasedFoodSafety::class, 'Google_Service_MyBusinessLodging_IncreasedFoodSafety');
