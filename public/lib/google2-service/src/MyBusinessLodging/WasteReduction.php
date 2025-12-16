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

class WasteReduction extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COMPOSTABLE_FOOD_CONTAINERS_AND_CUTLERY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COMPOSTABLE_FOOD_CONTAINERS_AND_CUTLERY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COMPOSTABLE_FOOD_CONTAINERS_AND_CUTLERY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COMPOSTABLE_FOOD_CONTAINERS_AND_CUTLERY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COMPOSTS_EXCESS_FOOD_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COMPOSTS_EXCESS_FOOD_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COMPOSTS_EXCESS_FOOD_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COMPOSTS_EXCESS_FOOD_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DONATES_EXCESS_FOOD_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DONATES_EXCESS_FOOD_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DONATES_EXCESS_FOOD_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DONATES_EXCESS_FOOD_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FOOD_WASTE_REDUCTION_PROGRAM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FOOD_WASTE_REDUCTION_PROGRAM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FOOD_WASTE_REDUCTION_PROGRAM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FOOD_WASTE_REDUCTION_PROGRAM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const NO_SINGLE_USE_PLASTIC_STRAWS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const NO_SINGLE_USE_PLASTIC_STRAWS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const NO_SINGLE_USE_PLASTIC_STRAWS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const NO_SINGLE_USE_PLASTIC_STRAWS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const NO_SINGLE_USE_PLASTIC_WATER_BOTTLES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const NO_SINGLE_USE_PLASTIC_WATER_BOTTLES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const NO_SINGLE_USE_PLASTIC_WATER_BOTTLES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const NO_SINGLE_USE_PLASTIC_WATER_BOTTLES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const NO_STYROFOAM_FOOD_CONTAINERS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const NO_STYROFOAM_FOOD_CONTAINERS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const NO_STYROFOAM_FOOD_CONTAINERS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const NO_STYROFOAM_FOOD_CONTAINERS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const RECYCLING_PROGRAM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const RECYCLING_PROGRAM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const RECYCLING_PROGRAM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const RECYCLING_PROGRAM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const REFILLABLE_TOILETRY_CONTAINERS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const REFILLABLE_TOILETRY_CONTAINERS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const REFILLABLE_TOILETRY_CONTAINERS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const REFILLABLE_TOILETRY_CONTAINERS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SAFELY_DISPOSES_BATTERIES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SAFELY_DISPOSES_BATTERIES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SAFELY_DISPOSES_BATTERIES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SAFELY_DISPOSES_BATTERIES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SAFELY_DISPOSES_ELECTRONICS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SAFELY_DISPOSES_ELECTRONICS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SAFELY_DISPOSES_ELECTRONICS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SAFELY_DISPOSES_ELECTRONICS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SAFELY_DISPOSES_LIGHTBULBS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SAFELY_DISPOSES_LIGHTBULBS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SAFELY_DISPOSES_LIGHTBULBS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SAFELY_DISPOSES_LIGHTBULBS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SAFELY_HANDLES_HAZARDOUS_SUBSTANCES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SAFELY_HANDLES_HAZARDOUS_SUBSTANCES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SAFELY_HANDLES_HAZARDOUS_SUBSTANCES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SAFELY_HANDLES_HAZARDOUS_SUBSTANCES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SOAP_DONATION_PROGRAM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SOAP_DONATION_PROGRAM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SOAP_DONATION_PROGRAM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SOAP_DONATION_PROGRAM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TOILETRY_DONATION_PROGRAM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TOILETRY_DONATION_PROGRAM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TOILETRY_DONATION_PROGRAM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TOILETRY_DONATION_PROGRAM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATER_BOTTLE_FILLING_STATIONS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATER_BOTTLE_FILLING_STATIONS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATER_BOTTLE_FILLING_STATIONS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATER_BOTTLE_FILLING_STATIONS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Compostable food containers and cutlery. 100% of food service containers
   * and to-go cutlery are compostable, and reusable utensils are offered
   * wherever possible. Compostable materials are capable of undergoing
   * biological decomposition in a compost site, such that material is not
   * visually distinguishable and breaks down into carbon dioxide, water,
   * inorganic compounds, and biomass.
   *
   * @var bool
   */
  public $compostableFoodContainersAndCutlery;
  /**
   * Compostable food containers and cutlery exception.
   *
   * @var string
   */
  public $compostableFoodContainersAndCutleryException;
  /**
   * Composts excess food. The property has a program and/or policy for
   * diverting waste from landfill by composting food and yard waste, either
   * through compost collection and off-site processing or on-site compost
   * processing.
   *
   * @var bool
   */
  public $compostsExcessFood;
  /**
   * Composts excess food exception.
   *
   * @var string
   */
  public $compostsExcessFoodException;
  /**
   * Donates excess food. The property has a program and/or policy for diverting
   * waste from landfill that may include efforts to donate for human
   * consumption or divert food for animal feed.
   *
   * @var bool
   */
  public $donatesExcessFood;
  /**
   * Donates excess food exception.
   *
   * @var string
   */
  public $donatesExcessFoodException;
  /**
   * Food waste reduction program. The property has established a food waste
   * reduction and donation program, aiming to reduce food waste by half. These
   * programs typically use tools such as the Hotel Kitchen Toolkit and others
   * to track waste and measure progress.
   *
   * @var bool
   */
  public $foodWasteReductionProgram;
  /**
   * Food waste reduction program exception.
   *
   * @var string
   */
  public $foodWasteReductionProgramException;
  /**
   * No single use plastic straws. The property bans single-use plastic straws.
   *
   * @var bool
   */
  public $noSingleUsePlasticStraws;
  /**
   * No single use plastic straws exception.
   *
   * @var string
   */
  public $noSingleUsePlasticStrawsException;
  /**
   * No single use plastic water bottles. The property bans single-use plastic
   * water bottles.
   *
   * @var bool
   */
  public $noSingleUsePlasticWaterBottles;
  /**
   * No single use plastic water bottles exception.
   *
   * @var string
   */
  public $noSingleUsePlasticWaterBottlesException;
  /**
   * No styrofoam food containers. The property eliminates the use of Styrofoam
   * in disposable food service items.
   *
   * @var bool
   */
  public $noStyrofoamFoodContainers;
  /**
   * No styrofoam food containers exception.
   *
   * @var string
   */
  public $noStyrofoamFoodContainersException;
  /**
   * Recycling program. The property has a recycling program, aligned with LEED
   * waste requirements, and a policy outlining efforts to send less than 50% of
   * waste to landfill. The recycling program includes storage locations for
   * recyclable materials, including mixed paper, corrugated cardboard, glass,
   * plastics, and metals.
   *
   * @var bool
   */
  public $recyclingProgram;
  /**
   * Recycling program exception.
   *
   * @var string
   */
  public $recyclingProgramException;
  /**
   * Refillable toiletry containers. The property has replaced miniature
   * individual containers with refillable amenity dispensers for shampoo,
   * conditioner, soap, and lotion.
   *
   * @var bool
   */
  public $refillableToiletryContainers;
  /**
   * Refillable toiletry containers exception.
   *
   * @var string
   */
  public $refillableToiletryContainersException;
  /**
   * Safely disposes batteries. The property safely stores and disposes
   * batteries.
   *
   * @var bool
   */
  public $safelyDisposesBatteries;
  /**
   * Safely disposes batteries exception.
   *
   * @var string
   */
  public $safelyDisposesBatteriesException;
  /**
   * Safely disposes electronics. The property has a reputable recycling program
   * that keeps hazardous electronic parts and chemical compounds out of
   * landfills, dumps and other unauthorized abandonment sites, and
   * recycles/reuses applicable materials. (e.g. certified electronics
   * recyclers).
   *
   * @var bool
   */
  public $safelyDisposesElectronics;
  /**
   * Safely disposes electronics exception.
   *
   * @var string
   */
  public $safelyDisposesElectronicsException;
  /**
   * Safely disposes lightbulbs. The property safely stores and disposes
   * lightbulbs.
   *
   * @var bool
   */
  public $safelyDisposesLightbulbs;
  /**
   * Safely disposes lightbulbs exception.
   *
   * @var string
   */
  public $safelyDisposesLightbulbsException;
  /**
   * Safely handles hazardous substances. The property has a hazardous waste
   * management program aligned wit GreenSeal and LEED requirements, and meets
   * all regulatory requirements for hazardous waste disposal and recycling.
   * Hazardous means substances that are classified as "hazardous" by an
   * authoritative body (such as OSHA or DOT), are labeled with signal words
   * such as "Danger," "Caution," "Warning," or are flammable, corrosive, or
   * ignitable. Requirements include: - The property shall maintain records of
   * the efforts it has made to replace the hazardous substances it uses with
   * less hazardous alternatives. - An inventory of the hazardous materials
   * stored on-site. - Products intended for cleaning, dishwashing, laundry, and
   * pool maintenance shall be stored in clearly labeled containers. These
   * containers shall be checked regularly for leaks, and replaced a necessary.
   * - Spill containment devices shall be installed to collect spills, drips, or
   * leaching of chemicals.
   *
   * @var bool
   */
  public $safelyHandlesHazardousSubstances;
  /**
   * Safely handles hazardous substances exception.
   *
   * @var string
   */
  public $safelyHandlesHazardousSubstancesException;
  /**
   * Soap donation program. The property participates in a soap donation program
   * such as Clean the World or something similar.
   *
   * @var bool
   */
  public $soapDonationProgram;
  /**
   * Soap donation program exception.
   *
   * @var string
   */
  public $soapDonationProgramException;
  /**
   * Toiletry donation program. The property participates in a toiletry donation
   * program such as Clean the World or something similar.
   *
   * @var bool
   */
  public $toiletryDonationProgram;
  /**
   * Toiletry donation program exception.
   *
   * @var string
   */
  public $toiletryDonationProgramException;
  /**
   * Water bottle filling stations. The property offers water stations
   * throughout the building for guest use.
   *
   * @var bool
   */
  public $waterBottleFillingStations;
  /**
   * Water bottle filling stations exception.
   *
   * @var string
   */
  public $waterBottleFillingStationsException;

  /**
   * Compostable food containers and cutlery. 100% of food service containers
   * and to-go cutlery are compostable, and reusable utensils are offered
   * wherever possible. Compostable materials are capable of undergoing
   * biological decomposition in a compost site, such that material is not
   * visually distinguishable and breaks down into carbon dioxide, water,
   * inorganic compounds, and biomass.
   *
   * @param bool $compostableFoodContainersAndCutlery
   */
  public function setCompostableFoodContainersAndCutlery($compostableFoodContainersAndCutlery)
  {
    $this->compostableFoodContainersAndCutlery = $compostableFoodContainersAndCutlery;
  }
  /**
   * @return bool
   */
  public function getCompostableFoodContainersAndCutlery()
  {
    return $this->compostableFoodContainersAndCutlery;
  }
  /**
   * Compostable food containers and cutlery exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COMPOSTABLE_FOOD_CONTAINERS_AND_CUTLERY_EXCEPTION_* $compostableFoodContainersAndCutleryException
   */
  public function setCompostableFoodContainersAndCutleryException($compostableFoodContainersAndCutleryException)
  {
    $this->compostableFoodContainersAndCutleryException = $compostableFoodContainersAndCutleryException;
  }
  /**
   * @return self::COMPOSTABLE_FOOD_CONTAINERS_AND_CUTLERY_EXCEPTION_*
   */
  public function getCompostableFoodContainersAndCutleryException()
  {
    return $this->compostableFoodContainersAndCutleryException;
  }
  /**
   * Composts excess food. The property has a program and/or policy for
   * diverting waste from landfill by composting food and yard waste, either
   * through compost collection and off-site processing or on-site compost
   * processing.
   *
   * @param bool $compostsExcessFood
   */
  public function setCompostsExcessFood($compostsExcessFood)
  {
    $this->compostsExcessFood = $compostsExcessFood;
  }
  /**
   * @return bool
   */
  public function getCompostsExcessFood()
  {
    return $this->compostsExcessFood;
  }
  /**
   * Composts excess food exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COMPOSTS_EXCESS_FOOD_EXCEPTION_* $compostsExcessFoodException
   */
  public function setCompostsExcessFoodException($compostsExcessFoodException)
  {
    $this->compostsExcessFoodException = $compostsExcessFoodException;
  }
  /**
   * @return self::COMPOSTS_EXCESS_FOOD_EXCEPTION_*
   */
  public function getCompostsExcessFoodException()
  {
    return $this->compostsExcessFoodException;
  }
  /**
   * Donates excess food. The property has a program and/or policy for diverting
   * waste from landfill that may include efforts to donate for human
   * consumption or divert food for animal feed.
   *
   * @param bool $donatesExcessFood
   */
  public function setDonatesExcessFood($donatesExcessFood)
  {
    $this->donatesExcessFood = $donatesExcessFood;
  }
  /**
   * @return bool
   */
  public function getDonatesExcessFood()
  {
    return $this->donatesExcessFood;
  }
  /**
   * Donates excess food exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DONATES_EXCESS_FOOD_EXCEPTION_* $donatesExcessFoodException
   */
  public function setDonatesExcessFoodException($donatesExcessFoodException)
  {
    $this->donatesExcessFoodException = $donatesExcessFoodException;
  }
  /**
   * @return self::DONATES_EXCESS_FOOD_EXCEPTION_*
   */
  public function getDonatesExcessFoodException()
  {
    return $this->donatesExcessFoodException;
  }
  /**
   * Food waste reduction program. The property has established a food waste
   * reduction and donation program, aiming to reduce food waste by half. These
   * programs typically use tools such as the Hotel Kitchen Toolkit and others
   * to track waste and measure progress.
   *
   * @param bool $foodWasteReductionProgram
   */
  public function setFoodWasteReductionProgram($foodWasteReductionProgram)
  {
    $this->foodWasteReductionProgram = $foodWasteReductionProgram;
  }
  /**
   * @return bool
   */
  public function getFoodWasteReductionProgram()
  {
    return $this->foodWasteReductionProgram;
  }
  /**
   * Food waste reduction program exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FOOD_WASTE_REDUCTION_PROGRAM_EXCEPTION_* $foodWasteReductionProgramException
   */
  public function setFoodWasteReductionProgramException($foodWasteReductionProgramException)
  {
    $this->foodWasteReductionProgramException = $foodWasteReductionProgramException;
  }
  /**
   * @return self::FOOD_WASTE_REDUCTION_PROGRAM_EXCEPTION_*
   */
  public function getFoodWasteReductionProgramException()
  {
    return $this->foodWasteReductionProgramException;
  }
  /**
   * No single use plastic straws. The property bans single-use plastic straws.
   *
   * @param bool $noSingleUsePlasticStraws
   */
  public function setNoSingleUsePlasticStraws($noSingleUsePlasticStraws)
  {
    $this->noSingleUsePlasticStraws = $noSingleUsePlasticStraws;
  }
  /**
   * @return bool
   */
  public function getNoSingleUsePlasticStraws()
  {
    return $this->noSingleUsePlasticStraws;
  }
  /**
   * No single use plastic straws exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::NO_SINGLE_USE_PLASTIC_STRAWS_EXCEPTION_* $noSingleUsePlasticStrawsException
   */
  public function setNoSingleUsePlasticStrawsException($noSingleUsePlasticStrawsException)
  {
    $this->noSingleUsePlasticStrawsException = $noSingleUsePlasticStrawsException;
  }
  /**
   * @return self::NO_SINGLE_USE_PLASTIC_STRAWS_EXCEPTION_*
   */
  public function getNoSingleUsePlasticStrawsException()
  {
    return $this->noSingleUsePlasticStrawsException;
  }
  /**
   * No single use plastic water bottles. The property bans single-use plastic
   * water bottles.
   *
   * @param bool $noSingleUsePlasticWaterBottles
   */
  public function setNoSingleUsePlasticWaterBottles($noSingleUsePlasticWaterBottles)
  {
    $this->noSingleUsePlasticWaterBottles = $noSingleUsePlasticWaterBottles;
  }
  /**
   * @return bool
   */
  public function getNoSingleUsePlasticWaterBottles()
  {
    return $this->noSingleUsePlasticWaterBottles;
  }
  /**
   * No single use plastic water bottles exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::NO_SINGLE_USE_PLASTIC_WATER_BOTTLES_EXCEPTION_* $noSingleUsePlasticWaterBottlesException
   */
  public function setNoSingleUsePlasticWaterBottlesException($noSingleUsePlasticWaterBottlesException)
  {
    $this->noSingleUsePlasticWaterBottlesException = $noSingleUsePlasticWaterBottlesException;
  }
  /**
   * @return self::NO_SINGLE_USE_PLASTIC_WATER_BOTTLES_EXCEPTION_*
   */
  public function getNoSingleUsePlasticWaterBottlesException()
  {
    return $this->noSingleUsePlasticWaterBottlesException;
  }
  /**
   * No styrofoam food containers. The property eliminates the use of Styrofoam
   * in disposable food service items.
   *
   * @param bool $noStyrofoamFoodContainers
   */
  public function setNoStyrofoamFoodContainers($noStyrofoamFoodContainers)
  {
    $this->noStyrofoamFoodContainers = $noStyrofoamFoodContainers;
  }
  /**
   * @return bool
   */
  public function getNoStyrofoamFoodContainers()
  {
    return $this->noStyrofoamFoodContainers;
  }
  /**
   * No styrofoam food containers exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::NO_STYROFOAM_FOOD_CONTAINERS_EXCEPTION_* $noStyrofoamFoodContainersException
   */
  public function setNoStyrofoamFoodContainersException($noStyrofoamFoodContainersException)
  {
    $this->noStyrofoamFoodContainersException = $noStyrofoamFoodContainersException;
  }
  /**
   * @return self::NO_STYROFOAM_FOOD_CONTAINERS_EXCEPTION_*
   */
  public function getNoStyrofoamFoodContainersException()
  {
    return $this->noStyrofoamFoodContainersException;
  }
  /**
   * Recycling program. The property has a recycling program, aligned with LEED
   * waste requirements, and a policy outlining efforts to send less than 50% of
   * waste to landfill. The recycling program includes storage locations for
   * recyclable materials, including mixed paper, corrugated cardboard, glass,
   * plastics, and metals.
   *
   * @param bool $recyclingProgram
   */
  public function setRecyclingProgram($recyclingProgram)
  {
    $this->recyclingProgram = $recyclingProgram;
  }
  /**
   * @return bool
   */
  public function getRecyclingProgram()
  {
    return $this->recyclingProgram;
  }
  /**
   * Recycling program exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::RECYCLING_PROGRAM_EXCEPTION_* $recyclingProgramException
   */
  public function setRecyclingProgramException($recyclingProgramException)
  {
    $this->recyclingProgramException = $recyclingProgramException;
  }
  /**
   * @return self::RECYCLING_PROGRAM_EXCEPTION_*
   */
  public function getRecyclingProgramException()
  {
    return $this->recyclingProgramException;
  }
  /**
   * Refillable toiletry containers. The property has replaced miniature
   * individual containers with refillable amenity dispensers for shampoo,
   * conditioner, soap, and lotion.
   *
   * @param bool $refillableToiletryContainers
   */
  public function setRefillableToiletryContainers($refillableToiletryContainers)
  {
    $this->refillableToiletryContainers = $refillableToiletryContainers;
  }
  /**
   * @return bool
   */
  public function getRefillableToiletryContainers()
  {
    return $this->refillableToiletryContainers;
  }
  /**
   * Refillable toiletry containers exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::REFILLABLE_TOILETRY_CONTAINERS_EXCEPTION_* $refillableToiletryContainersException
   */
  public function setRefillableToiletryContainersException($refillableToiletryContainersException)
  {
    $this->refillableToiletryContainersException = $refillableToiletryContainersException;
  }
  /**
   * @return self::REFILLABLE_TOILETRY_CONTAINERS_EXCEPTION_*
   */
  public function getRefillableToiletryContainersException()
  {
    return $this->refillableToiletryContainersException;
  }
  /**
   * Safely disposes batteries. The property safely stores and disposes
   * batteries.
   *
   * @param bool $safelyDisposesBatteries
   */
  public function setSafelyDisposesBatteries($safelyDisposesBatteries)
  {
    $this->safelyDisposesBatteries = $safelyDisposesBatteries;
  }
  /**
   * @return bool
   */
  public function getSafelyDisposesBatteries()
  {
    return $this->safelyDisposesBatteries;
  }
  /**
   * Safely disposes batteries exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SAFELY_DISPOSES_BATTERIES_EXCEPTION_* $safelyDisposesBatteriesException
   */
  public function setSafelyDisposesBatteriesException($safelyDisposesBatteriesException)
  {
    $this->safelyDisposesBatteriesException = $safelyDisposesBatteriesException;
  }
  /**
   * @return self::SAFELY_DISPOSES_BATTERIES_EXCEPTION_*
   */
  public function getSafelyDisposesBatteriesException()
  {
    return $this->safelyDisposesBatteriesException;
  }
  /**
   * Safely disposes electronics. The property has a reputable recycling program
   * that keeps hazardous electronic parts and chemical compounds out of
   * landfills, dumps and other unauthorized abandonment sites, and
   * recycles/reuses applicable materials. (e.g. certified electronics
   * recyclers).
   *
   * @param bool $safelyDisposesElectronics
   */
  public function setSafelyDisposesElectronics($safelyDisposesElectronics)
  {
    $this->safelyDisposesElectronics = $safelyDisposesElectronics;
  }
  /**
   * @return bool
   */
  public function getSafelyDisposesElectronics()
  {
    return $this->safelyDisposesElectronics;
  }
  /**
   * Safely disposes electronics exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SAFELY_DISPOSES_ELECTRONICS_EXCEPTION_* $safelyDisposesElectronicsException
   */
  public function setSafelyDisposesElectronicsException($safelyDisposesElectronicsException)
  {
    $this->safelyDisposesElectronicsException = $safelyDisposesElectronicsException;
  }
  /**
   * @return self::SAFELY_DISPOSES_ELECTRONICS_EXCEPTION_*
   */
  public function getSafelyDisposesElectronicsException()
  {
    return $this->safelyDisposesElectronicsException;
  }
  /**
   * Safely disposes lightbulbs. The property safely stores and disposes
   * lightbulbs.
   *
   * @param bool $safelyDisposesLightbulbs
   */
  public function setSafelyDisposesLightbulbs($safelyDisposesLightbulbs)
  {
    $this->safelyDisposesLightbulbs = $safelyDisposesLightbulbs;
  }
  /**
   * @return bool
   */
  public function getSafelyDisposesLightbulbs()
  {
    return $this->safelyDisposesLightbulbs;
  }
  /**
   * Safely disposes lightbulbs exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SAFELY_DISPOSES_LIGHTBULBS_EXCEPTION_* $safelyDisposesLightbulbsException
   */
  public function setSafelyDisposesLightbulbsException($safelyDisposesLightbulbsException)
  {
    $this->safelyDisposesLightbulbsException = $safelyDisposesLightbulbsException;
  }
  /**
   * @return self::SAFELY_DISPOSES_LIGHTBULBS_EXCEPTION_*
   */
  public function getSafelyDisposesLightbulbsException()
  {
    return $this->safelyDisposesLightbulbsException;
  }
  /**
   * Safely handles hazardous substances. The property has a hazardous waste
   * management program aligned wit GreenSeal and LEED requirements, and meets
   * all regulatory requirements for hazardous waste disposal and recycling.
   * Hazardous means substances that are classified as "hazardous" by an
   * authoritative body (such as OSHA or DOT), are labeled with signal words
   * such as "Danger," "Caution," "Warning," or are flammable, corrosive, or
   * ignitable. Requirements include: - The property shall maintain records of
   * the efforts it has made to replace the hazardous substances it uses with
   * less hazardous alternatives. - An inventory of the hazardous materials
   * stored on-site. - Products intended for cleaning, dishwashing, laundry, and
   * pool maintenance shall be stored in clearly labeled containers. These
   * containers shall be checked regularly for leaks, and replaced a necessary.
   * - Spill containment devices shall be installed to collect spills, drips, or
   * leaching of chemicals.
   *
   * @param bool $safelyHandlesHazardousSubstances
   */
  public function setSafelyHandlesHazardousSubstances($safelyHandlesHazardousSubstances)
  {
    $this->safelyHandlesHazardousSubstances = $safelyHandlesHazardousSubstances;
  }
  /**
   * @return bool
   */
  public function getSafelyHandlesHazardousSubstances()
  {
    return $this->safelyHandlesHazardousSubstances;
  }
  /**
   * Safely handles hazardous substances exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SAFELY_HANDLES_HAZARDOUS_SUBSTANCES_EXCEPTION_* $safelyHandlesHazardousSubstancesException
   */
  public function setSafelyHandlesHazardousSubstancesException($safelyHandlesHazardousSubstancesException)
  {
    $this->safelyHandlesHazardousSubstancesException = $safelyHandlesHazardousSubstancesException;
  }
  /**
   * @return self::SAFELY_HANDLES_HAZARDOUS_SUBSTANCES_EXCEPTION_*
   */
  public function getSafelyHandlesHazardousSubstancesException()
  {
    return $this->safelyHandlesHazardousSubstancesException;
  }
  /**
   * Soap donation program. The property participates in a soap donation program
   * such as Clean the World or something similar.
   *
   * @param bool $soapDonationProgram
   */
  public function setSoapDonationProgram($soapDonationProgram)
  {
    $this->soapDonationProgram = $soapDonationProgram;
  }
  /**
   * @return bool
   */
  public function getSoapDonationProgram()
  {
    return $this->soapDonationProgram;
  }
  /**
   * Soap donation program exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SOAP_DONATION_PROGRAM_EXCEPTION_* $soapDonationProgramException
   */
  public function setSoapDonationProgramException($soapDonationProgramException)
  {
    $this->soapDonationProgramException = $soapDonationProgramException;
  }
  /**
   * @return self::SOAP_DONATION_PROGRAM_EXCEPTION_*
   */
  public function getSoapDonationProgramException()
  {
    return $this->soapDonationProgramException;
  }
  /**
   * Toiletry donation program. The property participates in a toiletry donation
   * program such as Clean the World or something similar.
   *
   * @param bool $toiletryDonationProgram
   */
  public function setToiletryDonationProgram($toiletryDonationProgram)
  {
    $this->toiletryDonationProgram = $toiletryDonationProgram;
  }
  /**
   * @return bool
   */
  public function getToiletryDonationProgram()
  {
    return $this->toiletryDonationProgram;
  }
  /**
   * Toiletry donation program exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TOILETRY_DONATION_PROGRAM_EXCEPTION_* $toiletryDonationProgramException
   */
  public function setToiletryDonationProgramException($toiletryDonationProgramException)
  {
    $this->toiletryDonationProgramException = $toiletryDonationProgramException;
  }
  /**
   * @return self::TOILETRY_DONATION_PROGRAM_EXCEPTION_*
   */
  public function getToiletryDonationProgramException()
  {
    return $this->toiletryDonationProgramException;
  }
  /**
   * Water bottle filling stations. The property offers water stations
   * throughout the building for guest use.
   *
   * @param bool $waterBottleFillingStations
   */
  public function setWaterBottleFillingStations($waterBottleFillingStations)
  {
    $this->waterBottleFillingStations = $waterBottleFillingStations;
  }
  /**
   * @return bool
   */
  public function getWaterBottleFillingStations()
  {
    return $this->waterBottleFillingStations;
  }
  /**
   * Water bottle filling stations exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATER_BOTTLE_FILLING_STATIONS_EXCEPTION_* $waterBottleFillingStationsException
   */
  public function setWaterBottleFillingStationsException($waterBottleFillingStationsException)
  {
    $this->waterBottleFillingStationsException = $waterBottleFillingStationsException;
  }
  /**
   * @return self::WATER_BOTTLE_FILLING_STATIONS_EXCEPTION_*
   */
  public function getWaterBottleFillingStationsException()
  {
    return $this->waterBottleFillingStationsException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WasteReduction::class, 'Google_Service_MyBusinessLodging_WasteReduction');
