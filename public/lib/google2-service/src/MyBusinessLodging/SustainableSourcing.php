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

class SustainableSourcing extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ECO_FRIENDLY_TOILETRIES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ECO_FRIENDLY_TOILETRIES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ECO_FRIENDLY_TOILETRIES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ECO_FRIENDLY_TOILETRIES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LOCALLY_SOURCED_FOOD_AND_BEVERAGES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LOCALLY_SOURCED_FOOD_AND_BEVERAGES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LOCALLY_SOURCED_FOOD_AND_BEVERAGES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LOCALLY_SOURCED_FOOD_AND_BEVERAGES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ORGANIC_CAGE_FREE_EGGS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ORGANIC_CAGE_FREE_EGGS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ORGANIC_CAGE_FREE_EGGS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ORGANIC_CAGE_FREE_EGGS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ORGANIC_FOOD_AND_BEVERAGES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ORGANIC_FOOD_AND_BEVERAGES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ORGANIC_FOOD_AND_BEVERAGES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ORGANIC_FOOD_AND_BEVERAGES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const RESPONSIBLE_PURCHASING_POLICY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const RESPONSIBLE_PURCHASING_POLICY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const RESPONSIBLE_PURCHASING_POLICY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const RESPONSIBLE_PURCHASING_POLICY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const RESPONSIBLY_SOURCES_SEAFOOD_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const RESPONSIBLY_SOURCES_SEAFOOD_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const RESPONSIBLY_SOURCES_SEAFOOD_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const RESPONSIBLY_SOURCES_SEAFOOD_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const VEGAN_MEALS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const VEGAN_MEALS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const VEGAN_MEALS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const VEGAN_MEALS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const VEGETARIAN_MEALS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const VEGETARIAN_MEALS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const VEGETARIAN_MEALS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const VEGETARIAN_MEALS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Eco friendly toiletries. Soap, shampoo, lotion, and other toiletries
   * provided for guests have a nationally or internationally recognized
   * sustainability certification, such as USDA Organic, EU Organic, or cruelty-
   * free.
   *
   * @var bool
   */
  public $ecoFriendlyToiletries;
  /**
   * Eco friendly toiletries exception.
   *
   * @var string
   */
  public $ecoFriendlyToiletriesException;
  /**
   * Locally sourced food and beverages. Property sources locally in order to
   * lower the environmental footprint from reduced transportation and to
   * stimulate the local economy. Products produced less than 62 miles from the
   * establishment are normally considered as locally produced.
   *
   * @var bool
   */
  public $locallySourcedFoodAndBeverages;
  /**
   * Locally sourced food and beverages exception.
   *
   * @var string
   */
  public $locallySourcedFoodAndBeveragesException;
  /**
   * Organic cage free eggs. The property sources 100% certified organic and
   * cage-free eggs (shell, liquid, and egg products). Cage-free means hens are
   * able to walk, spread their wings and lay their eggs in nests).
   *
   * @var bool
   */
  public $organicCageFreeEggs;
  /**
   * Organic cage free eggs exception.
   *
   * @var string
   */
  public $organicCageFreeEggsException;
  /**
   * Organic food and beverages. At least 25% of food and beverages, by spend,
   * are certified organic. Organic means products that are certified to one of
   * the organic standard listed in the IFOAM family of standards. Qualifying
   * certifications include USDA Organic and EU Organic, among others.
   *
   * @var bool
   */
  public $organicFoodAndBeverages;
  /**
   * Organic food and beverages exception.
   *
   * @var string
   */
  public $organicFoodAndBeveragesException;
  /**
   * Responsible purchasing policy. The property has a responsible procurement
   * policy in place. Responsible means integration of social, ethical, and/or
   * environmental performance factors into the procurement process when
   * selecting suppliers.
   *
   * @var bool
   */
  public $responsiblePurchasingPolicy;
  /**
   * Responsible purchasing policy exception.
   *
   * @var string
   */
  public $responsiblePurchasingPolicyException;
  /**
   * Responsibly sources seafood. The property does not source seafood from the
   * Monterey Bay Aquarium Seafood Watch "avoid" list, and must sustainably
   * source seafood listed as "good alternative," "eco-certified," and "best
   * choice". The property has a policy outlining a commitment to source Marine
   * Stewardship Council (MSC) and/or Aquaculture Stewardship Council (ASC)
   * Chain of Custody certified seafood.
   *
   * @var bool
   */
  public $responsiblySourcesSeafood;
  /**
   * Responsibly sources seafood exception.
   *
   * @var string
   */
  public $responsiblySourcesSeafoodException;
  /**
   * Vegan meals. The property provides vegan menu options for guests. Vegan
   * food does not contain animal products or byproducts.
   *
   * @var bool
   */
  public $veganMeals;
  /**
   * Vegan meals exception.
   *
   * @var string
   */
  public $veganMealsException;
  /**
   * Vegetarian meals. The property provides vegetarian menu options for guests.
   * Vegetarian food does not contain meat, poultry, fish, or seafood.
   *
   * @var bool
   */
  public $vegetarianMeals;
  /**
   * Vegetarian meals exception.
   *
   * @var string
   */
  public $vegetarianMealsException;

  /**
   * Eco friendly toiletries. Soap, shampoo, lotion, and other toiletries
   * provided for guests have a nationally or internationally recognized
   * sustainability certification, such as USDA Organic, EU Organic, or cruelty-
   * free.
   *
   * @param bool $ecoFriendlyToiletries
   */
  public function setEcoFriendlyToiletries($ecoFriendlyToiletries)
  {
    $this->ecoFriendlyToiletries = $ecoFriendlyToiletries;
  }
  /**
   * @return bool
   */
  public function getEcoFriendlyToiletries()
  {
    return $this->ecoFriendlyToiletries;
  }
  /**
   * Eco friendly toiletries exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ECO_FRIENDLY_TOILETRIES_EXCEPTION_* $ecoFriendlyToiletriesException
   */
  public function setEcoFriendlyToiletriesException($ecoFriendlyToiletriesException)
  {
    $this->ecoFriendlyToiletriesException = $ecoFriendlyToiletriesException;
  }
  /**
   * @return self::ECO_FRIENDLY_TOILETRIES_EXCEPTION_*
   */
  public function getEcoFriendlyToiletriesException()
  {
    return $this->ecoFriendlyToiletriesException;
  }
  /**
   * Locally sourced food and beverages. Property sources locally in order to
   * lower the environmental footprint from reduced transportation and to
   * stimulate the local economy. Products produced less than 62 miles from the
   * establishment are normally considered as locally produced.
   *
   * @param bool $locallySourcedFoodAndBeverages
   */
  public function setLocallySourcedFoodAndBeverages($locallySourcedFoodAndBeverages)
  {
    $this->locallySourcedFoodAndBeverages = $locallySourcedFoodAndBeverages;
  }
  /**
   * @return bool
   */
  public function getLocallySourcedFoodAndBeverages()
  {
    return $this->locallySourcedFoodAndBeverages;
  }
  /**
   * Locally sourced food and beverages exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LOCALLY_SOURCED_FOOD_AND_BEVERAGES_EXCEPTION_* $locallySourcedFoodAndBeveragesException
   */
  public function setLocallySourcedFoodAndBeveragesException($locallySourcedFoodAndBeveragesException)
  {
    $this->locallySourcedFoodAndBeveragesException = $locallySourcedFoodAndBeveragesException;
  }
  /**
   * @return self::LOCALLY_SOURCED_FOOD_AND_BEVERAGES_EXCEPTION_*
   */
  public function getLocallySourcedFoodAndBeveragesException()
  {
    return $this->locallySourcedFoodAndBeveragesException;
  }
  /**
   * Organic cage free eggs. The property sources 100% certified organic and
   * cage-free eggs (shell, liquid, and egg products). Cage-free means hens are
   * able to walk, spread their wings and lay their eggs in nests).
   *
   * @param bool $organicCageFreeEggs
   */
  public function setOrganicCageFreeEggs($organicCageFreeEggs)
  {
    $this->organicCageFreeEggs = $organicCageFreeEggs;
  }
  /**
   * @return bool
   */
  public function getOrganicCageFreeEggs()
  {
    return $this->organicCageFreeEggs;
  }
  /**
   * Organic cage free eggs exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ORGANIC_CAGE_FREE_EGGS_EXCEPTION_* $organicCageFreeEggsException
   */
  public function setOrganicCageFreeEggsException($organicCageFreeEggsException)
  {
    $this->organicCageFreeEggsException = $organicCageFreeEggsException;
  }
  /**
   * @return self::ORGANIC_CAGE_FREE_EGGS_EXCEPTION_*
   */
  public function getOrganicCageFreeEggsException()
  {
    return $this->organicCageFreeEggsException;
  }
  /**
   * Organic food and beverages. At least 25% of food and beverages, by spend,
   * are certified organic. Organic means products that are certified to one of
   * the organic standard listed in the IFOAM family of standards. Qualifying
   * certifications include USDA Organic and EU Organic, among others.
   *
   * @param bool $organicFoodAndBeverages
   */
  public function setOrganicFoodAndBeverages($organicFoodAndBeverages)
  {
    $this->organicFoodAndBeverages = $organicFoodAndBeverages;
  }
  /**
   * @return bool
   */
  public function getOrganicFoodAndBeverages()
  {
    return $this->organicFoodAndBeverages;
  }
  /**
   * Organic food and beverages exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ORGANIC_FOOD_AND_BEVERAGES_EXCEPTION_* $organicFoodAndBeveragesException
   */
  public function setOrganicFoodAndBeveragesException($organicFoodAndBeveragesException)
  {
    $this->organicFoodAndBeveragesException = $organicFoodAndBeveragesException;
  }
  /**
   * @return self::ORGANIC_FOOD_AND_BEVERAGES_EXCEPTION_*
   */
  public function getOrganicFoodAndBeveragesException()
  {
    return $this->organicFoodAndBeveragesException;
  }
  /**
   * Responsible purchasing policy. The property has a responsible procurement
   * policy in place. Responsible means integration of social, ethical, and/or
   * environmental performance factors into the procurement process when
   * selecting suppliers.
   *
   * @param bool $responsiblePurchasingPolicy
   */
  public function setResponsiblePurchasingPolicy($responsiblePurchasingPolicy)
  {
    $this->responsiblePurchasingPolicy = $responsiblePurchasingPolicy;
  }
  /**
   * @return bool
   */
  public function getResponsiblePurchasingPolicy()
  {
    return $this->responsiblePurchasingPolicy;
  }
  /**
   * Responsible purchasing policy exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::RESPONSIBLE_PURCHASING_POLICY_EXCEPTION_* $responsiblePurchasingPolicyException
   */
  public function setResponsiblePurchasingPolicyException($responsiblePurchasingPolicyException)
  {
    $this->responsiblePurchasingPolicyException = $responsiblePurchasingPolicyException;
  }
  /**
   * @return self::RESPONSIBLE_PURCHASING_POLICY_EXCEPTION_*
   */
  public function getResponsiblePurchasingPolicyException()
  {
    return $this->responsiblePurchasingPolicyException;
  }
  /**
   * Responsibly sources seafood. The property does not source seafood from the
   * Monterey Bay Aquarium Seafood Watch "avoid" list, and must sustainably
   * source seafood listed as "good alternative," "eco-certified," and "best
   * choice". The property has a policy outlining a commitment to source Marine
   * Stewardship Council (MSC) and/or Aquaculture Stewardship Council (ASC)
   * Chain of Custody certified seafood.
   *
   * @param bool $responsiblySourcesSeafood
   */
  public function setResponsiblySourcesSeafood($responsiblySourcesSeafood)
  {
    $this->responsiblySourcesSeafood = $responsiblySourcesSeafood;
  }
  /**
   * @return bool
   */
  public function getResponsiblySourcesSeafood()
  {
    return $this->responsiblySourcesSeafood;
  }
  /**
   * Responsibly sources seafood exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::RESPONSIBLY_SOURCES_SEAFOOD_EXCEPTION_* $responsiblySourcesSeafoodException
   */
  public function setResponsiblySourcesSeafoodException($responsiblySourcesSeafoodException)
  {
    $this->responsiblySourcesSeafoodException = $responsiblySourcesSeafoodException;
  }
  /**
   * @return self::RESPONSIBLY_SOURCES_SEAFOOD_EXCEPTION_*
   */
  public function getResponsiblySourcesSeafoodException()
  {
    return $this->responsiblySourcesSeafoodException;
  }
  /**
   * Vegan meals. The property provides vegan menu options for guests. Vegan
   * food does not contain animal products or byproducts.
   *
   * @param bool $veganMeals
   */
  public function setVeganMeals($veganMeals)
  {
    $this->veganMeals = $veganMeals;
  }
  /**
   * @return bool
   */
  public function getVeganMeals()
  {
    return $this->veganMeals;
  }
  /**
   * Vegan meals exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::VEGAN_MEALS_EXCEPTION_* $veganMealsException
   */
  public function setVeganMealsException($veganMealsException)
  {
    $this->veganMealsException = $veganMealsException;
  }
  /**
   * @return self::VEGAN_MEALS_EXCEPTION_*
   */
  public function getVeganMealsException()
  {
    return $this->veganMealsException;
  }
  /**
   * Vegetarian meals. The property provides vegetarian menu options for guests.
   * Vegetarian food does not contain meat, poultry, fish, or seafood.
   *
   * @param bool $vegetarianMeals
   */
  public function setVegetarianMeals($vegetarianMeals)
  {
    $this->vegetarianMeals = $vegetarianMeals;
  }
  /**
   * @return bool
   */
  public function getVegetarianMeals()
  {
    return $this->vegetarianMeals;
  }
  /**
   * Vegetarian meals exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::VEGETARIAN_MEALS_EXCEPTION_* $vegetarianMealsException
   */
  public function setVegetarianMealsException($vegetarianMealsException)
  {
    $this->vegetarianMealsException = $vegetarianMealsException;
  }
  /**
   * @return self::VEGETARIAN_MEALS_EXCEPTION_*
   */
  public function getVegetarianMealsException()
  {
    return $this->vegetarianMealsException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SustainableSourcing::class, 'Google_Service_MyBusinessLodging_SustainableSourcing');
